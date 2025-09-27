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
                @php
                    $displayValue = $resultat->display_value_pdf ?? '';
                @endphp
                {!! $displayValue !!}
            @endif
        </td>
        <td class="col-valref">
            {{ $analyse->valeur_ref ?? '' }}
        </td>

        <td class="col-anteriorite">
            @if($resultat && $resultat->anteriorite)
                @php
                    // ✅ CONSTRUCTION COMPACTE EN UNE LIGNE - TOUT EN NOIR
                    $affichageAnteriorite = $resultat->anteriorite;
                    
                    // Gestion de la date
                    $dateFormatee = '';
                    if ($resultat->anteriorite_date) {
                        if (is_string($resultat->anteriorite_date)) {
                            $dateFormatee = $resultat->anteriorite_date;
                        } elseif (is_object($resultat->anteriorite_date) && method_exists($resultat->anteriorite_date, 'format')) {
                            $dateFormatee = $resultat->anteriorite_date->format('d/m/Y');
                        } else {
                            try {
                                $dateFormatee = \Carbon\Carbon::parse($resultat->anteriorite_date)->format('d/m/Y');
                            } catch (\Exception $e) {
                                $dateFormatee = $resultat->anteriorite_date;
                            }
                        }
                    }
                    
                    // Ajouter la date entre parenthèses
                    if ($dateFormatee) {
                        $affichageAnteriorite .= ' (' . $dateFormatee . ')';
                    }
                    
                    // Gestion de la comparaison
                    $comparaison = $resultat->anteriorite_comparaison;
                    $texteComparaison = '';
                    
                    if ($comparaison) {
                        if ($comparaison['tendance'] === 'hausse') {
                            $texteComparaison = '+' . abs($comparaison['difference']);
                        } elseif ($comparaison['tendance'] === 'baisse') {
                            $texteComparaison = '-' . abs($comparaison['difference']);
                        } else {
                            $texteComparaison = '=';
                        }
                    }
                @endphp
                
                <div style="font-size: 8pt; color: #999; white-space: nowrap;">
                    {{ $affichageAnteriorite }}{{-- 
                    --}}@if($texteComparaison), {{ $texteComparaison }}@endif
                </div>
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
                    <td style="padding-left: {{ ($level + 2) * 20 }}px;?  font-size: 9pt; color: #666; font-weight: 200;">
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