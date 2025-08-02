<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescripteur extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom',
        'is_active',
    ];

    // Optionnel : Scope pour récupérer les prescripteurs actifs
    public function scopeActifs($query)
    {
        return $query->where('is_active', true);
    }

    // Relation avec prescriptions (si tu veux retrouver toutes les prescriptions faites par ce prescripteur)
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
}