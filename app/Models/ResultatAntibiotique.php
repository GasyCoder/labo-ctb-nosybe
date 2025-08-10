<?php
// app/Models/ResultatAntibiotique.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultatAntibiotique extends Model
{
    protected $fillable = [
        'antibiogramme_id',
        'antibiotique_id',
        'interpretation',
        'diametre_mm',
    ];

    protected $casts = [
        'diametre_mm' => 'decimal:2',
    ];

    // Relations avec vos modèles existants
    public function antibiogramme()
    {
        return $this->belongsTo(Antibiogramme::class);
    }

    public function antibiotique()
    {
        return $this->belongsTo(Antibiotique::class);
    }
}