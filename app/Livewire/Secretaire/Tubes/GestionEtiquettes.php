<?php

namespace App\Livewire\Secretaire\Tubes;

use Carbon\Carbon;
use App\Models\Tube;
use App\Models\Prescription;
use Livewire\Component;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

class GestionEtiquettes extends Component
{
    use WithPagination;

    // FILTRES
    public string $recherche = '';
    public string $filtreStatut = 'tous';
    public string $filtreDate = 'aujourd_hui';
    public ?string $dateDebut = null;
    public ?string $dateFin = null;
    public string $typeAffichage = 'tous';
    
    // SÉLECTION
    public array $prescriptionsSelectionnees = [];
    public bool $toutSelectionner = false;

    // CONFIGURATION POUR ÉTIQUETTES - Format fixe horizontal
    public int $nombreColonnes = 2; // Garde pour compatibilité mais pas utilisé
    public bool $inclurePatient = true;
    
    // STATISTIQUES
    public array $statistiques = [];

    protected $queryString = [
        'recherche' => ['except' => ''],
        'filtreStatut' => ['except' => 'tous'],
        'filtreDate' => ['except' => 'aujourd_hui'],
        'typeAffichage' => ['except' => 'tous'],
        'page' => ['except' => 1]
    ];

    public function mount()
    {
        $this->dateDebut = today()->format('Y-m-d');
        $this->dateFin = today()->format('Y-m-d');
        $this->calculerStatistiques();
    }

    public function updated($property)
    {
        if (in_array($property, ['recherche', 'filtreStatut', 'filtreDate', 'dateDebut', 'dateFin', 'typeAffichage'])) {
            $this->resetPage();
            $this->calculerStatistiques();
        }

        if ($property === 'toutSelectionner') {
            $this->toggleToutSelectionner();
        }

        if ($property === 'filtreDate') {
            $this->ajusterDates();
        }
    }

    private function ajusterDates()
    {
        switch ($this->filtreDate) {
            case 'aujourd_hui':
                $this->dateDebut = today()->format('Y-m-d');
                $this->dateFin = today()->format('Y-m-d');
                break;
            case 'hier':
                $this->dateDebut = Carbon::yesterday()->format('Y-m-d');
                $this->dateFin = Carbon::yesterday()->format('Y-m-d');
                break;
            case 'cette_semaine':
                $this->dateDebut = now()->startOfWeek()->format('Y-m-d');
                $this->dateFin = now()->endOfWeek()->format('Y-m-d');
                break;
            case 'ce_mois':
                $this->dateDebut = now()->startOfMonth()->format('Y-m-d');
                $this->dateFin = now()->endOfMonth()->format('Y-m-d');
                break;
        }
    }

    private function calculerStatistiques()
    {
        try {
            $baseQuery = $this->getBaseQuery();
            
            $this->statistiques = [
                'total_prescriptions' => (clone $baseQuery)->count(),
                'avec_tubes' => (clone $baseQuery)->has('tubes')->count(),
                'sans_tubes' => (clone $baseQuery)->doesntHave('tubes')->count(),
                'avec_analyses' => \DB::table('prescriptions')
                    ->join('prescription_analyse', 'prescriptions.id', '=', 'prescription_analyse.prescription_id')
                    ->whereBetween('prescriptions.created_at', [
                        $this->dateDebut . ' 00:00:00',
                        $this->dateFin . ' 23:59:59'
                    ])
                    ->distinct('prescriptions.id')
                    ->count('prescriptions.id'),
                'tubes_receptionnes' => Tube::whereHas('prescription', function($q) {
                    $this->applyPrescriptionFilters($q);
                })->whereNotNull('receptionne_par')->count(),
                'tubes_non_receptionnes' => Tube::whereHas('prescription', function($q) {
                    $this->applyPrescriptionFilters($q);
                })->whereNull('receptionne_par')->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Erreur calcul statistiques étiquettes', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            $this->statistiques = [
                'total_prescriptions' => 0,
                'avec_tubes' => 0,
                'sans_tubes' => 0,
                'avec_analyses' => 0,
                'tubes_receptionnes' => 0,
                'tubes_non_receptionnes' => 0,
            ];
        }
    }

