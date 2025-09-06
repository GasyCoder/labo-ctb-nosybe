<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture - {{ $prescription->reference }}</title>
    <style>
        @page {
            margin: 20mm 18mm 20mm 18mm;
            size: A4;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            padding: 10px 15px;
        }
        
        .container {
            max-width: 100%;
            margin: 0 auto;
        }
        
        .header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
            padding-right: 15px;
        }
        
        .header-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: right;
        }
        
        .lab-title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        
        .lab-details {
            font-size: 11px;
            line-height: 1.3;
        }
        
        .facture-title {
            font-size: 24px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 10px;
        }
        
        .reference-info {
            font-size: 11px;
            font-weight: bold;
        }
        
        .divider {
            text-align: center;
            margin: 15px 0;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .patient-info {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .patient-left, .patient-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 15px;
        }
        
        .section-title {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 8px;
            font-size: 13px;
        }
        
        .info-line {
            margin-bottom: 3px;
            font-size: 11px;
        }
        
        .info-label {
            display: inline-block;
            width: 80px;
            font-weight: bold;
        }
        
        .analyses-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10px;
        }
        
        .analyses-table th {
            text-align: center;
            padding: 6px 4px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 2px solid #000;
            border-top: 2px solid #000;
        }
        
        .analyses-table td {
            padding: 4px;
            text-align: center;
            vertical-align: middle;
            border-bottom: 1px solid #000;
        }
        
        .designation {
            text-align: left !important;
            padding-left: 6px;
        }
        
        .prix {
            text-align: right !important;
            padding-right: 6px;
            font-weight: bold;
        }
        
        .totals {
            float: right;
            width: 250px;
            margin: 10px 0;
        }
        
        .total-line {
            display: table;
            width: 100%;
            margin-bottom: 4px;
            font-size: 11px;
        }
        
        .total-label {
            display: table-cell;
            text-align: left;
            font-weight: bold;
        }
        
        .total-value {
            display: table-cell;
            text-align: right;
            font-weight: bold;
        }
        
        .final-total {
            border-top: 2px solid #000;
            padding-top: 6px;
            margin-top: 6px;
            font-size: 13px;
        }
        
        .payment-section {
            clear: both;
            margin-top: 20px;
            display: table;
            width: 100%;
        }
        
        .payment-left, .payment-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 15px;
        }
        
        .signatures {
            margin-top: 30px;
            display: table;
            width: 100%;
        }
        
        .signature-box {
            display: table-cell;
            width: 33%;
            text-align: center;
            vertical-align: bottom;
            height: 60px;
            padding: 0 10px;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            width: 100px;
            margin: 0 auto 5px auto;
            height: 40px;
        }
        
        .signature-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .signature-name {
            font-size: 9px;
            margin-top: 2px;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 9px;
            border-top: 1px solid #000;
            padding-top: 8px;
        }
        
        .status-global {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin: 10px 0;
            text-transform: uppercase;
        }
        
        .clearfix {
            clear: both;
        }
    </style>
