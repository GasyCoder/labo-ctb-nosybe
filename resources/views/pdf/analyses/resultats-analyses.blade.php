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
            font-size: 11pt;
            color: black;
            line-height: 1.1;
        }

        .bold {
            font-weight: bold;
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

        /* Information patient */
        .patient-info {
            margin: 9px 0;
            line-height: 1.5;
            width: 100%;
        }

        /* Tables principales */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            padding: 0;
        }

        .main-table td {
            padding: 1px 0;
            line-height: 1.2;
            vertical-align: middle;
        }

        /* Ligne rouge */
        .red-line {
            border-top: 0.5px solid #002ba0ff;
            margin: 1px 0;
            width: 100%;
        }

        /* Colonnes */
        .col-designation {
            width: 40%;
            text-align: left;
            padding-right: 10px;
            font-size: 10.5pt;
        }

        .col-resultat {
            width: 20%;
            text-align: left;
            padding-left: 20px;
            font-size: 10.5pt;
        }

        .col-valref {
            width: 20%;
            text-align: left;
            padding-left: 20px;
            font-size: 10.5pt;
        }

        .col-anteriorite {
            width: 8%;
            padding-left: 10px;
            text-align: left;
            font-size: 10.5pt;
        }

        /* Styles des titres */
        .section-title {
            color: #042379ff;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header-cols {
            font-size: 8pt;
            color: #000;
        }

        /* Niveaux de hiérarchie */
        .parent-row {
            font-weight: bold;
        }

        .child-row td:first-child {
            padding-left: 20px;
        }

        .subchild-row td:first-child {
            padding-left: 40px;
        }

        /* ✅ NOUVEAUX STYLES : Antibiogrammes */
        .antibiogramme-header td {
            background-color: #f8f9fa;
            border-top: 1px solid #ccc;
            border-bottom: 1px solid #e9ecef;
            font-weight: bold;
            font-size: 10pt;
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
            height: 3px;
        }

        /* Résultats pathologiques */
        .pathologique {
            font-weight: bold;
            color: #000;
        }

        /* Styles pour conclusions */
        .conclusion-section {
            margin-top: 15px;
            margin-bottom: 10px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .conclusion-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 8px;
            color: #333;
        }

        .conclusion-content {
            font-size: 10pt;
            line-height: 1.4;
            text-align: justify;
            color: #000;
        }

        .conclusion-examen {
            margin-top: 10px;
            margin-bottom: 5px;
        }

        .conclusion-examen-title {
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 3px;
            color: #666;
        }

        .conclusion-examen-content {
            font-size: 9.5pt;
            line-height: 1.3;
            text-align: justify;
            margin-left: 10px;
        }

        .conclusion-row td {
            padding: 3px 0;
            font-size: 9pt;
            color: #666;
            font-style: italic;
        }

        /* ✅ STYLES RESPONSIVES pour les antibiotiques */
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
        {{-- Informations patient --}}
        <div class="patient-info">
            <strong>Patient :</strong> 
            {{ $prescription->patient->sexe ?? '' }} 
            {{ $prescription->patient->nom ?? 'N/A' }} 
            {{ $prescription->patient->prenom ?? '' }}<br>
            
            <strong>Âge :</strong> {{ $prescription->age ?? 'N/A' }} {{ $prescription->unite_age ?? '' }}<br>
            
            @if($prescription->patient->telephone)
            <strong>Téléphone :</strong> {{ $prescription->patient->telephone }}<br>
            @endif
            
            <strong>Prescripteur :</strong> {{ $prescription->prescripteur->nom ?? 'Non assigné' }}<br>
            <strong>Référence :</strong> {{ $prescription->reference }}<br>
            <strong>Date :</strong> {{ $prescription->created_at->format('d/m/Y') }}
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
                </table>
                
                <div class="red-line"></div>
                <div class="spacing"></div>

                {{-- Contenu des analyses --}}
                <table class="main-table">
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

                <div class="spacing"></div>
            @endif
        @endforeach

        {{-- Conclusion générale de la prescription --}}
        @if(isset($conclusion_generale) && !empty($conclusion_generale))
            <div class="conclusion-section">
                <div class="conclusion-title">Conclusion générale :</div>
                <div class="conclusion-content">
                    {!! nl2br(e($conclusion_generale)) !!}
                </div>
            </div>
        @endif

        {{-- Signature --}}
        <div class="signature">
            <img src="{{ public_path('assets/images/signature.png') }}" alt="Signature" style="max-width: 180px;">
        </div>
    </div>
</body>
</html>