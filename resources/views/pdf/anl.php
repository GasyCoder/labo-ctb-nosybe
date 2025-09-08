{{-- resources/views/pdf/analyses/resultats-analyses.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Résultats d'analyses - {{ $prescription->reference }}</title>
    <style>
        /* Reset et styles de base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: black;
            line-height: 1.1;
        }

        .bold {
            font-weight: bold !important;
        }

        /* En-tête */
        .header-section {
            width: 100%;
            display: block;
            margin: 0;
            padding: 0;
            line-height: 0;
        }

        .header-logo {
            width: 100%;
            max-height: 120px;
            object-fit: contain;
            object-position: left top;
            margin: 0;
            padding: 0;
            display: block;
        }

        /* Section contenu */
        .content-wrapper {
            padding: 0 40px;
        }

        /* Information patient - AMÉLIORATION EN DEUX COLONNES */
        .patient-info {
            margin: 15px 0;
            width: 100%;
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
            display: table;
            table-layout: fixed;
        }

        .patient-info-row {
            display: table-row;
        }

        .patient-info-left {
            display: table-cell;
            width: 50%;
            padding-right: 20px;
            vertical-align: top;
            line-height: 1.5;
        }

        .patient-info-right {
            display: table-cell;
            width: 50%;
            padding-left: 20px;
            vertical-align: top;
            line-height: 1.5;
        }

        .text-muted {
            color: #6b7280;
            font-weight: normal;
        }

        .text-fine {
            font-weight: normal;
            font-size: 9pt;
        }

        .info-label {
            color: #374151;
            font-size: 9pt;
        }

        .info-value {
            color: #111827;
            font-size: 9pt;
            margin-bottom: 2px;
        }

        .renseignement-clinique {
            margin-top: 5px;
            padding: 2px;
            font-style: italic;
            color: #4b5563;
            font-size: 8pt;
        }

        /* Tables principales */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
            padding: 0;
        }

        .main-table td {
            padding: 4px 5px;
            line-height: 1.4;
            vertical-align: top;
            border-bottom: 1px solid #0052ccff;
        }

        .main-table tr:last-child td {
            border-bottom: 1px solid #ccc;
        }

        /* Ligne rouge */
        .red-line {
            border-top: 0.5px solid #002ba0ff;
            margin: 1px 0;
            width: 100%;
        }

        /* Colonnes */
        .col-designation {
            text-align: left;
            padding-right: 15px;
            font-size: 10pt;
            vertical-align: top;
        }

        .col-resultat {
            text-align: left;
            padding-left: 10px;
            padding-right: 10px;
            font-size: 8.5pt;
            vertical-align: top;
        }

        .col-valref {
            width: 20%;
            text-align: left;
            padding-left: 10px;
            padding-right: 10px;
            font-size: 9pt;
            vertical-align: top;
        }

        .col-anteriorite {
            width: 10%;
            text-align: left;
            padding-left: 10px;
            font-size: 9pt;
            vertical-align: top;
        }

        /* Styles des titres */
        .section-title {
            color: #042379ff;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 0;
        }

        .header-cols {
            font-size: 8pt;
            color: #333;
            padding: 2px 3px;
            border-bottom: 1px solid #042379ff;
        }

        /* Niveaux de hiérarchie */
        .parent-row {
            font-weight: normal;
        }

        .parent-row td {
            /* background-color: #fafafa; */
        }

        .child-row td:first-child {
            padding-left: 25px;
        }

        .subchild-row td:first-child {
            padding-left: 45px;
        }

        /* Antibiogrammes */
        .antibiogramme-header td {
            background-color: #f8f9fa;
            border-top: 1px solid #ccc;
            border-bottom: 1px solid #e9ecef;
            font-weight: bold;
            font-size: 9pt;
            color: #333;
            padding: 6px 0 4px 0;
        }

        .antibiogramme-row td {
            padding: 2px 0;
            font-size: 9pt;
            line-height: 1.3;
        }

        .antibiogramme-row td:first-child {
            color: #666;
            font-weight: 500;
        }

        /* Styles spéciaux pour les germes */
        .germe-result {
            font-style: italic;
            font-weight: normal;
        }

        .germe-option {
            font-style: normal;
            text-transform: capitalize;
        }

        /* Styles spéciaux */
        .indent-1 {
            padding-left: 20px !important;
        }

        .indent-2 {
            padding-left: 40px !important;
        }

        /* Signature */
        .signature {
            margin-top: 20px;
            text-align: right;
            padding-right: 40px;
        }

        /* Espacement */
        .spacing {
            height: 8px;
        }

        .section-spacing {
            height: 12px;
        }

        /* Résultats pathologiques */
        .pathologique {
            font-weight: normal;
            color: #000;
        }

        /* Styles pour conclusions - COMPACT */
        .conclusion-section {
            margin-top: 8px;
            margin-bottom: 6px;
            border-top: 0.5px solid #ddd;
            padding-top: 4px;
        }

        .conclusion-title {
            font-weight: normal;
            font-size: 8pt;
            margin-bottom: 3px;
            color: #333;
        }

        .conclusion-content {
            font-size: 7pt;
            line-height: 1.2;
            text-align: justify;
            color: #000;
        }

        .conclusion-examen {
            margin-top: 4px;
            margin-bottom: 2px;
        }

        .conclusion-examen-title {
            font-weight: normal;
            font-size: 7pt;
            margin-bottom: 1px;
            color: #666;
        }

        .conclusion-examen-content {
            font-size: 7pt;
            line-height: 1.2;
            text-align: justify;
            margin-left: 6px;
        }

        .conclusion-row td {
            padding: 1px 0;
            font-size: 7pt;
            color: #666;
            font-style: italic;
        }

        /* Styles pour les antibiotiques */
        .antibiotique-sensible {
            color: #28a745;
        }

        .antibiotique-resistant {
            color: #002ba0ff;
            font-weight: bold;
        }

        .antibiotique-intermediaire {
            color: #ffc107;
            font-style: italic;
        }

        /* Séparateurs visuels */
        .section-separator {
            border-top: 1px solid #e9ecef;
            margin: 5px 0;
        }

        /* Amélioration de la lisibilité */
        .analyse-principale {
            border-bottom: 1px solid #f1f3f4;
        }

        .sous-analyse {
            background-color: #fafbfc;
        }
    </style>
</head>
<body>
    {{-- En-tête avec logo --}}
    <div class="header-section">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/images/logo.png'))) }}" alt="LABORATOIRE LA LNB" class="header-logo">
    </div>
    
    <div class="red-line"></div>
    
    <div class="content-wrapper">
        <div class="patient-info">
            <div class="patient-info-row">
                <div class="patient-info-left">
                    <div class="info-value">
                        <span class="info-label">Résultats de :</span><br>
                        <span class="text-fine">{{ $prescription->patient->civilite ?? '' }} 
                        {{ $prescription->patient->nom ?? 'N/A' }} 
                        {{ $prescription->patient->prenom ?? '' }}</span>
                    </div>
                    
                    <div class="info-value">
                        <span class="info-label">Âge :</span>
                        <span class="text-muted text-fine">{{ $prescription->age ?? 'N/A' }} {{ $prescription->unite_age ?? '' }}</span>
                    </div>
                    
                    @if($prescription->patient->telephone)
                    <div class="info-value">
                        <span class="info-label">Tél:</span>
                        <span class="text-muted text-fine">{{ $prescription->patient->telephone }}</span>
                    </div>
                    @endif
                </div>
                
                <div class="patient-info-right">
                    <div class="info-value">
                        <span class="text-fine">{{ ($prescription->prescripteur->grade ?? '') . ' ' . ($prescription->prescripteur->nom ?? 'Non assigné') }}</span>
                    </div>
                    
                    <div class="info-value">
                        <span class="info-label">Dossier n° :</span>
                        <span class="text-muted text-fine">{{ $prescription->patient->numero_dossier ?? $prescription->reference }} du {{ $prescription->created_at->format('d/m/Y') }}</span>
                    </div>
                    @if(!empty($prescription->renseignement_clinique))
                    <div class="renseignement-clinique">
                    {{ $prescription->renseignement_clinique }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Contenu des analyses par examen --}}
        @foreach($examens as $examen)
            @if($examen->analyses->isNotEmpty())
                {{-- En-tête du tableau pour chaque examen --}}
                <table class="main-table">
                    <tr>
                        <td class="col-designation section-title">{{ strtoupper($examen->name) }}</td>
                        <td class="col-resultat header-cols">Résultat</td>
                        <td class="col-valref header-cols">Val Réf</td>
                        <td class="col-anteriorite header-cols">Antériorité</td>
                    </tr>

                    {{-- Contenu des analyses --}}
                    @foreach($examen->analyses as $analyse)
                        @if($analyse->level === 'PARENT' || is_null($analyse->parent_id))
                            {{-- Analyse parent --}}
                            @include('pdf.analyses.analyse-row', ['analyse' => $analyse, 'level' => 0])
                            
                            {{-- Ses enfants --}}
                            @if($analyse->children && $analyse->children->count() > 0)
                                @include('pdf.analyses.children-analyse', ['children' => $analyse->children, 'level' => 1])
                            @endif
                        @endif
                    @endforeach
                </table>

                {{-- Conclusions pour cet examen --}}
                @if(isset($examen->conclusions) && $examen->conclusions->isNotEmpty())
                    <div class="conclusion-examen">
                        <div class="conclusion-examen-title">Conclusion :</div>
                        @foreach($examen->conclusions as $conclusion)
                            <div class="conclusion-examen-content">
                                {!! nl2br(e($conclusion['conclusion'])) !!}
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="section-spacing"></div>
            @endif
        @endforeach

        {{-- Conclusion générale de la prescription --}}
        {{-- @if(isset($conclusion_generale) && !empty($conclusion_generale))
            <div class="conclusion-section">
                <div class="conclusion-title">Conclusion générale :</div>
                <div class="conclusion-content">
                    {!! nl2br(e($conclusion_generale)) !!}
                </div>
            </div>
        @endif --}}

        {{-- Signature --}}
        <div class="signature">
            <img src="{{ public_path('assets/images/signature.png') }}" alt="Signature" style="max-width: 180px;">
        </div>
    </div>
</body>
</html>