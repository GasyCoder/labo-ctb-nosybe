<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class ResultatController extends Controller
{
    /**
     * Générer le PDF des résultats d'analyse - VERSION OPTIMISÉE
     */
    public function generatePdf(Request $request, Prescription $prescription)
    {
        try {
            // ✅ CHARGEMENT OPTIMISÉ avec relations nécessaires uniquement
            $prescription = $this->chargerDonneesCompletes($prescription);

            if (!$prescription) {
                abort(404, 'Prescription non trouvée');
            }

            // ✅ VALIDATION des résultats disponibles
            if ($prescription->resultats->isEmpty()) {
                return back()->with('error', 'Aucun résultat disponible pour cette prescription');
            }

            // ✅ ORGANISATION OPTIMISÉE des résultats
            $donneesOrganisees = $this->preparerDonneesPourPdf($prescription);

            // ✅ GÉNÉRATION PDF avec options optimisées
            $pdf = Pdf::loadView('pdf.resultats-analyse', $donneesOrganisees)
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'dpi' => 150,
                    'defaultFont' => 'Arial',
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'enable_php' => false,
                    'chroot' => public_path(),
                    'debugKeepTemp' => false, // Performance
                ]);

            $fileName = $this->genererNomFichier($prescription);

            // ✅ LOGGING optimisé
            $this->loggerGenerationPdf($prescription);

            return $pdf->stream($fileName, ['Attachment' => false]);

        } catch (\Exception $e) {
            return $this->gererErreur($e, $prescription);
        }
    }

    /**
     * ✅ CHARGEMENT OPTIMISÉ des données avec relations
     */
    private function chargerDonneesCompletes(Prescription $prescription): Prescription
    {
        return $prescription->load([
            // Patient et prescripteur
            'patient:id,numero_dossier,nom,prenom,telephone',
            'prescripteur:id,nom,prenom,grade,specialite',
            
            // Résultats avec leurs relations essentielles
            'resultats' => function ($query) {
                $query->with([
                    'analyse:id,code,designation,unite,valeur_ref,level,parent_id,type_id,examen_id,ordre',
                    'analyse.type:id,libelle,name',
                    'analyse.examen:id,name',
                    'analyse.parent:id,designation,prix',
                    'validatedBy:id,name'
                ])
                ->whereNotNull('valeur') // Seuls les résultats avec valeur
                ->orWhereNotNull('resultats') // Ou avec résultats JSON
                ->orderBy('created_at');
            }
        ]);
    }

    /**
     * ✅ PRÉPARATION OPTIMISÉE des données pour le PDF
     */
    private function preparerDonneesPourPdf(Prescription $prescription): array
    {
        // Organisation des résultats par groupes
        $resultatsGroupes = $this->organiserResultatsOptimise($prescription);
        
        // Informations du biologiste validateur
        $biologiste = $this->obtenirBiologisteValidateur($prescription);
        
        // Dates importantes
        $dates = $this->extraireDatesImportantes($prescription);

        return [
            'prescription' => $prescription,
            'resultatsGroupes' => $resultatsGroupes,
            'biologiste' => $biologiste,
            'dates' => $dates,
            'statistiques' => $this->calculerStatistiques($prescription)
        ];
    }

    /**
     * ✅ ORGANISATION OPTIMISÉE des résultats par type et hiérarchie
     */
    private function organiserResultatsOptimise(Prescription $prescription): Collection
    {
        // Grouper par type d'analyse
        $groupesParType = $prescription->resultats
            ->filter(function ($resultat) {
                // Filtrer les résultats vides
                return !empty($resultat->valeur) || !empty($resultat->resultats);
            })
            ->groupBy(function ($resultat) {
                $analyse = $resultat->analyse;
                if ($analyse && $analyse->type) {
                    return $analyse->type->id;
                }
                return 'autres';
            });

        $resultatsOrganises = collect();

        foreach ($groupesParType as $typeId => $resultats) {
            $nomGroupe = $this->obtenirNomGroupe($typeId, $resultats->first());
            $ordreGroupe = $this->obtenirOrdreGroupe($typeId);
            
            // Organisation hiérarchique optimisée
            $hierarchieOptimisee = $this->construireHierarchieOptimisee($resultats);

            $resultatsOrganises->push([
                'id' => $typeId,
                'nom' => $nomGroupe,
                'ordre' => $ordreGroupe,
                'resultats' => $hierarchieOptimisee,
                'nombre_resultats' => $hierarchieOptimisee->count()
            ]);
        }

        return $resultatsOrganises->sortBy('ordre');
    }

    /**
     * ✅ CONSTRUCTION HIÉRARCHIQUE OPTIMISÉE Parent -> Enfants
     */
    private function construireHierarchieOptimisee(Collection $resultats): Collection
    {
        $hierarchie = collect();
        $analyseTraitees = collect();

        // Trier par ordre et niveau
        $resultatsOrdonnes = $resultats->sortBy([
            ['analyse.ordre', 'asc'],
            ['analyse.level', 'desc'], // PARENT en premier
            ['analyse.id', 'asc']
        ]);

        foreach ($resultatsOrdonnes as $resultat) {
            $analyse = $resultat->analyse;
            
            // Éviter les doublons
            if ($analyseTraitees->contains($analyse->id)) {
                continue;
            }

            if ($analyse->level === 'PARENT') {
                $this->traiterAnalyseParent($hierarchie, $resultat, $resultatsOrdonnes, $analyseTraitees);
            } elseif ($analyse->level === 'CHILD' && !$analyse->parent_id) {
                // Enfant orphelin -> traiter comme autonome
                $this->ajouterAnalyseAutonome($hierarchie, $resultat, $analyseTraitees);
            } elseif (!$analyse->parent_id) {
                // Analyse normale sans parent
                $this->ajouterAnalyseAutonome($hierarchie, $resultat, $analyseTraitees);
            }
        }

        return $hierarchie;
    }

    /**
     * ✅ TRAITEMENT des analyses PARENT avec leurs enfants
     */
    private function traiterAnalyseParent(Collection &$hierarchie, $resultatParent, Collection $tousResultats, Collection &$analyseTraitees): void
    {
        $analyseParent = $resultatParent->analyse;
        
        // Ajouter le parent
        $hierarchie->push([
            'type' => 'parent',
            'niveau' => 1,
            'resultat' => $resultatParent,
            'analyse' => $analyseParent,
            'has_value' => !empty($resultatParent->valeur),
            'formatted_value' => $this->formaterValeur($resultatParent)
        ]);
        
        $analyseTraitees->push($analyseParent->id);

        // Chercher et ajouter les enfants
        $enfants = $tousResultats->filter(function ($resultat) use ($analyseParent) {
            return $resultat->analyse->parent_id === $analyseParent->id;
        })->sortBy('analyse.ordre');

        foreach ($enfants as $enfant) {
            if (!$analyseTraitees->contains($enfant->analyse->id)) {
                $hierarchie->push([
                    'type' => 'enfant',
                    'niveau' => 2,
                    'resultat' => $enfant,
                    'analyse' => $enfant->analyse,
                    'parent_id' => $analyseParent->id,
                    'has_value' => !empty($enfant->valeur),
                    'formatted_value' => $this->formaterValeur($enfant)
                ]);
                
                $analyseTraitees->push($enfant->analyse->id);
            }
        }
    }

    /**
     * ✅ AJOUT d'une analyse autonome
     */
    private function ajouterAnalyseAutonome(Collection &$hierarchie, $resultat, Collection &$analyseTraitees): void
    {
        $hierarchie->push([
            'type' => 'autonome',
            'niveau' => 1,
            'resultat' => $resultat,
            'analyse' => $resultat->analyse,
            'has_value' => !empty($resultat->valeur),
            'formatted_value' => $this->formaterValeur($resultat)
        ]);
        
        $analyseTraitees->push($resultat->analyse->id);
    }

    /**
     * ✅ FORMATAGE optimisé de la valeur du résultat
     */
    private function formaterValeur($resultat): string
    {
        $valeur = '';
        
        // Valeur simple
        if (!empty($resultat->valeur)) {
            $valeur = $resultat->valeur;
        }
        
        // Résultats JSON complexes
        if (!empty($resultat->resultats) && is_array($resultat->resultats)) {
            $elements = [];
            foreach ($resultat->resultats as $cle => $val) {
                $elements[] = "$cle: $val";
            }
            $valeur = implode(', ', $elements);
        }
        
        // Indicateur pathologique
        if ($resultat->interpretation === 'PATHOLOGIQUE' && !empty($valeur)) {
            $valeur .= ' *';
        }
        
        return $valeur ?: '-';
    }

    /**
     * ✅ OBTENTION du nom du groupe
     */
    private function obtenirNomGroupe($typeId, $premierResultat): string
    {
        if ($typeId === 'autres') {
            return 'ANALYSES DIVERSES';
        }
        
        $type = $premierResultat->analyse->type ?? null;
        return $type ? strtoupper($type->libelle ?? $type->name) : 'NON CLASSÉ';
    }

    /**
     * ✅ OBTENTION de l'ordre du groupe
     */
    private function obtenirOrdreGroupe($typeId): int
    {
        return $typeId === 'autres' ? 9999 : (int) $typeId;
    }

    /**
     * ✅ EXTRACTION du biologiste validateur
     */
    private function obtenirBiologisteValidateur(Prescription $prescription): ?object
    {
        $resultatValide = $prescription->resultats
            ->where('status', 'VALIDE')
            ->whereNotNull('validated_by')
            ->first();
            
        return $resultatValide?->validatedBy;
    }

    /**
     * ✅ EXTRACTION des dates importantes
     */
    private function extraireDatesImportantes(Prescription $prescription): array
    {
        $dateValidation = $prescription->resultats
            ->where('status', 'VALIDE')
            ->whereNotNull('validated_at')
            ->max('validated_at');

        return [
            'creation' => $prescription->created_at,
            'validation' => $dateValidation ? \Carbon\Carbon::parse($dateValidation) : null,
        ];
    }

    /**
     * ✅ CALCUL des statistiques
     */
    private function calculerStatistiques(Prescription $prescription): array
    {
        $resultats = $prescription->resultats;
        
        return [
            'total_resultats' => $resultats->count(),
            'resultats_valides' => $resultats->where('status', 'VALIDE')->count(),
            'resultats_normaux' => $resultats->where('interpretation', 'NORMAL')->count(),
            'resultats_pathologiques' => $resultats->where('interpretation', 'PATHOLOGIQUE')->count(),
        ];
    }

    /**
     * ✅ GÉNÉRATION du nom de fichier optimisé
     */
    private function genererNomFichier(Prescription $prescription): string
    {
        $reference = str_replace(['/', '\\', ' '], '_', $prescription->reference);
        $patient = str_replace([' ', '.'], '_', $prescription->patient->nom);
        $date = date('Y-m-d_H-i-s');
        
        return "Resultats_{$reference}_{$patient}_{$date}.pdf";
    }

    /**
     * ✅ LOGGING optimisé
     */
    private function loggerGenerationPdf(Prescription $prescription): void
    {
        Log::info('Génération PDF résultats réussie', [
            'prescription_id' => $prescription->id,
            'reference' => $prescription->reference,
            'patient' => $prescription->patient->nom . ' ' . $prescription->patient->prenom,
            'user_id' => Auth::id(),
            'resultats_count' => $prescription->resultats->count(),
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * ✅ GESTION d'erreur optimisée
     */
    private function gererErreur(\Exception $e, ?Prescription $prescription): \Illuminate\Http\RedirectResponse
    {
        Log::error('Erreur génération PDF résultats', [
            'prescription_id' => $prescription->id ?? 'N/A',
            'reference' => $prescription->reference ?? 'N/A',
            'error_message' => $e->getMessage(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine(),
            'user_id' => Auth::id(),
            'timestamp' => now()->toISOString()
        ]);

        return back()->with('error', 'Erreur lors de la génération du PDF : ' . $e->getMessage());
    }

    /**
     * ✅ APERÇU optimisé des résultats
     */
    public function preview(Request $request, Prescription $prescription)
    {
        try {
            $prescription = $this->chargerDonneesCompletes($prescription);
            
            if (!$prescription) {
                abort(404, 'Prescription non trouvée');
            }

            $donneesOrganisees = $this->preparerDonneesPourPdf($prescription);

            return view('pdf.resultats-analyse', $donneesOrganisees);

        } catch (\Exception $e) {
            return $this->gererErreur($e, $prescription);
        }
    }

    /**
     * ✅ STATISTIQUES optimisées
     */
    public function statistics(Prescription $prescription)
    {
        try {
            $prescription->load([
                'resultats:id,prescription_id,status,interpretation,validated_at,validated_by',
                'resultats.validatedBy:id,name',
                'resultats.analyse:id,examen_id',
                'resultats.analyse.examen:id,name,abr',
                'analyses:id'
            ]);

            $stats = $this->calculerStatistiques($prescription);
            
            // Statistiques par examen
            $statistiquesParExamen = $prescription->resultats
                ->groupBy('analyse.examen.name')
                ->map(function ($resultats, $examenNom) {
                    return [
                        'nom' => $examenNom ?: 'Non classé',
                        'total' => $resultats->count(),
                        'valides' => $resultats->where('status', 'VALIDE')->count(),
                        'pathologiques' => $resultats->where('interpretation', 'PATHOLOGIQUE')->count(),
                    ];
                });
            
            // Informations du biologiste validateur
            $dates = $this->extraireDatesImportantes($prescription);
            $biologiste = $this->obtenirBiologisteValidateur($prescription);
            
            $statsEtendues = array_merge($stats, [
                'total_analyses' => $prescription->analyses->count(),
                'date_derniere_validation' => $dates['validation']?->format('d/m/Y H:i'),
                'biologiste_validateur' => $biologiste?->name,
                'taux_completion' => $prescription->analyses->count() > 0 
                    ? round(($stats['resultats_valides'] / $prescription->analyses->count()) * 100, 2)
                    : 0,
                'statistiques_par_examen' => $statistiquesParExamen
            ]);

            return response()->json($statsEtendues);

        } catch (\Exception $e) {
            Log::error('Erreur statistiques résultats', [
                'prescription_id' => $prescription->id ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Erreur lors du calcul des statistiques'], 500);
        }
    }
}