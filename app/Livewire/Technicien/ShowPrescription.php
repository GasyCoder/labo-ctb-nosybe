<?php

namespace App\Livewire\Technicien;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Analyse;
use App\Models\Prescription;

class ShowPrescription extends Component
{
    public int $prescriptionId;
    public ?int $selectedAnalyseId = null;
    public ?int $selectedParentId = null;

    public function mount(Prescription $prescription)
    {
        $this->prescriptionId = $prescription->id;
    }

    #[On('analyseSelected')]
    public function selectAnalyse(int $analyseId): void
    {
        $this->selectedAnalyseId = $analyseId;
        $this->selectedParentId = null;
    }

    #[On('parentSelected')]
    public function onParentSelected(int $parentId): void
    {
        $this->selectedParentId = $parentId;
        $this->selectedAnalyseId = null;
    }

    public function render()
    {
        $prescription = Prescription::with([
            'patient','prescripteur','analyses.parent','analyses.examen','analyses.type','resultats',
        ])->findOrFail($this->prescriptionId);

        return view('livewire.technicien.show-prescription', compact('prescription'));
    }
}