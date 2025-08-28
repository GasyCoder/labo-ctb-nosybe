<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Setting;
use App\Models\PaymentMethod;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class Settings extends Component
{
    use WithFileUploads;

    // Propriétés du formulaire - Informations entreprise
    public $nom_entreprise = '';
    public $nif = '';
    public $statut = '';
    public $format_unite_argent = 'Ar';
    public $logo;
    public $favicon;
    public $logo_path = '';
    public $favicon_path = '';

    // Propriétés remises
    public $remise_pourcentage = 0;
    public $activer_remise = false;

    // Propriétés commissions
    public $commission_prescripteur = true;
    public $commission_prescripteur_pourcentage = 10;

    // Propriétés méthodes de paiement
    public $payment_methods = [];
    public $new_payment_method = [
        'code' => '',
        'label' => '',
        'is_active' => true,
        'display_order' => 1
    ];

    // États du composant
    public $showSuccessMessage = false;
    public $showCommissionAlert = false;
    public $ancienPourcentage = null;
    public $successMessage = '';

    public $editingPaymentMethod = null;
    public $edit_payment_method = [
        'code' => '',
        'label' => '',
        'is_active' => true,
        'display_order' => 1
    ];

    protected function getValidationRules()
    {
        $rules = [
            // Entreprise
            'nom_entreprise' => 'required|string|max:255',
            'nif' => 'nullable|string|max:100',
            'statut' => 'nullable|string|max:100',
            'format_unite_argent' => 'required|string|max:10',
            'logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|image|max:1024',
            
            // Remises
            'remise_pourcentage' => 'required|numeric|min:0|max:100',
            'activer_remise' => 'boolean',
            
            // Commissions
            'commission_prescripteur' => 'boolean',
            'commission_prescripteur_pourcentage' => 'required|numeric|min:0|max:100',
            
            // Nouvelles méthodes de paiement (pas d'édition)
            'new_payment_method.label' => 'required|string|max:100',
            'new_payment_method.is_active' => 'boolean',
            'new_payment_method.display_order' => 'required|integer|min:1',
        ];

        // Règle pour le code du nouveau payment method
        if (!$this->editingPaymentMethod) {
            $rules['new_payment_method.code'] = 'required|string|max:50|unique:payment_methods,code';
        }

        return $rules;
    }

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
        'new_payment_method.code.required' => 'Le code est obligatoire.',
        'new_payment_method.code.unique' => 'Ce code existe déjà.',
        'new_payment_method.label.required' => 'Le libellé est obligatoire.',
        'logo.image' => 'Le logo doit être une image.',
        'logo.max' => 'Le logo ne doit pas dépasser 2MB.',
        'favicon.image' => 'Le favicon doit être une image.',
        'favicon.max' => 'Le favicon ne doit pas dépasser 1MB.',
    ];

    public function mount()
    {
        $this->chargerSettings();
        $this->chargerPaymentMethods();
    }

    private function chargerSettings()
    {
        $setting = Setting::first();
        
        if ($setting) {
            $this->nom_entreprise = $setting->nom_entreprise ?? '';
            $this->nif = $setting->nif ?? '';
            $this->statut = $setting->statut ?? '';
            $this->format_unite_argent = $setting->format_unite_argent ?? 'Ar';
            $this->logo_path = $setting->logo ?? '';
            $this->favicon_path = $setting->favicon ?? '';
            $this->remise_pourcentage = is_numeric($setting->remise_pourcentage) ? (float) $setting->remise_pourcentage : 0;
            $this->activer_remise = (bool) ($setting->activer_remise ?? false);
            $this->commission_prescripteur = (bool) ($setting->commission_prescripteur ?? true);
            $this->commission_prescripteur_pourcentage = is_numeric($setting->commission_prescripteur_pourcentage) 
                ? (float) $setting->commission_prescripteur_pourcentage 
                : 10;

            // Stocker l'ancien pourcentage pour détecter les changements
            $this->ancienPourcentage = $this->commission_prescripteur_pourcentage;
        } else {
            // Valeurs par défaut si pas de settings
            $this->commission_prescripteur_pourcentage = 10;
            $this->ancienPourcentage = 10;
        }
    }

    public function updated($propertyName)
    {
        // Validation en temps réel pour le pourcentage de commission
        if ($propertyName === 'commission_prescripteur_pourcentage') {
            if ($this->commission_prescripteur_pourcentage !== '' && $this->commission_prescripteur_pourcentage !== null) {
                $this->validateOnly('commission_prescripteur_pourcentage', [
                    'commission_prescripteur_pourcentage' => 'required|numeric|min:0|max:100',
                ]);
            }
        }
    }

    /**
    * Passer en mode édition pour une méthode de paiement
    */
    public function modifierPaymentMethod($id)
    {
        try {
            $method = PaymentMethod::findOrFail($id);
            
            $this->editingPaymentMethod = $id;
            $this->edit_payment_method = [
                'code' => $method->code,
                'label' => $method->label,
                'is_active' => $method->is_active,
                'display_order' => $method->display_order
            ];
            
            $this->successMessage = '';
            $this->showSuccessMessage = false;
            
        } catch (\Exception $e) {
            flash()->error('Erreur lors du chargement de la méthode : ' . $e->getMessage());
        }
    }

    private function chargerPaymentMethods()
    {
        $this->payment_methods = PaymentMethod::orderBy('display_order')->get()->toArray();
    }

    public function updatedCommissionPrescripteurPourcentage($value)
    {
        // Normaliser la valeur
        if ($value === '' || $value === null) {
            $this->showCommissionAlert = false;
            return;
        }

        // Convertir en float et valider
        $nouveauPourcentage = is_numeric($value) ? (float)$value : 0;
        $ancienPourcentage = is_numeric($this->ancienPourcentage) ? (float)$this->ancienPourcentage : 0;

        // Valider que les valeurs sont dans la plage acceptable
        if ($nouveauPourcentage < 0 || $nouveauPourcentage > 100) {
            $this->showCommissionAlert = false;
            return;
        }

        if ($ancienPourcentage > 0 && abs($nouveauPourcentage - $ancienPourcentage) > 0.01) {
            $this->showCommissionAlert = true;
        } else {
            $this->showCommissionAlert = false;
        }
    }


    public function sauvegarderEntreprise()
    {
        $this->validate([
            'nom_entreprise' => 'required|string|max:255',
            'nif' => 'nullable|string|max:100',
            'statut' => 'nullable|string|max:100',
            'format_unite_argent' => 'required|string|max:10',
            'logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|image|max:1024',
        ]);

        try {
            $setting = Setting::first();
            
            $data = [
                'nom_entreprise' => $this->nom_entreprise,
                'nif' => $this->nif,
                'statut' => $this->statut,
                'format_unite_argent' => $this->format_unite_argent,
            ];

            // Gestion upload logo
            if ($this->logo) {
                $logoPath = $this->logo->store('logos', 'public');
                $data['logo'] = $logoPath;
                $this->logo_path = $logoPath;
                $this->logo = null;
            }

            // Gestion upload favicon
            if ($this->favicon) {
                $faviconPath = $this->favicon->store('favicons', 'public');
                $data['favicon'] = $faviconPath;
                $this->favicon_path = $faviconPath;
                $this->favicon = null;
            }

            if ($setting) {
                $setting->update($data);
            } else {
                Setting::create($data);
            }

            flash()->success('🏢 Informations de l\'entreprise sauvegardées avec succès !');
            
        } catch (\Exception $e) {
            flash()->error('❌ Erreur lors de la sauvegarde : ' . $e->getMessage());
        }
    }

    public function sauvegarderRemises()
    {
        $this->validate([
            'remise_pourcentage' => 'required|numeric|min:0|max:100',
            'activer_remise' => 'boolean',
        ]);

        try {
            $setting = Setting::first();
            
            $data = [
                'remise_pourcentage' => (float) $this->remise_pourcentage,
                'activer_remise' => (bool) $this->activer_remise,
            ];

            if ($setting) {
                $setting->update($data);
            } else {
                $setting = Setting::create(array_merge($data, [
                    'nom_entreprise' => 'Mon Entreprise',
                    'format_unite_argent' => 'Ar'
                ]));
            }

            flash()->success('🏷️ Configuration des remises sauvegardée avec succès !');
            
        } catch (\Exception $e) {
            flash()->error('❌ Erreur lors de la sauvegarde : ' . $e->getMessage());
        }
    }

    public function sauvegarderCommissions()
    {
        // Validation renforcée
        $this->validate([
            'commission_prescripteur' => 'boolean',
            'commission_prescripteur_pourcentage' => 'required|numeric|min:0|max:100',
        ]);

        try {
            // S'assurer que la valeur est numérique
            $pourcentage = is_numeric($this->commission_prescripteur_pourcentage) 
                ? (float) $this->commission_prescripteur_pourcentage 
                : 0;

            $setting = Setting::first();
            
            $data = [
                'commission_prescripteur' => (bool) $this->commission_prescripteur,
                'commission_prescripteur_pourcentage' => $pourcentage,
            ];

            if ($setting) {
                $setting->update($data);
            } else {
                $setting = Setting::create(array_merge($data, [
                    'nom_entreprise' => 'Mon Entreprise',
                    'format_unite_argent' => 'Ar'
                ]));
            }

            $this->ancienPourcentage = $pourcentage;
            $this->showCommissionAlert = false;
            
            flash()->success('💰 Configuration des commissions sauvegardée avec succès ! Le recalcul automatique est en cours...');
            
        } catch (\Exception $e) {
            flash()->error('❌ Erreur lors de la sauvegarde : ' . $e->getMessage());
        }
    }

    public function ajouterPaymentMethod()
    {
        $this->validate([
            'new_payment_method.code' => 'required|string|max:50|unique:payment_methods,code',
            'new_payment_method.label' => 'required|string|max:100',
            'new_payment_method.is_active' => 'boolean',
            'new_payment_method.display_order' => 'required|integer|min:1',
        ]);

        try {
            PaymentMethod::create($this->new_payment_method);
            
            $this->chargerPaymentMethods();
            $this->resetNewPaymentMethod();
            
            flash()->success("💳 Méthode de paiement « {$this->new_payment_method['label']} » ajoutée avec succès !");
            
        } catch (\Exception $e) {
            flash()->error('❌ Erreur lors de l\'ajout : ' . $e->getMessage());
        }
    }

    public function togglePaymentMethodStatus($id)
    {
        try {
            $method = PaymentMethod::findOrFail($id);
            $ancienStatut = $method->is_active;
            $method->update(['is_active' => !$method->is_active]);
            
            $this->chargerPaymentMethods();
            
            $nouveauStatut = $ancienStatut ? 'désactivée' : 'activée';
            $icone = $ancienStatut ? '🔴' : '🟢';
            
            flash()->success("{$icone} Méthode « {$method->label} » {$nouveauStatut} avec succès !");
            
        } catch (\Exception $e) {
            flash()->error('❌ Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    public function supprimerPaymentMethod($id)
    {
        try {
            $method = PaymentMethod::findOrFail($id);
            $nomMethode = $method->label; // Sauvegarder le nom avant suppression
            $method->delete();
            $this->chargerPaymentMethods();
            $this->resetNewPaymentMethod();
            
            flash()->success("🗑️ Méthode de paiement « {$nomMethode} » supprimée avec succès !");
            
        } catch (\Exception $e) {
            flash()->error('❌ Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    private function resetNewPaymentMethod()
    {
        $this->new_payment_method = [
            'code' => '',
            'label' => '',
            'is_active' => true,
            'display_order' => count($this->payment_methods) + 1
        ];
    }

    public function resetForm()
    {
        $this->chargerSettings();
        $this->chargerPaymentMethods();
        $this->resetNewPaymentMethod();
        $this->annulerEdition();
        $this->showCommissionAlert = false;
        $this->showSuccessMessage = false;
        $this->resetErrorBag();
        
        flash()->info('🔄 Formulaire réinitialisé avec succès !');
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
        // Validation stricte des valeurs d'entrée
        if (!is_numeric($this->ancienPourcentage) || !is_numeric($this->commission_prescripteur_pourcentage)) {
            return null;
        }

        $ancienPourcentage = (float) $this->ancienPourcentage;
        $nouveauPourcentage = (float) $this->commission_prescripteur_pourcentage;
        
        // Vérifier que les valeurs sont valides
        if ($ancienPourcentage < 0 || $nouveauPourcentage < 0 || 
            $ancienPourcentage > 100 || $nouveauPourcentage > 100) {
            return null;
        }
        
        if ($ancienPourcentage == 0 || abs($nouveauPourcentage - $ancienPourcentage) < 0.01) {
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
                        $montant = is_numeric($paiement->montant) ? (float) $paiement->montant : 0;
                        
                        // Éviter la division par zéro et s'assurer que les calculs sont valides
                        if ($montant > 0) {
                            $ancienneCommission = $montant * ($ancienPourcentage / 100);
                            $nouvelleCommission = $montant * ($nouveauPourcentage / 100);
                            
                            $ancienTotal += $ancienneCommission;
                            $nouveauTotal += $nouvelleCommission;
                            $paiementsAfectes++;
                        }
                    }
                }
            }

            return [
                'paiementsAfectes' => $paiementsAfectes,
                'ancienTotal' => round($ancienTotal, 2),
                'nouveauTotal' => round($nouveauTotal, 2),
                'difference' => round($nouveauTotal - $ancienTotal, 2),
            ];
        } catch (\Exception $e) {
            \Log::error('Erreur calcul impact changement: ' . $e->getMessage());
            return null;
        }
    }


    /**
     * Sauvegarder les modifications d'une méthode de paiement
     */
    public function sauvegarderPaymentMethod($id)
    {
        // Validation spécifique pour l'édition
        $this->validate([
            'edit_payment_method.code' => [
                'required',
                'string',
                'max:50',
                // Unique sauf pour l'enregistrement actuel
                Rule::unique('payment_methods', 'code')->ignore($id)
            ],
            'edit_payment_method.label' => 'required|string|max:100',
            'edit_payment_method.is_active' => 'boolean',
            'edit_payment_method.display_order' => 'required|integer|min:1',
        ], [
            'edit_payment_method.code.required' => 'Le code est obligatoire.',
            'edit_payment_method.code.unique' => 'Ce code existe déjà.',
            'edit_payment_method.label.required' => 'Le libellé est obligatoire.',
            'edit_payment_method.display_order.required' => 'L\'ordre d\'affichage est obligatoire.',
            'edit_payment_method.display_order.min' => 'L\'ordre d\'affichage doit être au minimum 1.',
        ]);

        try {
            $method = PaymentMethod::findOrFail($id);
            
            $method->update([
                'code' => strtoupper(trim($this->edit_payment_method['code'])),
                'label' => trim($this->edit_payment_method['label']),
                'is_active' => $this->edit_payment_method['is_active'],
                'display_order' => $this->edit_payment_method['display_order']
            ]);
            
            $this->chargerPaymentMethods();
            $this->annulerEdition();
            
            flash()->success("✅ Méthode de paiement « {$method->label} » mise à jour avec succès !");
            
        } catch (\Exception $e) {
            flash()->error('❌ Erreur lors de la modification : ' . $e->getMessage());
        }
    }

    /**
     * Annuler l'édition en cours
     */
    public function annulerEdition()
    {
        $this->editingPaymentMethod = null;
        $this->edit_payment_method = [
            'code' => '',
            'label' => '',
            'is_active' => true,
            'display_order' => 1
        ];
        $this->resetErrorBag(['edit_payment_method.code', 'edit_payment_method.label', 'edit_payment_method.display_order']);
        
        flash()->info('🚫 Édition annulée');
    }

    public function render()
    {
        return view('livewire.admin.settings', [
            'statistiques' => $this->statistiquesCommissions,
            'impactChangement' => $this->calculerImpactChangement(),
        ]);
    }
}