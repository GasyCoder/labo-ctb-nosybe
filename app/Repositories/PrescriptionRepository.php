<?php

namespace App\Repositories;

use App\Models\Prescription;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class PrescriptionRepository
{
    protected Prescription $model;

    public function __construct(Prescription $prescription)
    {
        $this->model = $prescription;
    }

    /**
     * Créer prescription avec relations
     */
    public function create(array $donnees): Prescription
    {
        return $this->model->create($donnees);
    }

    /**
     * Prescriptions en attente avec priorité
     */
    public function getEnAttente(int $limit = 50): Collection
    {
        return $this->model->with([
                'patient:id,nom,prenom,reference,statut',
                'prescripteur:id,nom,prenom',
                'secretaire:id,name'
            ])
            ->where('status', 'EN_ATTENTE')
            ->addSelect([
                'prescriptions.*',
                // Calculer priorité
                'priorite_score' => DB::raw("
                    CASE 
                        WHEN patient_type = 'URGENCE-NUIT' THEN 100
                        WHEN patient_type = 'URGENCE-JOUR' THEN 90
                        WHEN patient_type = 'HOSPITALISE' THEN 80
                        ELSE 50
                    END +
                    CASE 
                        WHEN age < 1 AND unite_age = 'Ans' THEN 20
                        WHEN age > 70 AND unite_age = 'Ans' THEN 15
                        ELSE 0
                    END
                "),
                // Temps d'attente en minutes
                'temps_attente' => DB::raw('TIMESTAMPDIFF(MINUTE, created_at, NOW())')
            ])
            ->orderByDesc('priorite_score')
            ->orderBy('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Prescriptions par patient avec pagination
     */
    public function getParPatient(int $patientId, int $limit = 10): Collection
    {
        return $this->model->with([
                'prescripteur:id,nom,prenom,specialite',
                'analyses:id,nom,prix',
                'paiements:id,prescription_id,montant,mode_paiement,created_at'
            ])
            ->where('patient_id', $patientId)
            ->addSelect([
                'prescriptions.*',
                // Montant total calculé
                'montant_total' => DB::table('prescription_analyse')
                    ->selectRaw('COALESCE(SUM(prix_unitaire * quantite), 0)')
                    ->whereColumn('prescription_id', 'prescriptions.id'),
                // Nombre d'analyses
                'nombre_analyses' => DB::table('prescription_analyse')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('prescription_id', 'prescriptions.id')
            ])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Statistiques détaillées
     */
    public function getStatistiques(array $filtres = []): array
    {
        $query = $this->model->newQuery();
        
        // Application des filtres
        if (isset($filtres['date_debut'])) {
            $query->where('created_at', '>=', $filtres['date_debut']);
        }
        
        if (isset($filtres['date_fin'])) {
            $query->where('created_at', '<=', $filtres['date_fin']);
        }
        
        if (isset($filtres['secretaire_id'])) {
            $query->where('secretaire_id', $filtres['secretaire_id']);
        }
        
        if (isset($filtres['patient_type'])) {
            $query->where('patient_type', $filtres['patient_type']);
        }
        
        // Statistiques de base
        $stats = [
            'nombre_total' => $query->count(),
            'par_status' => $query->selectRaw('status, COUNT(*) as nombre')
                                 ->groupBy('status')
                                 ->pluck('nombre', 'status')
                                 ->toArray(),
            'par_type_patient' => $query->selectRaw('patient_type, COUNT(*) as nombre')
                                       ->groupBy('patient_type')
                                       ->pluck('nombre', 'patient_type')
                                       ->toArray(),
        ];
        
        // Montants avec jointure
        $montants = DB::table('prescriptions')
            ->join('prescription_analyse', 'prescriptions.id', '=', 'prescription_analyse.prescription_id')
            ->when(isset($filtres['date_debut']), function($q) use ($filtres) {
                return $q->where('prescriptions.created_at', '>=', $filtres['date_debut']);
            })
            ->when(isset($filtres['date_fin']), function($q) use ($filtres) {
                return $q->where('prescriptions.created_at', '<=', $filtres['date_fin']);
            })
            ->selectRaw('
                COUNT(DISTINCT prescriptions.id) as prescriptions_avec_analyses,
                SUM(prescription_analyse.prix_unitaire * prescription_analyse.quantite) as chiffre_affaires,
                AVG(prescription_analyse.prix_unitaire * prescription_analyse.quantite) as panier_moyen,
                COUNT(prescription_analyse.id) as total_analyses
            ')
            ->first();
        
        $stats = array_merge($stats, [
            'chiffre_affaires' => $montants->chiffre_affaires ?? 0,
            'panier_moyen' => $montants->panier_moyen ?? 0,
            'total_analyses' => $montants->total_analyses ?? 0,
        ]);
        
        // Top analyses
        $stats['top_analyses'] = DB::table('prescription_analyse')
            ->join('analyses', 'prescription_analyse.analyse_id', '=', 'analyses.id')
            ->join('prescriptions', 'prescription_analyse.prescription_id', '=', 'prescriptions.id')
            ->when(isset($filtres['date_debut']), function($q) use ($filtres) {
                return $q->where('prescriptions.created_at', '>=', $filtres['date_debut']);
            })
            ->when(isset($filtres['date_fin']), function($q) use ($filtres) {
                return $q->where('prescriptions.created_at', '<=', $filtres['date_fin']);
            })
            ->selectRaw('
                analyses.nom,
                COUNT(*) as nombre_prescriptions,
                SUM(prescription_analyse.prix_unitaire) as chiffre_affaires_analyse
            ')
            ->groupBy('analyses.id', 'analyses.nom')
            ->orderByDesc('nombre_prescriptions')
            ->limit(10)
            ->get()
            ->toArray();
        
        // Performance secrétaires
        $stats['performance_secretaires'] = DB::table('prescriptions')
            ->join('users', 'prescriptions.secretaire_id', '=', 'users.id')
            ->when(isset($filtres['date_debut']), function($q) use ($filtres) {
                return $q->where('prescriptions.created_at', '>=', $filtres['date_debut']);
            })
            ->when(isset($filtres['date_fin']), function($q) use ($filtres) {
                return $q->where('prescriptions.created_at', '<=', $filtres['date_fin']);
            })
            ->selectRaw('
                users.name,
                COUNT(prescriptions.id) as nombre_prescriptions,
                AVG(TIMESTAMPDIFF(MINUTE, prescriptions.created_at, prescriptions.updated_at)) as temps_moyen_traitement
            ')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('nombre_prescriptions')
            ->get()
            ->toArray();
        
        return $stats;
    }

    /**
     * Recherche avancée prescriptions
     */
    public function rechercher(array $criteres, int $limit = 50): Collection
    {
        $query = $this->model->with([
            'patient:id,nom,prenom,reference',
            'prescripteur:id,nom,prenom',
            'secretaire:id,name'
        ]);
        
        // Recherche par référence
        if (!empty($criteres['reference'])) {
            $query->where('reference', 'LIKE', "%{$criteres['reference']}%");
        }
        
        // Recherche par patient
        if (!empty($criteres['patient_nom'])) {
            $query->whereHas('patient', function(Builder $q) use ($criteres) {
                $q->where('nom', 'LIKE', "%{$criteres['patient_nom']}%")
                  ->orWhere('prenom', 'LIKE', "%{$criteres['patient_nom']}%");
            });
        }
        
        // Recherche par prescripteur
        if (!empty($criteres['prescripteur_nom'])) {
            $query->whereHas('prescripteur', function(Builder $q) use ($criteres) {
                $q->where('nom', 'LIKE', "%{$criteres['prescripteur_nom']}%");
            });
        }
        
        // Filtres de date
        if (!empty($criteres['date_debut'])) {
            $query->whereDate('created_at', '>=', $criteres['date_debut']);
        }
        
        if (!empty($criteres['date_fin'])) {
            $query->whereDate('created_at', '<=', $criteres['date_fin']);
        }
        
        // Filtre status
        if (!empty($criteres['status'])) {
            if (is_array($criteres['status'])) {
                $query->whereIn('status', $criteres['status']);
            } else {
                $query->where('status', $criteres['status']);
            }
        }
        
        // Filtre type patient
        if (!empty($criteres['patient_type'])) {
            $query->where('patient_type', $criteres['patient_type']);
        }
        
        // Recherche dans analyses
        if (!empty($criteres['analyse_nom'])) {
            $query->whereHas('analyses', function(Builder $q) use ($criteres) {
                $q->where('nom', 'LIKE', "%{$criteres['analyse_nom']}%");
            });
        }
        
        // Tri
        $tri = $criteres['tri'] ?? 'recent';
        match($tri) {
            'ancien' => $query->orderBy('created_at'),
            'reference' => $query->orderBy('reference'),
            'patient' => $query->join('patients', 'prescriptions.patient_id', '=', 'patients.id')
                              ->orderBy('patients.nom'),
            'status' => $query->orderBy('status'),
            default => $query->orderByDesc('created_at')
        };
        
        return $query->limit($limit)->get();
    }

    /**
     * Prescriptions nécessitant attention (retards, problèmes)
     */
    public function getNecessitantAttention(): Collection
    {
        return $this->model->with(['patient:id,nom,prenom,reference'])
            ->where(function(Builder $query) {
                // Prescriptions en retard (plus de 2h en EN_ATTENTE)
                $query->where('status', 'EN_ATTENTE')
                      ->where('created_at', '<', now()->subHours(2))
                      
                      // Ou urgences non traitées (plus de 30min)
                      ->orWhere(function(Builder $subQuery) {
                          $subQuery->whereIn('patient_type', ['URGENCE-JOUR', 'URGENCE-NUIT'])
                                   ->where('status', 'EN_ATTENTE')
                                   ->where('created_at', '<', now()->subMinutes(30));
                      })
                      
                      // Ou analyses en cours depuis plus de 24h
                      ->orWhere(function(Builder $subQuery) {
                          $subQuery->where('status', 'EN_COURS')
                                   ->where('updated_at', '<', now()->subHours(24));
                      });
            })
            ->addSelect([
                'prescriptions.*',
                'temps_attente_minutes' => DB::raw('TIMESTAMPDIFF(MINUTE, created_at, NOW())'),
                'type_alerte' => DB::raw("
                    CASE 
                        WHEN patient_type IN ('URGENCE-JOUR', 'URGENCE-NUIT') AND status = 'EN_ATTENTE' 
                             AND created_at < DATE_SUB(NOW(), INTERVAL 30 MINUTE)
                        THEN 'URGENCE_RETARD'
                        
                        WHEN status = 'EN_ATTENTE' AND created_at < DATE_SUB(NOW(), INTERVAL 2 HOUR)
                        THEN 'ATTENTE_LONGUE'
                        
                        WHEN status = 'EN_COURS' AND updated_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)
                        THEN 'ANALYSE_BLOQUEE'
                        
                        ELSE 'AUTRE'
                    END
                ")
            ])
            ->orderByRaw("
                CASE type_alerte
                    WHEN 'URGENCE_RETARD' THEN 1
                    WHEN 'ANALYSE_BLOQUEE' THEN 2
                    WHEN 'ATTENTE_LONGUE' THEN 3
                    ELSE 4
                END
            ")
            ->orderByDesc('temps_attente_minutes')
            ->get();
    }

    /**
     * Suivi production journalière
     */
    public function getRapportProductionJour(string $date): array
    {
        $debut = $date . ' 00:00:00';
        $fin = $date . ' 23:59:59';
        
        return [
            // Créations
            'nouvelles_prescriptions' => $this->model
                ->whereBetween('created_at', [$debut, $fin])
                ->count(),
            
            // Progression dans workflow
            'prescriptions_terminees' => $this->model
                ->whereBetween('updated_at', [$debut, $fin])
                ->where('status', 'TERMINE')
                ->count(),
            
            'prescriptions_validees' => $this->model
                ->whereBetween('updated_at', [$debut, $fin])
                ->where('status', 'VALIDE')
                ->count(),
            
            // Répartition par type
            'par_type_patient' => $this->model
                ->whereBetween('created_at', [$debut, $fin])
                ->selectRaw('patient_type, COUNT(*) as nombre')
                ->groupBy('patient_type')
                ->pluck('nombre', 'patient_type')
                ->toArray(),
            
            // Temps moyens
            'temps_moyen_traitement' => DB::table('prescriptions')
                ->whereBetween('created_at', [$debut, $fin])
                ->where('status', 'TERMINE')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) as temps_moyen')
                ->value('temps_moyen'),
            
            // Analyses les plus demandées
            'top_analyses_jour' => DB::table('prescription_analyse')
                ->join('prescriptions', 'prescription_analyse.prescription_id', '=', 'prescriptions.id')
                ->join('analyses', 'prescription_analyse.analyse_id', '=', 'analyses.id')
                ->whereBetween('prescriptions.created_at', [$debut, $fin])
                ->selectRaw('analyses.nom, COUNT(*) as nombre')
                ->groupBy('analyses.id', 'analyses.nom')
                ->orderByDesc('nombre')
                ->limit(10)
                ->get()
                ->toArray(),
        ];
    }

    /**
     * Export données pour reporting
     */
    public function exporterDonnees(array $filtres): Collection
    {
        return DB::table('prescriptions')
            ->join('patients', 'prescriptions.patient_id', '=', 'patients.id')
            ->leftJoin('prescripteurs', 'prescriptions.prescripteur_id', '=', 'prescripteurs.id')
            ->join('users as secretaires', 'prescriptions.secretaire_id', '=', 'secretaires.id')
            ->leftJoin('prescription_analyse', 'prescriptions.id', '=', 'prescription_analyse.prescription_id')
            ->leftJoin('analyses', 'prescription_analyse.analyse_id', '=', 'analyses.id')
            ->leftJoin('paiements', 'prescriptions.id', '=', 'paiements.prescription_id')
            ->when(isset($filtres['date_debut']), function($q) use ($filtres) {
                return $q->where('prescriptions.created_at', '>=', $filtres['date_debut']);
            })
            ->when(isset($filtres['date_fin']), function($q) use ($filtres) {
                return $q->where('prescriptions.created_at', '<=', $filtres['date_fin']);
            })
            ->select([
                'prescriptions.reference as prescription_ref',
                'prescriptions.created_at as date_prescription',
                'prescriptions.status',
                'prescriptions.patient_type',
                'prescriptions.age',
                'prescriptions.unite_age',
                'patients.reference as patient_ref',
                'patients.nom as patient_nom',
                'patients.prenom as patient_prenom',
                'prescripteurs.nom as prescripteur_nom',
                'prescripteurs.prenom as prescripteur_prenom',
                'secretaires.name as secretaire_nom',
                'analyses.nom as analyse_nom',
                'prescription_analyse.prix_unitaire as prix_analyse',
                'paiements.montant as montant_paye',
                'paiements.mode_paiement',
            ])
            ->orderBy('prescriptions.created_at')
            ->get();
    }

    /**
     * Prescriptions patient pour historique médical
     */
    public function getHistoriqueMedical(int $patientId): Collection
    {
        return $this->model->with([
                'analyses' => function($query) {
                    $query->select(['analyses.id', 'analyses.nom', 'analyses.code'])
                          ->with('parent:id,nom');
                },
                'prescripteur:id,nom,prenom,specialite',
                'resultats' => function($query) {
                    $query->select(['id', 'prescription_id', 'analyse_id', 'valeur', 'valeur_normale', 'interpretation'])
                          ->where('valide', true);
                }
            ])
            ->where('patient_id', $patientId)
            ->where('status', 'VALIDE')
            ->orderByDesc('created_at')
            ->get()
            ->map(function($prescription) {
                return [
                    'date' => $prescription->created_at,
                    'prescripteur' => $prescription->prescripteur?->nom . ' ' . $prescription->prescripteur?->prenom,
                    'specialite' => $prescription->prescripteur?->specialite,
                    'renseignement_clinique' => $prescription->renseignement_clinique,
                    'analyses' => $prescription->analyses->map(function($analyse) use ($prescription) {
                        $resultat = $prescription->resultats->where('analyse_id', $analyse->id)->first();
                        return [
                            'nom' => $analyse->nom,
                            'famille' => $analyse->parent?->nom,
                            'resultat' => $resultat?->valeur,
                            'normal' => $resultat?->valeur_normale,
                            'interpretation' => $resultat?->interpretation,
                        ];
                    })
                ];
            });
    }
}