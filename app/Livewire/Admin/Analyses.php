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
    public $selectedLevel = 'tous';
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

    public $showDeleteModal = false;
    public $analyseToDelete = null;

    // Propriété pour la gestion des sous-analyses
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
        'sousAnalyses.*.level' => 'required|in:PARENT,NORMAL,CHILD',
        'sousAnalyses.*.examen_id' => 'nullable|exists:examens,id',
        'sousAnalyses.*.type_id' => 'nullable|exists:types,id',
        'sousAnalyses.*.parent_id' => 'nullable|exists:analyses,id',
        'sousAnalyses.*.valeur_ref' => 'nullable|string|max:255',
        'sousAnalyses.*.unite' => 'nullable|string|max:50',
        'sousAnalyses.*.suffixe' => 'nullable|string|max:50',
        'sousAnalyses.*.ordre' => 'nullable|integer',
        'sousAnalyses.*.is_bold' => 'boolean',
        'sousAnalyses.*.status' => 'boolean',
        'sousAnalyses.*.id' => 'nullable|exists:analyses,id',

        // Règles pour sous-sous-analyses
        'sousAnalyses.*.children.*.code' => 'required|string|max:50',
        'sousAnalyses.*.children.*.designation' => 'required|string|max:255',
        'sousAnalyses.*.children.*.prix' => 'required|numeric|min:0',
        'sousAnalyses.*.children.*.level' => 'required|in:NORMAL,CHILD',
        'sousAnalyses.*.children.*.examen_id' => 'nullable|exists:examens,id',
        'sousAnalyses.*.children.*.type_id' => 'nullable|exists:types,id',
        'sousAnalyses.*.children.*.valeur_ref' => 'nullable|string|max:255',
        'sousAnalyses.*.children.*.unite' => 'nullable|string|max:50',
        'sousAnalyses.*.children.*.suffixe' => 'nullable|string|max:50',
        'sousAnalyses.*.children.*.ordre' => 'nullable|integer',
        'sousAnalyses.*.children.*.is_bold' => 'boolean',
        'sousAnalyses.*.children.*.status' => 'boolean',
        'sousAnalyses.*.children.*.id' => 'nullable|exists:analyses,id',
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
        'sousAnalyses.*.prix.numeric' => 'Le prix de la sous-analyse doit être un nombre.',
        'sousAnalyses.*.level.required' => 'Le niveau de la sous-analyse est requis.',
        'sousAnalyses.*.level.in' => 'Le niveau de la sous-analyse doit être PARENT, NORMAL ou CHILD.',

        'sousAnalyses.*.children.*.code.required' => 'Le code de la sous-sous-analyse est requis.',
        'sousAnalyses.*.children.*.code.unique' => 'Ce code de sous-sous-analyse existe déjà.',
        'sousAnalyses.*.children.*.designation.required' => 'La désignation de la sous-sous-analyse est requise.',
        'sousAnalyses.*.children.*.prix.required' => 'Le prix de la sous-sous-analyse est requis.',
        'sousAnalyses.*.children.*.prix.numeric' => 'Le prix de la sous-sous-analyse doit être un nombre.',
        'sousAnalyses.*.children.*.level.required' => 'Le niveau de la sous-sous-analyse est requis.',
        'sousAnalyses.*.children.*.level.in' => 'Le niveau de la sous-sous-analyse doit être NORMAL ou CHILD.',
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

    public function updatedSelectedExamen()
    {
        $this->resetPage();
    }

    public function updatedSelectedLevel()
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
            if (empty($this->sousAnalyses)) {
                $this->addSousAnalyse();
            }
        } else {
            $this->createWithChildren = false;
            $this->sousAnalyses = [];
        }
    }

    public function updatedSousAnalyses($value, $name)
    {
        if (preg_match('/(\d+)\.level/', $name, $matches)) {
            $index = $matches[1];
            if ($value === 'PARENT' && !isset($this->sousAnalyses[$index]['children'])) {
                $this->sousAnalyses[$index]['children'] = [];
                $this->addChildToSousAnalyse($index);
            } elseif ($value !== 'PARENT') {
                unset($this->sousAnalyses[$index]['children']);
            }
        }
    }

    public function getAnalysesProperty()
    {
        $query = Analyse::with(['examen', 'type', 'parent', 'enfants']);

        switch ($this->selectedLevel) {
            case 'parents':
                $query->where('level', 'PARENT');
                break;
            case 'racines':
                $query->whereNull('parent_id');
                break;
            case 'normales':
                $query->where('level', 'NORMAL');
                break;
            case 'enfants':
                $query->where('level', 'CHILD');
                break;
            case 'tous':
            default:
                break;
        }

        $query->orderBy('level', 'DESC')
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

    public function getAnalysesCountByLevel()
    {
        return [
            'racines' => Analyse::whereNull('parent_id')->count(),
            'parents' => Analyse::where('level', 'PARENT')->count(),
            'normales' => Analyse::where('level', 'NORMAL')->count(),
            'enfants' => Analyse::where('level', 'CHILD')->count(),
            'tous' => Analyse::count(),
        ];
    }

    public function render()
    {
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
        $this->analyse = Analyse::with(['enfants' => function ($query) {
            $query->with('enfants')->orderBy('ordre');
        }])->findOrFail($id);
        $this->fillForm();
        $this->mode = 'edit';
    }

    public function addSousAnalyse()
    {
        $this->sousAnalyses[] = [
            'code' => '',
            'designation' => '',
            'prix' => 0,
            'level' => 'CHILD',
            'examen_id' => null,
            'type_id' => null,
            'unite' => '',
            'ordre' => count($this->sousAnalyses) + 1,
            'valeur_ref' => '',
            'suffixe' => '',
            'parent_id' => null,
            'is_bold' => false,
            'status' => true,
            'children' => []
        ];
    }

    public function removeSousAnalyse($index)
    {
        if (isset($this->sousAnalyses[$index]['id'])) {
            $this->sousAnalyses[$index]['_delete'] = true;
        } else {
            unset($this->sousAnalyses[$index]);
        }
        $this->sousAnalyses = array_values($this->sousAnalyses);

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

            $this->sousAnalyses[$index]['ordre'] = $index + 1;
            $this->sousAnalyses[$index + 1]['ordre'] = $index + 2;
        }
    }

    public function addChildToSousAnalyse($parentIndex)
    {
        if (!isset($this->sousAnalyses[$parentIndex]['children'])) {
            $this->sousAnalyses[$parentIndex]['children'] = [];
        }

        $this->sousAnalyses[$parentIndex]['children'][] = [
            'id' => null,
            'code' => '',
            'designation' => '',
            'level' => 'CHILD',
            'prix' => 0,
            'examen_id' => null,
            'type_id' => null,
            'valeur_ref' => '',
            'unite' => '',
            'suffixe' => '',
            'ordre' => count($this->sousAnalyses[$parentIndex]['children']) + 1,
            'status' => true,
            'is_bold' => false,
        ];
    }

    public function removeChildFromSous($parentIndex, $childIndex)
    {
        if (isset($this->sousAnalyses[$parentIndex]['children'][$childIndex]['id'])) {
            $this->sousAnalyses[$parentIndex]['children'][$childIndex]['_delete'] = true;
        } else {
            unset($this->sousAnalyses[$parentIndex]['children'][$childIndex]);
        }
        $this->sousAnalyses[$parentIndex]['children'] = array_values($this->sousAnalyses[$parentIndex]['children']);

        foreach ($this->sousAnalyses[$parentIndex]['children'] as $key => $value) {
            $this->sousAnalyses[$parentIndex]['children'][$key]['ordre'] = $key + 1;
        }
    }

    public function moveChildUp($parentIndex, $childIndex)
    {
        if ($childIndex > 0) {
            $temp = $this->sousAnalyses[$parentIndex]['children'][$childIndex];
            $this->sousAnalyses[$parentIndex]['children'][$childIndex] = $this->sousAnalyses[$parentIndex]['children'][$childIndex - 1];
            $this->sousAnalyses[$parentIndex]['children'][$childIndex - 1] = $temp;

            $this->sousAnalyses[$parentIndex]['children'][$childIndex]['ordre'] = $childIndex + 1;
            $this->sousAnalyses[$parentIndex]['children'][$childIndex - 1]['ordre'] = $childIndex;
        }
    }

    public function moveChildDown($parentIndex, $childIndex)
    {
        if ($childIndex < count($this->sousAnalyses[$parentIndex]['children']) - 1) {
            $temp = $this->sousAnalyses[$parentIndex]['children'][$childIndex];
            $this->sousAnalyses[$parentIndex]['children'][$childIndex] = $this->sousAnalyses[$parentIndex]['children'][$childIndex + 1];
            $this->sousAnalyses[$parentIndex]['children'][$childIndex + 1] = $temp;

            $this->sousAnalyses[$parentIndex]['children'][$childIndex]['ordre'] = $childIndex + 1;
            $this->sousAnalyses[$parentIndex]['children'][$childIndex + 1]['ordre'] = $childIndex + 2;
        }
    }

    public function store()
    {
        $rules = $this->rules;
        $rules['code'] = 'required|string|max:50|unique:analyses,code';

        if ($this->createWithChildren && count($this->sousAnalyses) > 0) {
            foreach ($this->sousAnalyses as $index => $sousAnalyse) {
                $rules["sousAnalyses.{$index}.code"] = 'required|string|max:50|unique:analyses,code';
                if (isset($sousAnalyse['children']) && count($sousAnalyse['children']) > 0) {
                    foreach ($sousAnalyse['children'] as $cindex => $child) {
                        $rules["sousAnalyses.{$index}.children.{$cindex}.code"] = 'required|string|max:50|unique:analyses,code';
                    }
                }
            }
        }

        $this->validate($rules);

        DB::transaction(function () {
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

            if ($this->createWithChildren && count($this->sousAnalyses) > 0) {
                foreach ($this->sousAnalyses as $sousAnalyse) {
                    $sousAnalyseRecord = Analyse::create([
                        'code' => $sousAnalyse['code'],
                        'level' => $sousAnalyse['level'],
                        'parent_id' => $sousAnalyse['parent_id'] ?? $analyseParent->id,
                        'designation' => $sousAnalyse['designation'],
                        'prix' => $sousAnalyse['prix'],
                        'is_bold' => $sousAnalyse['is_bold'] ?? false,
                        'examen_id' => $sousAnalyse['examen_id'] ?? $this->examen_id,
                        'type_id' => $sousAnalyse['type_id'] ?? $this->type_id,
                        'valeur_ref' => $sousAnalyse['valeur_ref'],
                        'unite' => $sousAnalyse['unite'],
                        'suffixe' => $sousAnalyse['suffixe'] ?? null,
                        'ordre' => $sousAnalyse['ordre'],
                        'status' => $sousAnalyse['status'] ?? true,
                    ]);

                    if (isset($sousAnalyse['children']) && count($sousAnalyse['children']) > 0) {
                        foreach ($sousAnalyse['children'] as $child) {
                            Analyse::create([
                                'code' => $child['code'],
                                'level' => $child['level'],
                                'parent_id' => $sousAnalyseRecord->id,
                                'designation' => $child['designation'],
                                'prix' => $child['prix'],
                                'is_bold' => $child['is_bold'] ?? false,
                                'examen_id' => $child['examen_id'] ?? $this->examen_id,
                                'type_id' => $child['type_id'] ?? $this->type_id,
                                'valeur_ref' => $child['valeur_ref'],
                                'unite' => $child['unite'],
                                'suffixe' => $child['suffixe'] ?? null,
                                'ordre' => $child['ordre'],
                                'status' => $child['status'] ?? true,
                            ]);
                        }
                    }
                }
            }
        });

        $totalChildren = 0;
        foreach ($this->sousAnalyses as $sousAnalyse) {
            $totalChildren++;
            if (isset($sousAnalyse['children'])) {
                $totalChildren += count($sousAnalyse['children']);
            }
        }

        $message = $this->createWithChildren && $totalChildren > 0
            ? 'Analyse parent et ' . $totalChildren . ' sous-analyses créées avec succès !'
            : 'Analyse créée avec succès !';

        session()->flash('message', $message);
        $this->backToList();
    }

    public function update()
    {
        $rules = $this->rules;
        $rules['code'] = 'required|string|max:50|unique:analyses,code,' . $this->analyse->id;

        if ($this->createWithChildren && count($this->sousAnalyses) > 0) {
            foreach ($this->sousAnalyses as $index => $sousAnalyse) {
                $rules["sousAnalyses.{$index}.code"] = 'required|string|max:50|unique:analyses,code,' . ($sousAnalyse['id'] ?? null);
                if (isset($sousAnalyse['children']) && count($sousAnalyse['children']) > 0) {
                    foreach ($sousAnalyse['children'] as $cindex => $child) {
                        $rules["sousAnalyses.{$index}.children.{$cindex}.code"] = 'required|string|max:50|unique:analyses,code,' . ($child['id'] ?? null);
                    }
                }
            }
        }

        $this->validate($rules);

        DB::transaction(function () {
            // Update main analyse
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

            // Handle sous-analyses
            $existingIds = [];
            if ($this->createWithChildren && count($this->sousAnalyses) > 0) {
                foreach ($this->sousAnalyses as $index => $sousAnalyse) {
                    if (isset($sousAnalyse['_delete']) && $sousAnalyse['_delete'] && $sousAnalyse['id']) {
                        Analyse::find($sousAnalyse['id'])->delete();
                        continue;
                    }

                    $data = [
                        'code' => $sousAnalyse['code'],
                        'level' => $sousAnalyse['level'],
                        'parent_id' => $sousAnalyse['parent_id'] ?? $this->analyse->id,
                        'designation' => $sousAnalyse['designation'],
                        'prix' => $sousAnalyse['prix'],
                        'is_bold' => $sousAnalyse['is_bold'] ?? false,
                        'examen_id' => $sousAnalyse['examen_id'] ?? $this->examen_id,
                        'type_id' => $sousAnalyse['type_id'] ?? $this->type_id,
                        'valeur_ref' => $sousAnalyse['valeur_ref'],
                        'unite' => $sousAnalyse['unite'],
                        'suffixe' => $sousAnalyse['suffixe'] ?? null,
                        'ordre' => $sousAnalyse['ordre'],
                        'status' => $sousAnalyse['status'] ?? true,
                    ];

                    if (isset($sousAnalyse['id']) && $sousAnalyse['id']) {
                        $sousAnalyseRecord = Analyse::find($sousAnalyse['id']);
                        $sousAnalyseRecord->update($data);
                        $existingIds[] = $sousAnalyse['id'];
                    } else {
                        $sousAnalyseRecord = Analyse::create($data);
                        $existingIds[] = $sousAnalyseRecord->id;
                    }

                    // Handle sous-sous-analyses
                    $existingChildIds = [];
                    if (isset($sousAnalyse['children']) && count($sousAnalyse['children']) > 0) {
                        foreach ($sousAnalyse['children'] as $child) {
                            if (isset($child['_delete']) && $child['_delete'] && $child['id']) {
                                Analyse::find($child['id'])->delete();
                                continue;
                            }

                            $childData = [
                                'code' => $child['code'],
                                'level' => $child['level'],
                                'parent_id' => $sousAnalyseRecord->id,
                                'designation' => $child['designation'],
                                'prix' => $child['prix'],
                                'is_bold' => $child['is_bold'] ?? false,
                                'examen_id' => $child['examen_id'] ?? $this->examen_id,
                                'type_id' => $child['type_id'] ?? $this->type_id,
                                'valeur_ref' => $child['valeur_ref'],
                                'unite' => $child['unite'],
                                'suffixe' => $child['suffixe'] ?? null,
                                'ordre' => $child['ordre'],
                                'status' => $child['status'] ?? true,
                            ];

                            if (isset($child['id']) && $child['id']) {
                                $childRecord = Analyse::find($child['id']);
                                $childRecord->update($childData);
                                $existingChildIds[] = $child['id'];
                            } else {
                                $childRecord = Analyse::create($childData);
                                $existingChildIds[] = $childRecord->id;
                            }
                        }
                    }

                    // Delete sous-sous-analyses that are no longer present
                    Analyse::where('parent_id', $sousAnalyseRecord->id)
                        ->whereNotIn('id', $existingChildIds)
                        ->delete();
                }
            }

            // Delete sous-analyses that are no longer present
            Analyse::where('parent_id', $this->analyse->id)
                ->whereNotIn('id', $existingIds)
                ->delete();
        });

        $totalChildren = 0;
        foreach ($this->sousAnalyses as $sousAnalyse) {
            if (!isset($sousAnalyse['_delete']) || !$sousAnalyse['_delete']) {
                $totalChildren++;
                if (isset($sousAnalyse['children'])) {
                    foreach ($sousAnalyse['children'] as $child) {
                        if (!isset($child['_delete']) || !$child['_delete']) {
                            $totalChildren++;
                        }
                    }
                }
            }
        }

        $message = $totalChildren > 0
            ? 'Analyse et ' . $totalChildren . ' sous-analyses mises à jour avec succès !'
            : 'Analyse mise à jour avec succès !';

        session()->flash('message', $message);
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

    public function resetFilters()
    {
        $this->selectedExamen = '';
        $this->selectedLevel = 'racines';
        $this->search = '';
        $this->resetPage();
    }

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
        $this->valeurs_predefinies = $this->analyse->valeurs_predefinies ? json_decode($this->analyse->valeurs_predefinies, true) : [];
        $this->ordre = $this->analyse->ordre;
        $this->status = $this->analyse->status;

        $this->createWithChildren = $this->analyse->level === 'PARENT' && $this->analyse->enfants->isNotEmpty();
        $this->sousAnalyses = [];

        if ($this->createWithChildren) {
            foreach ($this->analyse->enfants as $index => $enfant) {
                $sousAnalyse = [
                    'id' => $enfant->id,
                    'code' => $enfant->code,
                    'designation' => $enfant->designation,
                    'level' => $enfant->level,
                    'prix' => $enfant->prix,
                    'examen_id' => $enfant->examen_id,
                    'type_id' => $enfant->type_id,
                    'valeur_ref' => $enfant->valeur_ref,
                    'unite' => $enfant->unite,
                    'suffixe' => $enfant->suffixe,
                    'parent_id' => $enfant->parent_id,
                    'ordre' => $enfant->ordre,
                    'status' => $enfant->status,
                    'is_bold' => $enfant->is_bold,
                    'children' => [],
                ];

                if ($enfant->level === 'PARENT' && $enfant->enfants->isNotEmpty()) {
                    foreach ($enfant->enfants as $cindex => $sousEnfant) {
                        $sousAnalyse['children'][] = [
                            'id' => $sousEnfant->id,
                            'code' => $sousEnfant->code,
                            'designation' => $sousEnfant->designation,
                            'level' => $sousEnfant->level,
                            'prix' => $sousEnfant->prix,
                            'examen_id' => $sousEnfant->examen_id,
                            'type_id' => $sousEnfant->type_id,
                            'valeur_ref' => $sousEnfant->valeur_ref,
                            'unite' => $sousEnfant->unite,
                            'suffixe' => $sousEnfant->suffixe,
                            'ordre' => $sousEnfant->ordre,
                            'status' => $sousEnfant->status,
                            'is_bold' => $sousEnfant->is_bold,
                        ];
                    }
                }

                $this->sousAnalyses[] = $sousAnalyse;
            }
        }
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

    public function toggleStatus($id)
    {
        $analyse = Analyse::find($id);
        $analyse->status = !$analyse->status;
        $analyse->save();

        $this->resetPage();
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

    public function confirmDelete($id)
    {
        $this->analyseToDelete = Analyse::findOrFail($id);
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        try {
            $this->analyseToDelete->delete();
            session()->flash('message', "L'analyse a été supprimée avec succès.");
            $this->closeDeleteModal();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression.');
            $this->closeDeleteModal();
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->analyseToDelete = null;
    }
}