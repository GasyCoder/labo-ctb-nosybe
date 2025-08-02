<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Antibiotique extends Model
{
    protected $fillable = [
        'famille_id',
        'designation',
        'commentaire',
        'status',
    ];

    // Relation : antibiotique appartient à une famille de bactéries
    public function famille()
    {
        return $this->belongsTo(BacterieFamille::class, 'famille_id');
    }
}
