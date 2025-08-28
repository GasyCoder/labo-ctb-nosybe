<?php
namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Type;

class Types extends Component
{
    use WithPagination;

    public $mode = 'list';
    public $type;

    // Filtres et pagination
    public $search = '';
    public $perPage = 10;

    // Propriétés pour les formulaires
    public $name = '';
    public $libelle = '';
    public $status = true;

    // Utiliser la pagination par défaut de Livewire (commenté la ligne personnalisée)
    // protected $paginationTheme = 'livewire.pagination-tailwind';

    protected $rules = [
        'name' => 'required|string|max:100',
        'libelle' => 'required|string|max:255',
        'status' => 'boolean',
    ];

    protected $messages = [
        'name.required' => 'Le nom du type est requis.',
        'name.max' => 'Le nom ne peut pas dépasser 100 caractères.',
        'name.unique' => 'Ce nom de type existe déjà.',
        'libelle.required' => 'Le libellé est requis.',
        'libelle.max' => 'Le libellé ne peut pas dépasser 255 caractères.',
    ];

    // Listeners pour réinitialiser la pagination
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    // Propriété computed pour les types avec pagination et recherche
    public function getTypesProperty()
    {
        $query = Type::withCount('analyses')
            ->orderBy('id');

        // Appliquer la recherche textuelle si présente
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('libelle', 'like', '%' . $this->search . '%');
            });
        }

        return $query->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.admin.types');
    }

    public function show($id)
    {
        $this->type = Type::withCount('analyses')
            ->with([
                'analyses' => function ($query) {
                    $query->select('id', 'code', 'designation', 'type_id')
                        ->where('status', true)
                        ->orderBy('designation');
                }
            ])
            ->findOrFail($id);
        $this->mode = 'show';
    }

    public function create()
    {
        $this->resetForm();
        $this->mode = 'create';
    }

    public function edit($id)
    {
        $this->type = Type::findOrFail($id);
        $this->fillForm();
        $this->mode = 'edit';
    }

    public function store()
    {
        $this->validate(array_merge($this->rules, [
            'name' => 'required|string|max:100|unique:types,name',
        ]));

        Type::create([
            'name' => strtoupper(trim($this->name)),
            'libelle' => trim($this->libelle),
            'status' => $this->status,
        ]);

        session()->flash('message', 'Type d\'analyse créé avec succès !');
        $this->backToList();
    }

    public function update()
    {
        $this->validate(array_merge($this->rules, [
            'name' => 'required|string|max:100|unique:types,name,' . $this->type->id,
        ]));

        $this->type->update([
            'name' => strtoupper(trim($this->name)),
            'libelle' => trim($this->libelle),
            'status' => $this->status,
        ]);

        session()->flash('message', 'Type d\'analyse modifié avec succès !');
        $this->backToList();
    }

    public function backToList()
    {
        $this->resetForm();
        $this->type = null;
        $this->mode = 'list';
    }

    // Méthode pour toggle le statut
    public function toggleStatus($id)
    {
        $type = Type::findOrFail($id);
        $type->update(['status' => !$type->status]);

        $status = $type->status ? 'activé' : 'désactivé';
        session()->flash('message', "Type {$status} avec succès !");
    }

    // Méthode pour réinitialiser la recherche
    public function resetSearch()
    {
        $this->search = '';
        $this->resetPage();
    }

    // Méthode pour dupliquer un type
    public function duplicate($id)
    {
        $original = Type::findOrFail($id);

        $copy = $original->replicate();
        $copy->name = $original->name . '_COPY';
        $copy->libelle = $original->libelle . ' (Copie)';
        $copy->save();

        session()->flash('message', 'Type dupliqué avec succès !');
    }

    // Méthode pour supprimer un type (soft delete)
    public function delete($id)
    {
        $type = Type::findOrFail($id);

        // Vérifier s'il y a des analyses liées
        if ($type->analyses()->count() > 0) {
            session()->flash('error', 'Impossible de supprimer ce type car il est utilisé par des analyses.');
            return;
        }

        $type->delete();
        session()->flash('message', 'Type supprimé avec succès !');
    }

    // Méthodes privées
    private function fillForm()
    {
        $this->name = $this->type->name;
        $this->libelle = $this->type->libelle;
        $this->status = $this->type->status;
    }

    private function resetForm()
    {
        $this->name = '';
        $this->libelle = '';
        $this->status = true;
        $this->resetErrorBag();
    }

    // Méthodes utilitaires pour les statistiques
    public function getStatsProperty()
    {
        return [
            'total' => Type::count(),
            'actifs' => Type::where('status', true)->count(),
            'inactifs' => Type::where('status', false)->count(),
            'avec_analyses' => Type::has('analyses')->count(),
        ];
    }

    // Méthode pour exporter les types (optionnelle)
    public function export()
    {
        // Logique d'export en CSV ou Excel
        $types = Type::withCount('analyses')->get();

        // Ici vous pouvez implémenter l'export
        session()->flash('message', 'Export en cours de développement...');
    }

    // Méthode pour importer des types depuis un fichier
    public function showImportModal()
    {
        // Logique pour afficher un modal d'import
        $this->dispatch('show-import-modal');
    }

    // Validation en temps réel
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
}