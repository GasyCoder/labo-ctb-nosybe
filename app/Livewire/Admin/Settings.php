<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Setting;
use Illuminate\Validation\Rule;

class Settings extends Component
{
    // Propriétés du formulaire
    public $nom_entreprise = '';
    public $nif = '';
    public $statut = '';
    public $format_unite_argent = 'Ar';
    public $remise_pourcentage = 0;
    public $activer_remise = false;
    public $commission_prescripteur = true;
    public $commission_prescripteur_pourcentage = 10;

    // États du composant
    public $showSuccessMessage = false;
    public $showCommissionAlert = false;
    public $ancienPourcentage = null;

    protected $rules = [
        'nom_entreprise' => 'required|string|max:255',
        'nif' => 'nullable|string|max:100',
        'statut' => 'nullable|string|max:100',
        'format_unite_argent' => 'required|string|max:10',
        'remise_pourcentage' => 'required|numeric|min:0|max:100',
        'activer_remise' => 'boolean',
        'commission_prescripteur' => 'boolean',
        'commission_prescripteur_pourcentage' => 'required|numeric|min:0|max:100',
    ];

    protected $messages = [
        'nom_entreprise.required' => 'Le nom de l\'entreprise est obligatoire.',
        'format_unite_argent.required' => 'L\'unité monétaire est obligatoire.',
        'remise_pourcentage.required' => 'Le pourcentage de remise est obligatoire.',
        'remise_pourcentage.numeric' => 'Le pourcentage de remise doit être un nombre.',
        'remise_pourcentage.min' => 'Le pourcentage de remise ne peut pas être négatif.',
        'remise_pourcentage.max' => 'Le pourcentage de remise ne peut pas dépasser 100%.',
        'commission_prescripteur_pourcentage.required' => 'Le pourcentage de commission est obligatoire.',
        'commission_prescripteur_pourcentage.numeric' => 'Le pourcentage de commission doit être un nombre.',
        'commission_prescripteur_pourcentage.min' => 'Le pourcentage de commission ne peut pas être négatif.',
        'commission_prescripteur_pourcentage.max' => 'Le pourcentage de commission ne peut pas dépasser 100%.',
    ];

    public function mount()
    {
        $this->chargerSettings();
    }

    private function chargerSettings()
    {
        $setting = Setting::first();
        
        if ($setting) {
            $this->nom_entreprise = $setting->nom_entreprise ?? '';
            $this->nif = $setting->nif ?? '';
            $this->statut = $setting->statut ?? '';
            $this->format_unite_argent = $setting->format_unite_argent ?? 'Ar';
            $this->remise_pourcentage = (float) ($setting->remise_pourcentage ?? 0);
            $this->activer_remise = (bool) ($setting->activer_remise ?? false);
            $this->commission_prescripteur = (bool) ($setting->commission_prescripteur ?? true);
            $this->commission_prescripteur_pourcentage = (float) ($setting->commission_prescripteur_pourcentage ?? 10);

            // Stocker l'ancien pourcentage pour détecter les changements
            $this->ancienPourcentage = (float) $this->commission_prescripteur_pourcentage;
        } else {
            // Valeurs par défaut si pas de settings
            $this->ancienPourcentage = 10.0;
        }
    }


    public function updatedCommissionPrescripteurPourcentage($value)
    {
        // Si la valeur est vide ou non numérique, on ignore toute opération
        if ($value === '' || !is_numeric($value) || !is_numeric($this->ancienPourcentage)) {
            $this->showCommissionAlert = false;
            return;
        }

        $nouveauPourcentage = (float)$value;
        $ancienPourcentage = (float)$this->ancienPourcentage;

        if ($ancienPourcentage && abs($nouveauPourcentage - $ancienPourcentage) > 0) {
            $this->showCommissionAlert = true;
        } else {
            $this->showCommissionAlert = false;
        }
    }




