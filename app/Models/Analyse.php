<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Analyse extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'level',
        'parent_id',
        'designation',
        'description',
        'prix',
        'is_bold',
        'examen_id',
        'type_id',
        'valeur_ref', // valeur de référence temporaire entente de mise à jour

        // nouveaux champs pour valeurs de référence spécifiques
        'valeur_ref_homme',
        'valeur_ref_femme',
        'valeur_ref_enfant_garcon',
        'valeur_ref_enfant_fille',
        'unite',
        'suffixe',
        'valeurs_predefinies',
        'ordre',
        'status',
    ];

    protected $casts = [
        'prix' => 'decimal:2',
        'is_bold' => 'boolean',
        'status' => 'boolean',
        'valeurs_predefinies' => 'array',
        'ordre' => 'integer',
    ];

    // Relations hiérarchie
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function enfants()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('ordre')->orderBy('id');
    }

    // Récursion profonde
    public function enfantsRecursive()
    {
        return $this->enfants()->with(['type','examen','enfantsRecursive']);
    }

    // Relations annexes
    public function examen()
    {
        return $this->belongsTo(Examen::class, 'examen_id');
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function resultats()
    {
        return $this->hasMany(Resultat::class);
    }

    // Scopes utiles
    public function scopeActives($q) { return $q->where('status', true); }
    public function scopeParents($q) { return $q->where('level', 'PARENT'); }
    public function scopeNormales($q){ return $q->where('level', 'NORMAL'); }
    public function scopeEnfants($q) { return $q->where('level', 'CHILD'); }
    public function scopeRacines($q) { return $q->whereNull('parent_id')->orWhere('level','PARENT'); }

    // Accessors
    public function getValeurCompleteAttribute()
    {
        if ($this->valeur_ref && $this->unite) {
            return $this->valeur_ref.' '.$this->unite;
        }
        return $this->valeur_ref;
    }

    public function getValeurHommeCompleteAttribute()
    {
        if ($this->valeur_ref_homme && $this->unite) {
            return $this->valeur_ref_homme.' '.$this->unite;
        }
        return $this->valeur_ref_homme;
    }

    public function getValeurFemmeCompleteAttribute()
    {
        if ($this->valeur_ref_femme && $this->unite) {
            return $this->valeur_ref_femme.' '.$this->unite;
        }
        return $this->valeur_ref_femme;
    }

    public function getValeurEnfantGarconCompleteAttribute()
    {
        if ($this->valeur_ref_enfant_garcon && $this->unite) {
            return $this->valeur_ref_enfant_garcon.' '.$this->unite;
        }
        return $this->valeur_ref_enfant_garcon;
    }

    public function getValeurEnfantFilleCompleteAttribute()
    {
        if ($this->valeur_ref_enfant_fille && $this->unite) {
            return $this->valeur_ref_enfant_fille.' '.$this->unite;
        }
        return $this->valeur_ref_enfant_fille;
    }

    public function getEstParentAttribute()
    {
        return $this->level === 'PARENT';
    }

    public function getADesEnfantsAttribute()
    {
        return $this->enfants()->exists();
    }

    // Accesseur pour formatted_results
    public function getFormattedResultsAttribute()
    {
        if (!$this->valeurs_predefinies || !is_array($this->valeurs_predefinies)) {
            return [];
        }
        return $this->valeurs_predefinies;
    }

    // Accesseur pour result_disponible (compatibilité ancien code)
    public function getResultDisponibleAttribute()
    {
        return [
            'val_ref_homme' => $this->valeur_ref_homme,
            'val_ref_femme' => $this->valeur_ref_femme,
            'unite' => $this->unite,
            'suffixe' => $this->suffixe,
        ];
    }

    // Méthodes utilitaires
    public function getPrixFormate() { 
        return number_format($this->prix, 0, ',', ' ').' Ar'; 
    }

    public function getPrixTotalAttribute()
    {
        if ($this->level === 'PARENT' && $this->enfants->count() > 0) {
            return $this->enfants->sum('prix');
        }
        return $this->prix;
    }

    public function descendantsIds(): array
    {
        $ids = [];
        $stack = [$this->loadMissing('enfants')];
        while ($node = array_pop($stack)) {
            foreach ($node->enfants as $child) {
                $ids[] = $child->id;
                $stack[] = $child->loadMissing('enfants');
            }
        }
        return $ids;
    }

    public function children()
    {
        return $this->enfantsRecursive();
    }

    // Nouvelle méthode pour calcul récursif du prix
    public function getPrixRecursifAttribute()
    {
        if ($this->level !== 'PARENT') {
            return $this->prix;
        }

        $total = 0;
        foreach ($this->enfants as $enfant) {
            $total += $enfant->prix_recursif;
        }
        return $total;
    }

    /**
     * Obtenir la valeur de référence selon le genre/civilité du patient
     */
    public function getValeurReferenceByPatient($patient = null)
    {
        if (!$patient || !$patient->civilite) {
            return $this->valeur_ref;
        }
        
        $civilite = strtolower(trim($patient->civilite));
        
        // Mapping des civilités vers les champs appropriés
        $mapping = [
            'monsieur' => 'valeur_ref_homme',
            'mr' => 'valeur_ref_homme',
            'm.' => 'valeur_ref_homme',
            'homme' => 'valeur_ref_homme',
            
            'madame' => 'valeur_ref_femme',
            'mme' => 'valeur_ref_femme',
            'mme.' => 'valeur_ref_femme',
            'femme' => 'valeur_ref_femme',
            
            'enfant (garçon)' => 'valeur_ref_enfant_garcon',
            'enfant garçon' => 'valeur_ref_enfant_garcon',
            'garçon' => 'valeur_ref_enfant_garcon',
            'garcon' => 'valeur_ref_enfant_garcon',
            
            'enfant (fille)' => 'valeur_ref_enfant_fille',
            'enfant fille' => 'valeur_ref_enfant_fille',
            'fille' => 'valeur_ref_enfant_fille',
        ];
        
        // Trouver le champ correspondant
        $field = $mapping[$civilite] ?? null;
        
        if ($field && !empty($this->$field)) {
            return $this->$field;
        }
        
        // Fallback vers la valeur générale
        return $this->valeur_ref;
    }

    /**
     * Obtenir le label de la valeur de référence selon le patient
     */
    public function getLabelValeurReferenceByPatient($patient = null)
    {
        if (!$patient || !$patient->civilite) {
            return 'Référence';
        }
        
        $civilite = strtolower(trim($patient->civilite));
        
        $labels = [
            'monsieur' => 'Référence (Homme)',
            'mr' => 'Référence (Homme)',
            'm.' => 'Référence (Homme)',
            'homme' => 'Référence (Homme)',
            
            'madame' => 'Référence (Femme)',
            'mme' => 'Référence (Femme)',
            'mme.' => 'Référence (Femme)',
            'femme' => 'Référence (Femme)',
            
            'enfant (garçon)' => 'Référence (Garçon)',
            'enfant garçon' => 'Référence (Garçon)',
            'garçon' => 'Référence (Garçon)',
            'garcon' => 'Référence (Garçon)',
            
            'enfant (fille)' => 'Référence (Fille)',
            'enfant fille' => 'Référence (Fille)',
            'fille' => 'Référence (Fille)',
        ];
        
        return $labels[$civilite] ?? 'Référence';
    }

    /**
     * Obtenir la valeur de référence complète avec l'unité selon le patient
     */
    public function getValeurReferenceCompleteByPatient($patient = null)
    {
        $valeur = $this->getValeurReferenceByPatient($patient);
        
        if ($valeur && $this->unite) {
            return $valeur . ' ' . $this->unite;
        }
        
        return $valeur;
    }


}