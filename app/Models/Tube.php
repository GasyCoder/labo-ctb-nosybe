<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Tube extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'prescription_id', 'patient_id', 'prelevement_id',
        'code_barre', 'numero_tube', 'statut',
        'type_tube', 'volume_ml', 'couleur_bouchon',
        'preleve_par', 'receptionne_par',
        'observations', 'metadata',
        'genere_at', 'preleve_at', 'receptionne_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'genere_at' => 'datetime',
        'preleve_at' => 'datetime',
        'receptionne_at' => 'datetime',
        'archive_at' => 'datetime',
        'volume_ml' => 'decimal:2',
    ];

    // RELATIONS
    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function prelevement()
    {
        return $this->belongsTo(Prelevement::class);
    }

    public function analyses()
    {
        return $this->belongsToMany(Analyse::class, 'tube_analyse')
                    ->withPivot(['statut_analyse', 'demarree_at', 'terminee_at', 'validee_at', 'technicien_id', 'validee_par'])
                    ->withTimestamps();
    }

    public function resultats()
    {
        return $this->hasMany(Resultat::class);
    }

    // public function incidents()
    // {
    //     return $this->hasMany(IncidentTube::class);
    // }

    // SCOPES
    public function scopeStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    public function scopePourPrescription($query, $prescriptionId)
    {
        return $query->where('prescription_id', $prescriptionId);
    }

    public function scopeGeneres($query)
    {
        return $query->where('statut', 'GENERE');
    }

    public function scopeEnAttentePrelvement($query)
    {
        return $query->where('statut', 'GENERE');
    }

    public function scopePrelevesNonReceptionnes($query)
    {
        return $query->where('statut', 'PRELEVE');
    }

    // MÉTHODES MÉTIER
    public function genererCodeBarre()
    {
        try {
            // Génère un code-barre unique basé sur l'ID et l'année
            $this->code_barre = 'T' . date('Y') . str_pad($this->id, 6, '0', STR_PAD_LEFT);
            $this->numero_tube = 'T-' . date('Y') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
            $this->genere_at = now();
            $this->save();
            
            return $this->code_barre;
        } catch (\Exception $e) {
            Log::error('Erreur génération code-barre tube', ['tube_id' => $this->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function marquerPreleve($userId = null)
    {
        try {
            $this->update([
                'statut' => 'PRELEVE',
                'preleve_at' => now(),
                'preleve_par' => $userId ?: Auth::id(),
            ]);

            $this->loggerChangementStatut('GENERE', 'PRELEVE');
        } catch (\Exception $e) {
            Log::error('Erreur marquage prélèvement tube', ['tube_id' => $this->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function marquerReceptionne($userId = null)
    {
        try {
            $this->update([
                'statut' => 'RECEPTIONNE',
                'receptionne_at' => now(),
                'receptionne_par' => $userId ?: Auth::id(),
            ]);

            $this->loggerChangementStatut('PRELEVE', 'RECEPTIONNE');
        } catch (\Exception $e) {
            Log::error('Erreur marquage réception tube', ['tube_id' => $this->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function demarrerAnalyses()
    {
        try {
            $this->update(['statut' => 'EN_ANALYSE']);
            
            // Marquer toutes les analyses comme démarrées
            $this->analyses()->wherePivot('statut_analyse', 'PLANIFIEE')
                 ->updateExistingPivot($this->analyses->pluck('id'), [
                     'statut_analyse' => 'EN_COURS',
                     'demarree_at' => now(),
                     'technicien_id' => Auth::id(),
                 ]);
        } catch (\Exception $e) {
            Log::error('Erreur démarrage analyses tube', ['tube_id' => $this->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function terminerAnalyses()
    {
        try {
            // Vérifier si toutes les analyses sont terminées
            $analysesPlanifiees = $this->analyses()
                                       ->wherePivot('statut_analyse', '!=', 'TERMINEE')
                                       ->wherePivot('statut_analyse', '!=', 'VALIDEE')
                                       ->count();

            if ($analysesPlanifiees === 0) {
                $this->update(['statut' => 'ANALYSE_TERMINEE']);
            }
        } catch (\Exception $e) {
            Log::error('Erreur finalisation analyses tube', ['tube_id' => $this->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getStatutCouleurAttribute()
    {
        return match($this->statut) {
            'GENERE' => 'blue',
            'PRELEVE' => 'orange',
            'RECEPTIONNE' => 'yellow',
            'EN_ANALYSE' => 'purple',
            'ANALYSE_TERMINEE' => 'green',
            'ARCHIVE' => 'gray',
            'PERDU' => 'red',
            'REJETE' => 'red',
            default => 'gray'
        };
    }

    private function loggerChangementStatut($ancien, $nouveau)
    {
        try {
            // Vérifier si la table existe avant de logger
            if (class_exists('App\Models\TubeHistoriqueStatut')) {
                TubeHistoriqueStatut::create([
                    'tube_id' => $this->id,
                    'ancien_statut' => $ancien,
                    'nouveau_statut' => $nouveau,
                    'modifie_par' => Auth::id(),
                    'modifie_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            // Log silencieusement les erreurs d'historique pour ne pas bloquer le processus principal
            Log::warning('Erreur logging changement statut tube', ['tube_id' => $this->id, 'error' => $e->getMessage()]);
        }
    }

    // MÉTHODE STATIQUE pour génération en masse - VERSION SÉCURISÉE
    public static function genererPourPrescription($prescriptionId)
    {
        try {
            $prescription = Prescription::with(['prelevements', 'analyses'])->find($prescriptionId);
            
            if (!$prescription) {
                throw new \Exception('Prescription introuvable');
            }

            $tubes = collect();

            foreach ($prescription->prelevements as $prelevement) {
                $quantite = max(1, $prelevement->pivot->quantite ?? 1);
                
                for ($i = 0; $i < $quantite; $i++) {
                    $tube = static::create([
                        'prescription_id' => $prescription->id,
                        'patient_id' => $prescription->patient_id,
                        'prelevement_id' => $prelevement->id,
                        'type_tube' => $prelevement->pivot->type_tube_requis ?? 'SEC',
                        'volume_ml' => $prelevement->pivot->volume_requis_ml ?? 5.0,
                        'statut' => 'GENERE',
                        'genere_at' => now(),
                    ]);

                    // Générer le code-barre après création pour avoir l'ID
                    $tube->update([
                        'code_barre' => 'T' . date('Y') . str_pad($tube->id, 6, '0', STR_PAD_LEFT),
                        'numero_tube' => 'T-' . date('Y') . '-' . str_pad($tube->id, 6, '0', STR_PAD_LEFT),
                    ]);
                    
                    // Associer les analyses de la prescription à ce tube
                    // Version simplifiée qui associe toutes les analyses au tube
                    foreach ($prescription->analyses as $analyse) {
                        $tube->analyses()->attach($analyse->id, [
                            'statut_analyse' => 'PLANIFIEE',
                        ]);
                    }

                    $tubes->push($tube);
                }
            }

            // Marquer les prélèvements comme générés
            foreach ($prescription->prelevements as $prelevement) {
                $prescription->prelevements()->updateExistingPivot($prelevement->id, [
                    'tubes_generes' => true,
                    'tubes_generes_at' => now(),
                ]);
            }

            // Changer statut prescription
            $prescription->update(['status' => 'PRELEVEMENTS_GENERES']);

            return $tubes;
            
        } catch (\Exception $e) {
            Log::error('Erreur génération tubes pour prescription', [
                'prescription_id' => $prescriptionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Erreur lors de la génération des tubes: ' . $e->getMessage());
        }
    }

    // Méthode pour récupérer le type de tube recommandé selon le prélèvement
    public static function getTypeTubeRecommande($prelevementId)
    {
        $types = [
            'SANG' => 'SEC',
            'URINE' => 'URINE',
            'SELLE' => 'STERILE',
            'EXPECTORATION' => 'STERILE',
            'LIQUIDE' => 'STERILE',
        ];

        try {
            $prelevement = Prelevement::find($prelevementId);
            if ($prelevement && isset($types[$prelevement->type])) {
                return $types[$prelevement->type];
            }
        } catch (\Exception $e) {
            Log::warning('Erreur récupération type tube', ['prelevement_id' => $prelevementId]);
        }

        return 'SEC'; // Défaut
    }

    // Méthode pour calculer le volume requis selon les analyses
    public static function calculerVolumeRequis($analyses)
    {
        $volume = 5.0; // Volume minimum par défaut
        
        try {
            foreach ($analyses as $analyse) {
                // Logique pour calculer le volume selon le type d'analyse
                if (stripos($analyse->designation, 'HÉMOGRAMME') !== false) {
                    $volume = max($volume, 3.0);
                } elseif (stripos($analyse->designation, 'BIOCHIMIE') !== false) {
                    $volume = max($volume, 5.0);
                } elseif (stripos($analyse->designation, 'SÉROLOGIE') !== false) {
                    $volume = max($volume, 2.0);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Erreur calcul volume requis', ['error' => $e->getMessage()]);
        }

        return min($volume, 10.0); // Maximum 10ml
    }
}