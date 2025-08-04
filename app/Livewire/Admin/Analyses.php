<?php
namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Analyse;
use App\Models\Examen;
use App\Models\Type;

class Analyses extends Component
{
    use WithPagination;

    public $mode = 'list';
    public $analyse;
    public $examens;
    public $types;
    public $analysesParents;

    // Filtres et pagination
    public $selectedExamen = '';
    public $perPage = 10; // Nombre d'éléments par page
    public $search = ''; // Recherche textuelle

    // Propriétés pour les formulaires
    public $code = '';
    public $level = '';
    public $parent_id = '';
    public $designation = '';
    public $description = '';
    public $prix = 0;
    public $is_bold = false;
    public $examen_id = '';
    public $type_id = '';
    public $valeur_ref = '';
    public $unite = '';
    public $suffixe = '';
    public $valeurs_predefinies = [];
    public $ordre = 99;
    public $status = true;

    protected $rules = [
        'code' => 'required|string|max:50',
        'level' => 'required|in:PARENT,NORMAL,CHILD',
        'designation' => 'required|string|max:255',
        'prix' => 'required|numeric|min:0',
        'examen_id' => 'required|exists:examens,id',
        'type_id' => 'required|exists:types,id',
        'parent_id' => 'nullable|exists:analyses,id',
        'description' => 'nullable|string',
        'valeur_ref' => 'nullable|string|max:255',
        'unite' => 'nullable|string|max:50',
        'suffixe' => 'nullable|string|max:50',
        'ordre' => 'nullable|integer',
        'is_bold' => 'boolean',
        'status' => 'boolean',
    ];

    protected $messages = [
        'code.required' => 'Le code est requis.',
        'code.unique' => 'Ce code existe déjà.',
        'level.required' => 'Le niveau est requis.',
        'level.in' => 'Le niveau doit être PARENT, NORMAL ou CHILD.',
        'designation.required' => 'La désignation est requise.',
        'prix.required' => 'Le prix est requis.',
        'prix.numeric' => 'Le prix doit être un nombre.',
        'prix.min' => 'Le prix ne peut pas être négatif.',
        'examen_id.required' => 'L\'examen est requis.',
        'examen_id.exists' => 'L\'examen sélectionné n\'existe pas.',
        'type_id.required' => 'Le type est requis.',
        'type_id.exists' => 'Le type sélectionné n\'existe pas.',
        'parent_id.exists' => 'Le parent sélectionné n\'existe pas.',
    ];

    public function mount()
    {
        $this->loadInitialData();
    }

    public function loadInitialData()
    {
        $this->examens = Examen::where('status', true)->orderBy('name')->get();
        $this->types = Type::where('status', true)->orderBy('name')->get();
        $this->analysesParents = Analyse::where('level', 'PARENT')
            ->where('status', true)
            ->orderBy('designation')
            ->get();
    }

    // Cette méthode sera appelée automatiquement quand les filtres changent
    public function updatedSelectedExamen()
    {
        $this->resetPage(); // Réinitialiser à la page 1 quand on change de filtre
    }

    public function updatedSearch()
    {
        $this->resetPage(); // Réinitialiser à la page 1 quand on fait une recherche
    }

    public function updatedPerPage()
    {
        $this->resetPage(); // Réinitialiser à la page 1 quand on change le nombre par page
    }

