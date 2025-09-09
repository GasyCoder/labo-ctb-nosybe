{{-- resources/views/pdf/analyses/analyse-row.blade.php - VERSION COMPLÈTE CORRIGÉE --}}
@php
    $resultat = $analyse->resultats->first();
    $hasResult = $resultat && ($resultat->valeur || $resultat->resultats);
    $isPathologique = $resultat && $resultat->est_pathologique;
    $isInfoLine = !$hasResult && $analyse->designation && ($analyse->prix == 0 || $analyse->level === 'PARENT');
    
    // Vérifier la présence d'antibiogrammes
    $hasAntibiogrammes = $analyse->has_antibiogrammes ?? false;
@endphp

@if($hasResult || $isInfoLine || $hasAntibiogrammes || $analyse->designation)
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
                    {{-- GESTION COMPLÈTE DE TOUS LES TYPES D'ANALYSES --}}
                    @php
                        $displayValue = '';
                        $analyseType = $analyse->type->name ?? '';
                        
                        switch($analyseType) {
                            // TYPES DE SAISIE SIMPLE
                            case 'INPUT':
                            case 'DOSAGE':
                            case 'COMPTAGE':
                                $displayValue = $resultat->valeur;
                                break;
                                
                            case 'INPUT_SUFFIXE':
                                $displayValue = $resultat->valeur;
                                break;
                                
                            // TYPES DE SÉLECTION
                            case 'SELECT':
                            case 'TEST':
                                $displayValue = $resultat->resultats ?: $resultat->valeur;
                                break;
                                
                            case 'SELECT_MULTIPLE':
                                $resultatsArray = $resultat->resultats_pdf ?? $resultat->resultats;
                                if (is_array($resultatsArray)) {
                                    $displayValue = implode(', ', $resultatsArray);
                                } else {
                                    $displayValue = $resultatsArray;
                                }
                                break;
                                
                            // TYPES NÉGATIF/POSITIF
                            case 'NEGATIF_POSITIF_1':
                                $displayValue = $resultat->valeur;
                                break;
                                
                            case 'NEGATIF_POSITIF_2':
                                $displayValue = $resultat->valeur; // NEGATIF ou POSITIF
                                if ($resultat->valeur === 'POSITIF' && $resultat->resultats) {
                                    $displayValue .= ' (' . $resultat->resultats . ')';
                                }
                                break;
                                
                            case 'NEGATIF_POSITIF_3':
                                $displayValue = $resultat->valeur;
                                if ($resultat->resultats) {
                                    if (is_array($resultat->resultats)) {
                                        $resultatsStr = implode(', ', $resultat->resultats);
                                        $displayValue .= ' (' . $resultatsStr . ')';
                                    } else {
                                        $displayValue .= ' (' . $resultat->resultats . ')';
                                    }
                                }
                                break;
                                
                            // TYPES ABSENCE/PRÉSENCE
                            case 'ABSENCE_PRESENCE_2':
                                $displayValue = $resultat->valeur;
                                if ($resultat->resultats) {
                                    $displayValue .= ' (' . $resultat->resultats . ')';
                                }
                                break;
                                
                            // FLORE VAGINALE
                            case 'FV':
                                if ($resultat->resultats) {
                                    $displayValue = $resultat->resultats;
                                    
                                    if ($resultat->valeur && in_array($resultat->resultats, [
                                        'Flore vaginale équilibrée',
                                        'Flore vaginale intermédiaire', 
                                        'Flore vaginale déséquilibrée'
                                    ])) {
                                        $displayValue .= ' (Score de Nugent: ' . $resultat->valeur . ')';
                                    } elseif ($resultat->resultats === 'Autre' && $resultat->valeur) {
                                        $displayValue = $resultat->valeur;
                                    }
                                } elseif ($resultat->valeur) {
                                    $displayValue = $resultat->valeur;
                                }
                                break;
                                
                            // LABEL
                            case 'LABEL':
                                $displayValue = '';
                                break;
                                
                            // FALLBACK POUR TYPES INCONNUS
                            default:
                                if ($resultat->resultats) {
                                    if (is_array($resultat->resultats)) {
                                        $displayValue = implode(', ', $resultat->resultats);
                                    } else {
                                        $displayValue = $resultat->resultats;
                                    }
                                    
                                    if ($resultat->resultats === 'Autre' && $resultat->valeur) {
                                        $displayValue = $resultat->valeur;
                                    }
                                } elseif ($resultat->valeur) {
                                    $displayValue = $resultat->valeur;
                                }
                                break;
                        }
                        
                        // POST-TRAITEMENT COMMUN
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

    {{-- AFFICHAGE ANTIBIOGRAMMES --}}
    @if($hasAntibiogrammes && isset($analyse->antibiogrammes))
        @foreach($analyse->antibiogrammes as $antibiogramme)
            <tr class="antibiogramme-header">
                <td colspan="4" style="padding: 8px 0 4px {{ ($level + 1) * 20 }}px; font-weight: bold; font-size: 10pt; color: #333;">
                    Antibiogramme - <i>{{ $antibiogramme->bacterie->designation ?? 'Bactérie inconnue' }}</i>
                    @if(isset($antibiogramme->bacterie->famille) && $antibiogramme->bacterie->famille)
                        ({{ $antibiogramme->bacterie->famille->designation }})
                    @endif
                </td>
            </tr>

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
               {!! nl2br(e($resultat->conclusion)) !!}
            </td>
            {{-- <td colspan="3" style="font-size: 9pt; line-height: 1.3;">
                {!! nl2br(e($resultat->conclusion)) !!}
            </td> --}}
        </tr>
    @endif
@endif