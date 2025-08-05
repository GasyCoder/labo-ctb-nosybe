<?php

namespace App\Livewire\Secretaire\Prescription;

use App\Models\Prescription;
use Livewire\Component;
use Livewire\WithPagination;

class PrescriptionIndex extends Component
{
    use WithPagination;

    public $search = '';

    // Define status labels for display
    public const STATUS_LABELS = [
        'EN_ATTENTE' => 'En attente',
        'EN_COURS' => 'En cours',
        'TERMINE' => 'Terminé',
        'VALIDE' => 'Validé',
        'A_REFAIRE' => 'À refaire',
        'ARCHIVE' => 'Archivé',
        'PRELEVEMENTS_GENERES' => 'Prélèvements générés',
    ];

    public function render()
    {
        $prescriptions = Prescription::query()
            ->with(['patient', 'prescripteur'])
            ->when($this->search, function ($query) {
                $query->whereHas('patient', function ($q) {
                    $q->where('nom', 'like', '%' . $this->search . '%')
                      ->orWhere('prenom', 'like', '%' . $this->search . '%')
                      ->orWhere('reference', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('prescripteur', function ($q) {
                    $q->where('nom', 'like', '%' . $this->search . '%');
                })
                ->orWhere('id', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Add status labels to prescriptions
        $prescriptions->getCollection()->transform(function ($prescription) {
            $prescription->status_label = self::STATUS_LABELS[$prescription->status] ?? $prescription->status;
            return $prescription;
        });

        return view('livewire.secretaire.prescription.prescription-index', [
            'prescriptions' => $prescriptions,
        ]);
    }
}