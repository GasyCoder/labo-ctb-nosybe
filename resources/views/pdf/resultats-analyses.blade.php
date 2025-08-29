
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
