<?php

namespace App\Livewire\Secretaire\Prescription;

use Livewire\Component;
use App\Models\Prescription;
use App\Models\Patient;
use App\Models\Prescripteur;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

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
    public $prescripteur;
    public $age;
    public $uniteAge;
    public $poids;
    public $renseignementClinique;
    public $patientType;
    public $modePaiement;
    public $montantPaye = 0;
    public $monnaieRendue = 0;
    public $laboratoireInfo;

    protected $listeners = ['charger-facturation' => 'chargerFacturation'];

    public function mount()
    {
        // Charger les informations du laboratoire
        $this->laboratoireInfo = Setting::first();
        
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
            $this->prescription = Prescription::with([
                'patient', 
                'analyses', 
                'prelevements', 
                'prescripteur',
                'paiements.paymentMethod'
            ])->find($this->prescriptionId);
            
            if ($this->prescription) {
                $this->patient = $this->prescription->patient;
                $this->prescripteur = $this->prescription->prescripteur;
                
                // Préparer les analyses pour affichage
                $this->analysesPanier = $this->prescription->analyses->map(function($analyse) {
                    return [
                        'id' => $analyse->id,
                        'code' => $analyse->code,
                        'designation' => $analyse->designation,
                        'prix' => $analyse->prix,
                        'quantite' => 1,
                        'montant' => $analyse->prix
                    ];
                })->toArray();
                
                // Préparer les prélèvements pour affichage
                $this->prelevementsSelectionnes = $this->prescription->prelevements->map(function($prelevement) {
                    return [
                        'id' => $prelevement->id,
                        'nom' => $prelevement->nom,
                        'prix' => $prelevement->pivot->prix_unitaire ?? $prelevement->prix ?? 0,
                        'quantite' => $prelevement->pivot->quantite ?? 1,
                        'montant' => ($prelevement->pivot->prix_unitaire ?? $prelevement->prix ?? 0) * ($prelevement->pivot->quantite ?? 1)
                    ];
                })->toArray();
                
                $this->total = $this->prescription->montant_total;
                $this->remise = $this->prescription->remise ?? 0;
                $this->reference = $this->prescription->reference;
                $this->age = $this->prescription->age;
                $this->uniteAge = $this->prescription->unite_age ?? 'Ans';
                $this->poids = $this->prescription->poids;
                $this->renseignementClinique = $this->prescription->renseignement_clinique;
                $this->patientType = $this->prescription->patient_type ?? 'EXTERNE';
                
                // Informations de paiement
                $paiement = $this->prescription->paiements->first();
                if ($paiement) {
                    $this->modePaiement = $paiement->paymentMethod->name ?? 'ESPECES';
                    $this->montantPaye = $paiement->montant;
                    $this->monnaieRendue = max(0, $this->montantPaye - $this->total);
                }
            }
        }
    }

    public function imprimer()
    {
        $this->dispatchBrowserEvent('imprimer-facture');
    }

    public function telechargerPDF()
    {
        $this->dispatchBrowserEvent('telecharger-pdf-facture');
    }

    public function getSousTotal()
    {
        $sousTotal = 0;
        
        foreach ($this->analysesPanier as $analyse) {
            $sousTotal += $analyse['montant'] ?? 0;
        }
        
        foreach ($this->prelevementsSelectionnes as $prelevement) {
            $sousTotal += $prelevement['montant'] ?? 0;
        }
        
        return $sousTotal;
    }

    public function render()
    {
        return view('livewire.secretaire.prescription.facture-impression');
    }
}