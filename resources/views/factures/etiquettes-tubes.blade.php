<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Étiquettes Tubes - {{ date('d/m/Y H:i') }}</title>
    <style>
        @page { 
            margin: 15mm 15mm 15mm 25mm; /* AJOUT MARGE GAUCHE PLUS GRANDE */
            size: A4;
        }
        
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        body { 
            font-family: Arial, sans-serif; 
            font-size: 0.9rem; /* DIMINUÉ de 1.2rem à 0.9rem */
            line-height: 1.3;
            color: #000;
            background: white;
        }
        
        /* En-tête de la page */
        .page-header {
            text-align: left;
            margin-bottom: 10mm; /* Réduit pour plus d'espace */
            padding-bottom: 3mm;
        }
        
        .lab-name {
            font-size: 1rem; /* DIMINUÉ de 1.6rem à 1.1rem */
            font-weight: bold;
            margin-bottom: 2mm;
             margin-left: 2mm;
        }
        
        .patient-header {
            font-size: 0.8rem; /* DIMINUÉ de 1.4rem à 1rem */
            line-height: 1;
            margin-left: 2mm;
        }
        
        .reference-header {
            font-size: 0.8rem; /* DIMINUÉ de 1.5rem à 1.1rem */
            font-weight: bold;
            margin-left: 2mm;
        }
        
        /* Container principal des étiquettes - 4 ÉTIQUETTES PAR PAGE */
        .etiquettes-container { 
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: repeat(2, 1fr); /* 2 LIGNES */
            gap: 8mm; /* AUGMENTÉ POUR ÉVITER LE COLLAGE À GAUCHE */
            width: 100%;
            height: auto;
            margin-left: 2mm; /* AJOUT MARGE GAUCHE POUR DÉCALER LES ÉTIQUETTES */
        }
        
        /* Chaque étiquette - DIMENSIONS RÉDUITES POUR 4 PAR PAGE */
        .etiquette {
            width: 80mm; /* RÉDUIT de 85mm à 80mm */
            height: 35mm; /* RÉDUIT de 45mm à 35mm */
            padding: 2mm; /* Réduit */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            page-break-inside: avoid;
            background: white;
        }
        
        /* Date et type de prélèvement en haut */
        .etiquette-header {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem; /* DIMINUÉ de 1.1rem à 0.8rem */
            margin-bottom: 1mm;
        }
        
        .date-prelev {
            font-weight: normal;
        }
        
        .type-tube {
            font-weight: bold;
            font-size: 0.9rem; /* DIMINUÉ de 1.3rem à 0.9rem */
        }
        
        /* Code-barre au centre */
        .barcode-section {
            text-align: left;
            margin: 1mm 0;
        }
        
        .barcode-image {
            height: 8mm; /* Réduit pour s'adapter */
            max-width: 40mm;
            display: block;
            margin: 0 auto 1mm auto;
        }
        
        .barcode-ascii {
            font-family: 'Courier New', monospace; 
            font-size: 1rem; /* RÉDUIT de 1.2rem à 1rem pour raccourcir */
            letter-spacing: 0.02em; /* TRÈS RÉDUIT pour compacter */
            text-align: left; 
            font-weight: bold;
            margin: 1mm 0;
        }
        
        /* Informations patient en bas */
        .patient-info {
            font-size: 0.8rem; /* DIMINUÉ de 1.1rem à 0.8rem */
            line-height: 1.2;
        }
        
        .patient-name {
            font-weight: bold;
            font-size: 0.8rem; /* DIMINUÉ de 1.25rem à 0.85rem */
        }
        
        .patient-details {
            margin-top: 0.5mm;
        }
        
        .reference-number {
            font-family: 'Courier New', monospace;
            font-size: 0.8rem; /* DIMINUÉ de 1.1rem à 0.8rem */
            font-weight: bold;
            margin-top: 0.5mm;
        }
        
        /* Optimisation impression - SANS BORDURE */
        @media print {
            body { 
                -webkit-print-color-adjust: exact;
                color-adjust: exact; 
            }
            
            .etiquette {
                background: white !important;
            }
        }
        
        /* Saut de page */
        .page-break {
            page-break-before: always;
            grid-column: 1 / -1;
        }
    </style>
