<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Analyse;
use App\Models\Examen;
use App\Models\Type;
use Illuminate\Support\Facades\DB;

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
    public $perPage = 10;
    public $search = '';

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

    // NOUVELLE PROPRIÉTÉ pour la création en lot
    public $sousAnalyses = [];
    public $createWithChildren = false;

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
        
        // Règles pour sous-analyses
        'sousAnalyses.*.code' => 'required|string|max:50',
        'sousAnalyses.*.designation' => 'required|string|max:255',
        'sousAnalyses.*.prix' => 'required|numeric|min:0',
        'sousAnalyses.*.level' => 'required|in:NORMAL,CHILD',
        'sousAnalyses.*.valeur_ref' => 'nullable|string|max:255',
        'sousAnalyses.*.unite' => 'nullable|string|max:50',
        'sousAnalyses.*.ordre' => 'nullable|integer',
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
        
        'sousAnalyses.*.code.required' => 'Le code de la sous-analyse est requis.',
        'sousAnalyses.*.code.unique' => 'Ce code de sous-analyse existe déjà.',
        'sousAnalyses.*.designation.required' => 'La désignation de la sous-analyse est requise.',
        'sousAnalyses.*.prix.required' => 'Le prix de la sous-analyse est requis.',
        'sousAnalyses.*.prix.numeric' => 'Le prix doit être un nombre.',
        'sousAnalyses.*.level.required' => 'Le niveau de la sous-analyse est requis.',
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

    // ÉVÉNEMENTS LIVEWIRE
    public function updatedSelectedExamen()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedLevel()
    {
        if ($this->level === 'PARENT') {
            $this->createWithChildren = true;
            $this->addSousAnalyse();
        } else {
            $this->createWithChildren = false;
            $this->sousAnalyses = [];
        }
    }

    // PROPRIÉTÉ COMPUTED POUR LES ANALYSES
    public function getAnalysesProperty()
    {
        $query = Analyse::with(['examen', 'type', 'parent', 'enfants'])
            ->orderBy('level', 'DESC')
            ->orderBy('ordre')
            ->orderBy('designation');

        if (!empty($this->selectedExamen)) {
            $query->where('examen_id', $this->selectedExamen);
        }

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
        return view('livewire.admin.analyses');
    }

    // ACTIONS CRUD
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

    // NOUVELLES MÉTHODES pour gestion sous-analyses
    public function addSousAnalyse()
    {
        $this->sousAnalyses[] = [
            'code' => '',
            'designation' => '',
            'level' => 'CHILD',
            'prix' => 0,
            'valeur_ref' => '',
            'unite' => '',
            'ordre' => count($this->sousAnalyses) + 1,
            'status' => true,
            'is_bold' => false,
        ];
    }

    public function removeSousAnalyse($index)
    {
        unset($this->sousAnalyses[$index]);
        $this->sousAnalyses = array_values($this->sousAnalyses);
        
        // Réordonner
        foreach ($this->sousAnalyses as $key => $value) {
            $this->sousAnalyses[$key]['ordre'] = $key + 1;
        }
    }

    public function moveSousAnalyseUp($index)
    {
        if ($index > 0) {
            $temp = $this->sousAnalyses[$index];
            $this->sousAnalyses[$index] = $this->sousAnalyses[$index - 1];
            $this->sousAnalyses[$index - 1] = $temp;
            
            // Mettre à jour les ordres
            $this->sousAnalyses[$index]['ordre'] = $index + 1;
            $this->sousAnalyses[$index - 1]['ordre'] = $index;
        }
    }

    public function moveSousAnalyseDown($index)
    {
        if ($index < count($this->sousAnalyses) - 1) {
            $temp = $this->sousAnalyses[$index];
            $this->sousAnalyses[$index] = $this->sousAnalyses[$index + 1];
            $this->sousAnalyses[$index + 1] = $temp;
            
            // Mettre à jour les ordres
            $this->sousAnalyses[$index]['ordre'] = $index + 1;
            $this->sousAnalyses[$index + 1]['ordre'] = $index + 2;
        }
    }

    public function store()
    {
        // Construire les règles de validation dynamiquement
        $rules = $this->rules;
        $rules['code'] = 'required|string|max:50|unique:analyses,code';

        // Si on a des sous-analyses, ajouter les règles de validation unique pour chaque code
        if ($this->createWithChildren && count($this->sousAnalyses) > 0) {
            foreach ($this->sousAnalyses as $index => $sousAnalyse) {
                // Construire la règle complète pour chaque sous-analyse
                $rules["sousAnalyses.{$index}.code"] = 'required|string|max:50|unique:analyses,code';
                $rules["sousAnalyses.{$index}.designation"] = 'required|string|max:255';
                $rules["sousAnalyses.{$index}.prix"] = 'required|numeric|min:0';
                $rules["sousAnalyses.{$index}.level"] = 'required|in:NORMAL,CHILD';
                $rules["sousAnalyses.{$index}.valeur_ref"] = 'nullable|string|max:255';
                $rules["sousAnalyses.{$index}.unite"] = 'nullable|string|max:50';
                $rules["sousAnalyses.{$index}.ordre"] = 'nullable|integer';
            }
        }

        $this->validate($rules);

        DB::transaction(function () {
            // Créer l'analyse principale
            $analyseParent = Analyse::create([
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

            // Créer les sous-analyses si applicable
            if ($this->createWithChildren && count($this->sousAnalyses) > 0) {
                foreach ($this->sousAnalyses as $sousAnalyse) {
                    Analyse::create([
                        'code' => $sousAnalyse['code'],
                        'level' => $sousAnalyse['level'],
                        'parent_id' => $analyseParent->id,
                        'designation' => $sousAnalyse['designation'],
                        'prix' => $sousAnalyse['prix'],
                        'is_bold' => $sousAnalyse['is_bold'] ?? false,
                        'examen_id' => $this->examen_id, // Hérite de l'examen parent
                        'type_id' => $this->type_id, // Hérite du type parent
                        'valeur_ref' => $sousAnalyse['valeur_ref'],
                        'unite' => $sousAnalyse['unite'],
                        'ordre' => $sousAnalyse['ordre'],
                        'status' => $sousAnalyse['status'] ?? true,
                    ]);
                }
            }
        });

        $message = $this->createWithChildren && count($this->sousAnalyses) > 0 
            ? 'Analyse parent et ' . count($this->sousAnalyses) . ' sous-analyses créées avec succès !'
            : 'Analyse créée avec succès !';
            
        session()->flash('message', $message);
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
        $this->analysesParents = Analyse::where('level', 'PARENT')
            ->where('status', true)
            ->orderBy('designation')
            ->get();
    }

    // MÉTHODES DE FILTRAGE
    public function resetFilters()
    {
        $this->selectedExamen = '';
        $this->search = '';
        $this->resetPage();
    }

    public function resetFilter()
    {
        $this->selectedExamen = '';
        $this->resetPage();
    }

    // MÉTHODES PRIVÉES
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
        
        // Réinitialiser les sous-analyses en mode édition
        $this->createWithChildren = false;
        $this->sousAnalyses = [];
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
        $this->createWithChildren = false;
        $this->sousAnalyses = [];
        $this->resetErrorBag();
    }

    // MÉTHODES UTILITAIRES
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