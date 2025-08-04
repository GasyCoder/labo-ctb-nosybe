<?php

namespace App\Services;

use App\Models\Analyse;
use App\Models\Patient;
use App\Models\Prelevement;
use App\Models\Prescription;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Repositories\PrescriptionRepository;

class PrescriptionService
{
    protected PrescriptionRepository $prescriptionRepository;
    protected PatientService $patientService;
    protected AnalyseService $analyseService;

    public function __construct(
        PrescriptionRepository $prescriptionRepository,
        PatientService $patientService,
        AnalyseService $analyseService
    ) {
        $this->prescriptionRepository = $prescriptionRepository;
        $this->patientService = $patientService;
        $this->analyseService = $analyseService;
    }

    /**
     * Créer une nouvelle prescription avec validation métier
     */
    public function creerPrescription(array $donnees): Prescription
    {
        // Validation métier spécifique laboratoire
        $this->validerDonneesPrescription($donnees);
        
        // Calcul âge en jours pour les analyses pédiatriques
        $donnees['age_en_jours'] = $this->calculerAgeEnJours(
            $donnees['age'], 
            $donnees['unite_age']
        );
        
        // Génération référence unique
        $donnees['reference'] = $this->genererReferencePrescription();
        
        return $this->prescriptionRepository->create($donnees);
    }

