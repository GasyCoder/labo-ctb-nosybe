<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture - {{ $prescription->reference }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
        }
        
        .header {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        
        .header-left {
            display: table-cell;
            width: 65%;
            vertical-align: top;
        }
        
        .header-right {
            display: table-cell;
            width: 35%;
            vertical-align: top;
            text-align: right;
        }
        
        .lab-info h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #000;
            text-transform: uppercase;
        }
        
        .lab-info p {
            font-size: 10px;
            margin-bottom: 3px;
            color: #000;
        }
        
        .facture-title {
            font-size: 28px;
            font-weight: bold;
            border: 3px solid #000;
            padding: 10px 20px;
            display: inline-block;
            letter-spacing: 2px;
        }
        
        .reference-section {
            margin-top: 15px;
            text-align: right;
            font-size: 12px;
        }
        
        .reference-section p {
            margin-bottom: 4px;
            font-weight: bold;
        }
        
        .patient-section {
            margin: 20px 0;
            display: table;
            width: 100%;
        }
        
        .patient-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }
        
        .patient-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-left: 20px;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 10px;
            text-decoration: underline;
            text-transform: uppercase;
            color: #000;
        }
        
        .info-line {
            margin-bottom: 5px;
            font-size: 10px;
        }
        
        .info-line strong {
            display: inline-block;
            width: 80px;
            font-weight: bold;
        }
        
        .analyses-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
        }
        
        .analyses-table th {
            background: #f0f0f0;
            border: 1px solid #000;
            padding: 8px 5px;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        
        .analyses-table td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            vertical-align: middle;
            font-size: 9px;
        }
        
        .analyses-table .designation {
            text-align: left;
            padding-left: 8px;
        }
        
        .analyses-table .prix {
            text-align: right;
            padding-right: 8px;
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }
        
        .status-paye {
            background: #d4edda;
            color: #155724;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 3px;
        }
        
        .status-non-paye {
            background: #f8d7da;
            color: #721c24;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 3px;
        }
        
        .totals-section {
            margin-top: 20px;
            float: right;
            width: 300px;
        }
        
        .total-line {
            display: table;
            width: 100%;
            margin-bottom: 5px;
            font-size: 11px;
        }
        
        .total-line .label {
            display: table-cell;
            text-align: left;
            font-weight: bold;
            padding-right: 15px;
        }
        
        .total-line .value {
            display: table-cell;
            text-align: right;
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }
        
        .final-total {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 8px 0;
            margin-top: 10px;
            font-size: 13px;
            font-weight: bold;
        }
        
        .payment-info {
            clear: both;
            margin-top: 30px;
            display: table;
            width: 100%;
        }
        
        .payment-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }
        
        .payment-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-left: 20px;
        }
        
        .footer {
            position: fixed;
            bottom: 10mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 8px;
            background: white;
        }
        
        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        
        .signature-box {
            display: table-cell;
            width: 33%;
            text-align: center;
            vertical-align: bottom;
            height: 80px;
            font-size: 10px;
        }
        
        .signature-line {
            border-bottom: 2px solid #000;
            width: 120px;
            margin: 0 auto 10px auto;
            height: 50px;
        }
        
        .center {
            text-align: center;
        }
        
        .right {
            text-align: right;
        }
        
        .bold {
            font-weight: bold;
        }
        
        .clearfix {
            clear: both;
        }
        
        .prescription-header {
            text-align: center;
            margin: 15px 0;
            padding: 8px;
            font-weight: bold;
            font-size: 13px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    {{-- HEADER --}}
    <div class="header">
        <div class="header-left">
            <div class="lab-info">
                <h1>LABORATOIRE D'ANALYSES MEDICALES</h1>
                <p><strong>Directeur Technique:</strong> Dr. {{ $prescription->prescripteur->nom ?? 'RESPONSABLE' }}</p>
                <p><strong>Adresse:</strong> Centre ville, Antananarivo, Madagascar</p>
                <p><strong>Téléphone:</strong> +261 34 XX XXX XX / +261 33 XX XXX XX</p>
                <p><strong>Email:</strong> contact@laboratoire.mg</p>
                <p><strong>Autorisation Ministérielle N°:</strong> 2024/MSAN/LAB/001</p>
            </div>
        </div>
        <div class="header-right">
            <div class="facture-title">FACTURE</div>
            <div class="reference-section">
                <p>Réf: {{ $prescription->reference }}</p>
                <p>Date: {{ $prescription->created_at->format('d/m/Y') }}</p>
                <p>Heure: {{ $prescription->created_at->format('H:i') }}</p>
            </div>
        </div>
    </div>

    {{-- EN-TÊTE PRESCRIPTION --}}
    <div class="prescription-header">
        ANALYSES MEDICALES - PRESCRIPTION N° {{ $prescription->reference }}
    </div>

    {{-- INFORMATIONS PATIENT --}}
    <div class="patient-section">
        <div class="patient-left">
            <div class="section-title">Informations Patient</div>
            <div class="info-line">
                <strong>Nom:</strong> {{ strtoupper($prescription->patient->nom) }} {{ ucfirst($prescription->patient->prenom) }}
            </div>
            <div class="info-line">
                <strong>Civilité:</strong> {{ $prescription->patient->civilite }}
            </div>
            <div class="info-line">
                <strong>Âge:</strong> {{ $prescription->age }} {{ $prescription->unite_age ?? 'ans' }}
            </div>
            @if($prescription->patient->telephone)
            <div class="info-line">
                <strong>Téléphone:</strong> {{ $prescription->patient->telephone }}
            </div>
            @endif
            @if($prescription->patient->email)
            <div class="info-line">
                <strong>Email:</strong> {{ $prescription->patient->email }}
            </div>
            @endif
            @if($prescription->poids)
            <div class="info-line">
                <strong>Poids:</strong> {{ $prescription->poids }} kg
            </div>
            @endif
        </div>
        
        <div class="patient-right">
            <div class="section-title">Informations Prescription</div>
            <div class="info-line">
                <strong>Type:</strong> {{ $prescription->patient_type ?? 'EXTERNE' }}
            </div>
            @if($prescription->prescripteur)
            <div class="info-line">
                <strong>Médecin:</strong> Dr. {{ $prescription->prescripteur->nom }} {{ $prescription->prescripteur->prenom ?? '' }}
            </div>
            @endif
            <div class="info-line">
                <strong>Secrétaire:</strong> {{ $prescription->secretaire->name ?? 'N/A' }}
            </div>
            <div class="info-line">
                <strong>Statut:</strong> {{ $prescription->status }}
            </div>
            @if($prescription->renseignement_clinique)
            <div class="info-line">
                <strong>Rens. Clinique:</strong> {{ $prescription->renseignement_clinique }}
            </div>
            @endif
        </div>
    </div>

    {{-- TABLEAU DES ANALYSES --}}
    <table class="analyses-table">
        <thead>
            <tr>
                <th style="width: 5%">N°</th>
                <th style="width: 12%">CODE</th>
                <th style="width: 40%">DESIGNATION</th>
                <th style="width: 12%">PRIX UNIT.</th>
                <th style="width: 6%">QTÉ</th>
                <th style="width: 12%">MONTANT</th>
                <th style="width: 13%">STATUT</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAnalyses = 0;
                $totalPrelevements = 0;
                $estPaye = $prescription->paiements->count() > 0;
            @endphp
            
            {{-- ANALYSES --}}
            @foreach($prescription->analyses as $index => $analyse)
            @php $totalAnalyses += $analyse->prix; @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="bold">{{ $analyse->code }}</td>
                <td class="designation">{{ $analyse->designation }}</td>
                <td class="prix">{{ number_format($analyse->prix, 0) }} Ar</td>
                <td>1</td>
                <td class="prix">{{ number_format($analyse->prix, 0) }} Ar</td>
                <td>
                    @if($estPaye)
                        <span class="status-paye">PAYÉ</span>
                    @else
                        <span class="status-non-paye">NON PAYÉ</span>
                    @endif
                </td>
            </tr>
            @endforeach

            {{-- PRÉLÈVEMENTS --}}
            @foreach($prescription->prelevements as $prelevement)
            @php 
                $prixUnitaire = $prelevement->pivot->prix_unitaire ?? 0;
                $quantite = $prelevement->pivot->quantite ?? 1;
                $montantPrelevement = $prixUnitaire * $quantite;
                $totalPrelevements += $montantPrelevement;
            @endphp
            <tr>
                <td>{{ count($prescription->analyses) + $loop->iteration }}</td>
                <td class="bold">PREL</td>
                <td class="designation">{{ $prelevement->nom }}</td>
                <td class="prix">{{ number_format($prixUnitaire, 0) }} Ar</td>
                <td>{{ $quantite }}</td>
                <td class="prix">{{ number_format($montantPrelevement, 0) }} Ar</td>
                <td>
                    @if($estPaye)
                        <span class="status-paye">PAYÉ</span>
                    @else
                        <span class="status-non-paye">NON PAYÉ</span>
                    @endif
                </td>
            </tr>
            @endforeach

            {{-- Lignes vides pour compléter le tableau --}}
            @for($i = 0; $i < (12 - count($prescription->analyses) - count($prescription->prelevements)); $i++)
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            @endfor
        </tbody>
    </table>

    {{-- TOTAUX --}}
    <div class="totals-section">
        @php
            $sousTotal = $totalAnalyses + $totalPrelevements;
            $remise = $prescription->remise ?? 0;
            $total = max(0, $sousTotal - $remise);
        @endphp
        
        <div class="total-line">
            <div class="label">Sous-total Analyses:</div>
            <div class="value">{{ number_format($totalAnalyses, 0) }} Ar</div>
        </div>
        
        @if($totalPrelevements > 0)
        <div class="total-line">
            <div class="label">Sous-total Prélèvements:</div>
            <div class="value">{{ number_format($totalPrelevements, 0) }} Ar</div>
        </div>
        @endif
        
        <div class="total-line">
            <div class="label">Total Brut:</div>
            <div class="value">{{ number_format($sousTotal, 0) }} Ar</div>
        </div>
        
        @if($remise > 0)
        <div class="total-line">
            <div class="label">Remise Accordée:</div>
            <div class="value">-{{ number_format($remise, 0) }} Ar</div>
        </div>
        @endif
        
        <div class="total-line final-total">
            <div class="label">TOTAL À PAYER:</div>
            <div class="value">{{ number_format($total, 0) }} Ar</div>
        </div>
    </div>

    <div class="clearfix"></div>

    {{-- INFORMATIONS DE PAIEMENT --}}
    <div class="payment-info">
        <div class="payment-left">
            <div class="section-title">Informations de Paiement</div>
            @php $paiement = $prescription->paiements->first(); @endphp
            <div class="info-line">
                <strong>Mode:</strong> {{ $paiement ? ($paiement->paymentMethod->name ?? 'ESPECES') : 'ESPECES' }}
            </div>
            <div class="info-line">
                <strong>Montant payé:</strong> {{ number_format($paiement ? $paiement->montant : ($estPaye ? $total : 0), 0) }} Ar
            </div>
            @if($paiement && $paiement->montant > $total)
            <div class="info-line">
                <strong>Monnaie rendue:</strong> {{ number_format($paiement->montant - $total, 0) }} Ar
            </div>
            @endif
            <div class="info-line">
                <strong>Statut:</strong> 
                @if($estPaye)
                    <span class="status-paye">PAYÉ</span>
                @else
                    <span class="status-non-paye">NON PAYÉ</span>
                @endif
            </div>
            @if($paiement)
            <div class="info-line">
                <strong>Date paiement:</strong> {{ $paiement->created_at->format('d/m/Y H:i') }}
            </div>
            @endif
        </div>
        
        <div class="payment-right">
            <div class="section-title">Résumé de la Prescription</div>
            <div class="info-line">
                <strong>Analyses:</strong> {{ count($prescription->analyses) }} item(s)
            </div>
            <div class="info-line">
                <strong>Prélèvements:</strong> {{ count($prescription->prelevements) }} item(s)
            </div>
            <div class="info-line">
                <strong>Tubes générés:</strong> {{ $prescription->tubes->count() }} tube(s)
            </div>
            <div class="info-line">
                <strong>Délai résultats:</strong> 24-48h ouvrables
            </div>
            <div class="info-line">
                <strong>Date émission:</strong> {{ $prescription->created_at->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>

    {{-- SIGNATURES --}}
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="bold">SIGNATURE PATIENT</div>
            <div style="font-size: 8px;">{{ $prescription->patient->nom }} {{ $prescription->patient->prenom }}</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="bold">CACHET LABORATOIRE</div>
            <div style="font-size: 8px;">{{ now()->format('d/m/Y') }}</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="bold">SIGNATURE RESPONSABLE</div>
            <div style="font-size: 8px;">{{ $prescription->secretaire->name ?? 'SECRÉTAIRE' }}</div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <p><strong>LABORATOIRE D'ANALYSES MEDICALES</strong> - Centre ville, Antananarivo, Madagascar</p>
        <p>Tél: +261 34 XX XXX XX - Email: contact@laboratoire.mg - Autorisation N° 2024/MSAN/LAB/001</p>
        <p>Facture générée le {{ now()->format('d/m/Y à H:i:s') }} - Document officiel - {{ $prescription->reference }}</p>
    </div>
</body>
</html>