<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;

class FactureController extends Controller
{
    public function show(Prescription $prescription)
    {
        return view('livewire.secretaire.prescription.facture-impression', [
            'prescription' => $prescription
        ]);
    }
}