{{-- resources/views/pdf/analyses/analyse-row.blade.php --}}
@php
    $resultat = $analyse->resultats->first();
    $hasResult = $resultat && ($resultat->valeur || $resultat->resultats);
    $isPathologique = $resultat && $resultat->est_pathologique;
    $isInfoLine = !$analyse->result_disponible && $analyse->designation && ($analyse->prix == 0 || $analyse->level === 'PARENT');
    $hasAntibiogrammes = isset($analyse->antibiogrammes) && $analyse->antibiogrammes->isNotEmpty();
@endphp

{{-- Afficher seulement si on a un résultat ou si c'est une ligne informative --}}
@if($hasResult || $isInfoLine)
    <tr class="{{ $level === 0 ? 'parent-row' : 'child-row' }}">
        <td class="col-designation {{ ($analyse->level === 'PARENT' || $analyse->is_bold) ? 'bold' : '' }}"
            @if($level > 0) style="padding-left: {{ $level * 20 }}px;" @endif>
            {{ $analyse->designation }}
        </td>
        <td class="col-resultat">
            @if($hasResult)
                @if($resultat->isGermeType() || $resultat->isCultureType())
                    {{-- ✅ GERMES : Affichage spécial pour GERME/CULTURE --}}
                    @php 
                        $selectedOptions = $resultat->resultats_pdf;
                        $autreValeur = $resultat->valeur;
                    @endphp
                    
                    @if(is_array($selectedOptions))
                        @foreach($selectedOptions as $option)
                            @if($option === 'Autre' && $autreValeur)
                                <i>{{ $autreValeur }}</i>
                            @else
                                {{ ucfirst(str_replace('-', ' ', $option)) }}
                            @endif
                            @if(!$loop->last), @endif
                        @endforeach
                    @else
                        {{ $selectedOptions }}
                    @endif
                    
                @elseif($resultat->isLeucocytesType())
                    {{-- LEUCOCYTES : Utiliser l'accessor du modèle --}}
                    @php $leucoData = $resultat->leucocytes_data; @endphp
                    @if($leucoData && isset($leucoData['valeur']))
                        {{ $leucoData['valeur'] }} /mm³
                    @endif
                @else
                    {{-- AUTRES TYPES --}}
                    @php
                        $displayValue = '';
                        
                        if ($analyse->type && $analyse->type->name === 'SELECT_MULTIPLE') {
                            $resultatsArray = $resultat->resultats_pdf;
                            if (is_array($resultatsArray)) {
                                $displayValue = implode(', ', $resultatsArray);
                            }
                        } elseif ($analyse->type && $analyse->type->name === 'NEGATIF_POSITIF_3') {
                            if ($resultat->resultats === 'Positif' && $resultat->valeur) {
                                $displayValue = $resultat->resultats;
                                $values = is_array($resultat->valeur) ? 
                                    implode(', ', $resultat->valeur) : 
                                    $resultat->valeur;
                                $displayValue .= ' (' . $values . ')';
                            } else {
                                $displayValue = $resultat->resultats ?: $resultat->valeur;
                            }
                        } elseif ($analyse->type && $analyse->type->name === 'FV') {
                            if ($resultat->resultats) {
                                $displayValue = $resultat->resultats;
                                if ($resultat->valeur && in_array($resultat->resultats, [
                                    'Flore vaginale équilibrée',
                                    'Flore vaginale intermédiaire', 
                                    'Flore vaginale déséquilibrée'
                                ])) {
                                    $displayValue .= ' (Score de Nugent: ' . $resultat->valeur . ')';
                                }
                            }
                        } else {
                            $displayValue = $resultat->valeur_pdf ?: $resultat->resultats_pdf;
                        }
                        
                        if ($resultat->est_pathologique && $displayValue) {
                            $displayValue = '<strong>' . $displayValue . '</strong>';
                        }
                    @endphp
                    
                    {!! $displayValue !!}
                @endif
            @endif
        </td>
        <td class="col-valref">
            {{ $analyse->valeur_ref ?? '' }}
        </td>
        <td class="col-anteriorite">
            {{-- Antécédent si disponible --}}
        </td>
    </tr>

    {{-- ✅ ANTIBIOGRAMMES : Afficher les antibiogrammes s'il y en a --}}
    @if($hasAntibiogrammes)
        @foreach($analyse->antibiogrammes as $antibiogramme)
            <tr class="antibiogramme-header">
                <td colspan="4" style="padding: 8px 0 4px {{ ($level + 1) * 20 }}px; font-weight: bold; font-size: 10pt; color: #333; border-top: 1px solid #ccc;">
                    Antibiogramme - <i>{{ $antibiogramme->bacterie->designation ?? 'Bactérie inconnue' }}</i>
                    @if($antibiogramme->bacterie && $antibiogramme->bacterie->famille)
                        ({{ $antibiogramme->bacterie->famille->designation }})
                    @endif
                </td>
            </tr>

            {{-- Antibiotiques sensibles --}}
            @if($antibiogramme->antibiotiques_sensibles->isNotEmpty())
                <tr class="antibiogramme-row">
                    <td style="padding-left: {{ ($level + 2) * 20 }}px; font-size: 9pt; color: #666;">
                        Sensible :
                    </td>
                    <td colspan="3" style="font-size: 9pt;">
                        @foreach($antibiogramme->antibiotiques_sensibles as $resultatAb)
                            {{ $resultatAb->antibiotique->designation ?? 'N/A' }}@if(!$loop->last), @endif
                        @endforeach
                    </td>
                </tr>
            @endif

            {{-- Antibiotiques résistants --}}
            @if($antibiogramme->antibiotiques_resistants->isNotEmpty())
                <tr class="antibiogramme-row">
                    <td style="padding-left: {{ ($level + 2) * 20 }}px; font-size: 9pt; color: #666;">
                        Résistant :
                    </td>
                    <td colspan="3" style="font-size: 9pt; font-weight: bold;">
                        @foreach($antibiogramme->antibiotiques_resistants as $resultatAb)
                            {{ $resultatAb->antibiotique->designation ?? 'N/A' }}@if(!$loop->last), @endif
                        @endforeach
                    </td>
                </tr>
            @endif

            {{-- Antibiotiques intermédiaires --}}
            @if($antibiogramme->antibiotiques_intermediaires->isNotEmpty())
                <tr class="antibiogramme-row">
                    <td style="padding-left: {{ ($level + 2) * 20 }}px; font-size: 9pt; color: #666;">
                        Intermédiaire :
                    </td>
                    <td colspan="3" style="font-size: 9pt; font-style: italic;">
                        @foreach($antibiogramme->antibiotiques_intermediaires as $resultatAb)
                            {{ $resultatAb->antibiotique->designation ?? 'N/A' }}@if(!$loop->last), @endif
                        @endforeach
                    </td>
                </tr>
            @endif
        @endforeach
    @endif
@endif