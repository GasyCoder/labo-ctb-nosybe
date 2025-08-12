<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Patient;
use App\Models\Prescription;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class TracePatient extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $activeTab = 'patients'; // 'patients' ou 'prescriptions'

    // Sélections
    public $selectedPatients = [];
    public $selectedPrescriptions = [];
    public $selectAllPatients = false;
    public $selectAllPrescriptions = false;

    // Confirmations de suppression
    public $confirmingForceDeletePatient = false;
    public $confirmingForceDeletePrescription = false;
    public $patientToDelete;
    public $prescriptionToDelete;

    // Confirmations de vidage
    public $confirmingEmptyPatientsTrash = false;
    public $confirmingEmptyPrescriptionsTrash = false;

    protected $listeners = [
        'refreshTrash' => '$refresh',
        'updateTraceCount' => 'updateCount'
    ];

    public function mount()
    {
        $this->activeTab = 'prescriptions';
    }

    public function updateCount()
    {
        $this->dispatch('updateTraceCount', [
            'patients' => Patient::onlyTrashed()->count(),
            'prescriptions' => Prescription::onlyTrashed()->count()
        ]);
    }

    public function render()
    {
        // Patients supprimés
        $patients = Patient::onlyTrashed()
            ->withCount('prescriptions')
            ->when($this->search, function ($query) {
                $query->where('numero_dossier', 'like', '%' . $this->search . '%')
                    ->orWhere('nom', 'like', '%' . $this->search . '%')
                    ->orWhere('prenom', 'like', '%' . $this->search . '%')
                    ->orWhere('telephone', 'like', '%' . $this->search . '%');
            })
            ->orderBy('deleted_at', 'desc')
            ->paginate($this->perPage, ['*'], 'patients_page');

        // Prescriptions supprimées
        $prescriptions = Prescription::onlyTrashed()
            ->with(['patient', 'prescripteur'])
            ->when($this->search, function ($query) {
                $query->where('reference', 'like', '%' . $this->search . '%')
                    ->orWhereHas('patient', function ($q) {
                        $q->where('nom', 'like', '%' . $this->search . '%')
                            ->orWhere('prenom', 'like', '%' . $this->search . '%')
                            ->orWhere('numero_dossier', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('prescripteur', function ($q) {
                        $q->where('nom', 'like', '%' . $this->search . '%');
                    });
            })
            ->orderBy('deleted_at', 'desc')
            ->paginate($this->perPage, ['*'], 'prescriptions_page');

        // Statistiques patients
        $patientsCount = Patient::onlyTrashed()->count();
        $patientsRecentCount = Patient::onlyTrashed()
            ->where('deleted_at', '>=', now()->subDays(7))
            ->count();
        $patientsOldCount = Patient::onlyTrashed()
            ->where('deleted_at', '<', now()->subDays(30))
            ->count();
        $patientsWithPrescriptionsCount = Patient::onlyTrashed()
            ->has('prescriptions')
            ->count();

        // Statistiques prescriptions
        $prescriptionsCount = Prescription::onlyTrashed()->count();
        $prescriptionsRecentCount = Prescription::onlyTrashed()
            ->where('deleted_at', '>=', now()->subDays(7))
            ->count();
        $prescriptionsOldCount = Prescription::onlyTrashed()
            ->where('deleted_at', '<', now()->subDays(30))
            ->count();

        // Calculer la valeur totale des prescriptions supprimées
        $prescriptionsTotalValue = Prescription::onlyTrashed()
            ->with(['analyses', 'prelevements'])
            ->get()
            ->sum('montant_total');

        $totalCount = $patientsCount + $prescriptionsCount;

        return view('livewire.admin.trace-patient', [
            'patients' => $patients,
            'prescriptions' => $prescriptions,
            'totalCount' => $totalCount,

            // Statistiques patients
            'patientsCount' => $patientsCount,
            'patientsRecentCount' => $patientsRecentCount,
            'patientsOldCount' => $patientsOldCount,
            'patientsWithPrescriptionsCount' => $patientsWithPrescriptionsCount,

            // Statistiques prescriptions
            'prescriptionsCount' => $prescriptionsCount,
            'prescriptionsRecentCount' => $prescriptionsRecentCount,
            'prescriptionsOldCount' => $prescriptionsOldCount,
            'prescriptionsTotalValue' => $prescriptionsTotalValue,
        ]);
    }

    // ===== MÉTHODES PATIENTS =====

    public function restorePatient($id)
    {
        $patient = Patient::onlyTrashed()->findOrFail($id);
        $patient->restore();

        $this->dispatch('refreshTrash');
        $this->updateCount();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Patient restauré avec succès!'
        ]);
    }

    public function confirmForceDeletePatient($id)
    {
        $this->patientToDelete = Patient::onlyTrashed()
            ->withCount('prescriptions')
            ->findOrFail($id);
        $this->confirmingForceDeletePatient = true;
    }

    public function forceDeletePatient()
    {
        if ($this->patientToDelete) {
            // Supprimer aussi toutes les prescriptions associées
            $this->patientToDelete->prescriptions()->forceDelete();
            $this->patientToDelete->forceDelete();

            $this->confirmingForceDeletePatient = false;
            $this->patientToDelete = null;
            $this->dispatch('refreshTrash');
            $this->updateCount();
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Patient définitivement supprimé!'
            ]);
        }
    }

    public function restoreSelectedPatients()
    {
        Patient::onlyTrashed()->whereIn('id', $this->selectedPatients)->restore();
        $this->selectedPatients = [];
        $this->selectAllPatients = false;
        $this->dispatch('refreshTrash');
        $this->updateCount();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Patients sélectionnés restaurés!'
        ]);
    }

    public function deleteSelectedPatients()
    {
        $patients = Patient::onlyTrashed()->whereIn('id', $this->selectedPatients)->get();
        foreach ($patients as $patient) {
            $patient->prescriptions()->forceDelete();
            $patient->forceDelete();
        }

        $this->selectedPatients = [];
        $this->selectAllPatients = false;
        $this->dispatch('refreshTrash');
        $this->updateCount();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Patients sélectionnés définitivement supprimés!'
        ]);
    }

    public function confirmEmptyPatientsTrash()
    {
        $this->confirmingEmptyPatientsTrash = true;
    }

    public function emptyPatientsTrash()
    {
        // Supprimer toutes les prescriptions des patients supprimés
        $patients = Patient::onlyTrashed()->get();
        foreach ($patients as $patient) {
            $patient->prescriptions()->forceDelete();
        }

        // Supprimer tous les patients
        Patient::onlyTrashed()->forceDelete();

        $this->confirmingEmptyPatientsTrash = false;
        $this->selectedPatients = [];
        $this->selectAllPatients = false;
        $this->dispatch('refreshTrash');
        $this->updateCount();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Corbeille des patients vidée avec succès!'
        ]);
    }

    // ===== MÉTHODES PRESCRIPTIONS =====

    public function restorePrescription($id)
    {
        $prescription = Prescription::onlyTrashed()->findOrFail($id);
        $prescription->restore();

        $this->dispatch('refreshTrash');
        $this->updateCount();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Prescription restaurée avec succès!'
        ]);
    }

    public function confirmForceDeletePrescription($id)
    {
        $this->prescriptionToDelete = Prescription::onlyTrashed()
            ->with(['patient', 'prescripteur'])
            ->findOrFail($id);
        $this->confirmingForceDeletePrescription = true;
    }

    public function forceDeletePrescription()
    {
        if ($this->prescriptionToDelete) {
            $this->prescriptionToDelete->forceDelete();

            $this->confirmingForceDeletePrescription = false;
            $this->prescriptionToDelete = null;
            $this->dispatch('refreshTrash');
            $this->updateCount();
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Prescription définitivement supprimée!'
            ]);
        }
    }

    public function restoreSelectedPrescriptions()
    {
        Prescription::onlyTrashed()->whereIn('id', $this->selectedPrescriptions)->restore();
        $this->selectedPrescriptions = [];
        $this->selectAllPrescriptions = false;
        $this->dispatch('refreshTrash');
        $this->updateCount();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Prescriptions sélectionnées restaurées!'
        ]);
    }

    public function deleteSelectedPrescriptions()
    {
        Prescription::onlyTrashed()->whereIn('id', $this->selectedPrescriptions)->forceDelete();
        $this->selectedPrescriptions = [];
        $this->selectAllPrescriptions = false;
        $this->dispatch('refreshTrash');
        $this->updateCount();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Prescriptions sélectionnées définitivement supprimées!'
        ]);
    }

    public function confirmEmptyPrescriptionsTrash()
    {
        $this->confirmingEmptyPrescriptionsTrash = true;
    }

    public function emptyPrescriptionsTrash()
    {
        Prescription::onlyTrashed()->forceDelete();

        $this->confirmingEmptyPrescriptionsTrash = false;
        $this->selectedPrescriptions = [];
        $this->selectAllPrescriptions = false;
        $this->dispatch('refreshTrash');
        $this->updateCount();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Corbeille des prescriptions vidée avec succès!'
        ]);
    }

    // ===== MÉTHODES DE SÉLECTION =====

    public function updatedSelectAllPatients($value)
    {
        if ($value) {
            $this->selectedPatients = Patient::onlyTrashed()
                ->when($this->search, function ($query) {
                    $query->where('numero_dossier', 'like', '%' . $this->search . '%')
                        ->orWhere('nom', 'like', '%' . $this->search . '%')
                        ->orWhere('prenom', 'like', '%' . $this->search . '%')
                        ->orWhere('telephone', 'like', '%' . $this->search . '%');
                })
                ->pluck('id')
                ->toArray();
        } else {
            $this->selectedPatients = [];
        }
    }

    public function updatedSelectAllPrescriptions($value)
    {
        if ($value) {
            $this->selectedPrescriptions = Prescription::onlyTrashed()
                ->when($this->search, function ($query) {
                    $query->where('reference', 'like', '%' . $this->search . '%')
                        ->orWhereHas('patient', function ($q) {
                            $q->where('nom', 'like', '%' . $this->search . '%')
                                ->orWhere('prenom', 'like', '%' . $this->search . '%')
                                ->orWhere('numero_dossier', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('prescripteur', function ($q) {
                            $q->where('nom', 'like', '%' . $this->search . '%');
                        });
                })
                ->pluck('id')
                ->toArray();
        } else {
            $this->selectedPrescriptions = [];
        }
    }

    // ===== MÉTHODES UTILITAIRES =====

    public function updatedActiveTab()
    {
        // Réinitialiser la recherche et les sélections quand on change d'onglet
        $this->search = '';
        $this->selectedPatients = [];
        $this->selectedPrescriptions = [];
        $this->selectAllPatients = false;
        $this->selectAllPrescriptions = false;
        $this->resetPage('patients_page');
        $this->resetPage('prescriptions_page');
    }

    public function updatedSearch()
    {
        // Réinitialiser les sélections quand on fait une recherche
        $this->selectedPatients = [];
        $this->selectedPrescriptions = [];
        $this->selectAllPatients = false;
        $this->selectAllPrescriptions = false;
        $this->resetPage('patients_page');
        $this->resetPage('prescriptions_page');
    }
}