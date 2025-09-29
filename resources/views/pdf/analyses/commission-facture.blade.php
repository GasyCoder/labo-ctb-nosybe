<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture des Commissions</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            margin: 0 auto;
            padding: 20px;
            border-radius: 8px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #4a90e2;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 22px;
            color: #4a90e2;
            margin: 0;
        }

        .header p {
            margin: 2px 0;
            color: #555;
        }
    .info {
        display: flex;
        justify-content: space-between;
        gap: 10px; /* espace entre les deux colonnes */
        margin-bottom: 25px;
        font-size: 13px;
        flex-wrap: wrap; /* si écran trop petit, les blocs passent en ligne */
    }

    .info .col {
        flex: 1; /* chaque colonne prend la même largeur */
        min-width: 45%; /* largeur minimale pour éviter trop petit */
        padding: 10px;
        border-radius: 6px;
    }

    .info strong {
        color: #333;
        display: block;
        margin-bottom: 5px;
    }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border-radius: 6px;
            overflow: hidden;
        }

        th, td {
            border: 1px solid #e0e6ed;
            padding: 8px;
            text-align: center;
            font-size: 12px;
        }

        th {
            background: #f0f4f8;
            font-weight: bold;
            color: #333;
        }

        .sub-table {
            margin-top: 8px;
            border: 1px solid #e0e6ed;
        }

        .sub-table th, .sub-table td {
            border: 1px solid #e0e6ed;
            font-size: 11px;
            padding: 5px;
        }

        /* .sub-table th {
            background: #f9fbfd;
        } */

        .total {
            text-align: right;
            margin-top: 15px;
            padding: 10px;
            /* background: #e6f3e6; */
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            color: #2d6a4f;
        }

        .footer {
            text-align: center;
            font-size: 11px;
            color: #777;
            /* border-top: 1px solid #ddd; */
            margin-top: 25px;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <div class="header">
            <h1>Facture des Commissions</h1>
            <p>{{ $prescripteur->nom_complet }}</p>
            <p>Période : {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}</p>
            <p>Date d'émission : {{ $dateEmission }}</p>
        </div>

   
<!-- Informations -->
<div class="info">
    <div class="col">
        <strong>Prescripteur</strong>
        Nom : {{ $prescripteur->nom_complet }}<br>
        Spécialité : {{ $prescripteur->specialite ?? 'N/A' }}<br>
        Email : {{ $prescripteur->email ?? 'N/A' }}<br>
        Téléphone : {{ $prescripteur->telephone ?? 'N/A' }}
    </div>
    <div class="col">
        <strong>Émetteur</strong>
        Nom : CBDC NOSY BE<br>
        Adresse : Immeuble ARO, 1ère étage, Nosy Be Hell Ville<br>
        Téléphone : 032 11 450 65<br>
        Email : laboratoirenosybe@gmail.com
    </div>
</div>

       <!-- Tableau détaillé des prescriptions -->
<!-- Tableau détaillé des prescriptions avec total -->
<table border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>Date</th>
            <th>N° Dossier</th>
            <th>Patient</th>
            <th>Montant payé (Ar)</th>
            <th>Commission (Ar)</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalPatients = 0;
            $totalMontantPaye = 0;
            $totalCommission = 0;
        @endphp

        @forelse($commissionDetails['data'] as $detail)
            @if(isset($detail->prescriptions) && $detail->prescriptions->count() > 0)
                @foreach($detail->prescriptions as $prescription)
                    <tr>
                        <td>{{ $prescription->date }}</td>
                        <td>{{ $prescription->patient_numero_dossier }}</td>
                        <td>{{ $prescription->patient_nom_complet }}</td>
                        <td>{{ number_format($prescription->montant_paye, 0, ',', ' ') }}</td>
                        <td>{{ number_format($prescription->commission, 0, ',', ' ') }}</td>
                    </tr>

                    @php
                        $totalPatients++;
                        $totalMontantPaye += $prescription->montant_paye;
                        $totalCommission += $prescription->commission;
                    @endphp
                @endforeach
            @endif
        @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 15px; color:#777;">
                    Aucune commission trouvée pour la période sélectionnée.
                </td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr style="font-weight: bold;">
             <td colspan="3" style="text-align:right;">Total :</td>
            <td>{{ number_format($totalMontantPaye, 0, ',', ' ') }}</td>
            <td>{{ number_format($totalCommission, 0, ',', ' ') }}</td>
        </tr>
    </tfoot>
</table>



        <!-- Résumé total -->
        @if($commissionDetails['total_commission'] > 0)
            <div class="total">
                Commission totale : {{ number_format($commissionDetails['total_commission'], 0, ',', ' ') }} Ar  
                ({{ $commissionDetails['total_prescriptions'] }} prescription(s) à {{ $commissionPourcentage }}%)
            </div>
        @endif

        <!-- Pied de page -->
        <div class="footer">
            CBDC NOSY BE | Facture des Commissions | {{ date('Y') }}
        </div>
    </div>
</body>
</html>