<?php

namespace App\Livewire\Biologiste;

use App\Models\Analyse;
use Livewire\Component;
use App\Models\Resultat;
use App\Models\Prescripteur;
use App\Models\Prescription;
use Livewire\WithPagination;
use App\Services\ResultatPdfShow;
use Illuminate\Support\Facades\DB;
use App\Models\AnalysePrescription;
use Illuminate\Support\Facades\Log;
use App\Services\AnterioriteService;
use Illuminate\Support\Facades\Auth;

class AnalyseValide extends Component
{
    use WithPagination;

    // Propriétés principales
    public $tab = 'termine';
    public $search = '';
    public $perPage = 20;
    public $showConfirmModal = false;
    public $prescriptionToValidate = null;

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

    public function openConfirmModal($prescriptionId)
    {
        $this->prescriptionToValidate = Prescription::with(['patient', 'analyses'])->findOrFail($prescriptionId);
        $this->showConfirmModal = true;
    }

    public function closeConfirmModal()
    {
        $this->showConfirmModal = false;
        $this->prescriptionToValidate = null;
    }

    public function confirmValidation()
    {
        if ($this->prescriptionToValidate) {
            $this->validateAnalyse($this->prescriptionToValidate->id);
            $this->closeConfirmModal();
        }
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
     * Consulter les résultats du technicien avant validation
     */
    public function viewAnalyseDetails($prescriptionId)
    {
        try {
            $prescription = Prescription::findOrFail($prescriptionId);
            return redirect()->route('technicien.prescription.show', $prescription);
        } catch (\Exception $e) {
            flash()->error('Impossible d\'ouvrir cette analyse');
        }
    }

    /**
     * CORRIGÉ : Valider une analyse (TERMINE → VALIDE)
     */
    public function validateAnalyse(int $prescriptionId)
    {
        try {
            DB::beginTransaction();

            $prescription = Prescription::findOrFail($prescriptionId);

            // Vérifier que l'analyse est bien terminée par le technicien
            if ($prescription->status !== Prescription::STATUS_TERMINE) {
                throw new \Exception('Cette analyse doit être terminée par le technicien avant validation');
            }

            // Valider l'analyse
            $this->validateSingleAnalyse($prescriptionId);

            DB::commit();

            $this->loadStatistics();
            // flash('success', 'Analyse validée avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            flash()->error('Erreur lors de la validation : ' . $e->getMessage());
        }
    }

    /**
     * CORRIGÉ : Validation en lot avec gestion des analyses enfants
     */
    public function bulkValidate()
    {
        if (empty($this->selectedPrescriptions)) {
            flash()->warning('Veuillez sélectionner au moins une prescription');
            return;
        }

        try {
            DB::beginTransaction();

            $count = 0;
            $totalAnalyses = 0;
            $errors = [];

            foreach ($this->selectedPrescriptions as $prescriptionId) {
                $prescription = Prescription::find($prescriptionId);
                
                if (!$prescription) {
                    $errors[] = "Prescription ID {$prescriptionId} non trouvée";
                    continue;
                }

                if ($prescription->status !== Prescription::STATUS_TERMINE) {
                    $errors[] = "Prescription {$prescription->reference} n'est pas terminée";
                    continue;
                }

                // Compter les analyses avant validation
                $analyseCount = Resultat::where('prescription_id', $prescriptionId)
                    ->where('status', 'TERMINE')
                    ->count();

                if ($this->validateSingleAnalyse($prescriptionId)) {
                    $count++;
                    $totalAnalyses += $analyseCount;
                } else {
                    $errors[] = "Erreur lors de la validation de {$prescription->reference}";
                }
            }

            DB::commit();

            $this->resetSelection();
            $this->loadStatistics();

            $message = "{$count} prescription(s) validée(s) avec succès";
            if ($totalAnalyses > 0) {
                $message .= " ({$totalAnalyses} analyses au total)";
            }

            if (!empty($errors)) {
                $message .= ". Erreurs : " . implode(', ', array_slice($errors, 0, 3));
                if (count($errors) > 3) {
                    $message .= "... (et " . (count($errors) - 3) . " autres)";
                }
            }

            flash($count > 0 ? 'success' : 'warning', $message);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de la validation en lot', [
                'error' => $e->getMessage(),
                'selected_prescriptions' => $this->selectedPrescriptions
            ]);
            flash()->error('Erreur lors de la validation en lot');
        }
    }

    public function generatePrescriptionPdf($prescriptionId)
    {
        try {
            $prescription = Prescription::findOrFail($prescriptionId);
            $pdfService = new ResultatPdfShow();

            // Vérifier si on peut générer un PDF
            if ($prescription->status === Prescription::STATUS_VALIDE) {
                // Pour prescriptions validées : PDF final
                if (!$pdfService->canGenerateFinalPdf($prescription)) {
                    flash()->error('Aucun résultat validé trouvé pour cette prescription.');
                    return;
                }
                
                $pdfUrl = $pdfService->generateFinalPDF($prescription);
                
            } elseif ($prescription->status === Prescription::STATUS_TERMINE) {
                // Pour prescriptions terminées : PDF final aussi (selon votre service)
                if (!$pdfService->canGenerateFinalPdf($prescription)) {
                    flash()->error('Aucun résultat terminé trouvé pour cette prescription.');
                    return;
                }
                
                $pdfUrl = $pdfService->generateFinalPDF($prescription);
                
            } else {
                // Pour autres statuts : vérifier s'il y a des résultats saisis pour aperçu
                if (!$pdfService->canGeneratePreviewPdf($prescription)) {
                    flash()->error('Aucun résultat saisi trouvé pour cette prescription.');
                    return;
                }
                
                $pdfUrl = $pdfService->generatePreviewPDF($prescription);
            }

            // Dispatch event pour ouvrir dans nouvel onglet
            $this->dispatch('openPdfInNewTab', ['url' => $pdfUrl]);

        } catch (\Exception $e) {
            Log::error('Erreur génération PDF:', [
                'prescription_id' => $prescriptionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            flash()->error('Erreur lors de la génération du PDF : ' . $e->getMessage());
        }
    }

    public function render()
    {
        $search = '%' . $this->search . '%';

        $baseQuery = Prescription::with([
            'patient',
            'prescripteur:id,nom,prenom,is_active',
            'analyses',
            'resultats'
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
            ->where('status', Prescription::STATUS_TERMINE)
            ->where($searchCondition)
            ->where($advancedFilters)
            ->latest()
            ->paginate($this->perPage, ['*'], 'page');

        // Charger les prescripteurs pour les filtres
        $prescripteurs = Prescripteur::where('is_active', true)
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
            $query->where('status', Prescription::STATUS_TERMINE);
        }

        return $query->get();
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

    /**
     * SOLUTION COMPLÈTE : Validation d'une analyse qui inclut TOUTES les analyses (parent + enfants)
     */
    private function validateSingleAnalyse($prescriptionId)
    {
        try {
            $prescription = Prescription::findOrFail($prescriptionId);

            if ($prescription->status !== Prescription::STATUS_TERMINE) {
                flash()->error('Cette prescription doit être terminée avant validation.');
                return false;
            }

            // Récupérer TOUTES les analyses qui ont des résultats (parent + enfants)
            $allAnalyseIds = Resultat::where('prescription_id', $prescriptionId)
                ->whereNull('deleted_at')
                ->pluck('analyse_id')
                ->unique()
                ->toArray();

            if (empty($allAnalyseIds)) {
                flash()->error('Aucune analyse avec résultats trouvée pour cette prescription.');
                return false;
            }

            // Récupérer les analyses principales (dans prescription_analyse)
            $principalAnalyseIds = AnalysePrescription::where('prescription_id', $prescriptionId)
                ->pluck('analyse_id')
                ->toArray();

            // Vérifier les résultats terminés
            $resultsToValidate = Resultat::where('prescription_id', $prescriptionId)
                ->whereIn('analyse_id', $allAnalyseIds)
                ->where('status', 'TERMINE')
                ->get();

            if ($resultsToValidate->isEmpty()) {
                flash()->error('Aucun résultat terminé disponible pour validation.');
                return false;
            }

            // ✅ NOUVELLE ÉTAPE : Calculer les antériorités AVANT la validation
            $anterioriteService = app(AnterioriteService::class);
            $anterioriteService->calculerAnteriorites($prescription);

            // ✅ ÉTAPE 1 : Mettre à jour TOUS les résultats terminés
            $updatedCount = Resultat::where('prescription_id', $prescriptionId)
                ->whereIn('analyse_id', $allAnalyseIds)
                ->where('status', 'TERMINE')
                ->update([
                    'validated_by' => Auth::id(),
                    'validated_at' => now(),
                    'status' => 'VALIDE'
                ]);

            // ✅ ÉTAPE 2 : Mettre à jour le statut dans la table pivot pour les analyses principales
            if (!empty($principalAnalyseIds)) {
                $pivotUpdatedCount = AnalysePrescription::where('prescription_id', $prescriptionId)
                    ->whereIn('analyse_id', $principalAnalyseIds)
                    ->update([
                        'status' => AnalysePrescription::STATUS_VALIDE,
                        'updated_at' => now()
                    ]);
            }

            // ✅ ÉTAPE 3 : Vérifier si TOUS les résultats sont validés
            $nonValidatedResults = Resultat::where('prescription_id', $prescriptionId)
                ->whereIn('analyse_id', $allAnalyseIds)
                ->where('status', '!=', 'VALIDE')
                ->count();

            // ✅ ÉTAPE 4 : Mettre à jour la prescription si tous les résultats sont validés
            if ($nonValidatedResults === 0) {
                $prescription->update([
                    'status' => Prescription::STATUS_VALIDE,
                    'validated_by' => Auth::id(),
                    'validated_at' => now()
                ]);
                
                // ✅ NOUVEAU : Message mentionnant les antériorités
                $anterioriteCount = Resultat::where('prescription_id', $prescriptionId)
                    ->whereNotNull('anteriorite')
                    ->count();
                
                $message = 'Prescription validée avec succès ! (' . count($allAnalyseIds) . ' analyses validées)';
                if ($anterioriteCount > 0) {
                    $message .= ' - ' . $anterioriteCount . ' antériorité(s) trouvée(s)';
                }
                
                flash()->success($message);
            } else {
                flash()->warning('Validation partielle effectuée. ' . $nonValidatedResults . ' analyses restent à valider.');
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Erreur lors de la validation unique', [
                'prescription_id' => $prescriptionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            flash()->error('Erreur lors de la validation : ' . $e->getMessage());
            return false;
        }
    }



    public function redoPrescription($prescriptionId, $commentaire = null)
    {
        try {
            $prescription = Prescription::findOrFail($prescriptionId);
            
            // Vérification de permission (optionnel)
            if (!$prescription->peutEtreRemiseARefaire()) {
                flash()->error('Cette prescription ne peut pas être remise à refaire');
                return;
            }
            
            // Utiliser la méthode du modèle
            $result = $prescription->marquerARefaire($commentaire, Auth::id());
            
            // Recharger les statistiques
            $this->loadStatistics();
            
            // Message de succès
            flash()->success($result['message']);
            
        } catch (\Exception $e) {
            flash()->error('Erreur lors de la mise à refaire : ' . $e->getMessage());

            Log::error('Erreur dans redoPrescription du composant', [
                'prescription_id' => $prescriptionId,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
        }
    }



    /**
     * Recalculer les antériorités pour une prescription
     */
    public function recalculerAnteriorites($prescriptionId)
    {
        try {
            $prescription = Prescription::findOrFail($prescriptionId);
            $anterioriteService = app(AnterioriteService::class);
            
            $anterioriteService->calculerAnteriorites($prescription);
            
            $count = Resultat::where('prescription_id', $prescriptionId)
                ->whereNotNull('anteriorite')
                ->count();
                
            flash()->success("Antériorités recalculées : {$count} trouvée(s)");
            
        } catch (\Exception $e) {
            flash()->error('Erreur lors du recalcul des antériorités : ' . $e->getMessage());
        }
    }

    /**
     * Voir l'historique d'une analyse pour un patient
     */
    public function voirHistoriqueAnalyse($prescriptionId, $analyseId)
    {
        try {
            $prescription = Prescription::with('patient')->findOrFail($prescriptionId);
            $analyse = Analyse::findOrFail($analyseId);
            
            $anterioriteService = app(AnterioriteService::class);
            $historique = $anterioriteService->getHistoriqueAnalyse($prescription->patient, $analyse);
            
            // Vous pouvez utiliser ceci pour afficher un modal avec l'historique
            $this->dispatch('showHistorique', [
                'patient' => $prescription->patient->nom . ' ' . $prescription->patient->prenom,
                'analyse' => $analyse->designation,
                'historique' => $historique->map(function($resultat) {
                    return [
                        'date' => $resultat->prescription->created_at->format('d/m/Y'),
                        'valeur' => $resultat->display_value_pdf,
                        'reference' => $resultat->prescription->reference
                    ];
                })
            ]);
            
        } catch (\Exception $e) {
            flash()->error('Erreur lors de la récupération de l\'historique');
        }
    }
}