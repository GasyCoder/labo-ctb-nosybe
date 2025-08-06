<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescription extends Model
{
    use HasFactory, SoftDeletes;

    // Constantes pour les statuts
    const STATUS_EN_ATTENTE = 'EN_ATTENTE';
    const STATUS_EN_COURS = 'EN_COURS';
    const STATUS_TERMINE = 'TERMINE';
    const STATUS_VALIDE = 'VALIDE';
    const STATUS_A_REFAIRE = 'A_REFAIRE';
    const STATUS_ARCHIVE = 'ARCHIVE';
    const STATUS_PRELEVEMENTS_GENERES = 'PRELEVEMENTS_GENERES';

    protected $fillable = [
        'secretaire_id',
        'patient_id',
        'prescripteur_id',
        'patient_type',
        'age',
        'unite_age',
        'poids',
        'renseignement_clinique',
        'remise',
        'is_archive', // Si vous ajoutez la colonne
        'status',
    ];

    // Ajouter les casts pour les décimaux
    protected $casts = [
        'remise' => 'decimal:2',
        'poids' => 'decimal:2',
        'is_archive' => 'boolean',
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

    // La relation Many to Many avec Analyse
    public function analyses()
    {
        return $this->belongsToMany(Analyse::class, 'prescription_analyse')
            ->withTimestamps();
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

    // CORRECTION: Méthode améliorée pour calculer le montant des analyses
    public function getMontantAnalysesAttribute()
    {
        $total = 0;
        $parentsTraites = [];

        foreach ($this->analyses as $analyse) {
            // Si l'analyse a un parent et qu'on ne l'a pas encore traité
            if ($analyse->parent_id && !in_array($analyse->parent_id, $parentsTraites)) {
                $parent = Analyse::find($analyse->parent_id);

                if ($parent && $parent->prix > 0) {
                    // Ajouter le prix du parent une seule fois
                    $total += $parent->prix;
                    $parentsTraites[] = $analyse->parent_id;
                    continue;
                }
            }

            // Si pas de parent ou parent sans prix, utiliser le prix de l'analyse
            if (!$analyse->parent_id || !in_array($analyse->parent_id, $parentsTraites)) {
                $total += $analyse->prix;
            }
        }

        return $total;
    }

    // Méthode pour calculer le montant total payé (analyses + prélèvements - remise)
    public function getMontantTotalAttribute()
    {
        $montantAnalyses = $this->montant_analyses;
        $montantPrelevements = $this->prelevements->sum(function ($prelevement) {
            return $prelevement->pivot->prix_unitaire * $prelevement->pivot->quantite;
        });

        return max(0, $montantAnalyses + $montantPrelevements - $this->remise);
    }

    // COMMISSION PRESCRIPTEUR: 10% du montant des analyses
    public function getPartPrescripteurAttribute()
    {
        return round($this->montant_analyses * 0.10, 2);
    }

    // Vérifier si la prescription est payée
    public function getEstPayeeAttribute()
    {
        return $this->paiements()->where('montant', '>=', $this->montant_total)->exists();
    }

    // Méthode pour calculer la commission seulement si payée et réalisée
    public function getCommissionPrescripteurAttribute()
    {
        // Commission seulement si prescription payée et analyses terminées
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
                'is_archive' => true // Si vous ajoutez la colonne
            ]);
            return true;
        }
        return false;
    }

    public function unarchive()
    {
        $this->update([
            'status' => self::STATUS_VALIDE,
            'is_archive' => false // Si vous ajoutez la colonne
        ]);
    }

    public function hasValidatedResultsByBiologiste()
    {
        // Vérifier que tous les résultats sont validés
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
        return $query->whereNotIn('status', [self::STATUS_ARCHIVE])
            ->where(function ($q) {
                $q->whereNull('is_archive')
                    ->orWhere('is_archive', false);
            });
    }

    public function scopeArchivees($query)
    {
        return $query->where('status', self::STATUS_ARCHIVE)
            ->orWhere('is_archive', true);
    }

    public function scopeParPrescripteur($query, $prescripteurId)
    {
        return $query->where('prescripteur_id', $prescripteurId);
    }

    public function scopeParPeriode($query, $dateDebut, $dateFin)
    {
        return $query->whereBetween('created_at', [$dateDebut, $dateFin]);
    }

    // Méthode pour obtenir le libellé du statut
    public function getStatusLabelAttribute()
    {
        $labels = [
            self::STATUS_EN_ATTENTE => 'En attente',
            self::STATUS_EN_COURS => 'En cours',
            self::STATUS_TERMINE => 'Terminé',
            self::STATUS_VALIDE => 'Validé',
            self::STATUS_A_REFAIRE => 'À refaire',
            self::STATUS_ARCHIVE => 'Archivé',
            self::STATUS_PRELEVEMENTS_GENERES => 'Prélèvements générés',
        ];

        return $labels[$this->status] ?? $this->status;
    }
}