<?php

namespace App\Livewire\Secretaire\Prescription;

use App\Models\Patient;
use App\Models\Analyse;
use App\Models\Prescription;
use App\Models\Prescripteur;
use App\Services\PrescriptionService;
use App\Services\PatientService;
use App\Services\AnalyseService;
use App\Services\PaiementService;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class NouvellePrescription extends Component
{
    // 🎯 ÉTAT WORKFLOW COMPLET
    public string $etapeActuelle = 'recherche_patient';
    public array $etapesValidees = [];
    
    // 👤 PATIENT SÉLECTIONNÉ
    public ?Patient $patientSelectionne = null;
    public array $historiquePatient = [];
    
    // 📝 DONNÉES PRESCRIPTION
    #[Rule('required|exists:prescripteurs,id')]
    public ?int $prescripteurId = null;
    
    #[Rule('required|in:HOSPITALISE,EXTERNE,URGENCE-NUIT,URGENCE-JOUR')]
    public string $patientType = 'EXTERNE';
    
    #[Rule('required|integer|min:0|max:150')]
    public int $age = 0;
    
    #[Rule('required|in:Ans,Mois,Jours')]
    public string $uniteAge = 'Ans';
    
    #[Rule('nullable|numeric|min:0|max:300')]
    public ?float $poids = null;
    
    #[Rule('nullable|string|max:1000')]
    public ?string $renseignementClinique = null;
    
    // 🧪 ANALYSES SÉLECTIONNÉES
    public array $analysesPanier = [];
    public float $montantTotal = 0;
    public float $remise = 0;
    
    // 💰 PAIEMENT
    public string $modePaiement = 'ESPECES';
    public float $montantPaye = 0;
    public float $monnaieRendue = 0;
    
    // 🔧 SERVICES INJECTÉS
    protected PrescriptionService $prescriptionService;
    protected PatientService $patientService;
    protected AnalyseService $analyseService;
    protected PaiementService $paiementService;

    public function boot(
        PrescriptionService $prescriptionService,
        PatientService $patientService,
        AnalyseService $analyseService,
        PaiementService $paiementService
    ) {
        $this->prescriptionService = $prescriptionService;
        $this->patientService = $patientService;
        $this->analyseService = $analyseService;
        $this->paiementService = $paiementService;
    }

    public function mount()
    {
        $this->resetWorkflow();
    }

    // 🎯 GESTION WORKFLOW ÉTAPES
    public function allerEtape(string $etape)
    {
        $etapesOrdre = [
            'recherche_patient',
            'saisie_prescription', 
            'selection_analyses',
            'recapitulatif_paiement',
            'confirmation'
        ];
        
        $etapeActuelleIndex = array_search($this->etapeActuelle, $etapesOrdre);
        $nouvelleEtapeIndex = array_search($etape, $etapesOrdre);
        
        // Vérification : peut-on aller à cette étape ?
        if ($nouvelleEtapeIndex > $etapeActuelleIndex) {
            if (!$this->validerEtapeActuelle()) {
                return;
            }
        }
        
        $this->etapeActuelle = $etape;
        $this->dispatch('etape-changee', etape: $etape);
    }

    private function validerEtapeActuelle(): bool
    {
        return match($this->etapeActuelle) {
            'recherche_patient' => $this->patientSelectionne !== null,
            'saisie_prescription' => $this->validerDonneesPrescription(),
            'selection_analyses' => count($this->analysesPanier) > 0,
            'recapitulatif_paiement' => $this->montantPaye >= $this->montantTotal,
            default => true
        };
    }

    private function validerDonneesPrescription(): bool
    {
        return $this->prescripteurId && 
               $this->age > 0 && 
               in_array($this->patientType, ['HOSPITALISE','EXTERNE','URGENCE-NUIT','URGENCE-JOUR']);
    }

    // 👤 ÉVÉNEMENTS PATIENT
    #[On('patient-selectionne')]
    public function patientSelectionne(int $patientId)
    {
        $this->patientSelectionne = $this->patientService->getPatientAvecHistorique($patientId);
        $this->historiquePatient = $this->patientService->getHistoriqueIntelligent($patientId);
        
        // Pré-remplir l'âge si disponible depuis l'historique
        if (!empty($this->historiquePatient)) {
            $dernierePrescription = collect($this->historiquePatient)->first();
            $this->age = $dernierePrescription['age'] ?? 0;
            $this->uniteAge = $dernierePrescription['unite_age'] ?? 'Ans';
        }
        
        $this->marquerEtapeValidee('recherche_patient');
        $this->allerEtape('saisie_prescription');
    }

    // 🧪 ÉVÉNEMENTS ANALYSES
    #[On('analyse-ajoutee')]
    public function ajouterAnalyse(int $analyseId)
    {
        $analyse = $this->analyseService->getAnalyseAvecDetails($analyseId);
        
        if (!isset($this->analysesPanier[$analyseId])) {
            $this->analysesPanier[$analyseId] = [
                'id' => $analyse->id,
                'nom' => $analyse->nom,
                'prix' => $analyse->prix,
                'parent_nom' => $analyse->parent?->nom,
                'quantite' => 1,
                'prelevement_requis' => $analyse->prelevement_requis,
            ];
            
            $this->calculerMontantTotal();
            $this->dispatch('panier-mis-a-jour', panier: $this->analysesPanier);
        }
    }

    #[On('analyse-retiree')]
    public function retirerAnalyse(int $analyseId)
    {
        unset($this->analysesPanier[$analyseId]);
        $this->calculerMontantTotal();
        $this->dispatch('panier-mis-a-jour', panier: $this->analysesPanier);
    }

    private function calculerMontantTotal()
    {
        $total = collect($this->analysesPanier)
            ->sum(fn($analyse) => $analyse['prix'] * $analyse['quantite']);
        
        $this->montantTotal = $total - $this->remise;
        $this->calculerMonnaieRendue();
    }

    // 💰 GESTION PAIEMENT
    public function updatedMontantPaye()
    {
        $this->calculerMonnaieRendue();
    }

    public function updatedRemise()
    {
        $this->calculerMontantTotal();
    }

    private function calculerMonnaieRendue()
    {
        $this->monnaieRendue = max(0, $this->montantPaye - $this->montantTotal);
    }

    // ✅ VALIDATION FINALE
    public function validerPrescription()
    {
        $this->validate();
        
        if (!$this->validerPaiement()) {
            session()->flash('error', 'Montant payé insuffisant');
            return;
        }

        try {
            DB::beginTransaction();
            
            // Créer la prescription
            $prescription = $this->prescriptionService->creerPrescription([
                'patient_id' => $this->patientSelectionne->id,
                'prescripteur_id' => $this->prescripteurId,
                'patient_type' => $this->patientType,
                'age' => $this->age,
                'unite_age' => $this->uniteAge,
                'poids' => $this->poids,
                'renseignement_clinique' => $this->renseignementClinique,
                'remise' => $this->remise,
                'secretaire_id' => Auth::id(),
            ]);
            
            // Associer les analyses
            $this->prescriptionService->associerAnalyses($prescription, $this->analysesPanier);
            
            // Calculer et associer les prélèvements
            $this->prescriptionService->calculerPrelevements($prescription);
            
            // Enregistrer le paiement
            $this->paiementService->enregistrerPaiement($prescription, [
                'montant' => $this->montantPaye,
                'mode_paiement' => $this->modePaiement,
                'recu_par' => Auth::id(),
            ]);
            
            DB::commit();
            
            $this->etapeActuelle = 'confirmation';
            $this->dispatch('prescription-creee', prescriptionId: $prescription->id);
            session()->flash('success', 'Prescription créée avec succès !');
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors de la création : ' . $e->getMessage());
        }
    }

    private function validerPaiement(): bool
    {
        return $this->montantPaye >= $this->montantTotal;
    }

    // 🔄 RESET WORKFLOW
    public function nouveauPrescription()
    {
        $this->resetWorkflow();
        $this->dispatch('workflow-reinitialise');
    }

    private function resetWorkflow()
    {
        $this->etapeActuelle = 'recherche_patient';
        $this->etapesValidees = [];
        $this->patientSelectionne = null;
        $this->historiquePatient = [];
        $this->prescripteurId = null;
        $this->patientType = 'EXTERNE';
        $this->age = 0;
        $this->uniteAge = 'Ans';
        $this->poids = null;
        $this->renseignementClinique = null;
        $this->analysesPanier = [];
        $this->montantTotal = 0;
        $this->remise = 0;
        $this->modePaiement = 'ESPECES';
        $this->montantPaye = 0;
        $this->monnaieRendue = 0;
    }

    private function marquerEtapeValidee(string $etape)
    {
        if (!in_array($etape, $this->etapesValidees)) {
            $this->etapesValidees[] = $etape;
        }
    }

    // 📊 COMPUTED PROPERTIES
    public function getPrescripteursProperty()
    {
        return Cache::remember('prescripteurs_actifs', 3600, function() {
            return Prescripteur::where('is_active', true)
                              ->orderBy('nom')
                              ->get(['id', 'nom']);
        });
    }

    public function getProgressionProperty(): int
    {
        $etapes = ['recherche_patient', 'saisie_prescription', 'selection_analyses', 'recapitulatif_paiement', 'confirmation'];
        $etapeActuelleIndex = array_search($this->etapeActuelle, $etapes);
        return (int) (($etapeActuelleIndex + 1) / count($etapes) * 100);
    }

    public function render()
    {
        return view('livewire.secretaire.prescription.nouvelle-prescription', [
            'prescripteurs' => $this->prescripteurs,
            'progression' => $this->progression,
        ]);
    }
}