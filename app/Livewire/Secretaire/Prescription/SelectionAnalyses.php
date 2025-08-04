<?php

namespace App\Livewire\Secretaire\Prescription;

use App\Models\Analyse;
use App\Services\AnalyseService;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Cache;

class SelectionAnalyses extends Component
{
    use WithPagination;

    // 🔍 RECHERCHE ET FILTRES
    public string $rechercheAnalyse = '';
    public ?int $categorieSelectionnee = null;
    public string $triPar = 'nom'; // nom, prix, frequence
    public bool $uniquementDisponibles = true;
    public bool $afficherSuggestions = true;
    
    // 📊 ÉTAT INTERFACE
    public string $modeAffichage = 'hierarchique'; // hierarchique, liste, grille
    public array $categoriesOuvertes = [];
    public bool $rechercheAvancee = false;
    
    // 🎯 SÉLECTION
    public array $analysesPanier = [];
    public array $analysesSelectionnees = [];
    public array $suggestionsPour = [];
    
    // 🔧 SERVICES
    protected AnalyseService $analyseService;

    public function boot(AnalyseService $analyseService)
    {
        $this->analyseService = $analyseService;
    }

    public function mount()
    {
        // Récupérer panier depuis composant parent si disponible
        $this->analysesPanier = $this->getPanierFromParent();
        
        // Charger suggestions si patient sélectionné
        $this->chargerSuggestions();
        
        // Ouvrir les premières catégories
        $this->categoriesOuvertes = $this->getCategoriesPrincipales()->take(3)->pluck('id')->toArray();
    }

    // 🔍 RECHERCHE INTELLIGENTE
    public function updatedRechercheAnalyse()
    {
        $this->resetPage();
        
        if (strlen($this->rechercheAnalyse) >= 2) {
            // Charger suggestions temps réel
            $this->chargerSuggestionsRecherche();
        }
    }

    private function chargerSuggestionsRecherche()
    {
        $cacheKey = "suggestions_analyse_" . md5($this->rechercheAnalyse);
        
        $this->suggestionsPour = Cache::remember($cacheKey, 300, function() {
            return $this->analyseService->rechercherAvecSuggestions(
                $this->rechercheAnalyse,
                10
            );
        });
    }

    // 🏷️ GESTION CATÉGORIES
    public function toggleCategorie(int $categorieId)
    {
        if (in_array($categorieId, $this->categoriesOuvertes)) {
            $this->categoriesOuvertes = array_values(
                array_diff($this->categoriesOuvertes, [$categorieId])
            );
        } else {
            $this->categoriesOuvertes[] = $categorieId;
        }
    }

    public function selectionnerCategorie(?int $categorieId)
    {
        $this->categorieSelectionnee = $categorieId;
        $this->resetPage();
        
        // Ouvrir automatiquement la catégorie sélectionnée
        if ($categorieId && !in_array($categorieId, $this->categoriesOuvertes)) {
            $this->categoriesOuvertes[] = $categorieId;
        }
    }

    // ➕ AJOUT/SUPPRESSION ANALYSES
    public function ajouterAnalyse(int $analyseId)
    {
        if (isset($this->analysesPanier[$analyseId])) {
            session()->flash('warning', 'Analyse déjà dans le panier');
            return;
        }

        $analyse = $this->analyseService->getAnalyseAvecDetails($analyseId);
        
        // Vérifications métier
        if (!$this->analyseService->estDisponible($analyse)) {
            session()->flash('error', 'Analyse temporairement indisponible');
            return;
        }

        // Vérifier compatibilité avec panier existant
        $incompatibles = $this->analyseService->verifierCompatibilite(
            $analyseId, 
            array_keys($this->analysesPanier)
        );
        
        if (!empty($incompatibles)) {
            session()->flash('error', 'Analyse incompatible avec : ' . implode(', ', $incompatibles));
            return;
        }

        // Ajouter au panier
        $this->analysesPanier[$analyseId] = [
            'id' => $analyse->id,
            'nom' => $analyse->nom,
            'prix' => $analyse->prix,
            'parent_nom' => $analyse->parent?->nom,
            'quantite' => 1,
            'prelevement_requis' => $analyse->prelevement_requis,
            'type_tube_requis' => $analyse->type_tube_requis,
            'volume_echantillon_ml' => $analyse->volume_echantillon_ml,
            'est_urgente' => $analyse->est_urgente ?? false,
        ];

        // Émettre événement vers composant parent
        $this->dispatch('analyse-ajoutee', analyseId: $analyseId);
        
        // Charger analyses complémentaires suggérées
        $this->chargerAnalysesComplementaires($analyseId);
        
        session()->flash('success', "« {$analyse->nom} » ajoutée au panier");
    }

