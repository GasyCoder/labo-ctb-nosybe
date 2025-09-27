<?php

namespace App\Models;

use Auth;
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
        'remise',
        'status',
    ];

    protected $casts = [
        'poids' => 'decimal:2',
        'remise' => 'decimal:2',
        'updated_at' => 'datetime',
    ];

    /**
     * Vérifier si la prescription a été modifiée
     */
    public function isModified(): bool
    {
        return $this->created_at->ne($this->updated_at);
    }

    /**
     * Accesseur pour vérifier si modifié
     */
    public function getIsModifiedAttribute(): bool
    {
        return $this->isModified();
    }

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
        return $this->hasMany(Resultat::class);
    }

    public function tubes()
    {
        return $this->hasMany(Tube::class);
    }

    /**
     * Relation avec les prélèvements via les tubes
     * Remplace l'ancienne relation Many-to-Many avec prelevement_prescription
     */
    public function prelevements()
    {
        return $this->hasManyThrough(
            Prelevement::class,
            Tube::class,
            'prescription_id', // Clé étrangère dans tubes
            'id', // Clé primaire dans prelevements
            'id', // Clé primaire dans prescriptions
            'prelevement_id' // Clé étrangère dans tubes vers prelevements
        );
    }

    /**
     * Obtenir les prélèvements uniques avec leur quantité
     */
    public function prelevementsAvecQuantite()
    {
        return $this->tubes()
                   ->join('prelevements', 'tubes.prelevement_id', '=', 'prelevements.id')
                   ->select(
                       'prelevements.*',
                       DB::raw('COUNT(tubes.id) as quantite_tubes'),
                       DB::raw('GROUP_CONCAT(tubes.code_barre) as codes_barres')
                   )
                   ->groupBy('prelevements.id', 'prelevements.code', 'prelevements.denomination', 'prelevements.prix')
                   ->get();
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
     * Marquer la prescription à refaire avec réinitialisation complète
     */
    public function marquerARefaire($commentaire = null, $userId = null)
    {
        DB::beginTransaction();
        
        try {
            // Vérifier le statut actuel
            if (!in_array($this->status, [self::STATUS_VALIDE, self::STATUS_TERMINE])) {
                throw new \Exception('Cette prescription ne peut pas être remise à refaire. Statut actuel : ' . $this->status);
            }

            // Mettre à jour la prescription
            $this->update([
                'status' => self::STATUS_A_REFAIRE,
                'commentaire_biologiste' => $commentaire,
                'updated_by' => $userId ?: Auth::id()
            ]);

            // Récupérer toutes les analyses avec résultats
            $allAnalyseIds = $this->resultats()
                ->whereNull('deleted_at')
                ->pluck('analyse_id')
                ->unique()
                ->toArray();

            // Récupérer les analyses principales (dans prescription_analyse)
            $principalAnalyseIds = DB::table('prescription_analyse')
                ->where('prescription_id', $this->id)
                ->pluck('analyse_id')
                ->toArray();

            // Mettre à jour la table pivot pour les analyses principales
            if (!empty($principalAnalyseIds)) {
                DB::table('prescription_analyse')
                    ->where('prescription_id', $this->id)
                    ->whereIn('analyse_id', $principalAnalyseIds)
                    ->update([
                        'status' => AnalysePrescription::STATUS_A_REFAIRE,
                        'updated_at' => now()
                    ]);
            }

            // Réinitialiser tous les résultats (parent + enfants)
            $resetCount = $this->resultats()
                ->whereIn('analyse_id', $allAnalyseIds)
                ->update([
                    'validated_by' => null,
                    'validated_at' => null,
                    'status' => 'EN_ATTENTE',
                    'updated_at' => now()
                ]);

            DB::commit();

            // Log de l'action
            \Log::info('Prescription remise à refaire', [
                'prescription_id' => $this->id,
                'reference' => $this->reference,
                'commentaire' => $commentaire,
                'user_id' => $userId ?: Auth::id(),
                'reset_results_count' => $resetCount,
                'total_analyse_ids' => count($allAnalyseIds),
                'principal_analyse_ids' => $principalAnalyseIds
            ]);

            return [
                'success' => true,
                'message' => "Prescription {$this->reference} marquée à refaire ({$resetCount} résultats réinitialisés)",
                'reset_count' => $resetCount
            ];

        } catch (\Exception $e) {
            DB::rollback();
            
            \Log::error('Erreur lors de la mise à refaire de la prescription', [
                'prescription_id' => $this->id,
                'reference' => $this->reference,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw new \Exception('Erreur lors de la mise à refaire : ' . $e->getMessage());
        }
    }

    /**
     * Vérifier si la prescription peut être remise à refaire
     */
    public function peutEtreRemiseARefaire(): bool
    {
        return in_array($this->status, [self::STATUS_VALIDE, self::STATUS_TERMINE]);
    }

    /**
     * Obtenir le nombre d'analyses avec résultats
     */
    public function getNombreAnalysesAvecResultats(): int
    {
        return $this->resultats()
            ->whereNull('deleted_at')
            ->where(function($query) {
                $query->whereNotNull('valeur')
                    ->where('valeur', '!=', '')
                    ->orWhereNotNull('resultats');
            })
            ->distinct('analyse_id')
            ->count();
    }

    /**
     * Calculer le montant des analyses
     */
    public function getMontantAnalysesCalcule()
    {
        $this->loadMissing(['analyses.parent']);
        
        $total = 0;
        $parentsTraites = [];

        foreach ($this->analyses as $analyse) {
            if ($analyse->parent_id && !in_array($analyse->parent_id, $parentsTraites)) {
                if ($analyse->parent && $analyse->parent->prix > 0) {
                    $total += $analyse->parent->prix;
                    $parentsTraites[] = $analyse->parent_id;
                    continue;
                } elseif ($analyse->prix > 0) {
                    $total += $analyse->prix;
                    continue;
                }
            }

            if (!$analyse->parent_id && $analyse->prix > 0) {
                $total += $analyse->prix;
            }
        }
        
        return $total;
    }

    /**
     * Calculer le montant des prélèvements via les tubes
     */
    public function getMontantPrelevementsCalcule()
    {
        return $this->prelevementsAvecQuantite()->sum(function($prelevement) {
            return $prelevement->prix * $prelevement->quantite_tubes;
        });
    }

    public function getMontantTotalAttribute()
    {
        $montantAnalyses = $this->getMontantAnalysesCalcule();
        $montantPrelevements = $this->getMontantPrelevementsCalcule();
        
        $total = $montantAnalyses + $montantPrelevements;
        return max(0, $total - ($this->remise ?? 0));
    }

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

    public function hasValidatedResultsByBiologiste()
    {
        if ($this->resultats()->count() === 0) {
            return false;
        }

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
            if (empty($prescription->reference)) {
                $prescription->reference = $prescription->genererReferenceUnique();
            }
        });
    }

    public function genererReferenceUnique()
    {
        $annee = date('Y');
        
        $compteur = static::withTrashed()
                         ->whereRaw('YEAR(created_at) = ?', [$annee])
                         ->count() + 1;
        
        $numero = str_pad($compteur, 5, '0', STR_PAD_LEFT);
        $reference = "PRE-{$annee}-{$numero}";
        
        while (static::withTrashed()->where('reference', $reference)->exists()) {
            $compteur++;
            $numero = str_pad($compteur, 5, '0', STR_PAD_LEFT);
            $reference = "PRE-{$annee}-{$numero}";
        }
        
        return $reference;
    }

    public static function getNextReference()
    {
        $annee = date('Y');
        $compteur = static::withTrashed()
                         ->whereRaw('YEAR(created_at) = ?', [$annee])
                         ->count() + 1;
        
        $numero = str_pad($compteur, 5, '0', STR_PAD_LEFT);
        return "PRE-{$annee}-{$numero}";
    }

    public function antibiogrammes()
    {
        return $this->hasMany(\App\Models\Antibiogramme::class);
    }
}