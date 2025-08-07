<?php

namespace App\Models;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Paiement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'prescription_id',
        'montant',
        'mode_paiement',
        'recu_par',
        'commission_prescripteur',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'commission_prescripteur' => 'decimal:2',
    ];

    // Relations
    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'recu_par');
    }

    // Auto-calcul de la commission avec exclusion BiologieSolidaire
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($paiement) {
            $paiement->commission_prescripteur = static::calculerCommission($paiement);
        });
        
        static::updating(function ($paiement) {
            if ($paiement->isDirty('montant')) {
                $paiement->commission_prescripteur = static::calculerCommission($paiement);
            }
        });
    }

    private static function calculerCommission($paiement)
    {
        // Charger la prescription avec le prescripteur
        $prescription = $paiement->prescription ?? Prescription::find($paiement->prescription_id);
        
        if (!$prescription || !$prescription->prescripteur) {
            return 0;
        }

        // Si le prescripteur est BiologieSolidaire, pas de commission
        if ($prescription->prescripteur->status === 'BiologieSolidaire') {
            return 0;
        }

        // Sinon, calculer la commission normale
        $pourcentage = Setting::getCommissionPourcentage();
        return $paiement->montant * ($pourcentage / 100);
    }
}