</head>
<body>
    {{-- LOGIQUE DU STATUT DE PAIEMENT --}}
    @php
        $paiement = $prescription->paiements->first();
        $estPaye = $paiement ? $paiement->status : false;
        $totalAnalyses = 0;
        $totalPrelevements = 0;
    @endphp

    <div class="container">
        {{-- HEADER --}}
        <div class="header">
            <div class="header-left">
                <div class="lab-title">LABORATOIRE D'ANALYSES MEDICALES</div>
                <div class="lab-details">
                    <p><strong>Directeur Technique:</strong> Dr. {{ $prescription->prescripteur->nom ?? 'RESPONSABLE' }}</p>
                    <p><strong>Adresse:</strong> Centre ville, Antananarivo, Madagascar</p>
                    <p><strong>Téléphone:</strong> +261 34 XX XXX XX</p>
                    <p><strong>Email:</strong> contact@laboratoire.mg</p>
                    <p><strong>Autorisation N°:</strong> 2024/MSAN/LAB/001</p>
                </div>
            </div>
            
            <div class="header-right">
                <div class="facture-title">FACTURE</div>
                <div class="reference-info">
                    <p>Réf: {{ $prescription->reference }}</p>
                    <p>Date: {{ $prescription->created_at->format('d/m/Y') }}</p>
                    <p>Heure: {{ $prescription->created_at->format('H:i') }}</p>
                </div>
            </div>
        </div>

        {{-- STATUT GLOBAL --}}
        <div class="status-global">
            {{ $estPaye ? 'FACTURE PAYEE' : 'FACTURE NON PAYEE' }}
        </div>

        <div class="divider">PRESCRIPTION MEDICALE N° {{ $prescription->reference }}</div>

        {{-- INFORMATIONS PATIENT --}}
        <div class="patient-info">
            <div class="patient-left">
                <div class="section-title">PATIENT</div>
                <div class="info-line">
                    <span class="info-label">Nom:</span>
                    {{ strtoupper($prescription->patient->nom) }} {{ ucfirst($prescription->patient->prenom) }}
                </div>
                <div class="info-line">
                    <span class="info-label">Civilité:</span>
                    {{ $prescription->patient->civilite }}
                </div>
                <div class="info-line">
                    <span class="info-label">Âge:</span>
                    {{ $prescription->age }} {{ $prescription->unite_age ?? 'ans' }}
                </div>
                @if($prescription->patient->telephone)
                <div class="info-line">
                    <span class="info-label">Téléphone:</span>
                    {{ $prescription->patient->telephone }}
                </div>
                @endif
                @if($prescription->poids)
                <div class="info-line">
                    <span class="info-label">Poids:</span>
                    {{ $prescription->poids }} kg
                </div>
                @endif
            </div>
            
            <div class="patient-right">
                <div class="section-title">PRESCRIPTION</div>
                <div class="info-line">
                    <span class="info-label">Type:</span>
                    {{ $prescription->patient_type ?? 'EXTERNE' }}
                </div>
                @if($prescription->prescripteur)
                <div class="info-line">
                    <span class="info-label">Médecin:</span>
                    Dr. {{ $prescription->prescripteur->nom }} {{ $prescription->prescripteur->prenom ?? '' }}
                </div>
                @endif
                <div class="info-line">
                    <span class="info-label">Secrétaire:</span>
                    {{ $prescription->secretaire->name ?? 'N/A' }}
                </div>
                <div class="info-line">
                    <span class="info-label">Statut:</span>
                    {{ $prescription->status }}
                </div>
            </div>
        </div>

        {{-- TABLEAU DES ANALYSES --}}
        @if(count($prescription->analyses) > 0 || count($prescription->prelevements) > 0)
        <table class="analyses-table">
            <thead>
                <tr>
                    <th style="width: 6%">N°</th>
                    <th style="width: 10%">CODE</th>
                    <th style="width: 45%">DESIGNATION</th>
                    <th style="width: 12%">PRIX UNIT.</th>
                    <th style="width: 6%">QTE</th>
                    <th style="width: 12%">MONTANT</th>
                    <th style="width: 9%">STATUT</th>
                </tr>
            </thead>
            <tbody>
                {{-- ANALYSES --}}
                @foreach($prescription->analyses as $analyse)
                @php $totalAnalyses += $analyse->prix; @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><strong>{{ $analyse->code }}</strong></td>
                    <td class="designation">{{ $analyse->designation }}</td>
                    <td class="prix">{{ number_format($analyse->prix, 0) }} Ar</td>
                    <td>1</td>
                    <td class="prix">{{ number_format($analyse->prix, 0) }} Ar</td>
                    <td>{{ $estPaye ? 'PAYE' : 'NON PAYE' }}</td>
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
                    <td><strong>PREL</strong></td>
                    <td class="designation">{{ $prelevement->nom }}</td>
                    <td class="prix">{{ number_format($prixUnitaire, 0) }} Ar</td>
                    <td>{{ $quantite }}</td>
                    <td class="prix">{{ number_format($montantPrelevement, 0) }} Ar</td>
                    <td>{{ $estPaye ? 'PAYE' : 'NON PAYE' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- TOTAUX --}}
        <div class="totals">
            @php
                $sousTotal = $totalAnalyses + $totalPrelevements;
                $remise = $prescription->remise ?? 0;
                $total = max(0, $sousTotal - $remise);
            @endphp
            
            @if($totalAnalyses > 0)
            <div class="total-line">
                <div class="total-label">Sous-total Analyses:</div>
                <div class="total-value">{{ number_format($totalAnalyses, 0) }} Ar</div>
            </div>
            @endif
            
            @if($totalPrelevements > 0)
            <div class="total-line">
                <div class="total-label">Sous-total Prélèvements:</div>
                <div class="total-value">{{ number_format($totalPrelevements, 0) }} Ar</div>
            </div>
            @endif
            
            <div class="total-line">
                <div class="total-label">Total Brut:</div>
                <div class="total-value">{{ number_format($sousTotal, 0) }} Ar</div>
            </div>
            
            @if($remise > 0)
            <div class="total-line">
                <div class="total-label">Remise Accordée:</div>
                <div class="total-value">-{{ number_format($remise, 0) }} Ar</div>
            </div>
            @endif
            
            <div class="total-line final-total">
                <div class="total-label">TOTAL A PAYER:</div>
                <div class="total-value">{{ number_format($total, 0) }} Ar</div>
            </div>
        </div>

        <div class="clearfix"></div>

        {{-- INFORMATIONS DE PAIEMENT --}}
        <div class="payment-section">
            <div class="payment-left">
                <div class="section-title">PAIEMENT</div>
                <div class="info-line">
                    <span class="info-label">Mode:</span>
                    {{ $paiement ? ($paiement->paymentMethod->label ?? 'ESPECES') : 'ESPECES' }}
                </div>
                <div class="info-line">
                    <span class="info-label">Montant payé:</span>
                    {{ number_format($paiement ? $paiement->montant : 0, 0) }} Ar
                </div>
                @if($paiement && $paiement->montant > $total)
                <div class="info-line">
                    <span class="info-label">Monnaie:</span>
                    {{ number_format($paiement->montant - $total, 0) }} Ar
                </div>
                @endif
                <div class="info-line">
                    <span class="info-label">Statut:</span>
                    {{ $estPaye ? 'PAYE' : 'NON PAYE' }}
                </div>
                @if($paiement)
                <div class="info-line">
                    <span class="info-label">Date:</span>
                    {{ $paiement->created_at->format('d/m/Y H:i') }}
                </div>
                <div class="info-line">
                    <span class="info-label">Reçu par:</span>
                    {{ $paiement->utilisateur->name ?? 'N/A' }}
                </div>
                @endif
            </div>
            
            <div class="payment-right">
                <div class="section-title">RESUME</div>
                <div class="info-line">
                    <span class="info-label">Analyses:</span>
                    {{ count($prescription->analyses) }} item(s)
                </div>
                <div class="info-line">
                    <span class="info-label">Prélèvements:</span>
                    {{ count($prescription->prelevements) }} item(s)
                </div>
                <div class="info-line">
                    <span class="info-label">Tubes:</span>
                    {{ $prescription->tubes->count() }} tube(s)
                </div>
                {{-- <div class="info-line">
                    <span class="info-label">Commission:</span>
                    {{ number_format($paiement->commission_prescripteur ?? 0, 0) }} Ar
                </div> --}}
                {{-- <div class="info-line">
                    <span class="info-label">Délai:</span>
                    24-48h ouvrables
                </div> --}}
            </div>
        </div>

        {{-- SIGNATURES --}}
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-title">Signature Patient</div>
                <div class="signature-name">{{ $prescription->patient->nom }} {{ $prescription->patient->prenom }}</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-title">Cachet Laboratoire</div>
                <div class="signature-name">{{ now()->format('d/m/Y') }}</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-title">Signature Responsable</div>
                <div class="signature-name">{{ $prescription->secretaire->name ?? 'SECRÉTAIRE' }}</div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="footer">
            <p><strong>LABORATOIRE D'ANALYSES MEDICALES</strong> - Antananarivo, Madagascar</p>
            <p>contact@laboratoire.mg - +261 34 XX XXX XX - Autorisation N° 2024/MSAN/LAB/001</p>
            <p>Document généré le {{ now()->format('d/m/Y à H:i:s') }} - {{ $prescription->reference }}</p>
        </div>
    </div>
</body>
</html>