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
    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'tab' => ['except' => 'actives'], // Onglet par défaut
    ];

    public $tab = 'actives';
    public $search = '';
    
    protected $pdfService;

    // Define status labels for display
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
        'deletePrescription',
        'restorePrescription',
        'permanentDeletePrescription',
        'archivePrescription',
        'unarchivePrescription',
    ];

    public function mount()
    {
        $this->tab = request()->query('tab', 'actives');
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

    // ========================================
    // MÉTHODES DE CONFIRMATION SWEETALERT2
    // ========================================

    public function confirmDelete($prescriptionId)
    {
        $this->dispatch('swal:confirm', [
            'type' => 'warning',
            'message' => 'Êtes-vous sûr de vouloir mettre cette prescription en corbeille ?',
            'text' => 'Cette action peut être annulée depuis la corbeille.',
            'confirmButtonText' => 'Oui, supprimer',
            'method' => 'deletePrescription',
            'params' => $prescriptionId
        ]);
    }

    public function confirmRestore($prescriptionId)
    {
        $this->dispatch('swal:confirm', [
            'type' => 'question',
            'message' => 'Êtes-vous sûr de vouloir restaurer cette prescription ?',
            'text' => 'Elle sera remise dans la liste active.',
            'confirmButtonText' => 'Oui, restaurer',
            'method' => 'restorePrescription',
            'params' => $prescriptionId
        ]);
    }

    public function confirmPermanentDelete($prescriptionId)
    {
        $this->dispatch('swal:confirm', [
            'type' => 'error',
            'message' => 'Êtes-vous sûr de vouloir supprimer définitivement cette prescription ?',
            'text' => 'Cette action est irréversible !',
            'confirmButtonText' => 'Oui, supprimer définitivement',
            'method' => 'permanentDeletePrescription',
            'params' => $prescriptionId
        ]);
    }

    public function confirmArchive($prescriptionId)
    {
        $this->dispatch('swal:confirm', [
            'type' => 'info',
            'message' => 'Voulez-vous archiver cette prescription ?',
            'text' => 'Elle sera déplacée vers les archives.',
            'confirmButtonText' => 'Oui, archiver',
            'method' => 'archivePrescription',
            'params' => $prescriptionId
        ]);
    }

    public function confirmUnarchive($prescriptionId)
    {
        $this->dispatch('swal:confirm', [
            'type' => 'info',
            'message' => 'Voulez-vous désarchiver cette prescription ?',
            'text' => 'Elle sera remise dans la liste validées.',
            'confirmButtonText' => 'Oui, désarchiver',
            'method' => 'unarchivePrescription',
            'params' => $prescriptionId
        ]);
    }

    // ========================================
    // MÉTHODES D'EXÉCUTION
    // ========================================

    public function deletePrescription($prescriptionId = null)
    {
        try {
            $prescription = Prescription::findOrFail($prescriptionId);
            $prescription->delete();

            session()->flash('success', 'Prescription mise en corbeille avec succès.');
            $this->dispatch('$refresh');

        } catch (\Exception $e) {
            Log::error('Erreur suppression prescription', [
                'prescription_id' => $prescriptionId,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Une erreur est survenue lors de la mise en corbeille.');
        }
    }

    public function restorePrescription($prescriptionId = null)
    {
        try {
            $prescription = Prescription::withTrashed()->findOrFail($prescriptionId);
            $prescription->restore();

            session()->flash('success', 'Prescription restaurée avec succès.');
            $this->dispatch('$refresh');

        } catch (\Exception $e) {
            Log::error('Erreur restauration prescription', [
                'prescription_id' => $prescriptionId,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Erreur lors de la restauration : ' . $e->getMessage());
        }
    }

    public function permanentDeletePrescription($prescriptionId = null)
    {
        try {
            $prescription = Prescription::withTrashed()->findOrFail($prescriptionId);
            $prescription->forceDelete();

            session()->flash('success', 'Prescription supprimée définitivement.');
            $this->dispatch('$refresh');

        } catch (\Exception $e) {
            Log::error('Erreur suppression définitive prescription', [
                'prescription_id' => $prescriptionId,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Erreur lors de la suppression définitive : ' . $e->getMessage());
        }
    }

    public function archivePrescription($prescriptionId = null)
    {
        try {
            $prescription = Prescription::findOrFail($prescriptionId);

            if ($prescription->status === 'VALIDE') {
                $prescription->update(['status' => 'ARCHIVE']);
                session()->flash('success', 'Prescription archivée avec succès.');
                $this->dispatch('$refresh');
            } else {
                session()->flash('error', 'Seules les prescriptions validées peuvent être archivées.');
            }

        } catch (\Exception $e) {
            Log::error('Erreur archivage prescription', [
                'prescription_id' => $prescriptionId,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Erreur lors de l\'archivage : ' . $e->getMessage());
        }
    }

    public function unarchivePrescription($prescriptionId = null)
    {
        try {
            $prescription = Prescription::findOrFail($prescriptionId);
            $prescription->update(['status' => 'VALIDE']);
            
            session()->flash('success', 'Prescription désarchivée avec succès.');
            $this->dispatch('$refresh');
            
        } catch (\Exception $e) {
            Log::error('Erreur désarchivage prescription', [
                'prescription_id' => $prescriptionId,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Erreur lors du désarchivage : ' . $e->getMessage());
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
                ->orWhere('reference', 'like', $search) // ✅ Recherche dans prescriptions.reference
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

        // Prescriptions actives (EN_ATTENTE, EN_COURS, TERMINE)
        $activePrescriptions = (clone $baseQuery)
            ->whereIn('status', ['EN_ATTENTE', 'EN_COURS', 'TERMINE'])
            ->where($searchCondition)
            ->latest()
            ->paginate(15);

        // Prescriptions validées (VALIDE seulement, ARCHIVE va vers la page archives)
        $validePrescriptions = (clone $baseQuery)
            ->where('status', 'VALIDE')
            ->where($searchCondition)
            ->latest()
            ->paginate(15, ['*'], 'valide_page');

        // Prescriptions supprimées
        $deletedPrescriptions = (clone $baseQuery)
            ->onlyTrashed()
            ->where($searchCondition)
            ->latest()
            ->paginate(15, ['*'], 'deleted_page');

        // Add status labels to all prescription collections
        $this->addStatusLabels($activePrescriptions);
        $this->addStatusLabels($validePrescriptions);
        $this->addStatusLabels($deletedPrescriptions);

        return view('livewire.secretaire.prescription.prescription-index', compact(
            'activePrescriptions',
            'validePrescriptions',
            'deletedPrescriptions',
        ));
    }
}