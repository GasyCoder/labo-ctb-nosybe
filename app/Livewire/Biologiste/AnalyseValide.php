<?php

namespace App\Livewire\Biologiste;

use Livewire\Component;
use App\Models\Resultat;
use App\Models\Prescription;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Models\Analyse;
use App\Models\AnalysePrescription;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AnalyseValide extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // Propriétés principales
    public $tab = 'termine';
    public $search = '';
    public $perPage = 20;

    // Filtres
    public $filterPrescripteur = '';
    public $filterUrgence = '';
    public $showFilters = false;

    // Sélection multiple
    public $selectedPrescriptions = [];
    public $selectAll = false;

    // Statistiques
    public $stats = [
        'total_termine' => 0,
        'total_valide' => 0,
        'urgences_nuit' => 0,
        'urgences_jour' => 0,
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'tab' => ['except' => 'termine'],
        'page' => ['except' => 1],
    ];

    protected $listeners = [
        'refreshComponent' => '$refresh',
    ];

    public function mount()
    {
        $this->loadStatistics();
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatedTab()
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedPrescriptions = $this->getCurrentPrescriptions()->pluck('id')->toArray();
        } else {
            $this->selectedPrescriptions = [];
        }
    }

    public function resetSelection()
    {
        $this->selectedPrescriptions = [];
        $this->selectAll = false;
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function resetFilters()
    {
        $this->filterPrescripteur = '';
        $this->filterUrgence = '';
        $this->showFilters = false;
        $this->resetPage();
    }

    /**
     * ✅ NOUVEAU : Consulter les résultats du technicien avant validation
     */
    public function viewAnalyseDetails($prescriptionId)
    {
        try {
            $prescription = Prescription::findOrFail($prescriptionId);

            // Redirection vers la page de consultation des résultats
            // Redirection vers la page de traitement
            return redirect()->route('technicien.prescription.show', $prescription);
        } catch (\Exception $e) {
            $this->alert('error', 'Impossible d\'ouvrir cette analyse');
        }
    }

    /**
     * ✅ CORRIGÉ : Valider une analyse (TERMINE → VALIDE)
     */
    public function validateAnalyse(int $prescriptionId)
    {
        try {
            DB::beginTransaction();

            $prescription = Prescription::findOrFail($prescriptionId);

            // Vérifier que l'analyse est bien terminée par le technicien
            if ($prescription->status !== Prescription::STATUS_EN_COURS) {
                throw new \Exception('Cette analyse doit être terminée par le technicien avant validation');
            }

            // Valider l'analyse
            $this->validateSingleAnalyse($prescriptionId);

            DB::commit();

            $this->loadStatistics();
            $this->alert('success', 'Analyse validée avec succès !');

            Log::info('Analyse validée par biologiste', [
                'prescription_id' => $prescriptionId,
                'reference' => $prescription->reference,
                'biologiste_id' => Auth::id(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur lors de la validation de l\'analyse', [
                'prescription_id' => $prescriptionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->alert('error', 'Erreur lors de la validation : ' . $e->getMessage());
        }
    }

    /**
     * ✅ AMÉLIORÉ : Remettre à refaire avec commentaire
     */
    public function redoPrescription($prescriptionId, $commentaire = null)
    {
        try {
            DB::beginTransaction();

            $prescription = Prescription::findOrFail($prescriptionId);

            if (!in_array($prescription->status, [Prescription::STATUS_VALIDE, Prescription::STATUS_EN_COURS])) {
                throw new \Exception('Cette prescription ne peut pas être remise à refaire');
            }

            // Mettre à jour le statut de la prescription
            $prescription->update([
                'status' => Prescription::STATUS_A_REFAIRE,
                'commentaire_biologiste' => $commentaire,
                'updated_by' => Auth::id()
            ]);

            // Mettre à jour toutes les analyses de la prescription
            foreach ($prescription->analyses as $analyse) {
                AnalysePrescription::where([
                    'prescription_id' => $prescriptionId,
                    'analyse_id' => $analyse->id
                ])->update([
                            'status' => AnalysePrescription::STATUS_A_REFAIRE,
                            'updated_at' => now()
                        ]);
            }

            // Réinitialiser les résultats
            Resultat::where('prescription_id', $prescriptionId)
                ->update([
                    'validated_by' => null,
                    'validated_at' => null,
                    'status' => 'EN_ATTENTE',
                    'updated_at' => now()
                ]);

            DB::commit();

            $this->loadStatistics();
            $this->alert('success', 'La prescription a été marquée à refaire');

            Log::info('Prescription remise à refaire par biologiste', [
                'prescription_id' => $prescriptionId,
                'commentaire' => $commentaire,
                'biologiste_id' => Auth::id(),
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de la mise à refaire:', [
                'message' => $e->getMessage(),
                'prescription_id' => $prescriptionId,
                'user_id' => Auth::id()
            ]);

            $this->alert('error', 'Erreur lors de la mise à refaire de la prescription');
        }
    }

  
    public function bulkValidate()
    {
        if (empty($this->selectedPrescriptions)) {
            $this->alert('warning', 'Veuillez sélectionner au moins une prescription');
            return;
        }

        try {
            DB::beginTransaction();

            $count = 0;
            foreach ($this->selectedPrescriptions as $prescriptionId) {
                $prescription = Prescription::find($prescriptionId);
                if ($prescription && $prescription->status === Prescription::STATUS_EN_COURS) {
                    if ($this->validateSingleAnalyse($prescriptionId)) {
                        $count++;
                    }
                }
            }

            DB::commit();

            $this->resetSelection();
            $this->loadStatistics();

            $this->alert('success', "{$count} analyse(s) validée(s) avec succès");

        } catch (\Exception $e) {
            DB::rollback();
            $this->alert('error', 'Erreur lors de la validation en lot');
        }
    }

    public function render()
    {
        $search = '%' . $this->search . '%';

        $baseQuery = Prescription::with([
            'patient',
            'prescripteur:id,nom,prenom,is_active',
            'analyses',
            'resultats' // ✅ AJOUTÉ : Charger les résultats pour affichage
        ])
            ->whereHas('patient', fn($q) => $q->whereNull('deleted_at'));

        // Application des filtres de recherche
        $searchCondition = function ($query) use ($search) {
            if ($this->search) {
                $query->where(function ($q) use ($search) {
                    $q->where('reference', 'like', $search)
                        ->orWhere('renseignement_clinique', 'like', $search)
                        ->orWhereHas('patient', function ($subQ) use ($search) {
                            $subQ->where('nom', 'like', $search)
                                ->orWhere('prenom', 'like', $search)
                                ->orWhere('telephone', 'like', $search);
                        })
                        ->orWhereHas('prescripteur', function ($subQ) use ($search) {
                            $subQ->where('nom', 'like', $search)
                                ->where('is_active', true);
                        });
                });
            }
        };

        // Application des filtres avancés
        $advancedFilters = function ($query) {
            if ($this->filterPrescripteur) {
                $query->where('prescripteur_id', $this->filterPrescripteur);
            }

            if ($this->filterUrgence) {
                $query->where('patient_type', $this->filterUrgence);
            }
        };

        // Construction des requêtes pour chaque onglet
        $analyseValides = (clone $baseQuery)
            ->where('status', Prescription::STATUS_VALIDE)
            ->where($searchCondition)
            ->where($advancedFilters)
            ->latest()
            ->paginate($this->perPage, ['*'], 'page');

        $analyseTermines = (clone $baseQuery)
            ->where('status', Prescription::STATUS_EN_COURS)
            ->where($searchCondition)
            ->where($advancedFilters)
            ->latest()
            ->paginate($this->perPage, ['*'], 'page');

        // Charger les prescripteurs pour les filtres
        $prescripteurs = \App\Models\Prescripteur::where('is_active', true)
            ->orderBy('nom')
            ->get(['id', 'nom', 'prenom']);

        return view('livewire.biologiste.analyse-valide', compact(
            'analyseValides',
            'analyseTermines',
            'prescripteurs'
        ));
    }

    private function getCurrentPrescriptions()
    {
        $query = Prescription::with(['patient', 'prescripteur']);

        if ($this->tab === 'valide') {
            $query->where('status', Prescription::STATUS_VALIDE);
        } else {
            $query->where('status', Prescription::STATUS_EN_COURS);
        }

        return $query->get();
    }

    private function loadStatistics()
    {
        $this->stats = [
            'total_termine' => Prescription::where('status', Prescription::STATUS_EN_COURS)->count(),
            'total_valide' => Prescription::where('status', Prescription::STATUS_VALIDE)->count(),
            'urgences_nuit' => Prescription::where('patient_type', 'URGENCE-NUIT')
                ->whereIn('status', [Prescription::STATUS_EN_COURS, Prescription::STATUS_VALIDE])
                ->count(),
            'urgences_jour' => Prescription::where('patient_type', 'URGENCE-JOUR')
                ->whereIn('status', [Prescription::STATUS_EN_COURS, Prescription::STATUS_VALIDE])
                ->count(),
        ];
    }

    private function validateSingleAnalyse($prescriptionId)
    {
        try {
            $prescription = Prescription::findOrFail($prescriptionId);

            if ($prescription->status !== Prescription::STATUS_EN_COURS) {
                return false;
            }

            // Récupérer toutes les analyses parents avec leurs enfants
            $parentAnalyses = $prescription->analyses()
                ->with(['enfants'])
                ->where('level', 'PARENT')
                ->get();

            $allAnalyseIds = collect();

            // Collecter tous les IDs des analyses (parents et enfants)
            foreach ($parentAnalyses as $parentAnalyse) {
                $allAnalyseIds->push($parentAnalyse->id);
                $this->collectChildAnalyseIds($parentAnalyse, $allAnalyseIds);
            }

            // Mettre à jour les résultats
            Resultat::where('prescription_id', $prescriptionId)
                ->whereIn('analyse_id', $allAnalyseIds)
                ->update([
                    'validated_by' => Auth::id(),
                    'validated_at' => now(),
                    'status' => 'VALIDE'
                ]);

            // Mettre à jour les statuts des analyses pivot
            foreach ($parentAnalyses as $parentAnalyse) {
                AnalysePrescription::where([
                    'prescription_id' => $prescriptionId,
                    'analyse_id' => $parentAnalyse->id
                ])->update([
                            'status' => AnalysePrescription::STATUS_VALIDE,
                            'updated_at' => now()
                        ]);
            }

            // Mise à jour du statut de la prescription
            $prescription->update([
                'status' => Prescription::STATUS_VALIDE,
                'validated_by' => Auth::id(),
                'validated_at' => now()
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Erreur validation analyse unique:', [
                'prescription_id' => $prescriptionId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    private function collectChildAnalyseIds($analyse, &$allAnalyseIds)
    {
        if ($analyse->enfants && $analyse->enfants->isNotEmpty()) {
            foreach ($analyse->enfants as $child) {
                $allAnalyseIds->push($child->id);
                $this->collectChildAnalyseIds($child, $allAnalyseIds);
            }
        }
    }
}