    public function retirerAnalyse(int $analyseId)
    {
        if (isset($this->analysesPanier[$analyseId])) {
            $nomAnalyse = $this->analysesPanier[$analyseId]['nom'];
            unset($this->analysesPanier[$analyseId]);
            
            $this->dispatch('analyse-retiree', analyseId: $analyseId);
            session()->flash('info', "« {$nomAnalyse} » retirée du panier");
        }
    }

    private function chargerAnalysesComplementaires(int $analyseId)
    {
        $complementaires = $this->analyseService->getAnalysesComplementaires($analyseId);
        
        if (!empty($complementaires)) {
            $this->suggestionsPour = array_merge(
                $this->suggestionsPour,
                $complementaires
            );
        }
    }

    // 🎯 SÉLECTION RAPIDE
    public function ajouterPack(string $typePack)
    {
        $analyses = match($typePack) {
            'bilan_basic' => $this->analyseService->getPackBilanBasic(),
            'bilan_complet' => $this->analyseService->getPackBilanComplet(),
            'bilan_lipidique' => $this->analyseService->getPackBilanLipidique(),
            'bilan_hepatique' => $this->analyseService->getPackBilanHepatique(),
            'bilan_renal' => $this->analyseService->getPackBilanRenal(),
            'bilan_thyroide' => $this->analyseService->getPackBilanThyroide(),
            default => []
        };

        $ajoutees = 0;
        foreach ($analyses as $analyse) {
            if (!isset($this->analysesPanier[$analyse->id])) {
                $this->ajouterAnalyse($analyse->id);
                $ajoutees++;
            }
        }
        
        if ($ajoutees > 0) {
            session()->flash('success', "{$ajoutees} analyses ajoutées du pack {$typePack}");
        }
    }

    public function appliquerSuggestions()
    {
        if (empty($this->suggestionsPour)) {
            session()->flash('info', 'Aucune suggestion disponible');
            return;
        }

        $ajoutees = 0;
        foreach ($this->suggestionsPour as $suggestion) {
            if (!isset($this->analysesPanier[$suggestion['id']])) {
                $this->ajouterAnalyse($suggestion['id']);
                $ajoutees++;
            }
        }
        
        session()->flash('success', "{$ajoutees} analyses suggérées ajoutées");
    }

    // 🔄 GESTION MODE AFFICHAGE
    public function changerModeAffichage(string $mode)
    {
        $this->modeAffichage = $mode;
        $this->resetPage();
    }

    public function toggleRechercheAvancee()
    {
        $this->rechercheAvancee = !$this->rechercheAvancee;
        
        if (!$this->rechercheAvancee) {
            // Reset filtres avancés
            $this->categorieSelectionnee = null;
            $this->triPar = 'nom';
            $this->uniquementDisponibles = true;
        }
    }

    // 📊 MÉTHODES PRIVÉES
    private function getPanierFromParent(): array
    {
        // Récupérer depuis le composant parent ou session
        return session('analyses_panier', []);
    }

    private function chargerSuggestions()
    {
        if (!$this->afficherSuggestions) return;
        
        // Suggestions basées sur patient si disponible
        $patientId = session('patient_selectionne_id');
        if ($patientId) {
            $this->suggestionsPour = $this->analyseService->getSuggestionsPatient($patientId);
        }
    }

    // 📊 COMPUTED PROPERTIES
    public function getCategoriesPrincipalesProperty()
    {
        return Cache::remember('categories_principales_analyses', 3600, function() {
            return Analyse::whereNull('parent_id')
                          ->where('is_active', true)
                          ->with(['children' => function($query) {
                              $query->where('is_active', true)
                                   ->orderBy('nom');
                          }])
                          ->orderBy('ordre')
                          ->orderBy('nom')
                          ->get();
        });
    }

