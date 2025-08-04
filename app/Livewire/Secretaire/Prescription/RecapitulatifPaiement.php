<?php

namespace App\Livewire\Secretaire\Prescription;

use App\Services\PaiementService;
use App\Services\AnalyseService;
use Livewire\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class RecapitulatifPaiement extends Component
{
    // 📝 DONNÉES REÇUES
    public array $analysesPanier = [];
    public float $montantTotal = 0;
    public ?array $patientSelectionne = null;
    public ?array $prescriptionData = null;
    
    // 💰 PAIEMENT
    #[Rule('required|in:ESPECES,CARTE,CHEQUE')]
    public string $modePaiement = 'ESPECES';
    
    #[Rule('required|numeric|min:0')]
    public float $montantPaye = 0;
    
    #[Rule('nullable|numeric|min:0|max:50')]
    public float $tauxRemise = 0;
    
    #[Rule('nullable|string|max:500')]
    public ?string $motifRemise = null;
    
    // 🧾 CALCULS
    public float $sousTotal = 0;
    public float $montantRemise = 0;
    public float $montantFinal = 0;
    public float $monnaieRendue = 0;
    
    // 📊 DÉTAILS PAIEMENT CARTE/CHÈQUE
    public ?string $numeroTransaction = null;
    public ?string $numeroAutorisation = null;
    public ?string $numeroCheque = null;
    public ?string $banqueCheque = null;
    
    // 🎯 ÉTAT INTERFACE
    public bool $calculatriceOuverte = false;
    public bool $validationEnCours = false;
    public array $erreursValidation = [];
    
    // 🔧 SERVICES
    protected PaiementService $paiementService;
    protected AnalyseService $analyseService;

    public function boot(
        PaiementService $paiementService,
        AnalyseService $analyseService
    ) {
        $this->paiementService = $paiementService;
        $this->analyseService = $analyseService;
    }

    public function mount(array $analysesPanier = [], float $montantTotal = 0)
    {
        $this->analysesPanier = $analysesPanier;
        $this->montantTotal = $montantTotal;
        $this->sousTotal = $montantTotal;
        $this->montantFinal = $montantTotal;
        
        // Pré-remplir montant payé
        $this->montantPaye = $montantTotal;
        
        $this->calculerTotaux();
    }

    // 💰 CALCULS AUTOMATIQUES
    public function updatedMontantPaye()
    {
        $this->calculerMonnaieRendue();
        $this->validerMontantPaye();
    }

    public function updatedTauxRemise()
    {
        $this->calculerTotaux();
        $this->validerRemise();
    }

    public function updatedModePaiement()
    {
        // Reset champs spécifiques selon mode
        $this->resetChampsModePaiement();
        $this->validerModePaiement();
    }

    private function calculerTotaux()
    {
        $this->sousTotal = collect($this->analysesPanier)->sum('prix');
        $this->montantRemise = ($this->sousTotal * $this->tauxRemise) / 100;
        $this->montantFinal = $this->sousTotal - $this->montantRemise;
        $this->calculerMonnaieRendue();
    }

    private function calculerMonnaieRendue()
    {
        $this->monnaieRendue = max(0, $this->montantPaye - $this->montantFinal);
    }

    private function resetChampsModePaiement()
    {
        $this->numeroTransaction = null;
        $this->numeroAutorisation = null;
        $this->numeroCheque = null;
        $this->banqueCheque = null;
    }

    // ✅ VALIDATIONS MÉTIER
    private function validerMontantPaye(): void
    {
        $this->erreursValidation = array_filter($this->erreursValidation, fn($key) => $key !== 'montant_paye', ARRAY_FILTER_USE_KEY);
        
        if ($this->montantPaye < $this->montantFinal) {
            $this->erreursValidation['montant_paye'] = 'Montant insuffisant';
        }
        
        // Validation montant cohérent (pas de montant aberrant)
        if ($this->montantPaye > ($this->montantFinal * 10)) {
            $this->erreursValidation['montant_paye'] = 'Montant anormalement élevé';
        }
    }

    private function validerRemise(): void
    {
        $this->erreursValidation = array_filter($this->erreursValidation, fn($key) => !str_starts_with($key, 'remise'), ARRAY_FILTER_USE_KEY);
        
        if ($this->tauxRemise > 0) {
            // Validation autorisation remise
            if (!$this->paiementService->autoriseRemise(Auth::user(), $this->tauxRemise)) {
                $this->erreursValidation['remise_autorisation'] = 'Remise non autorisée pour votre niveau';
            }
            
            // Validation motif requis
            if ($this->tauxRemise > 5 && empty($this->motifRemise)) {
                $this->erreursValidation['remise_motif'] = 'Motif requis pour remise > 5%';
            }
            
            // Validation montant minimum
            if ($this->montantFinal < 1000) {
                $this->erreursValidation['remise_minimum'] = 'Montant final trop faible';
            }
        }
    }

    private function validerModePaiement(): void
    {
        $this->erreursValidation = array_filter($this->erreursValidation, fn($key) => !str_starts_with($key, 'mode_paiement'), ARRAY_FILTER_USE_KEY);
        
        if ($this->modePaiement === 'CARTE') {
            if (empty($this->numeroTransaction)) {
                $this->erreursValidation['mode_paiement_carte'] = 'Numéro de transaction requis';
            }
        }
        
        if ($this->modePaiement === 'CHEQUE') {
            if (empty($this->numeroCheque) || empty($this->banqueCheque)) {
                $this->erreursValidation['mode_paiement_cheque'] = 'Numéro de chèque et banque requis';
            }
        }
    }

    // 🧮 CALCULATRICE
    public function toggleCalculatrice()
    {
        $this->calculatriceOuverte = !$this->calculatriceOuverte;
    }

    public function ajouterMontantCalculatrice(float $montant)
    {
        $this->montantPaye = $montant;
        $this->calculatriceOuverte = false;
        $this->calculerMonnaieRendue();
    }

    // 💫 RACCOURCIS PAIEMENT
    public function appliquerRaccourci(string $type)
    {
        match($type) {
            'montant_exact' => $this->montantPaye = $this->montantFinal,
            'arrondi_superieur' => $this->montantPaye = ceil($this->montantFinal / 1000) * 1000,
            'arrondi_5000' => $this->montantPaye = ceil($this->montantFinal / 5000) * 5000,
            'arrondi_10000' => $this->montantPaye = ceil($this->montantFinal / 10000) * 10000,
            default => null
        };
        
        $this->calculerMonnaieRendue();
    }

    public function appliquerRemiseType(string $type)
    {
        $remises = [
            'fidele' => 5,
            'senior' => 10,
            'etudiant' => 15,
            'personnel' => 20,
            'vip' => 25
        ];
        
        if (isset($remises[$type])) {
            $this->tauxRemise = $remises[$type];
            $this->motifRemise = "Remise $type";
            $this->calculerTotaux();
        }
    }

    // 📋 RÉCAPITULATIF DÉTAILLÉ
    public function getRecapitulatifPrelevementsProperty(): array
    {
        $prelevements = collect($this->analysesPanier)
            ->groupBy('prelevement_requis')
            ->map(function($analyses, $prelevement) {
                return [
                    'nom' => $prelevement ?: 'Non spécifié',
                    'analyses' => $analyses->pluck('nom')->toArray(),
                    'nombre_tubes' => $this->analyseService->calculerNombreTubes($analyses->toArray()),
                    'type_tube' => $this->analyseService->determinerTypeTube($analyses->toArray()),
                    'volume_total' => $analyses->sum('volume_echantillon_ml')
                ];
            })
            ->values()
            ->toArray();
        
        return $prelevements;
    }

    public function getAnalysesParCategorieProperty(): array
    {
        return collect($this->analysesPanier)
            ->groupBy('parent_nom')
            ->map(function($analyses, $categorie) {
                return [
                    'categorie' => $categorie ?: 'Divers',
                    'analyses' => $analyses->values()->toArray(),
                    'sous_total' => $analyses->sum('prix')
                ];
            })
            ->values()
            ->toArray();
    }

    // ✅ VALIDATION FINALE
    public function validerPaiement()
    {
        $this->validationEnCours = true;
        
        // Validation complète
        $this->validerMontantPaye();
        $this->validerRemise();
        $this->validerModePaiement();
        
        if (!empty($this->erreursValidation)) {
            $this->validationEnCours = false;
            session()->flash('error', 'Veuillez corriger les erreurs de validation');
            return;
        }
        
        // Validation métier finale
        if (!$this->paiementService->peutEncaisser(Auth::user(), $this->montantFinal)) {
            $this->validationEnCours = false;
            session()->flash('error', 'Vous n\'êtes pas autorisé à encaisser ce montant');
            return;
        }
        
        // Construire données paiement
        $donnesPaiement = $this->construireDonneesPaiement();
        
        // Émettre événement vers composant parent
        $this->dispatch('paiement-valide', donneesPaiement: $donnesPaiement);
        
        session()->flash('success', 'Paiement validé avec succès');
    }

    private function construireDonneesPaiement(): array
    {
        $donnees = [
            'mode_paiement' => $this->modePaiement,
            'montant_paye' => $this->montantPaye,
            'montant_final' => $this->montantFinal,
            'taux_remise' => $this->tauxRemise,
            'montant_remise' => $this->montantRemise,
            'motif_remise' => $this->motifRemise,
            'monnaie_rendue' => $this->monnaieRendue,
            'recu_par' => Auth::id(),
        ];
        
        // Ajout détails selon mode paiement
        match($this->modePaiement) {
            'CARTE' => $donnees = array_merge($donnees, [
                'numero_transaction' => $this->numeroTransaction,
                'numero_autorisation' => $this->numeroAutorisation,
            ]),
            'CHEQUE' => $donnees = array_merge($donnees, [
                'numero_cheque' => $this->numeroCheque,
                'banque_cheque' => $this->banqueCheque,
            ]),
            default => null
        };
        
        return $donnees;
    }

    // 🔄 RESET ET MODIFICATION
    public function modifierAnalyses()
    {
        $this->dispatch('retour-selection-analyses');
    }

    public function annulerPaiement()
    {
        $this->dispatch('paiement-annule');
        session()->flash('info', 'Paiement annulé');
    }

    // 📊 COMPUTED PROPERTIES
    public function getEstValidePourValidationProperty(): bool
    {
        return empty($this->erreursValidation) && 
               $this->montantPaye >= $this->montantFinal &&
               !empty($this->analysesPanier);
    }

    public function getInformationsComptablesProperty(): array
    {
        return [
            'tva_applicable' => false, // Laboratoires exonérés TVA à Madagascar
            'base_calcul' => $this->sousTotal,
            'remise_accordee' => $this->montantRemise,
            'montant_ht' => $this->montantFinal,
            'montant_ttc' => $this->montantFinal,
            'code_comptable' => $this->paiementService->getCodeComptable($this->modePaiement)
        ];
    }

    // 🎯 ÉVÉNEMENTS
    #[On('analyses-modifiees')]
    public function analysesModifiees(array $nouvelles)
    {
        $this->analysesPanier = $nouvelles;
        $this->calculerTotaux();
    }

    public function render()
    {
        return view('livewire.secretaire.prescription.recapitulatif-paiement', [
            'recapitulatifPrelevements' => $this->recapitulatifPrelevements,
            'analysesParCategorie' => $this->analysesParCategorie,
            'estValidePourValidation' => $this->estValidePourValidation,
            'informationsComptables' => $this->informationsComptables,
        ]);
    }
}