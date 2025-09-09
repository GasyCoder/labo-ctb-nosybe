{{-- resources/views/factures/ccare-style.blade.php - AVEC SETTINGS --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture - {{ $prescription->reference }}</title>
    <style>
        @page {
            margin: 15mm 10mm 15mm 10mm;
            size: A4;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.2;
            color: #000;
        }
        
        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 5px;
        }
        
        /* En-tête principal */
        .main-header {
            display: table;
            width: 100%;
            margin-bottom: 10px;
            border: 0px solid #000;
        }
        
        .header-left {
            display: table-cell;
            width: 30%;
            vertical-align: top;
            padding: 8px;
        }
        
        .header-center {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: center;
            padding: 8px;
            border-left: 0px solid #000;
            border-right: 0px solid #000;
        }
        
        .header-right {
            display: table-cell;
            width: 30%;
            vertical-align: top;
            padding: 8px;
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 5px;
        }
        
        .logo {
            max-width: 150px;
            max-height: 100px;
            object-fit: contain;
        }
        
        .lab-name {
            font-weight: bold;
            font-size: 8pt;
            color: #2E8B57;
            margin-top: 3px;
        }
        
        .facture-title {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        
        .facture-info {
            font-size: 8pt;
            text-align: center;
        }
        
        .facture-info div {
            margin-bottom: 2px;
        }
        
        .barcode-section {
            text-align: right;
            font-size: 7pt;
        }
        
        /* Section patient avec tableau pour alignement parfait */
        .patient-section {
            border: 0px solid #000;
            margin: 8px 0;
            padding: 0;
        }
        
        .patient-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }
        
        .patient-table td {
            padding: 4px 6px;
            vertical-align: top;
            border-bottom: 1px dotted #000;
        }
        
        .patient-table tr:last-child td {
            border-bottom: none;
        }
        
        .patient-label {
            font-weight: bold;
            width: 120px;
            background-color: #f9f9f9;
        }
        
        .patient-value {
            min-height: 16px;
        }
        
        .patient-separator {
            width: 20px;
            border-bottom: none !important;
        }
        
        /* Tableau des analyses */
        .analyses-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 8pt;
        }
        
        .analyses-table th {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 4px 2px;
            text-align: center;
            font-weight: bold;
            font-size: 7pt;
        }
        
        .analyses-table td {
            border: 1px solid #000;
            padding: 3px 2px;
            text-align: center;
        }
        
        .designation-cell {
            text-align: left !important;
            padding-left: 4px;
        }
        
        .montant-cell {
            text-align: right !important;
            padding-right: 4px;
            font-weight: bold;
        }
        
        /* Section totaux */
        .totaux-section {
            display: table;
            width: 100%;
            margin: 10px 0;
        }
        
        .totaux-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
            padding-right: 10px;
        }
        
        .totaux-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
        }
        
        .arret-text {
            font-size: 8pt;
            font-style: italic;
            border: 0px solid #000;
            padding: 5px;
            text-align: justify;
        }
        
        .totaux-table {
            border: 0px solid #000;
            width: 100%;
            font-size: 8pt;
        }
        
        .totaux-table td {
            padding: 3px 5px;
            border-bottom: 0px solid #000;
        }
        
        .totaux-label {
            text-align: right;
            font-weight: bold;
        }
        
        .totaux-value {
            text-align: right;
            font-weight: bold;
            width: 80px;
        }
        
        /* Section paiement */
        .paiement-section {
            border: 1px solid #000;
            margin: 10px 0;
            font-size: 8pt;
        }
        
        .paiement-header {
            background-color: #f0f0f0;
            padding: 3px 5px;
            font-weight: bold;
            text-align: center;
            border-bottom: 1px solid #000;
        }
        
        .paiement-content {
            padding: 5px;
        }
        
        .paiement-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .paiement-table td {
            padding: 2px 5px;
            border: 1px solid #000;
        }
        
        /* Section résumé */
        .resume-section {
            display: table;
            width: 100%;
            margin: 10px 0;
            font-size: 8pt;
        }
        
        .resume-left {
            display: table-cell;
            width: 50%;
            padding-right: 10px;
        }
        
        .resume-right {
            display: table-cell;
            width: 50%;
            text-align: center;
        }
        
        .resume-left div {
            margin-bottom: 3px;
        }
        
        .cachet-paye {
            border: 2px solid #000;
            padding: 15px;
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            transform: rotate(-15deg);
            margin: 10px auto;
            width: 100px;
        }
        
        .cachet-paye.paye {
            color: #008000;
            border-color: #008000;
        }
        
        .cachet-paye.non-paye {
            color: #ff0000;
            border-color: #ff0000;
        }
        
        /* Footer */
        .footer {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #000;
            font-size: 7pt;
            text-align: center;
        }
        
        .footer-line {
            margin-bottom: 1px;
        }
    </style>
