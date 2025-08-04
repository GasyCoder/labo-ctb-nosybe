<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Repositories\PatientRepository;

class PatientService
{
    protected PatientRepository $patientRepository;

    public function __construct(PatientRepository $patientRepository)
    {
        $this->patientRepository = $patientRepository;
    }

    /**
     * Recherche intelligente de patients avec cache
     */
    public function rechercherPatients(
        string $terme, 
        string $critere = 'nom', 
        string $tri = 'recent',
        int $limite = 50
    ): Collection {
        // Normalisation terme recherche
        $termeNormalise = $this->normaliserTermeRecherche($terme);
        
        $cacheKey = "recherche_patient_{$critere}_{$termeNormalise}_{$tri}_{$limite}";
        
        return Cache::remember($cacheKey, 300, function() use ($termeNormalise, $critere, $tri, $limite) {
            return $this->patientRepository->rechercher($termeNormalise, $critere, $tri, $limite);
        });
    }

    private function normaliserTermeRecherche(string $terme): string
    {
        // Suppression accents, espaces multiples, conversion minuscules
        $terme = strtolower(trim($terme));
        $terme = preg_replace('/\s+/', ' ', $terme);
        
        // Suppression accents français
        $accents = [
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'ą' => 'a',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ę' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c', 'ñ' => 'n'
        ];
        
        return strtr($terme, $accents);
    }

    /**
     * Récupérer patient avec statistiques complètes
     */
    public function getPatientAvecStatistiques(int $patientId): Patient
    {
        $cacheKey = "patient_complet_{$patientId}";
        
        return Cache::remember($cacheKey, 1800, function() use ($patientId) {
            return $this->patientRepository->getAvecStatistiques($patientId);
        });
    }

    /**
     * Récupérer patient avec historique médical
     */
    public function getPatientAvecHistorique(int $patientId): Patient
    {
        return $this->patientRepository->getAvecHistoriqueComplet($patientId);
    }

    /**
     * Historique intelligent avec analyses prédictives
     */
    public function getHistoriqueIntelligent(int $patientId, int $limite = 10): array
    {
        $cacheKey = "historique_intelligent_{$patientId}_{$limite}";
        
        return Cache::remember($cacheKey, 1800, function() use ($patientId, $limite) {
            $prescriptions = $this->patientRepository->getHistoriquePrescriptions($patientId, $limite);
            
            return $prescriptions->map(function($prescription) {
                return [
                    'id' => $prescription->id,
                    'reference' => $prescription->reference,
                    'date' => $prescription->created_at,
                    'age' => $prescription->age,
                    'unite_age' => $prescription->unite_age,
                    'patient_type' => $prescription->patient_type,
                    'prescripteur' => $prescription->prescripteur?->nom . ' ' . $prescription->prescripteur?->prenom,
                    'status' => $prescription->status,
                    'analyses' => $prescription->analyses->map(function($analyse) {
                        return [
                            'nom' => $analyse->nom,
                            'parent' => $analyse->parent?->nom,
                            'prix' => $analyse->pivot->prix_unitaire,
                            'statut' => $analyse->pivot->statut ?? 'PLANIFIEE'
                        ];
                    }),
                    'montant_total' => $prescription->montant_total ?? 0,
                    'renseignement_clinique' => $prescription->renseignement_clinique,
                    // Intelligence prédictive
                    'frequence_analyses' => $this->calculerFrequenceAnalyses($prescription->analyses),
                    'tendance_prix' => $this->calculerTendancePrix($prescription),
                    'delai_moyen_resultats' => $this->calculerDelaiMoyenResultats($prescription)
                ];
            })->toArray();
        });
    }

    private function calculerFrequenceAnalyses(Collection $analyses): array
    {
        return $analyses->groupBy('nom')
                       ->map(fn($groupe) => $groupe->count())
                       ->sortDesc()
                       ->take(5)
                       ->toArray();
    }

