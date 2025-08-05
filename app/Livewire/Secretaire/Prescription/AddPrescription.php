<?php

namespace App\Livewire\Secretaire\Prescription;

use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Analyse;
use App\Models\Prescripteur;
use App\Models\Prelevement;
use App\Models\Paiement;
use App\Models\Tube;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AddPrescription extends Component
{
    use WithPagination;

    // 🎯 ÉTAPES WORKFLOW LABORATOIRE
    public string $etape = 'patient';
    
    // 👤 DONNÉES PATIENT
    public ?Patient $patient = null;
    public bool $nouveauPatient = false;
    public string $recherchePatient = '';

    // Données nouveau patient
    public string $nom = '';
    public string $prenom = '';
    public string $sexe = '';
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
    
    // 🧾 PRÉLÈVEMENTS SÉLECTIONNÉS
    public array $prelevementsSelectionnes = [];
    public string $recherchePrelevement = '';
    
    // 💰 PAIEMENT
    public string $modePaiement = 'ESPECES';
    public float $montantPaye = 0;
    public float $remise = 0;
    public float $total = 0;
    public float $monnaieRendue = 0;
    
    // 🧪 TUBES
    public array $tubesGeneres = [];

    public function mount()
    {
        $this->calculerTotaux();
    }

    // =====================================
    // 👤 ÉTAPE 1: GESTION PATIENT
    // =====================================
    
    public function selectionnerPatient(int $patientId)
    {
        $this->patient = Patient::find($patientId);
        $this->nouveauPatient = false;
        $this->etape = 'clinique';
        
        // Pré-remplir avec dernières données connues
        $dernierePrescription = Prescription::where('patient_id', $patientId)->latest()->first();
        if ($dernierePrescription) {
            $this->age = $dernierePrescription->age ?? 0;
            $this->uniteAge = $dernierePrescription->unite_age ?? 'Ans';
            $this->poids = $dernierePrescription->poids;
            $this->prescripteurId = $dernierePrescription->prescripteur_id;
        }
    }
    
    public function creerNouveauPatient()
    {
        $this->nouveauPatient = true;
        $this->patient = null;
        $this->etape = 'patient';
    }
    
    public function validerNouveauPatient()
    {
        $this->validate([
            'nom' => 'required|min:2|max:50|regex:/^[a-zA-ZÀ-ÿ\s\-\']+$/',
            'prenom' => 'nullable|max:50|regex:/^[a-zA-ZÀ-ÿ\s\-\']*$/',
            'sexe' => 'required',
            'telephone' => 'nullable|regex:/^[0-9+\-\s()]{8,15}$/',
            'email' => 'nullable|email|max:255'
        ], [
            'nom.required' => 'Le nom est obligatoire',
            'nom.regex' => 'Le nom ne doit contenir que des lettres',
            'telephone.regex' => 'Format de téléphone invalide',
            'email.email' => 'Format email invalide'
        ]);
        
        // Vérifier si patient existe déjà (normalisation des noms)
        $nomNormalise = strtoupper(trim($this->nom));
        $prenomNormalise = strtoupper(trim($this->prenom));
        
        $patientExistant = Patient::whereRaw('UPPER(TRIM(nom)) = ?', [$nomNormalise])
                                 ->whereRaw('UPPER(TRIM(prenom)) = ?', [$prenomNormalise])
                                 ->first();
        
        if ($patientExistant) {
            session()->flash('warning', 'Patient similaire trouvé. Utilisez-vous celui-ci ?');
            $this->patient = $patientExistant;
            $this->nouveauPatient = false;
            $this->etape = 'clinique';
            return;
        }
        
        // Créer nouveau patient
        try {
            $this->patient = Patient::create([
                'reference' => $this->genererReferencePatient(),
                'nom' => ucwords(strtolower(trim($this->nom))),
                'prenom' => ucwords(strtolower(trim($this->prenom))),
                'sexe' => $this->sexe,
                'telephone' => trim($this->telephone),
                'email' => strtolower(trim($this->email)),
            ]);
            
            $this->nouveauPatient = false;
            $this->etape = 'clinique';
            session()->flash('success', 'Nouveau patient créé avec succès');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la création du patient: ' . $e->getMessage());
        }
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
        
        $this->etape = 'analyses';
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
            session()->flash('warning', 'Analyse déjà ajoutée');
            return;
        }

        try {
            $analyse = Analyse::with('parent')->find($analyseId);
            
            if (!$analyse) {
                session()->flash('error', 'Analyse introuvable');
                return;
            }

            if (!in_array($analyse->level, ['NORMAL', 'CHILD'])) {
                session()->flash('error', 'Seules les analyses finales peuvent être ajoutées');
                return;
            }

            // Logique du prix simplifiée et corrigée
            $prixEffectif = $analyse->prix;
            $parentNom = 'Divers';
            $prixAffiche = $analyse->prix;

            if ($analyse->parent) {
                $parentNom = $analyse->parent->designation;
                
                // Si le parent a un prix, l'enfant est inclus
                if ($analyse->parent->prix > 0) {
                    $prixEffectif = 0; // L'analyse enfant ne coûte rien
                    $prixAffiche = 0;
                    $parentNom .= ' (inclus)';
                }
            }

            $this->analysesPanier[$analyseId] = [
                'id' => $analyse->id,
                'designation' => $analyse->designation,
                'prix_original' => $analyse->prix,
                'prix_effectif' => $prixEffectif,
                'prix_affiche' => $prixAffiche,
                'prix' => $prixAffiche, // ← AJOUTER CETTE LIGNE !
                'parent_nom' => $parentNom,
                'code' => $analyse->code,
                'parent_id' => $analyse->parent_id,
            ];

            $this->calculerTotaux();
            session()->flash('success', "« {$analyse->designation} » ajoutée");
            
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'ajout de l\'analyse');
            Log::error('Erreur ajout analyse', ['error' => $e->getMessage(), 'analyse_id' => $analyseId]);
        }
    }
    
    public function retirerAnalyse(int $analyseId)
    {
        if (isset($this->analysesPanier[$analyseId])) {
            $nom = $this->analysesPanier[$analyseId]['designation'];
            unset($this->analysesPanier[$analyseId]);
            $this->calculerTotaux();
            session()->flash('info', "« {$nom} » retirée");
        }
    }
    
    public function validerAnalyses()
    {
        if (empty($this->analysesPanier)) {
            session()->flash('error', 'Veuillez sélectionner au moins une analyse');
            return;
        }
        
        $this->etape = 'prelevements';
    }

    // =====================================
    // 🧾 ÉTAPE 4: SÉLECTION PRÉLÈVEMENTS 
    // =====================================
    
    public function ajouterPrelevement(int $prelevementId)
    {
        if (isset($this->prelevementsSelectionnes[$prelevementId])) {
            session()->flash('warning', 'Prélèvement déjà ajouté');
            return;
        }

        try {
            $prelevement = Prelevement::find($prelevementId);
            
            if (!$prelevement) {
                session()->flash('error', 'Prélèvement introuvable');
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
            session()->flash('success', "« {$prelevement->nom} » ajouté");
            
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'ajout du prélèvement');
            Log::error('Erreur ajout prélèvement', ['error' => $e->getMessage(), 'prelevement_id' => $prelevementId]);
        }
    }
    
    public function retirerPrelevement(int $prelevementId)
    {
        if (isset($this->prelevementsSelectionnes[$prelevementId])) {
            $nom = $this->prelevementsSelectionnes[$prelevementId]['nom'];
            unset($this->prelevementsSelectionnes[$prelevementId]);
            $this->calculerTotaux();
            session()->flash('info', "« {$nom} » retiré");
        }
    }
    
    public function modifierQuantitePrelevement(int $prelevementId, int $quantite)
    {
        if (isset($this->prelevementsSelectionnes[$prelevementId]) && $quantite > 0 && $quantite <= 10) {
            $this->prelevementsSelectionnes[$prelevementId]['quantite'] = $quantite;
            $this->calculerTotaux();
        }
    }
    
    public function validerPrelevements()
    {
        if (empty($this->prelevementsSelectionnes)) {
            session()->flash('error', 'Veuillez sélectionner au moins un prélèvement');
            return;
        }
        
        $this->etape = 'paiement';
    }

    // =====================================
    // 💰 ÉTAPE 5: PAIEMENT
    // =====================================
    
    private function calculerTotaux()
    {
        try {
            // Calculer le total des analyses avec logique parent/enfant optimisée
            $sousTotal = 0;
            $parentsTraites = [];

            foreach ($this->analysesPanier as $analyse) {
                if ($analyse['parent_id'] && !in_array($analyse['parent_id'], $parentsTraites)) {
                    // Charger le parent une seule fois si pas encore traité
                    $parent = Analyse::find($analyse['parent_id']);
                    if ($parent && $parent->prix > 0) {
                        $sousTotal += $parent->prix;
                        $parentsTraites[] = $analyse['parent_id'];
                        continue;
                    }
                }
                
                // Si pas de parent ou parent sans prix, utiliser le prix effectif
                if (!$analyse['parent_id'] || !in_array($analyse['parent_id'], $parentsTraites)) {
                    $sousTotal += $analyse['prix_effectif'];
                }
            }

            // Total des prélèvements
            $totalPrelevements = 0;
            foreach ($this->prelevementsSelectionnes as $prelevement) {
                $totalPrelevements += ($prelevement['prix'] ?? 0) * ($prelevement['quantite'] ?? 1);
            }

            $this->total = max(0, $sousTotal + $totalPrelevements - $this->remise);
            
            // Assurer que le montant payé est au moins égal au total
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
        // Validation des données de paiement
        $this->validate([
            'modePaiement' => 'required|in:ESPECES,CARTE,CHEQUE',
            'montantPaye' => 'required|numeric|min:0',
            'remise' => 'nullable|numeric|min:0',
        ], [
            'modePaiement.required' => 'Veuillez sélectionner un mode de paiement',
            'montantPaye.required' => 'Le montant payé est obligatoire',
            'montantPaye.min' => 'Le montant payé doit être positif',
        ]);

        if ($this->montantPaye < $this->total) {
            session()->flash('error', 'Montant payé insuffisant. Total: ' . number_format($this->total, 0) . ' Ar');
            return;
        }

        if (empty($this->analysesPanier)) {
            session()->flash('error', 'Aucune analyse sélectionnée');
            return;
        }

        if (empty($this->prelevementsSelectionnes)) {
            session()->flash('error', 'Aucun prélèvement sélectionné');
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
            
            // Vérifications préalables
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
                'secretaire_id' => Auth::id(),
                'patient_type' => $this->patientType,
                'age' => $this->age,
                'unite_age' => $this->uniteAge,
                'poids' => $this->poids,
                'renseignement_clinique' => $this->renseignementClinique,
                'remise' => $this->remise,
                'status' => 'EN_ATTENTE'
            ]);
            
            // 2. Associer les analyses (vérification des IDs)
            $analyseIds = array_keys($this->analysesPanier);
            $analysesExistantes = Analyse::whereIn('id', $analyseIds)->pluck('id')->toArray();
            
            if (count($analysesExistantes) !== count($analyseIds)) {
                throw new \Exception('Certaines analyses sélectionnées n\'existent plus');
            }
            
            $prescription->analyses()->sync($analysesExistantes);
            
            // 3. Associer les prélèvements avec vérification
            foreach ($this->prelevementsSelectionnes as $prelevement) {
                if (!Prelevement::find($prelevement['id'])) {
                    throw new \Exception('Prélèvement ID ' . $prelevement['id'] . ' invalide');
                }

                $prescription->prelevements()->attach($prelevement['id'], [
                    'prix_unitaire' => $prelevement['prix'] ?? 0,
                    'quantite' => max(1, $prelevement['quantite'] ?? 1),
                    'type_tube_requis' => $prelevement['type_tube_requis'] ?? 'SEC',
                    'volume_requis_ml' => $prelevement['volume_requis_ml'] ?? 5.0,
                    'is_payer' => 'PAYE'
                ]);
            }
            
            // 4. Enregistrer le paiement
            Paiement::create([
                'prescription_id' => $prescription->id,
                'montant' => $this->total,
                'mode_paiement' => $this->modePaiement,
                'recu_par' => Auth::id()
            ]);
            
            // 5. Générer les tubes (version sécurisée)
            $this->tubesGeneres = $this->genererTubesPourPrescription($prescription);
            
            DB::commit();
            
            $this->etape = 'tubes';
            session()->flash('success', 'Prescription enregistrée avec succès');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur enregistrement prescription', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'patient_id' => $this->patient?->id,
                'prescripteur_id' => $this->prescripteurId
            ]);
            session()->flash('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage());
        }
    }

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
            
        } catch (\Exception $e) {
            Log::error('Erreur génération tubes', ['error' => $e->getMessage()]);
            throw new \Exception('Erreur lors de la génération des tubes: ' . $e->getMessage());
        }

        return $tubes;
    }

    // =====================================
    // 🧪 ÉTAPE 6: TUBES ET ÉTIQUETTES
    // =====================================
    
    public function imprimerEtiquettes()
    {
        session()->flash('success', 'Étiquettes envoyées à l\'impression');
        $this->etape = 'confirmation';
    }
    
    public function ignorerEtiquettes()
    {
        $this->etape = 'confirmation';
    }

    // =====================================
    // 🔄 NAVIGATION ET RESET
    // =====================================
    
    public function allerEtape(string $etape)
    {
        $this->etape = $etape;
    }
    
    public function nouveauPrescription()
    {
        $this->reset([
            'patient', 'nouveauPatient', 'nom', 'prenom', 'sexe', 'telephone', 'email',
            'prescripteurId', 'age', 'poids', 'renseignementClinique',
            'analysesPanier', 'prelevementsSelectionnes', 'tubesGeneres',
            'montantPaye', 'remise', 'total', 'monnaieRendue', 'recherchePatient', 
            'rechercheAnalyse', 'recherchePrelevement'
        ]);
        $this->etape = 'patient';
        $this->age = 0;
        $this->uniteAge = 'Ans';
        $this->patientType = 'EXTERNE';
        $this->modePaiement = 'ESPECES';
        $this->calculerTotaux();
    }

    // =====================================
    // 🔧 MÉTHODES UTILITAIRES
    // =====================================
    
    private function genererReferencePatient(): string
    {
        $annee = date('Y');
        $numero = str_pad(Patient::count() + 1, 5, '0', STR_PAD_LEFT);
        return "PAT{$annee}{$numero}";
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
                          ->orWhere('reference', 'like', "%{$terme}%")
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

        // Recherche optimisée
        $results = Analyse::where('status', true)
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
                         ->orderBy('designation')
                         ->limit(20)
                         ->get();

        // Vérifier si c'est un parent
        if ($results->isEmpty()) {
            $parent = Analyse::where('status', true)
                            ->where('level', 'PARENT')
                            ->where(function($query) use ($terme) {
                                $query->whereRaw('UPPER(code) LIKE ?', ["%{$terme}%"])
                                      ->orWhereRaw('UPPER(designation) LIKE ?', ["%{$terme}%"]);
                            })
                            ->first();

            if ($parent) {
                $this->parentRecherche = $parent;
                return Analyse::where('status', true)
                             ->whereIn('level', ['NORMAL', 'CHILD'])
                             ->where('parent_id', $parent->id)
                             ->with('parent')
                             ->orderBy('designation')
                             ->limit(20)
                             ->get();
            }
        }

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

    public function render()
    {
        return view('livewire.secretaire.prescription.add-prescription', [
            'patientsResultats' => $this->patientsResultats,
            'categoriesAnalyses' => $this->categoriesAnalyses,
            'analysesRecherche' => $this->analysesRecherche,
            'prescripteurs' => $this->prescripteurs,
            'prelevementsDisponibles' => $this->prelevementsDisponibles,
            'prelevementsRecherche' => $this->prelevementsRecherche,
        ]);
    }
}