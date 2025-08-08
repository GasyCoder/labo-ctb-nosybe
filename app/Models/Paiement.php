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
        'payment_method_id',
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

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'recu_par');
    }

    // ✅ Accesseurs pour la compatibilité
    public function getModeePaiementAttribute()
    {
        return $this->paymentMethod?->code ?? 'INCONNU';
    }
    
    public function getMethodePaiementLabelAttribute()
    {
        return $this->paymentMethod?->label ?? 'Méthode inconnue';
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

        // Récupérer le setting de commission
        $setting = Setting::first();
        if (!$setting || !$setting->commission_prescripteur) {
            return 0;
        }

        // Calculer la commission
        $pourcentage = (float) $setting->commission_prescripteur_pourcentage;
        return $paiement->montant * ($pourcentage / 100);
    }
}