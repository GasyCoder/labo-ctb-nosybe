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
    public $prescriptionId;
    protected $pdfService;

    // Define status labels for display
    public const STATUS_LABELS = [
        'EN_ATTENTE' => 'En attente',
        'EN_COURS' => 'En cours',
        'TERMINE' => 'Terminé',
        'VALIDE' => 'Validé',
        'A_REFAIRE' => 'À refaire',
        'ARCHIVE' => 'Archivé',
        'PRELEVEMENTS_GENERES' => 'Prélèvements générés',
    ];

    protected $listeners = [
        'prescriptionAdded' => '$refresh',
        'deleteConfirmed' => 'deletePrescription',
        'restorePrescription',
        'permanentDeletePrescription',
        'archivePrescription',
    ];

    public $search = '';

    public function mount()
    {
        $this->tab = request()->query('tab', 'actives'); // Par défaut : 'actives'
    }

    public function switchTab($tab)
    {
        $this->tab = $tab;
        $this->resetPage(); // Réinitialiser la pagination pour chaque onglet
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

    public function render()
    {
        $search = '%' . $this->search . '%';

        $baseQuery = Prescription::with([
            'patient:id,reference,nom,prenom,telephone',
            'prescripteur:id,nom',
            'analyses',
            'resultats'
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

        // Prescriptions actives (non archivées et non validées complètement)
        $activePrescriptions = (clone $baseQuery)
            ->whereNotIn('status', ['ARCHIVE'])
            ->where($searchCondition)
            ->latest()
            ->paginate(15);

        // Analyses validées (toutes les analyses ont des résultats validés)
        $analyseValides = (clone $baseQuery)
            ->where('status', 'VALIDE')
            ->whereDoesntHave('analyses', function ($q) {
                $q->whereDoesntHave('resultats', fn($q) => $q->whereNotNull('validated_by'));
            })
            ->where($searchCondition)
            ->latest()
            ->paginate(15, ['*'], 'valide_page');

        // Prescriptions archivées
        $archivedPrescriptions = (clone $baseQuery)
            ->where('status', 'ARCHIVE')
            ->where($searchCondition)
            ->latest()
            ->paginate(15, ['*'], 'archive_page');

        // Prescriptions supprimées
        $deletedPrescriptions = (clone $baseQuery)
            ->onlyTrashed()
            ->where($searchCondition)
            ->latest()
            ->paginate(15, ['*'], 'deleted_page');

        // Add status labels to all prescription collections
        $this->addStatusLabels($activePrescriptions);
        $this->addStatusLabels($analyseValides);
        $this->addStatusLabels($archivedPrescriptions);
        $this->addStatusLabels($deletedPrescriptions);

        return view('livewire.secretaire.prescription.prescription-index', compact(
            'activePrescriptions',
            'analyseValides',
            'archivedPrescriptions',
            'deletedPrescriptions',
        ));
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

    public function confirmDelete($prescriptionId)
    {
        $this->prescriptionId = $prescriptionId;
        $this->confirm('Êtes-vous sûr de vouloir mettre cette prescription en corbeille ?', [
            'toast' => true,
            'position' => 'center',
            'showConfirmButton' => true,
            'confirmButtonText' => 'Oui, corbeille',
            'cancelButtonText' => 'Annuler',
            'onConfirmed' => 'deleteConfirmed',
        ]);
    }

    public function deletePrescription($prescriptionId = null)
    {
        try {
            $id = $prescriptionId ?? $this->prescriptionId;
            $prescription = Prescription::findOrFail($id);
            $prescription->delete();

            session()->flash('success', 'Prescription mise en corbeille avec succès.');
            $this->dispatch('prescriptionDeleted');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise en corbeille de la prescription: ' . $e->getMessage());
            session()->flash('error', 'Une erreur est survenue lors de la mise en corbeille.');
        }

        $this->prescriptionId = null;
    }

    public function confirmRestore($prescriptionId)
    {
        $this->prescriptionId = $prescriptionId;
        $this->confirm('Êtes-vous sûr de vouloir restaurer cette prescription ?', [
            'toast' => true,
            'position' => 'center',
            'confirmButtonText' => 'Oui, restaurer',
            'onConfirmed' => 'restorePrescription',
            'onCancelled' => 'cancelled'
        ]);
    }

    public function restorePrescription($prescriptionId = null)
    {
        try {
            $id = $prescriptionId ?? $this->prescriptionId;
            $prescription = Prescription::withTrashed()->findOrFail($id);
            $prescription->restore();

            session()->flash('success', 'Prescription restaurée avec succès.');
            $this->dispatch('prescriptionRestored');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la restauration : ' . $e->getMessage());
        }
    }

    public function confirmPermanentDelete($prescriptionId)
    {
        $this->prescriptionId = $prescriptionId;
        $this->confirm('Êtes-vous sûr de vouloir supprimer définitivement cette prescription ?', [
            'toast' => true,
            'position' => 'center',
            'onConfirmed' => 'permanentDeletePrescription',
            'onCancelled' => 'cancelled'
        ]);
    }

    public function forceDeletePrescription($prescriptionId)
    {
        try {
            $prescription = Prescription::withTrashed()->findOrFail($prescriptionId);
            $prescription->forceDelete();

            session()->flash('success', 'Prescription supprimée définitivement.');
            $this->dispatch('prescriptionPermanentlyDeleted');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression définitive : ' . $e->getMessage());
        }
    }

    public function permanentDeletePrescription()
    {
        try {
            $prescription = Prescription::withTrashed()->findOrFail($this->prescriptionId);
            $prescription->forceDelete();

            session()->flash('success', 'Prescription supprimée définitivement.');
            $this->dispatch('prescriptionPermanentlyDeleted');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression définitive : ' . $e->getMessage());
        }
    }

    public function cancelled()
    {
        session()->flash('info', 'Action annulée');
    }

    public function confirmArchive($prescriptionId)
    {
        $this->prescriptionId = $prescriptionId;
        $this->confirm('Voulez-vous archiver cette prescription ?', [
            'toast' => true,
            'position' => 'center',
            'showConfirmButton' => true,
            'confirmButtonText' => 'Oui, archiver',
            'cancelButtonText' => 'Annuler',
            'onConfirmed' => 'archivePrescription'
        ]);
    }

    public function archivePrescription()
    {
        try {
            $prescription = Prescription::findOrFail($this->prescriptionId);

            if ($prescription->status === 'VALIDE') {
                $prescription->update(['status' => 'ARCHIVE']);
                session()->flash('success', 'Prescription archivée avec succès.');
            } else {
                session()->flash('error', 'Seules les prescriptions validées peuvent être archivées.');
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'archivage : ' . $e->getMessage());
        }
    }

    public function confirmUnarchive($prescriptionId)
    {
        $this->prescriptionId = $prescriptionId;
        $this->confirm('Voulez-vous désarchiver cette prescription ?', [
            'toast' => true,
            'position' => 'center',
            'showConfirmButton' => true,
            'confirmButtonText' => 'Oui, désarchiver',
            'cancelButtonText' => 'Annuler',
            'onConfirmed' => 'unarchivePrescription'
        ]);
    }

    public function unarchivePrescription()
    {
        try {
            $prescription = Prescription::findOrFail($this->prescriptionId);
            $prescription->update(['status' => 'VALIDE']);
            session()->flash('success', 'Prescription désarchivée avec succès.');
        } catch (\Exception $e) {
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
}