</head>
<body>
    @php
        $groupedByPatient = $tubes->groupBy('prescription.patient.id');
        $pageCount = 0;
    @endphp

    @foreach($groupedByPatient as $patientId => $patientTubes)
        @php
            $firstTube = $patientTubes->first();
            $patient = $firstTube->prescription->patient;
        @endphp
        
        {{-- En-tête pour chaque patient --}}
        @if($pageCount > 0)
            <div class="page-break"></div>
        @endif
        
        <div class="page-header">
            <div class="lab-name">
                &lt;{{ strtoupper($laboratoire ?? 'LABORATOIRE CTB') }}&gt;
                <hr>
            </div>
            
            <div class="reference-header">
                {{ $firstTube->prescription->reference ?? 'N/A' }} du {{ $firstTube->created_at->format('d/m/Y') }}
            </div>
            
            <div class="patient-header">
                @php
                    // LOGIQUE POUR LES CIVILITÉS
                    $civilite = '';
                    if (isset($patient->civilite)) {
                        switch(strtolower($patient->civilite)) {
                            case 'monsieur':
                            case 'mr':
                            case 'homme':
                                $civilite = 'M';
                                break;
                            case 'madame':
                            case 'mme':
                            case 'femme':
                                $civilite = 'F';
                                break;
                            case 'enfant masculin':
                            case 'garçon':
                            case 'enfant_m':
                                $civilite = 'EM';
                                break;
                            case 'enfant féminin':
                            case 'fille':
                            case 'enfant_f':
                                $civilite = 'EF';
                                break;
                            default:
                                $civilite = strtoupper(substr($patient->civilite, 0, 1));
                        }
                    }
                @endphp
                
                <strong>{{ strtoupper($patient->nom ?? '') }} {{ ucfirst(strtolower($patient->prenom ?? '')) }}</strong><br>
                NIP: {{ $patient->numero_dossier ?? 'N/A' }}<br>
                Age: {{ $firstTube->prescription->age }} {{ $firstTube->prescription->unite_age }}
            </div>
            
            @if(isset($firstTube->prescription->prescripteur))
            <div style="margin-top: 1mm; font-size:0.8rem;margin-left: 2mm;">
                Dr. {{ $firstTube->prescription->prescripteur->nom }}
            </div>
            @endif
        </div>

        {{-- Étiquettes pour ce patient --}}
        <div class="etiquettes-container">
            @foreach($patientTubes as $index => $tube)
                <div class="etiquette">
                    {{-- En-tête de l'étiquette --}}
                    <div class="etiquette-header">
                        <span class="date-prelev">
                        {{ $tube->created_at->format('d/m/Y') }} par</span>
                        <span class="type-tube">
                            {{ strtoupper($tube->prelevement->code ?? $tube->prelevement->denomination ?? 'TUBE') }}
                            - {{ $tube->prelevement->typeTubeRecommande->code }}
                        </span>
                    </div>

                    {{-- Code-barre --}}
                    <div class="barcode-section">
                        @if(method_exists($tube, 'peutGenererCodeBarre') && $tube->peutGenererCodeBarre())
                            @php
                                try {
                                    $barcodeImage = method_exists($tube, 'genererCodeBarreImage') ? $tube->genererCodeBarreImage() : null;
                                } catch (\Exception $e) {
                                    $barcodeImage = null;
                                }
                            @endphp
                            
                            @if(!empty($barcodeImage) && $barcodeImage !== 'data:image/png;base64,' && !str_contains($barcodeImage, 'error'))
                                <img src="{{ $barcodeImage }}" 
                                     alt="Code barre {{ $tube->code_barre }}" 
                                     class="barcode-image">
                            @else
                                <div class="barcode-ascii">
                                    ||| || ||| ||
                                </div>
                            @endif
                        @else
                            <div class="barcode-ascii">
                                ||| || ||| ||
                            </div>
                        @endif
                    </div>

                    {{-- Informations patient --}}
                    <div class="patient-info">
                        @php
                            // LOGIQUE POUR LES CIVILITÉS SUR L'ÉTIQUETTE
                            $civiliteEtiquette = '';
                            if (isset($patient->civilite)) {
                                switch(strtolower($patient->civilite)) {
                                    case 'monsieur':
                                    case 'mr':
                                    case 'homme':
                                        $civiliteEtiquette = 'M';
                                        break;
                                    case 'madame':
                                    case 'mme':
                                    case 'femme':
                                        $civiliteEtiquette = 'F';
                                        break;
                                    case 'enfant masculin':
                                    case 'garçon':
                                    case 'enfant_m':
                                        $civiliteEtiquette = 'EM';
                                        break;
                                    case 'enfant féminin':
                                    case 'fille':
                                    case 'enfant_f':
                                        $civiliteEtiquette = 'EF';
                                        break;
                                    default:
                                        $civiliteEtiquette = strtoupper(substr($patient->civilite, 0, 1));
                                }
                            }
                        @endphp
                        
                        <div class="patient-name">
                            ({{ $civiliteEtiquette }}) {{ $tube->code_barre }}
                        </div>
                        <div class="patient-details">
                            {{ strtoupper($patient->nom ?? '') }} {{ ucfirst(strtolower($patient->prenom ?? '')) }} - 
                            Age: {{ $firstTube->prescription->age }} {{ $firstTube->prescription->unite_age }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        @php $pageCount++; @endphp
    @endforeach
</body>
</html>