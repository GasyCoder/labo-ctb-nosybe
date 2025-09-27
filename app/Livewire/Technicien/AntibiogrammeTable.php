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

    // ✅ NOUVELLES PROPRIÉTÉS (affichage)
    public $compact = false;    // Pour affichage compact dans accordion
    public $hideHeader = false; // Pour masquer l'en-tête

    /* =======================
     |  Helpers Flash
     |=======================*/
    private function flashSuccess(string $message): void
    {
        if (\function_exists('flash')) {
            flash()->success($message);
        } else {
            session()->flash('message', $message);
        }
    }

    private function flashError(string $message): void
    {
        if (\function_exists('flash')) {
            flash()->error($message);
        } else {
            session()->flash('error', $message);
        }
    }

    private function flashInfo(string $message): void
    {
        if (\function_exists('flash')) {
            flash()->info($message);
        } else {
            session()->flash('info', $message);
        }
    }

    /**
     * ✅ Règles de validation
     */
    protected $rules = [
        'newAntibiotique'    => 'required|exists:antibiotiques,id',
        'newInterpretation'  => 'required|in:S,I,R',
        'newDiametre'        => 'nullable|numeric|min:0|max:50',
    ];

    /**
     * ✅ Messages d'erreur personnalisés
     */
    protected $messages = [
        'newAntibiotique.required'   => 'Veuillez sélectionner un antibiotique.',
        'newAntibiotique.exists'     => 'L\'antibiotique sélectionné n\'existe pas.',
        'newInterpretation.required' => 'Veuillez choisir une interprétation.',
        'newInterpretation.in'       => 'L\'interprétation doit être S, I ou R.',
        'newDiametre.numeric'        => 'Le diamètre doit être un nombre.',
        'newDiametre.min'            => 'Le diamètre doit être positif.',
        'newDiametre.max'            => 'Le diamètre ne peut pas dépasser 50mm.',
    ];

    public function mount($prescriptionId, $analyseId, $bacterieId, $compact = false, $hideHeader = false)
    {
        $this->prescriptionId = $prescriptionId;
        $this->analyseId      = $analyseId;
        $this->bacterieId     = $bacterieId;
        $this->compact        = $compact;
        $this->hideHeader     = $hideHeader;

        $this->loadData();
    }

    /**
     * ✅ Ne pas créer automatiquement l'antibiogramme
     */
    public function loadData()
    {
        try {
            // Chercher seulement (pas de création auto)
            $this->antibiogramme = Antibiogramme::where([
                'prescription_id' => $this->prescriptionId,
                'analyse_id'      => $this->analyseId,
                'bacterie_id'     => $this->bacterieId,
            ])->first();

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

            Log::info('AntibiogrammeTable chargé', [
                'prescription_id'          => $this->prescriptionId,
                'analyse_id'               => $this->analyseId,
                'bacterie_id'              => $this->bacterieId,
                'antibiogramme_existe'     => !is_null($this->antibiogramme),
                'antibiotiques_disponibles'=> count($this->antibiotiques),
                'resultats_count'          => count($this->resultats),
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement de l\'antibiogramme', [
                'prescription_id' => $this->prescriptionId,
                'analyse_id'      => $this->analyseId,
                'bacterie_id'     => $this->bacterieId,
                'error'           => $e->getMessage()
            ]);

            $this->flashError('Erreur lors du chargement de l\'antibiogramme.');
        }
    }

    /**
     * ✅ Chargement des résultats
     */
    private function loadResultats()
    {
        // Si pas d'antibiogramme → pas de résultats
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
                    'id'             => $resultat->id,
                    'antibiotique'   => [
                        'id'          => $resultat->antibiotique->id ?? null,
                        'designation' => $resultat->antibiotique->designation ?? '—',
                    ],
                    'interpretation' => $resultat->interpretation,
                    'diametre_mm'    => $resultat->diametre_mm,
                    'created_at'     => $resultat->created_at,
                ];
            })
            ->toArray();
    }

    /**
     * ✅ Créer l'antibiogramme SEULEMENT lors du premier ajout
     */
    public function addAntibiotique()
    {
        // Validation
        $this->validate();

        DB::beginTransaction();

        try {
            // Créer l'antibiogramme seulement maintenant
            if (!$this->antibiogramme) {
                $this->antibiogramme = Antibiogramme::firstOrCreate([
                    'prescription_id' => $this->prescriptionId,
                    'analyse_id'      => $this->analyseId,
                    'bacterie_id'     => $this->bacterieId,
                ]);
            }

            // Ajouter ou mettre à jour le résultat
            $resultatAntibiotique = ResultatAntibiotique::firstOrCreate(
                [
                    'antibiogramme_id' => $this->antibiogramme->id,
                    'antibiotique_id'  => $this->newAntibiotique,
                ],
                [
                    'interpretation'   => $this->newInterpretation,
                    'diametre_mm'      => $this->newDiametre ?: null,
                ]
            );

            if (!$resultatAntibiotique->wasRecentlyCreated) {
                $resultatAntibiotique->update([
                    'interpretation' => $this->newInterpretation,
                    'diametre_mm'    => $this->newDiametre ?: null,
                ]);
                $message = 'Antibiotique mis à jour avec succès.';
            } else {
                $message = 'Antibiotique ajouté avec succès.';
            }

            DB::commit();

            // Reset champs formulaire
            $this->reset(['newAntibiotique', 'newInterpretation', 'newDiametre']);
            $this->newInterpretation = 'S';

            // Recharger données & MAJ liste disponibles
            $this->loadResultats();

            $antibiotiquesUtilises = ResultatAntibiotique::where('antibiogramme_id', $this->antibiogramme->id)
                ->pluck('antibiotique_id')
                ->toArray();

            $this->antibiotiques = Antibiotique::actives()
                ->whereNotIn('id', $antibiotiquesUtilises)
                ->orderBy('designation')
                ->get();

            // ✅ Notification
            $this->flashSuccess($message);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur lors de l\'ajout d\'antibiotique', [
                'antibiogramme_id' => $this->antibiogramme?->id,
                'antibiotique_id'  => $this->newAntibiotique,
                'error'            => $e->getMessage(),
                'trace'            => $e->getTraceAsString()
            ]);

            $this->flashError('Erreur lors de l\'ajout de l\'antibiotique : ' . $e->getMessage());
        }
    }

    /**
     * ✅ Mise à jour inline sans rechargement complet
     */
    public function updateResultat($resultatId, $field, $value)
    {
        try {
            $resultat = ResultatAntibiotique::find($resultatId);

            if (!$resultat) {
                $this->flashError('Résultat d\'antibiotique introuvable.');
                return;
            }

            // Validation selon le champ
            if ($field === 'interpretation') {
                $this->validate([
                    'interpretation' => 'required|in:S,I,R',
                ], [
                    'interpretation.required' => 'L\'interprétation est requise.',
                    'interpretation.in'       => 'L\'interprétation doit être S, I ou R.',
                ], ['interpretation' => $value]);

            } elseif ($field === 'diametre_mm') {
                $this->validate([
                    'diametre_mm' => 'nullable|numeric|min:0|max:50',
                ], [
                    'diametre_mm.numeric' => 'Le diamètre doit être un nombre.',
                    'diametre_mm.min'     => 'Le diamètre doit être positif.',
                    'diametre_mm.max'     => 'Le diamètre ne peut pas dépasser 50mm.',
                ], ['diametre_mm' => $value]);
            }

            // Mise à jour
            $updateValue = ($field === 'diametre_mm') ? ($value ?: null) : $value;
            $resultat->update([$field => $updateValue]);

            // Recharger localement
            $this->loadResultats();

            // ✅ Notification
            $this->flashSuccess('Résultat mis à jour.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->flashError('Erreur de validation : ' . implode(', ', $e->validator->errors()->all()));

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du résultat', [
                'resultat_id' => $resultatId,
                'field'       => $field,
                'value'       => $value,
                'error'       => $e->getMessage()
            ]);

            $this->flashError('Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    /**
     * ✅ Suppression avec nettoyage automatique
     */
    public function removeResultat($resultatId)
    {
        DB::beginTransaction();

        try {
            $resultat = ResultatAntibiotique::find($resultatId);

            if (!$resultat) {
                $this->flashError('Résultat d\'antibiotique introuvable.');
                return;
            }

            $antibiotiqueName = $resultat->antibiotique->designation ?? 'Inconnu';
            $antibiogrammeId  = $this->antibiogramme->id ?? $resultat->antibiogramme_id;

            $resultat->delete();

            // Si plus de résultats → supprimer l'antibiogramme
            $remainingResults = ResultatAntibiotique::where('antibiogramme_id', $antibiogrammeId)->count();

            if ($remainingResults === 0) {
                // si le modèle est chargé, delete puis null
                if ($this->antibiogramme && $this->antibiogramme->id === $antibiogrammeId) {
                    $this->antibiogramme->delete();
                    $this->antibiogramme = null;
                } else {
                    // suppression directe si non chargé
                    Antibiogramme::where('id', $antibiogrammeId)->delete();
                }
            }

            DB::commit();
            // Recharger localement
            $this->loadResultats();

            // Remettre l'antibiotique dans la liste disponible
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

            // ✅ Notification
            $this->flashSuccess("Antibiotique « {$antibiotiqueName} » retiré avec succès.");

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur lors de la suppression du résultat', [
                'resultat_id' => $resultatId,
                'error'       => $e->getMessage()
            ]);

            $this->flashError('Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    /** Info : l'antibiogramme existe ? */
    public function hasAntibiogramme()
    {
        return !is_null($this->antibiogramme);
    }

    /** Info : nombre d’antibiotiques saisis */
    public function getAntibiotiquesCount()
    {
        return count($this->resultats);
    }

    public function render()
    {
        return view('livewire.technicien.antibiogramme-table');
    }
}
