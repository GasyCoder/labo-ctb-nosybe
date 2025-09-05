<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Résultats d'analyses</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* Styles personnalisés pour correspondre au design */
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }
        
        .header-section {
            position: relative;
            height: 120px;
        }
        
        .logo {
            position: absolute;
            left: 20px;
            top: 10px;
            width: 100px;
            height: 100px;
        }
        
        .lab-name {
            position: absolute;
            left: 140px;
            top: 20px;
            font-size: 18px;
            font-weight: bold;
            color: #8B0000;
        }
        
        .red-bar {
            position: absolute;
            left: 140px;
            top: 45px;
            width: 200px;
            height: 4px;
            background: linear-gradient(to right, #8B0000, #FF4500);
        }
        
        .contact-info {
            position: absolute;
            right: 20px;
            top: 15px;
            text-align: right;
            font-size: 9px;
            line-height: 1.2;
        }
        
        .qr-code {
            position: absolute;
            right: 20px;
            bottom: 10px;
            width: 80px;
            height: 80px;
        }
        
        .lab-details {
            position: absolute;
            left: 140px;
            top: 65px;
            font-size: 10px;
            color: #333;
        }
        
        .patient-section {
            margin-top: 20px;
            margin-bottom: 30px;
        }
        
        .patient-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .patient-info {
            font-size: 11px;
            line-height: 1.4;
        }
        
        .results-section {
            margin-top: 30px;
        }
        
        .examen-title {
            font-size: 12px;
            font-weight: bold;
            color: #DC143C;
            margin: 20px 0 10px 0;
            border-bottom: 1px solid #DC143C;
            padding-bottom: 2px;
        }
        
        .analysis-parent {
            font-weight: bold;
            margin: 8px 0 4px 0;
        }
        
        .analysis-child {
            margin-left: 20px;
            margin: 4px 0;
        }
        
        .results-table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }
        
        .results-table th,
        .results-table td {
            padding: 3px 8px;
            vertical-align: top;
        }
        
        .col-name {
            width: 40%;
            text-align: left;
        }
        
        .col-result {
            width: 20%;
            text-align: center;
        }
        
        .col-ref {
            width: 20%;
            text-align: center;
        }
        
        .col-ant {
            width: 20%;
            text-align: center;
        }
        
        .table-header {
            font-weight: bold;
            text-align: center;
            padding: 8px;
            border-bottom: 1px solid #ccc;
        }
        
        .pathological {
            color: #DC143C;
            font-weight: bold;
        }
        
        .normal {
            color: #008000;
        }
        
        .signature-section {
            position: absolute;
            bottom: 80px;
            right: 50px;
        }
        
        .signature-img {
            max-width: 150px;
            height: auto;
        }
        
        /* Éviter les sauts de page */
        .no-break {
            page-break-inside: avoid;
        }
        
        @page {
            margin: 1cm;
        }
        
        /* Ajout pour améliorer l'alignement */
        .results-table tr {
            border-bottom: 1px solid #f0f0f0;
        }
        
        .results-table tr:last-child {
            border-bottom: none;
        }
        
        .header-row {
            display: flex;
            justify-content: space-between;
            width: 100%;
            padding-right: 20px;
            box-sizing: border-box;
        }
    </style>
