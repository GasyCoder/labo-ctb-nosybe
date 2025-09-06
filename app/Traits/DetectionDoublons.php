<?php

namespace App\Traits;

use App\Services\DetectionDoublonsService;
use Illuminate\Support\Collection;

trait DetectionDoublons
{
    /**
     * Détecter les doublons avec le service avancé
     */
    public static function detecterDoublonsAvances($nom, $prenom, $dateNaissance = null, $telephone = null, $email = null): Collection
    {
        return DetectionDoublonsService::detecterDoublonsAvances($nom, $prenom, $dateNaissance, $telephone, $email);
    }

    /**
     * Obtenir des recommandations basées sur les similarités
     */
    public static function obtenirRecommandations($similarites): array
    {
        return DetectionDoublonsService::genererRecommandations($similarites);
    }

    /**
     * Vérifier si un patient peut être créé sans risque de doublon
     */
    public static function peutEtreCree($nom, $prenom, $dateNaissance = null, $telephone = null, $email = null): array
    {
        $similarites = self::detecterDoublonsAvances($nom, $prenom, $dateNaissance, $telephone, $email);
        $recommendations = self::obtenirRecommandations($similarites);
        
        $peutCreer = true;
        $forcerVerification = false;
        $bloquer = false;
        
        foreach ($recommendations as $rec) {
            switch ($rec['action']) {
                case 'BLOQUER_CREATION':
                    $peutCreer = false;
                    $bloquer = true;
                    break;
                case 'VERIFICATION_OBLIGATOIRE':
                    $forcerVerification = true;
                    break;
            }
        }
        
        return [
            'peut_creer' => $peutCreer,
            'forcer_verification' => $forcerVerification,
            'bloquer' => $bloquer,
            'similarites' => $similarites,
            'recommendations' => $recommendations
        ];
    }

    /**
     * Méthode simplifiée pour la recherche de doublons (rétrocompatibilité)
     */
    public static function rechercherDoublonsPotentiels($nom, $prenom, $dateNaissance = null)
    {
        $similarites = self::detecterDoublonsAvances($nom, $prenom, $dateNaissance);
        
        // Filtrer seulement les scores élevés pour la méthode simple
        return $similarites->filter(fn($item) => $item['score'] >= 80)
                          ->pluck('patient');
    }

    /**
     * Valider l'unicité avant création
     */
    public function validerUnicite(): array
    {
        $erreurs = [];
        
        // Vérifier les doublons exacts
        $doublonsExacts = static::where('nom', $this->nom)
                                ->where('prenom', $this->prenom)
                                ->where('date_naissance', $this->date_naissance)
                                ->where('id', '!=', $this->id ?? 0)
                                ->exists();
        
        if ($doublonsExacts) {
            $erreurs[] = 'Un patient avec exactement les mêmes informations existe déjà';
        }
        
        // Vérifier téléphone unique si renseigné
        if ($this->telephone) {
            $telephoneExiste = static::where('telephone', $this->telephone)
                                   ->where('id', '!=', $this->id ?? 0)
                                   ->exists();
            
            if ($telephoneExiste) {
                $erreurs[] = 'Ce numéro de téléphone est déjà utilisé par un autre patient';
            }
        }
        
        // Vérifier email unique si renseigné
        if ($this->email) {
            $emailExiste = static::where('email', $this->email)
                                ->where('id', '!=', $this->id ?? 0)
                                ->exists();
            
            if ($emailExiste) {
                $erreurs[] = 'Cette adresse email est déjà utilisée par un autre patient';
            }
        }
        
        return $erreurs;
    }

    /**
     * Obtenir un résumé des informations du patient pour comparaison
     */
    public function getResumeComparaison(): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'civilite' => $this->civilite,
            'date_naissance' => $this->date_naissance?->format('Y-m-d'),
            'age_formate' => $this->age_formate,
            'telephone' => $this->telephone,
            'email' => $this->email,
            'numero_dossier' => $this->numero_dossier,
            'nombre_prescriptions' => $this->prescriptions->count(),
            'derniere_visite' => $this->derniere_visite?->format('Y-m-d'),
            'created_at' => $this->created_at->format('Y-m-d H:i')
        ];
    }

    /**
     * Comparer ce patient avec un autre
     */
    public function comparerAvec($autrePatient): array
    {
        $differences = [];
        $similarites = [];
        
        $champs = ['nom', 'prenom', 'civilite', 'date_naissance', 'telephone', 'email'];
        
        foreach ($champs as $champ) {
            $valeur1 = $this->$champ;
            $valeur2 = $autrePatient->$champ;
            
            if ($valeur1 != $valeur2) {
                $differences[$champ] = [
                    'patient1' => $valeur1,
                    'patient2' => $valeur2
                ];
            } else {
                $similarites[] = $champ;
            }
        }
        
        return [
            'differences' => $differences,
            'similarites' => $similarites,
            'score_similarite' => $this->calculerScoreSimilarite($autrePatient)
        ];
    }

    /**
     * Calculer un score de similarité entre deux patients
     */
    private function calculerScoreSimilarite($autrePatient): int
    {
        $score = 0;
        $total = 0;
        
        // Nom (poids: 25%)
        if ($this->nom && $autrePatient->nom) {
            similar_text(strtoupper($this->nom), strtoupper($autrePatient->nom), $percent);
            $score += $percent * 0.25;
            $total += 25;
        }
        
        // Prénom (poids: 25%)
        if ($this->prenom && $autrePatient->prenom) {
            similar_text(strtoupper($this->prenom), strtoupper($autrePatient->prenom), $percent);
            $score += $percent * 0.25;
            $total += 25;
        }
        
        // Date de naissance (poids: 30%)
        if ($this->date_naissance && $autrePatient->date_naissance) {
            if ($this->date_naissance == $autrePatient->date_naissance) {
                $score += 30;
            }
            $total += 30;
        }
        
        // Téléphone (poids: 10%)
        if ($this->telephone && $autrePatient->telephone) {
            if ($this->telephone == $autrePatient->telephone) {
                $score += 10;
            }
            $total += 10;
        }
        
        // Email (poids: 10%)
        if ($this->email && $autrePatient->email) {
            if (strtolower($this->email) == strtolower($autrePatient->email)) {
                $score += 10;
            }
            $total += 10;
        }
        
        return $total > 0 ? intval($score / $total * 100) : 0;
    }
}