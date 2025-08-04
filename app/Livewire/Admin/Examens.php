<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Examen; // Modèle Eloquent

class Examens extends Component
{
    public $mode = 'list'; // 'list', 'create', 'edit', 'show'
    public $examens;
    public $examen;

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

    // Méthodes pour changer de mode, charger un examen, etc.
    public function show($id)
    {
        $this->examen = Examen::findOrFail($id);
        $this->mode = 'show';
    }

    public function create()
    {
        $this->examen = null;
        $this->mode = 'create';
    }

    public function edit($id)
    {
        $this->examen = Examen::findOrFail($id);
        $this->mode = 'edit';
    }

    public function backToList()
    {
        $this->examen = null;
        $this->mode = 'list';
        $this->loadExamens();
    }
}
