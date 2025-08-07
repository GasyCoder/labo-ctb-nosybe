<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Events\CommissionPourcentageChanged;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'nom_entreprise',
        'nif',
        'statut',
        'remise_pourcentage',
        'activer_remise',
        'format_unite_argent',
        'commission_prescripteur',
        'commission_prescripteur_pourcentage',
    ];

    protected $casts = [
        'remise_pourcentage' => 'float',
        'activer_remise' => 'boolean',
        'commission_prescripteur' => 'boolean',
        'commission_prescripteur_pourcentage' => 'float',
    ];
    
    public static function getCommissionPourcentage()
    {
        return static::first()?->commission_prescripteur_pourcentage ?? 10;
    }

    // DÉCLENCHEMENT AUTOMATIQUE DU RECALCUL
    protected static function boot()
    {
        parent::boot();
        
        static::updating(function ($setting) {
            // Vérifier si le pourcentage de commission a changé
            if ($setting->isDirty('commission_prescripteur_pourcentage')) {
                $ancienPourcentage = $setting->getOriginal('commission_prescripteur_pourcentage');
                $nouveauPourcentage = $setting->commission_prescripteur_pourcentage;
                
                // Déclencher l'event après la sauvegarde
                static::saved(function () use ($ancienPourcentage, $nouveauPourcentage) {
                    event(new CommissionPourcentageChanged($ancienPourcentage, $nouveauPourcentage));
                });
            }
        });
    }
}