<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paiement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'prescription_id',
        'montant',
        'commission_prescripteur',
        'payment_method_id',
        'recu_par',
        'status',
        'date_paiement',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'commission_prescripteur' => 'decimal:2',
        'status' => 'boolean',
        'date_paiement' => 'datetime',
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

    // ✅ Accesseurs pour le statut
    public function getStatusLabelAttribute()
    {
        return $this->status ? 'Payé' : 'Non Payé';
    }

    public function getStatusColorAttribute()
    {
        return $this->status ? 'green' : 'red';
    }

    public function getStatusBadgeClassAttribute()
    {
        return $this->status 
            ? 'bg-green-100 text-green-800 border-green-200' 
            : 'bg-red-100 text-red-800 border-red-200';
    }

    // ✅ Méthodes utilitaires pour le statut
    public function marquerCommePayé()
    {
        $this->update([
            'status' => true,
            'date_paiement' => now()
        ]);
    }

    public function marquerCommeNonPayé()
    {
        $this->update([
            'status' => false,
            'date_paiement' => null
        ]);
    }

    public function estPayé()
    {
        return $this->status === true;
    }

    public function estNonPayé()
    {
        return $this->status === false;
    }

    // ✅ Scopes pour filtrer par statut
    public function scopePayés($query)
    {
        return $query->where('status', true);
    }

    public function scopeNonPayés($query)
    {
        return $query->where('status', false);
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

    // ✅ Accesseur pour la date de paiement formatée
    public function getDatePaiementFormateeAttribute()
    {
        return $this->date_paiement ? $this->date_paiement->format('d/m/Y H:i') : null;
    }

    // ✅ Méthode pour définir automatiquement la date de paiement
    public function changerStatutPaiement($nouveauStatut)
    {
        $this->status = $nouveauStatut;
        
        if ($nouveauStatut) {
            // Si on marque comme payé et qu'il n'y a pas encore de date
            if (!$this->date_paiement) {
                $this->date_paiement = now();
            }
        } else {
            // Si on marque comme non payé, on supprime la date
            $this->date_paiement = null;
        }
        
        $this->save();
    }

    // Auto-calcul de la commission avec exclusion BiologieSolidaire
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($paiement) {
            $paiement->commission_prescripteur = static::calculerCommission($paiement);
        });

        static::updating(function ($paiement) {
            // Gérer automatiquement la date de paiement lors des mises à jour
            if ($paiement->isDirty('status')) {
                if ($paiement->status && !$paiement->date_paiement) {
                    // Si on passe à payé et qu'il n'y a pas de date, l'ajouter
                    $paiement->date_paiement = now();
                } elseif (!$paiement->status) {
                    // Si on passe à non payé, supprimer la date
                    $paiement->date_paiement = null;
                }
            }

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