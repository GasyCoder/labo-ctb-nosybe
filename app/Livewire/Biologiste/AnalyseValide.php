<?php

namespace App\Livewire\Biologiste;

use Livewire\Component;
use App\Models\Resultat;
use App\Models\Prescription;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Models\Analyse;
use App\Models\AnalysePrescription;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AnalyseValide extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $tab = 'termine';
    public $search = '';

    protected $queryString = [
        'search',
        'tab' => ['except' => 'termine'],
    ];



    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedTab()
    {
        $this->resetPage();
    }

    public function openAnalyse($prescriptionId)
    {
        $prescription = Prescription::findOrFail($prescriptionId);
        return $this->redirect(route('biologiste.valide.show', ['prescription' => $prescription]));
    }

    public function render()
    {
        $search = '%' . $this->search . '%';

        $baseQuery = Prescription::with([
            'patient',
            'prescripteur:id,nom,is_active',
            'analyses.type'
        ])
            ->whereHas('patient', fn($q) => $q->whereNull('deleted_at'));

        $searchCondition = function ($query) use ($search) {
            $query->where('renseignement_clinique', 'like', $search)
                ->orWhere('status', 'like', $search)
                ->orWhereHas('patient', function ($q) use ($search) {
                    $q->where('nom', 'like', $search)
                        ->orWhere('prenom', 'like', $search)
                        ->orWhere('telephone', 'like', $search);
                })
                ->orWhereHas('prescripteur', function ($q) use ($search) {
                    $q->where('nom', 'like', $search)
                        ->where('is_active', true);
                });
        };

        $analyseValides = (clone $baseQuery)
            ->where('status', Prescription::STATUS_VALIDE)
            ->where($searchCondition)
            ->oldest()
            ->paginate(15);

        $analyseTermines = (clone $baseQuery)
            ->where('status', Prescription::STATUS_TERMINE)
            ->where($searchCondition)
            ->oldest()
            ->paginate(15);

        return view('livewire.biologiste.analyse-valide', compact('analyseValides', 'analyseTermines'));
    }

 

    // Méthode pour collecter les IDs des enfants basée sur le modèle Analyse
    private function collectChildAnalyseIds($analyse, &$allAnalyseIds)
    {
        // Utiliser la relation 'enfants' du modèle Analyse
        if ($analyse->enfants && $analyse->enfants->isNotEmpty()) {
            foreach ($analyse->enfants as $child) {
                $allAnalyseIds->push($child->id);
                $this->collectChildAnalyseIds($child, $allAnalyseIds);
            }
        }
    }

    public function redoPrescription($prescriptionId)
    {
        try {
            DB::beginTransaction();

            $prescription = Prescription::findOrFail($prescriptionId);

            // Mettre à jour le statut de la prescription
            $prescription->update([
                'status' => Prescription::STATUS_A_REFAIRE
            ]);

            // Mettre à jour toutes les analyses de la prescription dans la table pivot
            foreach ($prescription->analyses as $analyse) {
                AnalysePrescription::where([
                    'prescription_id' => $prescriptionId,
                    'analyse_id' => $analyse->id
                ])->update([
                            'status' => AnalysePrescription::STATUS_A_REFAIRE
                        ]);
            }

            // Réinitialiser les résultats
            Resultat::where('prescription_id', $prescriptionId)
                ->update([
                    'validated_by' => null,
                    'validated_at' => null,
                    'status' => 'EN_ATTENTE'
                ]);

            DB::commit();

            $this->dispatch('prescription-status-updated');
            $this->alert('success', 'La prescription a été marquée à refaire');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de la mise à refaire de la prescription:', [
                'message' => $e->getMessage(),
                'prescription_id' => $prescriptionId,
                'user_id' => Auth::id()
            ]);

            $this->alert('error', "Une erreur s'est produite lors de la mise à refaire de la prescription");
        }
    }

    public function validateAnalyse($prescriptionId)
    {
        try {
            DB::beginTransaction();

            $prescription = Prescription::findOrFail($prescriptionId);

            // Récupérer toutes les analyses parents avec leurs enfants
            $parentAnalyses = $prescription->analyses()
                ->with(['enfants'])
                ->where('level', 'PARENT')
                ->get();

            $allAnalyseIds = collect();

            // Collecter tous les IDs des analyses (parents et enfants)
            foreach ($parentAnalyses as $parentAnalyse) {
                $allAnalyseIds->push($parentAnalyse->id);
                $this->collectChildAnalyseIds($parentAnalyse, $allAnalyseIds);
            }

            // Mettre à jour les résultats
            Resultat::where('prescription_id', $prescriptionId)
                ->whereIn('analyse_id', $allAnalyseIds)
                ->update([
                    'validated_by' => Auth::id(),
                    'validated_at' => now(),
                    'status' => 'VALIDE'
                ]);

            // Mettre à jour les statuts des analyses pivot
            foreach ($parentAnalyses as $parentAnalyse) {
                // Mettre à jour l'analyse parent en VALIDE
                AnalysePrescription::where([
                    'prescription_id' => $prescriptionId,
                    'analyse_id' => $parentAnalyse->id
                ])->update([
                            'status' => AnalysePrescription::STATUS_VALIDE,
                            'updated_at' => now()
                        ]);

                // Les analyses enfants restent en TERMINE si elles étaient déjà TERMINE
                if ($parentAnalyse->enfants->isNotEmpty()) {
                    foreach ($parentAnalyse->enfants as $enfant) {
                        AnalysePrescription::where([
                            'prescription_id' => $prescriptionId,
                            'analyse_id' => $enfant->id
                        ])->where('status', AnalysePrescription::STATUS_TERMINE)
                            ->update([
                                'status' => AnalysePrescription::STATUS_TERMINE,
                                'updated_at' => now()
                            ]);
                    }
                }
            }

            // Vérifier si toutes les analyses principales sont validées
            $totalParentAnalyses = $parentAnalyses->count();
            $validatedParentAnalyses = AnalysePrescription::where([
                'prescription_id' => $prescriptionId,
                'status' => AnalysePrescription::STATUS_VALIDE
            ])->whereIn('analyse_id', $parentAnalyses->pluck('id'))->count();

            // Mise à jour du statut de la prescription
            if ($totalParentAnalyses === $validatedParentAnalyses) {
                $prescription->update([
                    'status' => Prescription::STATUS_VALIDE
                ]);
            } else {
                $prescription->update([
                    'status' => Prescription::STATUS_TERMINE
                ]);
            }

            DB::commit();

            $this->dispatch('$refresh');
            $this->alert('success', 'Les analyses ont été validées avec succès');

            return redirect()->route('biologiste.analyse.index', ['tab' => 'valide']);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur validation analyses:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'prescription_id' => $prescriptionId,
                'user_id' => Auth::id()
            ]);

            $this->alert('error', "Une erreur s'est produite lors de la validation");
            return false;
        }
    }
}