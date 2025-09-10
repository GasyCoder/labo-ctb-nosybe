<?php
namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Prelevement;

class Prelevements extends Component
{
    use WithPagination;

    public $currentView = 'list';
    public $prelevement;
    public $darkMode = false;

    // Filtres et pagination
    public $search = '';
    public $perPage = 15;

    // Propriétés pour les formulaires
    public $nom = '';
    public $description = '';
    public $prix = 0;
    public $quantite = 1;
    public $is_active = true;

    public $showDeleteModal = false;
    public $prelevementToDelete = null;

    protected $rules = [
        'nom' => 'required|string|max:255',
        'description' => 'required|string|max:1000',
        'prix' => 'required|numeric|min:0',
        'quantite' => 'required|integer|min:1',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'nom.required' => 'Le nom du prélèvement est requis.',
        'nom.max' => 'Le nom ne peut pas dépasser 255 caractères.',
        'nom.unique' => 'Ce nom de prélèvement existe déjà.',
        'description.required' => 'La description est requise.',
        'description.max' => 'La description ne peut pas dépasser 1000 caractères.',
        'prix.required' => 'Le prix est requis.',
        'prix.numeric' => 'Le prix doit être un nombre.',
        'prix.min' => 'Le prix ne peut pas être négatif.',
        'quantite.required' => 'La quantité est requise.',
        'quantite.integer' => 'La quantité doit être un nombre entier.',
        'quantite.min' => 'La quantité doit être au minimum de 1.',
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
        $query = Prelevement::orderBy('nom');

        // Appliquer la recherche textuelle si présente
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('nom', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        return $query->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.admin.prelevement');
    }

    public function show($id)
    {
        $this->prelevement = Prelevement::findOrFail($id);
        $this->currentView = 'show';
    }

    public function create()
    {
        $this->resetForm();
        $this->currentView = 'create';
    }

    public function edit($id)
    {
        $this->prelevement = Prelevement::findOrFail($id);
        $this->fillForm();
        $this->currentView = 'edit';
    }

    public function store()
    {
        $this->validate(array_merge($this->rules, [
            'nom' => 'required|string|max:255|unique:prelevements,nom',
        ]));

        Prelevement::create([
            'nom' => trim($this->nom),
            'description' => trim($this->description),
            'prix' => $this->prix,
            'quantite' => $this->quantite,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', 'Prélèvement créé avec succès !');
        $this->backToList();
    }

    public function update()
    {
        $this->validate(array_merge($this->rules, [
            'nom' => 'required|string|max:255|unique:prelevements,nom,' . $this->prelevement->id,
        ]));

        $this->prelevement->update([
            'nom' => trim($this->nom),
            'description' => trim($this->description),
            'prix' => $this->prix,
            'quantite' => $this->quantite,
            'is_active' => $this->is_active,
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

    // Méthode pour dupliquer un prélèvement
    public function duplicate($id)
    {
        $original = Prelevement::findOrFail($id);

        $copy = $original->replicate();
        $copy->nom = $original->nom . ' (Copie)';
        $copy->save();

        session()->flash('message', 'Prélèvement dupliqué avec succès !');
    }

    // Méthode pour changer le statut de plusieurs prélèvements à la fois
    public function bulkToggleStatus($ids)
    {
        $prelevements = Prelevement::whereIn('id', $ids)->get();

        foreach ($prelevements as $prelevement) {
            $prelevement->update(['is_active' => !$prelevement->is_active]);
        }

        session()->flash('message', count($ids) . ' prélèvement(s) mis à jour !');
    }

    // Méthodes privées
    private function fillForm()
    {
        $this->nom = $this->prelevement->nom;
        $this->description = $this->prelevement->description;
        $this->prix = $this->prelevement->prix;
        $this->quantite = $this->prelevement->quantite;
        $this->is_active = $this->prelevement->is_active;
    }

    private function resetForm()
    {
        $this->nom = '';
        $this->description = '';
        $this->prix = 0;
        $this->quantite = 1;
        $this->is_active = true;
        
        // Reset des propriétés du modal
        $this->showDeleteModal = false;
        $this->prelevementToDelete = null;
        
        $this->resetErrorBag();
    }
    
    // Méthodes utilitaires pour les statistiques
    public function getStatsProperty()
    {
        return [
            'total' => Prelevement::count(),
            'actifs' => Prelevement::where('is_active', true)->count(),
            'inactifs' => Prelevement::where('is_active', false)->count(),
            'prix_moyen' => Prelevement::where('is_active', true)->avg('prix'),
            'prix_total' => Prelevement::where('is_active', true)->sum('prix'),
        ];
    }

    // Méthode pour obtenir les prélèvements par gamme de prix
    public function getPrelevementsByPriceRange()
    {
        return [
            'moins_2000' => Prelevement::where('prix', '<', 2000)->count(),
            'entre_2000_5000' => Prelevement::whereBetween('prix', [2000, 5000])->count(),
            'plus_5000' => Prelevement::where('prix', '>', 5000)->count(),
        ];
    }

    // Méthode pour exporter les prélèvements (optionnelle)
    public function export()
    {
        // Logique d'export en CSV ou Excel
        $prelevements = Prelevement::all();

        // Ici vous pouvez implémenter l'export
        session()->flash('message', 'Export en cours de développement...');
    }

    // Méthode pour importer des prélèvements depuis un fichier
    public function showImportModal()
    {
        // Logique pour afficher un modal d'import
        $this->dispatch('show-import-modal');
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

    // Méthode pour obtenir les prélèvements les plus utilisés
    public function getPrelevementsPlusUtilises($limit = 5)
    {
        return Prelevement::withCount('prescriptions')
            ->orderByDesc('prescriptions_count')
            ->where('is_active', true)
            ->limit($limit)
            ->get();
    }

    // Méthode pour recherche avancée
    public function searchAdvanced($criteria)
    {
        $query = Prelevement::query();

        if (isset($criteria['nom'])) {
            $query->where('nom', 'like', '%' . $criteria['nom'] . '%');
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

        return $query->get();
    }

    // Méthode pour obtenir les suggestions de prix basées sur des prélèvements similaires
    public function getSuggestionsPrix($nom)
    {
        $similaires = Prelevement::where('nom', 'like', '%' . $nom . '%')
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


    // Ouvrir le modal de confirmation
    public function confirmDelete($id)
    {
        $this->prelevementToDelete = Prelevement::findOrFail($id);
        $this->showDeleteModal = true;
    }

    // Supprimer le prélèvement (remplace votre méthode delete existante)
    public function delete()
    {
        try {
            // Vérifier s'il y a des prescriptions liées
            if (method_exists($this->prelevementToDelete, 'prescriptions') && $this->prelevementToDelete->prescriptions()->count() > 0) {
                session()->flash('error', 'Impossible de supprimer ce prélèvement car il est utilisé dans des prescriptions.');
                $this->closeDeleteModal();
                return;
            }

            $this->prelevementToDelete->delete();
            session()->flash('message', 'Prélèvement supprimé avec succès !');
            $this->closeDeleteModal();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression.');
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