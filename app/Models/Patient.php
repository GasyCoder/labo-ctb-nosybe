<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'numero_dossier', // ✅ NOUVEAU CHAMP POUR LE NUMÉRO DE DOSSIER
        'nom', 
        'prenom', 
        'civilite', 
        'telephone', 
        'email', 
        'statut'
    ];

    // ✅ CONSTANTES POUR LES CIVILITÉS
    const CIVILITES = [
        'Madame',
        'Monsieur', 
        'Mademoiselle',
        'Enfant-garçon',
        'Enfant-fille'
    ];

    // Relations
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    // ✅ RELATION POUR LA DERNIÈRE PRESCRIPTION (optimisée)
    public function dernierePrescription()
    {
        return $this->hasOne(Prescription::class)->latest();
    }

    // ✅ RELATION POUR LA PREMIÈRE PRESCRIPTION (optimisée) 
    public function premierePrescription()
    {
        return $this->hasOne(Prescription::class)->oldest();
    }

    // Scopes pour filtrer par statut
    public function scopeFideles($query)
    {
        return $query->where('statut', 'FIDELE');
    }

    public function scopeVip($query)
    {
        return $query->where('statut', 'VIP');
    }

    public function scopeNouveaux($query)
    {
        return $query->where('statut', 'NOUVEAU');
    }

    // ✅ SCOPE POUR FILTRER LES ENFANTS
    public function scopeEnfants($query)
    {
        return $query->whereIn('civilite', ['Enfant garçon', 'Enfant fille']);
    }

    // ✅ SCOPE POUR FILTRER LES ADULTES
    public function scopeAdultes($query)
    {
        return $query->whereIn('civilite', ['Madame', 'Monsieur', 'Mademoiselle']);
    }

    // ✅ ACCESSEUR POUR DÉTERMINER SI C'EST UN ENFANT
    public function getIsEnfantAttribute()
    {
        return in_array($this->civilite, ['Enfant garçon', 'Enfant fille']);
    }

    // ✅ ACCESSEUR POUR LE GENRE (utile pour les analyses spécifiques au genre)
    public function getGenreAttribute()
    {
        switch ($this->civilite) {
            case 'Madame':
            case 'Mademoiselle':
            case 'Enfant fille':
                return 'F';
            case 'Monsieur':
            case 'Enfant garçon':
                return 'M';
            default:
                return null;
        }
    }

    // ✅ ACCESSEUR POUR L'ÂGE LE PLUS RÉCENT
    public function getLatestAgeAttribute()
    {
        $latestPrescription = $this->dernierePrescription;
        return $latestPrescription ? $latestPrescription->age : null;
    }

    // ✅ ACCESSEUR POUR L'UNITÉ D'ÂGE LA PLUS RÉCENTE
    public function getLatestUniteAgeAttribute()
    {
        $latestPrescription = $this->dernierePrescription;
        return $latestPrescription ? $latestPrescription->unite_age : 'Ans';
    }

    // ✅ MÉTHODES UTILITAIRES OPTIMISÉES POUR LES STATISTIQUES
    public function getTotalPrescriptionsAttribute()
    {
        return $this->prescriptions_count ?? $this->prescriptions()->count();
    }

    public function getTotalAnalysesAttribute()
    {
        return $this->prescriptions()
                   ->withCount('analyses')
                   ->get()
                   ->sum('analyses_count');
    }

    public function getTotalPaiementsAttribute()
    {
        return $this->prescriptions()
                   ->withCount('paiements')
                   ->get()
                   ->sum('paiements_count');
    }

    public function getMontantTotalPayeAttribute()
    {
        return $this->prescriptions()
                   ->with('paiements')
                   ->get()
                   ->flatMap->paiements
                   ->sum('montant');
    }

    // ✅ STATUT DU PATIENT BASÉ SUR SES PRESCRIPTIONS
    public function getStatutAutomatiqueAttribute()
    {
        $nombrePrescriptions = $this->getTotalPrescriptionsAttribute();
        $montantTotal = $this->getMontantTotalPayeAttribute();
        
        // Logique métier pour déterminer le statut
        if ($montantTotal >= 500000 || $nombrePrescriptions >= 10) {
            return 'VIP';
        } elseif ($nombrePrescriptions >= 3) {
            return 'FIDELE';
        }
        
        return 'NOUVEAU';
    }

    // ✅ DERNIÈRE VISITE DU PATIENT
    public function getDerniereVisiteAttribute()
    {
        $dernierePrescription = $this->dernierePrescription;
        return $dernierePrescription ? $dernierePrescription->created_at : null;
    }

    // ✅ GÉNÉRATION AUTOMATIQUE DU NUMÉRO DE DOSSIER
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($patient) {
            if (empty($patient->numero_dossier)) {
                $patient->numero_dossier = static::genererNumeroDossier();
            }
            
            // ✅ STATUT PAR DÉFAUT SI VIDE
            if (empty($patient->statut)) {
                $patient->statut = 'NOUVEAU';
            }
        });

        // ✅ MISE À JOUR AUTOMATIQUE DU STATUT (optionnel)
        static::saving(function ($patient) {
            // Si le statut est encore "NOUVEAU", on peut le mettre à jour automatiquement
            if ($patient->statut === 'NOUVEAU' && $patient->exists) {
                $patient->statut = $patient->getStatutAutomatiqueAttribute();
            }
        });
    }

    // ✅ GÉNÉRATION DU NUMÉRO DE DOSSIER UNIQUE
    public static function genererNumeroDossier()
    {
        $annee = date('Y');
        
        // Compter les patients existants pour cette année
        $compteur = static::withTrashed()
                         ->whereRaw('YEAR(created_at) = ?', [$annee])
                         ->count() + 1;
        
        $numero = str_pad($compteur, 5, '0', STR_PAD_LEFT);
        $dossier = "DOS-{$annee}-{$numero}";
        
        // ✅ VÉRIFICATION D'UNICITÉ
        while (static::withTrashed()->where('numero_dossier', $dossier)->exists()) {
            $compteur++;
            $numero = str_pad($compteur, 5, '0', STR_PAD_LEFT);
            $dossier = "DOS-{$annee}-{$numero}";
        }
        
        return $dossier;
    }

    // ✅ MÉTHODE POUR OBTENIR LE PROCHAIN NUMÉRO DE DOSSIER (utile pour prévisualisation)
    public static function getNextNumeroDossier()
    {
        $annee = date('Y');
        $compteur = static::withTrashed()
                         ->whereRaw('YEAR(created_at) = ?', [$annee])
                         ->count() + 1;
        
        $numero = str_pad($compteur, 5, '0', STR_PAD_LEFT);
        return "DOS-{$annee}-{$numero}";
    }

    // ✅ SCOPE POUR RECHERCHE GLOBALE
    public function scopeRechercher($query, $terme)
    {
        return $query->where(function ($q) use ($terme) {
            $q->where('numero_dossier', 'like', "%{$terme}%")
              ->orWhere('nom', 'like', "%{$terme}%")
              ->orWhere('prenom', 'like', "%{$terme}%")
              ->orWhere('telephone', 'like', "%{$terme}%")
              ->orWhere('email', 'like', "%{$terme}%");
        });
    }

    // ✅ SCOPE POUR LES PATIENTS ACTIFS (ayant des prescriptions récentes)
    public function scopeActifs($query, $jours = 30)
    {
        return $query->whereHas('prescriptions', function ($q) use ($jours) {
            $q->where('created_at', '>=', now()->subDays($jours));
        });
    }

    // ✅ MÉTHODE POUR ARCHIVER UN PATIENT (soft delete avec vérifications)
    public function archiver()
    {
        // Vérifier qu'il n'y a pas de prescriptions en cours
        $prescriptionsEnCours = $this->prescriptions()
            ->whereIn('status', [
                Prescription::STATUS_EN_ATTENTE,
                Prescription::STATUS_EN_COURS
            ])->count();
        
        if ($prescriptionsEnCours > 0) {
            throw new \Exception('Impossible d\'archiver ce patient car il a des prescriptions en cours.');
        }
        
        return $this->delete();
    }
}