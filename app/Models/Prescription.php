<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'status',
    ];

    protected $casts = [
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

    // RELATION CORRIGÉE : Résultats directement liés à la prescription
    public function resultats()
    {
        return $this->hasMany(Resultat::class);
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

    /**
     * Calculer le montant des analyses avec chargement des relations
     */
    public function getMontantAnalysesCalcule()
    {
        $this->loadMissing(['analyses.parent']);
        
        $total = 0;
        $parentsTraites = [];

        foreach ($this->analyses as $analyse) {
            // Si analyse enfant ET parent a un prix > 0
            if ($analyse->parent_id && !in_array($analyse->parent_id, $parentsTraites)) {
                if ($analyse->parent && $analyse->parent->prix > 0) {
                    $total += $analyse->parent->prix;
                    $parentsTraites[] = $analyse->parent_id;
                    continue;
                }
                // Si parent prix = 0, on prend le prix de l'enfant
                elseif ($analyse->prix > 0) {
                    $total += $analyse->prix;
                    continue;
                }
            }

            // Analyse sans parent
            if (!$analyse->parent_id && $analyse->prix > 0) {
                $total += $analyse->prix;
            }
        }
        
        return $total;
    }

    public function getMontantTotalAttribute()
    {
        $montantAnalyses = $this->getMontantAnalysesCalcule();
        $montantPrelevements = $this->prelevements->sum(function ($prelevement) {
            return $prelevement->pivot->prix_unitaire * $prelevement->pivot->quantite;
        });
        return $montantAnalyses + $montantPrelevements;
    }

    // COMMISSION SIMPLIFIÉE (utilise le champ dans paiements)
    public function getCommissionPrescripteurAttribute()
    {
        return $this->paiements->sum('commission_prescripteur');
    }

    public function getEstPayeeAttribute()
    {
        return $this->paiements()->exists();
    }

    public function getEstPayeeCompletementAttribute()
    {
        $montantTotal = $this->getMontantTotalAttribute();
        $montantPaye = $this->paiements()->sum('montant');
        return $montantPaye >= $montantTotal;
    }

    // MÉTHODES D'ARCHIVAGE
    public function archive()
    {
        if ($this->hasValidatedResultsByBiologiste()) {
            $this->update(['status' => self::STATUS_ARCHIVE]);
            return true;
        }
        return false;
    }

    public function unarchive()
    {
        $this->update(['status' => self::STATUS_VALIDE]);
    }

    // MÉTHODE CORRIGÉE : Vérification des résultats validés
    public function hasValidatedResultsByBiologiste()
    {
        // Si pas de résultats, on ne peut pas archiver
        if ($this->resultats()->count() === 0) {
            return false;
        }

        // Tous les résultats doivent être validés
        return $this->resultats()->whereNull('validated_by')->count() === 0;
    }

    // SCOPES
    public function scopePayees($query)
    {
        return $query->whereHas('paiements');
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