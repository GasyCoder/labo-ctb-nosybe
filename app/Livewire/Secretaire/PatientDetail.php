<?php

namespace App\Livewire\Secretaire;

use App\Models\Patient;
use Livewire\Component;

class PatientDetail extends Component
{
    public $patient;
    public Patient $patientModel; // Injection du modèle via la route
    
    // Propriétés pour les onglets
    public $activeTab = 'infos';
    
    // Propriétés pour les statistiques
    public $totalAnalyses = 0;
    public $totalPaiements = 0;
    public $montantTotal = 0;

    public function mount(Patient $patient)
    {
        $this->patientModel = $patient;
        $this->loadPatient();
        $this->loadStatistics();
    }

    public function loadPatient()
    {
        $this->patient = $this->patientModel->load(['prescriptions.analyses', 'prescriptions.paiements']);
    }

    public function loadStatistics()
    {
        if ($this->patient) {
            $this->totalAnalyses = $this->patient->prescriptions->count();
            $this->totalPaiements = $this->patient->prescriptions->flatMap->paiements->count();
            $this->montantTotal = $this->patient->prescriptions->flatMap->paiements->sum('montant');
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function deletePatient()
    {
        if ($this->patient) {
            $this->patient->delete();
            session()->flash('message', 'Patient supprimé avec succès');
            return redirect()->route('secretaire.patients');
        }
    }

    public function generateInvoice($paiementId)
    {
        // Logique pour générer une facture
        session()->flash('message', 'Facture générée avec succès');
    }

    public function render()
    {
        return view('livewire.secretaire.patient-detail');
    }
}