<?php
// app/Livewire/Technicien/AntibiogrammeTable.php

namespace App\Livewire\Technicien;

use App\Models\Antibiogramme;
use App\Models\ResultatAntibiotique;
use App\Models\Antibiotique;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AntibiogrammeTable extends Component
{
    public $prescriptionId;
    public $analyseId;
    public $bacterieId;
    public ?Antibiogramme $antibiogramme = null;
    public $antibiotiques = [];
    public $resultats = [];
    public $newAntibiotique = null;
    public $newInterpretation = 'S';
    public $newDiametre = null;

    /**
     * ✅ Règles de validation
     */
    protected $rules = [
        'newAntibiotique' => 'required|exists:antibiotiques,id',
        'newInterpretation' => 'required|in:S,I,R',
        'newDiametre' => 'nullable|numeric|min:0|max:50',
    ];

    /**
     * ✅ Messages d'erreur personnalisés
     */
    protected $messages = [
        'newAntibiotique.required' => 'Veuillez sélectionner un antibiotique.',
        'newAntibiotique.exists' => 'L\'antibiotique sélectionné n\'existe pas.',
        'newInterpretation.required' => 'Veuillez choisir une interprétation.',
        'newInterpretation.in' => 'L\'interprétation doit être S, I ou R.',
        'newDiametre.numeric' => 'Le diamètre doit être un nombre.',
        'newDiametre.min' => 'Le diamètre doit être positif.',
        'newDiametre.max' => 'Le diamètre ne peut pas dépasser 50mm.',
    ];

    public function mount($prescriptionId, $analyseId, $bacterieId)
    {
        $this->prescriptionId = $prescriptionId;
        $this->analyseId = $analyseId;
        $this->bacterieId = $bacterieId;
        
        $this->loadData();
    }

    /**
     * ✅ LOGIQUE CORRIGÉE : Ne pas créer automatiquement l'antibiogramme
     */
    public function loadData()
    {
        try {
            // ✅ CHANGEMENT PRINCIPAL : Chercher seulement, ne pas créer automatiquement
            $this->antibiogramme = Antibiogramme::where([
                'prescription_id' => $this->prescriptionId,
                'analyse_id' => $this->analyseId,
                'bacterie_id' => $this->bacterieId,
            ])->first(); // ← first() au lieu de firstOrCreate()

            // Charger antibiotiques disponibles
            $antibiotiquesUtilises = [];
            if ($this->antibiogramme) {
                $antibiotiquesUtilises = ResultatAntibiotique::where('antibiogramme_id', $this->antibiogramme->id)
                    ->pluck('antibiotique_id')
                    ->toArray();
            }

            $this->antibiotiques = Antibiotique::actives()
                ->whereNotIn('id', $antibiotiquesUtilises)
                ->orderBy('designation')
                ->get();
            
            // Charger résultats existants
            $this->loadResultats();

        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement de l\'antibiogramme', [
                'prescription_id' => $this->prescriptionId,
                'analyse_id' => $this->analyseId,
                'bacterie_id' => $this->bacterieId,
                'error' => $e->getMessage()
            ]);
            
            session()->flash('error', 'Erreur lors du chargement de l\'antibiogramme.');
        }
    }

    /**
     * ✅ LOGIQUE CORRIGÉE : Chargement des résultats
     */
    private function loadResultats()
    {
        // ✅ Si pas d'antibiogramme, pas de résultats
        if (!$this->antibiogramme) {
            $this->resultats = [];
            return;
        }

        $this->resultats = ResultatAntibiotique::where('antibiogramme_id', $this->antibiogramme->id)
            ->with('antibiotique:id,designation')
            ->orderBy('created_at')
            ->get()
            ->map(function ($resultat) {
                return [
                    'id' => $resultat->id,
                    'antibiotique' => [
                        'id' => $resultat->antibiotique->id,
                        'designation' => $resultat->antibiotique->designation,
                    ],
                    'interpretation' => $resultat->interpretation,
                    'diametre_mm' => $resultat->diametre_mm,
                    'created_at' => $resultat->created_at,
                ];
            })
            ->toArray();
    }

    /**
     * ✅ LOGIQUE CORRIGÉE : Créer l'antibiogramme SEULEMENT lors du premier ajout
     */
    public function addAntibiotique()
    {
        // ✅ Validation avec gestion d'erreurs
        $this->validate();

        DB::beginTransaction();
        
        try {
            // ✅ CRÉER L'ANTIBIOGRAMME SEULEMENT ICI, au premier ajout d'antibiotique
            if (!$this->antibiogramme) {
                $this->antibiogramme = Antibiogramme::create([
                    'prescription_id' => $this->prescriptionId,
                    'analyse_id' => $this->analyseId,
                    'bacterie_id' => $this->bacterieId,
                ]);
                
                Log::info('Nouvel antibiogramme créé', [
                    'antibiogramme_id' => $this->antibiogramme->id,
                    'prescription_id' => $this->prescriptionId,
                    'analyse_id' => $this->analyseId,
                    'bacterie_id' => $this->bacterieId,
                ]);
            }

            // Vérifier si déjà existant (double sécurité)
            $existing = ResultatAntibiotique::where([
                'antibiogramme_id' => $this->antibiogramme->id,
                'antibiotique_id' => $this->newAntibiotique,
            ])->first();

            if ($existing) {
                session()->flash('error', 'Cet antibiotique est déjà dans l\'antibiogramme.');
                return;
            }

            // Créer le résultat d'antibiotique
            $resultatAntibiotique = ResultatAntibiotique::create([
                'antibiogramme_id' => $this->antibiogramme->id,
                'antibiotique_id' => $this->newAntibiotique,
                'interpretation' => $this->newInterpretation,
                'diametre_mm' => $this->newDiametre ?: null,
            ]);

            DB::commit();

            Log::info('Antibiotique ajouté à l\'antibiogramme', [
                'antibiogramme_id' => $this->antibiogramme->id,
                'antibiotique_id' => $this->newAntibiotique,
                'resultat_id' => $resultatAntibiotique->id,
            ]);

            // ✅ Réinitialiser les champs du formulaire
            $this->reset(['newAntibiotique', 'newInterpretation', 'newDiametre']);
            $this->newInterpretation = 'S'; // Remettre valeur par défaut
            
            // ✅ Recharger SEULEMENT les données nécessaires
            $this->loadResultats();
            
            // ✅ Mettre à jour la liste des antibiotiques disponibles
            $antibiotiquesUtilises = ResultatAntibiotique::where('antibiogramme_id', $this->antibiogramme->id)
                ->pluck('antibiotique_id')
                ->toArray();

            $this->antibiotiques = Antibiotique::actives()
                ->whereNotIn('id', $antibiotiquesUtilises)
                ->orderBy('designation')
                ->get();
            
            // ✅ Message de succès sans refresh
            session()->flash('message', 'Antibiotique ajouté avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erreur lors de l\'ajout d\'antibiotique', [
                'antibiogramme_id' => $this->antibiogramme?->id,
                'antibiotique_id' => $this->newAntibiotique,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            session()->flash('error', 'Erreur lors de l\'ajout de l\'antibiotique : ' . $e->getMessage());
        }
    }

    /**
     * ✅ MÉTHODE CORRIGÉE : Mise à jour sans rafraîchissement
     */
    public function updateResultat($resultatId, $field, $value)
    {
        try {
            $resultat = ResultatAntibiotique::find($resultatId);
            
            if (!$resultat) {
                session()->flash('error', 'Résultat d\'antibiotique introuvable.');
                return;
            }

            // ✅ Validation dynamique selon le champ
            if ($field === 'interpretation') {
                $this->validate([
                    'interpretation' => 'required|in:S,I,R',
                ], [
                    'interpretation.required' => 'L\'interprétation est requise.',
                    'interpretation.in' => 'L\'interprétation doit être S, I ou R.',
                ], ['interpretation' => $value]);
                
            } elseif ($field === 'diametre_mm') {
                $this->validate([
                    'diametre_mm' => 'nullable|numeric|min:0|max:50',
                ], [
                    'diametre_mm.numeric' => 'Le diamètre doit être un nombre.',
                    'diametre_mm.min' => 'Le diamètre doit être positif.',
                    'diametre_mm.max' => 'Le diamètre ne peut pas dépasser 50mm.',
                ], ['diametre_mm' => $value]);
            }
            
            // ✅ Mise à jour avec valeur correctement formatée
            $updateValue = ($field === 'diametre_mm') ? ($value ?: null) : $value;
            $resultat->update([$field => $updateValue]);
            
            Log::info('Résultat d\'antibiotique mis à jour', [
                'resultat_id' => $resultatId,
                'field' => $field,
                'new_value' => $updateValue,
            ]);
            
            // ✅ Recharger seulement les données locales
            $this->loadResultats();
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // ✅ Gestion spéciale des erreurs de validation
            session()->flash('error', 'Erreur de validation : ' . implode(', ', $e->validator->errors()->all()));
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du résultat', [
                'resultat_id' => $resultatId,
                'field' => $field,
                'value' => $value,
                'error' => $e->getMessage()
            ]);
            
            session()->flash('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    /**
     * ✅ MÉTHODE CORRIGÉE : Suppression avec nettoyage automatique
     */
    public function removeResultat($resultatId)
    {
        DB::beginTransaction();
        
        try {
            $resultat = ResultatAntibiotique::find($resultatId);
            
            if (!$resultat) {
                session()->flash('error', 'Résultat d\'antibiotique introuvable.');
                return;
            }

            $antibiotiqueName = $resultat->antibiotique->designation ?? 'Inconnu';
            $resultat->delete();
            
            // ✅ NETTOYAGE AUTOMATIQUE : Si plus de résultats, supprimer l'antibiogramme
            $remainingResults = ResultatAntibiotique::where('antibiogramme_id', $this->antibiogramme->id)->count();
            
            if ($remainingResults === 0) {
                Log::info('Suppression de l\'antibiogramme vide', [
                    'antibiogramme_id' => $this->antibiogramme->id
                ]);
                
                $this->antibiogramme->delete();
                $this->antibiogramme = null;
            }
            
            DB::commit();
            
            Log::info('Résultat d\'antibiotique supprimé', [
                'resultat_id' => $resultatId,
                'antibiotique' => $antibiotiqueName,
                'antibiogramme_supprime' => $remainingResults === 0,
            ]);
            
            // ✅ Recharger les données locales
            $this->loadResultats();
            
            // ✅ Remettre l'antibiotique dans la liste disponible
            $antibiotiquesUtilises = [];
            if ($this->antibiogramme) {
                $antibiotiquesUtilises = ResultatAntibiotique::where('antibiogramme_id', $this->antibiogramme->id)
                    ->pluck('antibiotique_id')
                    ->toArray();
            }

            $this->antibiotiques = Antibiotique::actives()
                ->whereNotIn('id', $antibiotiquesUtilises)
                ->orderBy('designation')
                ->get();
            
            session()->flash('message', "Antibiotique \"{$antibiotiqueName}\" retiré avec succès.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erreur lors de la suppression du résultat', [
                'resultat_id' => $resultatId,
                'error' => $e->getMessage()
            ]);
            
            session()->flash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    /**
     * ✅ MÉTHODE D'INFORMATION : Vérifier si l'antibiogramme existe
     */
    public function hasAntibiogramme()
    {
        return !is_null($this->antibiogramme);
    }

    /**
     * ✅ MÉTHODE D'INFORMATION : Compter les antibiotiques
     */
    public function getAntibiotiquesCount()
    {
        return count($this->resultats);
    }

    /**
     * ✅ MÉTHODE D'INFORMATION : Statistiques pour debug
     */
    public function getStatistiques()
    {
        return [
            'antibiogramme_existe' => $this->hasAntibiogramme(),
            'total_antibiotiques' => count($this->resultats),
            'sensibles' => collect($this->resultats)->where('interpretation', 'S')->count(),
            'intermediaires' => collect($this->resultats)->where('interpretation', 'I')->count(),
            'resistants' => collect($this->resultats)->where('interpretation', 'R')->count(),
            'avec_diametre' => collect($this->resultats)->whereNotNull('diametre_mm')->count(),
        ];
    }

    public function render()
    {
        return view('livewire.technicien.antibiogramme-table');
    }
}