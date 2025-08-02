<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prelevement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nom',
        'description',
        'prix',
        'quantite',
        'is_active',
    ];

    // Relation Many-to-Many avec Prescription via la table pivot
    public function prescriptions()
    {
        return $this->belongsToMany(Prescription::class, 'prelevement_prescription')
            ->withPivot('prix_unitaire', 'quantite', 'is_payer')
            ->withTimestamps();
    }

    
}