    private function applyPrescriptionFilters($query)
    {
        return $query->when($this->recherche, function($q) {
                $recherche = trim($this->recherche);
                $q->where(function($query) use ($recherche) {
                    $query->where('prescriptions.reference', 'like', "%{$recherche}%")
                        ->orWhereHas('patient', function($subQ) use ($recherche) {
                            $subQ->where('nom', 'like', "%{$recherche}%")
                                 ->orWhere('prenom', 'like', "%{$recherche}%");
                        })
                        ->orWhereHas('prescripteur', function($subQ) use ($recherche) {
                            $subQ->where('nom', 'like', "%{$recherche}%");
                        });
                });
            })
            ->when($this->dateDebut && $this->dateFin, function($q) {
                $q->whereBetween('prescriptions.created_at', [
                    $this->dateDebut . ' 00:00:00',
                    $this->dateFin . ' 23:59:59'
                ]);
            });
    }

    private function getBaseQuery()
    {
        $query = Prescription::with([
            'patient', 
            'prescripteur', 
            'tubes.prelevement',
            'tubes.prelevement.typeTubeRecommande'
        ]);

        $this->applyPrescriptionFilters($query);

        // Filtres spécifiques au type d'affichage
        if ($this->typeAffichage === 'avec_tubes') {
            $query->has('tubes');
        } elseif ($this->typeAffichage === 'sans_tubes') {
            $query->doesntHave('tubes');
        } elseif ($this->typeAffichage === 'avec_analyses') {
            $query->whereExists(function($q) {
                $q->select(\DB::raw(1))
                  ->from('prescription_analyse')
                  ->whereColumn('prescription_analyse.prescription_id', 'prescriptions.id');
            });
        } elseif ($this->typeAffichage === 'sans_analyses') {
            $query->whereNotExists(function($q) {
                $q->select(\DB::raw(1))
                  ->from('prescription_analyse')
                  ->whereColumn('prescription_analyse.prescription_id', 'prescriptions.id');
            });
        }

        // Filtre statut (pour les tubes seulement si prescriptions avec tubes)
        if ($this->filtreStatut !== 'tous') {
            if ($this->filtreStatut === 'receptionnes') {
                $query->whereHas('tubes', function($q) {
                    $q->whereNotNull('receptionne_par');
                });
            } elseif ($this->filtreStatut === 'non_receptionnes') {
                $query->whereHas('tubes', function($q) {
                    $q->whereNull('receptionne_par');
                });
            }
        }

        return $query;
    }

    public function toggleToutSelectionner()
    {
        if ($this->toutSelectionner) {
            $this->prescriptionsSelectionnees = $this->getBaseQuery()
                                                   ->pluck('id')
                                                   ->toArray();
        } else {
            $this->prescriptionsSelectionnees = [];
        }
    }

    public function viderSelection()
    {
        $this->prescriptionsSelectionnees = [];
        $this->toutSelectionner = false;
        $this->dispatch('notify', [
            'type' => 'info',
            'message' => 'Sélection vidée'
        ]);
    }

