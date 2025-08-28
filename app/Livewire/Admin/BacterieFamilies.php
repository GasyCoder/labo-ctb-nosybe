<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BacterieFamille;



class BacterieFamilies extends Component
{
    use WithPagination;

    public $currentView = 'list';
    public $famille;
    public $darkMode = false;

    // Filtres et pagination
    public $search = '';
    public $perPage = 15;

    // Propriétés pour les formulaires
    public $designation = '';
    public $status = true;

    protected $rules = [
        'designation' => 'required|string|max:255',
        'status' => 'boolean',
    ];

    protected $messages = [
        'designation.required' => 'La désignation est requise.',
        'designation.max' => 'La désignation ne peut pas dépasser 255 caractères.',
        'designation.unique' => 'Cette famille de bactérie existe déjà.',
    ];

    public function mount()
    {
        $this->darkMode = session('darkMode', false);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function getFamillesProperty()
    {
        $query = BacterieFamille::withCount('bacteries')->orderBy('designation');

        if (!empty($this->search)) {
            $query->where('designation', 'like', '%' . $this->search . '%');
        }

        return $query->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.admin.bacterie-families');
    }

    public function show($id)
    {
        $this->famille = BacterieFamille::with('bacteries')->findOrFail($id);
        $this->currentView = 'show';
    }

    public function create()
    {
        $this->resetForm();
        $this->currentView = 'create';
    }

    public function edit($id)
    {
        $this->famille = BacterieFamille::findOrFail($id);
        $this->fillForm();
        $this->currentView = 'edit';
    }

    public function store()
    {
        $this->validate(array_merge($this->rules, [
            'designation' => 'required|string|max:255|unique:bacterie_familles,designation',
        ]));

        BacterieFamille::create([
            'designation' => trim($this->designation),
            'status' => $this->status,
        ]);

        session()->flash('message', 'Famille de bactérie créée avec succès !');
        $this->backToList();
    }

    public function update()
    {
        $this->validate(array_merge($this->rules, [
            'designation' => 'required|string|max:255|unique:bacterie_familles,designation,' . $this->famille->id,
        ]));

        $this->famille->update([
            'designation' => trim($this->designation),
            'status' => $this->status,
        ]);

        session()->flash('message', 'Famille de bactérie modifiée avec succès !');
        $this->backToList();
    }

    public function delete($id)
    {
        $famille = BacterieFamille::findOrFail($id);

        // Vérifier s'il y a des bactéries liées
        if ($famille->bacteries()->count() > 0) {
            session()->flash('error', 'Impossible de supprimer cette famille car elle contient des bactéries.');
            return;
        }

        $famille->delete();
        session()->flash('message', 'Famille de bactérie supprimée avec succès !');
    }

    public function backToList()
    {
        $this->resetForm();
        $this->famille = null;
        $this->currentView = 'list';
    }

    public function toggleStatus($id)
    {
        $famille = BacterieFamille::findOrFail($id);
        $famille->update(['status' => !$famille->status]);

        $status = $famille->status ? 'activée' : 'désactivée';
        session()->flash('message', "Famille {$status} avec succès !");
    }

    public function resetSearch()
    {
        $this->search = '';
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
        $this->designation = $this->famille->designation;
        $this->status = $this->famille->status;
    }

    private function resetForm()
    {
        $this->designation = '';
        $this->status = true;
        $this->resetErrorBag();
    }

    public function getStatsProperty()
    {
        return [
            'total' => BacterieFamille::count(),
            'actives' => BacterieFamille::where('status', true)->count(),
            'inactives' => BacterieFamille::where('status', false)->count(),
            'avec_bacteries' => BacterieFamille::has('bacteries')->count(),
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
}