{{-- resources/views/factures/ccare-style.blade.php --}}
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Facture - {{ $prescription->reference }}</title>
    <style>
        @page { margin: 15mm 10mm; size: A4; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 9pt; line-height: 1.2; color: #000; }
        .container { max-width: 100%; margin: 0 auto; padding: 5px; }
        .main-header { display: table; width: 100%; margin-bottom: 10px; }
        .header-left, .header-center, .header-right { display: table-cell; vertical-align: top; padding: 8px; }
        .header-left { width: 30%; } .header-center { width: 40%; text-align: center; } .header-right { width: 30%; }
        .logo { max-width: 150px; max-height: 100px; object-fit: contain; }
        .facture-title { font-size: 16pt; font-weight: bold; text-transform: uppercase; margin-bottom: 8px; }
        .facture-info { font-size: 8pt; text-align: center; }
        .barcode-section { text-align: center; font-size: 7pt; }
        .patient-table, .analyses-table, .paiement-table, .totaux-table { width: 100%; border-collapse: collapse; font-size: 8pt; }
        .patient-table td { padding: 4px 6px; border-bottom: 1px dotted #000; }
        .patient-label { font-weight: bold; width: 120px; background-color: #f9f9f9; }
        .analyses-table th, .analyses-table td { border: 1px solid #000; padding: 3px 2px; text-align: center; }
        .analyses-table th { background-color: #f0f0f0; font-weight: bold; font-size: 7pt; }
        .designation-cell { text-align: left !important; padding-left: 4px; }
        .montant-cell { text-align: right !important; padding-right: 4px; font-weight: bold; }
        .totaux-label { text-align: right; font-weight: bold; } .totaux-value { text-align: right; width: 80px; }
        .paiement-header { background-color: #f0f0f0; font-weight: bold; text-align: center; padding: 3px; border-bottom: 1px solid #000; }
        .paiement-table td { border: 1px solid #000; padding: 2px 5px; }
        .resume-section { display: table; width: 100%; margin: 10px 0; font-size: 8pt; }
        .resume-left, .resume-right { display: table-cell; width: 50%; }
        .cachet-paye { border: 2px solid #000; padding: 15px; text-align: center; font-weight: bold; font-size: 12pt; transform: rotate(-15deg); margin: 10px auto; width: 100px; }
        .cachet-paye.paye { color: #008000; border-color: #008000; } .cachet-paye.non-paye { color: #ff0000; border-color: #ff0000; }
        .footer { margin-top: 15px; padding-top: 8px; border-top: 1px solid #000; font-size: 7pt; text-align: center; }
    </style>
</head>

<body>
@php
    $settings = \App\Models\Setting::first();
    $nomEntreprise = $settings->nom_entreprise ?? 'LABORATOIRE CTB';
    $nifEntreprise = $settings->nif ?? '2000000000';
    $statutEntreprise = $settings->statut ?? '72102 11 2010 010000';
    $formatArgent = $settings->format_unite_argent ?? 'Ar';

    $paiement = $prescription->paiements->first();
    $montantPaye = $paiement->montant ?? 0;
    $totalAnalyses = $prescription->analyses->sum('prix');
    $totalPrelevements = $prescription->prelevements->sum(fn($p) => ($p->prix ?? 0) * ($p->quantite ?? 1));
    $remise = $prescription->remise ?? 0;
    $sousTotal = $totalAnalyses + $totalPrelevements;
    $total = max(0, $sousTotal - $remise);
    $solde = max(0, $total - $montantPaye);
@endphp

<div class="container">

    {{-- EN-TÊTE --}}
    <div class="main-header">
        <div class="header-left">
            @php
                if ($settings && $settings->logo) {
                    $logoPath = storage_path('app/public/' . $settings->logo);
                    $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;
                } else {
                    $logoPath = public_path('assets/images/logo_facture.jpg');
                    $logoBase64 = file_exists($logoPath) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath)) : null;
                }
            @endphp
            @if ($logoBase64)
                <img src="{{ $logoBase64 }}" class="logo">
            @else
                <div style="width:150px;height:100px;background:#2E8B57;color:#fff;text-align:center;line-height:100px;font-weight:bold;">{{ strtoupper(substr($nomEntreprise,0,4)) }}</div>
            @endif
        </div>
        <div class="header-center">
            <div class="facture-title">FACTURE</div>
            <div class="facture-info">
                <div><strong>Du:</strong> {{ $prescription->created_at->format('d/m/Y H:i') }}</div>
                <div><strong>N°:</strong> {{ $prescription->reference }}</div>
            </div>
        </div>
        <div class="header-right barcode-section">
            @php
                try {
                    $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                    $barcodeBase64 = 'data:image/png;base64,' . base64_encode($generator->getBarcode($prescription->reference,$generator::TYPE_CODE_128,2,40));
                } catch (Exception $e) { $barcodeBase64 = null; }
            @endphp
            @if ($barcodeBase64)
                <img src="{{ $barcodeBase64 }}" style="max-width:120px;height:40px;margin:0 auto;">
            @else
                <div style="border:1px solid #000;padding:5px;font-family:monospace;">{{ $prescription->reference }}</div>
            @endif
            <div>{{ $prescription->reference }}</div>
        </div>
    </div>

    {{-- PATIENT --}}
    <table class="patient-table">
        <tr><td class="patient-label">PID :</td><td>{{ $prescription->patient->numero_dossier ?? $prescription->reference }}</td><td></td><td class="patient-label">STAT :</td><td>{{ $prescription->status }}</td></tr>
        <tr><td class="patient-label">Visit ID :</td><td>{{ $prescription->reference }}</td><td></td><td class="patient-label">Panel :</td><td>EXTERNE</td></tr>
        <tr><td class="patient-label">Nom du patient :</td><td colspan="4">{{ strtoupper(($prescription->patient->civilite ?? '').' '.$prescription->patient->nom.' '.($prescription->patient->prenom ?? '')) }}</td></tr>
        <tr><td class="patient-label">DDN/Sexe :</td><td>{{ $prescription->age }} {{ $prescription->unite_age ?? 'ans' }}</td><td></td><td class="patient-label">Type client :</td><td>{{ $prescription->patient_type ?? 'EXTERNE' }}</td></tr>
        <tr><td class="patient-label">Téléphone :</td><td>{{ $prescription->patient->telephone ?? '' }}</td><td></td><td class="patient-label">Référé par :</td><td>{{ $prescription->prescripteur->nom ?? '' }} {{ $prescription->prescripteur->prenom ?? '' }}</td></tr>
        <tr><td class="patient-label">Adresse :</td><td colspan="4">{{ $prescription->patient->adresse ?? '' }}</td></tr>
    </table>

    {{-- ANALYSES + PRELEVEMENTS --}}
    <table class="analyses-table">
        <thead>
            <tr><th>N°</th><th>Détails</th><th>Unités</th><th>Tarif ({{ $formatArgent }})</th><th>Remise</th><th>Montant</th></tr>
        </thead>
        <tbody>
            @foreach($prescription->analyses as $analyse)
            <tr><td>{{ $loop->iteration }}</td><td class="designation-cell">{{ $analyse->designation }}</td><td>1.00</td><td class="montant-cell">{{ number_format($analyse->prix,2) }}</td><td class="montant-cell">0.00</td><td class="montant-cell">{{ number_format($analyse->prix,2) }}</td></tr>
            @endforeach
            @foreach($prescription->prelevements as $prelevement)
            @php $montant=$prelevement->prix*$prelevement->quantite; @endphp
            <tr><td>{{ count($prescription->analyses)+$loop->iteration }}</td><td class="designation-cell">{{ $prelevement->nom ?? $prelevement->denomination }}</td><td>{{ $prelevement->quantite }}.00</td><td class="montant-cell">{{ number_format($prelevement->prix,2) }}</td><td class="montant-cell">0.00</td><td class="montant-cell">{{ number_format($montant,2) }}</td></tr>
            @endforeach
        </tbody>
    </table>

    {{-- TOTAUX --}}
    <table class="totaux-table">
        <tr><td class="totaux-label">Sous-total :</td><td class="totaux-value">{{ number_format($sousTotal,2) }}</td></tr>
        <tr><td class="totaux-label">Remise :</td><td class="totaux-value">{{ number_format($remise,2) }}</td></tr>
        <tr><td class="totaux-label"><strong>Total à payer :</strong></td><td class="totaux-value"><strong>{{ number_format($total,2) }}</strong></td></tr>
        <tr><td class="totaux-label">Montant payé :</td><td class="totaux-value">{{ number_format($montantPaye,2) }}</td></tr>
        <tr><td class="totaux-label">Solde :</td><td class="totaux-value">{{ number_format($solde,2) }}</td></tr>
    </table>

    {{-- PAIEMENT --}}
    <div class="paiement-header">Informations de paiement</div>
    <table class="paiement-table">
        <tr><td>Date reçu</td><td>N° reçu</td><td>Montant</td><td>Mode paiement</td><td>Collecté par</td></tr>
        <tr><td>{{ $paiement?->created_at?->format('d/m/Y H:i') }}</td><td>{{ $paiement->id ?? '' }}</td><td>{{ number_format($montantPaye,2) }}</td><td>{{ $paiement->paymentMethod->label ?? 'ESPÈCES' }}</td><td>{{ $paiement->utilisateur->name ?? '' }}</td></tr>
    </table>

    {{-- RESUME --}}
    <div class="resume-section">
        <div class="resume-left">
            <div><strong>Total :</strong> {{ number_format($total,2) }} {{ $formatArgent }}</div>
            <div><strong>Payé :</strong> {{ number_format($montantPaye,2) }} {{ $formatArgent }}</div>
            <div><strong>Solde :</strong> {{ number_format($solde,2) }} {{ $formatArgent }}</div>
            <div><strong>Préparé par :</strong> {{ $prescription->secretaire->name ?? 'SECRETAIRE' }}</div>
        </div>
        <div class="resume-right">
            <div class="cachet-paye {{ $solde==0?'paye':'non-paye' }}">{{ $solde==0?'PAYÉ':'NON PAYÉ' }}</div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <div><strong>{{ strtoupper($nomEntreprise) }}</strong></div>
        <div>@if($nifEntreprise)NIF:{{ $nifEntreprise }}@endif @if($statutEntreprise)- STAT:{{ $statutEntreprise }}@endif</div>
        <div>Siège: Antananarivo - Tél: +261 34 XX XXX XX - Email: contact@laboratoire.mg</div>
        <div>Comptes: MCB: 00000 00000 000000 - BNI: 00000 021579201023</div>
    </div>
</div>
</body>
</html>
