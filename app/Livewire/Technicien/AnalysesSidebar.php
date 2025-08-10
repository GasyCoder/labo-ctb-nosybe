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

    // exposé à la vue
    public array $analysesParents = [];
    public array $resultatsExistants = [];

    public function mount(int $prescriptionId): void
    {
        $this->prescriptionId = $prescriptionId;
        $this->loadAnalyses();
    }

    public function loadAnalyses(): void
    {
        $prescription = Prescription::select('id')
            ->with([
                'analyses' => fn ($q) => $q->select('analyses.id','analyses.code','analyses.designation','analyses.level','analyses.parent_id'),
                'resultats:id,prescription_id,analyse_id'
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
            $enfants = Analyse::where('parent_id', $parent->id)->where('status', true)->pluck('id')->all();
            $completed = count(array_intersect($enfants, $this->resultatsExistants));

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
                'enfants_completed' => $completed,
            ];
        }
    }

    #[On('refreshSidebar')]
    public function refreshSidebar(): void
    {
        $this->loadAnalyses();
    }

    public function selectAnalyseParent(int $parentId): void
    {
        $this->dispatch('parentSelected', parentId: $parentId)->to(ShowPrescription::class);
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Marquer la prescription comme terminée
     */
    public function markPrescriptionAsCompleted()
    {
        try {
            DB::beginTransaction();
            
            $prescription = Prescription::findOrFail($this->prescriptionId);
            
            // Vérifier que toutes les analyses ont des résultats
            $totalAnalyses = $prescription->analyses()->count();
            $completedAnalyses = $prescription->resultats()->count();
            
            if ($totalAnalyses === $completedAnalyses && $totalAnalyses > 0) {
                $prescription->update(['status' => 'TERMINE']);
                
                Log::info('Prescription marquée comme terminée', [
                    'prescription_id' => $this->prescriptionId,
                    'reference' => $prescription->reference,
                    'total_analyses' => $totalAnalyses,
                    'completed_analyses' => $completedAnalyses,
                    'user_id' => Auth::id(),
                ]);
                
                DB::commit();
                
                // Émettre un événement pour mettre à jour d'autres composants
                $this->dispatch('prescriptionCompleted', prescriptionId: $this->prescriptionId);
                
                // Message de succès
                session()->flash('message', 'Prescription marquée comme terminée avec succès !');
                
                // Rediriger vers la liste des prescriptions
                return redirect()->route('technicien.index');
                
            } else {
                session()->flash('error', 'Toutes les analyses doivent être complétées avant de terminer la prescription.');
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