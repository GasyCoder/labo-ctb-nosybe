<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Journal de Caisse</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #d32f2f;
            margin-bottom: 5px;
        }
        
        .company-info {
            font-size: 12px;
            color: #666;
        }
        
        .period {
            text-align: center;
            font-weight: bold;
            margin: 20px 0;
            font-size: 14px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .section-header {
            font-weight: bold;
            padding: 12px 0 8px 0;
            text-align: left;
            text-transform: uppercase;
            font-size: 11px;
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
        }
        
        .table-header {
            font-weight: bold;
            padding: 8px 0 4px 0;
            font-size: 10px;
            text-transform: uppercase;
            border-bottom: 0.5px solid #000;
        }
        
        .table-header.left { text-align: left; }
        .table-header.right { text-align: right; }
        
        .data-row td {
            padding: 3px 8px 3px 0;
            font-size: 10px;
            line-height: 1.4;
        }
        
        .data-row .left { text-align: left; }
        .data-row .right { text-align: right; }
        
        .amount {
            text-align: right;
            font-weight: bold;
        }
        
        .subtotal {
            font-weight: bold;
            padding: 8px 0;
            border-top: 0.5px solid #000;
            margin-top: 5px;
        }
        
        .total {
            font-weight: bold;
            padding: 10px 0;
            border-top: 2px solid #000;
            border-bottom: 1px solid #000;
            font-size: 12px;
            margin-top: 10px;
        }
        
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">LNB SITE MAITRE</div>
        <div class="company-info">
            Analyses Médicales<br>
            IMMEUBLE ARO<br>
            Tél: 0321145065
        </div>
    </div>

    <!-- Période -->
    <div class="period">
        CAISSE du {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}
    </div>

    @if($paiements->count() > 0)
        @php
            $paiementsGroupes = $paiements->groupBy('paymentMethod.label');
            $totalGeneral = 0;
        @endphp

        @foreach($paiementsGroupes as $methodePaiement => $paiementsGroupe)
            <!-- Section Header -->
            <table>
                <tr>
                    <td class="section-header">{{ strtoupper($methodePaiement ?: 'NON DÉFINI') }}</td>
                </tr>
            </table>

            <!-- Table Header -->
            <table>
                <tr>
                    <td class="table-header left" style="width: 15%;">DATE</td>
                    <td class="table-header left" style="width: 15%;">DOSSIER</td>
                    <td class="table-header left" style="width: 50%;">CLIENT</td>
                    <td class="table-header right" style="width: 20%;">MONTANT</td>
                </tr>
                
                @php $sousTotal = 0; @endphp
                @foreach($paiementsGroupe as $paiement)
                    <tr class="data-row">
                        <td class="left">{{ $paiement->created_at->format('d/m/Y') }}</td>
                        <td class="left">{{ $paiement->prescription->patient->numero_dossier ?? 'N/A' }}</td>
                        <td class="left">
                            {{ $paiement->prescription->patient->nom ?? 'Client non défini' }} 
                            {{ $paiement->prescription->patient->prenom ?? '' }}
                        </td>
                        <td class="amount">{{ number_format($paiement->montant, 2, '.', ' ') }}</td>
                    </tr>
                    @php $sousTotal += $paiement->montant; @endphp
                @endforeach

                <!-- Sous-total -->
                <tr>
                    <td colspan="3" class="subtotal" style="text-align: right;">SOUS TOTAL</td>
                    <td class="subtotal amount">{{ number_format($sousTotal, 2, '.', ' ') }}Ar.</td>
                </tr>
            </table>

            @php $totalGeneral += $sousTotal; @endphp
        @endforeach

        <!-- Total Général -->
        <table style="margin-top: 15px;">
            <tr>
                <td colspan="3" class="total" style="text-align: right;">TOTAL GENERAL</td>
                <td class="total amount">{{ number_format($totalGeneral, 2, '.', ' ') }}Ar.</td>
            </tr>
        </table>

    @else
        <div class="no-data">
            Aucun paiement enregistré pour cette période
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        Édité le {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>