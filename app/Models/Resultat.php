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
        'famille_id',
        'bacterie_id',
    ];

    /**
     * CASTS
     */
    protected $casts = [
        'validated_at' => 'datetime',
    ];

    // ============================================
    // MUTATORS/ACCESSORS JSON
    // ============================================

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

    // ============================================
    // RELATIONS
    // ============================================

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

    public function famille() 
    { 
        return $this->belongsTo(BacterieFamille::class, 'famille_id'); 
    }

    public function bacterie() 
    { 
        return $this->belongsTo(Bacterie::class, 'bacterie_id'); 
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopeStatus($q, $s) 
    { 
        return $q->where('status', $s); 
    }

    public function scopeValides($q) 
    { 
        return $q->where('status', 'VALIDE'); 
    }

    public function scopeEnCours($q) 
    { 
        return $q->where('status', 'EN_COURS'); 
    }

    public function scopeEnAttente($q) 
    { 
        return $q->where('status', 'EN_ATTENTE'); 
    }

    public function scopeTermines($q) 
    { 
        return $q->where('status', 'TERMINE'); 
    }

    public function scopePathologiques($q) 
    { 
        return $q->where('interpretation', 'PATHOLOGIQUE'); 
    }

    public function scopeNormaux($q) 
    { 
        return $q->where('interpretation', 'NORMAL'); 
    }

    // ============================================
    // ACCESSORS EXISTANTS
    // ============================================

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
        $unite = $this->analyse?->unite ?? '';
        $suffixe = $this->analyse?->suffixe ?? '';
        return trim($this->valeur . ' ' . $unite . ' ' . $suffixe);
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

    // ============================================
    // NOUVEAUX ACCESSORS POUR PDF
    // ============================================

    /**
     * Obtenir la valeur formatée pour l'affichage PDF
     */
    public function getValeurPdfAttribute()
    {
        if (!$this->valeur && !$this->resultats) {
            return null;
        }

        // Si c'est du JSON, le décoder
        if ($this->isJson($this->valeur)) {
            $decoded = json_decode($this->valeur, true);
            
            // Cas spécial pour les leucocytes
            if (isset($decoded['valeur'])) {
                return $decoded['valeur'] . ' /mm³';
            }
            
            return $decoded;
        }

        // Sinon retourner la valeur formatée normale
        return $this->valeur_formatee;
    }

    /**
     * Obtenir les résultats formatés pour l'affichage PDF
     */
    public function getResultatsPdfAttribute()
    {
        if (!$this->resultats) {
            return null;
        }

        // Les resultats sont automatiquement décodés par l'accessor
        if (is_array($this->resultats)) {
            return $this->resultats;
        }

        return $this->resultats;
    }

    /**
     * Obtenir les données de germe formatées
     */
    public function getGermeDataAttribute()
    {
        if (!$this->isGermeType()) {
            return null;
        }

        $resultats = $this->resultats_pdf;
        if (!is_array($resultats)) {
            return null;
        }

        return [
            'options_speciales' => $resultats['option_speciale'] ?? [],
            'bacteries' => $resultats['bacteries'] ?? [],
            'autre_valeur' => $resultats['autre_valeur'] ?? null
        ];
    }

    /**
     * Obtenir les données de leucocytes formatées
     */
    public function getLeucocytesDataAttribute()
    {
        if (!$this->isLeucocytesType()) {
            return null;
        }

        if ($this->isJson($this->valeur)) {
            return json_decode($this->valeur, true);
        }

        return null;
    }

    /**
     * Obtenir la valeur d'affichage complète pour PDF
     */
    public function getDisplayValuePdfAttribute()
    {
        $displayValue = '';

        if ($this->isGermeType()) {
            $germeData = $this->germe_data;
            if ($germeData && isset($germeData['options_speciales'])) {
                $displayValue = implode(', ', array_map('ucfirst', $germeData['options_speciales']));
            }
            // Afficher les bactéries
            if ($germeData && isset($germeData['bacteries'])) {
                foreach ($germeData['bacteries'] as $bacteriaName => $bacteriaData) {
                    $displayValue = '<i>' . $bacteriaName . '</i>';
                    break; // Premier seulement
                }
            }
        } elseif ($this->isLeucocytesType()) {
            $leucoData = $this->leucocytes_data;
            if ($leucoData && isset($leucoData['valeur'])) {
                $displayValue = $leucoData['valeur'] . ' /mm³';
            }
        } else {
            $displayValue = $this->valeur_pdf ?: $this->resultats_pdf;
            
            // Cas spéciaux selon le type d'analyse
            if ($this->analyse && $this->analyse->type) {
                switch($this->analyse->type->name) {
                    case 'SELECT_MULTIPLE':
                        $resultatsArray = $this->resultats_pdf;
                        if (is_array($resultatsArray)) {
                            $displayValue = implode(', ', $resultatsArray);
                        }
                        break;
                        
                    case 'NEGATIF_POSITIF_3':
                        if ($this->resultats === 'Positif' && $this->valeur) {
                            $displayValue = $this->resultats;
                            $values = is_array($this->valeur) ? 
                                implode(', ', $this->valeur) : 
                                $this->valeur;
                            $displayValue .= ' (' . $values . ')';
                        } else {
                            $displayValue = $this->resultats ?: $this->valeur;
                        }
                        break;
                        
                    case 'FV':
                        if ($this->resultats) {
                            $displayValue = $this->resultats;
                            if ($this->valeur && in_array($this->resultats, [
                                'Flore vaginale équilibrée',
                                'Flore vaginale intermédiaire', 
                                'Flore vaginale déséquilibrée'
                            ])) {
                                $displayValue .= ' (Score de Nugent: ' . $this->valeur . ')';
                            }
                        }
                        break;
                }
            }
        }

        // Formater en gras si pathologique
        if ($this->est_pathologique && $displayValue) {
            $displayValue = '<strong>' . $displayValue . '</strong>';
        }

        return $displayValue;
    }

    // ============================================
    // MÉTHODES DE VÉRIFICATION TYPE
    // ============================================

    /**
     * Vérifier si c'est un résultat de type germe
     */
    public function isGermeType()
    {
        return $this->analyse && $this->analyse->type && $this->analyse->type->name === 'GERME';
    }

    /**
     * Vérifier si c'est un résultat de type leucocytes
     */
    public function isLeucocytesType()
    {
        return $this->analyse && $this->analyse->type && $this->analyse->type->name === 'LEUCOCYTES';
    }

    /**
     * Vérifier si c'est un résultat de culture
     */
    public function isCultureType()
    {
        return $this->analyse && $this->analyse->type && $this->analyse->type->name === 'CULTURE';
    }

    /**
     * Vérifier si c'est un résultat de flore vaginale
     */
    public function isFloreVaginaleType()
    {
        return $this->analyse && $this->analyse->type && $this->analyse->type->name === 'FV';
    }

    // ============================================
    // MÉTHODES UTILITAIRES JSON
    // ============================================

    /**
     * Vérifier si une chaîne est du JSON
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
     * Encoder proprement en JSON avec Unicode
     */
    public static function encodeJsonUnicode($data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }

    /**
     * Décoder JSON
     */
    public static function decodeJson($json)
    {
        if (is_null($json) || !is_string($json)) {
            return $json;
        }
        
        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }

    // ============================================
    // MÉTHODES MÉTIER
    // ============================================

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
        if (!$this->valeur || !($this->analyse?->valeur_ref)) return null;
        $valeurRef = $this->analyse->valeur_ref;
        $valeur = (float) $this->valeur;

        if (preg_match('/(\d+\.?\d*)\s*-\s*(\d+\.?\d*)/', $valeurRef, $m)) {
            $min = (float) $m[1]; 
            $max = (float) $m[2];
            return $valeur >= $min && $valeur <= $max;
        }
        if (preg_match('/<\s*(\d+\.?\d*)/', $valeurRef, $m)) { 
            $max = (float) $m[1]; 
            return $valeur < $max; 
        }
        if (preg_match('/>\s*(\d+\.?\d*)/', $valeurRef, $m)) { 
            $min = (float) $m[1]; 
            return $valeur > $min; 
        }

        return null;
    }

    public function interpreterAutomatiquement()
    {
        $ok = $this->estDansIntervalle();
        if ($ok === true)  $this->update(['interpretation' => 'NORMAL']);
        if ($ok === false) $this->update(['interpretation' => 'PATHOLOGIQUE']);
    }

    // ============================================
    // MÉTHODES STATIQUES
    // ============================================

    public static function statistiques()
    {
        return [
            'total'         => static::count(),
            'en_attente'    => static::enAttente()->count(),
            'en_cours'      => static::enCours()->count(),
            'termines'      => static::termines()->count(),
            'valides'       => static::valides()->count(),
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