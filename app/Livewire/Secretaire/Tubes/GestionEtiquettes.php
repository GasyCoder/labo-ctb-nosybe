<?php

namespace App\Livewire\Secretaire\Tubes;

use Carbon\Carbon;
use App\Models\Tube;
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
    
    // SÉLECTION
    public array $tubesSelectionnes = [];
    public bool $toutSelectionner = false;

    // CONFIGURATION POUR ÉTIQUETTES
    public int $nombreColonnes = 2; // RÉDUIT pour optimiser l'espace
    public bool $inclurePatient = true;
    public string $modeAffichage = 'optimise'; // 'optimise' ou 'separe'
    
    // STATISTIQUES
    public array $statistiques = [];

    protected $queryString = [
        'recherche' => ['except' => ''],
        'filtreStatut' => ['except' => 'tous'],
        'filtreDate' => ['except' => 'aujourd_hui'],
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
        if (in_array($property, ['recherche', 'filtreStatut', 'filtreDate', 'dateDebut', 'dateFin'])) {
            $this->resetPage();
            $this->calculerStatistiques();
        }

        if ($property === 'toutSelectionner') {
            $this->toggleToutSelectionner();
        }

        if ($property === 'filtreDate') {
            $this->ajusterDates();
        }

        // Validation des colonnes (2-4 colonnes max pour optimisation)
        if ($property === 'nombreColonnes') {
            $this->nombreColonnes = max(2, min(4, $this->nombreColonnes));
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
                'total' => (clone $baseQuery)->count(),
                'non_receptionnes' => (clone $baseQuery)->whereNull('tubes.receptionne_par')->count(),
                'receptionnes' => (clone $baseQuery)->whereNotNull('tubes.receptionne_par')->count(),
                'aujourd_hui' => Tube::whereDate('tubes.created_at', today())->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Erreur calcul statistiques étiquettes', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            $this->statistiques = [
                'total' => 0, 
                'non_receptionnes' => 0, 
                'receptionnes' => 0, 
                'aujourd_hui' => 0
            ];
        }
    }

    private function getBaseQuery()
    {
        return Tube::with([
                'prescription.patient', 
                'prescription.prescripteur', 
                'prelevement',
                'prelevement.typeTubeRecommande' // AJOUT pour le type de tube
            ])
            ->when($this->recherche, function($q) {
                $recherche = trim($this->recherche);
                $q->where(function($query) use ($recherche) {
                    $query->where('tubes.code_barre', 'like', "%{$recherche}%")
                        ->orWhereHas('prescription', function($subQ) use ($recherche) {
                            $subQ->where('reference', 'like', "%{$recherche}%");
                        })
                        ->orWhereHas('prescription.patient', function($subQ) use ($recherche) {
                            $subQ->where('nom', 'like', "%{$recherche}%")
                                 ->orWhere('prenom', 'like', "%{$recherche}%");
                        })
                        ->orWhereHas('prelevement', function($subQ) use ($recherche) {
                            $subQ->where('denomination', 'like', "%{$recherche}%")
                                 ->orWhere('code', 'like', "%{$recherche}%");
                        });
                });
            })
            ->when($this->filtreStatut !== 'tous', function($q) {
                if ($this->filtreStatut === 'receptionnes') {
                    $q->whereNotNull('tubes.receptionne_par');
                } else {
                    $q->whereNull('tubes.receptionne_par');
                }
            })
            ->when($this->dateDebut && $this->dateFin, function($q) {
                $q->whereBetween('tubes.created_at', [
                    $this->dateDebut . ' 00:00:00',
                    $this->dateFin . ' 23:59:59'
                ]);
            });
    }

    public function toggleToutSelectionner()
    {
        if ($this->toutSelectionner) {
            $this->tubesSelectionnes = $this->getBaseQuery()
                                           ->pluck('tubes.id')
                                           ->toArray();
        } else {
            $this->tubesSelectionnes = [];
        }
    }

    public function viderSelection()
    {
        $this->tubesSelectionnes = [];
        $this->toutSelectionner = false;
        $this->dispatch('notify', [
            'type' => 'info',
            'message' => 'Sélection vidée'
        ]);
    }

    public function imprimerEtiquettes()
    {
        if (empty($this->tubesSelectionnes)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Veuillez sélectionner au moins un tube'
            ]);
            return;
        }

        try {
            $tubes = Tube::with([
                    'prescription.patient', 
                    'prescription.prescripteur',
                    'prelevement',
                    'prelevement.typeTubeRecommande'
                ])
                ->whereIn('id', $this->tubesSelectionnes)
                ->orderBy('prescription_id') // IMPORTANT: Grouper par prescription pour optimiser
                ->orderBy('tubes.created_at')
                ->get();

            if ($tubes->isEmpty()) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Aucun tube trouvé pour l\'impression'
                ]);
                return;
            }

            // Statistiques pour l'utilisateur
            $groupedByPatient = $tubes->groupBy('prescription.patient.id');
            $nombrePatients = $groupedByPatient->count();
            $nombreTubes = $tubes->count();

            // Calcul estimé du nombre de pages selon le mode
            if ($this->modeAffichage === 'optimise') {
                $etiquettesParPage = 8; // 4 lignes x 2 colonnes
                $lignesHeaderParPatient = 1; // Une ligne d'en-tête par patient
                $totalLignesNecessaires = $nombreTubes + $nombrePatients; // tubes + headers patients
                $pagesEstimees = ceil($totalLignesNecessaires / $etiquettesParPage);
            } else {
                $pagesEstimees = $nombrePatients; // Une page par patient (ancien mode)
            }

            // Choisir le bon template selon le mode
            $templateView = $this->modeAffichage === 'optimise' 
                ? 'factures.etiquettes-tubes' 
                : 'factures.etiquettes-tubes';

            $pdf = Pdf::loadView($templateView, [
                'tubes' => $tubes,
                'colonnes' => $this->nombreColonnes,
                'inclurePatient' => $this->inclurePatient,
                'modeAffichage' => $this->modeAffichage,
                'titre' => 'Étiquettes Tubes - ' . now()->format('d/m/Y H:i'),
                'laboratoire' => config('app.name', 'Laboratoire CTB'),
                'statistiques' => [
                    'nombre_patients' => $nombrePatients,
                    'nombre_tubes' => $nombreTubes,
                    'pages_estimees' => $pagesEstimees
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

            Log::info('Génération étiquettes PDF optimisées', [
                'user_id' => Auth::id(),
                'tubes_count' => $nombreTubes,
                'patients_count' => $nombrePatients,
                'colonnes' => $this->nombreColonnes,
                'mode_affichage' => $this->modeAffichage,
                'pages_estimees' => $pagesEstimees
            ]);

            $filename = 'etiquettes-' . $this->modeAffichage . '-' . now()->format('Y-m-d-H-i') . '.pdf';

            // Message de succès avec statistiques
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "PDF généré: {$nombreTubes} étiquettes pour {$nombrePatients} patient(s) sur ~{$pagesEstimees} page(s)"
            ]);

            return response()->streamDownload(
                fn () => print($pdf->output()),
                $filename,
                ['Content-Type' => 'application/pdf']
            );

        } catch (\Exception $e) {
            Log::error('Erreur génération PDF étiquettes optimisées', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'tubes' => $this->tubesSelectionnes,
                'user_id' => Auth::id()
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Erreur lors de la génération: ' . $e->getMessage()
            ]);
        }
    }

    public function marquerReceptionne($tubeId)
    {
        try {
            $tube = Tube::find($tubeId);
            
            if (!$tube) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Tube introuvable'
                ]);
                return;
            }
            
            if ($tube->estReceptionne()) {
                $this->dispatch('notify', [
                    'type' => 'info',
                    'message' => 'Tube déjà réceptionné'
                ]);
                return;
            }

            $tube->marquerReceptionne(Auth::id());
            $this->calculerStatistiques();
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Tube {$tube->code_barre} marqué comme réceptionné"
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur marquage réception tube', [
                'tube_id' => $tubeId,
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
            'tubesSelectionnes',
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
    public function tubes()
    {
        return $this->getBaseQuery()
                   ->orderByDesc('tubes.created_at')
                   ->paginate(20);
    }

    #[Computed]
    public function selectionSummary()
    {
        if (empty($this->tubesSelectionnes)) {
            return null;
        }

        $tubes = Tube::whereIn('id', $this->tubesSelectionnes)
                    ->with(['prelevement', 'prescription.patient'])
                    ->get();

        $groupedByPatient = $tubes->groupBy('prescription.patient.id');

        return [
            'total' => $tubes->count(),
            'nombre_patients' => $groupedByPatient->count(),
            'par_type' => $tubes->groupBy('prelevement.denomination')
                               ->map->count()
                               ->sortDesc()
                               ->take(3),
            'estimation_pages' => $this->estimer_pages($tubes->count(), $groupedByPatient->count())
        ];
    }

    private function estimer_pages($nombreTubes, $nombrePatients)
    {
        if ($this->modeAffichage === 'optimise') {
            $etiquettesParPage = 8;
            $totalLignes = $nombreTubes + $nombrePatients; // tubes + headers
            return ceil($totalLignes / $etiquettesParPage);
        } else {
            return $nombrePatients; // Une page par patient
        }
    }

    #[Computed]
    public function modesAffichageDisponibles()
    {
        return [
            'optimise' => [
                'label' => 'Optimisé (plusieurs patients/page)',
                'description' => 'Économise le papier en regroupant les patients',
                'avantages' => ['Moins de papier', 'Plus écologique', 'Impression rapide']
            ],
            'separe' => [
                'label' => 'Séparé (un patient/page)',
                'description' => 'Chaque patient sur une page séparée',
                'avantages' => ['Plus lisible', 'Facilite le tri', 'Format traditionnel']
            ]
        ];
    }

    public function render()
    {
        return view('livewire.secretaire.tubes.gestion-etiquettes', [
            'tubes' => $this->tubes,
            'selectionSummary' => $this->selectionSummary,
            'modesAffichageDisponibles' => $this->modesAffichageDisponibles
        ]);
    }
}