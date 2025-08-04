<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Resultat extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'prescription_id',
        'analyse_id',
        'resultats',
        'valeur',
        'tube_id',  
        'interpretation',
        'conclusion',
        'status',
        'validated_by',
        'validated_at',
    ];

    // CASTS pour optimiser les types
    protected $casts = [
        'validated_at' => 'datetime',
        'resultats' => 'array', // Si tu stockes du JSON
    ];

    // RELATIONS
    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function analyse()
    {
        return $this->belongsTo(Analyse::class);
    }

    public function tube()
    {
        return $this->belongsTo(Tube::class);
    }

    public function validatedBy()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    // SCOPES pour les statuts
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeValides($query)
    {
        return $query->where('status', 'VALIDE');
    }

    public function scopeEnCours($query)
    {
        return $query->where('status', 'EN_COURS');
    }

    public function scopeEnAttente($query)
    {
        return $query->where('status', 'EN_ATTENTE');
    }

    public function scopeTermines($query)
    {
        return $query->where('status', 'TERMINE');
    }

    public function scopePathologiques($query)
    {
        return $query->where('interpretation', 'PATHOLOGIQUE');
    }

    public function scopeNormaux($query)
    {
        return $query->where('interpretation', 'NORMAL');
    }

    // ACCESSEURS et MUTATEURS
    public function getEstValideAttribute()
    {
        return $this->status === 'VALIDE';
    }

    public function getEstPathologiqueAttribute()
    {
        return $this->interpretation === 'PATHOLOGIQUE';
    }

    public function getValeurFormateeAttribute()
    {
        if (!$this->valeur) return null;
        
        $unite = $this->analyse->unite ?? '';
        $suffixe = $this->analyse->suffixe ?? '';
        
        return trim($this->valeur . ' ' . $unite . ' ' . $suffixe);
    }

    public function getStatutCouleurAttribute()
    {
        return match($this->status) {
            'EN_ATTENTE' => 'gray',
            'EN_COURS' => 'blue',
            'TERMINE' => 'orange',
            'VALIDE' => 'green',
            'A_REFAIRE' => 'red',
            'ARCHIVE' => 'gray',
            default => 'gray'
        };
    }

    public function getInterpretationCouleurAttribute()
    {
        return match($this->interpretation) {
            'NORMAL' => 'green',
            'PATHOLOGIQUE' => 'red',
            default => 'gray'
        };
    }

    // MÉTHODES MÉTIER
    public function valider($userId = null)
    {
        $this->update([
            'status' => 'VALIDE',
            'validated_by' => $userId ?: Auth::id(),
            'validated_at' => now(),
        ]);
    }

    public function terminer()
    {
        $this->update(['status' => 'TERMINE']);
    }

    public function marquerARefaire()
    {
        $this->update(['status' => 'A_REFAIRE']);
    }

    public function estDansIntervalle()
    {
        if (!$this->valeur || !$this->analyse->valeur_ref) {
            return null; // Impossible de déterminer
        }

        // Logique pour vérifier si la valeur est dans l'intervalle de référence
        $valeurRef = $this->analyse->valeur_ref;
        $valeur = (float) $this->valeur;

        // Exemple: "120 - 160" ou "<5.18" ou "H: 62-123 F: 53-97"
        if (preg_match('/(\d+\.?\d*)\s*-\s*(\d+\.?\d*)/', $valeurRef, $matches)) {
            $min = (float) $matches[1];
            $max = (float) $matches[2];
            return $valeur >= $min && $valeur <= $max;
        }

        if (preg_match('/<\s*(\d+\.?\d*)/', $valeurRef, $matches)) {
            $max = (float) $matches[1];
            return $valeur < $max;
        }

        if (preg_match('/>\s*(\d+\.?\d*)/', $valeurRef, $matches)) {
            $min = (float) $matches[1];
            return $valeur > $min;
        }

        return null; // Format non reconnu
    }

    public function interpreterAutomatiquement()
    {
        $dansIntervalle = $this->estDansIntervalle();
        
        if ($dansIntervalle === true) {
            $this->update(['interpretation' => 'NORMAL']);
        } elseif ($dansIntervalle === false) {
            $this->update(['interpretation' => 'PATHOLOGIQUE']);
        }
        // Si null, on ne change pas l'interprétation
    }

    // MÉTHODES STATIQUES utiles
    public static function statistiques()
    {
        return [
            'total' => static::count(),
            'en_attente' => static::enAttente()->count(),
            'en_cours' => static::enCours()->count(),
            'termines' => static::termines()->count(),
            'valides' => static::valides()->count(),
            'pathologiques' => static::pathologiques()->count(),
        ];
    }

    public static function pourPrescription($prescriptionId)
    {
        return static::where('prescription_id', $prescriptionId)
                    ->with(['analyse', 'validatedBy'])
                    ->orderBy('created_at')
                    ->get();
    }
}