    /**
     * Associer analyses à une prescription avec validation compatibilité
     */
    public function associerAnalyses(Prescription $prescription, array $analyses): void
    {
        DB::beginTransaction();
        
        try {
            // Validation compatibilité analyses
            $this->validerCompatibiliteAnalyses($analyses);
            
            // Associer chaque analyse
            foreach ($analyses as $analyseData) {
                $prescription->analyses()->attach($analyseData['id'], [
                    'prix_unitaire' => $analyseData['prix'],
                    'quantite' => $analyseData['quantite'] ?? 1,
                    'statut' => 'PLANIFIEE',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Mettre à jour le statut prescription
            $prescription->update(['status' => 'ANALYSES_ASSOCIEES']);
            
            // Invalider cache analyses patient
            $this->invaliderCachePatient($prescription->patient_id);
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Erreur association analyses : " . $e->getMessage());
        }
    }

    /**
     * Calculer et associer automatiquement les prélèvements requis
     */
    public function calculerPrelevements(Prescription $prescription): void
    {
        // Récupérer toutes les analyses avec leurs prélèvements requis
        $analyses = $prescription->analyses()->with('prelevement')->get();
        
        // Grouper par type de prélèvement
        $prelevementsGroupes = $analyses->groupBy('prelevement.id')
                                       ->filter(fn($group) => $group->first()->prelevement !== null);
        
        foreach ($prelevementsGroupes as $prelevementId => $analysesPrelevement) {
            $prelevement = $analysesPrelevement->first()->prelevement;
            
            // Calculer quantité optimale de tubes
            $quantiteTubes = $this->calculerQuantiteTubesOptimale($analysesPrelevement);
            
            // Déterminer type de tube et volume requis
            $typeTubeRequis = $this->determinerTypeTubeOptimal($analysesPrelevement);
            $volumeRequis = $this->calculerVolumeTotal($analysesPrelevement);
            
            // Associer prélèvement à la prescription
            $prescription->prelevements()->attach($prelevementId, [
                'prix_unitaire' => $prelevement->prix,
                'quantite' => $quantiteTubes,
                'type_tube_requis' => $typeTubeRequis,
                'volume_requis_ml' => $volumeRequis,
                'is_payer' => 'NON_PAYE',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Mettre à jour statut
        $prescription->update(['status' => 'PRELEVEMENTS_CALCULES']);
    }

    /**
     * Validation métier spécifique laboratoire
     */
    private function validerDonneesPrescription(array $donnees): void
    {
        // Validation âge cohérent
        if (!$this->ageCoherent($donnees['age'], $donnees['unite_age'])) {
            throw new \InvalidArgumentException('Âge incohérent avec l\'unité spécifiée');
        }
        
        // Validation patient type vs âge
        if ($donnees['patient_type'] === 'HOSPITALISE' && $donnees['age'] < 16 && $donnees['unite_age'] === 'Ans') {
            // Nécessite autorisation parentale pour mineurs hospitalisés
            $donnees['autorisation_parentale_requise'] = true;
        }
        
        // Validation poids vs âge pour analyses particulières
        if (isset($donnees['poids']) && !$this->poidsCoherentAvecAge($donnees['poids'], $donnees['age'], $donnees['unite_age'])) {
            // Warning mais pas d'erreur bloquante
            logger()->warning('Poids incohérent avec âge', $donnees);
        }
    }

    private function ageCoherent(int $age, string $unite): bool
    {
        return match($unite) {
            'Jours' => $age >= 0 && $age <= 365,
            'Mois' => $age >= 0 && $age <= 120,
            'Ans' => $age >= 0 && $age <= 150,
            default => false
        };
    }

    private function calculerAgeEnJours(int $age, string $unite): int
    {
        return match($unite) {
            'Jours' => $age,
            'Mois' => $age * 30,
            'Ans' => $age * 365,
            default => 0
        };
    }

    /**
     * Validation compatibilité entre analyses sélectionnées
     */
    private function validerCompatibiliteAnalyses(array $analyses): void
    {
        $idsAnalyses = collect($analyses)->pluck('id');
        
        // Vérifier incompatibilités connues (ex: jeûne requis)
        $analysesJeune = $this->analyseService->getAnalysesNecessitantJeune($idsAnalyses);
        $analysesSansJeune = $this->analyseService->getAnalysesInterdisantJeune($idsAnalyses);
        
        if ($analysesJeune->isNotEmpty() && $analysesSansJeune->isNotEmpty()) {
            throw new \InvalidArgumentException(
                'Analyses incompatibles : certaines nécessitent le jeûne, d\'autres l\'interdisent'
            );
        }
        
        // Vérifier doublons dans même famille
        $this->verifierDoublonsAnalyses($idsAnalyses);
    }

    private function verifierDoublonsAnalyses(Collection $idsAnalyses): void
    {
        $analyses = Analyse::whereIn('id', $idsAnalyses)
                          ->with('parent')
                          ->get();
        
        // Grouper par parent pour détecter doublons
        $parFamille = $analyses->groupBy('parent_id')->filter(fn($groupe) => $groupe->count() > 1);
        
        foreach ($parFamille as $parentId => $analysesDouble) {
            $nomParent = $analysesDouble->first()->parent->nom ?? 'Famille inconnue';
            $nomsAnalyses = $analysesDouble->pluck('nom')->join(', ');
            
            throw new \InvalidArgumentException(
                "Analyses redondantes dans {$nomParent} : {$nomsAnalyses}"
            );
        }
    }

    /**
     * Calcul optimal des tubes requis
     */
    private function calculerQuantiteTubesOptimale(Collection $analyses): int
    {
        // Calculer volume total nécessaire
        $volumeTotal = $analyses->sum('volume_echantillon_ml');
        
        // Analyser la répartition temporelle (urgent vs programmé)
        $urgentes = $analyses->where('est_urgente', true)->count();
        $programmees = $analyses->count() - $urgentes;
        
        // Règle métier laboratoire : séparer urgent/programmé
        $tubesUrgents = $urgentes > 0 ? 1 : 0;
        $tubesProgrammes = $programmees > 0 ? max(1, ceil($volumeTotal / 5)) : 0;
        
        return $tubesUrgents + $tubesProgrammes;
    }

    private function determinerTypeTubeOptimal(Collection $analyses): string
    {
        // Prioriser selon l'ordre d'exigence
        $typesRequis = $analyses->pluck('type_tube_requis')->unique()->filter();
        
        // Hiérarchie des types de tubes (du plus au moins contraignant)
        $hierarchie = ['EDTA', 'HEPARINE', 'FLUORURE', 'SEC', 'URINE'];
        
        foreach ($hierarchie as $type) {
            if ($typesRequis->contains($type)) {
                return $type;
            }
        }
        
        return 'SEC'; // Défaut
    }

    private function calculerVolumeTotal(Collection $analyses): float
    {
        return $analyses->sum('volume_echantillon_ml') * 1.2; // +20% marge sécurité
    }

    /**
     * Génération référence unique prescription
     */
    private function genererReferencePrescription(): string
    {
        $prefixe = 'P' . date('Y');
        $compteur = Cache::increment("compteur_prescription_" . date('Y'), 1);
        
        if ($compteur === 1) {
            Cache::put("compteur_prescription_" . date('Y'), 1, now()->endOfYear());
        }
        
        return $prefixe . str_pad($compteur, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Statistiques et analyses pour le dashboard
     */
    public function getStatistiquesPrescriptions(array $filtres = []): array
    {
        $cacheKey = 'stats_prescriptions_' . md5(serialize($filtres));
        
        return Cache::remember($cacheKey, 1800, function() use ($filtres) {
            return $this->prescriptionRepository->getStatistiques($filtres);
        });
    }

    public function getPrescriptionsEnAttente(int $limit = 50): Collection
    {
        return $this->prescriptionRepository->getEnAttente($limit);
    }

    public function getPrescriptionsParPatient(int $patientId, int $limit = 10): Collection
    {
        return $this->prescriptionRepository->getParPatient($patientId, $limit);
    }

    /**
     * Mise à jour statut prescription
     */
    public function changerStatut(Prescription $prescription, string $nouveauStatut, ?string $commentaire = null): void
    {
        $ancienStatut = $prescription->status;
        
        // Validation changement statut autorisé
        if (!$this->changementStatutAutorise($ancienStatut, $nouveauStatut)) {
            throw new \InvalidArgumentException("Changement de statut non autorisé : {$ancienStatut} -> {$nouveauStatut}");
        }
        
        DB::beginTransaction();
        
        try {
            $prescription->update(['status' => $nouveauStatut]);
            
            // Historique changement statut
            $prescription->historiqueStatuts()->create([
                'ancien_statut' => $ancienStatut,
                'nouveau_statut' => $nouveauStatut,
                'modifie_par' => Auth::id(),
                'commentaire' => $commentaire,
                'modifie_at' => now(),
            ]);
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Erreur changement statut : " . $e->getMessage());
        }
    }

    private function changementStatutAutorise(string $ancien, string $nouveau): bool
    {
        $transitions = [
            'EN_ATTENTE' => ['EN_COURS', 'ARCHIVE'],
            'EN_COURS' => ['TERMINE', 'A_REFAIRE', 'ARCHIVE'],
            'TERMINE' => ['VALIDE', 'A_REFAIRE'],
            'VALIDE' => ['ARCHIVE'],
            'A_REFAIRE' => ['EN_COURS', 'ARCHIVE'],
        ];
        
        return isset($transitions[$ancien]) && in_array($nouveau, $transitions[$ancien]);
    }

    private function invaliderCachePatient(int $patientId): void
    {
        Cache::forget("patient_historique_{$patientId}");
        Cache::forget("patient_statistiques_{$patientId}");
        Cache::forget("patient_analyses_frequentes_{$patientId}");
    }

    private function poidsCoherentAvecAge(float $poids, int $age, string $unite): bool
    {
        $ageEnAnnes = match($unite) {
            "Jours" => $age / 365,
            "Mois" => $age / 12,
            "Ans" => $age,
            default => 0
        };
        
        // Règles approximatives poids/âge
        if ($ageEnAnnes < 1) return $poids >= 2 && $poids <= 15;
        if ($ageEnAnnes < 18) return $poids >= 5 && $poids <= 100;
        return $poids >= 30 && $poids <= 300;
    }
}