<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Antibiotique extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'antibiotiques';

    protected $fillable = [
        'famille_id',
        'designation',
        'commentaire',
        'status',
    ];

    protected $casts = [
        'famille_id' => 'integer',
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $with = ['famille'];

    // Scopes
    public function scopeActives($query)
    {
        return $query->where('status', true);
    }

    public function scopeInactives($query)
    {
        return $query->where('status', false);
    }

    public function scopeByFamille($query, $familleId)
    {
        return $query->where('famille_id', $familleId);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('designation', 'like', '%' . $term . '%')
                ->orWhere('commentaire', 'like', '%' . $term . '%')
                ->orWhereHas('famille', function ($sq) use ($term) {
                    $sq->where('designation', 'like', '%' . $term . '%');
                });
        });
    }

    public function scopeWithBacteries($query)
    {
        return $query->has('bacteries');
    }

    // Relations
    public function famille()
    {
        return $this->belongsTo(BacterieFamille::class, 'famille_id');
    }

    public function bacteries()
    {
        return $this->belongsToMany(
            Bacterie::class,
            'bacterie_antibiotique',
            'antibiotique_id',
            'bacterie_id'
        )->withTimestamps();
    }

    // // Relations pour les tests d'antibiogramme (si applicable)
    // public function antibiogrammes()
    // {
    //     return $this->hasMany(Antibiogramme::class);
    // }

    // Accessors
    public function getStatusTextAttribute()
    {
        return $this->status ? 'Actif' : 'Inactif';
    }

    public function getStatusBadgeAttribute()
    {
        return $this->status
            ? '<span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Actif</span>'
            : '<span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">Inactif</span>';
    }

    public function getFullNameAttribute()
    {
        return $this->famille->designation . ' - ' . $this->designation;
    }

    public function getFamilleNameAttribute()
    {
        return $this->famille ? $this->famille->designation : 'N/A';
    }

    // public function getShortCommentaireAttribute()
    // {
    //     return $this->commentaire ? \Str::limit($this->commentaire, 50) : '';
    // }

    // Mutators
    public function setDesignationAttribute($value)
    {
        $this->attributes['designation'] = trim($value);
    }

    public function setCommentaireAttribute($value)
    {
        $this->attributes['commentaire'] = $value ? trim($value) : null;
    }

    // Méthodes personnalisées
    public function canBeDeleted()
    {
        return $this->bacteries()->count() === 0 &&
            (!method_exists($this, 'antibiogrammes') || $this->antibiogrammes()->count() === 0);
    }

    public function getBacteriesCount()
    {
        return $this->bacteries()->count();
    }

    public function getActiveBacteriesCount()
    {
        return $this->bacteries()->where('status', true)->count();
    }

    public function isCompatibleWithBacterie($bacterieId)
    {
        return $this->bacteries()->where('bacterie_id', $bacterieId)->exists();
    }

    public function attachBacterie($bacterieId)
    {
        if (!$this->isCompatibleWithBacterie($bacterieId)) {
            $this->bacteries()->attach($bacterieId);
        }
    }

    public function detachBacterie($bacterieId)
    {
        $this->bacteries()->detach($bacterieId);
    }

    public function syncBacteries($bacterieIds)
    {
        $this->bacteries()->sync($bacterieIds);
    }

    // Méthodes statiques utiles
    public static function getByFamille($familleId)
    {
        return static::where('famille_id', $familleId)->actives()->get();
    }

    public static function searchByName($term)
    {
        return static::where('designation', 'like', '%' . $term . '%')->actives()->get();
    }

    public static function getMostUsed($limit = 10)
    {
        return static::withCount('bacteries')
            ->orderByDesc('bacteries_count')
            ->actives()
            ->limit($limit)
            ->get();
    }

    // Méthodes pour les statistiques
    public function getUsageStats()
    {
        return [
            'total_bacteries' => $this->getBacteriesCount(),
            'active_bacteries' => $this->getActiveBacteriesCount(),
            'usage_percentage' => $this->getBacteriesCount() > 0
                ? round(($this->getActiveBacteriesCount() / $this->getBacteriesCount()) * 100, 2)
                : 0
        ];
    }

    // Boot method pour les événements
    protected static function boot()
    {
        parent::boot();

        // Avant suppression, vérifier les relations
        static::deleting(function ($antibiotique) {
            if (!$antibiotique->canBeDeleted()) {
                throw new \Exception('Impossible de supprimer cet antibiotique car il est lié à des bactéries ou des tests.');
            }
        });

        // Après création, associer automatiquement aux bactéries de la même famille
        static::created(function ($antibiotique) {
            // Optionnel : associer automatiquement aux bactéries de la même famille
            $bacteries = Bacterie::where('famille_id', $antibiotique->famille_id)
                ->where('status', true)
                ->pluck('id');

            if ($bacteries->isNotEmpty()) {
                $antibiotique->bacteries()->sync($bacteries);
            }
        });
    }
}