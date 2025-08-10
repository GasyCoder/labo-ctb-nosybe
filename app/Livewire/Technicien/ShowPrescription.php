<?php

namespace App\Livewire\Technicien;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use App\Models\Analyse;
use App\Models\Prescription;

class ShowPrescription extends Component
{
    public int $prescriptionId;
    
    #[Url(as: 'analyse')]
    public ?int $selectedAnalyseId = null;
    
    #[Url(as: 'parent')]
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

    /**
     * ✅ NOUVELLE MÉTHODE : Marquer une analyse comme terminée individuellement
     */
    #[On('analyseCompleted')]
    public function onAnalyseCompleted(int $parentId): void
    {
        // Rafraîchir la sidebar pour mettre à jour les statuts
        $this->dispatch('refreshSidebar')->to(AnalysesSidebar::class);
        
        // Message de succès
        session()->flash('message', 'Analyse marquée comme terminée avec succès !');
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Gérer la finalisation complète de la prescription
     */
    #[On('prescriptionCompleted')]
    public function onPrescriptionCompleted(): void
    {
        // Rediriger vers la liste des prescriptions
        $this->redirect(route('technicien.index'));
    }

    public function render()
    {
        $prescription = Prescription::with([
            'patient','prescripteur','analyses.parent','analyses.examen','analyses.type','resultats',
        ])->findOrFail($this->prescriptionId);

        return view('livewire.technicien.show-prescription', compact('prescription'));
    }
}