    public function imprimerEtiquettes()
    {
        if (empty($this->prescriptionsSelectionnees)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Veuillez sélectionner au moins une prescription'
            ]);
            return;
        }

        try {
            $prescriptions = Prescription::with([
                    'patient', 
                    'prescripteur',
                    'tubes.prelevement',
                    'tubes.prelevement.typeTubeRecommande'
                ])
                ->whereIn('id', $this->prescriptionsSelectionnees)
                ->orderBy('created_at')
                ->get();

            // Charger les analyses séparément
            foreach ($prescriptions as $prescription) {
                $prescription->analyses_data = \DB::table('prescription_analyse')
                    ->join('analyses', 'prescription_analyse.analyse_id', '=', 'analyses.id')
                    ->where('prescription_analyse.prescription_id', $prescription->id)
                    ->select('analyses.designation', 'analyses.code')
                    ->get();
            }

            if ($prescriptions->isEmpty()) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Aucune prescription trouvée pour l\'impression'
                ]);
                return;
            }

            // Calcul des statistiques pour génération
            $nombrePrescriptions = $prescriptions->count();
            
            // Compter les tubes réels
            $nombreTubes = $prescriptions->sum(function($prescription) {
                return $prescription->tubes->count();
            });
            
            // Compter prescriptions sans tubes
            $prescriptionsSanstubes = $prescriptions->filter(function($prescription) {
                return $prescription->tubes->isEmpty();
            })->count();

            // Compter prescriptions avec analyses seulement
            $prescriptionsAvecAnalyses = $prescriptions->filter(function($prescription) {
                $hasAnalyses = \DB::table('prescription_analyse')
                    ->where('prescription_id', $prescription->id)
                    ->exists();
                return $prescription->tubes->isEmpty() && $hasAnalyses;
            })->count();

            // Compter prescriptions complètement vides
            $prescriptionsVides = $prescriptions->filter(function($prescription) {
                $hasAnalyses = \DB::table('prescription_analyse')
                    ->where('prescription_id', $prescription->id)
                    ->exists();
                return $prescription->tubes->isEmpty() && !$hasAnalyses;
            })->count();

            // CALCUL TOTAL ÉTIQUETTES selon nouvelle logique :
            // - Chaque tube = 5 étiquettes
            // - Chaque prescription sans tubes (avec/sans analyses) = 5 étiquettes
            $nombreEtiquettesTotales = ($nombreTubes * 5) + ($prescriptionsSanstubes * 5);

            $pdf = Pdf::loadView('factures.etiquettes-prescriptions', [
                'prescriptions' => $prescriptions,
                'inclurePatient' => $this->inclurePatient,
                'titre' => 'Étiquettes Prescriptions - ' . now()->format('d/m/Y H:i'),
                'laboratoire' => config('app.name', 'Laboratoire CTB'),
                'statistiques' => [
                    'nombre_prescriptions' => $nombrePrescriptions,
                    'nombre_tubes' => $nombreTubes,
                    'prescriptions_sans_tubes' => $prescriptionsSanstubes,
                    'prescriptions_avec_analyses' => $prescriptionsAvecAnalyses,
                    'prescriptions_vides' => $prescriptionsVides,
                    'nombre_etiquettes_totales' => $nombreEtiquettesTotales
                ]
            ])
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'dpi' => 300,
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'chroot' => public_path(),
                'debugKeepTemp' => false,
            ]);

            Log::info('Génération étiquettes prescriptions PDF format horizontal', [
                'user_id' => Auth::id(),
                'prescriptions_count' => $nombrePrescriptions,
                'tubes_count' => $nombreTubes,
                'sans_tubes_count' => $prescriptionsSanstubes,
                'avec_analyses_count' => $prescriptionsAvecAnalyses,
                'vides_count' => $prescriptionsVides,
                'etiquettes_totales' => $nombreEtiquettesTotales
            ]);

            $filename = 'etiquettes-prescriptions-' . now()->format('Y-m-d-H-i') . '.pdf';

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "PDF généré: {$nombrePrescriptions} prescription(s) → {$nombreEtiquettesTotales} étiquettes"
            ]);

            return response()->streamDownload(
                fn () => print($pdf->output()),
                $filename,
                ['Content-Type' => 'application/pdf']
            );

        } catch (\Exception $e) {
            Log::error('Erreur génération PDF étiquettes prescriptions', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'prescriptions' => $this->prescriptionsSelectionnees,
                'user_id' => Auth::id()
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Erreur lors de la génération: ' . $e->getMessage()
            ]);
        }
    }

    public function marquerTubesReceptionnes($prescriptionId)
    {
        try {
            $prescription = Prescription::with('tubes')->find($prescriptionId);
            
            if (!$prescription) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Prescription introuvable'
                ]);
                return;
            }

            if ($prescription->tubes->isEmpty()) {
                $this->dispatch('notify', [
                    'type' => 'info',
                    'message' => 'Cette prescription n\'a pas de tubes'
                ]);
                return;
            }
            
            $tubesMarques = 0;
            foreach ($prescription->tubes as $tube) {
                if (!$tube->estReceptionne()) {
                    $tube->marquerReceptionne(Auth::id());
                    $tubesMarques++;
                }
            }

            $this->calculerStatistiques();
            
            if ($tubesMarques > 0) {
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => "{$tubesMarques} tube(s) marqué(s) comme réceptionné(s)"
                ]);
            } else {
                $this->dispatch('notify', [
                    'type' => 'info',
                    'message' => 'Tous les tubes sont déjà réceptionnés'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Erreur marquage réception tubes prescription', [
                'prescription_id' => $prescriptionId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Erreur lors du marquage: ' . $e->getMessage()
            ]);
        }
    }

    public function reinitialiserFiltres()
    {
        $this->reset([
            'recherche', 
            'filtreStatut', 
            'filtreDate',
            'typeAffichage',
            'prescriptionsSelectionnees',
            'toutSelectionner'
        ]);
        
        $this->dateDebut = today()->format('Y-m-d');
        $this->dateFin = today()->format('Y-m-d');
        $this->calculerStatistiques();
        $this->resetPage();
        
        $this->dispatch('notify', [
            'type' => 'info',
            'message' => 'Filtres réinitialisés'
        ]);
    }

    // PROPRIÉTÉS CALCULÉES
    #[Computed]
    public function prescriptions()
    {
        return $this->getBaseQuery()
                   ->orderByDesc('created_at')
                   ->paginate(100);
    }

    #[Computed]
    public function selectionSummary()
    {
        if (empty($this->prescriptionsSelectionnees)) {
            return null;
        }

        $prescriptions = Prescription::whereIn('id', $this->prescriptionsSelectionnees)
                    ->with(['tubes.prelevement', 'patient'])
                    ->get();

        $totalTubes = $prescriptions->sum(function($prescription) {
            return $prescription->tubes->count();
        });

        $totalAnalyses = \DB::table('prescription_analyse')
            ->whereIn('prescription_id', $this->prescriptionsSelectionnees)
            ->count();

        $prescriptionsSanstubes = $prescriptions->filter(function($prescription) {
            return $prescription->tubes->isEmpty();
        })->count();

        $prescriptionsAvecAnalysesSeulement = 0;
        $prescriptionsVides = 0;

        foreach ($prescriptions as $prescription) {
            $hasAnalyses = \DB::table('prescription_analyse')
                ->where('prescription_id', $prescription->id)
                ->exists();
            
            if ($prescription->tubes->isEmpty()) {
                if ($hasAnalyses) {
                    $prescriptionsAvecAnalysesSeulement++;
                } else {
                    $prescriptionsVides++;
                }
            }
        }

        // NOUVEAU CALCUL : Format horizontal fixe
        // Chaque tube = 5 étiquettes, chaque prescription sans tubes = 5 étiquettes
        $totalEtiquettes = ($totalTubes * 5) + ($prescriptionsSanstubes * 5);

        return [
            'total_prescriptions' => $prescriptions->count(),
            'total_tubes' => $totalTubes,
            'total_analyses' => $totalAnalyses,
            'sans_tubes' => $prescriptionsSanstubes,
            'avec_tubes' => $prescriptions->count() - $prescriptionsSanstubes,
            'avec_analyses_seulement' => $prescriptionsAvecAnalysesSeulement,
            'prescriptions_vides' => $prescriptionsVides,
            'total_etiquettes' => $totalEtiquettes
        ];
    }

    public function render()
    {
        return view('livewire.secretaire.tubes.gestion-etiquettes', [
            'prescriptions' => $this->prescriptions,
            'selectionSummary' => $this->selectionSummary
        ]);
    }
}