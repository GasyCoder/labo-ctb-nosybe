<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ResultatController extends Controller
{
    /**
     * Générer le PDF des résultats d'analyse
     */
    public function generatePdf(Request $request, Prescription $prescription)
    {
        try {
            // Charger toutes les relations nécessaires
            $prescription->load([
                'patient',
                'prescripteur',
                'resultats' => function ($query) {
                    $query->with([
                        'analyse' => function ($q) {
                            $q->with(['type', 'examen', 'parent', 'enfants']);
                        },
                        'validatedBy'
                    ])->orderBy('created_at');
                },
                'analyses' => function ($query) {
                    $query->with(['type', 'examen', 'parent', 'enfants'])
                          ->orderBy('ordre')
                          ->orderBy('id');
                }
            ]);

            // Vérifier que la prescription existe et a des résultats
            if (!$prescription) {
                abort(404, 'Prescription non trouvée');
            }

            if ($prescription->resultats->isEmpty()) {
                return back()->with('error', 'Aucun résultat disponible pour cette prescription');
            }

            // Organiser les résultats par type/examen avec hiérarchie parent-enfant
            $resultatsGroupes = $this->organiserResultatsParType($prescription);


            // Configuration PDF
            $pdf = Pdf::loadView('pdf.resultats-analyse', compact('prescription', 'resultatsGroupes'))
                     ->setPaper('a4', 'portrait')
                     ->setOptions([
                         'dpi' => 150,
                         'defaultFont' => 'Arial',
                         'isRemoteEnabled' => true, // Activé pour les images (logo, signature, QR code)
                         'isHtml5ParserEnabled' => true,
                         'enable_php' => false,
                         'chroot' => public_path(),
                     ]);

            $fileName = 'Resultats_' . $prescription->reference . '_' . date('Y-m-d_H-i-s') . '.pdf';

            // Log de génération
            Log::info('Génération PDF résultats', [
                'prescription_id' => $prescription->id,
                'reference' => $prescription->reference,
                'patient' => $prescription->patient->nom . ' ' . $prescription->patient->prenom,
                'user_id' => Auth::id(),
                'resultats_count' => $prescription->resultats->count()
            ]);

            // Diffuser le PDF pour affichage dans une nouvelle fenêtre
            return $pdf->stream($fileName, ['Attachment' => false]);

        } catch (\Exception $e) {
            Log::error('Erreur génération PDF résultats', [
                'prescription_id' => $prescription->id ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Erreur lors de la génération du PDF : ' . $e->getMessage());
        }
    }

    /**
     * Aperçu des résultats (pour biologiste/technicien)
     */
    public function preview(Request $request, Prescription $prescription)
    {
        try {
            // Charger les relations nécessaires
            $prescription->load([
                'patient',
                'prescripteur',
                'resultats.analyse.type',
                'resultats.analyse.examen',
                'resultats.analyse.parent',
                'resultats.validatedBy'
            ]);

            // Vérifier que la prescription existe
            if (!$prescription) {
                abort(404, 'Prescription non trouvée');
            }

            // Organiser les résultats
            $resultatsGroupes = $this->organiserResultatsParType($prescription);

            // Retourner la vue HTML pour aperçu
            return view('pdf.resultats-analyse', compact('prescription', 'resultatsGroupes'));

        } catch (\Exception $e) {
            Log::error('Erreur lors de la prévisualisation des résultats', [
                'prescription_id' => $prescription->id ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Erreur lors de la prévisualisation : ' . $e->getMessage());
        }
    }

    /**
     * Statistiques des résultats pour une prescription
     */
    public function statistics(Prescription $prescription)
    {
        try {
            // Charger les relations nécessaires pour les statistiques
            $prescription->load(['resultats.validatedBy', 'analyses']);

            $stats = [
                'total_analyses' => $prescription->analyses->count(),
                'total_resultats' => $prescription->resultats->count(),
                'resultats_valides' => $prescription->resultats->where('status', 'VALIDE')->count(),
                'resultats_normaux' => $prescription->resultats->where('interpretation', 'NORMAL')->count(),
                'resultats_pathologiques' => $prescription->resultats->where('interpretation', 'PATHOLOGIQUE')->count(),
                'date_derniere_validation' => $prescription->resultats
                    ->where('status', 'VALIDE')
                    ->max('validated_at')
                    ? \Carbon\Carbon::parse($prescription->resultats->where('status', 'VALIDE')->max('validated_at'))->format('d/m/Y H:i')
                    : null,
                'biologiste_validateur' => $prescription->resultats
                    ->where('status', 'VALIDE')
                    ->first()?->validatedBy?->name
            ];

            return response()->json($stats);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des statistiques', [
                'prescription_id' => $prescription->id ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Erreur lors de la récupération des statistiques'], 500);
        }
    }

    /**
     * Organiser les résultats par type/examen avec hiérarchie
     */
    private function organiserResultatsParType(Prescription $prescription)
    {
        $resultatsGroupes = collect();

        // Grouper d'abord par type ou examen
        $groupes = $prescription->resultats->groupBy(function ($resultat) {
            $analyse = $resultat->analyse;

            if ($analyse->type) {
                return [
                    'cle' => 'type_' . $analyse->type->id,
                    'nom' => strtoupper($analyse->type->libelle ?? $analyse->type->name),
                    'ordre' => $analyse->type->id
                ];
            }

            if ($analyse->examen) {
                return [
                    'cle' => 'examen_' . $analyse->examen->id,
                    'nom' => strtoupper($analyse->examen->name),
                    'ordre' => 1000 + $analyse->examen->id
                ];
            }

            return [
                'cle' => 'autres',
                'nom' => 'ANALYSES DIVERSES',
                'ordre' => 9999
            ];
        });

        // Organiser chaque groupe avec hiérarchie parent-enfant
        foreach ($groupes as $groupeInfo => $resultats) {
            $groupeData = is_array($groupeInfo) ? $groupeInfo : [
                'cle' => 'unknown',
                'nom' => 'NON CLASSÉ',
                'ordre' => 10000
            ];

            $resultatsOrganises = $this->organiserHierarchieParentEnfant($resultats);

            $resultatsGroupes->push([
                'nom' => $groupeData['nom'],
                'ordre' => $groupeData['ordre'],
                'resultats' => $resultatsOrganises
            ]);
        }

        // Trier par ordre
        return $resultatsGroupes->sortBy('ordre');
    }

    /**
     * Organiser les résultats avec hiérarchie parent-enfant
     */
    private function organiserHierarchieParentEnfant($resultats)
    {
        $organises = collect();
        $parentsTraites = [];

        foreach ($resultats as $resultat) {
            $analyse = $resultat->analyse;

            // Si c'est un parent
            if ($analyse->level === 'PARENT') {
                if (in_array($analyse->id, $parentsTraites)) {
                    continue;
                }

                $parentsTraites[] = $analyse->id;

                // Ajouter le parent
                $organises->push([
                    'type' => 'parent',
                    'resultat' => $resultat,
                    'analyse' => $analyse
                ]);

                // Chercher et ajouter les enfants
                $enfants = $resultats->filter(function ($r) use ($analyse) {
                    return $r->analyse->parent_id === $analyse->id;
                });

                foreach ($enfants->sortBy('analyse.ordre') as $enfant) {
                    $organises->push([
                        'type' => 'enfant',
                        'resultat' => $enfant,
                        'analyse' => $enfant->analyse,
                        'parent_id' => $analyse->id
                    ]);
                }
            }
            // Si c'est une analyse sans parent (autonome)
            elseif (!$analyse->parent_id) {
                $organises->push([
                    'type' => 'autonome',
                    'resultat' => $resultat,
                    'analyse' => $analyse
                ]);
            }
            // Les enfants sont traités avec leurs parents, on les ignore ici
        }

        return $organises;
    }
}