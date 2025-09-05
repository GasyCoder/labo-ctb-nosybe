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
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 13px;
            line-height: 1.5;
            color: #333;
            background-color: #fff;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 15mm;
        }

        /* ===== EN-TÊTE OPTIMISÉ ===== */
        .header-logo {
            max-width: 150px;
            margin-bottom: 10px;
        }

        .red-line {
            height: 2px;
            background-color: #dc2626;
            margin: 10px 0;
        }

        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }

        .lab-info {
            flex: 2;
        }

        .lab-name {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .lab-details, .lab-contact, .date-info {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 2px;
        }

        .patient-info {
            flex: 1;
            text-align: right;
        }

        .report-id {
            font-weight: bold;
            font-size: 14px;
            color: #1f2937;
        }

        /* ===== SECTION PATIENT OPTIMISÉE ===== */
        .patient-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8fafc;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }

        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
            color: #374151;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 4px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 8px 12px;
            align-items: baseline;
        }

        .info-label {
            font-weight: 600;
            color: #4b5563;
            font-size: 12px;
        }

        .info-value {
            color: #1f2937;
            font-size: 12px;
        }

        .clinical-notes {
            grid-column: span 2;
            margin-top: 15px;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            background-color: #f9fafb;
        }

        .clinical-notes .label {
            font-weight: bold;
            color: #374151;
            margin-bottom: 6px;
        }

        /* ===== SECTION RÉSULTATS OPTIMISÉE ===== */
        .results-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .results-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 12px;
            color: #1f2937;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #dc2626;
            padding-bottom: 4px;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 12px;
        }

        .results-table th, .results-table td {
            border: 1px solid #d1d5db;
            padding: 8px 6px;
            text-align: left;
            vertical-align: top;
        }

        .results-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            color: #374151;
            font-size: 11px;
            text-transform: uppercase;
        }

        /* ===== STYLES DES LIGNES DE RÉSULTATS ===== */
        .row-parent {
            background-color: #f8fafc;
            font-weight: bold;
            color: #1f2937;
        }

        .row-enfant {
            background-color: #fff;
            font-style: italic;
            padding-left: 20px;
        }

        .row-autonome {
            background-color: #fff;
            font-weight: 500;
        }

        .row-group-header {
            background-color: #e5e7eb;
            font-weight: bold;
            color: #374151;
            text-align: center;
        }

        /* ===== VALEURS ET INTERPRÉTATIONS ===== */
        .valeur-normale {
            color: #059669;
        }

        .valeur-pathologique {
            color: #dc2626;
            font-weight: bold;
        }

        .valeur-pathologique::after {
            content: " *";
            color: #dc2626;
        }

        .interpretation-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .interpretation-normal {
            background-color: #dcfce7;
            color: #166534;
        }

        .interpretation-pathologique {
            background-color: #fecaca;
            color: #991b1b;
        }

        /* ===== OBSERVATIONS ===== */
        .observation {
            background-color: #fffbeb;
            border: 1px solid #f59e0b;
            border-radius: 4px;
            padding: 10px;
            margin-top: 8px;
            font-size: 11px;
            font-style: italic;
        }

        .observation-label {
            font-weight: bold;
            color: #92400e;
        }

        /* ===== STATISTIQUES ===== */
        .stats-summary {
            display: flex;
            justify-content: space-around;
            background-color: #f8fafc;
            padding: 10px;
            border-radius: 6px;
            margin: 15px 0;
            font-size: 11px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-weight: bold;
            font-size: 14px;
            color: #1f2937;
        }

        .stat-label {
            color: #6b7280;
            font-size: 10px;
        }

        /* ===== PIED DE PAGE ===== */
        .signature {
            text-align: right;
            margin: 30px 0 20px 0;
        }

        .signature img {
            max-width: 180px;
            height: auto;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #d1d5db;
            text-align: center;
            font-size: 11px;
            color: #6b7280;
        }

        .footer-line {
            margin-bottom: 3px;
        }

        /* ===== RESPONSIVE ET PRINT ===== */
        @media print {
            .container {
                padding: 0;
                max-width: 100%;
            }
            
            .results-section {
                page-break-inside: avoid;
            }
            
            .results-table {
                page-break-inside: auto;
            }
            
            .results-table tr {
                page-break-inside: avoid;
            }
        }

        @page {
            margin: 15mm;
            size: A4;
        }

        /* ===== UTILITAIRES ===== */
        .text-bold { font-weight: bold; }
        .text-italic { font-style: italic; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .mb-10 { margin-bottom: 10px; }
        .mt-10 { margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- ===== LOGO ET LIGNE ROUGE ===== -->
        <div class="header-section">
            <img src="{{ public_path('assets/images/logo.png') }}" alt="LABORATOIRE CTB Nosy Be" class="header-logo">
        </div>
        <div class="red-line"></div>

        <!-- ===== EN-TÊTE PRINCIPAL ===== -->
        <div class="header">
            <div class="lab-info">
                <div class="lab-name">Laboratoire d'Analyses Médicales CTB Nosy Be</div>
                <div class="lab-details">
                    @if(isset($biologiste) && $biologiste)
                        Dr {{ $biologiste->name }} - Médecin Spécialiste en Biologie Clinique
                    @else
                        Dr [NOM BIOLOGISTE] - Médecin Spécialiste en Biologie Clinique
                    @endif
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
                    Date d'émission: {{ $dates['creation'] ? $dates['creation']->format('d/m/Y') : 'N/A' }}
                </div>
                @if(isset($dates['validation']) && $dates['validation'])
                    <div class="date-info">
                        Validation: {{ $dates['validation']->format('d/m/Y H:i') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- ===== INFORMATIONS PATIENT ===== -->
        <div class="patient-section">
            <div>
                <div class="section-title">Informations Patient</div>
                <div class="info-grid">
                    <span class="info-label">Nom:</span>
                    <span class="info-value">{{ strtoupper($prescription->patient->nom ?? 'N/A') }} {{ strtoupper($prescription->patient->prenom ?? '') }}</span>
                    
                    <span class="info-label">N° Dossier:</span>
                    <span class="info-value">{{ $prescription->patient->numero_dossier ?? 'N/A' }}</span>
                    
                    <span class="info-label">Âge:</span>
                    <span class="info-value">{{ $prescription->age ?? 'N/A' }} {{ $prescription->unite_age ?? 'Ans' }}</span>
                    
                    @if($prescription->patient->telephone)
                        <span class="info-label">Téléphone:</span>
                        <span class="info-value">{{ $prescription->patient->telephone }}</span>
                    @endif
                </div>
            </div>
            
            <div>
                <div class="section-title">Détails du Prélèvement</div>
                <div class="info-grid">
                    <span class="info-label">Date prélèvement:</span>
                    <span class="info-value">{{ $dates['creation'] ? $dates['creation']->format('d/m/Y H:i') : 'N/A' }}</span>
                    
                    <span class="info-label">Médecin traitant:</span>
                    <span class="info-value">{{ $prescription->prescripteur->nom_complet ?? 'N/A' }}</span>
                    
                    @if($prescription->prescripteur->specialite)
                        <span class="info-label">Spécialité:</span>
                        <span class="info-value">{{ $prescription->prescripteur->specialite }}</span>
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

        

        <div class="red-line"></div>

        <!-- ===== RÉSULTATS PAR GROUPE ===== -->
        @foreach($resultatsGroupes as $groupe)
            @if($groupe['nombre_resultats'] > 0)
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
                                    $niveau = $item['niveau'];
                                    
                                    // Classes CSS selon le type
                                    $rowClass = match($type) {
                                        'parent' => 'row-parent',
                                        'enfant' => 'row-enfant', 
                                        'autonome' => 'row-autonome',
                                        default => 'row-autonome'
                                    };
                                    
                                    // Classes pour la valeur selon l'interprétation
                                    $valueClass = match($resultat->interpretation) {
                                        'PATHOLOGIQUE' => 'valeur-pathologique',
                                        'NORMAL' => 'valeur-normale',
                                        default => ''
                                    };
                                @endphp

                                @if($type === 'parent' && !$item['has_value'])
                                    {{-- En-tête de groupe sans valeur --}}
                                    <tr class="row-group-header">
                                        <td colspan="4">
                                            {{ strtoupper($analyse->designation ?? $analyse->code ?? 'N/A') }}
                                        </td>
                                    </tr>
                                @else
                                    {{-- Ligne normale avec résultat --}}
                                    <tr class="{{ $rowClass }}">
                                        <td style="padding-left: {{ $niveau * 15 }}px;">
                                            {{ $analyse->designation ?? $analyse->code ?? 'N/A' }}
                                            @if($resultat->interpretation && $item['has_value'])
                                                <span class="interpretation-badge interpretation-{{ strtolower($resultat->interpretation) }}">
                                                    {{ $resultat->interpretation }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="{{ $valueClass }}">
                                            {{ $item['formatted_value'] }}
                                        </td>
                                        <td>{{ $analyse->unite ?? '' }}</td>
                                        <td>{{ $analyse->valeur_ref ?? '-' }}</td>
                                    </tr>
                                @endif

                                {{-- Observation/Conclusion --}}
                                @if($resultat->conclusion)
                                    <tr>
                                        <td colspan="4">
                                            <div class="observation">
                                                <span class="observation-label">Observation:</span> 
                                                {{ $resultat->conclusion }}
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                    
                    <div class="red-line" style="height: 1px; background-color: #e5e7eb;"></div>
                </div>
            @endif
        @endforeach

        <!-- ===== SIGNATURE ===== -->
        <div class="signature">
            <img src="{{ public_path('assets/images/signature.png') }}" alt="Signature">
        </div>

        <!-- ===== PIED DE PAGE ===== -->
        <div class="footer">
            <div class="footer-line">Laboratoire d'Analyses Médicales CTB Nosy Be</div>
            <div class="footer-line">N° d'Agrément : {{ config('app.lab_agreement', '000/00') }}</div>
            <div class="footer-line">
                Tél: {{ config('app.lab_phone', '+261 XX XX XX XX') }} | 
                Email: contact@laboratoire-ctb.mg | 
                www.laboratoire-ctb.mg
            </div>
        </div>
    </div>
</body>
</html>