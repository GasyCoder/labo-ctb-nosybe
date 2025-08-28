<?php

namespace App\Livewire\Secretaire\Prescription;

use App\Models\Patient;
use Livewire\Component;
use App\Models\Prescription;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\ResultatPdfShow;
use Illuminate\Support\Facades\DB;
use App\Models\AnalysePrescription;
use Illuminate\Support\Facades\Log;
use App\Services\ResultatPdfService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PrescriptionIndex extends Component
{
    use WithPagination, AuthorizesRequests;

    public ?Prescription $prescription = null;
    public $countArchive; // Add property for archive count

    protected $queryString = [
        'search' => ['except' => ''],
        'tab' => ['except' => 'actives'],
    ];

    public $tab = 'actives';
    public $search = '';

    // Modal properties
    public $showDeleteModal = false;
    public $showRestoreModal = false;
    public $showPermanentDeleteModal = false;
    public $showArchiveModal = false;
    public $showUnarchiveModal = false;
    public $selectedPrescriptionId = null;

    protected $pdfService;

    public const STATUS_LABELS = [
        'EN_ATTENTE' => 'En attente',
        'EN_COURS' => 'En cours',
        'TERMINE' => 'Terminé',
        'VALIDE' => 'Validé',
        'A_REFAIRE' => 'À refaire',
        'ARCHIVE' => 'Archivé',
    ];

    protected $listeners = [
        'prescriptionAdded' => '$refresh',
    ];

    public function mount()
    {
        $this->tab = request()->query('tab', 'actives');
        $this->refreshArchiveCount(); // Initialize count on mount
    }

    public function switchTab($tab)
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function clearSearch()
    {
        $this->search = '';
        $this->resetPage();
    }

    public function refreshArchiveCount()
    {
        $this->countArchive = Prescription::where('status', Prescription::STATUS_ARCHIVE)->count();
        $this->dispatch('updateArchiveCount', count: $this->countArchive); // Dispatch event
    }

    public function resetModal()
    {
        $this->showDeleteModal = false;
        $this->showRestoreModal = false;
        $this->showPermanentDeleteModal = false;
        $this->showArchiveModal = false;
        $this->showUnarchiveModal = false;
        $this->selectedPrescriptionId = null;
    }

    private function addStatusLabels($prescriptions)
    {
        $prescriptions->getCollection()->transform(function ($prescription) {
            $prescription->status_label = self::STATUS_LABELS[$prescription->status] ?? $prescription->status;
            return $prescription;
        });
    }

    public function edit($prescriptionId)
    {
        $this->dispatch('editPrescription', $prescriptionId);
    }

    // Confirmation Methods
    public function confirmDelete($prescriptionId)
    {
        $this->selectedPrescriptionId = $prescriptionId;
        $this->showDeleteModal = true;
    }

    public function confirmRestore($prescriptionId)
    {
        $this->selectedPrescriptionId = $prescriptionId;
        $this->showRestoreModal = true;
    }

    public function confirmPermanentDelete($prescriptionId)
    {
        if (!auth()->user()->isAdmin()) {
            session()->flash('error', 'Seuls les administrateurs peuvent supprimer définitivement.');
            return;
        }

        $this->selectedPrescriptionId = $prescriptionId;
        $this->showPermanentDeleteModal = true;
    }

    public function confirmArchive($prescriptionId)
    {
        $this->selectedPrescriptionId = $prescriptionId;
        $this->showArchiveModal = true;
    }

    public function confirmUnarchive($prescriptionId)
    {
        $this->selectedPrescriptionId = $prescriptionId;
        $this->showUnarchiveModal = true;
    }

    // Execution Methods
    public function deletePrescription()
    {
        try {
            if (!$this->selectedPrescriptionId) {
                return;
            }

            $prescription = Prescription::findOrFail($this->selectedPrescriptionId);
            $prescription->delete();

            session()->flash('success', 'Prescription mise en corbeille avec succès.');
            $this->resetModal();
            $this->dispatch('$refresh');

        } catch (\Exception $e) {
            Log::error('Erreur suppression prescription', [
                'prescription_id' => $this->selectedPrescriptionId,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Une erreur est survenue lors de la mise en corbeille.');
            $this->resetModal();
        }
    }

    public function restorePrescription()
    {
        try {
            if (!$this->selectedPrescriptionId) {
                return;
            }

            $prescription = Prescription::withTrashed()->findOrFail($this->selectedPrescriptionId);
            $prescription->restore();

            session()->flash('success', 'Prescription restaurée avec succès.');
            $this->resetModal();
            $this->dispatch('$refresh');

        } catch (\Exception $e) {
            Log::error('Erreur restauration prescription', [
                'prescription_id' => $this->selectedPrescriptionId,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Erreur lors de la restauration : ' . $e->getMessage());
            $this->resetModal();
        }
    }

    public function permanentDeletePrescription()
    {
        try {
            if (!auth()->user()->isAdmin()) {
                session()->flash('error', 'Action non autorisée.');
                return;
            }

            if (!$this->selectedPrescriptionId) {
                return;
            }

            $prescription = Prescription::withTrashed()->findOrFail($this->selectedPrescriptionId);
            $prescription->forceDelete();

            session()->flash('success', 'Prescription supprimée définitivement.');
            $this->resetModal();
            $this->dispatch('$refresh');
            $this->refreshArchiveCount(); // Refresh count after deletion

        } catch (\Exception $e) {
            Log::error('Erreur suppression définitive prescription', [
                'prescription_id' => $this->selectedPrescriptionId,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Erreur lors de la suppression définitive : ' . $e->getMessage());
            $this->resetModal();
        }
    }

    public function archivePrescription()
    {
        try {
            if (!$this->selectedPrescriptionId) {
                return;
            }

            $prescription = Prescription::findOrFail($this->selectedPrescriptionId);

            if ($prescription->status === 'VALIDE') {
                $prescription->update(['status' => 'ARCHIVE']);
                session()->flash('success', 'Prescription archivée avec succès.');
                $this->refreshArchiveCount(); // Refresh count after archiving
            } else {
                session()->flash('error', 'Seules les prescriptions validées peuvent être archivées.');
            }

            $this->resetModal();
            $this->dispatch('$refresh');

        } catch (\Exception $e) {
            Log::error('Erreur archivage prescription', [
                'prescription_id' => $this->selectedPrescriptionId,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Erreur lors de l\'archivage : ' . $e->getMessage());
            $this->resetModal();
        }
    }

    public function unarchivePrescription()
    {
        try {
            if (!$this->selectedPrescriptionId) {
                return;
            }

            $prescription = Prescription::findOrFail($this->selectedPrescriptionId);
            $prescription->update(['status' => 'VALIDE']);
            session()->flash('success', 'Prescription désarchivée avec succès.');
            $this->refreshArchiveCount(); // Refresh count after unarchiving

            $this->resetModal();
            $this->dispatch('$refresh');

        } catch (\Exception $e) {
            Log::error('Erreur désarchivage prescription', [
                'prescription_id' => $this->selectedPrescriptionId,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Erreur lors du désarchivage : ' . $e->getMessage());
            $this->resetModal();
        }
    }

    public function generateResultatsPDF($prescriptionId)
    {
        try {
            $prescription = Prescription::findOrFail($prescriptionId);
            return $this->pdfService->generatePDF($prescription);

        } catch (\Exception $e) {
            Log::error('Erreur génération PDF:', [
                'message' => $e->getMessage(),
                'prescription_id' => $prescriptionId,
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', "Erreur lors de la génération du PDF : {$e->getMessage()}");
            return null;
        }
    }

    public function render()
    {
        $search = '%' . $this->search . '%';

        $baseQuery = Prescription::with([
            'patient:id,nom,prenom,telephone',
            'prescripteur:id,nom',
            'analyses',
            'resultats'
        ])
            ->whereHas('patient', fn($q) => $q->whereNull('deleted_at'));

        $searchCondition = function ($query) use ($search) {
            $query->where('renseignement_clinique', 'like', $search)
                ->orWhere('status', 'like', $search)
                ->orWhere('reference', 'like', $search)
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

        $activePrescriptions = (clone $baseQuery)
            ->whereIn('status', ['EN_ATTENTE', 'EN_COURS', 'TERMINE'])
            ->where($searchCondition)
            ->latest()
            ->paginate(15);

        $validePrescriptions = (clone $baseQuery)
            ->where('status', 'VALIDE')
            ->where($searchCondition)
            ->latest()
            ->paginate(15, ['*'], 'valide_page');

        $deletedPrescriptions = (clone $baseQuery)
            ->onlyTrashed()
            ->where($searchCondition)
            ->latest()
            ->paginate(15, ['*'], 'deleted_page');

        $this->addStatusLabels($activePrescriptions);
        $this->addStatusLabels($validePrescriptions);
        $this->addStatusLabels($deletedPrescriptions);

        return view('livewire.secretaire.prescription.prescription-index', [
            'activePrescriptions' => $activePrescriptions,
            'validePrescriptions' => $validePrescriptions,
            'deletedPrescriptions' => $deletedPrescriptions,
            'countArchive' => $this->countArchive, // Pass count to view
        ]);
    }
}