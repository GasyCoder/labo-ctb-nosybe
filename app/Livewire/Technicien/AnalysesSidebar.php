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
    public ?int $selectedParentId = null;

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

    public function mount(int $prescriptionId): void
    {
        $this->prescriptionId = $prescriptionId;
        $this->loadAnalyses();
    }

    /**
     * ✅ Chargement des analyses avec logique améliorée
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
        $attachedIds = $prescription->analyses->pluck('id')->all();

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

            // ✅ Logique de statut améliorée
            $analysisData = $this->getAnalysisStatus($parent->id, $enfants, $enfantsCompleted);

            // Code d'affichage
            $displayCode = $parent->code;
            if ($parent->parent_id && !in_array($parent->parent_id, $attachedIds)) {
                $realParent = Analyse::find($parent->parent_id);
                if ($realParent) {
                    $displayCode = $realParent->code . ' - ' . $parent->code;
                }
            }

            $this->analysesParents[] = [
                'id' => $parent->id,
                'code' => $displayCode,
                'designation' => $parent->designation,
                'enfants_count' => count($enfants),
                'enfants_completed' => $enfantsCompleted,
                'status' => $analysisData['status'],
                'can_complete' => $analysisData['can_complete'],
                'is_ready' => $analysisData['is_ready'],
            ];
        }
    }

    /**
     * ✅ Déterminer le statut et les actions possibles pour une analyse
     */
    private function getAnalysisStatus(int $parentId, array $enfants, int $enfantsCompleted): array
    {
        if (empty($enfants)) {
            // Analyse sans enfants → vérifier résultat direct
            $hasResult = in_array($parentId, $this->resultatsExistants);
            $isCompleted = $this->checkResultStatus($parentId) === 'TERMINE';
            
            return [
                'status' => $isCompleted ? 'TERMINE' : ($hasResult ? 'EN_COURS' : 'VIDE'),
                'can_complete' => $hasResult && !$isCompleted,
                'is_ready' => $hasResult && !$isCompleted,
            ];
        }

        // Analyse avec enfants
        $allChildrenHaveResults = $this->checkAllChildrenHaveResults($enfants);
        $allResultsCompleted = $this->checkAllResultsCompleted($enfants);
        $someChildrenHaveResults = $enfantsCompleted > 0 || $this->hasAnyChildResults($enfants);

        if ($allChildrenHaveResults && $allResultsCompleted) {
            return [
                'status' => 'TERMINE',
                'can_complete' => false,
                'is_ready' => false,
            ];
        } elseif ($allChildrenHaveResults) {
            return [
                'status' => 'EN_COURS',
                'can_complete' => true,
                'is_ready' => true,
            ];
        } elseif ($someChildrenHaveResults) {
            return [
                'status' => 'EN_COURS',
                'can_complete' => false,
                'is_ready' => false,
            ];
        } else {
            return [
                'status' => 'VIDE',
                'can_complete' => false,
                'is_ready' => false,
            ];
        }
    }

    /**
     * ✅ Vérifier le statut d'un résultat spécifique
     */
    private function checkResultStatus(int $analyseId): ?string
    {
        $prescription = Prescription::findOrFail($this->prescriptionId);
        $resultat = $prescription->resultats()->where('analyse_id', $analyseId)->first();
        return $resultat?->status;
    }

    /**
     * ✅ Vérifier si tous les enfants ont des résultats
     */
    private function checkAllChildrenHaveResults(array $enfantIds): bool
    {
        foreach ($enfantIds as $enfantId) {
            if (!$this->analyseHasCompleteResults($enfantId)) {
                return false;
            }
        }
        return true;
    }

    /**
     * ✅ Vérifier si tous les résultats sont marqués comme terminés
     */
    private function checkAllResultsCompleted(array $enfantIds): bool
    {
        $prescription = Prescription::findOrFail($this->prescriptionId);
        
        foreach ($enfantIds as $enfantId) {
            $resultat = $prescription->resultats()->where('analyse_id', $enfantId)->first();
            if (!$resultat || $resultat->status !== 'TERMINE') {
                return false;
            }
        }
        return true;
    }

    /**
     * ✅ Une analyse a-t-elle des résultats complets ?
     */
    private function analyseHasCompleteResults(int $analyseId): bool
    {
        // Si résultat direct
        if (in_array($analyseId, $this->resultatsExistants)) {
            return true;
        }

        // Vérifier ses enfants (hors LABEL)
        $enfants = Analyse::where('parent_id', $analyseId)
            ->where('status', true)
            ->whereHas('type', function($q) {
                $q->where('name', '!=', 'LABEL');
            })
            ->pluck('id')->all();

        if (empty($enfants)) {
            return false;
        }

        return $this->checkAllChildrenHaveResults($enfants);
    }

    /**
     * ✅ Au moins un enfant a des résultats ?
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

    /**
     * ✅ Vérifier si la prescription peut être finalisée
     */
    public function canFinalizePrescription(): bool
    {
        return collect($this->analysesParents)->every(function($analysis) {
            return $analysis['status'] === 'TERMINE';
        });
    }

    /**
     * ✅ Vérifier si la prescription est prête à être finalisée
     */
    public function isReadyToFinalize(): bool
    {
        return collect($this->analysesParents)->every(function($analysis) {
            return $analysis['can_complete'] || $analysis['status'] === 'TERMINE';
        });
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Vérifier et mettre à jour le statut de la prescription
     */
    private function updatePrescriptionStatusIfNeeded(): void
    {
        $prescription = Prescription::findOrFail($this->prescriptionId);
        
        // Si toutes les analyses sont terminées, marquer la prescription comme terminée
        if ($this->canFinalizePrescription()) {
            $prescription->update(['status' => 'TERMINE']);
            Log::info('Prescription automatiquement marquée comme terminée', [
                'prescription_id' => $this->prescriptionId,
                'user_id' => Auth::id(),
            ]);
        }
        // Si au moins une analyse est en cours, marquer la prescription comme en cours
        elseif ($prescription->status === 'EN_ATTENTE' && $this->hasAnalysesInProgress()) {
            $prescription->update(['status' => 'EN_COURS']);
            Log::info('Prescription marquée comme en cours', [
                'prescription_id' => $this->prescriptionId,
                'user_id' => Auth::id(),
            ]);
        }
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Vérifier si des analyses sont en cours
     */
    private function hasAnalysesInProgress(): bool
    {
        return collect($this->analysesParents)->some(function($analysis) {
            return in_array($analysis['status'], ['EN_COURS', 'TERMINE']);
        });
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
     * ✅ Marquer une analyse individuelle comme terminée - CORRIGÉ
     */
    public function markAnalyseAsCompleted(int $parentId)
    {
        try {
            DB::beginTransaction();

            $prescription = Prescription::findOrFail($this->prescriptionId);

            // Trouver l'analyse dans notre liste
            $analysis = collect($this->analysesParents)->firstWhere('id', $parentId);
            
            if (!$analysis || !$analysis['can_complete']) {
                $this->flashError('Cette analyse ne peut pas encore être marquée comme terminée.');
                return;
            }

            // Enfants hors LABEL
            $enfants = Analyse::where('parent_id', $parentId)
                ->where('status', true)
                ->whereHas('type', function($q) {
                    $q->where('name', '!=', 'LABEL');
                })
                ->pluck('id')->all();

            if (empty($enfants)) {
                // Pas d'enfants → on cible l'analyse elle-même
                $enfants = [$parentId];
            }

            // Marquer ces résultats comme terminés
            $updated = $prescription->resultats()
                ->whereIn('analyse_id', $enfants)
                ->update(['status' => 'TERMINE']);

            if ($updated > 0) {
                Log::info('Analyse marquée comme terminée', [
                    'prescription_id' => $this->prescriptionId,
                    'parent_id' => $parentId,
                    'enfants_updated' => $updated,
                    'user_id' => Auth::id(),
                ]);

                // ✅ NOUVEAU : Vérifier et mettre à jour le statut de la prescription
                $this->updatePrescriptionStatusIfNeeded();

                DB::commit();

                // Rafraîchir la sidebar
                $this->loadAnalyses();

                // Notifier le composant parent
                $this->dispatch('analyseCompleted', parentId: $parentId);

                $this->flashSuccess('Analyse marquée comme terminée !');
            } else {
                $this->flashError('Aucun résultat trouvé pour cette analyse.');
            }

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur lors de la finalisation de l\'analyse', [
                'prescription_id' => $this->prescriptionId,
                'parent_id' => $parentId,
                'error' => $e->getMessage(),
            ]);

            $this->flashError('Erreur lors de la finalisation : ' . $e->getMessage());
        }
    }

    /**
     * ✅ Finaliser toute la prescription - CORRIGÉ
     */
    public function markPrescriptionAsCompleted()
    {
        try {
            DB::beginTransaction();

            if (!$this->isReadyToFinalize()) {
                $this->flashError('Toutes les analyses doivent avoir leurs résultats avant de finaliser la prescription.');
                return;
            }

            $prescription = Prescription::findOrFail($this->prescriptionId);

            // Marquer tous les résultats comme terminés
            $prescription->resultats()->update(['status' => 'TERMINE']);

            // Marquer la prescription comme terminée
            $prescription->update(['status' => 'TERMINE']);

            Log::info('Prescription marquée comme terminée', [
                'prescription_id' => $this->prescriptionId,
                'reference' => $prescription->reference,
                'total_analyses' => count($this->analysesParents),
                'user_id' => Auth::id(),
            ]);

            DB::commit();

            // Rafraîchir la sidebar
            $this->loadAnalyses();

            // Événement pour redirection
            $this->dispatch('prescriptionCompleted')->to(ShowPrescription::class);

            $this->flashSuccess('Prescription marquée comme terminée avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur lors de la finalisation de la prescription', [
                'prescription_id' => $this->prescriptionId,
                'error' => $e->getMessage(),
            ]);

            $this->flashError('Erreur lors de la finalisation : ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.technicien.analyses-sidebar');
    }
}