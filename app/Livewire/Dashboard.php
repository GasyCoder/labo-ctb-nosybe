<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $user; // Déclarer comme propriété publique

    public function mount()
    {
        $this->user = Auth::user();

        // Vérification de débogage
        if (!$this->user) {
            dd('Aucun utilisateur connecté');
        }

        if (!$this->user->type) {
            dd('Type utilisateur non défini', $this->user);
        }
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}

