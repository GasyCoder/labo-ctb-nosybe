<?php

namespace App\Livewire\Biologiste;

use Livewire\Component;
use App\Models\Prescription;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Dashboard extends Component
{

    public $stats = [
        'total_termine' => 0,
        'total_valide' => 0,
        'urgences_nuit' => 0,
        'urgences_jour' => 0,
    ];

    public $search = '';
    public $perPage = 5;
    public $prescriptions;

    protected $queryString = [
        'search' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function mount()
    {
        $this->loadStatistics();
        $this->loadPrescriptions();
    }

    public function updatedSearch()
    {
        $this->resetPage();
        $this->loadPrescriptions();
    }

    private function loadStatistics()
    {
        $this->stats = [
            'total_termine' => Prescription::where('status', Prescription::STATUS_TERMINE)->count(),
            'total_valide' => Prescription::where('status', Prescription::STATUS_VALIDE)->count(),
            'urgences_nuit' => Prescription::where('patient_type', 'URGENCE-NUIT')
                ->whereIn('status', [Prescription::STATUS_TERMINE, Prescription::STATUS_VALIDE])
                ->count(),
            'urgences_jour' => Prescription::where('patient_type', 'URGENCE-JOUR')
                ->whereIn('status', [Prescription::STATUS_TERMINE, Prescription::STATUS_VALIDE])
                ->count(),
        ];
    }

    private function loadPrescriptions()
    {
        $search = '%' . $this->search . '%';

        $this->prescriptions = Prescription::with(['patient', 'prescripteur:id,nom,prenom'])
            ->where('status', Prescription::STATUS_TERMINE)
            ->where(function ($query) use ($search) {
                $query->where('reference', 'like', $search)
                    ->orWhereHas('patient', function ($subQ) use ($search) {
                        $subQ->where('nom', 'like', $search)
                            ->orWhere('prenom', 'like', $search);
                    })
                    ->orWhereHas('prescripteur', function ($subQ) use ($search) {
                        $subQ->where('nom', 'like', $search);
                    });
            })
            ->latest()
            ->paginate($this->perPage);
    }

    public function viewAnalyseDetails($prescriptionId)
    {
        try {
            $prescription = Prescription::findOrFail($prescriptionId);
            return redirect()->route('biologiste.valide.show', $prescription);
        } catch (\Exception $e) {
            $this->alert('error', 'Impossible d\'ouvrir cette analyse');
        }
    }

    public function render()
    {
        return view('livewire.biologiste.dashboard');
    }
}