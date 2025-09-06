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

    // Ancien filtre de statut remplacé par activeTab
    // public $statusFilter = '';

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

    /**
     * ✅ MÉTHODE ADAPTÉE : Démarrer l'analyse (votre méthode originale avec améliorations)
     */
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

    /**
     * ✅ NOUVELLES MÉTHODES : Issues du deuxième code
     */
    public function viewResults($prescriptionId)
    {
        return redirect()->route('technicien.voir-resultats', $prescriptionId);
    }

    public function redoAnalysis($prescriptionId)
    {
        try {
            $prescription = Prescription::findOrFail($prescriptionId);
            
            // Remettre en cours de traitement
            $prescription->update([
                'status' => 'EN_ATTENTE',
                'technicien_id' => auth()->id(),
                'commentaire_biologiste' => null,
                'date_reprise_traitement' => now()
            ]);
            
            session()->flash('message', 'Reprise du traitement pour la prescription ' . $prescription->reference);
            
            // Rediriger vers la page de saisie des résultats
            return redirect()->route('technicien.saisie-resultats', $prescription->id);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la reprise du traitement: ' . $e->getMessage());
        }
    }

    public function exportData()
    {
        // Logique d'export selon l'onglet actif
        $fileName = 'analyses_' . $this->activeTab . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        // Ici vous pouvez implémenter la logique d'export
        session()->flash('message', 'Export des données en cours...');
    }

    public function handlePrescriptionUpdate($prescriptionId)
    {
        // Rafraîchir les données après mise à jour
        $this->dispatch('refreshData');
    }

    /**
     * ✅ MÉTHODE ADAPTÉE : Requête de base avec vos filtres originaux + nouveaux
     */
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

    /**
     * ✅ PROPRIÉTÉS CALCULÉES : Remplacent l'ancienne méthode render()
     */
    public function getPrescriptionsEnAttenteProperty()
    {
        return $this->getBaseQuery()
            ->where('status', 'EN_ATTENTE')
            ->paginate(15, ['*'], 'page-en-attente');
    }

    public function getPrescriptionsEnCoursProperty()
    {
        return $this->getBaseQuery()
            ->where('status', 'EN_COURS')
            ->paginate(15, ['*'], 'page-en-cours');
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

    /**
     * ✅ STATS ADAPTÉES : Vos stats originales
     */
    public function getStatsProperty()
    {
        $stats = [
            'en_attente' => Prescription::where('status', 'EN_ATTENTE')->count(),
            'en_cours' => Prescription::where('status', 'EN_COURS')->count(),
            'termine' => Prescription::where('status', 'TERMINE')->count(),
            'a_refaire'=> Prescription::where('status', 'A_REFAIRE')->count(),
        ];
        
        $stats['total'] = array_sum($stats);
        
        return $stats;
    }

    public function getPrescriteursProperty()
    {
        return Prescripteur::orderBy('nom')->get();
    }

    /**
     * ✅ MÉTHODE RENDER ADAPTÉE : Utilise la nouvelle interface
     */
    public function render()
    {
        $data = [
            'stats' => $this->stats,
            // 'prescripteurs' => $this->prescripteurs,
        ];

        // Ajouter les prescriptions selon l'onglet actif
        switch ($this->activeTab) {
            case 'en_attente':
            case 'en_cours':
                $data['prescriptionsEnAttente'] = $this->prescriptionsEnAttente;
                $data['prescriptionsEnCours'] = $this->prescriptionsEnCours;
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