<?php

namespace App\Livewire\Technicien;

use Livewire\Component;
use App\Models\Prescription;
use App\Models\Prescripteur;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class IndexTechnicien extends Component
{
    use WithPagination;

    // Propriétés de navigation (nouvelles)
    public $activeTab = 'en_attente';
    
    // Propriétés de recherche et filtres (adaptées)
    public $search = '';
    public $dateFilter = '';
    public $prescripteurFilter = '';
    public $typeAnalyseFilter = '';
    public $prioriteFilter = '';
    public $ageFilter = '';
    public $showAdvancedFilters = false;
    
    // Propriétés de tri (existantes)
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'activeTab' => ['except' => 'en_attente'],
        'search' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    protected $listeners = [
        'refreshData' => '$refresh',
        'prescriptionUpdated' => 'handlePrescriptionUpdate'
    ];

    public function mount()
    {
        $this->activeTab = 'en_attente';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    public function updatingPrescripteurFilter()
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
        $this->resetPage();
    }

    public function toggleAdvancedFilters()
    {
        $this->showAdvancedFilters = !$this->showAdvancedFilters;
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->dateFilter = '';
        $this->prescripteurFilter = '';
        $this->typeAnalyseFilter = '';
        $this->prioriteFilter = '';
        $this->ageFilter = '';
        $this->resetPage();
    }

    public function startAnalysis($prescriptionId)
    {
        try {
            DB::beginTransaction();
            
            $prescription = Prescription::findOrFail($prescriptionId);
            
            // Vérifier que la prescription est bien en attente
            if ($prescription->status !== 'EN_ATTENTE') {
                session()->flash('error', 'Cette prescription ne peut pas être traitée.');
                DB::rollBack();
                return;
            }
            
            // Changer le statut à EN_COURS
            $prescription->update([
                'status' => 'EN_COURS',
                'technicien_id' => auth()->id(),
                'date_debut_traitement' => now()
            ]);
            
            Log::info('Prescription passée en cours', [
                'prescription_id' => $prescriptionId,
                'reference' => $prescription->reference,
                'user_id' => Auth::id(),
            ]);
            
            DB::commit();
            
            // Message de succès
            session()->flash('message', 'Traitement de la prescription ' . $prescription->reference . ' commencé.');
            
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

    public function continueAnalysis($prescriptionId)
    {
        try {
            $prescription = Prescription::findOrFail($prescriptionId);
            
            // Vérifier que la prescription est bien en cours
            if ($prescription->status !== 'EN_COURS') {
                session()->flash('error', 'Cette prescription ne peut pas être continuée.');
                return;
            }
            
            // Redirection vers la page de traitement
            return redirect()->route('technicien.prescription.show', $prescription);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la continuation de l\'analyse', [
                'prescription_id' => $prescriptionId,
                'error' => $e->getMessage(),
            ]);
            
            session()->flash('error', 'Erreur lors de la continuation de l\'analyse : ' . $e->getMessage());
        }
    }

    public function viewResults($prescriptionId)
    {
        return redirect()->route('technicien.voir-resultats', $prescriptionId);
    }

    public function redoAnalysis($prescriptionId)
    {
        try {
            DB::beginTransaction();

            $prescription = Prescription::findOrFail($prescriptionId);

            if (!$prescription) {
                session()->flash('error', 'Prescription introuvable.');
                DB::rollBack();
                return;
            }

            $prescription->update([
                'status' => 'EN_COURS',
                'technicien_id' => auth()->id(),
                'commentaire_biologiste' => null,
                'date_debut_traitement' => now(),
                'date_reprise_traitement' => now()
            ]);

            Log::info('Prescription relancée pour un nouveau traitement', [
                'prescription_id' => $prescriptionId,
                'reference' => $prescription->reference,
                'user_id' => Auth::id(),
            ]);

            DB::commit();

            session()->flash('message', 'La prescription ' . $prescription->reference . ' a été relancée pour un nouveau traitement.');

            return redirect()->route('technicien.prescription.show', $prescription);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur lors du recommencement de l\'analyse', [
                'prescription_id' => $prescriptionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Erreur lors du recommencement : ' . $e->getMessage());
        }
    }

    public function exportData()
    {
        $fileName = 'analyses_' . $this->activeTab . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        session()->flash('message', 'Export des données en cours...');
    }

    public function handlePrescriptionUpdate($prescriptionId)
    {
        $this->dispatch('refreshData');
    }

    private function getBaseQuery()
    {
        $query = Prescription::with(['patient:id,nom,prenom', 'prescripteur:id,nom,prenom', 'analyses:id,designation'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('reference', 'like', '%' . $this->search . '%')
                        ->orWhereHas('patient', function ($sq) {
                            $sq->where('nom', 'like', '%' . $this->search . '%')
                                ->orWhere('prenom', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('prescripteur', function ($sq) {
                            $sq->where('nom', 'like', '%' . $this->search . '%')
                                ->orWhere('prenom', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->prescripteurFilter, function ($q) {
                $q->where('prescripteur_id', $this->prescripteurFilter);
            })
            ->when($this->dateFilter, function ($q) {
                switch ($this->dateFilter) {
                    case 'today':
                        $q->whereDate('created_at', today());
                        break;
                    case 'yesterday':
                        $q->whereDate('created_at', today()->subDay());
                        break;
                    case 'this_week':
                        $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'this_month':
                        $q->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                        break;
                }
            });

        return $query->orderBy($this->sortField, $this->sortDirection);
    }

    // MODIFICATION PRINCIPALE : L'onglet "En attente" affiche maintenant EN_ATTENTE ET EN_COURS
    public function getPrescriptionsToutesProperty()
    {
        return $this->getBaseQuery()
            ->whereIn('status', ['EN_ATTENTE', 'EN_COURS'])
            ->paginate(15, ['*'], 'page-toutes');
    }

    public function getPrescriptionsTermineesProperty()
    {
        return $this->getBaseQuery()
            ->where('status', 'TERMINE')
            ->paginate(15, ['*'], 'page-termine');
    }

    public function getPrescriptionsARefaireProperty()
    {
        return $this->getBaseQuery()
            ->where('status', 'A_REFAIRE')
            ->paginate(15, ['*'], 'page-À-refaire');
    }

    public function getStatsProperty()
    {
        $stats = [
            'en_attente' => Prescription::where('status', 'EN_ATTENTE')->count(),
            'en_cours' => Prescription::where('status', 'EN_COURS')->count(),
            'termine' => Prescription::where('status', 'TERMINE')->count(),
            'a_refaire'=> Prescription::where('status', 'A_REFAIRE')->count(),
        ];
        
        // Pour l'onglet "En attente" on combine EN_ATTENTE + EN_COURS
        $stats['toutes'] = $stats['en_attente'] + $stats['en_cours'];
        $stats['total'] = array_sum($stats);
        
        return $stats;
    }

    public function getPrescriteursProperty()
    {
        return Prescripteur::orderBy('nom')->get();
    }

    public function render()
    {
        $data = [
            'stats' => $this->stats,
        ];

        // Ajouter les prescriptions selon l'onglet actif
        switch ($this->activeTab) {
            case 'en_attente':
                $data['prescriptionsToutes'] = $this->prescriptionsToutes;
                break;
            case 'termine':
                $data['prescriptionsTerminees'] = $this->prescriptionsTerminees;
                break;
            case 'a_refaire':
                $data['prescriptionsARefaire'] = $this->prescriptionsARefaire;
                break;
        }

        return view('livewire.technicien.index-technicien', $data);
    }
}