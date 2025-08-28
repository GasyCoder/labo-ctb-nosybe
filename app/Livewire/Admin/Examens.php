<?php
namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Examen;

class Examens extends Component
{
    public $mode = 'list';
    public $examens;
    public $examen;

    // Propriétés pour les formulaires
    public $name = '';
    public $abr = '';
    public $status = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'abr' => 'required|string|max:10',
        'status' => 'boolean',
    ];

    protected $messages = [
        'name.required' => 'Le nom de l\'examen est requis.',
        'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
        'abr.required' => 'L\'abréviation est requise.',
        'abr.max' => 'L\'abréviation ne peut pas dépasser 10 caractères.',
    ];

    public function mount()
    {
        $this->loadExamens();
    }

    public function loadExamens()
    {
        $this->examens = Examen::all();
    }

    public function render()
    {
        return view('livewire.admin.examens');
    }

    public function show($id)
    {
        $this->examen = Examen::findOrFail($id);
        $this->mode = 'show';
    }

    public function create()
    {
        $this->resetForm();
        $this->mode = 'create';
    }

    public function edit($id)
    {
        $this->examen = Examen::findOrFail($id);
        $this->name = $this->examen->name;
        $this->abr = $this->examen->abr;
        $this->status = $this->examen->status;
        $this->mode = 'edit';
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'abr' => 'required|string|max:10|unique:examens,abr',
            'status' => 'boolean',
        ]);

        Examen::create([
            'name' => $this->name,
            'abr' => $this->abr,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Examen créé avec succès !');
        $this->backToList();
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'abr' => 'required|string|max:10|unique:examens,abr,' . $this->examen->id,
            'status' => 'boolean',
        ]);

        $this->examen->update([
            'name' => $this->name,
            'abr' => $this->abr,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Examen modifié avec succès !');
        $this->backToList();
    }

    public function backToList()
    {
        $this->resetForm();
        $this->examen = null;
        $this->mode = 'list';
        $this->loadExamens();
    }

    private function resetForm()
    {
        $this->name = '';
        $this->abr = '';
        $this->status = true;
        $this->resetErrorBag();
    }
}