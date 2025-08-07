<?php

namespace App\Models;

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

    // Auto-calcul de la commission
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($paiement) {
            $paiement->commission_prescripteur = $paiement->montant * 0.10;
        });
        
        static::updating(function ($paiement) {
            if ($paiement->isDirty('montant')) {
                $paiement->commission_prescripteur = $paiement->montant * 0.10;
            }
        });
    }
}