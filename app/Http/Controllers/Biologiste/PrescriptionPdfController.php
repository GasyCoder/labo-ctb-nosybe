<?php

namespace App\Http\Controllers\Biologiste;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Services\ResultatPdfShow;
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
     * Générer et afficher le PDF des résultats
     */
    public function show(Prescription $prescription)
    {
        try {
            // Vérifier que la prescription est validée
            if ($prescription->status !== Prescription::STATUS_VALIDE) {
                return redirect()->back()->with('error', 'Cette prescription n\'est pas encore validée.');
            }

            $pdfUrl = $this->pdfService->generatePDF($prescription);

            // Rediriger vers le PDF
            return redirect($pdfUrl);

        } catch (\Exception $e) {
            Log::error('Erreur génération PDF:', [
                'prescription_id' => $prescription->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->back()->with('error', 'Erreur lors de la génération du PDF.');
        }
    }

    /**
     * Télécharger le PDF des résultats
     */
    public function downloadPdf(Prescription $prescription)
    {
        try {
            if ($prescription->status !== Prescription::STATUS_VALIDE) {
                return redirect()->back()->with('error', 'Cette prescription n\'est pas encore validée.');
            }

            $pdfUrl = $this->pdfService->generatePDF($prescription);
            
            // Extraire le nom du fichier de l'URL
            $filename = 'resultats-' . $prescription->reference . '.pdf';

            $filePath = storage_path('app/public/pdfs/' . basename($pdfUrl));
            
            if (!file_exists($filePath)) {
                throw new \Exception('Fichier PDF non trouvé');
            }

            return response()->download($filePath, $filename);

        } catch (\Exception $e) {
            Log::error('Erreur téléchargement PDF:', [
                'prescription_id' => $prescription->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->back()->with('error', 'Erreur lors du téléchargement du PDF.');
        }
    }
}