    private function calculerTendancePrix(Prescription $prescription): string
    {
        // Analyser l'évolution des prix sur les 6 derniers mois
        $prixHistorique = DB::table('prescriptions')
            ->where('patient_id', $prescription->patient_id)
            ->where('created_at', '>=', now()->subMonths(6))
            ->join('prescription_analyse', 'prescriptions.id', '=', 'prescription_analyse.prescription_id')
            ->selectRaw('DATE(prescriptions.created_at) as date, SUM(prescription_analyse.prix_unitaire) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        if ($prixHistorique->count() < 2) return 'stable';
        
        $premiereValeur = $prixHistorique->first()->total;
        $derniereValeur = $prixHistorique->last()->total;
        
        $variation = (($derniereValeur - $premiereValeur) / $premiereValeur) * 100;
        
        if ($variation > 10) return 'hausse';
        if ($variation < -10) return 'baisse';
        return 'stable';
    }

    private function calculerDelaiMoyenResultats(Prescription $prescription): ?int
    {
        // Calculer délai moyen basé sur l'historique des analyses similaires
        $analyseIds = $prescription->analyses->pluck('id');
        
        $delais = DB::table('prescription_analyse')
            ->whereIn('analyse_id', $analyseIds)
            ->whereNotNull('terminee_at')
            ->whereNotNull('demarree_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, demarree_at, terminee_at)) as delai_moyen')
            ->first();
        
        return $delais->delai_moyen ? (int) $delais->delai_moyen : null;
    }

    /**
     * Analyses fréquentes du patient pour suggestions
     */
    public function getAnalysesFrequentes(int $patientId, int $limite = 10): array
    {
        $cacheKey = "analyses_frequentes_{$patientId}_{$limite}";
        
        return Cache::remember($cacheKey, 3600, function() use ($patientId, $limite) {
            return DB::table('prescriptions')
                ->where('patient_id', $patientId)
                ->join('prescription_analyse', 'prescriptions.id', '=', 'prescription_analyse.prescription_id')
                ->join('analyses', 'prescription_analyse.analyse_id', '=', 'analyses.id')
                ->selectRaw('
                    analyses.id,
                    analyses.nom,
                    analyses.prix,
                    COUNT(*) as frequence,
                    MAX(prescriptions.created_at) as derniere_fois,
                    AVG(prescription_analyse.prix_unitaire) as prix_moyen
                ')
                ->groupBy('analyses.id', 'analyses.nom', 'analyses.prix')
                ->orderByDesc('frequence')
                ->orderByDesc('derniere_fois')
                ->limit($limite)
                ->get()
                ->toArray();
        });
    }

    /**
     * Patients récents pour l'utilisateur connecté
     */
    public function getPatientsRecents(int $userId, int $limite = 10): array
    {
        $cacheKey = "patients_recents_utilisateur_{$userId}_{$limite}";
        
        return Cache::remember($cacheKey, 1800, function() use ($userId, $limite) {
            return DB::table('prescriptions')
                ->where('secretaire_id', $userId)
                ->join('patients', 'prescriptions.patient_id', '=', 'patients.id')
                ->select([
                    'patients.id',
                    'patients.nom', 
                    'patients.prenom',
                    'patients.reference',
                    'patients.telephone',
                    'patients.statut',
                    DB::raw('MAX(prescriptions.created_at) as derniere_visite'),
                    DB::raw('COUNT(prescriptions.id) as nombre_prescriptions')
                ])
                ->groupBy('patients.id', 'patients.nom', 'patients.prenom', 'patients.reference', 'patients.telephone', 'patients.statut')
                ->orderByDesc('derniere_visite')
                ->limit($limite)
                ->get()
                ->toArray();
        });
    }

    /**
     * Création nouveau patient avec validation
     */
    public function creerPatient(array $donnees): Patient
    {
        // Validation unicité téléphone si fourni
        if (isset($donnees['telephone']) && !empty($donnees['telephone'])) {
            $existe = Patient::where('telephone', $donnees['telephone'])
                            ->whereNull('deleted_at')
                            ->exists();
            
            if ($existe) {
                throw new \InvalidArgumentException('Un patient avec ce numéro de téléphone existe déjà');
            }
        }
        
        // Génération référence unique
        $donnees['reference'] = $this->genererReferencePatient();
        
        // Détermination statut initial
        $donnees['statut'] = 'NOUVEAU';
        
        DB::beginTransaction();
        
        try {
            $patient = $this->patientRepository->create($donnees);
            
            // Log création patient
            logger()->info('Nouveau patient créé', [
                'patient_id' => $patient->id,
                'reference' => $patient->reference,
                'cree_par' => Auth::id()
            ]);
            
            DB::commit();
            
            // Invalider caches relatifs
            $this->invaliderCachesRecherche();
            
            return $patient;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Erreur création patient : " . $e->getMessage());
        }
    }

    /**
     * Mise à jour statut patient (NOUVEAU -> FIDELE -> VIP)
     */
    public function mettreAJourStatutPatient(int $patientId): void
    {
        $patient = Patient::find($patientId);
        if (!$patient) return;
        
        $nombrePrescriptions = $patient->prescriptions()->count();
        $montantTotal = $patient->prescriptions()
                              ->join('prescription_analyse', 'prescriptions.id', '=', 'prescription_analyse.prescription_id')
                              ->sum('prescription_analyse.prix_unitaire');
        
        $nouveauStatut = match(true) {
            $montantTotal > 10000 => 'VIP',
            $nombrePrescriptions >= 5 => 'FIDELE',
            default => $patient->statut
        };
        
        if ($nouveauStatut !== $patient->statut) {
            $patient->update(['statut' => $nouveauStatut]);
            
            // Invalider cache patient
            Cache::forget("patient_complet_{$patientId}");
        }
    }

    /**
     * Suggestions intelligentes basées sur l'historique
     */
    public function getSuggestionsIntelligentes(int $patientId): array
    {
        $historique = $this->getHistoriqueIntelligent($patientId, 20);
        
        // Analyses manquantes dans le suivi
        $analysesSuivi = $this->detecterAnalysesSuiviManquantes($historique);
        
        // Analyses saisonnières
        $analysesSaisonnieres = $this->detecterAnalysesSaisonnieres($patientId);
        
        // Analyses complémentaires
        $analysesComplementaires = $this->detecterAnalysesComplementaires($historique);
        
        return [
            'suivi_manquant' => $analysesSuivi,
            'saisonnieres' => $analysesSaisonnieres,
            'complementaires' => $analysesComplementaires,
        ];
    }

    private function detecterAnalysesSuiviManquantes(array $historique): array
    {
        // Logique détection analyses de suivi (ex: HbA1c tous les 3 mois pour diabétiques)
        $analysesRecurrentes = collect($historique)
            ->flatMap(fn($prescription) => $prescription['analyses'])
            ->groupBy('nom')
            ->filter(fn($groupe) => $groupe->count() >= 2);
        
        $suggestions = [];
        
        foreach ($analysesRecurrentes as $nomAnalyse => $occurrences) {
            $derniereDate = collect($historique)
                ->filter(fn($p) => collect($p['analyses'])->contains('nom', $nomAnalyse))
                ->first()['date'] ?? null;
            
            if ($derniereDate && now()->diffInMonths($derniereDate) >= 3) {
                $suggestions[] = [
                    'nom' => $nomAnalyse,
                    'raison' => 'Suivi périodique',
                    'derniere_fois' => $derniereDate,
                    'priorite' => 'moyenne'
                ];
            }
        }
        
        return $suggestions;
    }

    private function detecterAnalysesSaisonnieres(int $patientId): array
    {
        // Analyses saisonnières basées sur période de l'année
        $moisActuel = now()->month;
        
        $suggestions = [];
        
        // Période hivernale (Nov-Mars) : vitamines, défenses immunitaires
        if (in_array($moisActuel, [11, 12, 1, 2, 3])) {
            $suggestions[] = [
                'nom' => 'Vitamine D',
                'raison' => 'Période hivernale',
                'priorite' => 'faible'
            ];
        }
        
        // Période pré-estivale (Avril-Juin) : check-up général
        if (in_array($moisActuel, [4, 5, 6])) {
            $suggestions[] = [
                'nom' => 'Bilan lipidique',
                'raison' => 'Check-up saisonnier',
                'priorite' => 'faible'
            ];
        }
        
        return $suggestions;
    }

    private function detecterAnalysesComplementaires(array $historique): array
    {
        // Analyse des associations fréquentes d'analyses
        return []; // Logique complexe d'associations à implémenter
    }

    private function genererReferencePatient(): string
    {
        $annee = date('Y');
        $compteur = Cache::increment("compteur_patients_{$annee}", 1);
        
        if ($compteur === 1) {
            Cache::put("compteur_patients_{$annee}", 1, now()->endOfYear());
        }
        
        return 'PAT' . $annee . str_pad($compteur, 5, '0', STR_PAD_LEFT);
    }

    private function invaliderCachesRecherche(): void
    {
        // Invalider tous les caches de recherche (pattern matching)
        $tags = ['recherche_patient'];
        Cache::tags($tags)->flush();
    }
}