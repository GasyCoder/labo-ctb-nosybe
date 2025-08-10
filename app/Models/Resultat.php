<?php

namespace App\Models;

use App\Casts\JsonUnicode;
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
        // pour bacterio :
        'famille_id',
        // 'bacterie_id',
    ];

    /**
     * ✅ CASTS CORRIGÉS avec Unicode
     */
    protected $casts = [
        'validated_at' => 'datetime',
        'resultats' => JsonUnicode::class,  // ← Custom cast avec Unicode
    ];

    // ✅ ALTERNATIVE : Mutators/Accessors manuels
    /**
     * Mutator pour resultats avec Unicode
     */
    public function setResultatsAttribute($value)
    {
        if (is_null($value)) {
            $this->attributes['resultats'] = null;
            return;
        }
        
        if (is_array($value)) {
            $this->attributes['resultats'] = json_encode($value, JSON_UNESCAPED_UNICODE);
        } else {
            $this->attributes['resultats'] = $value;
        }
    }

    /**
     * Accessor pour resultats
     */
    public function getResultatsAttribute($value)
    {
        if (is_null($value)) {
            return null;
        }
        
        if (is_string($value) && $this->isJson($value)) {
            return json_decode($value, true);
        }
        
        return $value;
    }

    /**
     * ✅ MÉTHODE UTILITAIRE : Vérifier si une chaîne est du JSON
     */
    private function isJson($string)
    {
        if (!is_string($string)) {
            return false;
        }
        
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * ✅ MÉTHODE UTILITAIRE : Encoder proprement en JSON avec Unicode
     */
    public static function encodeJsonUnicode($data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }

    /**
     * ✅ MÉTHODE UTILITAIRE : Décoder JSON
     */
    public static function decodeJson($json)
    {
        if (is_null($json) || !is_string($json)) {
            return $json;
        }
        
        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }

    // Relations (restent identiques)
    public function prescription() { return $this->belongsTo(Prescription::class); }
    public function analyse() { return $this->belongsTo(Analyse::class); }
    public function tube() { return $this->belongsTo(Tube::class); }
    public function validatedBy() { return $this->belongsTo(User::class, 'validated_by'); }
    public function famille() { return $this->belongsTo(BacterieFamille::class, 'famille_id'); }
    public function bacterie() { return $this->belongsTo(Bacterie::class, 'bacterie_id'); }

    // Scopes (restent identiques)
    public function scopeStatus($q,$s){ return $q->where('status',$s); }
    public function scopeValides($q){ return $q->where('status','VALIDE'); }
    public function scopeEnCours($q){ return $q->where('status','EN_COURS'); }
    public function scopeEnAttente($q){ return $q->where('status','EN_ATTENTE'); }
    public function scopeTermines($q){ return $q->where('status','TERMINE'); }
    public function scopePathologiques($q){ return $q->where('interpretation','PATHOLOGIQUE'); }
    public function scopeNormaux($q){ return $q->where('interpretation','NORMAL'); }

    // Accessors (restent identiques)
    public function getEstValideAttribute() { return $this->status === 'VALIDE'; }
    public function getEstPathologiqueAttribute() { return $this->interpretation === 'PATHOLOGIQUE'; }

    public function getValeurFormateeAttribute()
    {
        if (!$this->valeur) return null;
        $unite = $this->analyse?->unite ?? '';
        $suffixe = $this->analyse?->suffixe ?? '';
        return trim($this->valeur.' '.$unite.' '.$suffixe);
    }

    public function getStatutCouleurAttribute()
    {
        return match($this->status) {
            'EN_ATTENTE' => 'gray',
            'EN_COURS'   => 'blue',
            'TERMINE'    => 'orange',
            'VALIDE'     => 'green',
            'A_REFAIRE'  => 'red',
            'ARCHIVE'    => 'gray',
            default      => 'gray',
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

    // Métier (restent identiques)
    public function valider($userId = null)
    {
        $this->update([
            'status' => 'VALIDE',
            'validated_by' => $userId ?: Auth::id(),
            'validated_at' => now(),
        ]);
    }

    public function terminer() { $this->update(['status' => 'TERMINE']); }
    public function marquerARefaire() { $this->update(['status' => 'A_REFAIRE']); }

    public function estDansIntervalle()
    {
        if (!$this->valeur || !($this->analyse?->valeur_ref)) return null;
        $valeurRef = $this->analyse->valeur_ref;
        $valeur = (float) $this->valeur;

        if (preg_match('/(\d+\.?\d*)\s*-\s*(\d+\.?\d*)/', $valeurRef, $m)) {
            $min = (float) $m[1]; $max = (float) $m[2];
            return $valeur >= $min && $valeur <= $max;
        }
        if (preg_match('/<\s*(\d+\.?\d*)/', $valeurRef, $m)) { $max = (float) $m[1]; return $valeur < $max; }
        if (preg_match('/>\s*(\d+\.?\d*)/', $valeurRef, $m)) { $min = (float) $m[1]; return $valeur > $min; }

        return null;
    }

    public function interpreterAutomatiquement()
    {
        $ok = $this->estDansIntervalle();
        if ($ok === true)  $this->update(['interpretation' => 'NORMAL']);
        if ($ok === false) $this->update(['interpretation' => 'PATHOLOGIQUE']);
    }

    public static function statistiques()
    {
        return [
            'total'       => static::count(),
            'en_attente'  => static::enAttente()->count(),
            'en_cours'    => static::enCours()->count(),
            'termines'    => static::termines()->count(),
            'valides'     => static::valides()->count(),
            'pathologiques'=> static::pathologiques()->count(),
        ];
    }

    public static function pourPrescription($prescriptionId)
    {
        return static::where('prescription_id', $prescriptionId)
            ->with(['analyse','validatedBy'])
            ->orderBy('created_at')
            ->get();
    }
}
