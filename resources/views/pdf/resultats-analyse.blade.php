<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport d'Analyses Médicales - {{ $prescription->reference ?? 'N/A' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            background-color: #fff;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 15mm;
        }

        /* Header Styles */
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #000;
        }

        .lab-info {
            flex: 2;
        }

        .lab-name {
            font-size: 20px;
            font-weight: bold;
        }

        .lab-details, .lab-contact, .date-info {
            font-size: 12px;
        }

        .patient-info {
            flex: 1;
            text-align: right;
        }

        .report-id {
            font-weight: bold;
        }

        /* Patient Details */
        .patient-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 8px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 5px 10px;
        }

        .info-label {
            font-weight: bold;
        }

        .clinical-notes {
            grid-column: span 2;
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #ccc;
        }

        .clinical-notes .label {
            font-weight: bold;
        }

        /* Results Table */
        .results-section {
            margin-bottom: 20px;
        }

        .results-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .results-table th, .results-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        .results-table th {
            background-color: #e0e0e0;
            font-weight: bold;
        }

        .test-group {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .parent-analysis {
            font-weight: bold;
        }

        .child-analysis {
            padding-left: 20px;
            font-style: italic;
        }

        .abnormal-value {
            font-weight: bold;
        }

        .observation {
            font-style: italic;
            font-size: 12px;
            margin-top: 5px;
            padding: 8px;
            background-color: #f9f9f9;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 12px;
        }

        .signature {
            text-align: right;
            margin-top: 20px;
        }

        .signature-name {
            font-weight: bold;
        }

        .signature-title {
            font-size: 12px;
        }

        /* Print styles */
        @media print {
            .container {
                padding: 0;
                max-width: 100%;
            }
        }

        @page {
            margin: 15mm;
            size: A4;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="lab-info">
                <div class="lab-name">Laboratoire d'Analyses Médicales CTB Nosy Be</div>
                <div class="lab-details">
                    @php
                        $biologiste = $prescription->resultats->where('status', 'VALIDE')->first()?->validatedBy;
                    @endphp
                    Dr {{ $biologiste->name ?? 'NOM BIOLOGISTE' }} - Médecin Spécialiste en Biologie Clinique
                </div>
                <div class="lab-contact">
                    N° d'Agrément : {{ config('app.lab_agreement', '000/00') }} | 
                    Tél: {{ config('app.lab_phone', '+261 XX XX XX XX') }} | 
                    www.laboratoire-ctb.mg
                </div>
            </div>
            <div class="patient-info">
                <div class="report-id">Rapport N°: {{ $prescription->reference ?? 'N/A' }}</div>
                <div class="date-info">
                    Date d'émission: {{ $prescription->created_at ? $prescription->created_at->format('d/m/Y') : 'N/A' }}
                </div>
                @php
                    $dateValidation = $prescription->resultats->where('status', 'VALIDE')->max('validated_at');
                @endphp
                @if($dateValidation)
                    <div class="date-info">
                        Validation: {{ \Carbon\Carbon::parse($dateValidation)->format('d/m/Y H:i') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Patient Details -->
        <div class="patient-section">
            <div>
                <div class="section-title">Informations Patient</div>
                <div class="info-grid">
                    <span class="info-label">Nom:</span>
                    <span>{{ strtoupper($prescription->patient->nom ?? 'N/A') }} {{ strtoupper($prescription->patient->prenom ?? '') }}</span>
                    <span class="info-label">N° Dossier:</span>
                    <span>{{ $prescription->patient->numero_dossier ?? 'N/A' }}</span>
                    <span class="info-label">Âge:</span>
                    <span>{{ $prescription->age ?? 'N/A' }} {{ $prescription->unite_age ?? 'Ans' }}</span>
                    @if($prescription->patient->telephone)
                        <span class="info-label">Téléphone:</span>
                        <span>{{ $prescription->patient->telephone }}</span>
                    @endif
                </div>
            </div>
            <div>
                <div class="section-title">Détails du Prélèvement</div>
                <div class="info-grid">
                    <span class="info-label">Date prélèvement:</span>
                    <span>{{ $prescription->created_at ? $prescription->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                    <span class="info-label">Médecin traitant:</span>
                    <span>{{ $prescription->prescripteur->name ?? 'N/A' }}</span>
                    @if($prescription->prescripteur->specialite)
                        <span class="info-label">Spécialité:</span>
                        <span>{{ $prescription->prescripteur->specialite }}</span>
                    @endif
                </div>
            </div>
            @if($prescription->renseignement_clinique)
                <div class="clinical-notes">
                    <div class="label">Renseignements Cliniques:</div>
                    <div>{{ $prescription->renseignement_clinique }}</div>
                </div>
            @endif
        </div>

        <!-- Results by Analysis Group -->
        @php
            $renderedAnalyses = []; // Track rendered analysis codes to avoid duplicates
        @endphp
        @foreach($resultatsGroupes as $groupe)
            @if(!empty($groupe['resultats']))
                <div class="results-section">
                    <div class="results-title">{{ $groupe['nom'] }}</div>
                    <table class="results-table">
                        <thead>
                            <tr>
                                <th style="width: 40%;">Paramètre</th>
                                <th style="width: 15%;">Résultat</th>
                                <th style="width: 15%;">Unité</th>
                                <th style="width: 30%;">Valeurs de Référence</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupe['resultats'] as $item)
                                @php
                                    $resultat = $item['resultat'];
                                    $analyse = $item['analyse'];
                                    $type = $item['type'];
                                    $analyseCode = $analyse->code ?? $analyse->designation;

                                    // Skip if already rendered
                                    if (in_array($analyseCode, $renderedAnalyses)) {
                                        continue;
                                    }
                                    $renderedAnalyses[] = $analyseCode;

                                    // Determine CSS classes
                                    $rowClass = $type === 'parent' ? 'parent-analysis' : '';
                                    $parameterClass = $type === 'parent' ? 'parent-analysis' : ($type === 'enfant' ? 'child-analysis' : '');
                                    $resultClass = $resultat->interpretation === 'PATHOLOGIQUE' ? 'abnormal-value' : '';

                                    // Handle result display
                                    $valeurAffichee = $resultat->valeur ?? '';
                                    if ($resultat->resultats && is_array($resultat->resultats)) {
                                        $valeurAffichee = implode(', ', array_map(
                                            fn($key, $value) => "$key: $value",
                                            array_keys($resultat->resultats),
                                            $resultat->resultats
                                        ));
                                    }
                                    if ($resultat->interpretation === 'PATHOLOGIQUE' && $valeurAffichee) {
                                        $valeurAffichee .= ' *';
                                    }
                                @endphp

                                @if($type === 'parent' && !$resultat->valeur)
                                    <tr class="test-group">
                                        <td colspan="4">{{ strtoupper($analyse->designation ?? $analyse->code ?? 'N/A') }}</td>
                                    </tr>
                                @else
                                    <tr class="{{ $rowClass }}">
                                        <td class="{{ $parameterClass }}">{{ $analyse->designation ?? $analyse->code ?? 'N/A' }}</td>
                                        <td class="{{ $resultClass }}">{{ $valeurAffichee ?: '-' }}</td>
                                        <td>{{ $analyse->unite ?? '' }}</td>
                                        <td>{{ $analyse->valeur_ref ?? '-' }}</td>
                                    </tr>
                                @endif

                                @if($resultat->conclusion)
                                    <tr>
                                        <td colspan="4">
                                            <div class="observation">
                                                <strong>Observation:</strong> {{ $resultat->conclusion }}
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endforeach

        <!-- Footer -->
        <div class="footer">
            <div class="signature">
                <div class="signature-name">Dr. {{ $biologiste->name ?? 'NOM BIOLOGISTE' }}</div>
                <div class="signature-title">Médecin Spécialiste en Biologie Clinique</div>
                @if($dateValidation)
                    <div class="signature-stamp">
                        Résultat validé le {{ \Carbon\Carbon::parse($dateValidation)->format('d/m/Y') }}
                    </div>
                @endif
            </div>
            <div style="margin-top: 20px;">
                <p><strong>Laboratoire d'Analyses Médicales LA REFERENCE</strong></p>
                <p>N° d'Agrément : {{ config('app.lab_agreement', '000/00') }}</p>
                <p>Tél: {{ config('app.lab_phone', '+261 XX XX XX XX') }} | www.laboratoire-reference.mg</p>
            </div>
        </div>
    </div>
</body>
</html>