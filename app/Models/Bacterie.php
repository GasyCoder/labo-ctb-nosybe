<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bacterie extends Model
{
    protected $fillable = [
        'famille_id',
        'designation',
        'status',
    ];

    // Relation : bactérie appartient à une famille
    public function famille()
    {
        return $this->belongsTo(BacterieFamille::class, 'famille_id');
    }

    // Si tu veux relier chaque bactérie à des antibiotiques spécifiques (relation avancée)
    public function antibiotiques()
    {
        return $this->belongsToMany(Antibiotique::class, 'bacterie_antibiotique');
    }
}
