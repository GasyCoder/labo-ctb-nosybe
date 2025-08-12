<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;

class BiologistePrescriptionController extends Controller
{
    public function show(Prescription $prescription)
    {
        return view('biologiste.prescription-show', [
            'prescription' => $prescription->load([
                'patient',
                'prescripteur',
                'analyses.parent',
                'analyses.examen',
                'analyses.type',
                'resultats'
            ])
        ]);
    }

    public function validate(Prescription $prescription)
    {
        $prescription->update(['status' => 'TERMINE']);
        return redirect()->route('biologiste.index')
            ->with('success', 'Prescription validée avec succès');
    }
}