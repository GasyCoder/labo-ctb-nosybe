<?php

namespace App\Livewire\Secretaire\Prescription;

use App\Models\Tube;
use App\Models\Analyse;
use App\Models\Patient;
use App\Models\Setting;
use Livewire\Component;
use App\Models\Paiement;
use App\Models\Prelevement;
use App\Models\Prescripteur;
use App\Models\Prescription;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AddPrescription extends Component
{
    use WithPagination;

    // SESSION KEY POUR LA PERSISTANCE
    private const SESSION_KEY = 'prescription_en_cours';

    // 🎯 ÉTAPES WORKFLOW LABORATOIRE - INTÉGRATION URL
    #[Url(as: 'step', except: 'patient', history: true)]
    public string $etape = 'patient';

    public bool $isEditMode = false;
    public bool $activer_remise = false;
    public bool $afficherFactureComplete = false;

    public ?Prescription $prescription = null;
    
    // 👤 DONNÉES PATIENT
    public ?Patient $patient = null;
    public bool $nouveauPatient = false;
    public string $recherchePatient = '';

    // Données nouveau patient
    public string $nom = '';
    public string $prenom = '';
    public string $civilite = 'Monsieur';
    public string $telephone = '';
    public string $email = '';
    
    // 📋 INFORMATIONS CLINIQUES
    public ?int $prescripteurId = null;
    public string $patientType = 'EXTERNE';
    public int $age = 0;
    public string $uniteAge = 'Ans';
    public ?float $poids = null;
    public ?string $renseignementClinique = null;
    
    // 🧪 ANALYSES SÉLECTIONNÉES
    public array $analysesPanier = [];
    public string $rechercheAnalyse = '';
    public ?int $categorieOuverte = null;
    public $parentRecherche = null;
    
    // 🧾 PRÉLÈVEMENTS SÉLECTIONNÉES
    public array $prelevementsSelectionnes = [];
    public string $recherchePrelevement = '';
    
    // 💰 PAIEMENT
    public string $modePaiement = 'ESPECES';
    public float $montantPaye = 0;
    public float $remise = 0;
    public float $total = 0;
    public float $monnaieRendue = 0;
    public $reference;

    public bool $paiementStatut = true; // true = Payé, false = Non Payé
    
    // 🧪 TUBES
    public array $tubesGeneres = [];

    // =====================================
    // 💾 GESTION DE LA PERSISTANCE SESSION
    // =====================================

    protected function getPersistableProperties(): array
    {
        return [
            'etape', 'nouveauPatient', 'nom', 'prenom', 'civilite', 'telephone', 'email',
            'prescripteurId', 'patientType', 'age', 'uniteAge', 'poids', 'renseignementClinique',
            'analysesPanier', 'prelevementsSelectionnes', 'modePaiement', 'montantPaye', 
            'remise', 'total', 'monnaieRendue', 'reference', 'tubesGeneres', 'paiementStatut' // ← Ajoutez ceci
        ];
    }

    protected function sauvegarderSession(): void
    {
        try {
            $data = [];
            foreach ($this->getPersistableProperties() as $property) {
                $data[$property] = $this->$property;
            }
            
            // Sauvegarder l'ID du patient si présent
            if ($this->patient) {
                $data['patient_id'] = $this->patient->id;
            }

            session()->put(self::SESSION_KEY, $data);
        } catch (\Exception $e) {
            Log::error('Erreur sauvegarde session prescription', ['error' => $e->getMessage()]);
        }
    }

    protected function chargerSession(): void
    {
        try {
            $data = session()->get(self::SESSION_KEY);
            
            if (!$data || !is_array($data)) {
                return;
            }

            foreach ($this->getPersistableProperties() as $property) {
                if (isset($data[$property])) {
                    $this->$property = $data[$property];
                }
            }

            // Recharger le patient si ID présent
            if (!empty($data['patient_id'])) {
                $this->patient = Patient::find($data['patient_id']);
                if ($this->patient) {
                    // Synchroniser les données du patient
                    $this->nom = $this->patient->nom;
                    $this->prenom = $this->patient->prenom;
                    $this->civilite = $this->patient->civilite;
                    $this->telephone = $this->patient->telephone;
                    $this->email = $this->patient->email;
                }
            }

            // Recalculer les totaux après chargement
            $this->calculerTotaux();

        } catch (\Exception $e) {
            Log::error('Erreur chargement session prescription', ['error' => $e->getMessage()]);
        }
    }

    protected function viderSession(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    // =====================================
    // 🎯 HOOKS LIVEWIRE POUR AUTO-SAVE
    // =====================================

    public function updated($property, $value): void
    {
        $autoSaveProperties = [
            'nom', 'prenom', 'civilite', 'telephone', 'email',
            'prescripteurId', 'patientType', 'age', 'uniteAge', 'poids', 'renseignementClinique',
            'modePaiement', 'montantPaye', 'remise', 'paiementStatut' // ← Ajoutez ceci
        ];

        if (in_array($property, $autoSaveProperties)) {
            $this->sauvegarderSession();
        }
    }

    public function updatedAnalysesPanier(): void
    {
        $this->calculerTotaux();
        $this->sauvegarderSession();
    }

    public function updatedPrelevementsSelectionnes(): void
    {
        $this->calculerTotaux();
        $this->sauvegarderSession();
    }

    public function updatedRemise(): void
    {
        $this->remise = max(0, $this->remise);
        $this->calculerTotaux();
        $this->sauvegarderSession();
    }

    public function updatedMontantPaye(): void
    {
        $this->montantPaye = max(0, $this->montantPaye);
        $this->calculerMonnaie();
        $this->sauvegarderSession();
    }

    // =====================================
    // 🚀 MOUNT ET INITIALISATION
    // =====================================

    public function mount()
    {
        // Charger les données de session en premier
        $this->chargerSession();

        // S'assurer que la civilité a une valeur par défaut valide
        if (empty($this->civilite) || !in_array($this->civilite, Patient::CIVILITES)) {
            $this->civilite = 'Monsieur';
        }
        
        // Valider l'étape depuis l'URL
        $this->validateEtape();
        
        // Charger le setting de remise
        $this->chargerSettingsRemise();
        
        // Générer une référence si pas déjà présente
        if (empty($this->reference)) {
            $this->reference = (new Prescription())->genererReferenceUnique();
        }

        // Configurer le mode de paiement par défaut si pas défini
        if (empty($this->modePaiement)) {
            $premiereMethode = PaymentMethod::where('is_active', true)
                                ->orderBy('display_order')
                                ->first();
            $this->modePaiement = $premiereMethode?->code ?? 'ESPECES';
        }

        $this->isEditMode = false;
        
        // Recalculer les totaux
        $this->calculerTotaux();
        
        // Sauvegarder l'état initial
        $this->sauvegarderSession();
    }

    // =====================================
    // 📊 MÉTHODES POUR LA FACTURE (inchangées)
    // =====================================
    
    public function afficherFactureComplete()
    {
        $this->afficherFactureComplete = true;
    }

    public function fermerFacture()
    {
        $this->afficherFactureComplete = false;
    }

    public function facture()
    {
        if (!$this->prescription) {
            if ($this->reference) {
                $this->prescription = Prescription::where('reference', $this->reference)->first();
            }
            
            if (!$this->prescription) {
                return redirect()->back()->with('error', 'Aucune prescription à facturer');
            }
        }
        Log::info('Génération de la facture pour la prescription ID: ' . $this->prescription->id);
        
        return view('livewire.secretaire.prescription.facture-impression', [
            'prescription' => $this->prescription
        ]);
    }

    public function getTitle()
    {
        if ($this->prescription) {
            return 'Référence: ' . $this->prescription->reference;
        } elseif ($this->reference) {
            return 'Référence: ' . $this->reference;
        } else {
            return 'Nouvelle prescription';
        }
    }

    // =====================================
    // 📊 PROPRIÉTÉS CALCULÉES
    // =====================================
    
    public function getMethodesPaiementProperty()
    {
        return PaymentMethod::where('is_active', true)
                        ->orderBy('display_order')
                        ->get();
    }

    private function chargerSettingsRemise()
    {
        $setting = Setting::first();
        $this->activer_remise = $setting?->activer_remise ?? false;
    }

    // =====================================
    // 🌐 GESTION URL ET NAVIGATION
    // =====================================
    
    private function validateEtape()
    {
        $etapesValides = ['patient', 'clinique', 'analyses', 'prelevements', 'paiement', 'tubes', 'confirmation'];
        
        if (!in_array($this->etape, $etapesValides)) {
            $this->etape = 'patient';
        }
    }
    
    public function allerEtape(string $etape)
    {
        if (!$this->etapeAccessible($etape)) {
            flash()->warning('Veuillez compléter les étapes précédentes');
            return;
        }
        
        $this->etape = $etape;
        $this->sauvegarderSession(); // Sauvegarder le changement d'étape
        
        flash()->info('Navigation vers étape: ' . ucfirst($etape));
    }
    
    private function etapeAccessible(string $etape): bool
    {
        switch ($etape) {
            case 'patient':
                return true;
            case 'clinique':
                return $this->patient !== null;
            case 'analyses':
                return $this->patient !== null && $this->prescripteurId !== null;
            case 'prelevements':
                return !empty($this->analysesPanier);
            case 'paiement':
                return !empty($this->analysesPanier);
            case 'tubes':
                return $this->total > 0 && !empty($this->prelevementsSelectionnes);
            case 'confirmation':
                return (!empty($this->tubesGeneres) || empty($this->prelevementsSelectionnes)) || $this->etape === 'confirmation';
            default:
                return false;
        }
    }

    // =====================================
    // 👤 ÉTAPE 1: GESTION PATIENT
    // =====================================
    
    public function selectionnerPatient(int $patientId)
    {
        $this->patient = Patient::find($patientId);
        $this->nouveauPatient = false;
        
        // Pré-remplir avec dernières données connues
        $dernierePrescription = Prescription::where('patient_id', $patientId)->latest()->first();
        if ($dernierePrescription) {
            $this->age = $dernierePrescription->age ?? 0;
            $this->uniteAge = $dernierePrescription->unite_age ?? 'Ans';
            $this->poids = $dernierePrescription->poids;
            $this->prescripteurId = $dernierePrescription->prescripteur_id;
        }

        // Pré-remplir les informations du patient pour modification
        $this->nom = $this->patient->nom;
        $this->prenom = $this->patient->prenom;
        $this->civilite = $this->patient->civilite;
        $this->telephone = $this->patient->telephone;
        $this->email = $this->patient->email;
        
        $this->nouveauPatient = true;
        $this->etape = 'patient';
        
        // Sauvegarder immédiatement la sélection
        $this->sauvegarderSession();
        
        flash()->success("Patient « {$this->patient->nom} {$this->patient->prenom} » sélectionné - Vous pouvez modifier ses informations");
    }
    
    public function creerNouveauPatient()
    {
        $this->nouveauPatient = true;
        $this->patient = null;
        $this->etape = 'patient';
        
        // Vider les données du patient précédent
        $this->nom = '';
        $this->prenom = '';
        $this->civilite = 'Monsieur';
        $this->telephone = '';
        $this->email = '';
        
        $this->sauvegarderSession();
        
        flash()->info('Nouveau Patient : Remplissez les informations ci-dessous');
    }

    public function nouveauPrescription()
    {
        // Vider complètement la session
        $this->viderSession();
        
        $this->reset([
            'patient', 'nouveauPatient', 'nom', 'prenom', 'civilite', 'telephone', 'email',
            'prescripteurId', 'age', 'poids', 'renseignementClinique',
            'analysesPanier', 'prelevementsSelectionnes', 'tubesGeneres',
            'montantPaye', 'remise', 'total', 'monnaieRendue', 'recherchePatient', 
            'rechercheAnalyse', 'recherchePrelevement', 'afficherFactureComplete', 'prescription'
        ]);
        
        // Réinitialiser l'étape et l'URL
        $this->etape = 'patient';
        $this->age = 0;
        $this->uniteAge = 'Ans';
        $this->patientType = 'EXTERNE';
        $this->modePaiement = 'ESPECES';
        $this->civilite = 'Monsieur';
        
        // Recharger les settings
        $this->chargerSettingsRemise();
        
        // Générer nouvelle référence
        $this->reference = (new Prescription())->genererReferenceUnique();
        
        $this->calculerTotaux();

        // Sauvegarder le nouvel état
        $this->sauvegarderSession();

        flash()->info('Nouvelle prescription initialisée');
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
            'civilite.required' => 'La civilité est obligatoire',
            'civilite.in' => 'Civilité non valide',
            'telephone.regex' => 'Format de téléphone invalide',
            'email.email' => 'Format email invalide'
        ]);
        
        try {
            if ($this->patient) {
                // Mise à jour du patient existant
                $this->patient->update([
                    'nom' => ucwords(strtolower(trim($this->nom))),
                    'prenom' => ucwords(strtolower(trim($this->prenom))),
                    'civilite' => $this->civilite,
                    'telephone' => trim($this->telephone),
                    'email' => strtolower(trim($this->email)),
                ]);
                
                flash()->success("Informations du patient « {$this->patient->nom} {$this->patient->prenom} » mises à jour");
            } else {
                // Création d'un nouveau patient
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
            $this->sauvegarderSession(); // Sauvegarder après création/modification
            $this->allerEtape('clinique');
            
        } catch (\Exception $e) {
            flash()->error('Erreur lors de ' . ($this->patient ? 'la modification' : 'la création') . ' du patient: ' . $e->getMessage());
        }
    }

    public function getCivilitesDisponiblesProperty()
    {
        return [
            'Madame' => [
                'label' => '👩 Mme',
                'genre' => 'F',
                'type' => 'adulte'
            ],
            'Monsieur' => [
                'label' => '👨 M.',
                'genre' => 'M', 
                'type' => 'adulte'
            ],
            'Mademoiselle' => [
                'label' => '👧 Mlle',
                'genre' => 'F',
                'type' => 'adulte'
            ],
            'Enfant-garçon' => [
                'label' => '👦 Enfant garçon',
                'genre' => 'M',
                'type' => 'enfant'
            ],
            'Enfant-fille' => [
                'label' => '👧 Enfant fille', 
                'genre' => 'F',
                'type' => 'enfant'
            ]
        ];
    }

    // =====================================
    // 📋 ÉTAPE 2: INFORMATIONS CLINIQUES
    // =====================================
    
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

    // =====================================
    // 🧪 ÉTAPE 3: SÉLECTION ANALYSES
    // =====================================
    
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
            $this->sauvegarderSession(); // Auto-save après ajout
            
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

        // Vérifier si des enfants de ce parent sont déjà dans le panier
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

        // Vérifier si le parent de cette analyse est déjà dans le panier
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
            $this->sauvegarderSession(); // Auto-save après suppression
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

    // =====================================
    // 🧾 ÉTAPE 4: SÉLECTION PRÉLÈVEMENTS 
    // =====================================
    
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
                'nom' => $prelevement->nom,
                'description' => $prelevement->description ?? '',
                'prix' => $prelevement->prix ?? 0,
                'quantite' => 1,
                'type_tube_requis' => 'SEC',
                'volume_requis_ml' => 5.0,
            ];

            $this->calculerTotaux();
            $this->sauvegarderSession(); // Auto-save
            flash()->success("Prélèvement « {$prelevement->nom} » ajouté");
            
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
            $this->sauvegarderSession(); // Auto-save
            flash()->info("Prélèvement « {$nom} » retiré");
        }
    }
    
    public function modifierQuantitePrelevement(int $prelevementId, int $quantite)
    {
        if (isset($this->prelevementsSelectionnes[$prelevementId]) && $quantite > 0 && $quantite <= 10) {
            $this->prelevementsSelectionnes[$prelevementId]['quantite'] = $quantite;
            $this->calculerTotaux();
            $this->sauvegarderSession(); // Auto-save
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

    // =====================================
    // 💰 ÉTAPE 5: PAIEMENT
    // =====================================
    
    private function calculerTotaux()
    {
        try {
            $sousTotal = 0;
            $parentsTraites = [];

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

            $totalPrelevements = 0;
            foreach ($this->prelevementsSelectionnes as $prelevement) {
                $totalPrelevements += ($prelevement['prix'] ?? 0) * ($prelevement['quantite'] ?? 1);
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

    // =====================================
    // 💾 ENREGISTREMENT COMPLET
    // =====================================
    
    private function enregistrerPrescription()
    {
        try {
            DB::beginTransaction();
            
            if (!$this->patient) {
                throw new \Exception('Patient non défini');
            }

            if (!Prescripteur::find($this->prescripteurId)) {
                throw new \Exception('Prescripteur invalide');
            }
            
            // 1. Créer la prescription
            $prescription = Prescription::create([
                'patient_id' => $this->patient->id,
                'prescripteur_id' => $this->prescripteurId,
                'secretaire_id' => Auth::user()->id,
                'patient_type' => $this->patientType,
                'age' => $this->age,
                'unite_age' => $this->uniteAge,
                'poids' => $this->poids,
                'renseignement_clinique' => $this->renseignementClinique,
                'remise' => $this->remise,
                'status' => 'EN_ATTENTE'
            ]);
            
            $this->prescription = $prescription;
            $this->reference = $prescription->reference;
            
            // 2. Associer les analyses (SIMPLIFIÉ - sans is_payer et prix)
            $analyseIds = array_keys($this->analysesPanier);
            $analysesExistantes = Analyse::whereIn('id', $analyseIds)->pluck('id')->toArray();
            
            if (count($analysesExistantes) !== count($analyseIds)) {
                throw new \Exception('Certaines analyses sélectionnées n\'existent plus');
            }
            
            // Utiliser sync() pour associer uniquement les IDs
            $prescription->analyses()->sync($analysesExistantes);
            
            // 3. Associer les prélèvements (SIMPLIFIÉ - sans is_payer)
            if (!empty($this->prelevementsSelectionnes)) {
                foreach ($this->prelevementsSelectionnes as $prelevement) {
                    if (!Prelevement::find($prelevement['id'])) {
                        throw new \Exception('Prélèvement ID ' . $prelevement['id'] . ' invalide');
                    }

                    $prescription->prelevements()->attach($prelevement['id'], [
                        'prix_unitaire' => $prelevement['prix'] ?? 0,
                        'quantite' => max(1, $prelevement['quantite'] ?? 1),
                        'type_tube_requis' => $prelevement['type_tube_requis'] ?? 'SEC',
                        'volume_requis_ml' => $prelevement['volume_requis_ml'] ?? 5.0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
            
            // 4. Enregistrer le paiement (SOURCE DE VÉRITÉ UNIQUE)
            $paymentMethod = PaymentMethod::where('code', $this->modePaiement)->first();
            
            if (!$paymentMethod) {
                throw new \Exception('Méthode de paiement invalide: ' . $this->modePaiement);
            }
            
            Paiement::create([
                'prescription_id' => $prescription->id,
                'montant' => $this->total,
                'payment_method_id' => $paymentMethod->id,
                'recu_par' => Auth::user()->id,
                'status' => $this->paiementStatut // true = payé, false = non payé
            ]);
            
            // 5. Générer les tubes si prélèvements présents
            if (!empty($this->prelevementsSelectionnes)) {
                $this->tubesGeneres = $this->genererTubesPourPrescription($prescription);
                $this->allerEtape('tubes');
            } else {
                $this->allerEtape('confirmation');
            }
            
            DB::commit();
            
            // Vider la session après succès
            $this->viderSession();
            
            flash()->success('Prescription enregistrée avec succès!');
            
            Log::info('Prescription créée avec succès', [
                'prescription_id' => $prescription->id,
                'reference' => $prescription->reference,
                'patient_id' => $this->patient->id,
                'montant_total' => $this->total,
                'paiement_status' => $this->paiementStatut,
                'analyses_count' => count($analyseIds),
                'prelevements_count' => count($this->prelevementsSelectionnes)
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erreur enregistrement prescription', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'patient_id' => $this->patient?->id,
                'prescripteur_id' => $this->prescripteurId,
                'analyses_panier' => array_keys($this->analysesPanier ?? []),
                'total' => $this->total
            ]);
            
            flash()->error('Erreur lors de l\'enregistrement: ' . $e->getMessage());
        }
    }


    private function genererTubesPourPrescription($prescription)
    {
        $tubes = [];
        
        try {
            foreach ($prescription->prelevements as $prelevement) {
                $quantite = $prelevement->pivot->quantite ?? 1;
                
                for ($i = 0; $i < $quantite; $i++) {
                    $tube = new Tube([
                        'prescription_id' => $prescription->id,
                        'patient_id' => $prescription->patient_id,
                        'prelevement_id' => $prelevement->id,
                        'type_tube' => $prelevement->pivot->type_tube_requis ?? 'SEC',
                        'volume_ml' => $prelevement->pivot->volume_requis_ml ?? 5.0,
                        'statut' => 'GENERE',
                        'genere_at' => now(),
                    ]);
                    
                    $tube->save();

                    // Générer codes après sauvegarde pour avoir l'ID
                    $tube->code_barre = 'T' . date('Y') . str_pad($tube->id, 6, '0', STR_PAD_LEFT);
                    $tube->numero_tube = 'T-' . date('Y') . '-' . str_pad($tube->id, 6, '0', STR_PAD_LEFT);
                    $tube->save();

                    $tubes[] = [
                        'id' => $tube->id,
                        'numero_tube' => $tube->numero_tube,
                        'code_barre' => $tube->code_barre,
                        'statut' => $tube->statut,
                        'type_tube' => $tube->type_tube,
                        'volume_ml' => $tube->volume_ml,
                        'prelevement_nom' => $prelevement->nom
                    ];
                }
            }

            // Mettre à jour le statut de la prescription
            $prescription->update(['status' => 'EN_ATTENTE']);
            
            flash()->success(count($tubes) . ' tube(s) généré(s) avec succès');
            
            Log::info('Tubes générés', [
                'prescription_id' => $prescription->id,
                'tubes_count' => count($tubes)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur génération tubes', [
                'prescription_id' => $prescription->id,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Erreur lors de la génération des tubes: ' . $e->getMessage());
        }

        return $tubes;
    }



    // =====================================
    // 🧪 ÉTAPE 6: TUBES ET ÉTIQUETTES
    // =====================================
    
    public function terminerPrescription()
    {
        $this->allerEtape('confirmation');
        
        $message = 'Nouvelle prescription enregistrée';
        
        if (!empty($this->tubesGeneres)) {
            $message .= ' - ' . count($this->tubesGeneres) . ' tube(s) généré(s)';
        }
        
        // Vider la session car prescription terminée
        $this->viderSession();
        
        session()->flash('success', $message);
    }

    // =====================================
    // 📄 MÉTHODES POUR LA FACTURE
    // =====================================
    
    public function ouvrirFacture()
    {
        if (!$this->prescription) {
            flash()->error('Aucune prescription disponible');
            return;
        }
        
        // Redirection vers une nouvelle fenêtre avec la facture
        $url = route('secretaire.prescription.facture', $this->prescription->id);
        $this->dispatch('open-window', ['url' => $url]);
    }
    
    public function imprimerFacture()
    {
        if (!$this->prescription) {
            flash()->error('Aucune prescription disponible');
            return;
        }
        
        // Redirection vers la page d'impression
        $url = route('secretaire.prescription.facture', $this->prescription->id) . '?print=1';
        $this->dispatch('open-window', ['url' => $url]);
    }
    
    public function telechargerFacturePDF()
    {
        if (!$this->prescription) {
            flash()->error('Aucune prescription disponible');
            return;
        }
        
        // Pour l'instant, redirection vers la facture (PDF à implémenter plus tard)
        $this->ouvrirFacture();
    }

    // =====================================
    // 📊 COMPUTED PROPERTIES (inchangées)
    // =====================================
    
    public function getPatientsResultatsProperty()
    {
        if (strlen($this->recherchePatient) < 2) {
            return collect();
        }
        
        $terme = trim($this->recherchePatient);
        
        return Patient::where(function($query) use ($terme) {
                    $query->where('nom', 'like', "%{$terme}%")
                          ->orWhere('prenom', 'like', "%{$terme}%")
                          ->orWhere('telephone', 'like', "%{$terme}%");
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
        
        // 1. RECHERCHE DES PARENTS AVEC PRIX (ex: NFS avec prix)
        $parentsPayants = Analyse::where('status', true)
                        ->where('level', 'PARENT')
                        ->where('prix', '>', 0) // Parents PAYANTS
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

        // 2. SI DES PARENTS PAYANTS TROUVÉS → LES RETOURNER UNIQUEMENT
        if ($parentsPayants->count() > 0) {
            $this->parentRecherche = null;
            return $parentsPayants;
        }

        // 3. RECHERCHE DES PARENTS GRATUITS (prix = 0)
        $parentsGratuits = Analyse::where('status', true)
                        ->where('level', 'PARENT')
                        ->where(function($query) {
                            $query->where('prix', 0)->orWhereNull('prix');
                        })
                        ->where(function($query) use ($terme) {
                            $query->whereRaw('UPPER(code) LIKE ?', ["%{$terme}%"])
                                ->orWhereRaw('UPPER(designation) LIKE ?', ["%{$terme}%"]);
                        })
                        ->with(['enfants' => function($query) {
                            $query->where('status', true)
                                  ->where('prix', '>', 0); // Enfants PAYANTS seulement
                        }])
                        ->get();

        // 4. SI PARENT GRATUIT TROUVÉ AVEC ENFANTS PAYANTS → RETOURNER LES ENFANTS
        $enfantsPayants = collect();
        foreach ($parentsGratuits as $parentGratuit) {
            if ($parentGratuit->enfants->count() > 0) {
                // Marquer le parent de recherche pour l'affichage
                $this->parentRecherche = $parentGratuit;
                $enfantsPayants = $enfantsPayants->concat($parentGratuit->enfants);
            }
        }

        if ($enfantsPayants->count() > 0) {
            return $enfantsPayants->sortBy(function($analyse) use ($terme) {
                // Même logique de tri que pour les parents
                if (strtoupper($analyse->code) === $terme) return 1;
                if (str_starts_with(strtoupper($analyse->code), $terme)) return 2;
                if (str_contains(strtoupper($analyse->designation), $terme)) return 3;
                return 4;
            })->take(15);
        }

        // 5. RECHERCHE DES ANALYSES INDIVIDUELLES 
        // (sans parent OU avec parent gratuit SANS enfants payants)
        $individuelles = Analyse::where('status', true)
                            ->whereIn('level', ['NORMAL', 'CHILD'])
                            ->where(function($query) use ($terme) {
                                $query->whereRaw('UPPER(code) LIKE ?', ["%{$terme}%"])
                                        ->orWhereRaw('UPPER(designation) LIKE ?', ["%{$terme}%"]);
                            })
                            ->with('parent')
                            ->get()
                            ->filter(function($analyse) {
                                // Inclure si :
                                // - Pas de parent du tout
                                // - Parent gratuit ET l'analyse a un prix
                                // - Parent payant mais l'analyse n'est pas dans un "panel complet"
                                return !$analyse->parent || 
                                       ($analyse->parent && $analyse->parent->prix <= 0 && $analyse->prix > 0);
                            })
                            ->sortBy(function($analyse) use ($terme) {
                                if (strtoupper($analyse->code) === $terme) return 1;
                                if (str_starts_with(strtoupper($analyse->code), $terme)) return 2;
                                if (str_contains(strtoupper($analyse->designation), $terme)) return 3;
                                return 4;
                            })
                            ->take(15);

        $this->parentRecherche = null;
        return $individuelles;
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
                         ->orderBy('nom')
                         ->get();
    }

    public function getPrelevementsRechercheProperty()
    {
        if (strlen($this->recherchePrelevement) < 2) {
            return collect();
        }

        return Prelevement::where('is_active', true)
                         ->where(function($query) {
                             $query->where('nom', 'like', "%{$this->recherchePrelevement}%")
                                   ->orWhere('description', 'like', "%{$this->recherchePrelevement}%");
                         })
                         ->orderBy('nom')
                         ->limit(10)
                         ->get();
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
        ]);
    }
}