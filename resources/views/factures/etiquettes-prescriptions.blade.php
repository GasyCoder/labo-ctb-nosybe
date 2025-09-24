<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Étiquettes Prescriptions - {{ date('d/m/Y H:i') }}</title>
    <style>
        @page { 
            margin: 15mm 10mm;
            size: A4;
        }
        
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        body { 
            font-family: Arial, sans-serif; 
            font-size: 0.9rem;
            line-height: 1.3;
            color: #000;
            background: white;
        }
        
        /* En-tête global */
        .page-header-global {
            text-align: left;
            margin-bottom: 8mm;
            padding-bottom: 3mm;
            border-bottom: 1px solid #000;
        }
        
        .lab-name-global {
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 2mm;
        }
        
        /* Section patient */
        .patient-section {
            margin: 6mm 0;
            page-break-inside: avoid;
            border: 1px solid #000;
            padding: 3mm;
        }
        
        /* En-tête patient */
        .patient-header {
            margin-bottom: 3mm;
            padding: 2mm;
            border: 1px solid #000;
            font-weight: bold;
            font-size: 0.85rem;
        }
        
        /* Ligne d'étiquettes */
        .etiquettes-ligne {
            display: table;
            width: 100%;
            margin-bottom: 3mm;
            table-layout: fixed;
        }
        
        /* Chaque étiquette */
        .etiquette-mini {
            display: table-cell;
            width: 35mm;
            height: 18mm;
            padding: 1.5mm;
            border: 1px solid #000;
            background: white;
            vertical-align: top;
            font-size: 0.65rem;
            margin-right: 2mm;
        }
        
        .etiquette-mini-header {
            text-align: center;
            font-weight: bold;
            font-size: 0.6rem;
            margin-bottom: 1mm;
            border-bottom: 1px solid #000;
            padding-bottom: 1mm;
        }
        
        .barcode-mini {
            text-align: center;
            margin: 1mm 0;
        }
        
        .barcode-image-mini {
            height: 6mm;
            max-width: 30mm;
            display: block;
            margin: 0 auto;
        }
        
        .barcode-ascii-mini {
            font-family: 'Courier New', monospace; 
            font-size: 0.7rem;
            letter-spacing: 0.02em;
            text-align: center;
            font-weight: bold;
        }
        
        .patient-info-mini {
            font-size: 0.55rem;
            line-height: 1;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 1mm;
        }
        
        .patient-name-mini {
            font-weight: bold;
            font-size: 0.6rem;
        }
        
        /* Section sans tubes */
        .prescription-sans-tubes {
            margin: 4mm 0;
            padding: 3mm;
            border: 1px solid #000;
            text-align: center;
        }
        
        .sans-tubes-header {
            font-weight: bold;
            margin-bottom: 2mm;
            text-decoration: underline;
        }
        
        .sans-tubes-info {
            font-size: 0.75rem;
        }
        
        /* Section avec analyses */
        .prescription-avec-analyses {
            margin: 4mm 0;
            padding: 3mm;
            border: 1px solid #000;
            text-align: center;
        }
        
        .avec-analyses-header {
            font-weight: bold;
            margin-bottom: 2mm;
            text-decoration: underline;
        }
        
        .avec-analyses-info {
            font-size: 0.75rem;
            text-align: left;
        }
        
        .manual-page-break {
            page-break-before: always;
        }
        
        @media print {
            * {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <!-- En-tête global -->
    <div class="page-header-global">
        <div class="lab-name-global">
            {{ strtoupper($laboratoire ?? 'LABORATOIRE CTB') }}
        </div>
        <div style="font-size: 0.8rem;">
            Étiquettes générées le {{ now()->format('d/m/Y à H:i') }}
        </div>
    </div>

    @php
        $sectionsParPage = 0;
        $maxSectionsParPage = 4;
    @endphp

    @foreach($prescriptions as $prescription)
        @php
            $patient = $prescription->patient;
            $tubes = $prescription->tubes;
            $nombreTubes = $tubes->count();
            
            // Civilité
            $civilite = '';
            if (isset($patient->civilite)) {
                switch(strtolower($patient->civilite)) {
                    case 'monsieur': case 'mr': case 'homme':
                        $civilite = 'M'; break;
                    case 'madame': case 'mme': case 'femme':
                        $civilite = 'F'; break;
                    case 'enfant masculin': case 'garçon': case 'enfant_m':
                        $civilite = 'EM'; break;
                    case 'enfant féminin': case 'fille': case 'enfant_f':
                        $civilite = 'EF'; break;
                    default:
                        $civilite = strtoupper(substr($patient->civilite, 0, 1));
                }
            }
        @endphp
        
        <!-- Saut de page si nécessaire -->
        @if($sectionsParPage >= $maxSectionsParPage)
            <div class="manual-page-break"></div>
            @php $sectionsParPage = 0; @endphp
        @endif
        
        @if($nombreTubes > 0)
            <!-- Section patient avec tubes -->
            <div class="patient-section">
                <!-- En-tête patient -->
                <div class="patient-header">
                    <div style="font-size: 0.9rem; margin-bottom: 1mm;">
                        {{ strtoupper($patient->nom ?? '') }} {{ ucfirst(strtolower($patient->prenom ?? '')) }}
                    </div>
                    <div style="font-size: 0.75rem;">
                        {{ $prescription->reference }} | NIP: {{ $patient->numero_dossier ?? 'N/A' }} | 
                        Age: {{ $prescription->age ?? 'N/A' }} {{ $prescription->unite_age ?? '' }} | 
                        {{ $prescription->created_at->format('d/m/Y') }}
                        @if(isset($prescription->prescripteur))
                            | Dr. {{ $prescription->prescripteur->nom }}
                        @endif
                    </div>
                </div>

                <!-- Étiquettes répétées 5 fois horizontalement -->
                @foreach($tubes as $tube)
                    <div class="etiquettes-ligne">
                        @for($rep = 1; $rep <= 5; $rep++)
                            <div class="etiquette-mini">
                                <!-- En-tête étiquette -->
                                <div class="etiquette-mini-header">
                                    {{ strtoupper($tube->prelevement->code ?? $tube->prelevement->denomination ?? 'TUBE') }}
                                    @if(isset($tube->prelevement->typeTubeRecommande))
                                        - {{ $tube->prelevement->typeTubeRecommande->code }}
                                    @endif
                                </div>

                                <!-- Code-barre -->
                                <div class="barcode-mini">
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
                                                 class="barcode-image-mini">
                                        @else
                                            <div class="barcode-ascii-mini">{{ $tube->code_barre ?? '||| || |||' }}</div>
                                        @endif
                                    @else
                                        <div class="barcode-ascii-mini">{{ $tube->code_barre ?? '||| || |||' }}</div>
                                    @endif
                                </div>

                                <!-- Infos patient -->
                                <div class="patient-info-mini">
                                    <div class="patient-name-mini">
                                        ({{ $civilite }}) {{ $tube->code_barre }}
                                    </div>
                                    <div>
                                        {{ strtoupper(Str::limit($patient->nom ?? '', 8)) }} {{ ucfirst(strtolower(Str::limit($patient->prenom ?? '', 6))) }}
                                    </div>
                                    <div>
                                        {{ $prescription->age ?? 'N/A' }}{{ $prescription->unite_age ?? '' }}
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                @endforeach
            </div>
            
            @php $sectionsParPage++; @endphp
            
        @elseif(isset($prescription->analyses_data) && $prescription->analyses_data->count() > 0)
            <!-- Prescription avec analyses seulement -->
            <div class="patient-section">
                <!-- En-tête patient -->
                <div class="patient-header">
                    <div style="font-size: 0.9rem; margin-bottom: 1mm;">
                        {{ strtoupper($patient->nom ?? '') }} {{ ucfirst(strtolower($patient->prenom ?? '')) }} - ANALYSES SEULEMENT
                    </div>
                    <div style="font-size: 0.75rem;">
                        {{ $prescription->reference }} | NIP: {{ $patient->numero_dossier ?? 'N/A' }} | 
                        Age: {{ $prescription->age ?? 'N/A' }} {{ $prescription->unite_age ?? '' }} | 
                        {{ $prescription->created_at->format('d/m/Y') }} | {{ $prescription->analyses_data->count() }} analyse(s)
                        @if(isset($prescription->prescripteur))
                            | Dr. {{ $prescription->prescripteur->nom }}
                        @endif
                    </div>
                </div>

                <!-- 5 étiquettes identiques pour prescription avec analyses -->
                <div class="etiquettes-ligne">
                    @for($rep = 1; $rep <= 5; $rep++)
                        <div class="etiquette-mini">
                            <!-- En-tête étiquette -->
                            <div class="etiquette-mini-header">
                                ANALYSES SEULEMENT
                            </div>

                            <!-- Info prescription -->
                            <div class="barcode-mini">
                                <div class="barcode-ascii-mini">{{ $prescription->reference }}</div>
                            </div>

                            <!-- Infos patient -->
                            <div class="patient-info-mini">
                                <div class="patient-name-mini">
                                    ({{ $civilite }}) {{ $prescription->reference }}
                                </div>
                                <div>
                                    {{ strtoupper(Str::limit($patient->nom ?? '', 8)) }} {{ ucfirst(strtolower(Str::limit($patient->prenom ?? '', 6))) }}
                                </div>
                                <div>
                                    {{ $prescription->age ?? 'N/A' }}{{ $prescription->unite_age ?? '' }}
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
            
            @php $sectionsParPage++; @endphp
            
        @else
            <!-- Prescription sans tubes ni analyses -->
            <div class="patient-section">
                <!-- En-tête patient -->
                <div class="patient-header">
                    <div style="font-size: 0.9rem; margin-bottom: 1mm;">
                        {{ strtoupper($patient->nom ?? '') }} {{ ucfirst(strtolower($patient->prenom ?? '')) }} - PRESCRIPTION VIDE
                    </div>
                    <div style="font-size: 0.75rem;">
                        {{ $prescription->reference }} | NIP: {{ $patient->numero_dossier ?? 'N/A' }} | 
                        Age: {{ $prescription->age ?? 'N/A' }} {{ $prescription->unite_age ?? '' }} | 
                        {{ $prescription->created_at->format('d/m/Y') }}
                        @if(isset($prescription->prescripteur))
                            | Dr. {{ $prescription->prescripteur->nom }}
                        @endif
                    </div>
                </div>

                <!-- 5 étiquettes identiques pour prescription vide -->
                <div class="etiquettes-ligne">
                    @for($rep = 1; $rep <= 5; $rep++)
                        <div class="etiquette-mini">
                            <!-- En-tête étiquette -->
                            <div class="etiquette-mini-header">
                                PRESCRIPTION VIDE
                            </div>

                            <!-- Info prescription -->
                            <div class="barcode-mini">
                                <div class="barcode-ascii-mini">{{ $prescription->reference }}</div>
                            </div>

                            <!-- Infos patient -->
                            <div class="patient-info-mini">
                                <div class="patient-name-mini">
                                    ({{ $civilite }}) {{ $prescription->reference }}
                                </div>
                                <div>
                                    {{ strtoupper(Str::limit($patient->nom ?? '', 8)) }} {{ ucfirst(strtolower(Str::limit($patient->prenom ?? '', 6))) }}
                                </div>
                                <div>
                                    {{ $prescription->age ?? 'N/A' }}{{ $prescription->unite_age ?? '' }}
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
            
            @php $sectionsParPage++; @endphp
        @endif
    @endforeach
</body>
</html>