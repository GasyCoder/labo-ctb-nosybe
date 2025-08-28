<?php

namespace App\Livewire\Secretaire;

use App\Models\Patient;
use Livewire\Component;
use Livewire\WithPagination;

class Patients extends Component
{
    use WithPagination;

    // Propriétés pour la recherche et les filtres
    public $search = '';
    public $sexeFilter = '';
    public $statutFilter = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Mode sélection (si on veut permettre de sélectionner un patient)
    public $selectionMode = false;
    public $selectedPatient = null;

    // Propriétés pour les statistiques
    public $totalPatients = 0;
    public $patientsNouveaux = 0;
    public $patientsFideles = 0;
    public $patientsVip = 0;

    // Reset pagination when searching/filtering
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSexeFilter()
    {
        $this->resetPage();
    }

    public function updatedStatutFilter()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    // Méthode pour trier
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
        $this->resetPage();
    }

    // Méthode pour réinitialiser les filtres
    public function resetFilters()
    {
        $this->search = '';
        $this->sexeFilter = '';
        $this->statutFilter = '';
        $this->perPage = 10;
        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    // Méthode pour sélectionner un patient (optionnel)
    public function selectPatient($patientId)
    {
        $this->selectedPatient = Patient::find($patientId);
        // Vous pouvez émettre un événement pour notifier le parent
        $this->dispatch('patientSelected', $this->selectedPatient);
    }

    // Méthode pour activer/désactiver le mode sélection
    public function toggleSelectionMode()
    {
        $this->selectionMode = !$this->selectionMode;
        $this->selectedPatient = null;
    }

    public function mount($selectionMode = false)
    {
        $this->selectionMode = $selectionMode;
        $this->loadStatistics();
    }

    private function loadStatistics()
    {
        $this->totalPatients = Patient::count();
        $this->patientsNouveaux = Patient::where('statut', 'NOUVEAU')->count();
        $this->patientsFideles = Patient::where('statut', 'FIDELE')->count();
        $this->patientsVip = Patient::where('statut', 'VIP')->count();
    }

    public function render()
    {
        // Construire la requête avec les filtres
        $query = Patient::query();

        // Recherche globale
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nom', 'like', '%' . $this->search . '%')
                  ->orWhere('prenom', 'like', '%' . $this->search . '%')
                  ->orWhere('telephone', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('reference', 'like', '%' . $this->search . '%');
            });
        }

        // Filtre par sexe
        if ($this->sexeFilter) {
            $query->where('sexe', $this->sexeFilter);
        }

        // Filtre par statut
        if ($this->statutFilter) {
            $query->where('statut', $this->statutFilter);
        }

        // Tri
        $query->orderBy($this->sortField, $this->sortDirection);

        // Ajouter le nombre de prescriptions pour chaque patient
        $query->withCount('prescriptions');

        $patients = $query->paginate($this->perPage);

        // Recharger les statistiques
        $this->loadStatistics();

        return view('livewire.secretaire.patients', [
            'patients' => $patients
        ]);
    }
}