<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescription extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['reference'];

    // Constantes pour les statuts
    const STATUS_EN_ATTENTE = 'EN_ATTENTE';
    const STATUS_EN_COURS = 'EN_COURS';
    const STATUS_TERMINE = 'TERMINE';
    const STATUS_VALIDE = 'VALIDE';
    const STATUS_A_REFAIRE = 'A_REFAIRE';
    const STATUS_ARCHIVE = 'ARCHIVE';

    protected $fillable = [
        'secretaire_id',
        'reference',
        'patient_id',
        'prescripteur_id',
        'patient_type',
        'age',
        'unite_age',
        'poids',
        'renseignement_clinique',
        'remise',
        'status',
    ];

    // Ajouter les casts pour les décimaux
    protected $casts = [
        'remise' => 'decimal:2',
        'poids' => 'decimal:2',
    ];

    // RELATIONS
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

    public function analyses()
    {
        return $this->belongsToMany(Analyse::class, 'prescription_analyse')->withTimestamps();
    }

    public function resultats()
    {
        return $this->hasManyThrough(Resultat::class, Analyse::class, 'id', 'analyse_id', 'id', 'id')
            ->whereIn('analyse_id', function ($query) {
                $query->select('analyse_id')
                    ->from('prescription_analyse')
                    ->where('prescription_id', $this->id);
            });
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

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
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

    public function getMontantAnalysesAttribute()
    {
        $total = 0;
        $parentsTraites = [];

        foreach ($this->analyses as $analyse) {
            if ($analyse->parent_id && !in_array($analyse->parent_id, $parentsTraites)) {
                $parent = Analyse::find($analyse->parent_id);

                if ($parent && $parent->prix > 0) {
                    $total += $parent->prix;
                    $parentsTraites[] = $analyse->parent_id;
                    continue;
                }
            }

            if (!$analyse->parent_id || !in_array($analyse->parent_id, $parentsTraites)) {
                $total += $analyse->prix;
            }
        }
        return $total;
    }

    public function getMontantTotalAttribute()
    {
        $montantAnalyses = $this->montant_analyses;
        $montantPrelevements = $this->prelevements->sum(function ($prelevement) {
            return $prelevement->pivot->prix_unitaire * $prelevement->pivot->quantite;
        });
        return max(0, $montantAnalyses + $montantPrelevements - $this->remise);
    }

    public function getPartPrescripteurAttribute()
    {
        return round($this->montant_analyses * 0.10, 2);
    }

    public function getEstPayeeAttribute()
    {
        return $this->paiements()->where('montant', '>=', $this->montant_total)->exists();
    }

    public function getCommissionPrescripteurAttribute()
    {
        if ($this->est_payee && in_array($this->status, [self::STATUS_TERMINE, self::STATUS_VALIDE, self::STATUS_ARCHIVE])) {
            return $this->part_prescripteur;
        }
        return 0;
    }

    // MÉTHODES D'ARCHIVAGE
    public function archive()
    {
        if ($this->hasValidatedResultsByBiologiste()) {
            $this->update([
                'status' => self::STATUS_ARCHIVE,
            ]);
            return true;
        }
        return false;
    }

    public function unarchive()
    {
        $this->update([
            'status' => self::STATUS_VALIDE,
        ]);
    }

    public function hasValidatedResultsByBiologiste()
    {
        return $this->analyses->every(function ($analyse) {
            return $analyse->resultats->every(function ($resultat) {
                return !is_null($resultat->validated_by);
            });
        });
    }

    // SCOPES utiles
    public function scopePayees($query)
    {
        return $query->whereHas('paiements');
    }

    public function scopeTerminees($query)
    {
        return $query->whereIn('status', [self::STATUS_TERMINE, self::STATUS_VALIDE, self::STATUS_ARCHIVE]);
    }

    public function scopeActives($query)
    {
        return $query->where('status', '!=', self::STATUS_ARCHIVE);
    }

    public function scopeArchivees($query)
    {
        return $query->where('status', self::STATUS_ARCHIVE);
    }

    public function scopeParPrescripteur($query, $prescripteurId)
    {
        return $query->where('prescripteur_id', $prescripteurId);
    }

    public function scopeParPeriode($query, $dateDebut, $dateFin)
    {
        return $query->whereBetween('created_at', [$dateDebut, $dateFin]);
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            self::STATUS_EN_ATTENTE => 'En attente',
            self::STATUS_EN_COURS => 'En cours',
            self::STATUS_TERMINE => 'Terminé',
            self::STATUS_VALIDE => 'Validé',
            self::STATUS_A_REFAIRE => 'À refaire',
            self::STATUS_ARCHIVE => 'Archivé',
        ];

        return $labels[$this->status] ?? $this->status;
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($prescription) {
            $prescription->reference = $prescription->genererReferenceUnique();
        });
    }

    public function genererReferenceUnique()
    {
        $annee = date('Y');
        $numero = str_pad(Prescription::withTrashed()->count() + 1, 5, '0', STR_PAD_LEFT);
        return "PAT{$annee}{$numero}";
    }
}
