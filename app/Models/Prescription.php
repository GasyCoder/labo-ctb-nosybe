<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'secretaire_id',
        'patient_id',
        'prescripteur_id',
        'patient_type',
        'age',
        'unite_age',
        'poids',
        'renseignement_clinique',
        'remise',
        'is_archive',
        'status',
    ];

    public function secretaire()
    {
        return $this->belongsTo(User::class, 'secretaire_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function prescripteur()
    {
        return $this->belongsTo(Prescripteur::class, 'prescripteur_id');
    }

    // La relation Many to Many avec Analyse
    public function analyses()
    {
        return $this->belongsToMany(Analyse::class, 'prescription_analyse')
            ->withTimestamps();
    }
}
