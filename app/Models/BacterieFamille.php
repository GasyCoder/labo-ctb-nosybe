<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BacterieFamille extends Model
{
    protected $fillable = [
        'designation',
        'status',
    ];

    // Scope pour les familles actives
    public function scopeActives($query)
    {
        return $query->where('status', true);
    }

    // Relation : une famille a plusieurs bactéries
    public function bacteries()
    {
        return $this->hasMany(Bacterie::class, 'famille_id');
    }

    // Relation : une famille a plusieurs antibiotiques (si tu utilises la même logique que plus haut)
    public function antibiotiques()
    {
        return $this->hasMany(Antibiotique::class, 'famille_id');
    }
}