    public function getAnalysesAffichageProperty()
    {
        $query = Analyse::with(['parent'])
                        ->where('is_active', true);

        // Appliquer recherche
        if (!empty($this->rechercheAnalyse)) {
            $terme = strtolower($this->rechercheAnalyse);
            $query->where(function($q) use ($terme) {
                $q->whereRaw('LOWER(nom) LIKE ?', ["%{$terme}%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%{$terme}%"])
                  ->orWhereRaw('LOWER(synonymes) LIKE ?', ["%{$terme}%"]);
            });
        }

        // Appliquer filtre catégorie
        if ($this->categorieSelectionnee) {
            $query->where(function($q) {
                $q->where('parent_id', $this->categorieSelectionnee)
                  ->orWhere('id', $this->categorieSelectionnee);
            });
        }

        // Afficher seulement les analyses finales (pas les catégories)
        if ($this->modeAffichage === 'liste') {
            $query->whereNotNull('parent_id');
        }

        // Filtre disponibilité
        if ($this->uniquementDisponibles) {
            $query->where('is_available', true);
        }

        // Tri
        match($this->triPar) {
            'nom' => $query->orderBy('nom'),
            'prix' => $query->orderBy('prix'),
            'frequence' => $query->orderByDesc('compteur_utilisation'),
            default => $query->orderBy('nom')
        };

        return $query->paginate(50);
    }

    public function getPacksDisponiblesProperty(): array
    {
        return [
            'bilan_basic' => [
                'nom' => 'Bilan Basic',
                'description' => 'Hémogramme, Glycémie, Urée, Créatinine',
                'prix' => 25000,
                'analyses_count' => 4
            ],
            'bilan_complet' => [
                'nom' => 'Bilan Complet',
                'description' => 'Bilan basic + Lipides + Hépatique',
                'prix' => 45000,
                'analyses_count' => 12
            ],
            'bilan_lipidique' => [
                'nom' => 'Bilan Lipidique',
                'description' => 'Cholestérol total, HDL, LDL, Triglycérides',
                'prix' => 18000,
                'analyses_count' => 4
            ],
            'bilan_hepatique' => [
                'nom' => 'Bilan Hépatique',
                'description' => 'ALAT, ASAT, Bilirubine, GGT',
                'prix' => 20000,
                'analyses_count' => 4
            ],
        ];
    }

    public function getStatistiquesPanierProperty(): array
    {
        if (empty($this->analysesPanier)) {
            return [
                'nombre_analyses' => 0,
                'montant_total' => 0,
                'prelevements_requis' => [],
                'temps_estime' => 0
            ];
        }

        $prelevements = collect($this->analysesPanier)
            ->pluck('prelevement_requis')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $montantTotal = collect($this->analysesPanier)
            ->sum('prix');

        $tempsEstime = collect($this->analysesPanier)
            ->sum(fn($analyse) => $analyse['est_urgente'] ? 2 : 24); // heures

        return [
            'nombre_analyses' => count($this->analysesPanier),
            'montant_total' => $montantTotal,
            'prelevements_requis' => $prelevements,
            'temps_estime' => $tempsEstime
        ];
    }

    // 🎯 ÉVÉNEMENTS PUBLICS
    #[On('panier-vide')]
    public function viderPanier()
    {
        $this->analysesPanier = [];
        $this->dispatch('analyses-panier-vide');
        session()->flash('info', 'Panier vidé');
    }

    #[On('patient-change')]
    public function patientChange(int $patientId)
    {
        // Recharger suggestions pour nouveau patient
        $this->chargerSuggestions();
    }

    public function render()
    {
        return view('livewire.secretaire.prescription.selection-analyses', [
            'categoriesPrincipales' => $this->categoriesPrincipales,
            'analysesAffichage' => $this->analysesAffichage,
            'packsDisponibles' => $this->packsDisponibles,
            'statistiquesPanier' => $this->statistiquesPanier,
        ]);
    }
}