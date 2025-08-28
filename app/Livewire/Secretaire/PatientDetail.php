<?php

namespace App\Livewire\Secretaire;

use App\Models\Patient;
use Livewire\Component;
use Carbon\Carbon;

class PatientDetail extends Component
{
    public $patient;
    public Patient $patientModel;
    
    public $activeTab = 'infos';
    
    public $totalAnalyses = 0;
    public $totalPaiements = 0;
    public $montantTotal = 0;

    public $searchPrescriptions = '';
    public $filtreStatut = '';
    public $prescriptionsEtendues = [];
    
    public $showHistorique = false;
    public $joursRecents = 30;
    
    public $prescriptionsEnAttente = 0;
    public $prescriptionsEnCours = 0;
    public $prescriptionsTerminees = 0;

    // protected $listeners = [
    //     'refresh' => '$refresh',
    // ];

    public function mount(Patient $patient)
    {
        $this->patientModel = $patient;
        $this->loadPatient();
        $this->loadStatistics();
        $this->loadCounters();
    }

    public function loadPatient()
    {
        $this->patient = $this->patientModel->load([
            'prescriptions' => function ($query) {
                $query->with([
                    'prescripteur:id,nom,prenom',
                    'analyses' => function ($subQuery) {
                        $subQuery->select('analyses.id', 'analyses.designation', 'analyses.code', 'analyses.prix', 'analyses.parent_id')
                                 ->with('parent:id,designation');
                    },
                    'paiements' => function ($subQuery) {
                        $subQuery->select('id', 'prescription_id', 'montant', 'created_at')
                                 ->with('paymentMethod:id,code,label');
                    }
                ])->latest();
            }
        ]);
    }

    public function loadStatistics()
    {
        if ($this->patient) {
            $this->totalAnalyses = $this->patient->prescriptions->count();
            $this->totalPaiements = $this->patient->prescriptions->flatMap->paiements->count();
            $this->montantTotal = $this->patient->prescriptions->flatMap->paiements->sum('montant');
        }
    }

    public function loadCounters()
    {
        if ($this->patient) {
            $prescriptions = $this->patient->prescriptions;
            $this->prescriptionsEnAttente = $prescriptions->where('status', 'EN_ATTENTE')->count();
            $this->prescriptionsEnCours = $prescriptions->where('status', 'EN_COURS')->count();
            $this->prescriptionsTerminees = $prescriptions->where('status', 'TERMINE')->count();
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        if ($tab !== 'analyses') {
            $this->searchPrescriptions = '';
            $this->filtreStatut = '';
            $this->prescriptionsEtendues = [];
            $this->showHistorique = false;
        }
        \Log::debug('Active tab set to: ' . $tab);
    }

    public function getPrescriptionsFiltreesProperty()
    {
        $query = $this->patient->prescriptions()->with(['prescripteur', 'analyses']);
        
        if ($this->filtreStatut) {
            $query->where('status', $this->filtreStatut);
        }
        
        if ($this->searchPrescriptions) {
            $terme = '%' . strtolower($this->searchPrescriptions) . '%';
            $query->where(function ($q) use ($terme) {
                $q->whereRaw('LOWER(reference) LIKE ?', [$terme])
                  ->orWhereRaw('LOWER(status) LIKE ?', [$terme])
                  ->orWhereHas('prescripteur', fn($q) => $q->whereRaw('LOWER(nom) LIKE ?', [$terme]))
                  ->orWhereHas('analyses', fn($q) => $q->whereRaw('LOWER(designation) LIKE ?', [$terme])
                                                     ->orWhereRaw('LOWER(code) LIKE ?', [$terme]));
            });
        }
        
        return $query->get()->map(function ($prescription) {
            $prescription->status_label = match ($prescription->status) {
                'EN_ATTENTE' => 'En attente',
                'EN_COURS' => 'En cours',
                'TERMINE' => 'Terminée',
                default => $prescription->status,
            };
            return $prescription;
        })->values();
    }

    public function filtrerParStatut($statut)
    {
        \Log::debug('Filtrer par statut: ' . $statut);
        $this->filtreStatut = $statut;
        $this->searchPrescriptions = '';
        $this->prescriptionsEtendues = [];
        $this->showHistorique = false;
        $this->loadCounters();
    }

    public function resetSearch()
    {
        \Log::debug('Resetting search');
        $this->searchPrescriptions = '';
        $this->filtreStatut = '';
        $this->prescriptionsEtendues = [];
        $this->showHistorique = false;
        $this->loadCounters();
    }


    public function voirPrescription($prescriptionId)
    {
        \Log::debug('Viewing prescription: ' . $prescriptionId);
        return redirect()->route('secretaire.prescription.index', ['prescriptionId' => $prescriptionId]);
    }

    public function render()
    {
        return view('livewire.secretaire.patient-detail', [
            'prescriptionsFiltrees' => $this->prescriptionsFiltrees,
        ]);
    }
}