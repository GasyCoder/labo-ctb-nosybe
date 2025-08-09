<?php

namespace App\Livewire;

use App\Models\Prescription;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class Archives extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $dateFilter = '';
    public $prescripteurFilter = '';
    public $countArchive; // Add property for archive count

    // Messages de confirmation
    public $showUnarchiveModal = false;
    public $showDeleteModal = false;
    public $selectedPrescriptionId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'dateFilter' => ['except' => ''],
        'prescripteurFilter' => ['except' => ''],
    ];

    public function mount()
    {
        $this->refreshArchiveCount(); // Initialize count on mount
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    public function updatingPrescripteurFilter()
    {
        $this->resetPage();
    }

    public function refreshArchiveCount()
    {
        $this->countArchive = Prescription::where('status', Prescription::STATUS_ARCHIVE)->count();
        $this->dispatch('updateArchiveCount', count: $this->countArchive); // Dispatch event
    }

    public function getArchivedPrescriptions()
    {
        return Prescription::with(['patient', 'prescripteur', 'analyses'])
            ->archivees()
            ->when($this->search, function (Builder $query) {
                $query->whereHas('patient', function (Builder $subQuery) {
                    $subQuery->where('nom', 'like', '%' . $this->search . '%')
                        ->orWhere('prenom', 'like', '%' . $this->search . '%')
                        ->orWhere('reference', 'like', '%' . $this->search . '%')
                        ->orWhere('telephone', 'like', '%' . $this->search . '%');
                })
                    ->orWhereHas('prescripteur', function (Builder $subQuery) {
                        $subQuery->where('nom', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->prescripteurFilter, function (Builder $query) {
                $query->where('prescripteur_id', $this->prescripteurFilter);
            })
            ->when($this->dateFilter, function (Builder $query) {
                switch ($this->dateFilter) {
                    case 'today':
                        $query->whereDate('created_at', today());
                        break;
                    case 'week':
                        $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'month':
                        $query->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year);
                        break;
                    case 'year':
                        $query->whereYear('created_at', now()->year);
                        break;
                }
            })
            ->latest('updated_at')
            ->paginate(15);
    }

    public function getPrescripteurs()
    {
        return \App\Models\User::where('type', 'prescripteur')
            ->whereHas('prescriptions', function (Builder $query) {
                $query->archivees();
            })
            ->get(['id', 'name']);
    }

    public function confirmUnarchive($prescriptionId)
    {
        $this->selectedPrescriptionId = $prescriptionId;
        $this->showUnarchiveModal = true;
    }

    public function unarchive()
    {
        if (!$this->selectedPrescriptionId) {
            return;
        }

        try {
            $prescription = Prescription::findOrFail($this->selectedPrescriptionId);

            // Vérifier les permissions
            if (!$this->canUnarchive($prescription)) {
                session()->flash('error', 'Vous n\'avez pas l\'autorisation de désarchiver cette prescription.');
                return;
            }

            $prescription->unarchive();
            session()->flash('success', 'La prescription a été désarchivée avec succès.');
            $this->refreshArchiveCount(); // Refresh count after unarchiving

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la désarchivage : ' . $e->getMessage());
        }

        $this->resetModal();
    }

    public function confirmPermanentDelete($prescriptionId)
    {
        if (!Auth::user()->isAdmin()) {
            session()->flash('error', 'Seuls les administrateurs peuvent supprimer définitivement.');
            return;
        }

        $this->selectedPrescriptionId = $prescriptionId;
        $this->showDeleteModal = true;
    }

    public function permanentDelete()
    {
        if (!Auth::user()->isAdmin()) {
            session()->flash('error', 'Action non autorisée.');
            return;
        }

        if (!$this->selectedPrescriptionId) {
            return;
        }

        try {
            $prescription = Prescription::findOrFail($this->selectedPrescriptionId);
            $prescription->forceDelete();
            session()->flash('success', 'La prescription a été supprimée définitivement.');
            $this->refreshArchiveCount(); // Refresh count after deletion

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }

        $this->resetModal();
    }

    private function canUnarchive($prescription)
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->type === 'secretaire') {
            return true;
        }

        if ($user->type === 'biologiste' && $prescription->prescripteur_id === $user->id) {
            return true;
        }

        return false;
    }

    public function resetModal()
    {
        $this->showUnarchiveModal = false;
        $this->showDeleteModal = false;
        $this->selectedPrescriptionId = null;
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->dateFilter = '';
        $this->prescripteurFilter = '';
        $this->resetPage();
    }

    public function export()
    {
        session()->flash('info', 'Fonctionnalité d\'export en cours de développement.');
    }

    public function render()
    {
        $prescriptions = $this->getArchivedPrescriptions();
        $prescripteurs = $this->getPrescripteurs();

        return view('livewire.archives', [
            'prescriptions' => $prescriptions,
            'prescripteurs' => $prescripteurs,
            'countArchive' => $this->countArchive, // Pass count to view
        ]);
    }
}