<?php

namespace App\Services;

use App\Models\Prescription;
use App\Models\Resultat;
use App\Models\Antibiogramme;
use App\Models\Bacterie;

class ResultatPdfService
{
    /**
     * ✅ MÉTHODE PRINCIPALE : Préparer toutes les données pour le PDF
     */
    public function prepareDataForPdf(Prescription $prescription): array
    {
        $resultats = $prescription->resultats()
            ->with(['analyse.type', 'analyse.examen', 'validatedBy'])
            ->orderBy('created_at')
            ->get();

        $data = [
            'prescription' => $prescription,
            'patient' => $prescription->patient,
            'analyses' => [],
            'antibiogrammes' => [],
            'metadonnees' => $this->getMetadonnees($prescription),
        ];

        foreach ($resultats as $resultat) {
            $analyse = $resultat->analyse;
            $type = strtoupper($analyse->type->name ?? '');

            // ✅ Traitement spécial pour GERME/CULTURE
            if (in_array($type, ['GERME', 'CULTURE'])) {
                $analyseData = $this->formatGermeCulture($resultat);
                
                // Récupérer les antibiogrammes associés
                $antibiogrammes = $this->getAntibiogrammesForAnalyse($prescription, $analyse->id);
                if (!empty($antibiogrammes)) {
                    $data['antibiogrammes'][$analyse->id] = $antibiogrammes;
                }
            } else {
                // ✅ Traitement pour autres types d'analyses
                $analyseData = $this->formatAutreAnalyse($resultat);
            }

            $data['analyses'][] = $analyseData;
        }

        return $data;
    }

    /**
     * ✅ Formatage spécial pour GERME/CULTURE
     */
    private function formatGermeCulture(Resultat $resultat): array
    {
        $analyse = $resultat->analyse;
        $resultatsData = $resultat->resultats; // Déjà un array grâce au cast

        $optionsStandards = ['non-rechercher', 'en-cours', 'culture-sterile', 'absence-germe-pathogene', 'Autre'];
        $bacterieIds = [];
        $optionsSelectionnees = [];

        if (is_array($resultatsData)) {
            foreach ($resultatsData as $option) {
                if (in_array($option, $optionsStandards)) {
                    $optionsSelectionnees[] = $this->formatOptionStandard($option);
                } elseif (str_starts_with($option, 'bacterie-')) {
                    $bacterieIds[] = (int) str_replace('bacterie-', '', $option);
                }
            }
        }

        // Récupérer les noms des bactéries
        $bacteries = [];
        if (!empty($bacterieIds)) {
            $bacteries = Bacterie::whereIn('id', $bacterieIds)
                ->with('famille')
                ->get()
                ->map(function ($bacterie) {
                    return [
                        'id' => $bacterie->id,
                        'nom' => $bacterie->designation,
                        'famille' => $bacterie->famille->designation ?? 'Non classée',
                    ];
                })
                ->toArray();
        }

        return [
            'id' => $analyse->id,
            'code' => $analyse->code,
            'designation' => $analyse->designation,
            'type' => 'GERME/CULTURE',
            'options_standards' => $optionsSelectionnees,
            'bacteries' => $bacteries,
            'autre_valeur' => $resultat->valeur,
            'interpretation' => $resultat->interpretation,
            'conclusion' => $resultat->conclusion,
            'status' => $resultat->status,
            'validated_at' => $resultat->validated_at,
            'validated_by' => $resultat->validatedBy?->name,
        ];
    }

