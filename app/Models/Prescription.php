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


    public function tubes()
    {
        return $this->hasMany(Tube::class);
    }

    public function prelevements()
    {
        return $this->belongsToMany(Prelevement::class, 'prelevement_prescription')
                    ->withPivot(['prix_unitaire', 'quantite', 'is_payer', 'type_tube_requis', 'volume_requis_ml', 'tubes_generes', 'tubes_generes_at'])
                    ->withTimestamps();
    }

    // MÉTHODES MÉTIER
    public function genererTousLestubes()
    {
        return Tube::genererPourPrescription($this->id);
    }

    public function getTubesParStatutAttribute()
    {
        return $this->tubes->groupBy('statut')->map->count();
    }

    public function getProgresAnalysesAttribute()
    {
        $total = $this->tubes->count();
        $termines = $this->tubes->where('statut', 'ANALYSE_TERMINEE')->count();
        
        return $total > 0 ? round(($termines / $total) * 100) : 0;
    }
}
