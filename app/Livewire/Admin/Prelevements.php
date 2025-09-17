<?php
namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Prelevement;
use App\Models\TypeTube;

class Prelevements extends Component
{
    use WithPagination;

    public $currentView = 'list';
    public $prelevement;
    public $darkMode = false;

    // Filtres et pagination
    public $search = '';
    public $perPage = 15;

    // Propriétés pour les formulaires - MISES À JOUR selon le nouveau modèle
    public $code = '';
    public $denomination = '';
    public $prix = 0;
    public $quantite = 1;
    public $is_active = true;
    public $type_tube_id = null;

    public $showDeleteModal = false;
    public $prelevementToDelete = null;

    protected $rules = [
        'code' => 'required|string|max:10',
        'denomination' => 'required|string|max:255',
        'prix' => 'required|numeric|min:0',
        'quantite' => 'required|integer|min:1',
        'is_active' => 'boolean',
        'type_tube_id' => 'nullable|exists:type_tubes,id',
    ];

    protected $messages = [
        'code.required' => 'Le code du prélèvement est requis.',
        'code.max' => 'Le code ne peut pas dépasser 10 caractères.',
        'code.unique' => 'Ce code de prélèvement existe déjà.',
        'denomination.required' => 'La dénomination est requise.',
        'denomination.max' => 'La dénomination ne peut pas dépasser 255 caractères.',
        'prix.required' => 'Le prix est requis.',
        'prix.numeric' => 'Le prix doit être un nombre.',
        'prix.min' => 'Le prix ne peut pas être négatif.',
        'quantite.required' => 'La quantité est requise.',
        'quantite.integer' => 'La quantité doit être un nombre entier.',
        'quantite.min' => 'La quantité doit être au minimum de 1.',
        'type_tube_id.exists' => 'Le type de tube sélectionné n\'existe pas.',
    ];

    public function mount()
    {
        // Initialiser le mode sombre depuis le localStorage ou la session
        $this->darkMode = session('darkMode', false);
    }

