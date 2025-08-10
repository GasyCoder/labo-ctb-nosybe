<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\ResultatPdfService;

class ResultatController extends Controller
{
    public function generatePdf(Prescription $prescription)
    {
        $pdfService = new ResultatPdfService();
        $data = $pdfService->prepareDataForPdf($prescription);
        
        $pdf = Pdf::loadView('pdf.resultats-analyses', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true
            ]);
        
        $filename = "resultats-{$prescription->patient->nom}-{$prescription->id}.pdf";
        
        return $pdf->download($filename);
        // ou return $pdf->stream($filename); pour afficher dans le navigateur
    }
}