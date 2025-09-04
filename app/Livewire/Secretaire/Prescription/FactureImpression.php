<?php

namespace App\Livewire\Secretaire\Prescription;

use Livewire\Component;
use App\Models\Prescription;
use App\Models\Patient;
use App\Models\Prescripteur;

class FactureImpression extends Component
{
    public $prescriptionId;
    public $prescription;
    public $patient;
    public $analysesPanier = [];
    public $prelevementsSelectionnes = [];
    public $tubesGeneres = [];
    public $total = 0;
    public $remise = 0;
    public $reference;
    public $prescripteurId;
    public $age;
    public $uniteAge;
    public $poids;
    public $renseignementClinique;
    public $patientType;
    public $modePaiement;

    protected $listeners = ['charger-facturation' => 'chargerFacturation'];

    public function mount()
    {
        // Chargez les données initiales si prescriptionId est fourni
        if ($this->prescriptionId) {
            $this->loadPrescriptionData();
        }
    }

    public function chargerFacturation($params)
    {
        $this->prescriptionId = $params['prescriptionId'] ?? null;
        $this->loadPrescriptionData();
        $this->dispatchBrowserEvent('ouvrir-modal-facture');
    }

    public function loadPrescriptionData()
    {
        if ($this->prescriptionId) {
            $this->prescription = Prescription::with(['patient', 'analyses', 'prelevements'])->find($this->prescriptionId);
            
            if ($this->prescription) {
                $this->patient = $this->prescription->patient;
                $this->analysesPanier = $this->prescription->analyses->toArray();
                $this->prelevementsSelectionnes = $this->prescription->prelevements->toArray();
                $this->total = $this->prescription->montant_total;
                $this->remise = $this->prescription->remise ?? 0;
                $this->reference = $this->prescription->reference;
                $this->prescripteurId = $this->prescription->prescripteur_id;
                
                // Chargez les autres données nécessaires
                $this->age = $this->patient->age ?? null;
                $this->uniteAge = 'ans'; // Adaptez selon vos besoins
                $this->poids = $this->prescription->poids ?? null;
                $this->renseignementClinique = $this->prescription->renseignement_clinique ?? null;
                $this->patientType = $this->prescription->type_patient ?? 'EXTERNE';
                $this->modePaiement = $this->prescription->mode_paiement ?? 'CASH';
            }
        } else {
            // Si pas de prescription, utilisez les données de session ou par défaut
            $this->reference = 'PRES-' . date('Y') . '-XXXXX';
        }
    }

    public function imprimer()
    {
        $this->dispatchBrowserEvent('imprimer-facture');
    }

    public function render()
    {
        return view('livewire.secretaire.prescription.facture-impression');
    }
}