</head>
<body class="bg-white">
    <!-- En-tête avec logo, nom du labo et informations -->
    <div class="header-section">
        <!-- Logo -->
        <div class="logo">
            <img src="{{ public_path('assets/images/logo.png') }}" alt="Logo Laboratoire" class="w-full h-full object-contain">
        </div>
        
        <!-- Nom du laboratoire -->
        <div class="lab-name">
            LABORATOIRE<br>
            <span style="color: #DC143C;">La RÉFÉRENCE</span>
        </div>
        
        <!-- Barre rouge -->
        <div class="red-bar"></div>
        
        <!-- Détails du laboratoire -->
        <div class="lab-details">
            NIF:4003190741 du 30/10/18 - STAT:86903412017G.00010<br>
            RCS 2018A00156
        </div>
        
        <!-- Informations de contact -->
        <div class="contact-info">
            <strong>Tél Bureau : 261 34 53 211 41</strong><br>
            <strong>Tél Urgences : 261 34 76 637 92</strong><br>
            Mangarivotra<br>
            <strong>MAHAJANGA 401</strong>
        </div>
        
        <!-- Code QR -->
        <div class="qr-code">
            <!-- Générer le QR code avec les données de la prescription -->
            @php
                $qrData = json_encode([
                    'reference' => $prescription->reference,
                    'patient' => $patient->nom . ' ' . $patient->prenom,
                    'date' => $datePrelevement,
                    'laboratoire' => 'La Référence'
                ]);
                $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=' . urlencode($qrData);
            @endphp
            <img src="{{ $qrCodeUrl }}" alt="QR Code" class="w-full h-full">
        </div>
    </div>

    <!-- Section patient -->
    <div class="patient-section">
        <div class="patient-name">
            Résultats de : {{ $patient->nom }} {{ $patient->prenom }}
        </div>
        <div class="patient-info">
            Age: {{ $patient->age }} Ans<br>
            Réf n° {{ $prescription->reference }} du {{ $datePrelevement }}<br>
            Prescripteur: <strong>{{ strtoupper($medecin) }}</strong>
        </div>
    </div>

    <!-- Section résultats -->
    <div class="results-section">
        @foreach($examens as $examen)
            @if($examen->analyses->isNotEmpty())
            <div class="no-break">
                <!-- Titre de l'examen -->
                <div class="examen-title">
                    <div class="header-row">
                        <span>{{ strtoupper($examen->name) }}</span>
                        <span>
                        
                        </span>
                    </div>
                </div>

                <!-- Table des résultats -->
                <table class="results-table">
                    <thead>
                        <tr>
                            <th class="col-name table-header">Paramètre</th>
                            <th class="col-result table-header">Résultat</th>
                            <th class="col-ref table-header">Valeurs de référence</th>
                            <th class="col-ant table-header">Antériorité</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($examen->analyses as $analyse)
                        <!-- Analyse principale -->
                        <tr class="@if($analyse->level_value === 'PARENT') analysis-parent @endif">
                            <td class="col-name">{{ $analyse->name }}</td>
                            <td class="col-result">
                                @if($analyse->resultats->isNotEmpty())
                                    @php
                                        $resultat = $analyse->resultats->first();
                                        $interpretation = $resultat->interpretation ?? null;
                                    @endphp
                                    <span class="@if($interpretation === 'PATHOLOGIQUE') pathological @elseif($interpretation === 'NORMAL') normal @endif">
                                        {{ $resultat->valeur }}
                                        @if($analyse->unite) {{ $analyse->unite }} @endif
                                    </span>
                                @endif
                            </td>
                            <td class="col-ref">
                                @if($analyse->valeur_min && $analyse->valeur_max)
                                    {{ $analyse->valeur_min }} - {{ $analyse->valeur_max }}
                                @elseif($analyse->valeur_normal)
                                    {{ $analyse->valeur_normal }}
                                @endif
                            </td>
                            <td class="col-ant">
                                <!-- Antériorité -->
                            </td>
                        </tr>

                        <!-- Analyses enfants -->
                        @if($analyse->enfants->isNotEmpty())
                            @foreach($analyse->enfants as $enfant)
                                @if($enfant->resultats->isNotEmpty())
                                <tr class="analysis-child">
                                    <td class="col-name">{{ $enfant->name }}</td>
                                    <td class="col-result">
                                        @php
                                            $resultatEnfant = $enfant->resultats->first();
                                            $interpretationEnfant = $resultatEnfant->interpretation ?? null;
                                        @endphp
                                        <span class="@if($interpretationEnfant === 'PATHOLOGIQUE') pathological @elseif($interpretationEnfant === 'NORMAL') normal @endif">
                                            {{ $resultatEnfant->valeur }}
                                            @if($enfant->unite) {{ $enfant->unite }} @endif
                                        </span>
                                    </td>
                                    <td class="col-ref">
                                        @if($enfant->valeur_min && $enfant->valeur_max)
                                            {{ $enfant->valeur_min }} - {{ $enfant->valeur_max }}
                                        @elseif($enfant->valeur_normal)
                                            {{ $enfant->valeur_normal }}
                                        @endif
                                    </td>
                                    <td class="col-ant">
                                        <!-- Antériorité -->
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        @endforeach
    </div>

    <!-- Signature -->
    <div class="signature-section">
        <img src="{{ public_path('assets/images/signature.png') }}" alt="Signature" class="signature-img">
    </div>
</body>
</html>