    /**
     * ✅ Formatage pour autres types d'analyses
     */
    private function formatAutreAnalyse(Resultat $resultat): array
    {
        $analyse = $resultat->analyse;
        
        return [
            'id' => $analyse->id,
            'code' => $analyse->code,
            'designation' => $analyse->designation,
            'type' => $analyse->type->name ?? 'STANDARD',
            'valeur' => $this->formatValeur($resultat),
            'resultats' => $this->formatResultats($resultat),
            'unite' => $analyse->unite,
            'valeur_ref' => $analyse->valeur_ref,
            'interpretation' => $resultat->interpretation,
            'conclusion' => $resultat->conclusion,
            'status' => $resultat->status,
            'validated_at' => $resultat->validated_at,
            'validated_by' => $resultat->validatedBy?->name,
        ];
    }

    /**
     * ✅ Récupérer les antibiogrammes pour une analyse
     */
    private function getAntibiogrammesForAnalyse(Prescription $prescription, int $analyseId): array
    {
        $antibiogrammes = Antibiogramme::where('prescription_id', $prescription->id)
            ->where('analyse_id', $analyseId)
            ->with(['bacterie.famille', 'resultatAntibiotiques.antibiotique'])
            ->get();

        $result = [];
        foreach ($antibiogrammes as $antibiogramme) {
            $antibiotiques = [];
            foreach ($antibiogramme->resultatAntibiotiques as $resultatAb) {
                $antibiotiques[] = [
                    'nom' => $resultatAb->antibiotique->designation,
                    'interpretation' => $resultatAb->interpretation,
                    'diametre' => $resultatAb->diametre_mm,
                    'couleur' => $this->getInterpretationColor($resultatAb->interpretation),
                ];
            }

            $result[] = [
                'bacterie' => [
                    'nom' => $antibiogramme->bacterie->designation,
                    'famille' => $antibiogramme->bacterie->famille->designation ?? 'Non classée',
                ],
                'antibiotiques' => $antibiotiques,
                'notes' => $antibiogramme->notes,
            ];
        }

        return $result;
    }

    /**
     * ✅ Utilitaires de formatage
     */
    private function formatOptionStandard(string $option): string
    {
        return match($option) {
            'non-rechercher' => 'Non recherché',
            'en-cours' => 'En cours',
            'culture-sterile' => 'Culture stérile',
            'absence-germe-pathogene' => 'Absence de germe pathogène',
            'Autre' => 'Autre',
            default => $option,
        };
    }

    private function formatValeur(Resultat $resultat): ?string
    {
        if (!$resultat->valeur) return null;
        
        // Si c'est du JSON, essayer de le décoder
        if (is_string($resultat->valeur) && str_starts_with($resultat->valeur, '{')) {
            $decoded = json_decode($resultat->valeur, true);
            if ($decoded && is_array($decoded)) {
                // Pour LEUCOCYTES par exemple
                if (isset($decoded['polynucleaires']) || isset($decoded['lymphocytes'])) {
                    $parts = [];
                    if ($decoded['polynucleaires']) $parts[] = "Polynucléaires: {$decoded['polynucleaires']}%";
                    if ($decoded['lymphocytes']) $parts[] = "Lymphocytes: {$decoded['lymphocytes']}%";
                    if ($decoded['valeur']) $parts[] = "Valeur: {$decoded['valeur']}";
                    return implode(' | ', $parts);
                }
            }
        }
        
        return $resultat->valeur;
    }

    private function formatResultats(Resultat $resultat): ?string
    {
        if (!$resultat->resultats) return null;
        
        if (is_array($resultat->resultats)) {
            return implode(', ', $resultat->resultats);
        }
        
        return $resultat->resultats;
    }

    private function getInterpretationColor(string $interpretation): string
    {
        return match($interpretation) {
            'S' => 'green',
            'I' => 'orange', 
            'R' => 'red',
            default => 'gray',
        };
    }

    private function getMetadonnees(Prescription $prescription): array
    {
        return [
            'date_generation' => now(),
            'total_analyses' => $prescription->analyses()->count(),
            'analyses_terminees' => $prescription->resultats()->where('status', 'TERMINE')->count(),
            'analyses_validees' => $prescription->resultats()->where('status', 'VALIDE')->count(),
        ];
    }
}