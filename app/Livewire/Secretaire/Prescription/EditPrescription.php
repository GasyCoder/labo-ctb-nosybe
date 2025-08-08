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
    public bool $activer_remise = false; // ✅ AJOUT DE LA PROPRIÉTÉ PUBLIQUE

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

    public array $tubesGeneres = [];
    public ?Prescription $prescription = null;
    public int $prescriptionId;

    public function mount($prescriptionId)
    {
        $this->prescriptionId = $prescriptionId;
        $this->loadPrescription();
        $this->validateEtape();
        
        // ✅ CHARGER LES SETTINGS DE REMISE
        $this->chargerSettingsRemise();
        
        $this->calculerTotaux();
        $this->isEditMode = true;
    }

    // ✅ NOUVELLE MÉTHODE POUR CHARGER LES SETTINGS
    private function chargerSettingsRemise()
    {
        $setting = Setting::first();
        $this->activer_remise = $setting?->activer_remise ?? false;
    }

    // ✅ MÉTHODE POUR RÉCUPÉRER LES MÉTHODES DE PAIEMENT ACTIVES
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
                'nom' => $prelevement->nom,
                'description' => $prelevement->description ?? '',
                'prix' => $prelevement->pivot->prix_unitaire ?? $prelevement->prix ?? 0,
                'quantite' => $prelevement->pivot->quantite ?? 1,
                'type_tube_requis' => $prelevement->pivot->type_tube_requis ?? 'SEC',
                'volume_requis_ml' => $prelevement->pivot->volume_requis_ml ?? 5.0,
            ];
        }

        // ✅ PAIEMENT - RÉCUPÉRATION CORRECTE DU MODE DE PAIEMENT
        $lastPaiement = $this->prescription->paiements()->latest()->first();
        if ($lastPaiement && $lastPaiement->paymentMethod) {
            $this->modePaiement = $lastPaiement->paymentMethod->code;
        } else {
            // Fallback vers la première méthode active
            $premiereMethode = PaymentMethod::where('is_active', true)
                                           ->orderBy('display_order')
                                           ->first();
            $this->modePaiement = $premiereMethode?->code ?? 'ESPECES';
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

    // ==========================================
    // 🌐 GESTION URL ET NAVIGATION ET ÉTAPES
    // ==========================================

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

    // ==========================================
    // 👤 PATIENT / CLINIQUE / ANALYSES
    // ==========================================

    public function selectionnerPatient(int $patientId)
    {
        $this->patient = Patient::find($patientId);
        $this->nouveauPatient = false;
        
        // Pré-remplir avec données du patient pour modification
        $this->nom = $this->patient->nom;
        $this->prenom = $this->patient->prenom;
        $this->civilite = $this->patient->civilite;
        $this->telephone = $this->patient->telephone ?? '';
        $this->email = $this->patient->email ?? '';

        flash()->success("Patient « {$this->patient->nom} {$this->patient->prenom} » sélectionné - Vous pouvez modifier ses informations");
        
        // Rester sur l'étape patient pour permettre la modification
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
            'civilite' => 'required|in:Madame,Monsieur,Mademoiselle,Enfant', 
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
                // Création d'un nouveau patient (normalement ne devrait pas arriver en mode édition)
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

            // LOGIQUE DIFFÉRENTE SELON LE NIVEAU
            if ($analyse->level === 'PARENT') {
                // CAS 1: ANALYSE PARENT (Panel complet)
                $this->ajouterAnalyseParent($analyse);
            } else {
                // CAS 2: ANALYSE INDIVIDUELLE (Normal/Child)
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

        // Ajouter le parent comme une analyse complète
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

        // Calculer le prix selon la logique parent/enfant
        $prixEffectif = $analyse->prix;
        $parentNom = 'Analyse individuelle';

        if ($analyse->parent && $analyse->parent->prix > 0) {
            // Si le parent a un prix, cette analyse peut être gratuite ou payante selon la logique métier
            $parentNom = $analyse->parent->designation . ' (partie)';
            // Garder le prix de l'analyse individuelle
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

        // Validation des conflits parent/enfant
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

        // Vérifier les conflits
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
        // Prélèvements optionnels - pas de validation obligatoire
        if (empty($this->prelevementsSelectionnes)) {
            flash()->info('Aucun prélèvement sélectionné - Passage direct au paiement');
        } else {
            flash()->success(count($this->prelevementsSelectionnes) . ' prélèvement(s) ajouté(s)');
        }

        $this->allerEtape('paiement');
    }

    // =====================================
    // 💰 ÉTAPE 5: PAIEMENT
    // ====================================

    private function calculerTotaux()
    {
        try {
            $sousTotal = 0;
            $parentsTraites = [];

            foreach ($this->analysesPanier as $analyse) {
                if (isset($analyse['is_parent']) && $analyse['is_parent']) {
                    // CAS 1: Analyse PARENT (panel complet)
                    $sousTotal += $analyse['prix_effectif'];
                } else {
                    // CAS 2: Analyse individuelle
                    if ($analyse['parent_id'] && !in_array($analyse['parent_id'], $parentsTraites)) {
                        // Vérifier si le parent a un prix et n'est pas déjà compté
                        $parent = Analyse::find($analyse['parent_id']);
                        if ($parent && $parent->prix > 0) {
                            // ICI: Option C - Si on prend les enfants individuellement, on ne compte pas le parent
                            $sousTotal += $analyse['prix_effectif'];
                        } else {
                            $sousTotal += $analyse['prix_effectif'];
                        }
                    } else {
                        $sousTotal += $analyse['prix_effectif'];
                    }
                }
            }

            // Total des prélèvements
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
        // ✅ Récupérer les codes des méthodes actives pour validation dynamique
        $codesMethodesActives = PaymentMethod::where('is_active', true)
                                            ->pluck('code')
                                            ->toArray();
        
        $codesValidation = !empty($codesMethodesActives) 
            ? 'in:' . implode(',', $codesMethodesActives)
            : 'in:ESPECES,CARTE,CHEQUE,MOBILEMONEY'; // Fallback
        
        // Validation des données de paiement
        $this->validate([
            'modePaiement' => "required|{$codesValidation}",
            'montantPaye' => 'required|numeric|min:0',
            'remise' => 'nullable|numeric|min:0',
        ], [
            'modePaiement.required' => 'Veuillez sélectionner un mode de paiement',
            'modePaiement.in' => 'Mode de paiement non valide ou inactif',
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
    // 🧪 ÉTAPE 6: TUBES ET ÉTIQUETTES
    // =====================================

    public function terminerPrescription()
    {
        $this->allerEtape('confirmation');
        
        $message = 'Prescription mise à jour avec succès';
        
        if (!empty($this->tubesGeneres)) {
            $message .= ' - ' . count($this->tubesGeneres) . ' nouveau(x) tube(s) généré(s)';
        }
        
        session()->flash('success', $message);
    }

    // =====================================
    // 🔧 MÉTHODES UTILITAIRES
    // =====================================

    private function genererTubesPourPrescription($prescription)
    {
        $tubes = [];
        
        try {
            foreach ($prescription->prelevements as $prelevement) {
                $quantite = $prelevement->pivot->quantite ?? 1;
                
                for ($i = 0; $i < $quantite; $i++) {
                    // Créer le tube avec un nouveau pattern plus sûr
                    $tube = new Tube([
                        'prescription_id' => $prescription->id,
                        'patient_id' => $prescription->patient_id,
                        'prelevement_id' => $prelevement->id,
                        'type_tube' => $prelevement->pivot->type_tube_requis ?? 'SEC',
                        'volume_ml' => $prelevement->pivot->volume_requis_ml ?? 5.0,
                        'statut' => 'GENERE',
                        'genere_at' => now(),
                    ]);
                    
                    // Sauvegarder d'abord pour obtenir l'ID
                    $tube->save();

                    // Maintenant générer le code-barre avec l'ID
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
                    ];
                }
            }

            // Marquer la prescription comme ayant des tubes générés
            $prescription->update(['status' => 'EN_ATTENTE']);
            
            flash()->success(count($tubes) . ' tube(s) généré(s) avec succès');
            
        } catch (\Exception $e) {
            Log::error('Erreur génération tubes', ['error' => $e->getMessage()]);
            throw new \Exception('Erreur lors de la génération des tubes: ' . $e->getMessage());
        }

        return $tubes;
    }

    // =====================================
    // 💾 ENREGISTREMENT MODIFICATION
    // =====================================
    public function enregistrerPrescription()
    {
        try {
            DB::beginTransaction();

            // Vérifications préalables
            if (!$this->patient) {
                throw new \Exception('Patient non défini');
            }

            if (!Prescripteur::find($this->prescripteurId)) {
                throw new \Exception('Prescripteur invalide');
            }

            if (empty($this->analysesPanier)) {
                throw new \Exception('Aucune analyse sélectionnée');
            }

            // 1. Mettre à jour la prescription (sans toucher à la référence)
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

            // 2. Associer les analyses (vérification des IDs)
            $analyseIds = array_keys($this->analysesPanier);
            $analysesExistantes = Analyse::whereIn('id', $analyseIds)->pluck('id')->toArray();
            
            if (count($analysesExistantes) !== count($analyseIds)) {
                throw new \Exception('Certaines analyses sélectionnées n\'existent plus');
            }
            
            $this->prescription->analyses()->sync($analysesExistantes);

            // 3. Associer les prélèvements avec vérification (si présents)
            $this->prescription->prelevements()->detach();
            
            if (!empty($this->prelevementsSelectionnes)) {
                foreach ($this->prelevementsSelectionnes as $prelevement) {
                    if (!Prelevement::find($prelevement['id'])) {
                        throw new \Exception('Prélèvement ID ' . $prelevement['id'] . ' invalide');
                    }

                    $this->prescription->prelevements()->attach($prelevement['id'], [
                        'prix_unitaire' => $prelevement['prix'] ?? 0,
                        'quantite' => max(1, $prelevement['quantite'] ?? 1),
                        'type_tube_requis' => $prelevement['type_tube_requis'] ?? 'SEC',
                        'volume_requis_ml' => $prelevement['volume_requis_ml'] ?? 5.0,
                        'is_payer' => 'PAYE'
                    ]);
                }
            }

            // ✅ 4. Enregistrer le paiement avec payment_method_id
            // Récupérer l'ID de la méthode de paiement sélectionnée
            $paymentMethod = PaymentMethod::where('code', $this->modePaiement)->first();
            
            if (!$paymentMethod) {
                throw new \Exception('Méthode de paiement invalide: ' . $this->modePaiement);
            }
            
            // Supprimer les anciens paiements et créer le nouveau
            $this->prescription->paiements()->delete();
            
            Paiement::create([
                'prescription_id' => $this->prescription->id,
                'montant' => $this->total,
                'payment_method_id' => $paymentMethod->id, // ✅ Utilise payment_method_id
                'recu_par' => Auth::id()
            ]);

            // 5. Régénérer les tubes (seulement si prélèvements présents)
            $this->prescription->tubes()->delete();
            $this->tubesGeneres = [];
            
            if (!empty($this->prelevementsSelectionnes)) {
                $this->tubesGeneres = $this->genererTubesPourPrescription($this->prescription);
                $this->allerEtape('tubes');
            } else {
                $this->allerEtape('confirmation');
            }

            DB::commit();

            flash()->success('Prescription modifiée avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur modification prescription', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'patient_id' => $this->patient?->id,
                'prescripteur_id' => $this->prescripteurId,
                'prescription_id' => $this->prescriptionId
            ]);
            
            flash()->error('Erreur lors de la modification: ' . $e->getMessage());
        }
    }

    // =====================================
    // 📊 COMPUTED PROPERTIES
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
        $results = collect();

        // 1. RECHERCHE DIRECTE DES PARENTS (panels complets)
        $parents = Analyse::where('status', true)
                        ->where('level', 'PARENT')
                        ->where('prix', '>', 0) // Seulement les parents avec prix
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

        // 2. RECHERCHE DES ANALYSES INDIVIDUELLES (normal/child)
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

        // 3. COMBINER LES RÉSULTATS (Parents en premier)
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

    public function nouveauPrescription()
    {
        $this->reset([
            'patient', 'nouveauPatient', 'nom', 'prenom', 'civilite', 'telephone', 'email',
            'prescripteurId', 'age', 'poids', 'renseignementClinique',
            'analysesPanier', 'prelevementsSelectionnes', 'tubesGeneres',
            'montantPaye', 'remise', 'total', 'monnaieRendue', 'recherchePatient', 
            'rechercheAnalyse', 'recherchePrelevement'
        ]);
        
        // Réinitialiser l'étape et l'URL
        $this->etape = 'patient';
        $this->age = 0;
        $this->uniteAge = 'Ans';
        $this->patientType = 'EXTERNE';
        $this->civilite = 'Monsieur';
        
        // ✅ RECHARGER LES SETTINGS
        $this->chargerSettingsRemise();
        
        // Initialiser avec la première méthode active
        $premiereMethode = PaymentMethod::where('is_active', true)
                                       ->orderBy('display_order')
                                       ->first();
        $this->modePaiement = $premiereMethode?->code ?? 'ESPECES';
        
        $this->calculerTotaux();

        flash()->info('Nouvelle prescription initialisée');
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