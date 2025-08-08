<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $table = 'payment_methods';

    protected $fillable = [
        'code',
        'label',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Scope pour récupérer uniquement les méthodes actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour ordonner par ordre d'affichage
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

    /**
     * Scope pour récupérer les méthodes actives et ordonnées
     */
    public function scopeActiveOrdered($query)
    {
        return $query->active()->ordered();
    }

    /**
     * Mutateur pour forcer le code en majuscules
     */
    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper($value);
    }

    /**
     * Relation avec les paiements (si vous avez une table paiements qui référence les méthodes)
     * Décommentez si nécessaire
     */
    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'payment_methode_id');
    }
}