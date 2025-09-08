<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Antibiogramme extends Model
{
    use HasFactory;

    protected $table = 'antibiogrammes';

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

    // RELATIONS
    public function prescription()
    {
        return $this->belongsTo(Prescription::class, 'prescription_id');
    }

    public function analyse()
    {
        return $this->belongsTo(Analyse::class, 'analyse_id');
    }

    public function bacterie()
    {
        return $this->belongsTo(Bacterie::class, 'bacterie_id');
    }

    public function resultatsAntibiotiques()
    {
        return $this->hasMany(ResultatAntibiotique::class, 'antibiogramme_id');
    }

    // ✅ CORRECTION: Utiliser des méthodes au lieu d'accesseurs dynamiques
    public function getAntibiotiquesSenesibles()
    {
        return $this->resultatsAntibiotiques()
                   ->where('interpretation', 'S')
                   ->with('antibiotique')
                   ->get();
    }

    public function getAntibiotiquesResistants()
    {
        return $this->resultatsAntibiotiques()
                   ->where('interpretation', 'R')
                   ->with('antibiotique')
                   ->get();
    }

    public function getAntibiotiquesIntermediaires()
    {
        return $this->resultatsAntibiotiques()
                   ->where('interpretation', 'I')
                   ->with('antibiotique')
                   ->get();
    }

    // ✅ MÉTHODE PRINCIPALE pour organiser tous les antibiotiques
    public function getAntibiotiquesOrganises()
    {
        $antibiotiques = $this->resultatsAntibiotiques()
                             ->with('antibiotique')
                             ->get()
                             ->groupBy('interpretation');

        return (object) [
            'sensibles' => $antibiotiques->get('S', collect()),
            'resistants' => $antibiotiques->get('R', collect()),
            'intermediaires' => $antibiotiques->get('I', collect()),
        ];
    }

    // SCOPES
    public function scopeParPrescription($query, $prescriptionId)
    {
        return $query->where('prescription_id', $prescriptionId);
    }

    public function scopeParAnalyse($query, $analyseId)
    {
        return $query->where('analyse_id', $analyseId);
    }

    public function scopeParBacterie($query, $bacterieId)
    {
        return $query->where('bacterie_id', $bacterieId);
    }

    public function scopeAvecRelations($query)
    {
        return $query->with([
            'bacterie',
            'prescription',
            'analyse',
            'resultatsAntibiotiques.antibiotique'
        ]);
    }

    // ACCESSEURS SÉCURISÉS
    public function getBacterieNomAttribute()
    {
        return $this->bacterie ? $this->bacterie->designation : 'Bactérie inconnue';
    }

    public function getAnalyseNomAttribute()
    {
        return $this->analyse ? $this->analyse->designation : 'Analyse inconnue';
    }

    public function getNombreAntibiotiquesAttribute()
    {
        return $this->resultatsAntibiotiques()->count();
    }

    public function getNombreSensiblesAttribute()
    {
        return $this->resultatsAntibiotiques()->where('interpretation', 'S')->count();
    }

    public function getNombreResistantsAttribute()
    {
        return $this->resultatsAntibiotiques()->where('interpretation', 'R')->count();
    }

    public function getNombreIntermediairesAttribute()
    {
        return $this->resultatsAntibiotiques()->where('interpretation', 'I')->count();
    }

    // ✅ MÉTHODE POUR PDF - évite les erreurs Intelephense
    public function getPourPdf()
    {
        $organises = $this->getAntibiotiquesOrganises();
        
        return [
            'id' => $this->id,
            'bacterie' => $this->bacterie,
            'analyse' => $this->analyse,
            'notes' => $this->notes,
            'antibiotiques_sensibles' => $organises->sensibles,
            'antibiotiques_resistants' => $organises->resistants,
            'antibiotiques_intermediaires' => $organises->intermediaires,
            'statistiques' => [
                'total' => $this->nombre_antibiotiques,
                'sensibles' => $this->nombre_sensibles,
                'resistants' => $this->nombre_resistants,
                'intermediaires' => $this->nombre_intermediaires,
            ]
        ];
    }

    // MÉTHODES MÉTIER
    public function ajouterAntibiotique($antibiotiqueId, $interpretation, $diametreMm = null)
    {
        $interpretationsValides = ['S', 'I', 'R'];
        
        if (!in_array($interpretation, $interpretationsValides)) {
            throw new \InvalidArgumentException("Interprétation invalide: {$interpretation}");
        }

        return $this->resultatsAntibiotiques()->create([
            'antibiotique_id' => $antibiotiqueId,
            'interpretation' => $interpretation,
            'diametre_mm' => $diametreMm,
        ]);
    }

    public function supprimerAntibiotique($antibiotiqueId)
    {
        return $this->resultatsAntibiotiques()
                   ->where('antibiotique_id', $antibiotiqueId)
                   ->delete();
    }

    public function modifierInterpretation($antibiotiqueId, $nouvelleInterpretation)
    {
        $interpretationsValides = ['S', 'I', 'R'];
        
        if (!in_array($nouvelleInterpretation, $interpretationsValides)) {
            throw new \InvalidArgumentException("Interprétation invalide: {$nouvelleInterpretation}");
        }

        return $this->resultatsAntibiotiques()
                   ->where('antibiotique_id', $antibiotiqueId)
                   ->update(['interpretation' => $nouvelleInterpretation]);
    }

    public function hasAntibiotique($antibiotiqueId)
    {
        return $this->resultatsAntibiotiques()
                   ->where('antibiotique_id', $antibiotiqueId)
                   ->exists();
    }

    public function getInterpretationAntibiotique($antibiotiqueId)
    {
        $resultat = $this->resultatsAntibiotiques()
                        ->where('antibiotique_id', $antibiotiqueId)
                        ->first();
        
        return $resultat ? $resultat->interpretation : null;
    }
}