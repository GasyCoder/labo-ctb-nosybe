<?php

namespace App\Livewire\Secretaire;

use Livewire\Component;
use App\Models\Prescripteur;
use App\Models\Setting;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class Prescripteurs extends Component
{
    use WithPagination;

    // Propriétés de filtrage et tri
    public $search = '';
    public $specialiteFilter = '';
    public $statutFilter = '';
    public $sortField = 'nom';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Propriétés des modals
    public $showCommissionModal = false;
    public $showPrescripteurModal = false;
    public $showDeleteModal = false;

    // Propriétés pour le prescripteur en cours d'édition
    public $prescripteurId = null;
    public $nom = '';
    public $prenom = '';
    public $grade = '';
    public $specialite = '';
    public $status = 'Medecin';
    public $telephone = '';
    public $email = '';
    public $adresse = '';
    public $ville = '';
    public $code_postal = '';
    public $notes = '';
    public $is_active = true;

    // Propriétés pour les commissions
    public $selectedPrescripteur;
    public $dateDebut;
    public $dateFin;
    public $commissionDetails = [];
    public $commissionPourcentage = 10; 

    // Propriétés pour la suppression
    public $prescripteurToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'specialiteFilter' => ['except' => ''],
        'statutFilter' => ['except' => ''],
        'sortField' => ['except' => 'nom'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10],
    ];

    protected $rules = [
        'nom' => 'required|min:2|max:100',
        'prenom' => 'nullable|max:100',
        'grade' => 'nullable|max:20',
        'specialite' => 'nullable|max:100',
        'status' => 'required|in:Medecin,BiologieSolidaire',
        'telephone' => 'nullable|max:20',
        'email' => 'nullable|email|max:150',
        'adresse' => 'nullable|max:255',
        'ville' => 'nullable|max:100',
        'code_postal' => 'nullable|max:10',
        'notes' => 'nullable|max:1000',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'nom.required' => 'Le nom est obligatoire.',
        'nom.min' => 'Le nom doit contenir au moins 2 caractères.',
        'status.required' => 'Le statut est obligatoire.',
        'status.in' => 'Le statut sélectionné n\'est pas valide.',
        'email.email' => 'Le format de l\'email n\'est pas valide.',
        'email.unique' => 'Cet email est déjà utilisé.',
    ];

    public function mount()
    {
        $this->dateDebut = now()->startOfYear()->format('Y-m-d');
        $this->dateFin = now()->format('Y-m-d');
        $this->commissionPourcentage = Setting::getCommissionPourcentage();
    }

    // MÉTHODES DE FILTRAGE ET TRI
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function resetFilters()
    {
        $this->reset(['search', 'specialiteFilter', 'statutFilter']);
        $this->resetPage();
    }

    // MÉTHODES CRUD PRESCRIPTEUR
    public function createPrescripteur()
    {
        $this->resetPrescripteurForm();
        $this->prescripteurId = null;
        $this->showPrescripteurModal = true;
    }

    public function editPrescripteur($id)
    {
        $prescripteur = Prescripteur::findOrFail($id);
        
        $this->prescripteurId = $prescripteur->id;
        $this->nom = $prescripteur->nom;
        $this->prenom = $prescripteur->prenom;
        $this->grade = $prescripteur->grade;
        $this->specialite = $prescripteur->specialite;
        $this->status = $prescripteur->status ?? 'Medecin';
        $this->telephone = $prescripteur->telephone;
        $this->email = $prescripteur->email;
        $this->adresse = $prescripteur->adresse;
        $this->ville = $prescripteur->ville;
        $this->code_postal = $prescripteur->code_postal;
        $this->notes = $prescripteur->notes;
        $this->is_active = $prescripteur->is_active;

        $this->showPrescripteurModal = true;
    }

    public function savePrescripteur()
    {
        $rules = $this->rules;
        if ($this->email) {
            $rules['email'] = [
                'email',
                'max:150',
                Rule::unique('prescripteurs', 'email')->ignore($this->prescripteurId)
            ];
        }

        $this->validate($rules);

        try {
            $data = [
                'nom' => $this->nom,
                'prenom' => $this->prenom,
                'grade' => $this->grade,
                'specialite' => $this->specialite,
                'status' => $this->status,
                'telephone' => $this->telephone,
                'email' => $this->email,
                'adresse' => $this->adresse,
                'ville' => $this->ville,
                'code_postal' => $this->code_postal,
                'notes' => $this->notes,
                'is_active' => $this->is_active,
            ];

            if ($this->prescripteurId) {
                Prescripteur::findOrFail($this->prescripteurId)->update($data);
                flash()->success('Prescripteur modifié avec succès.');
            } else {
                Prescripteur::create($data);
                flash()->success('Prescripteur créé avec succès.');
            }

            $this->closePrescripteurModal();
            
        } catch (\Exception $e) {
             flash()->error('Une erreur s\'est produite lors de l\'enregistrement.');
        }
    }

    public function toggleStatus($id)
    {
        try {
            $prescripteur = Prescripteur::findOrFail($id);
            $prescripteur->update(['is_active' => !$prescripteur->is_active]);
            
            $status = $prescripteur->is_active ? 'activé' : 'désactivé';
            flash()->success("Prescripteur {$status} avec succès.");
        } catch (\Exception $e) {
            flash()->error('Une erreur s\'est produite lors du changement de statut.');
        }
    }

    public function confirmDelete($id)
    {
        $this->prescripteurToDelete = Prescripteur::findOrFail($id);
        $this->showDeleteModal = true;
    }

    public function deletePrescripteur()
    {
        try {
            if ($this->prescripteurToDelete) {
                if ($this->prescripteurToDelete->prescriptions()->count() > 0) {
                    $this->prescripteurToDelete->delete();
                    flash()->warning('Prescripteur archivé avec succès (prescriptions existantes).');
                } else {
                    $this->prescripteurToDelete->forceDelete();
                    flash()->success('Prescripteur supprimé définitivement avec succès.');
                }
                
                $this->showDeleteModal = false;
                $this->prescripteurToDelete = null;
            }
        } catch (\Exception $e) {
                    flash()->error('Impossible de supprimer ce prescripteur.');
        }
    }

    private function resetPrescripteurForm()
    {
        $this->reset([
            'nom', 'prenom', 'grade', 'specialite', 'status', 'telephone',
            'email', 'adresse', 'ville', 'code_postal', 'notes'
        ]);
        $this->is_active = true;
        $this->status = 'Medecin';
        $this->resetErrorBag();
    }

    public function closePrescripteurModal()
    {
        $this->showPrescripteurModal = false;
        $this->resetPrescripteurForm();
        $this->prescripteurId = null;
    }

    // MÉTHODES POUR LES COMMISSIONS (AVEC EXCLUSION BiologieSolidaire)
    public function showCommissions($prescripteurId)
    {
        $this->selectedPrescripteur = Prescripteur::find($prescripteurId);
        
        // Vérifier si le prescripteur est BiologieSolidaire
        if ($this->selectedPrescripteur && $this->selectedPrescripteur->status === 'BiologieSolidaire') {
            flash()->info('Les prescripteurs de Biologie Solidaire ne perçoivent pas de commission.');
            return;
        }
        
        $this->commissionPourcentage = Setting::getCommissionPourcentage();
        
        if (!$this->dateDebut) {
            $this->dateDebut = now()->startOfYear()->format('Y-m-d');
        }
        if (!$this->dateFin) {
            $this->dateFin = now()->format('Y-m-d');
        }
        
        $this->loadCommissionDetailsAll();
        
        $this->showCommissionModal = true;
    }

    // Charger toutes les données sans filtre
    public function loadCommissionDetailsAll()
    {
        if (!$this->selectedPrescripteur || $this->selectedPrescripteur->status === 'BiologieSolidaire') {
            $this->commissionDetails = [
                'data' => collect([]),
                'total_commission' => 0,
                'total_prescriptions' => 0,
                'montant_total_analyses' => 0,
                'montant_total_paye' => 0,
                'commission_moyenne' => 0
            ];
            return;
        }

        try {
            $statistiques = $this->selectedPrescripteur->getStatistiquesCommissions(null, null);
            $commissions = $this->selectedPrescripteur->getCommissionsParMois(null, null, null);

            $this->commissionDetails = [
                'data' => $commissions,
                ...$statistiques
            ];

        } catch (\Exception $e) {
            $this->commissionDetails = [
                'data' => collect([]),
                'total_commission' => 0,
                'total_prescriptions' => 0,
                'montant_total_analyses' => 0,
                'montant_total_paye' => 0,
                'commission_moyenne' => 0
            ];

            flash()->error('Erreur lors du chargement des commissions.');
        }
    }

    public function updatedDateDebut()
    {
        if ($this->dateDebut && $this->dateFin && $this->selectedPrescripteur && $this->selectedPrescripteur->status !== 'BiologieSolidaire') {
            $this->loadCommissionDetails();
        }
    }

    public function updatedDateFin()
    {
        if ($this->dateDebut && $this->dateFin && $this->selectedPrescripteur && $this->selectedPrescripteur->status !== 'BiologieSolidaire') {
            $this->loadCommissionDetails();
        }
    }

    public function loadCommissionDetails()
    {
        if (!$this->selectedPrescripteur || $this->selectedPrescripteur->status === 'BiologieSolidaire') {
            $this->commissionDetails = [
                'data' => collect([]),
                'total_commission' => 0,
                'total_prescriptions' => 0,
                'montant_total_analyses' => 0,
                'montant_total_paye' => 0,
                'commission_moyenne' => 0
            ];
            return;
        }

        try {
            $statistiques = $this->selectedPrescripteur->getStatistiquesCommissions(
                $this->dateDebut, 
                $this->dateFin
            );

            $commissions = $this->selectedPrescripteur->getCommissionsParMois(
                null,
                $this->dateDebut,
                $this->dateFin
            );

            $this->commissionDetails = [
                'data' => $commissions,
                ...$statistiques
            ];

        } catch (\Exception $e) {
            $this->commissionDetails = [
                'data' => collect([]),
                'total_commission' => 0,
                'total_prescriptions' => 0,
                'montant_total_analyses' => 0,
                'montant_total_paye' => 0,
                'commission_moyenne' => 0
            ];

            flash()->error('Erreur lors du filtrage des commissions: ' . $e->getMessage());
        }
    }

    private function calculateGlobalStatistics()
    {
        try {
            $totalPrescripteurs = Prescripteur::count();
            $prescripteursActifs = Prescripteur::where('is_active', true)->count();
            
            // Calcul uniquement pour les prescripteurs commissionnables (excluant BiologieSolidaire)
            $result = DB::table('paiements')
                ->join('prescriptions', 'paiements.prescription_id', '=', 'prescriptions.id')
                ->join('prescripteurs', 'prescriptions.prescripteur_id', '=', 'prescripteurs.id')
                ->where('prescripteurs.status', '!=', 'BiologieSolidaire')
                ->whereNull('paiements.deleted_at')
                ->selectRaw('
                    COUNT(*) as total_paiements,
                    SUM(paiements.commission_prescripteur) as total_commissions
                ')
                ->first();

            $totalCommissions = $result->total_commissions ?? 0;
            $totalPaiements = $result->total_paiements ?? 0;

            return [
                'totalPrescripteurs' => $totalPrescripteurs,
                'prescripteursActifs' => $prescripteursActifs,
                'totalCommissions' => $totalCommissions,
                'totalPrescriptionsCommissionnables' => $totalPaiements
            ];

        } catch (\Exception $e) {
            return [
                'totalPrescripteurs' => Prescripteur::count(),
                'prescripteursActifs' => Prescripteur::where('is_active', true)->count(),
                'totalCommissions' => 0,
                'totalPrescriptionsCommissionnables' => 0
            ];
        }
    }

    public function render()
    {
        $prescripteurs = Prescripteur::query()
            ->withCount([
                'prescriptions as total_prescriptions',
                'prescriptions as prescriptions_commissionnables' => function($q) {
                    $q->whereHas('paiements');
                }
            ])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nom', 'like', '%'.$this->search.'%')
                    ->orWhere('prenom', 'like', '%'.$this->search.'%')
                    ->orWhere('grade', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('telephone', 'like', '%'.$this->search.'%')
                    ->orWhere('specialite', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->specialiteFilter, function ($query) {
                $query->where('specialite', $this->specialiteFilter);
            })
            ->when($this->statutFilter, function ($query) {
                $query->where('is_active', $this->statutFilter === 'actif');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $statistiques = $this->calculateGlobalStatistics();
        
        $specialites = Prescripteur::select('specialite')
            ->distinct()
            ->whereNotNull('specialite')
            ->where('specialite', '!=', '')
            ->orderBy('specialite')
            ->pluck('specialite');

        $grades = Prescripteur::getGradesDisponibles();
        $statusOptions = Prescripteur::getStatusDisponibles();

        return view('livewire.secretaire.prescripteurs', array_merge([
            'prescripteurs' => $prescripteurs,
            'specialites' => $specialites,
            'grades' => $grades,
            'statusOptions' => $statusOptions,
        ], $statistiques));
    }
}