    // Utiliser une propriété computed pour les analyses avec pagination
    public function getAnalysesProperty()
    {
        $query = Analyse::with(['examen', 'type', 'parent', 'enfants'])
            ->orderBy('level', 'DESC') // PARENT en premier
            ->orderBy('ordre')
            ->orderBy('designation');

        // Appliquer le filtre par examen si sélectionné
        if (!empty($this->selectedExamen)) {
            $query->where('examen_id', $this->selectedExamen);
        }

        // Appliquer la recherche textuelle si présente
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('code', 'like', '%' . $this->search . '%')
                    ->orWhere('designation', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        return $query->paginate($this->perPage);
    }

    public function render()
    {
        // Les analyses seront récupérées via la propriété computed
        return view('livewire.admin.analyses');
    }

    public function show($id)
    {
        $this->analyse = Analyse::with(['examen', 'type', 'parent', 'enfants'])->findOrFail($id);
        $this->mode = 'show';
    }

    public function create()
    {
        $this->resetForm();
        $this->mode = 'create';
    }

    public function edit($id)
    {
        $this->analyse = Analyse::findOrFail($id);
        $this->fillForm();
        $this->mode = 'edit';
    }

    public function store()
    {
        $this->validate(array_merge($this->rules, [
            'code' => 'required|string|max:50|unique:analyses,code',
        ]));

        Analyse::create([
            'code' => $this->code,
            'level' => $this->level,
            'parent_id' => $this->parent_id ?: null,
            'designation' => $this->designation,
            'description' => $this->description,
            'prix' => $this->prix,
            'is_bold' => $this->is_bold,
            'examen_id' => $this->examen_id,
            'type_id' => $this->type_id,
            'valeur_ref' => $this->valeur_ref,
            'unite' => $this->unite,
            'suffixe' => $this->suffixe,
            'valeurs_predefinies' => $this->valeurs_predefinies ? json_encode($this->valeurs_predefinies) : null,
            'ordre' => $this->ordre,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Analyse créée avec succès !');
        $this->backToList();
    }

    public function update()
    {
        $this->validate(array_merge($this->rules, [
            'code' => 'required|string|max:50|unique:analyses,code,' . $this->analyse->id,
        ]));

        $this->analyse->update([
            'code' => $this->code,
            'level' => $this->level,
            'parent_id' => $this->parent_id ?: null,
            'designation' => $this->designation,
            'description' => $this->description,
            'prix' => $this->prix,
            'is_bold' => $this->is_bold,
            'examen_id' => $this->examen_id,
            'type_id' => $this->type_id,
            'valeur_ref' => $this->valeur_ref,
            'unite' => $this->unite,
            'suffixe' => $this->suffixe,
            'valeurs_predefinies' => $this->valeurs_predefinies ? json_encode($this->valeurs_predefinies) : null,
            'ordre' => $this->ordre,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Analyse modifiée avec succès !');
        $this->backToList();
    }

    public function backToList()
    {
        $this->resetForm();
        $this->analyse = null;
        $this->mode = 'list';
        // Recharger seulement les données qui peuvent avoir changé
        $this->analysesParents = Analyse::where('level', 'PARENT')
            ->where('status', true)
            ->orderBy('designation')
            ->get();
    }

    // Méthode pour réinitialiser tous les filtres
    public function resetFilters()
    {
        $this->selectedExamen = '';
        $this->search = '';
        $this->resetPage();
    }

    // Méthode pour réinitialiser le filtre examen
    public function resetFilter()
    {
        $this->selectedExamen = '';
        $this->resetPage();
    }

    private function fillForm()
    {
        $this->code = $this->analyse->code;
        $this->level = $this->analyse->level;
        $this->parent_id = $this->analyse->parent_id;
        $this->designation = $this->analyse->designation;
        $this->description = $this->analyse->description;
        $this->prix = $this->analyse->prix;
        $this->is_bold = $this->analyse->is_bold;
        $this->examen_id = $this->analyse->examen_id;
        $this->type_id = $this->analyse->type_id;
        $this->valeur_ref = $this->analyse->valeur_ref;
        $this->unite = $this->analyse->unite;
        $this->suffixe = $this->analyse->suffixe;
        $this->valeurs_predefinies = $this->analyse->valeurs_predefinies ?: [];
        $this->ordre = $this->analyse->ordre;
        $this->status = $this->analyse->status;
    }

    private function resetForm()
    {
        $this->code = '';
        $this->level = '';
        $this->parent_id = '';
        $this->designation = '';
        $this->description = '';
        $this->prix = 0;
        $this->is_bold = false;
        $this->examen_id = '';
        $this->type_id = '';
        $this->valeur_ref = '';
        $this->unite = '';
        $this->suffixe = '';
        $this->valeurs_predefinies = [];
        $this->ordre = 99;
        $this->status = true;
        $this->resetErrorBag();
    }

    // Méthodes utilitaires
    public function toggleStatus($id)
    {
        $analyse = Analyse::findOrFail($id);
        $analyse->update(['status' => !$analyse->status]);

        $status = $analyse->status ? 'activée' : 'désactivée';
        session()->flash('message', "Analyse {$status} avec succès !");
    }

    public function duplicate($id)
    {
        $original = Analyse::findOrFail($id);

        $copy = $original->replicate();
        $copy->code = $original->code . '_COPY';
        $copy->designation = $original->designation . ' (Copie)';
        $copy->save();

        session()->flash('message', 'Analyse dupliquée avec succès !');
    }
}