<?php

namespace App\Livewire\Secretaire\Prescription;

use App\Models\Patient;
use Livewire\Component;
use App\Models\Paiement;
use App\Models\Prescription;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\ResultatPdfShow;
use Illuminate\Support\Facades\DB;
use App\Models\AnalysePrescription;
use Illuminate\Support\Facades\Log;
use App\Services\ResultatPdfService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PrescriptionIndex extends Component
{
    use WithPagination, AuthorizesRequests;

    public ?Prescription $prescription = null;
    public $countArchive;
    
    // Propriétés pour les statistiques détaillées
    public $countEnAttente = 0;
    public $countEnCours = 0;
    public $countTermine = 0;
    public $countValide = 0;
    public $countDeleted = 0;
    
    // Nouvelles propriétés pour les statistiques de paiement
    public $countPaye = 0;
    public $countNonPaye = 0;

    // 🔥 NOUVELLE PROPRIÉTÉ POUR LE FILTRE DE PAIEMENT
    public $paymentFilter = null; // 'paye', 'non_paye', 'sans_paiement', ou null

    // 💰 PROPRIÉTÉS POUR CONFIRMATION PAIEMENT
    public $showConfirmPaymentModal = false;
    public $showConfirmUnpaymentModal = false;
    public $selectedPrescriptionForPayment = null;
    public $paymentAction = null; // 'pay' ou 'unpay'

    /**
     * Get count of active prescriptions (En attente + En cours + Terminé)
     */
    public function getCountActivesProperty()
    {
        return $this->countEnAttente + $this->countEnCours + $this->countTermine;
    }

    /**
     * Get progression statistics
     */
    public function getProgressionStats()
    {
        $totalActives = $this->countActives;
        return [
            'totalActives' => $totalActives,
            'termine' => $this->countTermine,
            'tauxProgression' => $totalActives > 0 ? round(($this->countTermine / $totalActives) * 100, 1) : 0
        ];
    }

    /**
     * Get efficiency statistics
     */
    public function getEfficiencyStats()
    {
        $totalGlobal = $this->countActives + $this->countValide;
        $completed = $this->countTermine + $this->countValide;
        
        return [
            'totalGlobal' => $totalGlobal,
            'completed' => $completed,
            'tauxEfficacite' => $totalGlobal > 0 ? round(($completed / $totalGlobal) * 100, 1) : 0
        ];
    }

    protected $queryString = [
        'search' => ['except' => ''],
        'tab' => ['except' => 'actives'],
        'paymentFilter' => ['except' => null], // 🔥 AJOUT DU FILTRE DANS QUERY STRING
    ];

    public $tab = 'actives';
    public $search = '';

    // Modal properties
    public $showDeleteModal = false;
    public $showRestoreModal = false;
    public $showPermanentDeleteModal = false;
    public $showArchiveModal = false;
    public $showUnarchiveModal = false;
    public $selectedPrescriptionId = null;

    protected $pdfService;

    public const STATUS_LABELS = [
        'EN_ATTENTE' => 'En attente',
        'EN_COURS' => 'En cours',
        'TERMINE' => 'Terminé',
        'VALIDE' => 'Validé',
        'A_REFAIRE' => 'À refaire',
        'ARCHIVE' => 'Archivé',
    ];

    protected $listeners = [
        'prescriptionAdded' => '$refresh',
    ];

    public function mount()
    {
        $this->tab = request()->query('tab', 'actives');
        $this->refreshCounts(); // Initialize all counts on mount
    }

    public function switchTab($tab)
    {
        $this->tab = $tab;
        $this->paymentFilter = null; // 🔥 RÉINITIALISER LE FILTRE LORS DU CHANGEMENT D'ONGLET
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function clearSearch()
    {
        $this->search = '';
        $this->resetPage();
    }

    // =====================================
    // 🔥 NOUVELLES MÉTHODES POUR LE FILTRE DE PAIEMENT
    // =====================================

    /**
     * Filtre les prescriptions par statut de paiement
     */
    public function filterByPaymentStatus($status)
    {
        // Toggle : si on clique sur le même filtre, on le désactive
        if ($status === 'tous' || $status === $this->paymentFilter) {
            $this->paymentFilter = null;
        } else {
            $this->paymentFilter = $status;
        }
        
        $this->resetPage(); // Réinitialiser la pagination
    }

    /**
     * Réinitialise le filtre de paiement
     */
    public function clearPaymentFilter()
    {
        $this->paymentFilter = null;
        $this->resetPage();
    }

    /**
     * Applique les filtres de paiement sur la requête
     */
    protected function applyPaymentFilter($query)
    {
        if (!$this->paymentFilter) {
            return $query;
        }

        switch ($this->paymentFilter) {
            case 'paye':
                // Prescriptions avec au moins un paiement marqué comme payé
                $query->whereHas('paiements', function($q) {
                    $q->where('status', true);
                });
                break;
                
            case 'non_paye':
                // Prescriptions avec au moins un paiement marqué comme non payé
                $query->whereHas('paiements', function($q) {
                    $q->where('status', false);
                });
                break;
                
            case 'sans_paiement':
                // Prescriptions sans aucun paiement enregistré
                $query->doesntHave('paiements');
                break;
        }

        return $query;
    }

    // =====================================
    // FIN DES NOUVELLES MÉTHODES
    // =====================================

    /**
     * Refresh all statistics counts including payment stats
     */
    public function refreshCounts()
    {
        // Comptes par statut de prescription
        $this->countEnAttente = Prescription::where('status', 'EN_ATTENTE')->count();
        $this->countEnCours = Prescription::where('status', 'EN_COURS')->count();
        $this->countTermine = Prescription::where('status', 'TERMINE')->count();
        $this->countValide = Prescription::where('status', 'VALIDE')->count();
        $this->countArchive = Prescription::where('status', 'ARCHIVE')->count();
        $this->countDeleted = Prescription::onlyTrashed()->count();
        
        // Comptes pour les paiements
        $this->refreshPaymentCounts();
        
        // Dispatch events for real-time updates if needed
        $this->dispatch('updateCounts', [
            'enAttente' => $this->countEnAttente,
            'enCours' => $this->countEnCours,
            'termine' => $this->countTermine,
            'valide' => $this->countValide,
            'archive' => $this->countArchive,
            'deleted' => $this->countDeleted,
            'paye' => $this->countPaye,
            'nonPaye' => $this->countNonPaye,
        ]);
    }

    /**
     * Refresh payment statistics
     */
    public function refreshPaymentCounts()
    {
        // Compter les paiements payés (status = 1 ou true)
        $this->countPaye = Paiement::whereHas('prescription', function($query) {
                $query->whereNull('deleted_at'); // Exclure les prescriptions supprimées
            })
            ->where('status', 1)
            ->count();

        // Compter les paiements non payés (status = 0 ou false)
        $this->countNonPaye = Paiement::whereHas('prescription', function($query) {
                $query->whereNull('deleted_at'); // Exclure les prescriptions supprimées
            })
            ->where('status', 0)
            ->count();
    }

    /**
     * Get payment statistics with more details
     */
    public function getPaymentStats()
    {
        return [
            'paye' => $this->countPaye,
            'nonPaye' => $this->countNonPaye,
            'total' => $this->countPaye + $this->countNonPaye,
            'tauxPaiement' => $this->countPaye + $this->countNonPaye > 0 
                ? round(($this->countPaye / ($this->countPaye + $this->countNonPaye)) * 100, 2)
                : 0
        ];
    }

    public function refreshArchiveCount()
    {
        $this->countArchive = Prescription::where('status', Prescription::STATUS_ARCHIVE)->count();
        $this->dispatch('updateArchiveCount', count: $this->countArchive);
    }

    public function resetModal()
    {
        $this->showDeleteModal = false;
        $this->showRestoreModal = false;
        $this->showPermanentDeleteModal = false;
        $this->showArchiveModal = false;
        $this->showUnarchiveModal = false;
        
        // Réinitialiser les modales de paiement
        $this->showConfirmPaymentModal = false;
        $this->showConfirmUnpaymentModal = false;
        $this->selectedPrescriptionForPayment = null;
        $this->paymentAction = null;
        
        $this->selectedPrescriptionId = null;
    }

    private function addStatusLabels($prescriptions)
    {
        $prescriptions->getCollection()->transform(function ($prescription) {
            $prescription->status_label = self::STATUS_LABELS[$prescription->status] ?? $prescription->status;
            return $prescription;
        });
    }

    public function edit($prescriptionId)
    {
        $this->dispatch('editPrescription', $prescriptionId);
    }

    // =====================================
    // 💰 GESTION DU STATUT DE PAIEMENT
    // =====================================
    
    public function togglePaiementStatus($prescriptionId)
    {
        try {
            $prescription = Prescription::with('paiements')->findOrFail($prescriptionId);
            $paiement = $prescription->paiements->first();
            
            if (!$paiement) {
                session()->flash('error', 'Aucun paiement trouvé pour cette prescription.');
                return;
            }
            
            // Déterminer l'action et ouvrir la modale appropriée
            if ($paiement->status) {
                // Actuellement payé, demander confirmation pour marquer comme non payé
                $this->confirmUnpayment($prescriptionId);
            } else {
                // Actuellement non payé, demander confirmation pour marquer comme payé
                $this->confirmPayment($prescriptionId);
            }
            
        } catch (\Exception $e) {
            Log::error('Erreur toggle paiement', [
                'prescription_id' => $prescriptionId,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Erreur lors de la vérification du statut de paiement.');
        }
    }

    /**
     * Demander confirmation pour marquer comme payé
     */
    public function confirmPayment($prescriptionId)
    {
        $this->selectedPrescriptionForPayment = $prescriptionId;
        $this->paymentAction = 'pay';
        $this->showConfirmPaymentModal = true;
    }

    /**
     * Exécuter le marquage comme payé après confirmation
     */
    public function executeMarquerCommePayé()
    {
        try {
            if (!$this->selectedPrescriptionForPayment) {
                $this->resetModal();
                return;
            }

            $prescription = Prescription::with('paiements')->findOrFail($this->selectedPrescriptionForPayment);
            $paiement = $prescription->paiements->first();
            
            if (!$paiement) {
                session()->flash('error', 'Aucun paiement trouvé pour cette prescription.');
                $this->resetModal();
                return;
            }
            
            $paiement->changerStatutPaiement(true);
            
            session()->flash('success', 'Paiement marqué comme payé avec succès. Date de paiement enregistrée automatiquement.');
            
            $this->refreshPaymentCounts();
            $this->dispatch('$refresh');
            $this->resetModal();
            
        } catch (\Exception $e) {
            Log::error('Erreur marquage paiement payé', [
                'prescription_id' => $this->selectedPrescriptionForPayment,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Erreur lors du marquage du paiement comme payé.');
            $this->resetModal();
        }
    }

    /**
     * Exécuter le marquage comme non payé après confirmation
     */
    public function executeMarquerCommeNonPayé()
    {
        try {
            if (!$this->selectedPrescriptionForPayment) {
                $this->resetModal();
                return;
            }

            $prescription = Prescription::with('paiements')->findOrFail($this->selectedPrescriptionForPayment);
            $paiement = $prescription->paiements->first();
            
            if (!$paiement) {
                session()->flash('error', 'Aucun paiement trouvé pour cette prescription.');
                $this->resetModal();
                return;
            }
            
            $paiement->changerStatutPaiement(false);
            
            session()->flash('success', 'Paiement marqué comme non payé avec succès. Date de paiement supprimée.');
            
            $this->refreshPaymentCounts();
            $this->dispatch('$refresh');
            $this->resetModal();
            
        } catch (\Exception $e) {
            Log::error('Erreur marquage paiement non payé', [
                'prescription_id' => $this->selectedPrescriptionForPayment,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Erreur lors du marquage du paiement comme non payé.');
            $this->resetModal();
        }
    }

    /**
     * Demander confirmation pour marquer comme non payé
     */
    public function confirmUnpayment($prescriptionId)
    {
        $this->selectedPrescriptionForPayment = $prescriptionId;
        $this->paymentAction = 'unpay';
        $this->showConfirmUnpaymentModal = true;
    }

    /**
     * Marquer un paiement comme payé (avec date automatique)
     */
    public function marquerCommePayé($prescriptionId)
    {
        try {
            $prescription = Prescription::with('paiements')->findOrFail($prescriptionId);
            $paiement = $prescription->paiements->first();
            
            if (!$paiement) {
                session()->flash('error', 'Aucun paiement trouvé pour cette prescription.');
                return;
            }
            
            $paiement->marquerCommePayé();
            
            session()->flash('success', 'Paiement marqué comme payé avec succès. Date de paiement enregistrée.');
            
            $this->refreshPaymentCounts();
            $this->dispatch('$refresh');
            
        } catch (\Exception $e) {
            Log::error('Erreur marquage paiement payé', [
                'prescription_id' => $prescriptionId,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Erreur lors du marquage du paiement.');
        }
    }

    /**
     * Marquer un paiement comme non payé (supprime la date)
     */
    public function marquerCommeNonPayé($prescriptionId)
    {
        try {
            $prescription = Prescription::with('paiements')->findOrFail($prescriptionId);
            $paiement = $prescription->paiements->first();
            
            if (!$paiement) {
                session()->flash('error', 'Aucun paiement trouvé pour cette prescription.');
                return;
            }
            
            $paiement->marquerCommeNonPayé();
            
            session()->flash('success', 'Paiement marqué comme non payé avec succès.');
            
            $this->refreshPaymentCounts();
            $this->dispatch('$refresh');
            
        } catch (\Exception $e) {
            Log::error('Erreur marquage paiement non payé', [
                'prescription_id' => $prescriptionId,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Erreur lors du marquage du paiement.');
        }
    }

    // Confirmation Methods
    public function confirmDelete($prescriptionId)
    {
        $this->selectedPrescriptionId = $prescriptionId;
        $this->showDeleteModal = true;
    }

    public function confirmRestore($prescriptionId)
    {
        $this->selectedPrescriptionId = $prescriptionId;
        $this->showRestoreModal = true;
    }

    public function confirmPermanentDelete($prescriptionId)
    {
        if (!Auth::user()->isAdmin()) {
            session()->flash('error', 'Seuls les administrateurs peuvent supprimer définitivement.');
            return;
        }

        $this->selectedPrescriptionId = $prescriptionId;
        $this->showPermanentDeleteModal = true;
    }

    public function confirmArchive($prescriptionId)
    {
        $this->selectedPrescriptionId = $prescriptionId;
        $this->showArchiveModal = true;
    }

    public function confirmUnarchive($prescriptionId)
    {
        $this->selectedPrescriptionId = $prescriptionId;
        $this->showUnarchiveModal = true;
    }

    // Execution Methods
    public function deletePrescription()
    {
        try {
            if (!$this->selectedPrescriptionId) {
                return;
            }

            $prescription = Prescription::findOrFail($this->selectedPrescriptionId);
            $prescription->delete();

            session()->flash('success', 'Prescription mise en corbeille avec succès.');
            $this->resetModal();
            $this->refreshCounts(); // Refresh all counts including payment counts
            $this->dispatch('$refresh');

        } catch (\Exception $e) {
            Log::error('Erreur suppression prescription', [
                'prescription_id' => $this->selectedPrescriptionId,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Une erreur est survenue lors de la mise en corbeille.');
            $this->resetModal();
        }
    }

    public function restorePrescription()
    {
        try {
            if (!$this->selectedPrescriptionId) {
                return;
            }

            $prescription = Prescription::withTrashed()->findOrFail($this->selectedPrescriptionId);
            $prescription->restore();

            session()->flash('success', 'Prescription restaurée avec succès.');
            $this->resetModal();
            $this->refreshCounts(); // Refresh all counts including payment counts
            $this->dispatch('$refresh');

        } catch (\Exception $e) {
            Log::error('Erreur restauration prescription', [
                'prescription_id' => $this->selectedPrescriptionId,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Erreur lors de la restauration : ' . $e->getMessage());
            $this->resetModal();
        }
    }

    public function permanentDeletePrescription()
    {
        try {
            if (!Auth::user()->isAdmin()) {
                session()->flash('error', 'Action non autorisée.');
                return;
            }

            if (!$this->selectedPrescriptionId) {
                return;
            }

            $prescription = Prescription::withTrashed()->findOrFail($this->selectedPrescriptionId);
            $prescription->forceDelete();

            session()->flash('success', 'Prescription supprimée définitivement.');
            $this->resetModal();
            $this->refreshCounts(); // Refresh all counts including payment counts
            $this->dispatch('$refresh');

        } catch (\Exception $e) {
            Log::error('Erreur suppression définitive prescription', [
                'prescription_id' => $this->selectedPrescriptionId,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Erreur lors de la suppression définitive : ' . $e->getMessage());
            $this->resetModal();
        }
    }

    public function archivePrescription()
    {
        try {
            if (!$this->selectedPrescriptionId) {
                return;
            }

            $prescription = Prescription::findOrFail($this->selectedPrescriptionId);

            if ($prescription->status === 'VALIDE') {
                $prescription->update(['status' => 'ARCHIVE']);
                session()->flash('success', 'Prescription archivée avec succès.');
                $this->refreshCounts(); // Refresh all counts
            } else {
                session()->flash('error', 'Seules les prescriptions validées peuvent être archivées.');
            }

            $this->resetModal();
            $this->dispatch('$refresh');

        } catch (\Exception $e) {
            Log::error('Erreur archivage prescription', [
                'prescription_id' => $this->selectedPrescriptionId,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Erreur lors de l\'archivage : ' . $e->getMessage());
            $this->resetModal();
        }
    }

    public function unarchivePrescription()
    {
        try {
            if (!$this->selectedPrescriptionId) {
                return;
            }

            $prescription = Prescription::findOrFail($this->selectedPrescriptionId);
            $prescription->update(['status' => 'VALIDE']);
            session()->flash('success', 'Prescription désarchivée avec succès.');
            $this->refreshCounts(); // Refresh all counts

            $this->resetModal();
            $this->dispatch('$refresh');

        } catch (\Exception $e) {
            Log::error('Erreur désarchivage prescription', [
                'prescription_id' => $this->selectedPrescriptionId,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Erreur lors du désarchivage : ' . $e->getMessage());
            $this->resetModal();
        }
    }

    public function generateResultatsPDF($prescriptionId)
    {
        try {
            $prescription = Prescription::findOrFail($prescriptionId);
            return $this->pdfService->generatePDF($prescription);

        } catch (\Exception $e) {
            Log::error('Erreur génération PDF:', [
                'message' => $e->getMessage(),
                'prescription_id' => $prescriptionId,
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', "Erreur lors de la génération du PDF : {$e->getMessage()}");
            return null;
        }
    }

    public function render()
    {
        $search = '%' . $this->search . '%';

        $baseQuery = Prescription::with([
            'patient:id,nom,prenom,telephone',
            'prescripteur:id,nom',
            'analyses',
            'resultats',
            'paiements.paymentMethod',
            'paiements.utilisateur:id,name'
        ])
            ->whereHas('patient', fn($q) => $q->whereNull('deleted_at'));

        $searchCondition = function ($query) use ($search) {
            $query->where('renseignement_clinique', 'like', $search)
                ->orWhere('status', 'like', $search)
                ->orWhere('reference', 'like', $search)
                ->orWhereHas('patient', function ($q) use ($search) {
                    $q->where('nom', 'like', $search)
                        ->orWhere('prenom', 'like', $search)
                        ->orWhere('telephone', 'like', $search);
                })
                ->orWhereHas('prescripteur', function ($q) use ($search) {
                    $q->where('nom', 'like', $search)
                        ->where('is_active', true);
                });
        };

        // 🔥 AJOUT DU FILTRE DE PAIEMENT DANS LES REQUÊTES
        $activePrescriptions = (clone $baseQuery)
            ->whereIn('status', ['EN_ATTENTE', 'EN_COURS', 'TERMINE'])
            ->where($searchCondition);
        
        // Appliquer le filtre de paiement
        $activePrescriptions = $this->applyPaymentFilter($activePrescriptions);
        
        $activePrescriptions = $activePrescriptions
            ->latest()
            ->paginate(15);

        $validePrescriptions = (clone $baseQuery)
            ->where('status', 'VALIDE')
            ->where($searchCondition);
        
        // Appliquer le filtre de paiement
        $validePrescriptions = $this->applyPaymentFilter($validePrescriptions);
        
        $validePrescriptions = $validePrescriptions
            ->latest()
            ->paginate(15, ['*'], 'valide_page');

        $deletedPrescriptions = (clone $baseQuery)
            ->onlyTrashed()
            ->where($searchCondition);
        
        // Appliquer le filtre de paiement
        $deletedPrescriptions = $this->applyPaymentFilter($deletedPrescriptions);
        
        $deletedPrescriptions = $deletedPrescriptions
            ->latest()
            ->paginate(15, ['*'], 'deleted_page');

        $this->addStatusLabels($activePrescriptions);
        $this->addStatusLabels($validePrescriptions);
        $this->addStatusLabels($deletedPrescriptions);

        return view('livewire.secretaire.prescription.prescription-index', [
            'activePrescriptions' => $activePrescriptions,
            'validePrescriptions' => $validePrescriptions,
            'deletedPrescriptions' => $deletedPrescriptions,
            'countArchive' => $this->countArchive,
            // Statistiques individuelles
            'countEnAttente' => $this->countEnAttente,
            'countEnCours' => $this->countEnCours,
            'countTermine' => $this->countTermine,
            'countValide' => $this->countValide,
            'countDeleted' => $this->countDeleted,
            // Statistiques de paiement
            'countPaye' => $this->countPaye,
            'countNonPaye' => $this->countNonPaye,
            'paymentStats' => $this->getPaymentStats(),
            // Nouvelles statistiques calculées
            'countActives' => $this->countActives,
            'progressionStats' => $this->getProgressionStats(),
            'efficiencyStats' => $this->getEfficiencyStats(),
        ]);
    }
}