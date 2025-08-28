<?php

namespace App\Livewire\Secretaire;

use Livewire\Component;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Prescripteur;
use App\Models\Analyse;
use App\Models\Paiement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $periode = '30'; // 7, 30, 90, 365 jours
    public $showDetails = [];
    
    // Statistiques principales
    public $stats = [];
    
    // Données pour les graphiques
    public $prescriptionsParJour = [];
    public $paiementsParJour = [];
    public $statutsPrescriptions = [];
    public $topPrescripteurs = [];
    public $analysesPopulaires = [];
    
    // Activités récentes
    public $prescriptionsRecentes = [];
    public $paiementsRecents = [];
    
    // État d'actualisation
    public $lastUpdated;

    public function mount()
    {
        $this->lastUpdated = now();
        $this->loadDashboardData();
    }

    public function updatedPeriode()
    {
        $this->loadDashboardData();
        $this->lastUpdated = now();
    }

    public function refreshDashboard()
    {
        $this->loadDashboardData();
        $this->lastUpdated = now();
        
        session()->flash('message', 'Dashboard actualisé avec succès');
    }

    public function toggleDetails($section)
    {
        $this->showDetails[$section] = !($this->showDetails[$section] ?? false);
    }

    private function loadDashboardData()
    {
        $dateDebut = match($this->periode) {
            '7' => now()->subDays(7),
            '30' => now()->subDays(30),
            '90' => now()->subDays(90),
            '365' => now()->subYear(),
            default => now()->subDays(30)
        };

        $this->loadStatistiques($dateDebut);
        // $this->loadGraphiques($dateDebut);
        $this->loadActivitesRecentes();
    }

    private function loadStatistiques($dateDebut)
    {
        // Statistiques générales
        $totalPatients = Patient::count();
        $nouveauxPatients = Patient::where('created_at', '>=', $dateDebut)->count();
        
        $totalPrescriptions = Prescription::count();
        $prescriptionsPeriode = Prescription::where('created_at', '>=', $dateDebut)->count();
        
        $totalPrescripteurs = Prescripteur::where('is_active', true)->count();
        
        // Statistiques financières
        $chiffreAffairePeriode = Paiement::where('created_at', '>=', $dateDebut)->sum('montant');
        $chiffreAffaireTotal = Paiement::sum('montant');
        
        $commissionsPeriode = Paiement::where('created_at', '>=', $dateDebut)
            ->whereHas('prescription.prescripteur', function($q) {
                $q->where('status', '!=', 'BiologieSolidaire');
            })
            ->sum('commission_prescripteur');
        
        // Prescriptions par statut
        $prescriptionsEnAttente = Prescription::where('status', 'EN_ATTENTE')->count();
        $prescriptionsEnCours = Prescription::where('status', 'EN_COURS')->count();
        $prescriptionsTerminees = Prescription::where('status', 'TERMINE')
            ->where('created_at', '>=', $dateDebut)->count();

        // Calcul des pourcentages d'évolution
        $dateComparaison = match($this->periode) {
            '7' => now()->subDays(14),
            '30' => now()->subDays(60),
            '90' => now()->subDays(180),
            '365' => now()->subYears(2),
            default => now()->subDays(60)
        };

        $anciennesPrescriptions = Prescription::whereBetween('created_at', [
            $dateComparaison, $dateDebut
        ])->count();

        $evolutionPrescriptions = $anciennesPrescriptions > 0 
            ? (($prescriptionsPeriode - $anciennesPrescriptions) / $anciennesPrescriptions) * 100 
            : 100;

        $ancienCA = Paiement::whereBetween('created_at', [
            $dateComparaison, $dateDebut
        ])->sum('montant');

        $evolutionCA = $ancienCA > 0 
            ? (($chiffreAffairePeriode - $ancienCA) / $ancienCA) * 100 
            : 100;

        $this->stats = [
            'patients' => [
                'total' => $totalPatients,
                'nouveaux' => $nouveauxPatients,
                'evolution' => $this->calculerEvolutionPatients($dateDebut, $dateComparaison),
            ],
            'prescriptions' => [
                'total' => $totalPrescriptions,
                'periode' => $prescriptionsPeriode,
                'en_attente' => $prescriptionsEnAttente,
                'en_cours' => $prescriptionsEnCours,
                'terminees' => $prescriptionsTerminees,
                'evolution' => round($evolutionPrescriptions, 1),
            ],
            'financier' => [
                'ca_periode' => $chiffreAffairePeriode,
                'ca_total' => $chiffreAffaireTotal,
                'commissions' => $commissionsPeriode,
                'evolution' => round($evolutionCA, 1),
            ],
            'prescripteurs' => [
                'actifs' => $totalPrescripteurs,
            ],
        ];
    }

    private function calculerEvolutionPatients($dateDebut, $dateComparaison)
    {
        $anciensPatients = Patient::whereBetween('created_at', [
            $dateComparaison, $dateDebut
        ])->count();

        $nouveauxPatients = $this->stats['patients']['nouveaux'] ?? 0;
        
        return $anciensPatients > 0 
            ? round((($nouveauxPatients - $anciensPatients) / $anciensPatients) * 100, 1)
            : 100;
    }

  
    private function loadActivitesRecentes()
    {
        // Dernières prescriptions
        $this->prescriptionsRecentes = Prescription::with(['patient', 'prescripteur'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function($prescription) {
                return [
                    'id' => $prescription->id,
                    'reference' => $prescription->reference,
                    'patient' => $prescription->patient->nom_complet ?? 'N/A',
                    'prescripteur' => $prescription->prescripteur->nom_simple ?? 'N/A',
                    'status' => $prescription->status,
                    'created_at' => $prescription->created_at,
                    'montant' => $prescription->getMontantAnalysesCalcule()
                ];
            })->toArray();

        // Derniers paiements
        $this->paiementsRecents = Paiement::with(['prescription.patient'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function($paiement) {
                return [
                    'id' => $paiement->id,
                    'prescription_reference' => $paiement->prescription->reference ?? 'N/A',
                    'patient' => $paiement->prescription->patient->nom_complet ?? 'N/A',
                    'montant' => $paiement->montant,
                    'created_at' => $paiement->created_at
                ];
            })->toArray();
    }

    public function render()
    {
        return view('livewire.secretaire.dashboard');
    }
}