    // Listeners pour réinitialiser la pagination
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    // Propriété computed pour les prélèvements avec pagination et recherche
    public function getPrelevementsProperty()
    {
        $query = Prelevement::with('typeTubeRecommande')->orderBy('code');

        // Appliquer la recherche textuelle si présente
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('code', 'like', '%' . $this->search . '%')
                    ->orWhere('denomination', 'like', '%' . $this->search . '%');
            });
        }

        return $query->paginate($this->perPage);
    }

    // Propriété pour récupérer tous les types de tubes
    public function getTypesTubesProperty()
    {
        return TypeTube::orderBy('code')->get();
    }

    public function render()
    {
        return view('livewire.admin.prelevement');
    }

    public function show($id)
    {
        $this->prelevement = Prelevement::with('typeTubeRecommande')->findOrFail($id);
        $this->currentView = 'show';
    }

    public function create()
    {
        $this->resetForm();
        $this->currentView = 'create';
    }

    public function edit($id)
    {
        $this->prelevement = Prelevement::with('typeTubeRecommande')->findOrFail($id);
        $this->fillForm();
        $this->currentView = 'edit';
    }

    public function store()
    {
        $this->validate(array_merge($this->rules, [
            'code' => 'required|string|max:10|unique:prelevements,code',
        ]));

        Prelevement::create([
            'code' => strtoupper(trim($this->code)),
            'denomination' => trim($this->denomination),
            'prix' => $this->prix,
            'quantite' => $this->quantite,
            'is_active' => $this->is_active,
            'type_tube_id' => $this->type_tube_id,
        ]);

        session()->flash('message', 'Prélèvement créé avec succès !');
        $this->backToList();
    }

    public function update()
    {
        $this->validate(array_merge($this->rules, [
            'code' => 'required|string|max:10|unique:prelevements,code,' . $this->prelevement->id,
        ]));

        $this->prelevement->update([
            'code' => strtoupper(trim($this->code)),
            'denomination' => trim($this->denomination),
            'prix' => $this->prix,
            'quantite' => $this->quantite,
            'is_active' => $this->is_active,
            'type_tube_id' => $this->type_tube_id,
        ]);

        session()->flash('message', 'Prélèvement modifié avec succès !');
        $this->backToList();
    }

    public function backToList()
    {
        $this->resetForm();
        $this->prelevement = null;
        $this->currentView = 'list';
    }

    // Méthode pour toggle le statut
    public function toggleStatus($id)
    {
        $prelevement = Prelevement::findOrFail($id);
        $prelevement->update(['is_active' => !$prelevement->is_active]);

        $status = $prelevement->is_active ? 'activé' : 'désactivé';
        session()->flash('message', "Prélèvement {$status} avec succès !");
    }

    // Méthode pour réinitialiser la recherche
    public function resetSearch()
    {
        $this->search = '';
        $this->resetPage();
    }

    // Méthode pour toggle le mode sombre
    public function toggleDarkMode()
    {
        $this->darkMode = !$this->darkMode;
        session(['darkMode' => $this->darkMode]);

        // Émettre un événement pour mettre à jour le frontend
        $this->dispatch('dark-mode-toggled', $this->darkMode);
    }


    // Méthode pour générer le prochain code disponible
    private function generateNextCode($baseCode)
    {
        $counter = 1;
        $newCode = $baseCode;
        
        while (Prelevement::where('code', $newCode)->exists()) {
            $counter++;
            $newCode = $baseCode . '_' . $counter;
        }
        
        return $newCode;
    }

    // Méthodes privées
    private function fillForm()
    {
        $this->code = $this->prelevement->code;
        $this->denomination = $this->prelevement->denomination;
        $this->prix = $this->prelevement->prix;
        $this->quantite = $this->prelevement->quantite;
        $this->is_active = $this->prelevement->is_active;
        $this->type_tube_id = $this->prelevement->type_tube_id;
    }

    private function resetForm()
    {
        $this->code = '';
        $this->denomination = '';
        $this->prix = 0;
        $this->quantite = 1;
        $this->is_active = true;
        $this->type_tube_id = null;
        
        // Reset des propriétés du modal
        $this->showDeleteModal = false;
        $this->prelevementToDelete = null;
        
        $this->resetErrorBag();
    }
    
    // Méthodes utilitaires pour les statistiques - MISES À JOUR
    public function getStatsProperty()
    {
        return [
            'total' => Prelevement::count(),
            'actifs' => Prelevement::where('is_active', true)->count(),
            'inactifs' => Prelevement::where('is_active', false)->count(),
            'sanguins' => Prelevement::sanguins()->count(),
            'ecouvillons' => Prelevement::ecouvillons()->count(),
            'prix_moyen' => Prelevement::where('is_active', true)->avg('prix'),
            'prix_total' => Prelevement::where('is_active', true)->sum('prix'),
        ];
    }

    // Méthode pour obtenir les prélèvements par gamme de prix
    public function getPrelevementsByPriceRange()
    {
        return [
            'moins_20' => Prelevement::where('prix', '<', 20)->count(),
            'entre_20_50' => Prelevement::whereBetween('prix', [20, 50])->count(),
            'plus_50' => Prelevement::where('prix', '>', 50)->count(),
        ];
    }

    // Méthode pour obtenir les prélèvements par type de tube
    public function getPrelevementsByTypeTube()
    {
        return Prelevement::join('type_tubes', 'prelevements.type_tube_id', '=', 'type_tubes.id')
                         ->selectRaw('type_tubes.code as tube_code, type_tubes.couleur, COUNT(*) as total')
                         ->groupBy('type_tubes.code', 'type_tubes.couleur')
                         ->orderByDesc('total')
                         ->get();
    }

    // Méthode pour exporter les prélèvements (optionnelle)
    public function export()
    {
        // Logique d'export en CSV ou Excel
        $prelevements = Prelevement::with('typeTubeRecommande')->get();

        // Ici vous pouvez implémenter l'export
        session()->flash('message', 'Export en cours de développement...');
    }

    // Validation en temps réel
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // Méthode pour calculer le prix total avec TVA
    public function getPrixAvecTVA($prix, $tauxTVA = 20)
    {
        return $prix * (1 + $tauxTVA / 100);
    }

    // Méthode pour obtenir les prélèvements les plus utilisés - CORRIGÉE
    public function getPrelevementsPlusUtilises($limit = 5)
    {
        return Prelevement::lesPlusUtilises($limit);
    }

    // Méthode pour recherche avancée - MISE À JOUR
    public function searchAdvanced($criteria)
    {
        $query = Prelevement::query();

        if (isset($criteria['code'])) {
            $query->where('code', 'like', '%' . $criteria['code'] . '%');
        }

        if (isset($criteria['denomination'])) {
            $query->where('denomination', 'like', '%' . $criteria['denomination'] . '%');
        }

        if (isset($criteria['prix_min'])) {
            $query->where('prix', '>=', $criteria['prix_min']);
        }

        if (isset($criteria['prix_max'])) {
            $query->where('prix', '<=', $criteria['prix_max']);
        }

        if (isset($criteria['is_active'])) {
            $query->where('is_active', $criteria['is_active']);
        }

        if (isset($criteria['type_tube_id'])) {
            $query->where('type_tube_id', $criteria['type_tube_id']);
        }

        if (isset($criteria['categorie'])) {
            if ($criteria['categorie'] === 'sanguins') {
                $query->sanguins();
            } elseif ($criteria['categorie'] === 'ecouvillons') {
                $query->ecouvillons();
            }
        }

        return $query->get();
    }

    // Méthode pour obtenir les suggestions de prix basées sur des prélèvements similaires
    public function getSuggestionsPrix($denomination)
    {
        $similaires = Prelevement::where('denomination', 'like', '%' . $denomination . '%')
            ->where('is_active', true)
            ->pluck('prix')
            ->toArray();

        if (empty($similaires)) {
            return null;
        }

        return [
            'prix_moyen' => array_sum($similaires) / count($similaires),
            'prix_min' => min($similaires),
            'prix_max' => max($similaires),
        ];
    }

    // NOUVELLES MÉTHODES pour gérer les types de tubes

    // Méthode pour récupérer les informations du tube recommandé
    public function getInfoTubeRecommande($prelevementId)
    {
        $prelevement = Prelevement::find($prelevementId);
        return $prelevement ? $prelevement->getTypeTubeRecommande() : null;
    }

    // Méthode pour changer le type de tube recommandé
    public function changerTypeTube($prelevementId, $typeTubeId)
    {
        $prelevement = Prelevement::findOrFail($prelevementId);
        $prelevement->update(['type_tube_id' => $typeTubeId]);
        
        session()->flash('message', 'Type de tube mis à jour avec succès !');
    }

    // Méthode pour obtenir les prélèvements par catégorie
    public function getPrelevementsParCategorie()
    {
        return Prelevement::parCategorie();
    }

    // Ouvrir le modal de confirmation
    public function confirmDelete($id)
    {
        $this->prelevementToDelete = Prelevement::findOrFail($id);
        $this->showDeleteModal = true;
    }

    // Supprimer le prélèvement
    public function delete()
    {
        try {
            // Vérifier s'il y a des prescriptions liées (seulement si la table pivot existe)
            try {
                if (method_exists($this->prelevementToDelete, 'prescriptions') && $this->prelevementToDelete->prescriptions()->count() > 0) {
                    session()->flash('error', 'Impossible de supprimer ce prélèvement car il est utilisé dans des prescriptions.');
                    $this->closeDeleteModal();
                    return;
                }
            } catch (\Exception $e) {
                // Table pivot n'existe pas encore, on continue
            }

            // Vérifier s'il y a des tubes liés
            try {
                if (method_exists($this->prelevementToDelete, 'tubes') && $this->prelevementToDelete->tubes()->count() > 0) {
                    session()->flash('error', 'Impossible de supprimer ce prélèvement car des tubes lui sont associés.');
                    $this->closeDeleteModal();
                    return;
                }
            } catch (\Exception $e) {
                // Table tubes n'existe pas encore ou pas de relation, on continue
            }

            $this->prelevementToDelete->delete();
            session()->flash('message', 'Prélèvement supprimé avec succès !');
            $this->closeDeleteModal();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression: ' . $e->getMessage());
            $this->closeDeleteModal();
        }
    }

    // Fermer le modal
    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->prelevementToDelete = null;
    }
}