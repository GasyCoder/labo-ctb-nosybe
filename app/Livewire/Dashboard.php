<?php

namespace App\Livewire;

use App\Models\Patient;
use App\Models\Paiement;
use App\Models\Resultat;
use App\Models\Prescription;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $user;
    public $stats = [];

    public function mount()
    {
        $this->user = Auth::user();

        if (!$this->user) {
            abort(401, 'Utilisateur non connecté');
        }

        $this->loadStatistics();
    }

    public function loadStatistics()
    {
        $this->stats = [
            'patients' => $this->getPatientStats(),
            'analyses' => $this->getAnalyseStats(),
            'finances' => $this->getFinanceStats(),
            'activites' => $this->getRecentActivities(),
        ];
    }

    private function getPatientStats()
    {
        // Visible uniquement pour admin et secrétaires
        if (!in_array($this->user->type, ['admin', 'secretaire'])) {
            return [];
        }

        return [
            'total' => Patient::count(),
            'nouveaux' => Patient::where('statut', 'NOUVEAU')->count(),
            'fideles' => Patient::where('statut', 'FIDELE')->count(),
            'vip' => Patient::where('statut', 'VIP')->count(),
            'actifs_30j' => Patient::whereHas('prescriptions', function ($q) {
                $q->where('created_at', '>=', now()->subDays(30));
            })->count(),
        ];
    }

    private function getAnalyseStats()
    {
        // Visible pour admin, biologiste, technicien
        if (!in_array($this->user->type, ['admin', 'biologiste', 'technicien'])) {
            return [];
        }

        // Utiliser UNIQUEMENT les prescriptions - PAS d'AnalysePrescription
        $prescriptionStats = [
            'en_attente' => Prescription::where('status', 'EN_ATTENTE')->count(),
            'en_cours' => Prescription::where('status', 'EN_COURS')->count(),
            'terminees' => Prescription::where('status', 'TERMINE')->count(),
            'valides' => Prescription::where('status', 'VALIDE')->count(),
            'a_refaire' => Prescription::where('status', 'A_REFAIRE')->count(),
        ];

        // Nombre total d'analyses dans le système
        try {
            $totalAnalyses = DB::table('prescription_analyse')->count();
        } catch (\Exception $e) {
            $totalAnalyses = 0;
        }
        
        // Statistiques des résultats si la table existe
        $pathologiques = 0;
        $totalResultats = 0;
        
        try {
            if (class_exists('App\Models\Resultat')) {
                $pathologiques = Resultat::where('interpretation', 'PATHOLOGIQUE')->count();
                $totalResultats = Resultat::count();
            }
        } catch (\Exception $e) {
            // Ignorer si la table n'existe pas encore
        }

        return array_merge($prescriptionStats, [
            'pathologiques' => $pathologiques,
            'total_resultats' => $totalResultats,
            'total_analyses' => $totalAnalyses,
        ]);
    }

    private function getFinanceStats()
    {
        // Visible uniquement pour admin et secrétaires
        if (!in_array($this->user->type, ['admin', 'secretaire'])) {
            return [];
        }

        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        try {
            return [
                'recettes_jour' => Paiement::whereDate('created_at', $today)->sum('montant') ?? 0,
                'recettes_mois' => Paiement::whereDate('created_at', '>=', $startOfMonth)->sum('montant') ?? 0,
                'nb_paiements' => Paiement::whereDate('created_at', $today)->count() ?? 0,
                'moyenne_paiement' => Paiement::whereDate('created_at', $today)->avg('montant') ?? 0,
            ];
        } catch (\Exception $e) {
            return [
                'recettes_jour' => 0,
                'recettes_mois' => 0,
                'nb_paiements' => 0,
                'moyenne_paiement' => 0,
            ];
        }
    }

    private function getRecentActivities()
    {
        $activities = [];

        try {
            // Derniers patients créés (pour admin/secrétaire)
            if (in_array($this->user->type, ['admin', 'secretaire'])) {
                $recentPatients = Patient::latest()
                    ->limit(3)
                    ->get();

                foreach ($recentPatients as $patient) {
                    $activities[] = [
                        'message' => "Nouveau patient : {$patient->prenom} {$patient->nom}",
                        'time' => $patient->created_at->diffForHumans(),
                        'color' => 'green',
                        'type' => 'patient'
                    ];
                }
            }

            // Dernières validations de résultats (pour biologiste/admin)
            if (in_array($this->user->type, ['admin', 'biologiste'])) {
                try {
                    $recentValidations = Resultat::with(['analyse', 'prescription.patient'])
                        ->where('status', 'VALIDE')
                        ->whereNotNull('validated_at')
                        ->latest('validated_at')
                        ->limit(2)
                        ->get();

                    foreach ($recentValidations as $resultat) {
                        $patientNom = $resultat->prescription->patient->nom ?? 'Patient inconnu';
                        $analyseNom = $resultat->analyse->designation ?? 'Analyse inconnue';
                        
                        $activities[] = [
                            'message' => "Résultat validé : {$analyseNom} pour {$patientNom}",
                            'time' => $resultat->validated_at->diffForHumans(),
                            'color' => 'blue',
                            'type' => 'validation'
                        ];
                    }
                } catch (\Exception $e) {
                    // Table résultats n'existe peut-être pas encore
                }
            }

            // Derniers paiements (pour admin/secrétaire)
            if (in_array($this->user->type, ['admin', 'secretaire'])) {
                try {
                    $recentPayments = Paiement::with('prescription.patient')
                        ->latest()
                        ->limit(2)
                        ->get();

                    foreach ($recentPayments as $paiement) {
                        $patientNom = $paiement->prescription->patient->nom ?? 'Patient inconnu';
                        $montant = number_format($paiement->montant, 0, ',', ' ');
                        
                        $activities[] = [
                            'message' => "Paiement reçu : {$montant} Ar de {$patientNom}",
                            'time' => $paiement->created_at->diffForHumans(),
                            'color' => 'green',
                            'type' => 'paiement'
                        ];
                    }
                } catch (\Exception $e) {
                    // Ignorer si problème de relation
                }
            }

            // Prescriptions en attente (pour technicien/admin)
            if (in_array($this->user->type, ['admin', 'technicien'])) {
                $prescriptionsEnAttente = Prescription::with('patient')
                    ->where('status', 'EN_ATTENTE')
                    ->latest()
                    ->limit(2)
                    ->get();

                foreach ($prescriptionsEnAttente as $prescription) {
                    $patientNom = $prescription->patient->nom ?? 'Patient inconnu';
                    
                    // Compter les analyses pour cette prescription
                    $nbAnalyses = DB::table('prescription_analyse')
                        ->where('prescription_id', $prescription->id)
                        ->count();
                    
                    $activities[] = [
                        'message' => "Prescription en attente : {$nbAnalyses} analyse(s) pour {$patientNom}",
                        'time' => $prescription->created_at->diffForHumans(),
                        'color' => 'yellow',
                        'type' => 'attente'
                    ];
                }
            }

            // Trier par date (plus récent en premier)
            usort($activities, function ($a, $b) {
                return strcmp($a['time'], $b['time']);
            });

            // Limiter à 8 activités maximum
            return array_slice($activities, 0, 8);

        } catch (\Exception $e) {
            // En cas d'erreur, retourner un tableau vide
            return [];
        }
    }

    public function refreshStats()
    {
        $this->loadStatistics();
        session()->flash('message', 'Statistiques mises à jour');
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}