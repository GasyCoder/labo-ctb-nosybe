<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats d'Analyses - {{ $patient->nom }} {{ $patient->prenom }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        
        .patient-info {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        
        .patient-info h3 {
            margin: 0 0 10px 0;
            color: #1e40af;
            font-size: 14px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        
        .analyse-section {
            margin-bottom: 30px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .analyse-header {
            background: #3b82f6;
            color: white;
            padding: 12px 15px;
            font-weight: bold;
            font-size: 13px;
        }
        
        .analyse-content {
            padding: 15px;
        }
        
        .germe-culture {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
        }
        
        .standard-analyse {
            background: white;
            border-left: 4px solid #6b7280;
        }
        
        .bacterie-item {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 6px;
            padding: 10px;
            margin: 8px 0;
        }
        
        .antibiogramme {
            margin-top: 20px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            overflow: hidden;
        }
        
        .antibiogramme-header {
            background: #059669;
            color: white;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 12px;
        }
        
        .antibiotique-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        
        .antibiotique-table th,
        .antibiotique-table td {
            padding: 6px 8px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .antibiotique-table th {
            background: #f9fafb;
            font-weight: bold;
        }
        
        .interpretation-S { color: #059669; font-weight: bold; }
        .interpretation-I { color: #d97706; font-weight: bold; }
        .interpretation-R { color: #dc2626; font-weight: bold; }
        
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-VALIDE { background: #dcfce7; color: #166534; }
        .status-TERMINE { background: #fed7aa; color: #9a3412; }
        .status-EN_COURS { background: #dbeafe; color: #1e40af; }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 9px;
            color: #6b7280;
            text-align: center;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    {{-- En-tête --}}
    <div class="header">
        <div class="logo">LABORATOIRE CTB</div>
        <div>Résultats d'Analyses Biologiques</div>
    </div>

    {{-- Informations Patient --}}
    <div class="patient-info">
        <h3>Informations Patient</h3>
        <div class="info-grid">
            <div>
                <strong>Nom :</strong> {{ $patient->nom }} {{ $patient->prenom }}<br>
                <strong>Date de naissance :</strong> {{ $patient->date_naissance ? $patient->date_naissance->format('d/m/Y') : 'Non renseignée' }}<br>
                <strong>Sexe :</strong> {{ $patient->sexe ?? 'Non renseigné' }}
            </div>
            <div>
                <strong>Prescription N° :</strong> {{ $prescription->id }}<br>
                <strong>Date prescription :</strong> {{ $prescription->created_at->format('d/m/Y H:i') }}<br>
                <strong>Médecin :</strong> {{ $prescription->medecin ?? 'Non renseigné' }}
            </div>
        </div>
    </div>

    {{-- Analyses --}}
    @foreach($analyses as $analyse)
        <div class="analyse-section">
            <div class="analyse-header">
                {{ $analyse['code'] }} - {{ $analyse['designation'] }}
                <span class="status-badge status-{{ $analyse['status'] }}">{{ $analyse['status'] }}</span>
            </div>
            
            <div class="analyse-content {{ $analyse['type'] === 'GERME/CULTURE' ? 'germe-culture' : 'standard-analyse' }}">
                @if($analyse['type'] === 'GERME/CULTURE')
                    {{-- Affichage spécial GERME/CULTURE --}}
                    
                    @if(!empty($analyse['options_standards']))
                        <div style="margin-bottom: 15px;">
                            <strong>Options :</strong> {{ implode(', ', $analyse['options_standards']) }}
                        </div>
                    @endif

                    @if(!empty($analyse['bacteries']))
                        <div style="margin-bottom: 15px;">
                            <strong>Germes identifiés :</strong>
                            @foreach($analyse['bacteries'] as $bacterie)
                                <div class="bacterie-item">
                                    <strong>{{ $bacterie['nom'] }}</strong>
                                    <span style="color: #6b7280; font-size: 10px;">({{ $bacterie['famille'] }})</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($analyse['autre_valeur'])
                        <div style="margin-bottom: 15px;">
                            <strong>Autre :</strong> {{ $analyse['autre_valeur'] }}
                        </div>
                    @endif

                @else
                    {{-- Affichage standard --}}
                    
                    @if($analyse['valeur'])
                        <div style="margin-bottom: 10px;">
                            <strong>Valeur :</strong> {{ $analyse['valeur'] }}
                            @if($analyse['unite'])
                                <span style="color: #6b7280;">{{ $analyse['unite'] }}</span>
                            @endif
                        </div>
                    @endif

                    @if($analyse['resultats'])
                        <div style="margin-bottom: 10px;">
                            <strong>Résultats :</strong> {{ $analyse['resultats'] }}
                        </div>
                    @endif

                    @if($analyse['valeur_ref'])
                        <div style="margin-bottom: 10px; color: #059669;">
                            <strong>Valeurs de référence :</strong> {{ $analyse['valeur_ref'] }}
                        </div>
                    @endif

                @endif

                @if($analyse['interpretation'])
                    <div style="margin-bottom: 10px;">
                        <strong>Interprétation :</strong> 
                        <span style="color: {{ $analyse['interpretation'] === 'NORMAL' ? '#059669' : '#dc2626' }}; font-weight: bold;">
                            {{ $analyse['interpretation'] === 'NORMAL' ? 'Normal' : 'Pathologique' }}
                        </span>
                    </div>
                @endif

                @if($analyse['conclusion'])
                    <div style="margin-bottom: 10px;">
                        <strong>Conclusion :</strong> {{ $analyse['conclusion'] }}
                    </div>
                @endif

                @if($analyse['validated_at'])
                    <div style="font-size: 9px; color: #6b7280; margin-top: 15px;">
                        Validé le {{ $analyse['validated_at']->format('d/m/Y H:i') }}
                        @if($analyse['validated_by'])
                            par {{ $analyse['validated_by'] }}
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Antibiogrammes --}}
        @if(isset($antibiogrammes[$analyse['id']]))
            @foreach($antibiogrammes[$analyse['id']] as $antibiogramme)
                <div class="antibiogramme">
                    <div class="antibiogramme-header">
                        🦠 Antibiogramme - {{ $antibiogramme['bacterie']['nom'] }}
                        <span style="font-weight: normal; font-size: 10px;">({{ $antibiogramme['bacterie']['famille'] }})</span>
                    </div>
                    
                    @if(!empty($antibiogramme['antibiotiques']))
                        <table class="antibiotique-table">
                            <thead>
                                <tr>
                                    <th>Antibiotique</th>
                                    <th>Interprétation</th>
                                    <th>Diamètre (mm)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($antibiogramme['antibiotiques'] as $ab)
                                    <tr>
                                        <td>{{ $ab['nom'] }}</td>
                                        <td class="interpretation-{{ $ab['interpretation'] }}">
                                            {{ $ab['interpretation'] }} 
                                            ({{ $ab['interpretation'] === 'S' ? 'Sensible' : ($ab['interpretation'] === 'I' ? 'Intermédiaire' : 'Résistant') }})
                                        </td>
                                        <td>{{ $ab['diametre'] ?? '--' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    @if($antibiogramme['notes'])
                        <div style="padding: 10px; background: #f9fafb; font-size: 10px;">
                            <strong>Notes :</strong> {{ $antibiogramme['notes'] }}
                        </div>
                    @endif
                </div>
            @endforeach
        @endif
    @endforeach

    {{-- Pied de page --}}
    <div class="footer">
        <div>Document généré le {{ $metadonnees['date_generation']->format('d/m/Y à H:i') }}</div>
        <div>{{ $metadonnees['analyses_validees'] }}/{{ $metadonnees['total_analyses'] }} analyses validées</div>
        <div>Laboratoire CTB - Analyses Biologiques</div>
    </div>
</body>
</html>