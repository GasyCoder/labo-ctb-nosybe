{{-- resources/views/pdf/analyses/analyse-row.blade.php --}}
@php
    $resultat = $analyse->resultats->first();
    $hasResult = $resultat && ($resultat->valeur || $resultat->resultats);
    $isPathologique = $resultat && $resultat->est_pathologique;
    $isInfoLine = !$hasResult && $analyse->designation && ($analyse->prix == 0 || $analyse->level === 'PARENT');
    
    // Vérifier la présence d'antibiogrammes
    $hasAntibiogrammes = $analyse->has_antibiogrammes;
@endphp

@if($hasResult || $isInfoLine || $hasAntibiogrammes)
    <tr class="{{ $level === 0 ? 'parent-row' : 'child-row' }}">
        <td class="col-designation {{ ($analyse->level === 'PARENT' || $analyse->is_bold) ? 'bold' : '' }}"
            @if($level > 0) style="padding-left: {{ $level * 20 }}px;" @endif>
            {{ $analyse->designation }}
        </td>
        <td class="col-resultat">
            @if($hasResult)
                @if(method_exists($resultat, 'isGermeType') && ($resultat->isGermeType() || $resultat->isCultureType()))
                    {{-- GERMES : Affichage spécial pour GERME/CULTURE --}}
                    @php 
                        $selectedOptions = $resultat->resultats_pdf ?? $resultat->resultats;
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
                    @elseif($selectedOptions)
                        {{ $selectedOptions }}
                    @elseif($autreValeur)
                        <i>{{ $autreValeur }}</i>
                    @endif
                    
                @elseif(method_exists($resultat, 'isLeucocytesType') && $resultat->isLeucocytesType())
                    {{-- LEUCOCYTES : Utiliser l'accessor du modèle --}}
                    @php $leucoData = $resultat->leucocytes_data ?? null; @endphp
                    @if($leucoData && isset($leucoData['valeur']))
                        {{ $leucoData['valeur'] }} /mm³
                    @endif
                @else
                    {{-- AUTRES TYPES --}}
                    @if(isset($resultat->display_value_pdf))
                        {!! $resultat->display_value_pdf !!}
                    @else
                        @php
                            $displayValue = '';
                            
                            if ($analyse->type && $analyse->type->name === 'SELECT_MULTIPLE') {
                                $resultatsArray = $resultat->resultats_pdf ?? $resultat->resultats;
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
                                $displayValue = $resultat->valeur_pdf ?? $resultat->valeur ?? $resultat->resultats_pdf ?? $resultat->resultats;
                                if (is_array($displayValue)) {
                                    $displayValue = implode(', ', $displayValue);
                                }
                            }
                            
                            // Ajouter les unités si nécessaire
                            if ($displayValue && $analyse->unite && !str_contains($displayValue, $analyse->unite)) {
                                $displayValue .= ' ' . $analyse->unite;
                            }
                            
                            if ($displayValue && isset($analyse->suffixe) && $analyse->suffixe && !str_contains($displayValue, $analyse->suffixe)) {
                                $displayValue .= ' ' . $analyse->suffixe;
                            }
                            
                            if ($resultat->est_pathologique && $displayValue) {
                                $displayValue = '<strong>' . $displayValue . '</strong>';
                            }
                        @endphp
                        
                        {!! $displayValue !!}
                    @endif
                @endif
            @endif
        </td>
        <td class="col-valref">
            {{ $analyse->valeur_ref ?? '' }}
        </td>
        <td class="col-anteriorite">
            @if($resultat && isset($resultat->antecedent))
                {{ $resultat->antecedent }}
            @endif
        </td>
    </tr>

    {{-- Sous-détails pour LEUCOCYTES --}}
    @if($hasResult && method_exists($resultat, 'isLeucocytesType') && $resultat->isLeucocytesType())
        @php $leucoData = $resultat->leucocytes_data ?? null; @endphp
        @if($leucoData && (isset($leucoData['polynucleaires']) || isset($leucoData['lymphocytes'])))
            @if(isset($leucoData['polynucleaires']))
                <tr class="subchild-row">
                    <td class="col-designation" style="padding-left: {{ ($level + 1) * 20 }}px;">Polynucléaires</td>
                    <td class="col-resultat">{{ $leucoData['polynucleaires'] }}%</td>
                    <td class="col-valref"></td>
                    <td class="col-anteriorite"></td>
                </tr>
            @endif
            
            @if(isset($leucoData['lymphocytes']))
                <tr class="subchild-row">
                    <td class="col-designation" style="padding-left: {{ ($level + 1) * 20 }}px;">Lymphocytes</td>
                    <td class="col-resultat">{{ $leucoData['lymphocytes'] }}%</td>
                    <td class="col-valref"></td>
                    <td class="col-anteriorite"></td>
                </tr>
            @endif
        @endif
    @endif

    {{-- Afficher les antibiogrammes --}}
    @if($hasAntibiogrammes && $analyse->antibiogrammes)
        @foreach($analyse->antibiogrammes as $antibiogramme)
            {{-- En-tête de l'antibiogramme --}}
            <tr class="antibiogramme-header">
                <td colspan="4" style="padding: 8px 0 4px {{ ($level + 1) * 20 }}px; font-weight: bold; font-size: 10pt; color: #333;">
                    Antibiogramme - <i>{{ $antibiogramme->bacterie->designation ?? 'Bactérie inconnue' }}</i>
                    @if(isset($antibiogramme->bacterie->famille) && $antibiogramme->bacterie->famille)
                        ({{ $antibiogramme->bacterie->famille->designation }})
                    @endif
                </td>
            </tr>

            {{-- Antibiotiques sensibles (S) --}}
            @if($antibiogramme->antibiotiques_sensibles->isNotEmpty())
                <tr class="antibiogramme-row">
                    <td style="padding-left: {{ ($level + 2) * 20 }}px; font-size: 9pt; color: #666; font-weight: 200;">
                        Sensible :
                    </td>
                    <td colspan="3" style="font-size: 9pt; color: #28a745;">
                        @foreach($antibiogramme->antibiotiques_sensibles as $resultatAb)
                            {{ $resultatAb->antibiotique->designation ?? 'N/A' }}
                            @if($resultatAb->diametre_mm) ({{ $resultatAb->diametre_mm }}mm) @endif
                            @if(!$loop->last), @endif
                        @endforeach
                    </td>
                </tr>
            @endif

            {{-- Antibiotiques résistants (R) --}}
            @if($antibiogramme->antibiotiques_resistants->isNotEmpty())
                <tr class="antibiogramme-row">
                    <td style="padding-left: {{ ($level + 2) * 20 }}px; font-size: 9pt; color: #666; font-weight: 200;">
                        Résistant :
                    </td>
                    <td colspan="3" style="font-size: 9pt; font-weight: bold; color: #dc3545;">
                        @foreach($antibiogramme->antibiotiques_resistants as $resultatAb)
                            {{ $resultatAb->antibiotique->designation ?? 'N/A' }}
                            @if($resultatAb->diametre_mm) ({{ $resultatAb->diametre_mm }}mm) @endif
                            @if(!$loop->last), @endif
                        @endforeach
                    </td>
                </tr>
            @endif

            {{-- Antibiotiques intermédiaires (I) --}}
            @if($antibiogramme->antibiotiques_intermediaires->isNotEmpty())
                <tr class="antibiogramme-row">
                    <td style="padding-left: {{ ($level + 2) * 20 }}px; font-size: 9pt; color: #666; font-weight: 200;">
                        Intermédiaire :
                    </td>
                    <td colspan="3" style="font-size: 9pt; font-style: italic; color: #ffc107;">
                        @foreach($antibiogramme->antibiotiques_intermediaires as $resultatAb)
                            {{ $resultatAb->antibiotique->designation ?? 'N/A' }}
                            @if($resultatAb->diametre_mm) ({{ $resultatAb->diametre_mm }}mm) @endif
                            @if(!$loop->last), @endif
                        @endforeach
                    </td>
                </tr>
            @endif

            {{-- Notes de l'antibiogramme --}}
            @if(isset($antibiogramme->notes) && $antibiogramme->notes)
                <tr class="antibiogramme-row">
                    <td style="padding-left: {{ ($level + 2) * 20 }}px; font-size: 9pt; color: #666; font-weight: 500;">
                        Notes :
                    </td>
                    <td colspan="3" style="font-size: 9pt; font-style: italic;">
                        {{ $antibiogramme->notes }}
                    </td>
                </tr>
            @endif
        @endforeach
    @endif

    {{-- CONCLUSION spécifique du résultat --}}
    @if($hasResult && $resultat && isset($resultat->conclusion) && !empty($resultat->conclusion))
        <tr class="conclusion-row">
            <td style="padding-left: {{ ($level + 1) * 20 }}px; font-size: 9pt; color: #666; font-style: italic;">
                Conclusion :
            </td>
            <td colspan="3" style="font-size: 9pt; line-height: 1.3;">
                {!! nl2br(e($resultat->conclusion)) !!}
            </td>
        </tr>
    @endif

    {{-- Traiter les enfants --}}
    @if($analyse->children && $analyse->children->isNotEmpty())
        @foreach($analyse->children as $child)
            @include('pdf.analyses.analyse-row', ['analyse' => $child, 'level' => $level + 1])
        @endforeach
    @endif
@endif