    public function sauvegarder()
    {
        $this->validate();

        try {
            $setting = Setting::first();
            
            $data = [
                'nom_entreprise' => $this->nom_entreprise,
                'nif' => $this->nif,
                'statut' => $this->statut,
                'format_unite_argent' => $this->format_unite_argent,
                'remise_pourcentage' => (float) $this->remise_pourcentage,
                'activer_remise' => (bool) $this->activer_remise,
                'commission_prescripteur' => (bool) $this->commission_prescripteur,
                'commission_prescripteur_pourcentage' => (float) $this->commission_prescripteur_pourcentage,
            ];

            if ($setting) {
                $setting->update($data);
            } else {
                Setting::create($data);
            }

            $this->ancienPourcentage = (float) $this->commission_prescripteur_pourcentage;
            $this->showCommissionAlert = false;
            $this->showSuccessMessage = true;
            
            session()->flash('success', 'Paramètres sauvegardés avec succès ! Le recalcul automatique des commissions est en cours...');
            
            // Le recalcul automatique se déclenche via l'Event dans le modèle Setting
            
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la sauvegarde : ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->chargerSettings();
        $this->showCommissionAlert = false;
        $this->showSuccessMessage = false;
        $this->resetErrorBag();
    }


    public function getStatistiquesCommissionsProperty()
    {
        try {
            $totalPaiements = \App\Models\Paiement::count();
            $totalCommissions = \App\Models\Paiement::sum('commission_prescripteur');
            $prescripteursMedecins = \App\Models\Prescripteur::where('status', 'Medecin')->count();
            $prescripteursBiologie = \App\Models\Prescripteur::where('status', 'BiologieSolidaire')->count();
            
            return [
                'totalPaiements' => $totalPaiements,
                'totalCommissions' => (float) $totalCommissions,
                'prescripteursMedecins' => $prescripteursMedecins,
                'prescripteursBiologie' => $prescripteursBiologie,
            ];
        } catch (\Exception $e) {
            return [
                'totalPaiements' => 0,
                'totalCommissions' => 0,
                'prescripteursMedecins' => 0,
                'prescripteursBiologie' => 0,
            ];
        }
    }



    public function calculerImpactChangement()
    {
        $ancienPourcentage = (float) $this->ancienPourcentage;
        
        // Vérifier si commission_prescripteur_pourcentage est numérique
        if (!is_numeric($this->commission_prescripteur_pourcentage)) {
            return null;
        }
        
        $nouveauPourcentage = (float) $this->commission_prescripteur_pourcentage;
        
        if (!$ancienPourcentage || $nouveauPourcentage == $ancienPourcentage) {
            return null;
        }

        try {
            $paiements = \App\Models\Paiement::with('prescription.prescripteur')->get();
            $ancienTotal = 0;
            $nouveauTotal = 0;
            $paiementsAfectes = 0;

            foreach ($paiements as $paiement) {
                if ($paiement->prescription && $paiement->prescription->prescripteur) {
                    $prescripteur = $paiement->prescription->prescripteur;
                    
                    // Seuls les médecins sont affectés
                    if ($prescripteur->status === 'Medecin') {
                        $montant = (float) $paiement->montant;
                        $ancienneCommission = $montant * ($ancienPourcentage / 100);
                        $nouvelleCommission = $montant * ($nouveauPourcentage / 100);
                        
                        $ancienTotal += $ancienneCommission;
                        $nouveauTotal += $nouvelleCommission;
                        $paiementsAfectes++;
                    }
                }
            }

            return [
                'paiementsAfectes' => $paiementsAfectes,
                'ancienTotal' => $ancienTotal,
                'nouveauTotal' => $nouveauTotal,
                'difference' => $nouveauTotal - $ancienTotal,
            ];
        } catch (\Exception $e) {
            \Log::error('Erreur calcul impact changement: ' . $e->getMessage());
            return null;
        }
    }

    public function render()
    {
        return view('livewire.admin.settings', [
            'statistiques' => $this->statistiquesCommissions,
            'impactChangement' => $this->calculerImpactChangement(),
        ]);
    }
}