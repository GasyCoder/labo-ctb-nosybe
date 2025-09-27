<?php

namespace App\Services;

use App\Models\Analyse;
use App\Models\Patient;
use App\Models\Resultat;
use App\Models\Prescription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class AnterioriteService
{
    /**
     * Calculer et assigner les antériorités pour une prescription
     */
    public function calculerAnteriorites(Prescription $prescription): void
    {
        try {
            $patient = $prescription->patient;
            if (!$patient) {
                Log::warning('Patient introuvable pour la prescription', ['prescription_id' => $prescription->id]);
                return;
            }

            // Récupérer tous les résultats actuels de cette prescription
            $resultatsActuels = Resultat::where('prescription_id', $prescription->id)
                ->with(['analyse.type'])
                ->get();

            foreach ($resultatsActuels as $resultatActuel) {
                $anteriorite = $this->trouverAnteriorite($patient, $resultatActuel, $prescription);
                
                if ($anteriorite) {
                    $resultatActuel->update([
                        'anteriorite' => $anteriorite['valeur'],
                        'anteriorite_date' => $anteriorite['date_object'], // ✅ CORRECTION: Utiliser l'objet Date
                        'anteriorite_prescription_id' => $anteriorite['prescription_id']
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::error('Erreur lors du calcul des antériorités', [
                'prescription_id' => $prescription->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Trouver l'antériorité pour un résultat spécifique
     */
    private function trouverAnteriorite(Patient $patient, Resultat $resultatActuel, Prescription $prescriptionActuelle): ?array
    {
        $analyse = $resultatActuel->analyse;
        if (!$analyse) return null;

        // 1. Recherche directe : même analyse dans prescriptions précédentes
        $anterioriteDirecte = $this->rechercherAnterioriteDirecte($patient, $analyse, $prescriptionActuelle);
        if ($anterioriteDirecte) {
            return $anterioriteDirecte;
        }

        // 2. Recherche dans analyses parents/enfants (ex: HB dans NFS)
        $anterioriteIndirecte = $this->rechercherAnterioriteIndirecte($patient, $analyse, $prescriptionActuelle);
        if ($anterioriteIndirecte) {
            return $anterioriteIndirecte;
        }

        return null;
    }

    /**
     * Recherche directe dans les mêmes analyses
     */
    private function rechercherAnterioriteDirecte(Patient $patient, Analyse $analyse, Prescription $prescriptionActuelle): ?array
    {
        $resultatAnterieur = Resultat::whereHas('prescription', function($query) use ($patient, $prescriptionActuelle) {
                $query->where('patient_id', $patient->id)
                      ->where('id', '!=', $prescriptionActuelle->id)
                      ->whereIn('status', ['VALIDE', 'TERMINE']);
            })
            ->where('analyse_id', $analyse->id)
            ->whereIn('status', ['VALIDE', 'TERMINE'])
            ->where(function($query) {
                $query->whereNotNull('valeur')->where('valeur', '!=', '')
                      ->orWhereNotNull('resultats');
            })
            ->with(['prescription'])
            ->orderBy('created_at', 'desc')
            ->first();

        if ($resultatAnterieur) {
            return [
                'valeur' => $this->formaterValeurAnteriorite($resultatAnterieur),
                'date' => $resultatAnterieur->prescription->created_at->format('d/m/Y'), // ✅ Pour affichage
                'date_object' => $resultatAnterieur->prescription->created_at, // ✅ NOUVEAU: Pour le stockage
                'prescription_id' => $resultatAnterieur->prescription_id
            ];
        }

        return null;
    }

    /**
     * Recherche indirecte dans la hiérarchie des analyses
     */
    private function rechercherAnterioriteIndirecte(Patient $patient, Analyse $analyse, Prescription $prescriptionActuelle): ?array
    {
        // Rechercher dans les analyses parentes ou dans les enfants d'une analyse parente
        $analysesLiees = $this->getAnalysesLiees($analyse);
        
        if ($analysesLiees->isEmpty()) {
            return null;
        }

        $resultatAnterieur = Resultat::whereHas('prescription', function($query) use ($patient, $prescriptionActuelle) {
                $query->where('patient_id', $patient->id)
                      ->where('id', '!=', $prescriptionActuelle->id)
                      ->whereIn('status', ['VALIDE', 'TERMINE']);
            })
            ->whereIn('analyse_id', $analysesLiees->pluck('id'))
            ->whereIn('status', ['VALIDE', 'TERMINE'])
            ->where(function($query) {
                $query->whereNotNull('valeur')->where('valeur', '!=', '')
                      ->orWhereNotNull('resultats');
            })
            ->with(['prescription', 'analyse'])
            ->orderBy('created_at', 'desc')
            ->first();

        if ($resultatAnterieur) {
            return [
                'valeur' => $this->formaterValeurAnteriorite($resultatAnterieur),
                'date' => $resultatAnterieur->prescription->created_at->format('d/m/Y'), // ✅ Pour affichage
                'date_object' => $resultatAnterieur->prescription->created_at, // ✅ NOUVEAU: Pour le stockage
                'prescription_id' => $resultatAnterieur->prescription_id
            ];
        }

        return null;
    }

    /**
     * Récupérer les analyses liées (parent, enfants, fratrie)
     */
    private function getAnalysesLiees(Analyse $analyse): Collection
    {
        $analysesLiees = collect([$analyse->id]);

        // Si l'analyse a un parent, récupérer la fratrie
        if ($analyse->parent_id) {
            $fratrie = Analyse::where('parent_id', $analyse->parent_id)
                ->where('id', '!=', $analyse->id)
                ->pluck('id');
            $analysesLiees = $analysesLiees->merge($fratrie);
            
            // Ajouter aussi le parent
            $analysesLiees->push($analyse->parent_id);
        }

        // Si l'analyse est un parent, récupérer les enfants
        $enfants = Analyse::where('parent_id', $analyse->id)->pluck('id');
        $analysesLiees = $analysesLiees->merge($enfants);

        // Recherche par designation similaire (ex: "Hémoglobine", "HB", "Hb")
        $designationsSimilaires = $this->getDesignationsSimilaires($analyse->designation);
        if (!empty($designationsSimilaires)) {
            $analysesSimilaires = Analyse::whereIn('designation', $designationsSimilaires)
                ->where('id', '!=', $analyse->id)
                ->pluck('id');
            $analysesLiees = $analysesLiees->merge($analysesSimilaires);
        }

        return Analyse::whereIn('id', $analysesLiees->unique())->get();
    }

    /**
     * Récupérer les désignations similaires pour une analyse
     */
    private function getDesignationsSimilaires(string $designation): array
    {
        $designation = strtolower(trim($designation));
        
        // Mapping des analyses courantes
        $mappings = [
            // Hémoglobine
            'hémoglobine' => ['hb', 'hemoglobine', 'hémoglobine'],
            'hb' => ['hémoglobine', 'hemoglobine'],
            'hemoglobine' => ['hb', 'hémoglobine'],
            
            // Créatinine
            'créatinine' => ['creat', 'creatinine', 'créat'],
            'creat' => ['créatinine', 'creatinine'],
            'creatinine' => ['créatinine', 'creat'],
            'créat' => ['créatinine', 'creat', 'creatinine'],
            
            // Glucose
            'glucose' => ['glycémie', 'gly', 'glyc'],
            'glycémie' => ['glucose', 'gly'],
            'gly' => ['glucose', 'glycémie'],
            
            // Cholestérol
            'cholestérol' => ['chol', 'cholesterol'],
            'chol' => ['cholestérol', 'cholesterol'],
            'cholesterol' => ['cholestérol', 'chol'],
            
            // Triglycérides
            'triglycérides' => ['tg', 'triglycerides'],
            'tg' => ['triglycérides', 'triglycerides'],
            'triglycerides' => ['triglycérides', 'tg'],
            
            // Urée
            'urée' => ['uree', 'bun'],
            'uree' => ['urée', 'bun'],
            'bun' => ['urée', 'uree'],
        ];

        return $mappings[$designation] ?? [];
    }

    /**
     * Formater la valeur d'antériorité pour l'affichage
     */
    private function formaterValeurAnteriorite(Resultat $resultat): string
    {
        $valeur = '';

        // Utiliser la méthode existante du modèle si disponible
        if (method_exists($resultat, 'getDisplayValuePdfAttribute')) {
            $valeur = strip_tags($resultat->getDisplayValuePdfAttribute());
        } else {
            // Fallback manuel
            if ($resultat->valeur) {
                $valeur = $resultat->valeur;
            } elseif ($resultat->resultats) {
                if (is_array($resultat->resultats)) {
                    $valeur = implode(', ', $resultat->resultats);
                } else {
                    $valeur = $resultat->resultats;
                }
            }
        }

        // Ajouter l'unité si disponible et pas déjà présente
        if ($valeur && $resultat->analyse && $resultat->analyse->unite) {
            if (!str_contains($valeur, $resultat->analyse->unite)) {
                $valeur .= ' ' . $resultat->analyse->unite;
            }
        }

        return trim($valeur);
    }

    /**
     * Recalculer les antériorités pour un patient donné
     */
    public function recalculerAnteroritesPatient(Patient $patient): void
    {
        $prescriptions = Prescription::where('patient_id', $patient->id)
            ->whereIn('status', ['VALIDE', 'TERMINE'])
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($prescriptions as $prescription) {
            $this->calculerAnteriorites($prescription);
        }
    }

    /**
     * Nettoyer les antériorités invalides
     */
    public function nettoyerAnteriorites(): void
    {
        // Supprimer les antériorités où la prescription d'origine n'existe plus
        DB::table('resultats')
            ->whereNotNull('anteriorite_prescription_id')
            ->whereNotExists(function($query) {
                $query->select('id')
                      ->from('prescriptions')
                      ->whereColumn('prescriptions.id', 'resultats.anteriorite_prescription_id');
            })
            ->update([
                'anteriorite' => null,
                'anteriorite_date' => null,
                'anteriorite_prescription_id' => null
            ]);
    }

    /**
     * Obtenir l'historique complet d'une analyse pour un patient
     */
    public function getHistoriqueAnalyse(Patient $patient, Analyse $analyse, int $limit = 5): Collection
    {
        return Resultat::whereHas('prescription', function($query) use ($patient) {
                $query->where('patient_id', $patient->id)
                      ->whereIn('status', ['VALIDE', 'TERMINE']);
            })
            ->where('analyse_id', $analyse->id)
            ->whereIn('status', ['VALIDE', 'TERMINE'])
            ->where(function($query) {
                $query->whereNotNull('valeur')->where('valeur', '!=', '')
                      ->orWhereNotNull('resultats');
            })
            ->with(['prescription'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}