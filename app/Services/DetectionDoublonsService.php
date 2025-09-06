<?php

namespace App\Services;

use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DetectionDoublonsService
{
    /**
     * Détection avancée de doublons avec scoring intelligent
     */
    public static function detecterDoublonsAvances($nom, $prenom, $dateNaissance = null, $telephone = null, $email = null): Collection
    {
        $nom = self::normaliserTexte($nom);
        $prenom = self::normaliserTexte($prenom);
        
        $similarites = collect();
        
        // 1. CORRESPONDANCE EXACTE (Score 100)
        $exactes = self::rechercherCorrespondanceExacte($nom, $prenom, $dateNaissance);
        foreach ($exactes as $patient) {
            $similarites->push([
                'patient' => $patient,
                'score' => 100,
                'raison' => 'Correspondance parfaite (nom, prénom, date)',
                'type' => 'exact',
                'conflits' => []
            ]);
        }

        // 2. MÊME IDENTITÉ MAIS DATE DIFFÉRENTE (Score 85-95)
        if ($dateNaissance) {
            $memeIdentite = self::rechercherMemeIdentite($nom, $prenom, $dateNaissance);
            foreach ($memeIdentite as $patient) {
                $score = self::calculerScoreDateDifference($dateNaissance, $patient->date_naissance);
                $conflits = self::analyserConflitsDates($dateNaissance, $patient->date_naissance);
                
                $similarites->push([
                    'patient' => $patient,
                    'score' => $score,
                    'raison' => 'Même nom/prénom, date de naissance différente',
                    'type' => 'date_conflit',
                    'conflits' => $conflits
                ]);
            }
        }

        // 3. CORRESPONDANCE TÉLÉPHONE (Score 80-90)
        if ($telephone) {
            $memeTelephone = self::rechercherParTelephone($telephone, $nom, $prenom);
            foreach ($memeTelephone as $patient) {
                $score = self::calculerScoreNomDifferent($nom, $prenom, $patient);
                
                $similarites->push([
                    'patient' => $patient,
                    'score' => $score,
                    'raison' => 'Même numéro de téléphone, nom/prénom différents',
                    'type' => 'telephone_conflit',
                    'conflits' => self::analyserConflitsNom($nom, $prenom, $patient)
                ]);
            }
        }

        // 4. CORRESPONDANCE EMAIL (Score 75-85)
        if ($email) {
            $memeEmail = self::rechercherParEmail($email, $nom, $prenom);
            foreach ($memeEmail as $patient) {
                $score = self::calculerScoreNomDifferent($nom, $prenom, $patient);
                
                $similarites->push([
                    'patient' => $patient,
                    'score' => $score - 5, // Email moins fiable que téléphone
                    'raison' => 'Même adresse email, nom/prénom différents',
                    'type' => 'email_conflit',
                    'conflits' => self::analyserConflitsNom($nom, $prenom, $patient)
                ]);
            }
        }

        // 5. SIMILARITÉ PHONÉTIQUE (Score 60-75)
        $phonetiques = self::rechercherSimilaritesPhonetiques($nom, $prenom, $dateNaissance);
        foreach ($phonetiques as $patient) {
            $score = self::calculerScorePhonetiqueAvance($nom, $prenom, $patient, $dateNaissance);
            
            $similarites->push([
                'patient' => $patient,
                'score' => $score,
                'raison' => 'Similarité phonétique du nom ou prénom',
                'type' => 'phonetique',
                'conflits' => self::analyserDifferencesPhonetiiques($nom, $prenom, $patient)
            ]);
        }

        // 6. FAUTES DE FRAPPE COURANTES (Score 50-70)
        $fautesFrappe = self::rechercherFautesFrappe($nom, $prenom);
        foreach ($fautesFrappe as $patient) {
            $score = self::calculerScoreFautesFrappe($nom, $prenom, $patient);
            
            $similarites->push([
                'patient' => $patient,
                'score' => $score,
                'raison' => 'Possibles fautes de frappe dans le nom/prénom',
                'type' => 'faute_frappe',
                'conflits' => self::analyserFautesFrappe($nom, $prenom, $patient)
            ]);
        }

        // Éliminer les doublons et trier par score
        return $similarites
            ->unique(fn($item) => $item['patient']->id)
            ->sortByDesc('score')
            ->values();
    }

    /**
     * Normaliser le texte pour les comparaisons
     */
    private static function normaliserTexte($texte): string
    {
        if (!$texte) return '';
        
        // Supprimer accents, mettre en majuscules, supprimer espaces multiples
        $texte = mb_strtoupper($texte, 'UTF-8');
        $texte = str_replace(
            ['À','Á','Â','Ã','Ä','Å','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ñ','Ò','Ó','Ô','Õ','Ö','Ù','Ú','Û','Ü','Ý'],
            ['A','A','A','A','A','A','C','E','E','E','E','I','I','I','I','N','O','O','O','O','O','U','U','U','U','Y'],
            $texte
        );
        
        // Garder seulement lettres, espaces et tirets
        $texte = preg_replace('/[^A-Z\s\-]/', '', $texte);
        $texte = preg_replace('/\s+/', ' ', $texte);
        
        return trim($texte);
    }

    /**
     * Rechercher correspondance exacte
     */
    private static function rechercherCorrespondanceExacte($nom, $prenom, $dateNaissance): Collection
    {
        $query = Patient::whereRaw('UPPER(REPLACE(nom, " ", "")) = ?', [str_replace(' ', '', $nom)])
                       ->whereRaw('UPPER(REPLACE(prenom, " ", "")) = ?', [str_replace(' ', '', $prenom)]);
        
        if ($dateNaissance) {
            $query->where('date_naissance', $dateNaissance);
        }
        
        return $query->get();
    }

    /**
     * Rechercher même identité avec date différente
     */
    private static function rechercherMemeIdentite($nom, $prenom, $dateNaissance): Collection
    {
        return Patient::whereRaw('UPPER(REPLACE(nom, " ", "")) = ?', [str_replace(' ', '', $nom)])
                     ->whereRaw('UPPER(REPLACE(prenom, " ", "")) = ?', [str_replace(' ', '', $prenom)])
                     ->where('date_naissance', '!=', $dateNaissance)
                     ->whereNotNull('date_naissance')
                     ->get();
    }

    /**
     * Rechercher par téléphone
     */
    private static function rechercherParTelephone($telephone, $nom, $prenom): Collection
    {
        $telephoneNormalise = preg_replace('/[^0-9]/', '', $telephone);
        
        return Patient::where('telephone', 'like', "%{$telephoneNormalise}%")
                     ->where(function($query) use ($nom, $prenom) {
                         $query->whereRaw('UPPER(nom) != ?', [$nom])
                               ->orWhereRaw('UPPER(prenom) != ?', [$prenom]);
                     })
                     ->get();
    }

    /**
     * Rechercher par email
     */
    private static function rechercherParEmail($email, $nom, $prenom): Collection
    {
        return Patient::where('email', strtolower($email))
                     ->where(function($query) use ($nom, $prenom) {
                         $query->whereRaw('UPPER(nom) != ?', [$nom])
                               ->orWhereRaw('UPPER(prenom) != ?', [$prenom]);
                     })
                     ->get();
    }

    /**
     * Rechercher similarités phonétiques avancées
     */
    private static function rechercherSimilaritesPhonetiques($nom, $prenom, $dateNaissance): Collection
    {
        // Utiliser SOUNDEX et Levenshtein pour détecter les similarités
        $patients = Patient::where(function($query) use ($nom, $prenom) {
            $query->whereRaw('SOUNDEX(nom) = SOUNDEX(?)', [$nom])
                  ->orWhereRaw('SOUNDEX(prenom) = SOUNDEX(?)', [$prenom])
                  ->orWhereRaw('LEVENSHTEIN(UPPER(nom), ?) <= 2', [$nom])
                  ->orWhereRaw('LEVENSHTEIN(UPPER(prenom), ?) <= 2', [$prenom]);
        })
        ->where(function($query) use ($nom, $prenom) {
            $query->whereRaw('UPPER(nom) != ?', [$nom])
                  ->orWhereRaw('UPPER(prenom) != ?', [$prenom]);
        })
        ->get();

        return $patients;
    }

    /**
     * Rechercher fautes de frappe courantes
     */
    private static function rechercherFautesFrappe($nom, $prenom): Collection
    {
        $variantes = [];
        
        // Générer des variantes communes pour le nom
        $variantes = array_merge($variantes, self::genererVariantesFrappe($nom));
        
        // Générer des variantes communes pour le prénom
        $variantes = array_merge($variantes, self::genererVariantesFrappe($prenom));
        
        if (empty($variantes)) {
            return collect();
        }
        
        return Patient::where(function($query) use ($variantes, $nom, $prenom) {
            foreach ($variantes as $variante) {
                $query->orWhereRaw('UPPER(nom) LIKE ?', ["%{$variante}%"])
                      ->orWhereRaw('UPPER(prenom) LIKE ?', ["%{$variante}%"]);
            }
        })
        ->where(function($query) use ($nom, $prenom) {
            $query->whereRaw('UPPER(nom) != ?', [$nom])
                  ->orWhereRaw('UPPER(prenom) != ?', [$prenom]);
        })
        ->limit(10)
        ->get();
    }

    /**
     * Générer des variantes de fautes de frappe
     */
    private static function genererVariantesFrappe($texte): array
    {
        if (strlen($texte) < 3) return [];
        
        $variantes = [];
        
        // Substitutions courantes
        $substitutions = [
            'C' => 'K', 'K' => 'C', 'S' => 'Z', 'Z' => 'S',
            'F' => 'PH', 'PH' => 'F', 'Y' => 'I', 'I' => 'Y'
        ];
        
        foreach ($substitutions as $original => $remplacement) {
            if (strpos($texte, $original) !== false) {
                $variantes[] = str_replace($original, $remplacement, $texte);
            }
        }
        
        return $variantes;
    }

    /**
     * Calculer score pour différence de date
     */
    private static function calculerScoreDateDifference($date1, $date2): int
    {
        if (!$date1 || !$date2) return 70;
        
        $diff = abs(Carbon::parse($date1)->diffInDays(Carbon::parse($date2)));
        
        if ($diff <= 1) return 95; // Erreur de 1 jour
        if ($diff <= 7) return 90; // Erreur d'une semaine
        if ($diff <= 30) return 85; // Erreur d'un mois
        if ($diff <= 365) return 80; // Erreur d'une année
        
        return 70;
    }

    /**
     * Calculer score pour nom différent
     */
    private static function calculerScoreNomDifferent($nom, $prenom, $patient): int
    {
        $scoreNom = similar_text(strtoupper($nom), strtoupper($patient->nom), $percentNom);
        $scorePrenom = similar_text(strtoupper($prenom), strtoupper($patient->prenom), $percentPrenom);
        
        return intval(($percentNom + $percentPrenom) / 2);
    }

    /**
     * Calculer score phonétique avancé
     */
    private static function calculerScorePhonetiqueAvance($nom, $prenom, $patient, $dateNaissance): int
    {
        $score = 60; // Score de base
        
        // Bonus si même date de naissance
        if ($dateNaissance && $patient->date_naissance == $dateNaissance) {
            $score += 15;
        }
        
        // Calculer similarité phonétique
        if (soundex($nom) === soundex($patient->nom)) {
            $score += 10;
        }
        
        if (soundex($prenom) === soundex($patient->prenom)) {
            $score += 10;
        }
        
        return min($score, 75);
    }

    /**
     * Calculer score pour fautes de frappe
     */
    private static function calculerScoreFautesFrappe($nom, $prenom, $patient): int
    {
        // Utiliser la distance de Levenshtein pour évaluer la similarité
        $distanceNom = levenshtein(strtoupper($nom), strtoupper($patient->nom));
        $distancePrenom = levenshtein(strtoupper($prenom), strtoupper($patient->prenom));
        
        $scoreNom = max(0, 70 - ($distanceNom * 10));
        $scorePrenom = max(0, 70 - ($distancePrenom * 10));
        
        return intval(($scoreNom + $scorePrenom) / 2);
    }

    /**
     * Analyser conflits de dates
     */
    private static function analyserConflitsDates($date1, $date2): array
    {
        if (!$date1 || !$date2) return ['Date manquante'];
        
        $carbon1 = Carbon::parse($date1);
        $carbon2 = Carbon::parse($date2);
        $diff = $carbon1->diffInDays($carbon2);
        
        $conflits = [];
        
        if ($diff <= 1) {
            $conflits[] = 'Différence de 1 jour (erreur de saisie possible)';
        } elseif ($diff <= 31) {
            $conflits[] = "Différence de {$diff} jours (même mois/année ?)";
        } elseif ($carbon1->year === $carbon2->year) {
            $conflits[] = 'Même année, mois différent';
        } else {
            $conflits[] = 'Années différentes (' . abs($carbon1->year - $carbon2->year) . ' ans d\'écart)';
        }
        
        return $conflits;
    }

    /**
     * Analyser conflits de nom
     */
    private static function analyserConflitsNom($nom, $prenom, $patient): array
    {
        $conflits = [];
        
        if (strtoupper($nom) !== strtoupper($patient->nom)) {
            $conflits[] = "Nom différent: {$nom} vs {$patient->nom}";
        }
        
        if (strtoupper($prenom) !== strtoupper($patient->prenom)) {
            $conflits[] = "Prénom différent: {$prenom} vs {$patient->prenom}";
        }
        
        return $conflits;
    }

    /**
     * Analyser différences phonétiques
     */
    private static function analyserDifferencesPhonetiiques($nom, $prenom, $patient): array
    {
        $differences = [];
        
        if (soundex($nom) === soundex($patient->nom) && $nom !== $patient->nom) {
            $differences[] = "Noms phonétiquement similaires: {$nom} ≈ {$patient->nom}";
        }
        
        if (soundex($prenom) === soundex($patient->prenom) && $prenom !== $patient->prenom) {
            $differences[] = "Prénoms phonétiquement similaires: {$prenom} ≈ {$patient->prenom}";
        }
        
        return $differences;
    }

    /**
     * Analyser fautes de frappe
     */
    private static function analyserFautesFrappe($nom, $prenom, $patient): array
    {
        $fautes = [];
        
        $distanceNom = levenshtein(strtoupper($nom), strtoupper($patient->nom));
        $distancePrenom = levenshtein(strtoupper($prenom), strtoupper($patient->prenom));
        
        if ($distanceNom <= 2 && $distanceNom > 0) {
            $fautes[] = "Nom possiblement mal saisi: {$nom} vs {$patient->nom}";
        }
        
        if ($distancePrenom <= 2 && $distancePrenom > 0) {
            $fautes[] = "Prénom possiblement mal saisi: {$prenom} vs {$patient->prenom}";
        }
        
        return $fautes;
    }

    /**
     * Recommandations basées sur l'analyse
     */
    public static function genererRecommandations($similarites): array
    {
        $recommendations = [];
        
        $scoreMax = $similarites->max('score') ?? 0;
        
        if ($scoreMax >= 95) {
            $recommendations[] = [
                'niveau' => 'CRITIQUE',
                'message' => 'Patient probablement déjà existant - Vérification obligatoire',
                'action' => 'BLOQUER_CREATION'
            ];
        } elseif ($scoreMax >= 80) {
            $recommendations[] = [
                'niveau' => 'ATTENTION',
                'message' => 'Forte probabilité de doublon - Vérification recommandée',
                'action' => 'VERIFICATION_OBLIGATOIRE'
            ];
        } elseif ($scoreMax >= 60) {
            $recommendations[] = [
                'niveau' => 'INFO',
                'message' => 'Patients similaires trouvés - Vérification conseillée',
                'action' => 'VERIFICATION_OPTIONNELLE'
            ];
        }
        
        return $recommendations;
    }
}