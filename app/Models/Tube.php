<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tube extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'prescription_id', 
        'patient_id', 
        'prelevement_id',
        'code_barre', 
        'receptionne_par',
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

    /**
     * Utilisateur qui a réceptionné le tube
     */
    public function receptionnePar()
    {
        return $this->belongsTo(User::class, 'receptionne_par');
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


    /**
     * Génère le code-barre au format image base64
     */
    public function genererCodeBarreImage()
    {
        try {
            if (empty($this->code_barre)) {
                return '';
            }

            $generator = new BarcodeGeneratorPNG();
            
            // Générer le code-barre
            $barcodeData = $generator->getBarcode(
                $this->code_barre, 
                $generator::TYPE_CODE_128,
                2, // épaisseur
                40  // hauteur
            );

            // Convertir en base64
            return 'data:image/png;base64,' . base64_encode($barcodeData);
            
        } catch (\Exception $e) {
            \Log::error('Erreur génération code-barre', [
                'tube_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return '';
        }
    }


    /**
     * Vérifie si le code-barre peut être généré
     */
    public function peutGenererCodeBarre()
    {
        return !empty($this->code_barre) && strlen($this->code_barre) > 3;
    }

    // SCOPES
    public function scopePourPrescription($query, $prescriptionId)
    {
        return $query->where('prescription_id', $prescriptionId);
    }

    public function scopePourPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeReceptionnes($query)
    {
        return $query->whereNotNull('receptionne_par');
    }

    public function scopeNonReceptionnes($query)
    {
        return $query->whereNull('receptionne_par');
    }

    public function scopeParCodeBarre($query, $codeBarre)
    {
        return $query->where('code_barre', $codeBarre);
    }

    public function scopeParReference($query, $reference)
    {
        return $query->where('code_barre', 'like', $reference . '%');
    }

    // MÉTHODES MÉTIER

    /**
     * Génère un code-barre basé sur la référence de prescription
     */
    public function genererCodeBarre()
    {
        try {
            if (!$this->code_barre && $this->prescription) {
                // Compter les tubes existants pour cette prescription
                $numeroTube = static::where('prescription_id', $this->prescription_id)->count();
                
                // Utiliser la référence de prescription + numéro de tube
                $this->code_barre = $this->prescription->reference . '-T' . str_pad($numeroTube, 2, '0', STR_PAD_LEFT);
                $this->save();
            }
            
            return $this->code_barre;
        } catch (\Exception $e) {
            Log::error('Erreur génération code-barre tube', ['tube_id' => $this->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Marque le tube comme réceptionné
     */
    public function marquerReceptionne($userId = null)
    {
        try {
            $this->update([
                'receptionne_par' => $userId ?: Auth::id(),
            ]);

            Log::info('Tube réceptionné', [
                'tube_id' => $this->id, 
                'code_barre' => $this->code_barre,
                'receptionne_par' => $this->receptionne_par
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur marquage réception tube', ['tube_id' => $this->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Vérifie si le tube est réceptionné
     */
    public function estReceptionne()
    {
        return !is_null($this->receptionne_par);
    }

    /**
     * Retourne les informations du type de tube recommandé selon le prélèvement
     */
    public function getInfoTypeTubeRecommande()
    {
        if ($this->prelevement) {
            return $this->prelevement->getTypeTubeRecommande();
        }

        return ['code' => 'INCONNU', 'couleur' => 'Gris', 'description' => 'Type inconnu'];
    }

    /**
     * Retourne le libellé complet du tube
     */
    public function getLibelleCompletAttribute()
    {
        $info = $this->getInfoTypeTubeRecommande();
        $prelevement = $this->prelevement ? $this->prelevement->code : 'N/A';
        return "{$this->code_barre} - {$prelevement} ({$info['couleur']})";
    }

    /**
     * Statut du tube basé sur la réception
     */
    public function getStatutAttribute()
    {
        return $this->estReceptionne() ? 'RECEPTIONNE' : 'GENERE';
    }

    /**
     * Couleur d'affichage du statut
     */
    public function getStatutCouleurAttribute()
    {
        return $this->estReceptionne() ? 'green' : 'blue';
    }

    /**
     * Icône selon le type de prélèvement
     */
    public function getIconeAttribute()
    {
        if ($this->prelevement) {
            return $this->prelevement->icone;
        }
        return '🧪';
    }

    /**
     * Retourne la référence de prescription depuis le code-barre
     */
    public function getReferenceAttribute()
    {
        if ($this->code_barre && str_contains($this->code_barre, '-T')) {
            return explode('-T', $this->code_barre)[0];
        }
        return $this->prescription ? $this->prescription->reference : null;
    }

    /**
     * Retourne le numéro de tube dans la série de la prescription
     */
    public function getNumeroTubeAttribute()
    {
        if ($this->code_barre && str_contains($this->code_barre, '-T')) {
            $parts = explode('-T', $this->code_barre);
            return isset($parts[1]) ? 'T' . $parts[1] : null;
        }
        return null;
    }

    // MÉTHODES STATIQUES

    /**
     * Génère des tubes pour une prescription donnée
     */
    public static function genererPourPrescription($prescriptionId)
    {
        try {
            $prescription = Prescription::with(['prelevements'])->find($prescriptionId);
            
            if (!$prescription) {
                throw new \Exception('Prescription introuvable');
            }

            $tubes = collect();
            $compteurTube = 1;

            foreach ($prescription->prelevements as $prelevement) {
                $quantite = max(1, $prelevement->pivot->quantite ?? 1);
                
                for ($i = 0; $i < $quantite; $i++) {
                    $tube = static::create([
                        'prescription_id' => $prescription->id,
                        'patient_id' => $prescription->patient_id,
                        'prelevement_id' => $prelevement->id,
                        'code_barre' => $prescription->reference . '-T' . str_pad($compteurTube, 2, '0', STR_PAD_LEFT),
                        'type_tube' => $prelevement->pivot->type_tube_requis ?? 'SEC',
                        'volume_ml' => $prelevement->pivot->volume_requis_ml ?? 5.0,
                    ]);
                    $tubes->push($tube);
                    $compteurTube++;
                }
            }

            Log::info('Tubes générés pour prescription', [
                'prescription_id' => $prescriptionId,
                'reference' => $prescription->reference,
                'nombre_tubes' => $tubes->count()
            ]);

            return $tubes;
            
        } catch (\Exception $e) {
            Log::error('Erreur génération tubes pour prescription', [
                'prescription_id' => $prescriptionId,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Erreur lors de la génération des tubes: ' . $e->getMessage());
        }
    }

    /**
     * Recherche un tube par code-barre
     */
    public static function parCodeBarre($codeBarre)
    {
        return static::where('code_barre', $codeBarre)->first();
    }

    /**
     * Recherche des tubes par référence de prescription
     */
    public static function parReference($reference)
    {
        return static::where('code_barre', 'like', $reference . '%')->get();
    }

    /**
     * Statistiques des tubes
     */
    public static function statistiques()
    {
        return [
            'total' => static::count(),
            'receptionnes' => static::receptionnes()->count(),
            'non_receptionnes' => static::nonReceptionnes()->count(),
            'par_prelevement' => static::join('prelevements', 'tubes.prelevement_id', '=', 'prelevements.id')
                                      ->selectRaw('prelevements.code, prelevements.denomination, COUNT(*) as total')
                                      ->groupBy('prelevements.code', 'prelevements.denomination')
                                      ->orderByDesc('total')
                                      ->get(),
            'par_prescription' => static::join('prescriptions', 'tubes.prescription_id', '=', 'prescriptions.id')
                                       ->selectRaw('prescriptions.reference, COUNT(*) as total')
                                       ->groupBy('prescriptions.reference')
                                       ->orderByDesc('total')
                                       ->get()
        ];
    }

    /**
     * Tubes en attente de réception pour un patient
     */
    public static function enAttentePourPatient($patientId)
    {
        return static::with(['prelevement', 'prescription'])
                    ->where('patient_id', $patientId)
                    ->nonReceptionnes()
                    ->orderBy('created_at')
                    ->get();
    }

    /**
     * Tubes réceptionnés aujourd'hui
     */
    public static function receptionnesAujourdhui()
    {
        return static::with(['prelevement', 'patient', 'receptionnePar'])
                    ->receptionnes()
                    ->whereDate('updated_at', today())
                    ->orderBy('updated_at', 'desc')
                    ->get();
    }

    /**
     * Valide un code-barre (format attendu : REFERENCE-TXX)
     */
    public static function validerFormatCodeBarre($codeBarre)
    {
        // Format : REFERENCE-TXX (ex: PRE-2025-001-T01)
        return preg_match('/^.+-T\d{2}$/', $codeBarre);
    }

    /**
     * Génère le prochain code-barre pour une prescription
     */
    public static function prochainCodeBarrePourPrescription($prescriptionId)
    {
        $prescription = Prescription::find($prescriptionId);
        if (!$prescription) {
            return null;
        }

        $nombreTubes = static::where('prescription_id', $prescriptionId)->count();
        $prochainNumero = $nombreTubes + 1;
        
        return $prescription->reference . '-T' . str_pad($prochainNumero, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Importe des tubes depuis un fichier CSV ou une liste
     */
    public static function importerDepuisListe(array $donneesToImporter)
    {
        $tubes = collect();
        
        foreach ($donneesToImporter as $donnee) {
            try {
                $prescription = Prescription::find($donnee['prescription_id']);
                if (!$prescription) {
                    continue;
                }

                $tube = static::create([
                    'prescription_id' => $donnee['prescription_id'],
                    'patient_id' => $donnee['patient_id'],
                    'prelevement_id' => $donnee['prelevement_id'],
                    'code_barre' => $donnee['code_barre'] ?? static::prochainCodeBarrePourPrescription($donnee['prescription_id']),
                ]);

                $tubes->push($tube);
            } catch (\Exception $e) {
                Log::warning('Erreur import tube', ['donnee' => $donnee, 'error' => $e->getMessage()]);
            }
        }

        return $tubes;
    }

    /**
     * Recherche avancée de tubes
     */
    public static function rechercher($criteres = [])
    {
        $query = static::with(['prescription', 'patient', 'prelevement', 'receptionnePar']);

        if (isset($criteres['code_barre'])) {
            $query->where('code_barre', 'like', '%' . $criteres['code_barre'] . '%');
        }

        if (isset($criteres['reference'])) {
            $query->parReference($criteres['reference']);
        }

        if (isset($criteres['patient_id'])) {
            $query->where('patient_id', $criteres['patient_id']);
        }

        if (isset($criteres['prescription_id'])) {
            $query->where('prescription_id', $criteres['prescription_id']);
        }

        if (isset($criteres['receptionne'])) {
            if ($criteres['receptionne']) {
                $query->receptionnes();
            } else {
                $query->nonReceptionnes();
            }
        }

        if (isset($criteres['date_debut'])) {
            $query->whereDate('created_at', '>=', $criteres['date_debut']);
        }

        if (isset($criteres['date_fin'])) {
            $query->whereDate('created_at', '<=', $criteres['date_fin']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}