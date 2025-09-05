<?php

namespace App\Livewire\Technicien;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use App\Models\Analyse;
use App\Models\Prescription;
use Illuminate\Support\Facades\Auth;

class ShowPrescription extends Component
{
    public int $prescriptionId;

    #[Url(as: 'analyse')]
    public ?int $selectedAnalyseId = null;

    #[Url(as: 'parent')]
    public ?int $selectedParentId = null;

    public string $viewMode = 'view'; // 'edit' for technicien, 'view' for biologiste

    public function mount(Prescription $prescription)
    {
        $this->prescriptionId = $prescription->id;
        $userType = Auth::user()->type;
        $this->viewMode = ($userType === 'technicien') ? 'edit' : 'view';
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
     * Mark prescription as "to redo"
     */
    public function markAsToRedo()
    {
        if (!$this->canValidate()) {
            return;
        }

        $prescription = Prescription::findOrFail($this->prescriptionId);
        $prescription->update(['status' => 'A_REFAIRE']);

        session()->flash('message', 'Prescription marquée comme "À refaire"');
        $this->redirect(route('biologiste.analyse.index'));
    }

    /**
     * Validate prescription
     */
    public function validatePrescription()
    {
        if (!$this->canValidate()) {
            return;
        }

        $prescription = Prescription::findOrFail($this->prescriptionId);
        $prescription->update(['status' => 'VALIDE']);

        session()->flash('message', 'Prescription validée avec succès');
        $this->redirect(route('biologiste.analyse.index'));
    }

    #[On('analyseCompleted')]
    public function onAnalyseCompleted(int $parentId): void
    {
        if ($this->viewMode !== 'edit') {
            return;
        }

        $this->dispatch('refreshSidebar')->to(AnalysesSidebar::class);
        session()->flash('message', 'Analyse marquée comme terminée avec succès !');
    }

    #[On('prescriptionCompleted')]
    public function onPrescriptionCompleted(): void
    {
        if ($this->viewMode !== 'edit') {
            return;
        }

        if (Auth::user()->type === 'technicien') {
            $this->redirect(route('technicien.index'));
        } elseif (Auth::user()->type === 'biologiste') {
            $this->redirect(route('biologiste.analyse.index'));
        }
    }

    public function startAnalysis($prescriptionId)
    {
        $prescription = Prescription::findOrFail($prescriptionId);
        
        // ✅ Mettre EN_COURS quand on clique "Traiter"
        if ($prescription->status === 'EN_ATTENTE') {
            $prescription->update(['status' => 'EN_COURS']);
        }

        $userType = Auth::user()->type;

        if ($userType === 'technicien') {
            return redirect()->route('technicien.prescription.show', $prescriptionId);
        } 
    }

    public function canEdit(): bool
    {
        return $this->viewMode === 'edit' && Auth::user()->type === 'technicien';
    }

    public function canValidate(): bool
    {
        return Auth::user()->type === 'biologiste';
    }

    public function render()
    {
        $prescription = Prescription::with([
            'patient',
            'prescripteur',
            'analyses.parent',
            'analyses.examen',
            'analyses.type',
            'resultats',
        ])->findOrFail($this->prescriptionId);

        return view('livewire.technicien.show-prescription', compact('prescription'))
            ->with([
                'viewMode' => $this->viewMode,
                'canEdit' => $this->canEdit(),
                'canValidate' => $this->canValidate(),
            ]);
    }
}