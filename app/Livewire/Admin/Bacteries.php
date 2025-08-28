<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Bacterie;
use App\Models\BacterieFamille;

class Bacteries extends Component
{
    use WithPagination;

    public $currentView = 'list';
    public $bacterie;
    public $darkMode = false;

    // Filtres et pagination
    public $search = '';
    public $perPage = 15;
    public $familleFilter = '';

    // Propriétés pour les formulaires
    public $famille_id = '';
    public $designation = '';
    public $status = true;

    protected $rules = [
        'famille_id' => 'required|exists:bacterie_familles,id',
        'designation' => 'required|string|max:255',
        'status' => 'boolean',
    ];

    protected $messages = [
        'famille_id.required' => 'La famille est requise.',
        'famille_id.exists' => 'La famille sélectionnée n\'existe pas.',
        'designation.required' => 'La désignation est requise.',
        'designation.max' => 'La désignation ne peut pas dépasser 255 caractères.',
        'designation.unique' => 'Cette bactérie existe déjà dans cette famille.',
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

    public function getBacteriesProperty()
    {
        $query = Bacterie::with('famille')->orderBy('designation');

        // Recherche textuelle
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('designation', 'like', '%' . $this->search . '%')
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
        return view('livewire.admin.bacteries');
    }

    public function show($id)
    {
        $this->bacterie = Bacterie::with('famille')->findOrFail($id);
        $this->currentView = 'show';
    }

    public function create()
    {
        $this->resetForm();
        $this->currentView = 'create';
    }

    public function edit($id)
    {
        $this->bacterie = Bacterie::findOrFail($id);
        $this->fillForm();
        $this->currentView = 'edit';
    }

    public function store()
    {
        $this->validate(array_merge($this->rules, [
            'designation' => 'required|string|max:255|unique:bacteries,designation,NULL,id,famille_id,' . $this->famille_id,
        ]));

        Bacterie::create([
            'famille_id' => $this->famille_id,
            'designation' => trim($this->designation),
            'status' => $this->status,
        ]);

        session()->flash('message', 'Bactérie créée avec succès !');
        $this->backToList();
    }

    public function update()
    {
        $this->validate(array_merge($this->rules, [
            'designation' => 'required|string|max:255|unique:bacteries,designation,' . $this->bacterie->id . ',id,famille_id,' . $this->famille_id,
        ]));

        $this->bacterie->update([
            'famille_id' => $this->famille_id,
            'designation' => trim($this->designation),
            'status' => $this->status,
        ]);

        session()->flash('message', 'Bactérie modifiée avec succès !');
        $this->backToList();
    }

    public function delete($id)
    {
        $bacterie = Bacterie::findOrFail($id);

        // Vérifier s'il y a des relations liées (antibiotiques, etc.)
        if (method_exists($bacterie, 'antibiotiques') && $bacterie->antibiotiques()->count() > 0) {
            session()->flash('error', 'Impossible de supprimer cette bactérie car elle est liée à des antibiotiques.');
            return;
        }

        $bacterie->delete();
        session()->flash('message', 'Bactérie supprimée avec succès !');
    }

    public function backToList()
    {
        $this->resetForm();
        $this->bacterie = null;
        $this->currentView = 'list';
    }

    public function toggleStatus($id)
    {
        $bacterie = Bacterie::findOrFail($id);
        $bacterie->update(['status' => !$bacterie->status]);

        $status = $bacterie->status ? 'activée' : 'désactivée';
        session()->flash('message', "Bactérie {$status} avec succès !");
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

    private function fillForm()
    {
        $this->famille_id = $this->bacterie->famille_id;
        $this->designation = $this->bacterie->designation;
        $this->status = $this->bacterie->status;
    }

    private function resetForm()
    {
        $this->famille_id = '';
        $this->designation = '';
        $this->status = true;
        $this->resetErrorBag();
    }

    public function getStatsProperty()
    {
        return [
            'total' => Bacterie::count(),
            'actives' => Bacterie::where('status', true)->count(),
            'inactives' => Bacterie::where('status', false)->count(),
            'par_famille' => Bacterie::with('famille')
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
}