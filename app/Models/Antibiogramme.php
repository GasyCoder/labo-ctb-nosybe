<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Antibiogramme extends Model
{
    use HasFactory;
    // ✅ RETIRER SoftDeletes

    protected $fillable = [
        'prescription_id',
        'analyse_id',
        'bacterie_id',
        'notes',
    ];

    protected $casts = [
        'prescription_id' => 'integer',
        'analyse_id' => 'integer',
        'bacterie_id' => 'integer',
    ];

    // Relations
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

    public function resultatsAntibiotiques()
    {
        return $this->hasMany(ResultatAntibiotique::class);
    }
}