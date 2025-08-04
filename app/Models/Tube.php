<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tube extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'prescription_id', 'patient_id', 'prelevement_id',
        'code_barre', 'numero_tube', 'statut',
        'type_tube', 'volume_ml', 'couleur_bouchon',
        'preleve_par', 'receptionne_par',
        'observations', 'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'genere_at' => 'datetime',
        'preleve_at' => 'datetime',
        'receptionne_at' => 'datetime',
        'archive_at' => 'datetime',
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

    public function incidents()
    {
        return $this->hasMany(IncidentTube::class);
    }

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
        // Génère un code-barre unique
        $this->code_barre = 'T' . date('Y') . str_pad($this->id, 6, '0', STR_PAD_LEFT);
        $this->numero_tube = 'T-' . date('Y') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
        $this->genere_at = now();
        $this->save();
        
        return $this->code_barre;
    }

    public function marquerPreleve($userId = null)
    {
        $this->update([
            'statut' => 'PRELEVE',
            'preleve_at' => now(),
            'preleve_par' => $userId ?: Auth::id(),
        ]);

        $this->loggerChangementStatut('GENERE', 'PRELEVE');
    }

    public function marquerReceptionne($userId = null)
    {
        $this->update([
            'statut' => 'RECEPTIONNE',
            'receptionne_at' => now(),
            'receptionne_par' => $userId ?: Auth::id(),
        ]);

        $this->loggerChangementStatut('PRELEVE', 'RECEPTIONNE');
    }

    public function demarrerAnalyses()
    {
        $this->update(['statut' => 'EN_ANALYSE']);
        
        // Marquer toutes les analyses comme démarrées
        $this->analyses()->wherePivot('statut_analyse', 'PLANIFIEE')
             ->updateExistingPivot($this->analyses->pluck('id'), [
                 'statut_analyse' => 'EN_COURS',
                 'demarree_at' => now(),
                 'technicien_id' => Auth::id(),
             ]);
    }

    public function terminerAnalyses()
    {
        // Vérifier si toutes les analyses sont terminées
        $analysesPlanifiees = $this->analyses()
                                   ->wherePivot('statut_analyse', '!=', 'TERMINEE')
                                   ->wherePivot('statut_analyse', '!=', 'VALIDEE')
                                   ->count();

        if ($analysesPlanifiees === 0) {
            $this->update(['statut' => 'ANALYSE_TERMINEE']);
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
        TubeHistoriqueStatut::create([
            'tube_id' => $this->id,
            'ancien_statut' => $ancien,
            'nouveau_statut' => $nouveau,
            'modifie_par' => Auth::id(),
            'modifie_at' => now(),
        ]);
    }

    // MÉTHODE STATIQUE pour génération en masse
    public static function genererPourPrescription($prescriptionId)
    {
        $prescription = Prescription::with(['prelevements'])->find($prescriptionId);
        $tubes = collect();

        foreach ($prescription->prelevements as $prelevement) {
            for ($i = 0; $i < $prelevement->pivot->quantite; $i++) {
                $tube = static::create([
                    'prescription_id' => $prescription->id,
                    'patient_id' => $prescription->patient_id,
                    'prelevement_id' => $prelevement->id,
                    'type_tube' => $prelevement->pivot->type_tube_requis,
                    'volume_ml' => $prelevement->pivot->volume_requis_ml,
                    'statut' => 'GENERE',
                ]);

                $tube->genererCodeBarre();
                
                // Associer les analyses requises à ce tube
                $analyses = $prescription->analyses()
                                        ->where('prelevement_requis', $prelevement->id)
                                        ->get();
                
                foreach ($analyses as $analyse) {
                    $tube->analyses()->attach($analyse->id, [
                        'statut_analyse' => 'PLANIFIEE',
                    ]);
                }

                $tubes->push($tube);
            }
        }

        // Marquer les prélèvements comme générés
        $prescription->prelevements()->updateExistingPivot($prescription->prelevements->pluck('id'), [
            'tubes_generes' => true,
            'tubes_generes_at' => now(),
        ]);

        // Changer statut prescription
        $prescription->update(['status' => 'PRELEVEMENTS_GENERES']);

        return $tubes;
    }
}