<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Journal de Caisse - Par Date de Paiement</title>
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
        
        .period-info {
            text-align: center;
            font-size: 10px;
            color: #666;
            margin-bottom: 15px;
            font-style: italic;
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
        
        /* Badge Modifié pour PDF */
        .badge-modified {
            display: inline-block;
            margin-left: 5px;
            padding: 2px 6px;
            color: #C2410C;
            font-size: 8px;
            border-radius: 3px;
            text-transform: uppercase;
        }
        
        /* Style pour date et heure de paiement */
        .payment-date {
            font-weight: bold;
        }
        
        .payment-time {
            font-size: 8px;
            color: #666;
            margin-left: 5px;
        }
        
        /* Note importante */
        .important-note {
            background-color: #f0f8ff;
            border: 1px solid #4a90e2;
            padding: 10px;
            margin: 10px 0;
            font-size: 10px;
            border-radius: 3px;
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

    <!-- Note importante -->
    <div class="important-note">
        <strong>NOTE :</strong> Ce journal est basé sur les <strong>dates de paiement effectives</strong> 
        (quand les paiements ont été marqués comme payés) et non sur les dates de création des dossiers.
    </div>

    <!-- Période -->
    <div class="period">
        JOURNAL CAISSE - PAIEMENTS EFFECTUÉS
    </div>
    <div class="period">
        du {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}
    </div>
    <div class="period-info">
        Période basée sur les dates de paiement réelles
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
                    <td class="table-header left" style="width: 18%;">DATE PAIEMENT</td>
                    <td class="table-header left" style="width: 15%;">DOSSIER</td>
                    <td class="table-header left" style="width: 47%;">CLIENT</td>
                    <td class="table-header right" style="width: 20%;">MONTANT</td>
                </tr>
                
                @php $sousTotal = 0; @endphp
                @foreach($paiementsGroupe as $paiement)
                    <tr class="data-row">
                        <td class="left">
                            @if($paiement->date_paiement)
                                <span class="payment-date">{{ $paiement->date_paiement->format('d/m/Y') }}</span>
                                <span class="payment-time">{{ $paiement->date_paiement->format('H:i') }}</span>
                            @else
                                <span style="color: #999;">N/A</span>
                            @endif
                        </td>
                        <td class="left">
                            {{ $paiement->prescription->patient->numero_dossier ?? 'N/A' }}
                            
                            {{-- Badge "MODIFIÉ" si la prescription a été modifiée --}}
                            @if($paiement->prescription && $paiement->prescription->created_at->ne($paiement->prescription->updated_at))
                                 (Modifié-{{ $paiement->prescription->updated_at->format('d/m/Y H:i') }})
                            @endif
                        </td>
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
                <td colspan="3" class="total" style="text-align: right;">TOTAL GENERAL (Période)</td>
                <td class="total amount">{{ number_format($totalGeneral, 2, '.', ' ') }}Ar.</td>
            </tr>
        </table>

        <!-- Récapitulatif des statistiques -->
        <table style="margin-top: 20px; font-size: 10px;">
            <tr>
                <td colspan="4" style="border-bottom: 1px solid #000; padding: 5px 0; font-weight: bold; text-align: center;">
                    RÉSUMÉ STATISTIQUE
                </td>
            </tr>
            <tr>
                <td style="padding: 3px 0;">Nombre de paiements effectués :</td>
                <td style="font-weight: bold;">{{ $paiements->count() }}</td>
                <td style="padding: 3px 0; padding-left: 20px;">Évolution vs période précédente :</td>
                <td style="font-weight: bold; color: {{ $evolutionSemaine >= 0 ? '#008000' : '#FF0000' }};">
                    {{ $evolutionSemaine >= 0 ? '+' : '' }}{{ number_format($evolutionSemaine, 1) }}%
                </td>
            </tr>
            <tr>
                <td style="padding: 3px 0;">Méthodes de paiement utilisées :</td>
                <td style="font-weight: bold;">{{ $paiementsGroupes->count() }}</td>
                <td style="padding: 3px 0; padding-left: 20px;">Total général (tous paiements) :</td>
                <td style="font-weight: bold;">{{ number_format($totalGeneral, 2, '.', ' ') }}Ar.</td>
            </tr>
        </table>

    @else
        <div class="no-data">
            Aucun paiement effectué durant cette période<br>
            Vérifiez les dates sélectionnées ou l'état des paiements.
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        Rapport généré le {{ now()->format('d/m/Y H:i:s') }}<br>
        <strong>Base de données :</strong> Dates de paiement effectives (non dates de création)
    </div>
</body>
</html>