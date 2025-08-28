<?php

namespace App\Livewire\Technicien;

use App\Models\Analyse;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Prescription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AnalysesSidebar extends Component
{
    public int $prescriptionId;
    public ?int $selectedParentId = null; // ✅ État actif

    // Exposé à la vue
    public array $analysesParents = [];
    public array $resultatsExistants = [];

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

    public function mount(int $prescriptionId): void
    {
        $this->prescriptionId = $prescriptionId;
        $this->loadAnalyses();
    }



    /**
     * ✅ Finalisation alternative basée sur statuts individuels
     */
    public function markPrescriptionAsCompletedAlternative()
    {
        try {
            DB::beginTransaction();

            $prescription = Prescription::findOrFail($this->prescriptionId);

            // Vérifier que tous les parents sont terminés
            $analysesParentsIncompletes = collect($this->analysesParents)
                ->where('status', '!=', 'TERMINE')
                ->count();

            Log::info('Vérification alternative finalisation', [
                'prescription_id'   => $this->prescriptionId,
                'total_parents'     => count($this->analysesParents),
                'parents_incomplets'=> $analysesParentsIncompletes,
                'parents_details'   => collect($this->analysesParents)->map(function($p) {
                    return [
                        'id'     => $p['id'],
                        'code'   => $p['code'],
                        'status' => $p['status']
                    ];
                })->toArray()
            ]);

            if ($analysesParentsIncompletes === 0 && count($this->analysesParents) > 0) {
                // Marquer tous les résultats comme terminés
                $prescription->resultats()->update(['status' => 'TERMINE']);

                // Marquer la prescription comme terminée
                $prescription->update(['status' => 'TERMINE']);

                Log::info('Prescription marquée comme terminée (méthode alternative)', [
                    'prescription_id' => $this->prescriptionId,
                    'reference'       => $prescription->reference,
                    'total_parents'   => count($this->analysesParents),
                    'user_id'         => Auth::id(),
                ]);

                DB::commit();

                // Événement pour redirection
                $this->dispatch('prescriptionCompleted')->to(ShowPrescription::class);

                $this->flashSuccess('Prescription marquée comme terminée avec succès !');
            } else {
                $this->flashError('Toutes les analyses doivent être terminées avant de finaliser la prescription.');
            }

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur lors de la finalisation alternative', [
                'prescription_id' => $this->prescriptionId,
                'error'           => $e->getMessage(),
            ]);

            $this->flashError('Erreur lors de la finalisation : ' . $e->getMessage());
        }
    }


    /**
     * ✅ Chargement des analyses avec statuts détaillés
     */
    public function loadAnalyses(): void
    {
        $prescription = Prescription::select('id')
            ->with([
                'analyses' => fn ($q) => $q->select('analyses.id','analyses.code','analyses.designation','analyses.level','analyses.parent_id','analyses.type_id')
                    ->with('type:id,name'),
                'resultats:id,prescription_id,analyse_id,status'
            ])
            ->findOrFail($this->prescriptionId);

        $this->resultatsExistants = $prescription->resultats->pluck('analyse_id')->all();
        $attachedIds              = $prescription->analyses->pluck('id')->all();

        $parents = $prescription->analyses->filter(function($analyse) use ($attachedIds) {
            return $analyse->level === 'PARENT'
                || is_null($analyse->parent_id)
                || !in_array($analyse->parent_id, $attachedIds);
        });

        $this->analysesParents = [];
        foreach ($parents as $parent) {
            $enfants = Analyse::where('parent_id', $parent->id)
                ->where('status', true)
                ->whereHas('type', function($q) {
                    $q->where('name', '!=', 'LABEL');
                })
                ->pluck('id')->all();

            $enfantsCompleted = count(array_intersect($enfants, $this->resultatsExistants));

            // statut calculé (tu peux laisser ta logique actuelle si tu veux garder la couleur)
            $status = $this->determineAnalyseStatus($parent->id, $enfants, $enfantsCompleted);

            // ✅ éligibilité à la finalisation (tout est prêt)
            $eligible = !empty($enfants)
                ? $this->checkAllChildrenRecursively($enfants)    // tous les descendants ont un résultat
                : in_array($parent->id, $this->resultatsExistants); // pas d’enfants → le parent lui-même a un résultat

            // code d’affichage
            $displayCode = $parent->code;
            if ($parent->parent_id && !in_array($parent->parent_id, $attachedIds)) {
                $realParent = Analyse::find($parent->parent_id);
                if ($realParent) {
                    $displayCode = $realParent->code . ' - ' . $parent->code;
                }
            }

            $this->analysesParents[] = [
                'id'                => $parent->id,
                'code'              => $displayCode,
                'designation'       => $parent->designation,
                'enfants_count'     => count($enfants),
                'enfants_completed' => $enfantsCompleted,
                'status'            => $status,
                'eligible'          => $eligible,      // 👈 ajouté
            ];
        }
    }

    /**
     * ✅ Déterminer le statut d'une analyse (récursif)
     */
    private function determineAnalyseStatus(int $parentId, array $enfants, int $enfantsCompleted): string
    {
        if (empty($enfants)) {
            // Analyse sans enfants → vérifier résultat direct
            return in_array($parentId, $this->resultatsExistants) ? 'TERMINE' : 'VIDE';
        }

        // Vérification récursive des enfants
        $allChildrenCompleted = $this->checkAllChildrenRecursively($enfants);

        if ($allChildrenCompleted) {
            return 'TERMINE';
        } elseif ($enfantsCompleted > 0 || $this->hasAnyChildResults($enfants)) {
            return 'EN_COURS';
        } else {
            return 'VIDE';
        }
    }

    /**
     * ✅ Vérifier récursivement tous les enfants
     */
    private function checkAllChildrenRecursively(array $enfantIds): bool
    {
        foreach ($enfantIds as $enfantId) {
            if (!$this->isAnalyseCompleteRecursively($enfantId)) {
                return false;
            }
        }
        return true;
    }

    /**
     * ✅ Une analyse est-elle complète ? (récursif)
     */
    private function isAnalyseCompleteRecursively(int $analyseId): bool
    {
        // Si résultat direct
        if (in_array($analyseId, $this->resultatsExistants)) {
            return true;
        }

        // Sinon, vérifier ses enfants (hors LABEL)
        $enfants = Analyse::where('parent_id', $analyseId)
            ->where('status', true)
            ->whereHas('type', function($q) {
                $q->where('name', '!=', 'LABEL');
            })
            ->pluck('id')->all();

        if (empty($enfants)) {
            return false;
        }

        return $this->checkAllChildrenRecursively($enfants);
    }

    /**
     * ✅ Au moins un enfant a des résultats ? (récursif)
     */
    private function hasAnyChildResults(array $enfantIds): bool
    {
        foreach ($enfantIds as $enfantId) {
            if (in_array($enfantId, $this->resultatsExistants)) {
                return true;
            }

            $sousEnfants = Analyse::where('parent_id', $enfantId)
                ->where('status', true)
                ->whereHas('type', function($q) {
                    $q->where('name', '!=', 'LABEL');
                })
                ->pluck('id')->all();

            if (!empty($sousEnfants) && $this->hasAnyChildResults($sousEnfants)) {
                return true;
            }
        }
        return false;
    }


    #[On('refreshSidebar')]
    public function refreshSidebar(): void
    {
        $this->loadAnalyses();
    }

    /**
     * ✅ Sélection avec event parent
     */
    public function selectAnalyseParent(int $parentId): void
    {
        $this->selectedParentId = $parentId;
        $this->dispatch('parentSelected', parentId: $parentId)->to(ShowPrescription::class);
    }

    /**
     * ✅ Marquer une analyse individuelle comme terminée
     */
    public function markAnalyseAsCompleted(int $parentId)
    {
        try {
            DB::beginTransaction();

            $prescription = Prescription::findOrFail($this->prescriptionId);

            // Enfants hors LABEL
            $enfants = Analyse::where('parent_id', $parentId)
                ->where('status', true)
                ->whereHas('type', function($q) {
                    $q->where('name', '!=', 'LABEL');
                })
                ->pluck('id')->all();

            if (empty($enfants)) {
                // Pas d'enfants (ou seulement LABEL) → on cible l’analyse elle-même
                $enfants = [$parentId];
            }

            // Tous les enfants ont-ils des résultats ?
            $resultatsCount = $prescription->resultats()
                ->whereIn('analyse_id', $enfants)
                ->count();

            if ($resultatsCount === count($enfants)) {
                // Marquer ces résultats terminés
                $prescription->resultats()
                    ->whereIn('analyse_id', $enfants)
                    ->update(['status' => 'TERMINE']);

                Log::info('Analyse marquée comme terminée', [
                    'prescription_id'      => $this->prescriptionId,
                    'parent_id'            => $parentId,
                    'enfants_count'        => count($enfants),
                    'enfants_with_results' => $resultatsCount,
                    'user_id'              => Auth::id(),
                ]);

                DB::commit();

                // Rafraîchir la sidebar
                $this->loadAnalyses();

                // Notifier le composant parent
                $this->dispatch('analyseCompleted', parentId: $parentId);

                $this->flashSuccess('Analyse marquée comme terminée !');
            } else {
                $this->flashError('Tous les résultats doivent être saisis avant de terminer cette analyse.');
            }

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur lors de la finalisation de l\'analyse', [
                'prescription_id' => $this->prescriptionId,
                'parent_id'       => $parentId,
                'error'           => $e->getMessage(),
            ]);

            $this->flashError('Erreur lors de la finalisation : ' . $e->getMessage());
        }
    }

    /**
     * ✅ Marquer la prescription comme terminée (comptage via pivot)
     */
    public function markPrescriptionAsCompleted()
    {
        try {
            DB::beginTransaction();

            $prescription = Prescription::findOrFail($this->prescriptionId);

            // Compter via la table pivot (hors LABEL)
            $totalAnalyses = DB::table('prescription_analyse')
                ->join('analyses', 'prescription_analyse.analyse_id', '=', 'analyses.id')
                ->join('types', 'analyses.type_id', '=', 'types.id')
                ->where('prescription_analyse.prescription_id', $this->prescriptionId)
                ->where('types.name', '!=', 'LABEL')
                ->count();

            $completedAnalyses = $prescription->resultats()->count();

            Log::info('Vérification finalisation prescription CORRIGÉE', [
                'prescription_id'                     => $this->prescriptionId,
                'total_analyses_non_label_via_pivot'  => $totalAnalyses,
                'completed_analyses'                  => $completedAnalyses,
                'analyses_details'                    => DB::table('prescription_analyse')
                    ->join('analyses', 'prescription_analyse.analyse_id', '=', 'analyses.id')
                    ->join('types', 'analyses.type_id', '=', 'types.id')
                    ->where('prescription_analyse.prescription_id', $this->prescriptionId)
                    ->select('analyses.id', 'analyses.code', 'types.name as type_name')
                    ->get()
                    ->toArray()
            ]);

            if ($totalAnalyses === $completedAnalyses && $totalAnalyses > 0) {
                // Marquer tous les résultats comme terminés
                $prescription->resultats()->update(['status' => 'TERMINE']);

                // Marquer la prescription comme terminée
                $prescription->update(['status' => 'TERMINE']);

                Log::info('Prescription marquée comme terminée', [
                    'prescription_id'   => $this->prescriptionId,
                    'reference'         => $prescription->reference,
                    'total_analyses'    => $totalAnalyses,
                    'completed_analyses'=> $completedAnalyses,
                    'user_id'           => Auth::id(),
                ]);

                DB::commit();

                // Événement pour redirection
                $this->dispatch('prescriptionCompleted')->to(ShowPrescription::class);

                $this->flashSuccess('Prescription marquée comme terminée avec succès !');

            } else {
                Log::warning('Finalisation impossible - comptage incorrect', [
                    'prescription_id'   => $this->prescriptionId,
                    'total_analyses'    => $totalAnalyses,
                    'completed_analyses'=> $completedAnalyses,
                    'condition_met'     => $totalAnalyses === $completedAnalyses
                ]);

                $this->flashError("Toutes les analyses doivent être complétées avant de terminer la prescription. ({$completedAnalyses}/{$totalAnalyses})");
            }

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur lors de la finalisation de la prescription', [
                'prescription_id' => $this->prescriptionId,
                'error'           => $e->getMessage(),
                'trace'           => $e->getTraceAsString()
            ]);

            $this->flashError('Erreur lors de la finalisation : ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.technicien.analyses-sidebar');
    }
}
