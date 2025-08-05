<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescription extends Model
{
    use HasFactory, SoftDeletes;

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
        'is_archive',
        'status',
    ];

    // Ajouter les casts pour les décimaux
    protected $casts = [
        'remise' => 'decimal:2',
        'poids' => 'decimal:2',
    ];

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
        $montantPrelevements = $this->prelevements->sum(function($prelevement) {
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
        if ($this->est_payee && in_array($this->status, ['ANALYSE_TERMINEE', 'TERMINE', 'ARCHIVE'])) {
            return $this->part_prescripteur;
        }
        
        return 0;
    }

    // SCOPES utiles
    public function scopePayees($query)
    {
        return $query->whereHas('paiements');
    }

    public function scopeTerminees($query)
    {
        return $query->whereIn('status', ['ANALYSE_TERMINEE', 'TERMINE', 'ARCHIVE']);
    }

    public function scopeParPrescripteur($query, $prescripteurId)
    {
        return $query->where('prescripteur_id', $prescripteurId);
    }

    public function scopeParPeriode($query, $dateDebut, $dateFin)
    {
        return $query->whereBetween('created_at', [$dateDebut, $dateFin]);
    }
}
