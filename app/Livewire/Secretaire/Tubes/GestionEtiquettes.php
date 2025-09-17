<?php

namespace App\Livewire\Secretaire\Tubes;

use Carbon\Carbon;
use App\Models\Tube;
use Livewire\Component;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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

    // CONFIGURATION OPTIMISÉE POUR PETITES ÉTIQUETTES
    public int $nombreColonnes = 5; // Augmenté par défaut
    public bool $inclurePatient = true;
    public string $formatEtiquette = 'petit'; // petit, standard
    
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

        // Validation des colonnes pour petites étiquettes (2-6 colonnes)
        if ($property === 'nombreColonnes') {
            $this->nombreColonnes = max(2, min(6, $this->nombreColonnes));
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
            Log::error('Erreur calcul statistiques étiquettes', ['error' => $e->getMessage()]);
            $this->statistiques = ['total' => 0, 'non_receptionnes' => 0, 'receptionnes' => 0, 'aujourd_hui' => 0];
        }
    }

    private function getBaseQuery()
    {
        return Tube::with(['prescription.patient', 'prescription.prescripteur', 'prelevement'])
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

    public function toggleSelection($tubeId)
    {
        if (in_array($tubeId, $this->tubesSelectionnes)) {
            $this->tubesSelectionnes = array_diff($this->tubesSelectionnes, [$tubeId]);
        } else {
            $this->tubesSelectionnes[] = $tubeId;
        }

        $totalVisible = $this->getBaseQuery()->count();
        $this->toutSelectionner = count($this->tubesSelectionnes) === $totalVisible && $totalVisible > 0;
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
                    'prelevement'
                ])
                ->whereIn('id', $this->tubesSelectionnes)
                ->orderBy('tubes.created_at')
                ->get();

            if ($tubes->isEmpty()) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Aucun tube trouvé pour l\'impression'
                ]);
                return;
            }

            // Validation du nombre de colonnes pour petites étiquettes
            $colonnes = max(2, min(6, $this->nombreColonnes));

            // Utiliser le template optimisé pour petites étiquettes
            $pdf = Pdf::loadView('factures.etiquettes-tubes', [
                'tubes' => $tubes,
                'colonnes' => $colonnes,
                'inclurePatient' => $this->inclurePatient,
                'formatEtiquette' => $this->formatEtiquette,
                'titre' => 'Étiquettes Tubes - ' . now()->format('d/m/Y H:i'),
                'laboratoire' => config('app.name', 'Laboratoire CTB')
            ])
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'dpi' => 300, // Augmenté pour petites étiquettes
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'chroot' => public_path(),
            ]);

            return response()->streamDownload(
                fn () => print($pdf->output()),
                'etiquettes-petites-' . now()->format('Y-m-d-H-i') . '.pdf',
                ['Content-Type' => 'application/pdf']
            );

        } catch (\Exception $e) {
            Log::error('Erreur génération PDF étiquettes petites', [
                'error' => $e->getMessage(),
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
    public function getTubesProperty()
    {
        return $this->getBaseQuery()
                   ->orderByDesc('tubes.created_at')
                   ->paginate(20);
    }

    public function getSelectionSummaryProperty()
    {
        if (empty($this->tubesSelectionnes)) {
            return null;
        }

        $tubes = Tube::whereIn('id', $this->tubesSelectionnes)
                    ->with('prelevement')
                    ->get();

        return [
            'total' => $tubes->count(),
            'par_type' => $tubes->groupBy('prelevement.denomination')
                               ->map->count()
                               ->sortDesc()
                               ->take(3)
        ];
    }

    public function getFormatsDisponiblesProperty()
    {
        return [
            'petit' => 'Petit (32x20mm) - 30 étiquettes/page',
            'standard' => 'Standard (65x40mm) - 12 étiquettes/page'
        ];
    }

    public function render()
    {
        return view('livewire.secretaire.tubes.gestion-etiquettes', [
            'tubes' => $this->tubes,
            'selectionSummary' => $this->selectionSummary,
            'formatsDisponibles' => $this->formatsDisponibles
        ]);
    }
}