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

        'anteriorite',
        'anteriorite_date',
        'anteriorite_prescription_id',
    ];

    /**
     * CASTS
     */
    protected $casts = [
        'validated_at' => 'datetime',
        'anteriorite_date' => 'date', // NOUVEAU
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
    if (!$this->valeur && !$this->resultats) {
        return '';
    }

    $displayValue = '';

    // Gestion des types GERME et CULTURE
    if ($this->isGermeType() || $this->isCultureType()) {
        $selectedOptions = $this->resultats_pdf ?? $this->resultats;
        $autreValeur = $this->valeur;

        if (is_array($selectedOptions)) {
            $formattedOptions = array_map(function($option) use ($autreValeur) {
                if ($option === 'Autre' && $autreValeur) {
                    return '<i>' . e($autreValeur) . '</i>';
                }
                return ucfirst(str_replace('-', ' ', $option));
            }, $selectedOptions);
            $displayValue = implode(', ', $formattedOptions);
        } elseif ($selectedOptions) {
            $displayValue = e($selectedOptions);
        } elseif ($autreValeur) {
            $displayValue = '<i>' . e($autreValeur) . '</i>';
        }
    }
    // Gestion des LEUCOCYTES
    elseif ($this->isLeucocytesType()) {
        $leucoData = $this->leucocytes_data;
        if ($leucoData && isset($leucoData['valeur'])) {
            $displayValue = $leucoData['valeur'] . ' /mm³';
        }
    }
    // Gestion des autres types
    else {
        $analyseType = $this->analyse->type->name ?? '';

        switch ($analyseType) {
            case 'INPUT':
            case 'DOSAGE':
            case 'COMPTAGE':
            case 'INPUT_SUFFIXE':
                $displayValue = $this->valeur ?? '';
                break;

            case 'SELECT':
            case 'TEST':
                $displayValue = $this->resultats ?? $this->valeur ?? '';
                break;

            case 'SELECT_MULTIPLE':
                $resultatsArray = $this->resultats_pdf ?? $this->resultats;
                $displayValue = is_array($resultatsArray) ? implode(', ', $resultatsArray) : ($resultatsArray ?? '');
                break;

            case 'NEGATIF_POSITIF_1':
                $displayValue = $this->valeur ?? '';
                break;

            case 'NEGATIF_POSITIF_2':
                $displayValue = $this->valeur ?? '';
                if ($this->valeur === 'POSITIF' && $this->resultats) {
                    $displayValue .= ' (' . (is_array($this->resultats) ? implode(', ', $this->resultats) : $this->resultats) . ')';
                }
                break;

            case 'NEGATIF_POSITIF_3':
                $displayValue = $this->valeur ?? '';
                if ($this->resultats) {
                    $resultatsStr = is_array($this->resultats) ? implode(', ', $this->resultats) : $this->resultats;
                    $displayValue .= ' (' . $resultatsStr . ')';
                }
                break;

            case 'ABSENCE_PRESENCE_2':
                $displayValue = $this->valeur ?? '';
                if ($this->resultats) {
                    $displayValue .= ' (' . (is_array($this->resultats) ? implode(', ', $this->resultats) : $this->resultats) . ')';
                }
                break;

            case 'FV':
                $displayValue = $this->resultats ?? '';
                if ($this->valeur && in_array($this->resultats, [
                    'Flore vaginale équilibrée',
                    'Flore vaginale intermédiaire',
                    'Flore vaginale déséquilibrée'
                ])) {
                    $displayValue .= ' (Score de Nugent: ' . $this->valeur . ')';
                } elseif ($this->resultats === 'Autre' && $this->valeur) {
                    $displayValue = $this->valeur;
                }
                break;

            case 'LABEL':
                $displayValue = '';
                break;

            default:
                $displayValue = is_array($this->resultats) ? implode(', ', $this->resultats) : ($this->resultats ?? $this->valeur ?? '');
                if ($this->resultats === 'Autre' && $this->valeur) {
                    $displayValue = $this->valeur;
                }
                break;
        }

        // Ajout des unités et suffixes
        if ($displayValue && $this->analyse && $this->analyse->unite && !str_contains($displayValue, $this->analyse->unite)) {
            $displayValue .= ' ' . $this->analyse->unite;
        }
        if ($displayValue && $this->analyse && $this->analyse->suffixe && !str_contains($displayValue, $this->analyse->suffixe)) {
            $displayValue .= ' ' . $this->analyse->suffixe;
        }
    }

    // Formatage pour les résultats pathologiques
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



    // NOUVELLES RELATIONS
    public function anteriorite_prescription()
    {
        return $this->belongsTo(Prescription::class, 'anteriorite_prescription_id');
    }

    // NOUVEAUX ACCESSORS
    public function getAAnterioriteAttribute(): bool
    {
        return !empty($this->anteriorite);
    }

    public function getAnterioriteFormatteeAttribute(): ?string
    {
        if (!$this->anteriorite) {
            return null;
        }
        
        $texte = $this->anteriorite;
        if ($this->anteriorite_date) {
            $texte .= ' (' . $this->anteriorite_date->format('d/m/Y') . ')';
        }
        
        return $texte;
    }

    public function getAnterioriteComparaisonAttribute(): ?array
    {
        if (!$this->anteriorite || !$this->valeur) {
            return null;
        }
        
        // Tentative de comparaison numérique
        $valeurActuelle = $this->extraireValeurNumerique($this->valeur);
        $valeurAncienne = $this->extraireValeurNumerique($this->anteriorite);
        
        if ($valeurActuelle !== null && $valeurAncienne !== null) {
            $difference = $valeurActuelle - $valeurAncienne;
            $pourcentage = $valeurAncienne != 0 ? ($difference / $valeurAncienne) * 100 : 0;
            
            return [
                'valeur_actuelle' => $valeurActuelle,
                'valeur_ancienne' => $valeurAncienne,
                'difference' => $difference,
                'pourcentage' => round($pourcentage, 1),
                'tendance' => $difference > 0 ? 'hausse' : ($difference < 0 ? 'baisse' : 'stable')
            ];
        }
        
        return null;
    }

    /**
     * Extraire une valeur numérique d'une chaîne
     */
    private function extraireValeurNumerique(?string $valeur): ?float
    {
        if (!$valeur) return null;
        
        // Nettoyer la chaîne et extraire le premier nombre
        $pattern = '/(\d+(?:[.,]\d+)?)/';
        if (preg_match($pattern, str_replace(',', '.', $valeur), $matches)) {
            return (float) $matches[1];
        }
        
        return null;
    }

    // NOUVELLE MÉTHODE POUR FORMATER L'AFFICHAGE AVEC ANTÉRIORITÉ
    public function getDisplayValueWithAnterioriteAttribute(): string
    {
        $display = $this->getDisplayValuePdfAttribute();
        
        if ($this->a_anteriorite) {
            $comparaison = $this->anteriorite_comparaison;
            if ($comparaison) {
                $icone = match($comparaison['tendance']) {
                    'hausse' => '↗',
                    'baisse' => '↘', 
                    default => '→'
                };
                $display .= " {$icone}";
            }
        }
        
        return $display;
    }
}