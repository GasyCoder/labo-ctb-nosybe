<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Antibiotique;
use App\Models\BacterieFamille;


class Antibiotiques extends Component
{
    use WithPagination;

    public $currentView = 'list';
    public $antibiotique;
    public $darkMode = false;

    // Filtres et pagination
    public $search = '';
    public $perPage = 15;
    public $familleFilter = '';

    // Propriétés pour les formulaires
    public $famille_id = '';
    public $designation = '';
    public $commentaire = '';
    public $status = true;

    protected $rules = [
        'famille_id' => 'required|exists:bacterie_familles,id',
        'designation' => 'required|string|max:255',
        'commentaire' => 'nullable|string|max:1000',
        'status' => 'boolean',
    ];

    protected $messages = [
        'famille_id.required' => 'La famille est requise.',
        'famille_id.exists' => 'La famille sélectionnée n\'existe pas.',
        'designation.required' => 'La désignation est requise.',
        'designation.max' => 'La désignation ne peut pas dépasser 255 caractères.',
        'designation.unique' => 'Cet antibiotique existe déjà dans cette famille.',
        'commentaire.max' => 'Le commentaire ne peut pas dépasser 1000 caractères.',
    ];

    public function mount()
    {
        $this->darkMode = session('darkMode', false);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFamilleFilter()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function getAntibiotiquesProperty()
    {
        $query = Antibiotique::with(['famille', 'bacteries'])->orderBy('designation');

        // Recherche textuelle
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('designation', 'like', '%' . $this->search . '%')
                    ->orWhere('commentaire', 'like', '%' . $this->search . '%')
                    ->orWhereHas('famille', function ($sq) {
                        $sq->where('designation', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Filtre par famille
        if (!empty($this->familleFilter)) {
            $query->where('famille_id', $this->familleFilter);
        }

        return $query->paginate($this->perPage);
    }

    public function getFamillesProperty()
    {
        return BacterieFamille::where('status', true)->orderBy('designation')->get();
    }

    public function getAllFamillesProperty()
    {
        return BacterieFamille::orderBy('designation')->get();
    }

    public function render()
    {
        return view('livewire.admin.antibiotiques', [
            'antibiotiques' => $this->antibiotiques,
            'familles' => BacterieFamille::orderBy('designation')->get(),
        ]);
    }
    public function show($id)
    {
        $this->antibiotique = Antibiotique::with(['famille', 'bacteries'])->findOrFail($id);
        $this->currentView = 'show';
    }

    public function create()
    {
        $this->resetForm();
        $this->currentView = 'create';
    }

    public function edit($id)
    {
        $this->antibiotique = Antibiotique::findOrFail($id);
        $this->fillForm();
        $this->currentView = 'edit';
    }

    public function store()
    {
        $this->validate(array_merge($this->rules, [
            'designation' => 'required|string|max:255|unique:antibiotiques,designation,NULL,id,famille_id,' . $this->famille_id,
        ]));

        Antibiotique::create([
            'famille_id' => $this->famille_id,
            'designation' => trim($this->designation),
            'commentaire' => $this->commentaire ? trim($this->commentaire) : null,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Antibiotique créé avec succès !');
        $this->backToList();
    }

    public function update()
    {
        $this->validate(array_merge($this->rules, [
            'designation' => 'required|string|max:255|unique:antibiotiques,designation,' . $this->antibiotique->id . ',id,famille_id,' . $this->famille_id,
        ]));

        $this->antibiotique->update([
            'famille_id' => $this->famille_id,
            'designation' => trim($this->designation),
            'commentaire' => $this->commentaire ? trim($this->commentaire) : null,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Antibiotique modifié avec succès !');
        $this->backToList();
    }

    public function delete($id)
    {
        $antibiotique = Antibiotique::findOrFail($id);

        // Vérifier s'il y a des relations liées
        if ($antibiotique->bacteries()->count() > 0) {
            session()->flash('error', 'Impossible de supprimer cet antibiotique car il est lié à des bactéries.');
            return;
        }

        $antibiotique->delete();
        session()->flash('message', 'Antibiotique supprimé avec succès !');
    }

    public function backToList()
    {
        $this->resetForm();
        $this->antibiotique = null;
        $this->currentView = 'list';
    }

    public function toggleStatus($id)
    {
        $antibiotique = Antibiotique::findOrFail($id);
        $antibiotique->update(['status' => !$antibiotique->status]);

        $status = $antibiotique->status ? 'activé' : 'désactivé';
        session()->flash('message', "Antibiotique {$status} avec succès !");
    }

    public function resetSearch()
    {
        $this->search = '';
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->familleFilter = '';
        $this->resetPage();
    }

    public function toggleDarkMode()
    {
        $this->darkMode = !$this->darkMode;
        session(['darkMode' => $this->darkMode]);
        $this->dispatch('dark-mode-toggled', $this->darkMode);
    }

    public function duplicate($id)
    {
        $original = Antibiotique::findOrFail($id);

        $copy = $original->replicate();
        $copy->designation = $original->designation . ' (Copie)';
        $copy->save();

        // Copier les relations avec les bactéries
        $copy->bacteries()->sync($original->bacteries->pluck('id')->toArray());

        session()->flash('message', 'Antibiotique dupliqué avec succès !');
    }

    private function fillForm()
    {
        $this->famille_id = $this->antibiotique->famille_id;
        $this->designation = $this->antibiotique->designation;
        $this->commentaire = $this->antibiotique->commentaire;
        $this->status = $this->antibiotique->status;
    }

    private function resetForm()
    {
        $this->famille_id = '';
        $this->designation = '';
        $this->commentaire = '';
        $this->status = true;
        $this->resetErrorBag();
    }

    // In App\Livewire\Admin\Antibiotiques.php

    // Add this method to ensure stats are always available
    public function getStatsProperty()
    {
        return [
            'total' => Antibiotique::count(),
            'actifs' => Antibiotique::where('status', true)->count(),
            'inactifs' => Antibiotique::where('status', false)->count(),
            'avec_bacteries' => Antibiotique::has('bacteries')->count(),
            'par_famille' => Antibiotique::with('famille')
                ->get()
                ->groupBy('famille.designation')
                ->map(function ($group) {
                    return $group->count();
                }),
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function filterByFamille($familleId)
    {
        $this->familleFilter = $familleId;
        $this->resetPage();
    }

    public function getMostUsedAntibiotiques($limit = 5)
    {
        return Antibiotique::withCount('bacteries')
            ->orderByDesc('bacteries_count')
            ->where('status', true)
            ->limit($limit)
            ->get();
    }

    public function exportData()
    {
        // Logique d'export des antibiotiques
        session()->flash('message', 'Export en cours de développement...');
    }

    public function bulkUpdateStatus($ids, $status)
    {
        Antibiotique::whereIn('id', $ids)->update(['status' => $status]);

        $action = $status ? 'activés' : 'désactivés';
        session()->flash('message', count($ids) . " antibiotique(s) {$action} avec succès !");
    }
}