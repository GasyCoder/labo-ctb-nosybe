<?php

namespace App\Http\Controllers\Biologiste;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Services\ResultatPdfShow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PrescriptionPdfController extends Controller
{
    protected $pdfService;

    public function __construct(ResultatPdfShow $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Générer le PDF selon le type demandé (route principale)
     */
    public function show(Prescription $prescription, Request $request)
    {
        try {
            $type = $request->get('type', 'auto'); // auto, final ou preview
            
            // Si type auto, essayer final d'abord, puis preview
            if ($type === 'auto') {
                $hasValidatedResults = $prescription->resultats()
                    ->where('status', 'VALIDE')
                    ->whereNotNull('validated_by')
                    ->exists();

                $hasAntibiogrammes = \App\Models\Antibiogramme::where('prescription_id', $prescription->id)->exists();

                if ($hasValidatedResults || $hasAntibiogrammes) {
                    return $this->generateFinalPdf($prescription);
                } else {
                    // Fallback vers preview si pas de résultats validés
                    return $this->generatePreviewPdf($prescription);
                }
            }
            
            if ($type === 'preview') {
                return $this->generatePreviewPdf($prescription);
            } else {
                return $this->generateFinalPdf($prescription);
            }

        } catch (\Exception $e) {
            Log::error('Erreur génération PDF biologiste:', [
                'prescription_id' => $prescription->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->back()->with('error', 'Erreur lors de la génération du PDF: ' . $e->getMessage());
        }
    }

    /**
     * Générer l'APERÇU PDF (résultats saisis, pas forcément validés)
     */
    private function generatePreviewPdf(Prescription $prescription)
    {
        $hasAnyResults = $prescription->resultats()
            ->where(function($query) {
                $query->whereNotNull('valeur')
                      ->where('valeur', '!=', '')
                      ->orWhereNotNull('resultats');
            })
            ->exists();

        if (!$hasAnyResults) {
            return redirect()->back()->with('error', 'Aucun résultat saisi trouvé pour cette prescription.');
        }

        $pdfUrl = $this->pdfService->generatePreviewPDF($prescription);
        return redirect($pdfUrl);
    }

    /**
     * Générer le PDF FINAL (uniquement résultats validés)
     */
    private function generateFinalPdf(Prescription $prescription)
    {
        $hasValidatedResults = $prescription->resultats()
            ->where('status', 'VALIDE')
            ->whereNotNull('validated_by')
            ->exists();

        $hasAntibiogrammes = \App\Models\Antibiogramme::where('prescription_id', $prescription->id)->exists();

        if (!$hasValidatedResults && !$hasAntibiogrammes) {
            return redirect()->back()->with('error', 'Aucun résultat validé ou antibiogramme trouvé pour cette prescription.');
        }

        $pdfUrl = $this->pdfService->generateFinalPDF($prescription);
        return redirect($pdfUrl);
    }

    /**
     * Télécharger le PDF final des résultats validés
     */
    public function downloadFinalPdf(Prescription $prescription)
    {
        try {
            if ($prescription->status !== Prescription::STATUS_VALIDE) {
                return redirect()->back()->with('error', 'Cette prescription n\'est pas encore validée.');
            }

            $hasValidatedResults = $prescription->resultats()
                ->where('status', 'VALIDE')
                ->whereNotNull('validated_by')
                ->exists();

            if (!$hasValidatedResults) {
                return redirect()->back()->with('error', 'Aucun résultat validé trouvé.');
            }

            $pdfUrl = $this->pdfService->generateFinalPDF($prescription);
            
            $filename = 'resultats-final-' . $prescription->reference . '.pdf';
            $filePath = storage_path('app/public/pdfs/' . basename($pdfUrl));
            
            if (!file_exists($filePath)) {
                throw new \Exception('Fichier PDF non trouvé');
            }

            return response()->download($filePath, $filename);

        } catch (\Exception $e) {
            Log::error('Erreur téléchargement PDF final:', [
                'prescription_id' => $prescription->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->back()->with('error', 'Erreur lors du téléchargement du PDF.');
        }
    }

    /**
     * Télécharger l'aperçu PDF
     */
    public function downloadPreviewPdf(Prescription $prescription)
    {
        try {
            $hasAnyResults = $prescription->resultats()
                ->where(function($query) {
                    $query->whereNotNull('valeur')
                          ->where('valeur', '!=', '')
                          ->orWhereNotNull('resultats');
                })
                ->exists();

            if (!$hasAnyResults) {
                return redirect()->back()->with('error', 'Aucun résultat saisi trouvé.');
            }

            $pdfUrl = $this->pdfService->generatePreviewPDF($prescription);
            
            $filename = 'apercu-resultats-' . $prescription->reference . '.pdf';
            $filePath = storage_path('app/public/pdfs/' . basename($pdfUrl));
            
            if (!file_exists($filePath) || !is_readable($filePath)) {
                throw new \Exception('Fichier PDF non trouvé ou non accessible');
            }

            return response()->download($filePath, $filename);

        } catch (\Exception $e) {
            Log::error('Erreur téléchargement aperçu PDF:', [
                'prescription_id' => $prescription->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->back()->with('error', 'Erreur lors du téléchargement de l\'aperçu.');
        }
    }
}