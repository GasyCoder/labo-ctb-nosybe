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
    public ?int $selectedParentId = null; // ✅ Ajout pour l'état actif

    // exposé à la vue
    public array $analysesParents = [];
    public array $resultatsExistants = [];

    public function mount(int $prescriptionId): void
    {
        $this->prescriptionId = $prescriptionId;
        $this->loadAnalyses();
    }

    /**
     * ✅ MÉTHODE ALTERNATIVE : Finalisation basée sur les statuts individuels
     */
    public function markPrescriptionAsCompletedAlternative()
    {
        try {
            DB::beginTransaction();
            
            $prescription = Prescription::findOrFail($this->prescriptionId);
            
            // ✅ NOUVELLE APPROCHE : Vérifier que tous les parents sont terminés
            $analysesParentsIncompletes = collect($this->analysesParents)
                ->where('status', '!=', 'TERMINE')
                ->count();
            
            Log::info('Vérification alternative finalisation', [
                'prescription_id' => $this->prescriptionId,
                'total_parents' => count($this->analysesParents),
                'parents_incomplets' => $analysesParentsIncompletes,
                'parents_details' => collect($this->analysesParents)->map(function($p) {
                    return [
                        'id' => $p['id'],
                        'code' => $p['code'], 
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
                    'reference' => $prescription->reference,
                    'total_parents' => count($this->analysesParents),
                    'user_id' => Auth::id(),
                ]);
                
                DB::commit();
                
                // Émettre un événement pour redirection
                $this->dispatch('prescriptionCompleted')->to(ShowPrescription::class);
                
                session()->flash('message', 'Prescription marquée comme terminée avec succès !');
                
            } else {
                session()->flash('error', 'Toutes les analyses doivent être terminées avant de finaliser la prescription.');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erreur lors de la finalisation alternative', [
                'prescription_id' => $this->prescriptionId,
                'error' => $e->getMessage(),
            ]);
            
            session()->flash('error', 'Erreur lors de la finalisation : ' . $e->getMessage());
        }
    }


    /**
     * ✅ MÉTHODE AMÉLIORÉE : Chargement des analyses avec statuts détaillés
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
            // ✅ CORRECTION : Exclure les analyses de type LABEL du comptage
            $enfants = Analyse::where('parent_id', $parent->id)
                ->where('status', true)
                ->whereHas('type', function($q) {
                    $q->where('name', '!=', 'LABEL'); // ✅ Exclure les LABEL
                })
                ->pluck('id')->all();
                
            $enfantsCompleted = count(array_intersect($enfants, $this->resultatsExistants));

            // ✅ NOUVEAU : Déterminer le statut de l'analyse parent
            $status = $this->determineAnalyseStatus($parent->id, $enfants, $enfantsCompleted);

            // Si orpheline avec parent, charger le code parent
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
                'status'            => $status, // ✅ NOUVEAU
            ];
        }
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Déterminer le statut d'une analyse
     */
    private function determineAnalyseStatus(int $parentId, array $enfants, int $enfantsCompleted): string
    {
        if (empty($enfants)) {
            // Analyse sans enfants, vérifier si elle a un résultat
            return in_array($parentId, $this->resultatsExistants) ? 'TERMINE' : 'VIDE';
        }

        if ($enfantsCompleted === 0) {
            return 'VIDE';
        } elseif ($enfantsCompleted === count($enfants)) {
            return 'TERMINE';
        } else {
            return 'EN_COURS';
        }
    }

    #[On('refreshSidebar')]
    public function refreshSidebar(): void
    {
        $this->loadAnalyses();
    }

    /**
     * ✅ MÉTHODE AMÉLIORÉE : Sélection avec URL ancrée
     */
    public function selectAnalyseParent(int $parentId): void
    {
        $this->selectedParentId = $parentId;
        $this->dispatch('parentSelected', parentId: $parentId)->to(ShowPrescription::class);
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Marquer une analyse individuelle comme terminée
     */
    public function markAnalyseAsCompleted(int $parentId)
    {
        try {
            DB::beginTransaction();
            
            $prescription = Prescription::findOrFail($this->prescriptionId);
            
            // ✅ CORRECTION : Récupérer les enfants en excluant les LABEL
            $enfants = Analyse::where('parent_id', $parentId)
                ->where('status', true)
                ->whereHas('type', function($q) {
                    $q->where('name', '!=', 'LABEL'); // ✅ Exclure les LABEL
                })
                ->pluck('id')->all();
            
            if (empty($enfants)) {
                // Si pas d'enfants (ou que des LABEL), c'est l'analyse elle-même
                $enfants = [$parentId];
            }
            
            // Vérifier que tous les enfants ont des résultats
            $resultatsCount = $prescription->resultats()
                ->whereIn('analyse_id', $enfants)
                ->count();
            
            if ($resultatsCount === count($enfants)) {
                // Marquer tous les résultats de cette analyse comme terminés
                $prescription->resultats()
                    ->whereIn('analyse_id', $enfants)
                    ->update(['status' => 'TERMINE']);
                
                Log::info('Analyse marquée comme terminée', [
                    'prescription_id' => $this->prescriptionId,
                    'parent_id' => $parentId,
                    'enfants_count' => count($enfants),
                    'enfants_with_results' => $resultatsCount,
                    'user_id' => Auth::id(),
                ]);
                
                DB::commit();
                
                // Rafraîchir la sidebar
                $this->loadAnalyses();
                
                // Notifier le composant parent
                $this->dispatch('analyseCompleted', parentId: $parentId);
                
                session()->flash('message', 'Analyse marquée comme terminée !');
                
            } else {
                session()->flash('error', 'Tous les résultats doivent être saisis avant de terminer cette analyse.');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erreur lors de la finalisation de l\'analyse', [
                'prescription_id' => $this->prescriptionId,
                'parent_id' => $parentId,
                'error' => $e->getMessage(),
            ]);
            
            session()->flash('error', 'Erreur lors de la finalisation : ' . $e->getMessage());
        }
    }

    /**
     * ✅ MÉTHODE CORRIGÉE : Marquer la prescription comme terminée
     */
    public function markPrescriptionAsCompleted()
    {
        try {
            DB::beginTransaction();
            
            $prescription = Prescription::findOrFail($this->prescriptionId);
            
            // ✅ CORRECTION : Compter via la table pivot prescription_analyse
            $totalAnalyses = DB::table('prescription_analyse')
                ->join('analyses', 'prescription_analyse.analyse_id', '=', 'analyses.id')
                ->join('types', 'analyses.type_id', '=', 'types.id')
                ->where('prescription_analyse.prescription_id', $this->prescriptionId)
                ->where('types.name', '!=', 'LABEL')
                ->count();
                
            $completedAnalyses = $prescription->resultats()->count();
            
            Log::info('Vérification finalisation prescription CORRIGÉE', [
                'prescription_id' => $this->prescriptionId,
                'total_analyses_non_label_via_pivot' => $totalAnalyses,
                'completed_analyses' => $completedAnalyses,
                'analyses_details' => DB::table('prescription_analyse')
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
                    'prescription_id' => $this->prescriptionId,
                    'reference' => $prescription->reference,
                    'total_analyses' => $totalAnalyses,
                    'completed_analyses' => $completedAnalyses,
                    'user_id' => Auth::id(),
                ]);
                
                DB::commit();
                
                // Émettre un événement pour redirection
                $this->dispatch('prescriptionCompleted')->to(ShowPrescription::class);
                
                session()->flash('message', 'Prescription marquée comme terminée avec succès !');
                
            } else {
                Log::warning('Finalisation impossible - comptage incorrect', [
                    'prescription_id' => $this->prescriptionId,
                    'total_analyses' => $totalAnalyses,
                    'completed_analyses' => $completedAnalyses,
                    'condition_met' => $totalAnalyses === $completedAnalyses
                ]);
                
                session()->flash('error', "Toutes les analyses doivent être complétées avant de terminer la prescription. ({$completedAnalyses}/{$totalAnalyses})");
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erreur lors de la finalisation de la prescription', [
                'prescription_id' => $this->prescriptionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            session()->flash('error', 'Erreur lors de la finalisation : ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.technicien.analyses-sidebar');
    }
}