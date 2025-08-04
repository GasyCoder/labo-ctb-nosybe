<?php

namespace App\Repositories;

use App\Models\Patient;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class PatientRepository
{
    protected Patient $model;

    public function __construct(Patient $patient)
    {
        $this->model = $patient;
    }

    /**
     * Recherche optimisée multi-critères avec performances
     */
    public function rechercher(
        string $terme, 
        string $critere = 'nom', 
        string $tri = 'recent',
        int $limite = 50
    ): Collection {
        $query = $this->model->newQuery()
            ->select([
                'id', 'reference', 'nom', 'prenom', 'sexe', 
                'telephone', 'email', 'statut', 'created_at'
            ]);

        // Application critère recherche
        match($critere) {
            'nom' => $query->where(function(Builder $q) use ($terme) {
                $q->whereRaw('LOWER(nom) LIKE ?', ["%{$terme}%"])
                  ->orWhereRaw('LOWER(prenom) LIKE ?', ["%{$terme}%"])
                  ->orWhereRaw("CONCAT(LOWER(nom), ' ', LOWER(prenom)) LIKE ?", ["%{$terme}%"]);
            }),
            'reference' => $query->where('reference', 'LIKE', "%{$terme}%"),
            'telephone' => $query->where('telephone', 'LIKE', "%{$terme}%"),
            'email' => $query->whereRaw('LOWER(email) LIKE ?', ["%{$terme}%"]),
            default => $query->where('nom', 'LIKE', "%{$terme}%")
        };

        // Tri optimisé avec sous-requêtes
        match($tri) {
            'recent' => $query->addSelect([
                    'derniere_prescription' => DB::table('prescriptions')
                        ->selectRaw('MAX(created_at)')
                        ->whereColumn('patient_id', 'patients.id')
                ])
                ->orderByDesc('derniere_prescription')
                ->orderByDesc('created_at'),
            
            'nom' => $query->orderBy('nom')->orderBy('prenom'),
            
            'statut' => $query->orderByRaw("
                CASE statut 
                    WHEN 'VIP' THEN 1 
                    WHEN 'FIDELE' THEN 2 
                    WHEN 'NOUVEAU' THEN 3 
                    ELSE 4 
                END
            "),
            
            'frequence' => $query->addSelect([
                    'nombre_prescriptions' => DB::table('prescriptions')
                        ->selectRaw('COUNT(*)')
                        ->whereColumn('patient_id', 'patients.id')
                ])
                ->orderByDesc('nombre_prescriptions'),
                
            default => $query->orderByDesc('created_at')
        };

        return $query->limit($limite)->get();
    }

    /**
     * Patient avec statistiques complètes (optimisé une seule requête)
     */
    public function getAvecStatistiques(int $patientId): Patient
    {
        return $this->model->select([
                'patients.*'
            ])
            ->addSelect([
                // Statistiques calculées en base
                'prescriptions_count' => DB::table('prescriptions')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('patient_id', 'patients.id')
                    ->whereNull('deleted_at'),
                
                'montant_total_depense' => DB::table('prescriptions')
                    ->join('prescription_analyse', 'prescriptions.id', '=', 'prescription_analyse.prescription_id')
                    ->selectRaw('COALESCE(SUM(prescription_analyse.prix_unitaire), 0)')
                    ->whereColumn('prescriptions.patient_id', 'patients.id')
                    ->whereNull('prescriptions.deleted_at'),
                
                'derniere_visite' => DB::table('prescriptions')
                    ->selectRaw('MAX(created_at)')
                    ->whereColumn('patient_id', 'patients.id')
                    ->whereNull('deleted_at'),
                
                'analyse_favorite' => DB::table('prescriptions')
                    ->join('prescription_analyse', 'prescriptions.id', '=', 'prescription_analyse.prescription_id')
                    ->join('analyses', 'prescription_analyse.analyse_id', '=', 'analyses.id')
                    ->selectRaw('analyses.nom')
                    ->whereColumn('prescriptions.patient_id', 'patients.id')
                    ->groupBy('analyses.id', 'analyses.nom')
                    ->orderByRaw('COUNT(*) DESC')
                    ->limit(1),
                
                'montant_moyen_prescription' => DB::table('prescriptions')
                    ->join('prescription_analyse', 'prescriptions.id', '=', 'prescription_analyse.prescription_id')
                    ->selectRaw('COALESCE(AVG(prescription_analyse.prix_unitaire), 0)')
                    ->whereColumn('prescriptions.patient_id', 'patients.id')
                    ->whereNull('prescriptions.deleted_at')
            ])
            ->findOrFail($patientId);
    }

    /**
     * Patient avec historique complet optimisé
     */
    public function getAvecHistoriqueComplet(int $patientId): Patient
    {
        return $this->model->with([
            'prescriptions' => function($query) {
                $query->latest()
                      ->limit(5)
                      ->with([
                          'prescripteur:id,nom,prenom,specialite',
                          'analyses:id,nom,prix',
                          'paiements:id,prescription_id,montant,mode_paiement,created_at'
                      ]);
            }
        ])->findOrFail($patientId);
    }

    /**
     * Historique prescriptions avec jointures optimisées
     */
    public function getHistoriquePrescriptions(int $patientId, int $limite = 10): Collection
    {
        return DB::table('prescriptions')
            ->select([
                'prescriptions.*',
                'prescripteurs.nom as prescripteur_nom',
                'prescripteurs.prenom as prescripteur_prenom',
                DB::raw('COALESCE(SUM(prescription_analyse.prix_unitaire), 0) as montant_total')
            ])
            ->leftJoin('prescripteurs', 'prescriptions.prescripteur_id', '=', 'prescripteurs.id')
            ->leftJoin('prescription_analyse', 'prescriptions.id', '=', 'prescription_analyse.prescription_id')
            ->where('prescriptions.patient_id', $patientId)
            ->whereNull('prescriptions.deleted_at')
            ->groupBy([
                'prescriptions.id', 'prescriptions.created_at', 'prescriptions.reference',
                'prescriptions.age', 'prescriptions.unite_age', 'prescriptions.patient_type',
                'prescriptions.status', 'prescriptions.renseignement_clinique',
                'prescripteurs.nom', 'prescripteurs.prenom'
            ])
            ->orderByDesc('prescriptions.created_at')
            ->limit($limite)
            ->get()
            ->map(function($prescription) {
                // Charger analyses pour chaque prescription
                $analyses = DB::table('prescription_analyse')
                    ->join('analyses', 'prescription_analyse.analyse_id', '=', 'analyses.id')
                    ->leftJoin('analyses as parents', 'analyses.parent_id', '=', 'parents.id')
                    ->select([
                        'analyses.id', 'analyses.nom', 'parents.nom as parent_nom',
                        'prescription_analyse.prix_unitaire', 'prescription_analyse.statut'
                    ])
                    ->where('prescription_analyse.prescription_id', $prescription->id)
                    ->get();
                
                $prescription->analyses = $analyses;
                return $prescription;
            });
    }

    /**
     * Création avec validation contraintes
     */
    public function create(array $donnees): Patient
    {
        // Validation contraintes unicité
        if (isset($donnees['telephone']) && !empty($donnees['telephone'])) {
            $existant = $this->model->where('telephone', $donnees['telephone'])
                                   ->whereNull('deleted_at')
                                   ->first();
            
            if ($existant) {
                throw new \InvalidArgumentException('Téléphone déjà utilisé par patient ID: ' . $existant->id);
            }
        }
        
        if (isset($donnees['email']) && !empty($donnees['email'])) {
            $existant = $this->model->where('email', $donnees['email'])
                                   ->whereNull('deleted_at')
                                   ->first();
            
            if ($existant) {
                throw new \InvalidArgumentException('Email déjà utilisé par patient ID: ' . $existant->id);
            }
        }
        
        return $this->model->create($donnees);
    }

    /**
     * Mise à jour avec historique
     */
    public function update(int $patientId, array $donnees): Patient
    {
        $patient = $this->model->findOrFail($patientId);
        
        // Sauvegarder ancien état pour audit
        $anciennesDonnees = $patient->only(array_keys($donnees));
        
        $patient->update($donnees);
        
        // Log des modifications
        if ($anciennesDonnees !== $donnees) {
            logger()->info('Patient modifié', [
                'patient_id' => $patientId,
                'anciennes_donnees' => $anciennesDonnees,
                'nouvelles_donnees' => $donnees,
                'modifie_par' => auth()->id()
            ]);
        }
        
        return $patient->fresh();
    }

    /**
     * Suppression soft avec vérifications
     */
    public function delete(int $patientId): bool
    {
        $patient = $this->model->findOrFail($patientId);
        
        // Vérifier prescriptions en cours
        $prescriptionsEnCours = $patient->prescriptions()
                                      ->whereIn('status', ['EN_ATTENTE', 'EN_COURS'])
                                      ->count();
        
        if ($prescriptionsEnCours > 0) {
            throw new \InvalidArgumentException(
                "Impossible de supprimer : {$prescriptionsEnCours} prescription(s) en cours"
            );
        }
        
        return $patient->delete();
    }

    /**
     * Recherche doublons potentiels
     */
    public function detecterDoublons(array $criteres): Collection
    {
        $query = $this->model->newQuery();
        
        if (isset($criteres['nom']) && isset($criteres['prenom'])) {
            $query->where('nom', 'LIKE', "%{$criteres['nom']}%")
                  ->where('prenom', 'LIKE', "%{$criteres['prenom']}%");
        }
        
        if (isset($criteres['telephone'])) {
            $query->orWhere('telephone', $criteres['telephone']);
        }
        
        if (isset($criteres['email'])) {
            $query->orWhere('email', $criteres['email']);
        }
        
        return $query->get(['id', 'reference', 'nom', 'prenom', 'telephone', 'email', 'created_at']);
    }

    /**
     * Statistiques globales patients
     */
    public function getStatistiquesGlobales(): array
    {
        return [
            'total' => $this->model->count(),
            'nouveaux_ce_mois' => $this->model->whereMonth('created_at', now()->month)
                                            ->whereYear('created_at', now()->year)
                                            ->count(),
            'par_statut' => $this->model->selectRaw('statut, COUNT(*) as nombre')
                                      ->groupBy('statut')
                                      ->pluck('nombre', 'statut')
                                      ->toArray(),
            'avec_telephone' => $this->model->whereNotNull('telephone')
                                          ->where('telephone', '!=', '')
                                          ->count(),
            'avec_email' => $this->model->whereNotNull('email')
                                      ->where('email', '!=', '')
                                      ->count(),
        ];
    }

    /**
     * Patients inactifs (sans prescription récente)
     */
    public function getPatientsInactifs(int $moisInactivite = 12): Collection
    {
        return $this->model->select(['id', 'reference', 'nom', 'prenom', 'telephone'])
            ->addSelect([
                'derniere_prescription' => DB::table('prescriptions')
                    ->selectRaw('MAX(created_at)')
                    ->whereColumn('patient_id', 'patients.id')
                    ->whereNull('deleted_at')
            ])
            ->having('derniere_prescription', '<', now()->subMonths($moisInactivite))
            ->orHavingNull('derniere_prescription')
            ->orderBy('derniere_prescription', 'asc')
            ->get();
    }

    /**
     * Export données patient (RGPD)
     */
    public function exporterDonneesPatient(int $patientId): array
    {
        $patient = $this->getAvecHistoriqueComplet($patientId);
        
        return [
            'informations_personnelles' => $patient->only([
                'reference', 'nom', 'prenom', 'sexe', 'telephone', 'email', 'statut', 'created_at'
            ]),
            'prescriptions' => $patient->prescriptions->map(function($prescription) {
                return [
                    'reference' => $prescription->reference,
                    'date' => $prescription->created_at,
                    'age' => $prescription->age,
                    'analyses' => $prescription->analyses->pluck('nom'),
                    'montant' => $prescription->paiements->sum('montant')
                ];
            }),
            'export_date' => now(),
            'total_prescriptions' => $patient->prescriptions->count(),
            'montant_total_depense' => $patient->prescriptions->sum(fn($p) => $p->paiements->sum('montant'))
        ];
    }
}