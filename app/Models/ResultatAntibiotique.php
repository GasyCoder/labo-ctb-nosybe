<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ResultatAntibiotique extends Model
{
    use HasFactory;

    protected $table = 'resultat_antibiotiques';

    protected $fillable = [
        'antibiogramme_id',
        'antibiotique_id',
        'interpretation',
        'diametre_mm',
    ];

    protected $casts = [
        'antibiogramme_id' => 'integer',
        'antibiotique_id' => 'integer',
        'diametre_mm' => 'decimal:2',
    ];

    // CONSTANTES - selon votre enum
    const INTERPRETATION_SENSIBLE = 'S';
    const INTERPRETATION_INTERMEDIAIRE = 'I';
    const INTERPRETATION_RESISTANT = 'R';

    // RELATIONS
    public function antibiogramme()
    {
        return $this->belongsTo(Antibiogramme::class, 'antibiogramme_id');
    }

    public function antibiotique()
    {
        return $this->belongsTo(Antibiotique::class, 'antibiotique_id');
    }

    // SCOPES
    public function scopeSensibles($query)
    {
        return $query->where('interpretation', self::INTERPRETATION_SENSIBLE);
    }

    public function scopeResistants($query)
    {
        return $query->where('interpretation', self::INTERPRETATION_RESISTANT);
    }

    public function scopeIntermediaires($query)
    {
        return $query->where('interpretation', self::INTERPRETATION_INTERMEDIAIRE);
    }

    public function scopeParInterpretation($query, $interpretation)
    {
        return $query->where('interpretation', $interpretation);
    }

    // ACCESSEURS
    public function getEstSensibleAttribute()
    {
        return $this->interpretation === self::INTERPRETATION_SENSIBLE;
    }

    public function getEstResistantAttribute()
    {
        return $this->interpretation === self::INTERPRETATION_RESISTANT;
    }

    public function getEstIntermediaiireAttribute()
    {
        return $this->interpretation === self::INTERPRETATION_INTERMEDIAIRE;
    }

    public function getResultatCompletAttribute()
    {
        $result = $this->antibiotique->designation ?? 'Antibiotique inconnu';
        
        if ($this->diametre_mm) {
            $result .= ' (' . $this->diametre_mm . 'mm)';
        }
        
        return $result;
    }

    public function getInterpretationLabelAttribute()
    {
        switch ($this->interpretation) {
            case self::INTERPRETATION_SENSIBLE:
                return 'Sensible';
            case self::INTERPRETATION_RESISTANT:
                return 'Résistant';
            case self::INTERPRETATION_INTERMEDIAIRE:
                return 'Intermédiaire';
            default:
                return 'Inconnu';
        }
    }

    public function getInterpretationColorAttribute()
    {
        switch ($this->interpretation) {
            case self::INTERPRETATION_SENSIBLE:
                return '#28a745'; // Vert
            case self::INTERPRETATION_RESISTANT:
                return '#dc3545'; // Rouge
            case self::INTERPRETATION_INTERMEDIAIRE:
                return '#ffc107'; // Jaune
            default:
                return '#6c757d'; // Gris
        }
    }

    // MÉTHODES
    public function updateInterpretation($nouvelleInterpretation)
    {
        $interpretationsValides = [
            self::INTERPRETATION_SENSIBLE,
            self::INTERPRETATION_RESISTANT,
            self::INTERPRETATION_INTERMEDIAIRE
        ];

        if (!in_array($nouvelleInterpretation, $interpretationsValides)) {
            throw new \InvalidArgumentException("Interprétation invalide: {$nouvelleInterpretation}");
        }

        $this->interpretation = $nouvelleInterpretation;
        $this->save();
    }

    public function marquerSensible()
    {
        $this->updateInterpretation(self::INTERPRETATION_SENSIBLE);
    }

    public function marquerResistant()
    {
        $this->updateInterpretation(self::INTERPRETATION_RESISTANT);
    }

    public function marquerIntermediaire()
    {
        $this->updateInterpretation(self::INTERPRETATION_INTERMEDIAIRE);
    }
}