</head>
<body>
    @php
        // Récupérer les settings de l'application
        $settings = \App\Models\Setting::first();
        $nomEntreprise = $settings ? $settings->nom_entreprise : 'LABORATOIRE CTB';
        $nifEntreprise = $settings ? $settings->nif : '2000000000';
        $statutEntreprise = $settings ? $settings->statut : '72102 11 2010 010000';
        $formatArgent = $settings ? $settings->format_unite_argent : 'Ar';
        
        $paiement = $prescription->paiements->first();
        $estPaye = $paiement ? $paiement->status : false;
        $totalAnalyses = 0;
        $totalPrelevements = 0;
    @endphp

    <div class="container">
        {{-- EN-TÊTE PRINCIPAL --}}
        <div class="main-header">
            <div class="header-left">
                <div class="logo-section">
                    @php
                        // Utiliser le logo des settings ou logo par défaut
                        if ($settings && $settings->logo) {
                            $logoPath = storage_path('app/public/' . $settings->logo);
                            if (file_exists($logoPath)) {
                                $logoData = file_get_contents($logoPath);
                                $extension = pathinfo($settings->logo, PATHINFO_EXTENSION);
                                $mimeType = $extension === 'png' ? 'png' : 'jpeg';
                                $logoBase64 = 'data:image/' . $mimeType . ';base64,' . base64_encode($logoData);
                            } else {
                                $logoBase64 = null;
                            }
                        } else {
                            // Logo par défaut
                            $logoPath = public_path('assets/images/logo_facture.jpg');
                            if (file_exists($logoPath)) {
                                $logoData = file_get_contents($logoPath);
                                $logoBase64 = 'data:image/jpeg;base64,' . base64_encode($logoData);
                            } else {
                                $logoBase64 = null;
                            }
                        }
                    @endphp
                    
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Logo {{ $nomEntreprise }}" class="logo">
                    @else
                        <div style="width: 150px; height: 100px; background: #2E8B57; color: white; text-align: center; line-height: 100px; font-size: 10pt; font-weight: bold;">
                            {{ strtoupper(substr($nomEntreprise, 0, 4)) }}
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="header-center">
                <div class="facture-title">FACTURE</div>
                <div class="facture-info">
                    <div><strong>Du:</strong> {{ $prescription->created_at->format('d/m/Y H:i') }}</div>
                    <div><strong>N°:</strong> {{ $prescription->reference }}</div>
                </div>
            </div>
            
            <div class="header-right">
                <div class="barcode-section" style="text-align: center;">
                    @php
                        try {
                            $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                            $barcodeImage = $generator->getBarcode($prescription->reference, $generator::TYPE_CODE_128, 2, 40);
                            $barcodeBase64 = 'data:image/png;base64,' . base64_encode($barcodeImage);
                        } catch (Exception $e) {
                            $barcodeBase64 = null;
                        }
                    @endphp
                    
                    @if($barcodeBase64)
                        <img src="{{ $barcodeBase64 }}" alt="Code-barres" style="max-width: 120px; height: 40px; display: block; margin: 0 auto;">
                    @else
                        <div style="font-family: monospace; border: 1px solid #000; padding: 5px; font-size: 8pt; text-align: center;">
                            {{ $prescription->reference }}
                        </div>
                    @endif
                    
                    <div style="font-size: 7pt; text-align: center; margin-top: 3px;">
                        {{ $prescription->reference }}
                    </div>
                </div>
            </div>
        </div>

        {{-- INFORMATIONS PATIENT - TABLEAU ALIGNÉ --}}
        <div class="patient-section">
            <table class="patient-table">
                <tr>
                    <td class="patient-label">PID :</td>
                    <td class="patient-value">{{ $prescription->patient->numero_dossier ?? $prescription->reference }}</td>
                    <td class="patient-separator"></td>
                    <td class="patient-label">STAT :</td>
                    <td class="patient-value">{{ $prescription->status }}</td>
                </tr>
                <tr>
                    <td class="patient-label">Visit ID :</td>
                    <td class="patient-value">{{ $prescription->reference }}</td>
                    <td class="patient-separator"></td>
                    <td class="patient-label">Panel :</td>
                    <td class="patient-value">EXTERNE</td>
                </tr>
                <tr>
                    <td class="patient-label">Nom du patient :</td>
                    <td class="patient-value" colspan="4">{{ strtoupper($prescription->patient->civilite ?? '') }} {{ strtoupper($prescription->patient->nom) }} {{ strtoupper($prescription->patient->prenom ?? '') }}</td>
                </tr>
                <tr>
                    <td class="patient-label">DDN/Sexe :</td>
                    <td class="patient-value">{{ $prescription->age }} {{ $prescription->unite_age ?? 'ans' }}</td>
                    <td class="patient-separator"></td>
                    <td class="patient-label">Type de client :</td>
                    <td class="patient-value">{{ $prescription->patient_type ?? 'EXTERNE' }}</td>
                </tr>
                <tr>
                    <td class="patient-label">Non de portable :</td>
                    <td class="patient-value">{{ $prescription->patient->telephone ?? '' }}</td>
                    <td class="patient-separator"></td>
                    <td class="patient-label">Référé par :</td>
                    <td class="patient-value">{{ $prescription->prescripteur->nom ?? '' }} {{ $prescription->prescripteur->prenom ?? '' }}</td>
                </tr>
                <tr>
                    <td class="patient-label">Adresse :</td>
                    <td class="patient-value" colspan="4">{{ $prescription->patient->adresse ?? '' }}</td>
                </tr>
            </table>
        </div>

        {{-- TABLEAU DES ANALYSES --}}
        <table class="analyses-table">
            <thead>
                <tr>
                    <th style="width: 8%">N° de série</th>
                    <th style="width: 42%">Détails / Désignation</th>
                    <th style="width: 8%">Unités</th>
                    <th style="width: 14%">Tarif ({{ $formatArgent }})</th>
                    <th style="width: 14%">Remise ({{ $formatArgent }})</th>
                    <th style="width: 14%">Montant ({{ $formatArgent }})</th>
                </tr>
            </thead>
            <tbody>
                {{-- ANALYSES --}}
                @foreach($prescription->analyses as $analyse)
                @php 
                    $totalAnalyses += $analyse->prix;
                    $remiseIndividuelle = 0;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="designation-cell">{{ $analyse->designation }}</td>
                    <td>1.00</td>
                    <td class="montant-cell">{{ number_format($analyse->prix, 2) }}</td>
                    <td class="montant-cell">{{ number_format($remiseIndividuelle, 2) }}</td>
                    <td class="montant-cell">{{ number_format($analyse->prix - $remiseIndividuelle, 2) }}</td>
                </tr>
                @endforeach

                {{-- PRÉLÈVEMENTS --}}
                @foreach($prescription->prelevements as $prelevement)
                @php 
                    $prixUnitaire = $prelevement->pivot->prix_unitaire ?? 0;
                    $quantite = $prelevement->pivot->quantite ?? 1;
                    $montantPrelevement = $prixUnitaire * $quantite;
                    $totalPrelevements += $montantPrelevement;
                    $remisePrelevement = 0;
                @endphp
                <tr>
                    <td>{{ count($prescription->analyses) + $loop->iteration }}</td>
                    <td class="designation-cell">{{ $prelevement->nom }}</td>
                    <td>{{ $quantite }}.00</td>
                    <td class="montant-cell">{{ number_format($prixUnitaire, 2) }}</td>
                    <td class="montant-cell">{{ number_format($remisePrelevement, 2) }}</td>
                    <td class="montant-cell">{{ number_format($montantPrelevement, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- SECTION TOTAUX --}}
        <div class="totaux-section">
            <div class="totaux-left">
                <div class="arret-text">
                    <strong>Arrêt la présente facture à la somme de :</strong><br>
                    @php
                        $sousTotal = $totalAnalyses + $totalPrelevements;
                        $remise = $prescription->remise ?? 0;
                        $total = max(0, $sousTotal - $remise);
                    @endphp
                    Quatre-vingt-dix-huit mille cent quatre-vingt-dix-huit {{ strtolower($formatArgent) }}
                </div>
            </div>
            
            <div class="totaux-right">
                <table class="totaux-table">
                    <tr>
                        <td class="totaux-label">Montant total (hors TVA) :</td>
                        <td class="totaux-value">{{ number_format($sousTotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="totaux-label">Montant total de la TVA :</td>
                        <td class="totaux-value">0.00</td>
                    </tr>
                    <tr>
                        <td class="totaux-label">TVA incluse :</td>
                        <td class="totaux-value">0.00</td>
                    </tr>
                    <tr>
                        <td class="totaux-label">Montant de la remise :</td>
                        <td class="totaux-value">{{ number_format($remise, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="totaux-label">Montant assuré :</td>
                        <td class="totaux-value">0.00</td>
                    </tr>
                    <tr style="border-top: 2px solid #000;">
                        <td class="totaux-label"><strong>Montant payé :</strong></td>
                        <td class="totaux-value"><strong>{{ number_format($total, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <td class="totaux-label">Montant du solde :</td>
                        <td class="totaux-value">0.00</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- SECTION PAIEMENT --}}
        <div class="paiement-section">
            <div class="paiement-header">Informations de paiement</div>
            <div class="paiement-content">
                <table class="paiement-table">
                    <tr>
                        <td><strong>Date et heure de reçu</strong></td>
                        <td><strong>N° de reçu</strong></td>
                        <td><strong>Montant</strong></td>
                        <td><strong>Mode de paiement</strong></td>
                        <td><strong>Collecté par</strong></td>
                    </tr>
                    <tr>
                        <td>{{ $paiement ? $paiement->created_at->format('d/m/Y H:i') : '' }}</td>
                        <td>{{ $paiement ? $paiement->id : '' }}</td>
                        <td>{{ $paiement ? number_format($paiement->montant, 2) : '0.00' }}</td>
                        <td>{{ $paiement ? ($paiement->paymentMethod->label ?? 'ESPÈCES') : 'ESPÈCES' }}</td>
                        <td>{{ $paiement ? ($paiement->utilisateur->name ?? '') : '' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- SECTION RÉSUMÉ --}}
        <div class="resume-section">
            <div class="resume-left">
                <div><strong>Total facture :</strong> {{ number_format($total, 2) }} {{ $formatArgent }}</div>
                <div><strong>Montant encaissé :</strong> {{ number_format($paiement ? $paiement->montant : 0, 2) }} {{ $formatArgent }}</div>
                <div><strong>Solde restant :</strong> {{ number_format(max(0, $total - ($paiement ? $paiement->montant : 0)), 2) }} {{ $formatArgent }}</div>
                <div><strong>Préparé par :</strong> {{ $prescription->secretaire->name ?? 'SECRÉTAIRE' }}</div>
            </div>
            
            <div class="resume-right">
                <div class="cachet-paye {{ $estPaye ? 'paye' : 'non-paye' }}">
                    {{ $estPaye ? 'PAYÉ' : 'NON PAYÉ' }}
                </div>
            </div>
        </div>

        {{-- FOOTER AVEC INFORMATIONS DYNAMIQUES --}}
        <div class="footer">
            <div class="footer-line"><strong>{{ strtoupper($nomEntreprise) }}</strong></div>
            <div class="footer-line">
                @if($nifEntreprise)
                    NIF: {{ $nifEntreprise }}
                @endif
                @if($nifEntreprise && $statutEntreprise) - @endif
                @if($statutEntreprise)
                    STAT: {{ $statutEntreprise }}
                @endif
            </div>
            <div class="footer-line">Siège social: Antananarivo - Tél: +261 34 XX XXX XX - Email: contact@laboratoire.mg</div>
            <div class="footer-line">Comptes bancaires: MCB TANA: 00000 00000 00000000000 - BNI-CL TANA: 00000 021579201023</div>
        </div>
    </div>
</body>
</html>