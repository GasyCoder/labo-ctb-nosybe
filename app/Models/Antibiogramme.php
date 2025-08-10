<?php
// app/Models/Antibiogramme.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Antibiogramme extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'prescription_id',
        'analyse_id',
        'bacterie_id',
        'notes',
    ];

    // Relations avec vos modèles existants
    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function analyse()
    {
        return $this->belongsTo(Analyse::class);
    }

    public function bacterie()
    {
        return $this->belongsTo(Bacterie::class);
    }

    public function resultatAntibiotiques()
    {
        return $this->hasMany(ResultatAntibiotique::class);
    }
}