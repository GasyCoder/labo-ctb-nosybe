<?php

namespace App\Livewire\Secretaire\Prescription;

use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Analyse;
use App\Models\Prescripteur;
use App\Models\Prelevement;
use App\Models\Paiement;
use App\Models\Tube;
use App\Models\Setting;
use App\Models\PaymentMethod;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EditPrescription extends Component
{
    use WithPagination;

    #[Url(as: 'step', except: 'patient', history: true)]
    public string $etape = 'patient';

    public bool $isEditMode = true;
    public bool $activer_remise = false;
    public bool $afficherFactureComplete = false;

    public ?Patient $patient = null;
    public bool $nouveauPatient = false;
    public string $recherchePatient = '';

    public string $nom = '';
    public string $prenom = '';
    public string $civilite = 'Monsieur';
    public string $telephone = '';
    public string $email = '';

    public ?int $prescripteurId = null;
    public string $patientType = 'EXTERNE';
    public int $age = 0;
    public string $uniteAge = 'Ans';
    public ?float $poids = null;
    public ?string $renseignementClinique = null;

    public array $analysesPanier = [];
    public string $rechercheAnalyse = '';
    public ?int $categorieOuverte = null;
    public $parentRecherche = null;

    public array $prelevementsSelectionnes = [];
    public string $recherchePrelevement = '';

    public string $modePaiement = 'ESPECES';
    public float $montantPaye = 0;
    public float $remise = 0;
    public float $total = 0;
    public float $monnaieRendue = 0;
    public bool $paiementStatut = true;

    public array $tubesGeneres = [];
    public ?Prescription $prescription = null;
    public int $prescriptionId;

    public function mount($prescriptionId)
    {
        $this->prescriptionId = $prescriptionId;
        $this->loadPrescription();
        $this->validateEtape();
        
        $this->chargerSettingsRemise();
        
        $this->calculerTotaux();
        $this->isEditMode = true;
    }

    private function chargerSettingsRemise()
    {
        $setting = Setting::first();
        $this->activer_remise = $setting?->activer_remise ?? false;
    }

    public function getMethodesPaiementProperty()
    {
        return PaymentMethod::where('is_active', true)
                        ->orderBy('display_order')
                        ->get();
    }

    public function getTitle()
    {
        return 'Référence #' . $this->prescription->reference;
    }

    public function afficherFactureComplete()
    {
        $this->afficherFactureComplete = true;
    }

    public function fermerFacture()
    {
        $this->afficherFactureComplete = false;
    }

    private function loadPrescription()
    {
        $this->prescription = Prescription::with([
            'patient', 'analyses', 'prelevements', 'paiements.paymentMethod', 'tubes'
        ])->findOrFail($this->prescriptionId);

        // PATIENT
        $this->patient = $this->prescription->patient;
        $this->nom = $this->patient->nom;
        $this->prenom = $this->patient->prenom;
        $this->civilite = $this->patient->civilite;
        $this->telephone = $this->patient->telephone ?? '';
        $this->email = $this->patient->email ?? '';

        // CLINIQUE
        $this->prescripteurId = $this->prescription->prescripteur_id;
        $this->patientType = $this->prescription->patient_type;
        $this->age = $this->prescription->age;
        $this->uniteAge = $this->prescription->unite_age;
        $this->poids = $this->prescription->poids;
        $this->renseignementClinique = $this->prescription->renseignement_clinique;

        // ANALYSES
        $this->analysesPanier = [];
        foreach ($this->prescription->analyses as $analyse) {
            $this->analysesPanier[$analyse->id] = [
                'id' => $analyse->id,
                'designation' => $analyse->designation,
                'prix_original' => $analyse->prix,
                'prix_effectif' => $analyse->pivot->prix_effectif ?? $analyse->prix,
                'prix_affiche' => $analyse->pivot->prix_affiche ?? $analyse->prix,
                'prix' => $analyse->pivot->prix ?? $analyse->prix,
                'parent_nom' => $analyse->parent->designation ?? 'Analyse individuelle',
                'code' => $analyse->code,
                'parent_id' => $analyse->parent_id,
                'is_parent' => $analyse->level === 'PARENT',
                'enfants_inclus' => $analyse->enfants ? $analyse->enfants->pluck('designation')->toArray() : [],
            ];
        }

        // PRELEVEMENTS
        $this->prelevementsSelectionnes = [];
        foreach ($this->prescription->prelevements as $prelevement) {
            $this->prelevementsSelectionnes[$prelevement->id] = [
                'id' => $prelevement->id,
                'nom' => $prelevement->denomination,
                'description' => $prelevement->code ?? '',
                'prix' => $prelevement->pivot->prix_unitaire ?? $prelevement->prix ?? 0,
                'quantite' => $prelevement->pivot->quantite ?? 1,
                'type_tube_requis' => $prelevement->pivot->type_tube_requis ?? 'SEC',
                'volume_requis_ml' => $prelevement->pivot->volume_requis_ml ?? 5.0,
            ];
        }

        // PAIEMENT
        $lastPaiement = $this->prescription->paiements()->latest()->first();
        if ($lastPaiement && $lastPaiement->paymentMethod) {
            $this->modePaiement = $lastPaiement->paymentMethod->code;
            $this->paiementStatut = $lastPaiement->status ?? true;
        } else {
            $premiereMethode = PaymentMethod::where('is_active', true)
                                           ->orderBy('display_order')
                                           ->first();
            $this->modePaiement = $premiereMethode?->code ?? 'ESPECES';
            $this->paiementStatut = true;
        }
        
        $this->montantPaye = $lastPaiement ? $lastPaiement->montant : 0;
        $this->remise = $this->prescription->remise ?? 0;
        $this->total = $this->prescription->paiements()->sum('montant') ?? 0;
        $this->monnaieRendue = max(0, $this->montantPaye - $this->total);

        // TUBES
        $this->tubesGeneres = [];
        foreach ($this->prescription->tubes as $tube) {
            $this->tubesGeneres[] = [
                'id' => $tube->id,
                'numero_tube' => $tube->numero_tube,
                'code_barre' => $tube->code_barre,
                'statut' => $tube->statut,
                'type_tube' => $tube->type_tube,
                'volume_ml' => $tube->volume_ml,
            ];
        }
    }

    private function validateEtape()
    {
        $etapesValides = ['patient', 'clinique', 'analyses', 'prelevements', 'paiement', 'tubes', 'confirmation'];
        if (!in_array($this->etape, $etapesValides)) $this->etape = 'patient';
    }

    public function allerEtape(string $etape)
    {
        if (!$this->etapeAccessible($etape)) {
            flash()->warning('Veuillez compléter les étapes précédentes');
            return;
        }
        $this->etape = $etape;
        flash()->info('Navigation vers étape: ' . ucfirst($etape));
    }

    private function etapeAccessible(string $etape): bool
    {
        switch ($etape) {
            case 'patient': return true;
            case 'clinique': return $this->patient !== null;
            case 'analyses': return $this->patient !== null && $this->prescripteurId !== null;
            case 'prelevements': return !empty($this->analysesPanier);
            case 'paiement': return !empty($this->analysesPanier);
            case 'tubes': return $this->total > 0 && !empty($this->prelevementsSelectionnes);
            case 'confirmation': return (!empty($this->tubesGeneres) || empty($this->prelevementsSelectionnes)) || $this->etape === 'confirmation';
            default: return false;
        }
    }

    public function selectionnerPatient(int $patientId)
    {
        $this->patient = Patient::find($patientId);
        $this->nouveauPatient = false;
        
        $this->nom = $this->patient->nom;
        $this->prenom = $this->patient->prenom;
        $this->civilite = $this->patient->civilite;
        $this->telephone = $this->patient->telephone ?? '';
        $this->email = $this->patient->email ?? '';

        flash()->success("Patient « {$this->patient->nom} {$this->patient->prenom} » sélectionné - Vous pouvez modifier ses informations");
        $this->etape = 'patient';
    }
    
    public function creerNouveauPatient()
    {
        $this->nouveauPatient = true;
        $this->patient = null;
        $this->etape = 'patient';
        flash()->info('Nouveau Patient : Remplissez les informations ci-dessous');
    }

    public function validerNouveauPatient()
    {
        $this->validate([
            'nom' => 'required|min:2|max:50|regex:/^[a-zA-ZÀ-ÿ\s\-\']+$/',
            'prenom' => 'nullable|max:50|regex:/^[a-zA-ZÀ-ÿ\s\-\']*$/',
            'civilite' => 'required|in:' . implode(',', Patient::CIVILITES),
            'telephone' => 'nullable|regex:/^[0-9+\-\s()]{8,15}$/',
            'email' => 'nullable|email|max:255',
        ], [
            'nom.required' => 'Le nom est obligatoire',
            'nom.regex' => 'Le nom ne doit contenir que des lettres',
            'telephone.regex' => 'Format de téléphone invalide',
            'email.email' => 'Format email invalide'
        ]);
        
        try {
            if ($this->patient) {
                $this->patient->update([
                    'nom' => ucwords(strtolower(trim($this->nom))),
                    'prenom' => ucwords(strtolower(trim($this->prenom))),
                    'civilite' => $this->civilite,
                    'telephone' => trim($this->telephone),
                    'email' => strtolower(trim($this->email)),
                ]);
                
                flash()->success("Informations du patient « {$this->patient->nom} {$this->patient->prenom} » mises à jour");
            } else {
                $this->patient = Patient::create([
                    'nom' => ucwords(strtolower(trim($this->nom))),
                    'prenom' => ucwords(strtolower(trim($this->prenom))),
                    'civilite' => $this->civilite,
                    'telephone' => trim($this->telephone),
                    'email' => strtolower(trim($this->email)),
                ]);
                
                flash()->success("Nouveau patient « {$this->patient->nom} {$this->patient->prenom} » créé avec succès");
            }
            
            $this->nouveauPatient = false;
            $this->allerEtape('clinique');
            
        } catch (\Exception $e) {
            flash()->error('Erreur lors de ' . ($this->patient ? 'la modification' : 'la création') . ' du patient: ' . $e->getMessage());
        }
    }

    public function validerInformationsCliniques()
    {
        $this->validate([
            'prescripteurId' => 'required|exists:prescripteurs,id',
            'age' => 'required|integer|min:0|max:150',
            'patientType' => 'required|in:HOSPITALISE,EXTERNE,URGENCE-NUIT,URGENCE-JOUR',
            'poids' => 'nullable|numeric|min:0|max:500'
        ], [
            'prescripteurId.required' => 'Veuillez sélectionner un prescripteur',
            'prescripteurId.exists' => 'Prescripteur invalide',
            'age.required' => 'L\'âge est obligatoire',
            'age.min' => 'L\'âge doit être positif',
            'age.max' => 'L\'âge ne peut pas dépasser 150 ans',
            'poids.max' => 'Le poids ne peut pas dépasser 500 kg'
        ]);

        flash()->success('Informations cliniques validées');
        $this->allerEtape('analyses');
    }

    public function toggleCategorie(int $categorieId)
    {
        $this->categorieOuverte = $this->categorieOuverte === $categorieId ? null : $categorieId;
    }

    public function ajouterAnalyse(int $analyseId)
    {
        if (isset($this->analysesPanier[$analyseId])) {
            flash()->warning('Analyse déjà ajoutée au panier');
            return;
        }

        try {
            $analyse = Analyse::with(['parent', 'enfants'])->find($analyseId);
            
            if (!$analyse) {
                flash()->error('Analyse introuvable');
                return;
            }

            if ($analyse->level === 'PARENT') {
                $this->ajouterAnalyseParent($analyse);
            } else {
                $this->ajouterAnalyseIndividuelle($analyse);
            }

            $this->calculerTotaux();
            
        } catch (\Exception $e) {
            flash()->error('Erreur lors de l\'ajout de l\'analyse');
            Log::error('Erreur ajout analyse', ['error' => $e->getMessage(), 'analyse_id' => $analyseId]);
        }
    }

    private function ajouterAnalyseParent($analyse)
    {
        if ($analyse->prix <= 0) {
            flash()->error('Ce panel n\'a pas de prix défini');
            return;
        }

        $enfantsDejaPresents = [];
        foreach ($this->analysesPanier as $id => $item) {
            if ($item['parent_id'] == $analyse->id) {
                $enfantsDejaPresents[] = $item['designation'];
            }
        }

        if (!empty($enfantsDejaPresents)) {
            flash()->warning('Certaines analyses de ce panel sont déjà sélectionnées: ' . implode(', ', $enfantsDejaPresents));
            return;
        }

        $this->analysesPanier[$analyse->id] = [
            'id' => $analyse->id,
            'designation' => $analyse->designation,
            'prix_original' => $analyse->prix,
            'prix_effectif' => $analyse->prix,
            'prix_affiche' => $analyse->prix,
            'prix' => $analyse->prix,
            'parent_nom' => 'Panel complet',
            'code' => $analyse->code,
            'parent_id' => null,
            'is_parent' => true,
            'enfants_inclus' => $analyse->enfants->pluck('designation')->toArray(),
        ];

        $message = "Panel « {$analyse->designation} » ajouté au panier";
        if ($analyse->enfants->count() > 0) {
            $message .= " (inclut {$analyse->enfants->count()} analyses)";
        }
        
        flash()->success($message);
    }

    private function ajouterAnalyseIndividuelle($analyse)
    {
        if (!in_array($analyse->level, ['NORMAL', 'CHILD'])) {
            flash()->error('Type d\'analyse non valide');
            return;
        }

        if ($analyse->parent_id) {
            foreach ($this->analysesPanier as $item) {
                if ($item['id'] == $analyse->parent_id && isset($item['is_parent'])) {
                    flash()->warning("Cette analyse est déjà incluse dans le panel « {$item['designation']} »");
                    return;
                }
            }
        }

        $prixEffectif = $analyse->prix;
        $parentNom = 'Analyse individuelle';

        if ($analyse->parent && $analyse->parent->prix > 0) {
            $parentNom = $analyse->parent->designation . ' (partie)';
        } elseif ($analyse->parent) {
            $parentNom = $analyse->parent->designation;
        }

        $this->analysesPanier[$analyse->id] = [
            'id' => $analyse->id,
            'designation' => $analyse->designation,
            'prix_original' => $analyse->prix,
            'prix_effectif' => $prixEffectif,
            'prix_affiche' => $prixEffectif,
            'prix' => $prixEffectif,
            'parent_nom' => $parentNom,
            'code' => $analyse->code,
            'parent_id' => $analyse->parent_id,
            'is_parent' => false,
        ];

        flash()->success("Analyse « {$analyse->designation} » ajoutée au panier");
    }

    public function retirerAnalyse(int $analyseId)
    {
        if (isset($this->analysesPanier[$analyseId])) {
            $nom = $this->analysesPanier[$analyseId]['designation'];
            unset($this->analysesPanier[$analyseId]);
            $this->calculerTotaux();
            flash()->info("Analyse « {$nom} » retirée du panier");
        }
    }

    public function validerAnalyses()
    {
        if (empty($this->analysesPanier)) {
            flash()->error('Veuillez sélectionner au moins une analyse');
            return;
        }

        $conflits = $this->detecterConflitsParentEnfant();
        if (!empty($conflits)) {
            flash()->error('Conflits détectés: ' . implode(', ', $conflits));
            return;
        }

        flash()->success(count($this->analysesPanier) . ' analyse(s) sélectionnée(s)');
        $this->allerEtape('prelevements');
    }

    private function detecterConflitsParentEnfant()
    {
        $conflits = [];
        $parentsPresents = [];
        $enfantsPresents = [];

        foreach ($this->analysesPanier as $analyse) {
            if (isset($analyse['is_parent']) && $analyse['is_parent']) {
                $parentsPresents[] = $analyse['id'];
            } else {
                if ($analyse['parent_id']) {
                    $enfantsPresents[$analyse['parent_id']][] = $analyse['designation'];
                }
            }
        }

        foreach ($parentsPresents as $parentId) {
            if (isset($enfantsPresents[$parentId])) {
                $parent = Analyse::find($parentId);
                $conflits[] = "Panel {$parent->designation} en conflit avec ses analyses individuelles";
            }
        }

        return $conflits;
    }

    public function ajouterPrelevement(int $prelevementId)
    {
        if (isset($this->prelevementsSelectionnes[$prelevementId])) {
            flash()->warning('Prélèvement déjà ajouté');
            return;
        }

        try {
            $prelevement = Prelevement::find($prelevementId);
            
            if (!$prelevement) {
                flash()->error('Prélèvement introuvable');
                return;
            }
            
            $this->prelevementsSelectionnes[$prelevementId] = [
                'id' => $prelevement->id,
                'nom' => $prelevement->denomination,
                'description' => $prelevement->code ?? '',
                'prix' => $prelevement->prix ?? 0,
                'quantite' => 1,
                'type_tube_requis' => 'SEC',
                'volume_requis_ml' => 5.0,
            ];

            $this->calculerTotaux();
            flash()->success("Prélèvement « {$prelevement->denomination} » ajouté");
            
        } catch (\Exception $e) {
            flash()->error('Erreur lors de l\'ajout du prélèvement');
            Log::error('Erreur ajout prélèvement', ['error' => $e->getMessage(), 'prelevement_id' => $prelevementId]);
        }
    }

    public function retirerPrelevement(int $prelevementId)
    {
        if (isset($this->prelevementsSelectionnes[$prelevementId])) {
            $nom = $this->prelevementsSelectionnes[$prelevementId]['nom'];
            unset($this->prelevementsSelectionnes[$prelevementId]);
            $this->calculerTotaux();
            flash()->info("Prélèvement « {$nom} » retiré");
        }
    }

    public function modifierQuantitePrelevement(int $prelevementId, int $quantite)
    {
        if (isset($this->prelevementsSelectionnes[$prelevementId]) && $quantite > 0 && $quantite <= 10) {
            $this->prelevementsSelectionnes[$prelevementId]['quantite'] = $quantite;
            $this->calculerTotaux();
            flash()->info('Quantité mise à jour');
        }
    }

    public function validerPrelevements()
    {
        if (empty($this->prelevementsSelectionnes)) {
            flash()->info('Aucun prélèvement sélectionné - Passage direct au paiement');
        } else {
            flash()->success(count($this->prelevementsSelectionnes) . ' prélèvement(s) ajouté(s)');
        }

        $this->allerEtape('paiement');
    }

    private function calculerTotaux()
    {
        try {
            $sousTotal = 0;
            $parentsTraites = [];

            // Calcul des analyses
            foreach ($this->analysesPanier as $analyse) {
                if (isset($analyse['is_parent']) && $analyse['is_parent']) {
                    $sousTotal += $analyse['prix_effectif'];
                } else {
                    if ($analyse['parent_id'] && !in_array($analyse['parent_id'], $parentsTraites)) {
                        $parent = Analyse::find($analyse['parent_id']);
                        if ($parent && $parent->prix > 0) {
                            $sousTotal += $analyse['prix_effectif'];
                        } else {
                            $sousTotal += $analyse['prix_effectif'];
                        }
                    } else {
                        $sousTotal += $analyse['prix_effectif'];
                    }
                }
            }

            // Calcul des prélèvements
            $totalPrelevements = 0;
            foreach ($this->prelevementsSelectionnes as $prelevementData) {
                $prelevement = Prelevement::find($prelevementData['id']);
                if ($prelevement) {
                    $totalPrelevements += ($prelevement->prix ?? 0) * ($prelevementData['quantite'] ?? 1);
                }
            }

            $this->total = max(0, $sousTotal + $totalPrelevements - $this->remise);
            
            if ($this->montantPaye < $this->total) {
                $this->montantPaye = $this->total;
            }
            
            $this->calculerMonnaie();
            
        } catch (\Exception $e) {
            Log::error('Erreur calcul totaux', ['error' => $e->getMessage()]);
            $this->total = 0;
            $this->montantPaye = 0;
        }
    }

    public function updatedRemise()
    {
        $this->remise = max(0, $this->remise);
        $this->calculerTotaux();
    }

    public function updatedMontantPaye()
    {
        $this->montantPaye = max(0, $this->montantPaye);
        $this->calculerMonnaie();
    }

    private function calculerMonnaie()
    {
        $this->monnaieRendue = max(0, $this->montantPaye - $this->total);
    }

    public function validerPaiement()
    {
        $codesMethodesActives = PaymentMethod::where('is_active', true)
                                            ->pluck('code')
                                            ->toArray();
        
        $codesValidation = !empty($codesMethodesActives) 
            ? 'in:' . implode(',', $codesMethodesActives)
            : 'in:ESPECES,CARTE,CHEQUE,MOBILEMONEY';
        
        $this->validate([
            'modePaiement' => "required|{$codesValidation}",
            'montantPaye' => 'required|numeric|min:0',
            'remise' => 'nullable|numeric|min:0',
        ], [
            'modePaiement.required' => 'Veuillez sélectionner un mode de paiement',
            'modePaiement.in' => 'Mode de paiement non valide ou inactivé',
            'montantPaye.required' => 'Le montant payé est obligatoire',
            'montantPaye.min' => 'Le montant payé doit être positif',
        ]);

        if ($this->montantPaye < $this->total) {
            flash()->error('Montant payé insuffisant. Total: ' . number_format($this->total, 0) . ' Ar');
            return;
        }

        if (empty($this->analysesPanier)) {
            flash()->error('Aucune analyse sélectionnée');
            return;
        }
        
        $this->enregistrerPrescription();
    }

    public function terminerPrescription()
    {
        $this->allerEtape('confirmation');
        
        $message = 'Prescription mise à jour avec succès';
        
        if (!empty($this->tubesGeneres)) {
            $message .= ' - ' . count($this->tubesGeneres) . ' nouveau(x) tube(s) généré(s)';
        }
        
        session()->flash('success', $message);
    }

    private function genererTubesPourPrescription($prescription)
    {
        $tubes = [];
        
        try {
            // Utiliser la méthode statique du modèle Tube qui est déjà bien implémentée
            $tubesGeneres = Tube::genererPourPrescription($prescription->id);
            
            foreach ($tubesGeneres as $tube) {
                $tubes[] = [
                    'id' => $tube->id,
                    'numero_tube' => $tube->numero_tube, // Utilise l'accesseur
                    'code_barre' => $tube->code_barre,
                    'statut' => $tube->statut, // Utilise l'accesseur
                    'prelevement_nom' => $tube->prelevement->denomination ?? 'N/A'
                ];
            }

            // Mettre à jour le statut de la prescription
            $prescription->update(['status' => 'EN_ATTENTE']);
            
            flash()->success(count($tubes) . ' tube(s) généré(s) avec succès');
            
            Log::info('Tubes générés via Livewire', [
                'prescription_id' => $prescription->id,
                'tubes_count' => count($tubes)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur génération tubes via Livewire', [
                'prescription_id' => $prescription->id,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Erreur lors de la génération des tubes: ' . $e->getMessage());
        }

        return $tubes;
    }

    public function enregistrerPrescription()
    {
        try {
            DB::beginTransaction();

            if (!$this->patient) {
                throw new \Exception('Patient non défini');
            }

            if (!Prescripteur::find($this->prescripteurId)) {
                throw new \Exception('Prescripteur invalide');
            }

            if (empty($this->analysesPanier)) {
                throw new \Exception('Aucune analyse sélectionnée');
            }

            // 1. Mettre à jour la prescription
            $this->prescription->update([
                'patient_id' => $this->patient->id,
                'prescripteur_id' => $this->prescripteurId,
                'secretaire_id' => Auth::id(),
                'patient_type' => $this->patientType,
                'age' => $this->age,
                'unite_age' => $this->uniteAge,
                'poids' => $this->poids,
                'renseignement_clinique' => $this->renseignementClinique,
                'remise' => $this->remise,
                'status' => 'EN_ATTENTE',
                'updated_at' => now()
            ]);

            // 2. Associer les analyses
            $analyseIds = array_keys($this->analysesPanier);
            $analysesExistantes = Analyse::whereIn('id', $analyseIds)->pluck('id')->toArray();
            
            if (count($analysesExistantes) !== count($analyseIds)) {
                throw new \Exception('Certaines analyses sélectionnées n\'existent plus');
            }
            
            $this->prescription->analyses()->sync($analysesExistantes);

            // 3. Gérer les prélèvements via les tubes
            $this->prescription->tubes()->forceDelete(); // Use forceDelete to permanently remove tubes

            $tubesGeneres = collect();
            // Get the highest existing tube number for this prescription to avoid duplicates
            $existingTubesCount = Tube::withTrashed()
                ->where('prescription_id', $this->prescription->id)
                ->count();
            $compteurTube = $existingTubesCount + 1;

            foreach ($this->prelevementsSelectionnes as $prelevementData) {
                $quantite = max(1, $prelevementData['quantite'] ?? 1);
                $prelevement = Prelevement::find($prelevementData['id']);
                
                if (!$prelevement) {
                    throw new \Exception("Prélèvement ID {$prelevementData['id']} introuvable");
                }

                for ($i = 0; $i < $quantite; $i++) {
                    // Generate a unique code_barre
                    $codeBarre = $this->prescription->reference . '-T' . str_pad($compteurTube, 2, '0', STR_PAD_LEFT);
                    // Check for uniqueness, including soft-deleted records
                    while (Tube::withTrashed()->where('code_barre', $codeBarre)->exists()) {
                        $compteurTube++;
                        $codeBarre = $this->prescription->reference . '-T' . str_pad($compteurTube, 2, '0', STR_PAD_LEFT);
                    }

                    $tube = Tube::create([
                        'prescription_id' => $this->prescription->id,
                        'patient_id' => $this->patient->id,
                        'prelevement_id' => $prelevementData['id'],
                        'code_barre' => $codeBarre,
                        'type_tube' => $prelevementData['type_tube_requis'] ?? 'SEC',
                        'volume_ml' => $prelevementData['volume_requis_ml'] ?? 5.0,
                    ]);
                    $tubesGeneres->push($tube);
                    $compteurTube++;
                }
            }

            // 4. Mettre à jour les tubes générés dans la propriété
            $this->tubesGeneres = $tubesGeneres->map(function($tube) {
                return [
                    'id' => $tube->id,
                    'numero_tube' => $tube->numero_tube,
                    'code_barre' => $tube->code_barre,
                    'statut' => $tube->statut,
                    'type_tube' => $tube->type_tube,
                    'volume_ml' => $tube->volume_ml,
                    'prelevement_nom' => $tube->prelevement->denomination ?? 'N/A'
                ];
            })->toArray();

            // 5. Enregistrer le paiement
            $paymentMethod = PaymentMethod::where('code', $this->modePaiement)->first();
            
            if (!$paymentMethod) {
                throw new \Exception('Méthode de paiement invalide: ' . $this->modePaiement);
            }
            
            $this->prescription->paiements()->delete();
            
            Paiement::create([
                'prescription_id' => $this->prescription->id,
                'montant' => $this->total,
                'payment_method_id' => $paymentMethod->id,
                'recu_par' => Auth::id(),
                'status' => $this->paiementStatut
            ]);

            // 6. Navigation
            if (!empty($this->prelevementsSelectionnes)) {
                $this->allerEtape('tubes');
            } else {
                $this->allerEtape('confirmation');
            }

            DB::commit();

            flash()->success('Prescription modifiée avec succès!');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            Log::error('Database error in enregistrerPrescription', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'patient_id' => $this->patient?->id,
                'prescripteur_id' => $this->prescripteurId,
                'prescription_id' => $this->prescriptionId
            ]);
            
            flash()->error('Erreur de base de données: ' . ($e->getCode() == '23000' ? 'Conflit de code-barre unique.' : $e->getMessage()));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('General error in enregistrerPrescription', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'patient_id' => $this->patient?->id,
                'prescripteur_id' => $this->prescripteurId,
                'prescription_id' => $this->prescriptionId
            ]);
            
            flash()->error('Erreur lors de la modification: ' . $e->getMessage());
        }
    }

    public function getPatientsResultatsProperty()
    {
        if (strlen($this->recherchePatient) < 2) {
            return collect();
        }
        
        $terme = trim($this->recherchePatient);
        
        return Patient::where(function($query) use ($terme) {
                    $query->whereRaw('UPPER(nom) LIKE ?', ['%' . strtoupper($terme) . '%'])
                        ->orWhereRaw('UPPER(prenom) LIKE ?', ['%' . strtoupper($terme) . '%'])
                        ->orWhere('telephone', 'like', "%{$terme}%")
                        ->orWhere('numero_dossier', 'like', "%{$terme}%");
                })
                ->orderBy('nom')
                ->limit(10)
                ->get();
    }

    public function getCategoriesAnalysesProperty()
    {
        return Analyse::where('level', 'PARENT')
                     ->where('status', true)
                     ->with(['enfants' => function($query) {
                         $query->where('status', true)
                               ->whereIn('level', ['NORMAL', 'CHILD'])
                               ->orderBy('ordre')
                               ->orderBy('designation');
                     }])
                     ->orderBy('ordre')
                     ->orderBy('designation')
                     ->get();
    }

    public function getAnalysesRechercheProperty()
    {
        if (strlen($this->rechercheAnalyse) < 2) {
            $this->parentRecherche = null;
            return collect();
        }

        $terme = trim(strtoupper($this->rechercheAnalyse));
        $results = collect();

        $parents = Analyse::where('status', true)
                        ->where('level', 'PARENT')
                        ->where('prix', '>', 0)
                        ->where(function($query) use ($terme) {
                            $query->whereRaw('UPPER(code) LIKE ?', ["%{$terme}%"])
                                ->orWhereRaw('UPPER(designation) LIKE ?', ["%{$terme}%"]);
                        })
                        ->orderByRaw("
                            CASE 
                                WHEN UPPER(code) = ? THEN 1
                                WHEN UPPER(code) LIKE ? THEN 2
                                WHEN UPPER(designation) LIKE ? THEN 3
                                ELSE 4
                            END
                        ", [$terme, "{$terme}%", "%{$terme}%"])
                        ->limit(10)
                        ->get();

        $individuelles = Analyse::where('status', true)
                            ->whereIn('level', ['NORMAL', 'CHILD'])
                            ->where(function($query) use ($terme) {
                                $query->whereRaw('UPPER(code) LIKE ?', ["%{$terme}%"])
                                        ->orWhereRaw('UPPER(designation) LIKE ?', ["%{$terme}%"]);
                            })
                            ->with('parent')
                            ->orderByRaw("
                                CASE 
                                    WHEN UPPER(code) = ? THEN 1
                                    WHEN UPPER(code) LIKE ? THEN 2
                                    WHEN UPPER(designation) LIKE ? THEN 3
                                    ELSE 4
                                END
                            ", [$terme, "{$terme}%", "%{$terme}%"])
                            ->limit(15)
                            ->get();

        $results = $parents->concat($individuelles)->take(20);

        $this->parentRecherche = null;
        return $results;
    }

    public function getPrescripteursProperty()
    {
        return Prescripteur::where('is_active', true)
                          ->orderBy('nom')
                          ->get();
    }

    public function getPrelevementsDisponiblesProperty()
    {
        return Prelevement::where('is_active', true)
                         ->orderBy('denomination')
                         ->get();
    }

    public function getPrelevementsRechercheProperty()
    {
        if (strlen($this->recherchePrelevement) < 2) {
            return collect();
        }

        return Prelevement::where('is_active', true)
                         ->where(function($query) {
                             $query->where('denomination', 'like', "%{$this->recherchePrelevement}%")
                                   ->orWhere('code', 'like', "%{$this->recherchePrelevement}%");
                         })
                         ->orderBy('denomination')
                         ->limit(10)
                         ->get();
    }

    public function nouveauPrescription()
    {
        $this->reset([
            'patient', 'nouveauPatient', 'nom', 'prenom', 'civilite', 'telephone', 'email',
            'prescripteurId', 'age', 'poids', 'renseignementClinique',
            'analysesPanier', 'prelevementsSelectionnes', 'tubesGeneres',
            'montantPaye', 'remise', 'total', 'monnaieRendue', 'recherchePatient', 
            'rechercheAnalyse', 'recherchePrelevement'
        ]);
        
        $this->etape = 'patient';
        $this->age = 0;
        $this->uniteAge = 'Ans';
        $this->patientType = 'EXTERNE';
        $this->civilite = 'Monsieur';
        
        $this->chargerSettingsRemise();
        
        $premiereMethode = PaymentMethod::where('is_active', true)
                                       ->orderBy('display_order')
                                       ->first();
        $this->modePaiement = $premiereMethode?->code ?? 'ESPECES';
        
        $this->calculerTotaux();

        flash()->info('Nouvelle prescription initialisée');
    }

    public function getCivilitesDisponiblesProperty()
    {
        return [
            'Madame' => [
                'label' => 'Mme',
                'emoji' => '👩',
                'genre' => 'F',
                'type' => 'adulte'
            ],
            'Monsieur' => [
                'label' => 'M.',
                'emoji' => '👨',
                'genre' => 'M', 
                'type' => 'adulte'
            ],
            'Mademoiselle' => [
                'label' => 'Mlle',
                'emoji' => '👧',
                'genre' => 'F',
                'type' => 'adulte'
            ],
            'Enfant garçon' => [
                'label' => 'Garçon',
                'emoji' => '👦',
                'genre' => 'M',
                'type' => 'enfant'
            ],
            'Enfant fille' => [
                'label' => 'Fille',
                'emoji' => '👧',
                'genre' => 'F',
                'type' => 'enfant'
            ]
        ];
    }

    public function render()
    {
        return view('livewire.secretaire.prescription.form-prescription', [
            'patientsResultats' => $this->patientsResultats,
            'categoriesAnalyses' => $this->categoriesAnalyses,
            'analysesRecherche' => $this->analysesRecherche,
            'prescripteurs' => $this->prescripteurs,
            'prelevementsDisponibles' => $this->prelevementsDisponibles,
            'prelevementsRecherche' => $this->prelevementsRecherche,
            'prescription' => $this->prescription,
        ]);
    }
}