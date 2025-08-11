<?php

namespace App\Livewire\Technicien;

use Livewire\Component;
use App\Models\Prescription;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class IndexTechnicien extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    /**
     * ✅ CORRIGÉ : Démarrer l'analyse (EN_ATTENTE → EN_COURS)
     */
    public function startAnalysis(int $prescriptionId)
    {
        try {
            DB::beginTransaction();

            $prescription = Prescription::findOrFail($prescriptionId);

            // Changer le statut à EN_COURS si ce n'est pas déjà le cas
            if ($prescription->status === Prescription::STATUS_EN_ATTENTE) {
                $prescription->update([
                    'status' => Prescription::STATUS_EN_COURS,
                    'started_by' => Auth::id(),
                    'started_at' => now()
                ]);

                Log::info('Prescription passée en cours', [
                    'prescription_id' => $prescriptionId,
                    'reference' => $prescription->reference,
                    'technicien_id' => Auth::id(),
                ]);
            }

            DB::commit();

            // Message de succès
            session()->flash('message', 'Analyse démarrée avec succès !');

            // Redirection vers la page de traitement
            return redirect()->route('technicien.prescription.show', $prescription);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur lors du démarrage de l\'analyse', [
                'prescription_id' => $prescriptionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Erreur lors du démarrage de l\'analyse : ' . $e->getMessage());
        }
    }

    /**
     * ✅ NOUVEAU : Terminer l'analyse (EN_COURS → TERMINE)
     */
    public function completeAnalysis(int $prescriptionId)
    {
        try {
            DB::beginTransaction();

            $prescription = Prescription::findOrFail($prescriptionId);

            // Vérifier que l'analyse est en cours
            if ($prescription->status !== Prescription::STATUS_EN_COURS) {
                throw new \Exception('Cette analyse doit être en cours pour être terminée');
            }

            // Vérifier que tous les résultats sont saisis
            $totalAnalyses = $prescription->analyses()->count();
            $resultatsComplete = $prescription->resultats()
                ->whereNotNull('valeur')
                ->where('valeur', '!=', '')
                ->count();

            if ($resultatsComplete < $totalAnalyses) {
                throw new \Exception('Veuillez compléter tous les résultats avant de terminer l\'analyse');
            }

            // Changer le statut à TERMINE
            $prescription->update([
                'status' => Prescription::STATUS_TERMINE,
                'completed_by' => Auth::id(),
                'completed_at' => now()
            ]);

            // Mettre à jour les résultats
            $prescription->resultats()->update([
                'status' => 'COMPLETE',
                'completed_by' => Auth::id(),
                'completed_at' => now()
            ]);

            DB::commit();

            Log::info('Prescription terminée par technicien', [
                'prescription_id' => $prescriptionId,
                'reference' => $prescription->reference,
                'technicien_id' => Auth::id(),
            ]);

            session()->flash('message', 'Analyse terminée avec succès ! Elle est maintenant prête pour validation par le biologiste.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur lors de la finalisation de l\'analyse', [
                'prescription_id' => $prescriptionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Erreur lors de la finalisation : ' . $e->getMessage());
        }
    }

    /**
     * ✅ NOUVEAU : Continuer le traitement d'une analyse en cours
     */
    public function continueAnalysis(int $prescriptionId)
    {
        try {
            $prescription = Prescription::findOrFail($prescriptionId);

            // Redirection vers la page de traitement
            return redirect()->route('technicien.prescription.show', $prescription);

        } catch (\Exception $e) {
            session()->flash('error', 'Impossible d\'accéder à cette analyse');
        }
    }

    public function render()
    {
        $prescriptions = Prescription::query()
            ->with([
                'patient:id,nom,prenom,telephone',
                'prescripteur:id,nom,prenom',
                'analyses:id,designation',
                'resultats'
            ])
            ->whereIn('status', [
                Prescription::STATUS_EN_ATTENTE,
                Prescription::STATUS_EN_COURS,
                Prescription::STATUS_TERMINE,
                Prescription::STATUS_A_REFAIRE
            ])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('reference', 'like', '%' . $this->search . '%')
                        ->orWhereHas('patient', function ($sq) {
                            $sq->where('nom', 'like', '%' . $this->search . '%')
                                ->orWhere('prenom', 'like', '%' . $this->search . '%')
                                ->orWhere('telephone', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('prescripteur', function ($sq) {
                            $sq->where('nom', 'like', '%' . $this->search . '%')
                                ->orWhere('prenom', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        // ✅ Stats en temps réel améliorées
        $stats = [
            'en_attente' => Prescription::where('status', Prescription::STATUS_EN_ATTENTE)->count(),
            'en_cours' => Prescription::where('status', Prescription::STATUS_EN_COURS)->count(),
            'termine' => Prescription::where('status', Prescription::STATUS_TERMINE)->count(),
            'a_refaire' => Prescription::where('status', Prescription::STATUS_A_REFAIRE)->count(),
        ];

        return view('livewire.technicien.index-technicien', [
            'prescriptions' => $prescriptions,
            'stats' => $stats,
        ]);
    }
}