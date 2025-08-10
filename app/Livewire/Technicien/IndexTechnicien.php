<?php


namespace App\Livewire\Technicien;

use Livewire\Component;
use App\Models\Prescription;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class IndexTechnicien extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Démarrer l'analyse (changer statut à EN_COURS)
     */
    public function startAnalysis(int $prescriptionId)
    {
        try {
            DB::beginTransaction();
            
            $prescription = Prescription::findOrFail($prescriptionId);
            
            // Changer le statut à EN_COURS si ce n'est pas déjà le cas
            if ($prescription->status === 'EN_ATTENTE') {
                $prescription->update(['status' => 'EN_COURS']);
                
                Log::info('Prescription passée en cours', [
                    'prescription_id' => $prescriptionId,
                    'reference' => $prescription->reference,
                    'user_id' =>  Auth::id(),
                ]);
            }
            
            DB::commit();
            
            // Message de succès
            session()->flash('message', 'Analyse démarrée avec succès !');
            
            // Redirection vers la page de traitement
            return redirect()->route('technicien.prescription.show', $prescription);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erreur lors du démarrage de l\'analyse', [
                'prescription_id' => $prescriptionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            session()->flash('error', 'Erreur lors du démarrage de l\'analyse : ' . $e->getMessage());
        }
    }

    public function render()
    {
        $prescriptions = Prescription::query()
            ->with(['patient:id,nom,prenom', 'prescripteur:id,nom,prenom', 'analyses:id,designation'])
            ->whereIn('status', ['EN_ATTENTE', 'EN_COURS', 'TERMINE'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('reference', 'like', '%' . $this->search . '%')
                    ->orWhereHas('patient', function ($sq) {
                        $sq->where('nom', 'like', '%' . $this->search . '%')
                            ->orWhere('prenom', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('prescripteur', function ($sq) {
                        $sq->where('nom_complet', 'like', '%' . $this->search . '%');
                    });
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        // ✅ Stats en temps réel
        $stats = [
            'en_attente' => Prescription::where('status', 'EN_ATTENTE')->count(),
            'en_cours' => Prescription::where('status', 'EN_COURS')->count(),
            'termine' => Prescription::where('status', 'TERMINE')->count(),
        ];

        return view('livewire.technicien.index-technicien', [
            'prescriptions' => $prescriptions,
            'stats' => $stats,
        ]);
    }
}
