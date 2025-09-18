<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Étiquettes Tubes - {{ date('d/m/Y H:i') }}</title>
    <style>
        @page { 
            margin: 15mm 15mm 15mm 25mm;
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
        
        /* En-tête global de la page */
        .page-header-global {
            text-align: left;
            margin-bottom: 8mm;
            padding-bottom: 3mm;
            border-bottom: 1px solid #ddd;
        }
        
        .lab-name-global {
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 2mm;
            margin-left: 2mm;
        }
        
        /* Container principal - FLUX CONTINU */
        .etiquettes-container { 
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 6mm;
            width: 100%;
            margin-left: 2mm;
        }
        
        /* Section patient compacte */
        .patient-section {
            grid-column: 1 / -1; /* S'étend sur toute la largeur */
            margin: 4mm 0 2mm 0;
            padding: 2mm;
            background-color: #f8f9fa;
            border-left: 3px solid #007bff;
            page-break-inside: avoid;
        }
        
        .patient-info-header {
            font-size: 0.75rem;
            line-height: 1.2;
            color: #333;
        }
        
        .patient-name-header {
            font-weight: bold;
            font-size: 0.8rem;
        }
        
        .reference-header {
            font-size: 0.75rem;
            font-weight: bold;
            color: #666;
        }
        
        /* Chaque étiquette */
        .etiquette {
            width: 80mm;
            height: 35mm;
            padding: 2mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            page-break-inside: avoid;
            background: white;
            border: 1px solid #eee;
            margin-bottom: 2mm;
        }
        
        /* En-tête de l'étiquette */
        .etiquette-header {
            display: flex;
            justify-content: space-between;
            font-size: 0.7rem;
            margin-bottom: 1mm;
        }
        
        .date-prelev {
            font-weight: normal;
        }
        
        .type-tube {
            font-weight: bold;
            font-size: 0.75rem;
        }
        
        /* Code-barre */
        .barcode-section {
            text-align: left;
            margin: 1mm 0;
        }
        
        .barcode-image {
            height: 8mm;
            max-width: 40mm;
            display: block;
            margin: 0 auto 1mm auto;
        }
        
        .barcode-ascii {
            font-family: 'Courier New', monospace; 
            font-size: 0.9rem;
            letter-spacing: 0.02em;
            text-align: left; 
            font-weight: bold;
            margin: 1mm 0;
        }
        
        /* Informations patient sur étiquette */
        .patient-info {
            font-size: 0.7rem;
            line-height: 1.2;
        }
        
        .patient-name {
            font-weight: bold;
            font-size: 0.75rem;
        }
        
        .patient-details {
            margin-top: 0.5mm;
        }
        
        /* Saut de page manuel seulement quand nécessaire */
        .manual-page-break {
            page-break-before: always;
            grid-column: 1 / -1;
        }
        
        /* Optimisation impression */
        @media print {
            body { 
                -webkit-print-color-adjust: exact;
                color-adjust: exact; 
            }
            
            .etiquette {
                background: white !important;
                border: 1px solid #ddd !important;
            }
            
            .patient-section {
                border-left: 3px solid #007bff !important;
            }
        }
    </style>
</head>
<body>
    {{-- En-tête global une seule fois --}}
    <div class="page-header-global">
        <div class="lab-name-global">
            &lt;{{ strtoupper($laboratoire ?? 'LABORATOIRE CTB') }}&gt;
        </div>
        <div style="font-size: 0.8rem; color: #666; margin-left: 2mm;">
            Étiquettes générées le {{ now()->format('d/m/Y à H:i') }}
        </div>
    </div>

    {{-- Container principal avec flux continu --}}
    <div class="etiquettes-container">
        @php
            $groupedByPatient = $tubes->groupBy('prescription.patient.id');
            $totalEtiquettes = 0;
            $etiquettesParPage = 8; // 4 lignes x 2 colonnes
        @endphp

        @foreach($groupedByPatient as $patientId => $patientTubes)
            @php
                $firstTube = $patientTubes->first();
                $patient = $firstTube->prescription->patient;
                $nombreTubesPatient = $patientTubes->count();
            @endphp
            
            {{-- SAUT DE PAGE INTELLIGENT : seulement si on dépasse la capacité de la page --}}
            @if($totalEtiquettes > 0 && ($totalEtiquettes + $nombreTubesPatient) > $etiquettesParPage)
                <div class="manual-page-break"></div>
                @php $totalEtiquettes = 0; @endphp
            @endif
            
            {{-- En-tête patient compact --}}
            <div class="patient-section">
                <div class="patient-info-header">
                    @php
                        // Logique des civilités
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
                    
                    <div class="reference-header">
                        {{ $firstTube->prescription->reference ?? 'N/A' }} - {{ $firstTube->created_at->format('d/m/Y') }}
                    </div>
                    
                    <div class="patient-name-header">
                        {{ strtoupper($patient->nom ?? '') }} {{ ucfirst(strtolower($patient->prenom ?? '')) }}
                        | NIP: {{ $patient->numero_dossier ?? 'N/A' }}
                        | Âge: {{ $firstTube->prescription->age }} {{ $firstTube->prescription->unite_age }}
                    </div>
                    
                    @if(isset($firstTube->prescription->prescripteur))
                    <div style="margin-top: 1mm; font-size: 0.7rem; color: #666;">
                        Dr. {{ $firstTube->prescription->prescripteur->nom }}
                    </div>
                    @endif
                </div>
            </div>

            {{-- Étiquettes pour ce patient (flux normal) --}}
            @foreach($patientTubes as $index => $tube)
                <div class="etiquette">
                    {{-- En-tête de l'étiquette --}}
                    <div class="etiquette-header">
                        <span class="date-prelev">{{ $tube->created_at->format('d/m/Y') }}</span>
                        <span class="type-tube">
                            {{ strtoupper($tube->prelevement->code ?? $tube->prelevement->denomination ?? 'TUBE') }}
                            @if(isset($tube->prelevement->typeTubeRecommande))
                                - {{ $tube->prelevement->typeTubeRecommande->code }}
                            @endif
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
                                <div class="barcode-ascii">||| || ||| ||</div>
                            @endif
                        @else
                            <div class="barcode-ascii">||| || ||| ||</div>
                        @endif
                    </div>

                    {{-- Informations patient sur l'étiquette --}}
                    <div class="patient-info">
                        <div class="patient-name">
                            ({{ $civilite }}) {{ $tube->code_barre }}
                        </div>
                        <div class="patient-details">
                            {{ strtoupper($patient->nom ?? '') }} {{ ucfirst(strtolower($patient->prenom ?? '')) }} - 
                            {{ $firstTube->prescription->age }}{{ $firstTube->prescription->unite_age }}
                        </div>
                    </div>
                </div>
                
                @php $totalEtiquettes++; @endphp
            @endforeach
        @endforeach
    </div>
</body>
</html>