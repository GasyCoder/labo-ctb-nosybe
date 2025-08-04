<?php

namespace App\Livewire\Secretaire\Prescription;

use App\Models\Patient;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;
use App\Services\PatientService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class RecherchePatient extends Component
{
    use WithPagination;

    // 🔍 CRITÈRES RECHERCHE
    #[Rule('nullable|string|min:2|max:100')]
    public string $recherche = '';
    
    public string $critereRecherche = 'nom'; // nom, reference, telephone
    public string $trierPar = 'recent'; // recent, nom, statut
    
    // 📊 RÉSULTATS
    public ?Patient $patientSelectionne = null;
    public array $resultatRecherche = [];
    public array $patientsRecents = [];
    public int $nombreResultats = 0;
    
    // 🎯 ÉTAT INTERFACE
    public bool $afficherHistorique = false;
    public bool $rechercheEnCours = false;
    public bool $creerNouveauPatient = false;
    
    // 🔧 SERVICES
    protected PatientService $patientService;

    public function boot(PatientService $patientService)
    {
        $this->patientService = $patientService;
    }

    public function mount()
    {
        $this->chargerPatientsRecents();
    }

    // 🔍 RECHERCHE INTELLIGENTE
    public function updatedRecherche()
    {
        if (strlen($this->recherche) < 2) {
            $this->resultatRecherche = [];
            $this->nombreResultats = 0;
            $this->rechercheEnCours = false;
            return;
        }
        
        $this->rechercheEnCours = true;
        $this->rechercherPatients();
    }

    public function updatedCritereRecherche()
    {
        if (!empty($this->recherche)) {
            $this->rechercherPatients();
        }
    }

    private function rechercherPatients()
    {
        $cacheKey = "recherche_patient_{$this->critereRecherche}_{$this->recherche}_{$this->trierPar}";
        
        $resultats = Cache::remember($cacheKey, 300, function() {
            return $this->patientService->rechercherPatients(
                $this->recherche,
                $this->critereRecherche,
                $this->trierPar,
                50 // Limite pour performance
            );
        });
        
        $this->resultatRecherche = $resultats->toArray();
        $this->nombreResultats = $resultats->count();
        $this->rechercheEnCours = false;
        
        // Auto-sélection si un seul résultat exact
        if ($this->nombreResultats === 1 && $this->rechercheExacte()) {
            $this->selectionnerPatient($resultats->first()->id);
        }
    }

    private function rechercheExacte(): bool
    {
        if (empty($this->resultatRecherche)) return false;
        
        $patient = $this->resultatRecherche[0];
        
        return match($this->critereRecherche) {
            'reference' => strtolower($patient['reference']) === strtolower($this->recherche),
            'telephone' => $patient['telephone'] === $this->recherche,
            'nom' => strtolower($patient['nom']) === strtolower($this->recherche),
            default => false
        };
    }

    // 👤 SÉLECTION PATIENT
    public function selectionnerPatient(int $patientId)
    {
        $this->patientSelectionne = $this->patientService->getPatientAvecStatistiques($patientId);
        $this->afficherHistorique = true;
        
        // Mettre en cache comme patient récent
        $this->ajouterPatientRecent($this->patientSelectionne);
        
        // Émettre événement vers composant parent
        $this->dispatch('patient-selectionne', patientId: $patientId);
    }

    public function deselectionnerPatient()
    {
        $this->patientSelectionne = null;
        $this->afficherHistorique = false;
        $this->recherche = '';
        $this->resultatRecherche = [];
    }

    // 📊 PATIENTS RÉCENTS
    private function chargerPatientsRecents()
    {
        $cacheKey = "patients_recents_" . Auth::id();
        
        $this->patientsRecents = Cache::remember($cacheKey, 1800, function() {
            return $this->patientService->getPatientsRecents(Auth::id(), 10);
        });
    }

    private function ajouterPatientRecent(Patient $patient)
    {
        $cacheKey = "patients_recents_" . Auth::id();
        
        // Ajouter en début de liste et limiter à 10
        $recents = collect($this->patientsRecents)
            ->reject(fn($p) => $p['id'] === $patient->id)
            ->prepend([
                'id' => $patient->id,
                'nom' => $patient->nom,
                'prenom' => $patient->prenom,
                'reference' => $patient->reference,
                'telephone' => $patient->telephone,
                'statut' => $patient->statut,
                'derniere_visite' => now()->format('Y-m-d H:i:s')
            ])
            ->take(10)
            ->toArray();
        
        $this->patientsRecents = $recents;
        Cache::put($cacheKey, $recents, 1800);
    }

    // ➕ NOUVEAU PATIENT
    public function afficherFormulaireNouveauPatient()
    {
        $this->creerNouveauPatient = true;
        $this->dispatch('ouvrir-modal-nouveau-patient');
    }

    public function nouveauPatientCree(int $patientId)
    {
        $this->creerNouveauPatient = false;
        $this->selectionnerPatient($patientId);
    }

    // 🔄 ACTIONS INTERFACE
    public function viderRecherche()
    {
        $this->recherche = '';
        $this->resultatRecherche = [];
        $this->nombreResultats = 0;
        $this->rechercheEnCours = false;
    }

    public function changerTriRecherche(string $tri)
    {
        $this->trierPar = $tri;
        if (!empty($this->recherche)) {
            $this->rechercherPatients();
        }
    }

    // 📊 COMPUTED PROPERTIES
    public function getHistoriquePatientProperty(): array
    {
        if (!$this->patientSelectionne) return [];
        
        return $this->patientService->getHistoriqueIntelligent(
            $this->patientSelectionne->id,
            10 // Dernières 10 prescriptions
        );
    }

    public function getStatistiquesPatientProperty(): array
    {
        if (!$this->patientSelectionne) return [];
        
        return [
            'nombre_prescriptions' => $this->patientSelectionne->prescriptions_count ?? 0,
            'montant_total_depense' => $this->patientSelectionne->montant_total_depense ?? 0,
            'derniere_visite' => $this->patientSelectionne->derniere_visite,
            'analyses_frequentes' => $this->patientSelectionne->analyses_frequentes ?? [],
        ];
    }

    public function getMessageRecherche(): string
    {
        if (empty($this->recherche)) {
            return "Tapez au moins 2 caractères pour rechercher";
        }
        
        if ($this->rechercheEnCours) {
            return "Recherche en cours...";
        }
        
        if ($this->nombreResultats === 0) {
            return "Aucun patient trouvé. Voulez-vous créer un nouveau patient ?";
        }
        
        return "{$this->nombreResultats} patient(s) trouvé(s)";
    }

    public function render()
    {
        return view('livewire.secretaire.prescription.recherche-patient', [
            'messageRecherche' => $this->getMessageRecherche(),
            'historiquePatient' => $this->historiquePatient,
            'statistiquesPatient' => $this->statistiquesPatient,
        ]);
    }
}