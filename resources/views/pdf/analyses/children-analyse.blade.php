{{-- resources/views/pdf/analyses/children-analyse.blade.php --}}
@foreach($children as $child)
    @php
        $resultat = $child->resultats->first();
        $hasResult = $resultat && ($resultat->valeur || $resultat->resultats);
        $isPathologique = $resultat && $resultat->est_pathologique;
        $isInfoLine = !$hasResult && $child->designation;
        $hasAntibiogrammes = isset($child->antibiogrammes) && $child->antibiogrammes->isNotEmpty();
    @endphp

    @if($hasResult || $isInfoLine)
        <tr class="child-row">
            <td class="col-designation {{ $child->is_bold ? 'bold' : '' }}"
                style="padding-left: {{ $level * 20 }}px;">
                {{ $child->designation }}
            </td>
            <td class="col-resultat">
                @if($hasResult)
                    @if($resultat->isGermeType() || $resultat->isCultureType())
                        {{-- GERMES : Affichage spécial pour GERME/CULTURE --}}
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
                        {{-- LEUCOCYTES --}}
                        @php $leucoData = $resultat->leucocytes_data; @endphp
                        @if($leucoData)
                            {{ $leucoData['valeur'] ?? '' }} /mm³
                            
                            @if(isset($leucoData['polynucleaires']) || isset($leucoData['lymphocytes']))
                                </td><td class="col-valref"></td><td class="col-anteriorite"></td></tr>
                                
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
                                
                                <tr><td colspan="4" style="display:none;">
                            @endif
                        @endif
                    @else
                        {{-- AUTRES TYPES --}}
                        @if($resultat->display_value_pdf)
                            {!! $resultat->display_value_pdf !!}
                        @else
                            @php
                                $displayValue = $resultat->valeur_pdf ?: $resultat->resultats_pdf;
                                if (is_array($displayValue)) {
                                    $displayValue = implode(', ', $displayValue);
                                }
                                
                                if ($resultat->est_pathologique && $displayValue) {
                                    $displayValue = '<strong>' . $displayValue . '</strong>';
                                }
                            @endphp
                            {!! $displayValue !!}
                        @endif
                        
                        @if($child->unite && !str_contains($resultat->display_value_pdf ?? '', $child->unite))
                            {{ $child->unite }}
                        @endif
                        
                        @if($child->suffixe && !str_contains($resultat->display_value_pdf ?? '', $child->suffixe))
                            {{ $child->suffixe }}
                        @endif
                    @endif
                @endif
            </td>
            <td class="col-valref">
                {{ $child->valeur_ref ?? '' }}
            </td>
            <td class="col-anteriorite">
                @if($resultat && $resultat->antecedent)
                    {{ $resultat->antecedent }}
                @endif
            </td>
        </tr>

        {{-- ✅ CORRECTION : Afficher les antibiogrammes selon les vraies données --}}
        @if($hasAntibiogrammes)
            @foreach($child->antibiogrammes as $antibiogramme)
                <tr class="antibiogramme-header">
                    <td colspan="4" style="padding: 8px 0 4px {{ ($level + 1) * 20 }}px; font-weight: bold; font-size: 10pt; color: #333; border-top: 1px solid #ccc;">
                        Antibiogramme - <i>{{ $antibiogramme->bacterie->designation ?? 'Bactérie inconnue' }}</i>
                        @if($antibiogramme->bacterie && $antibiogramme->bacterie->famille)
                            ({{ $antibiogramme->bacterie->famille->designation }})
                        @endif
                    </td>
                </tr>

                {{-- ✅ CORRECTION : Antibiotiques sensibles (S) --}}
                @if($antibiogramme->antibiotiques_sensibles->isNotEmpty())
                    <tr class="antibiogramme-row">
                        <td style="padding-left: {{ ($level + 2) * 20 }}px; font-size: 9pt; color: #666;">
                            Sensible :
                        </td>
                        <td colspan="3" style="font-size: 9pt;">
                            @foreach($antibiogramme->antibiotiques_sensibles as $resultatAb)
                                {{ $resultatAb->antibiotique->designation ?? 'N/A' }}
                                @if($resultatAb->diametre_mm) ({{ $resultatAb->diametre_mm }}mm) @endif
                                @if(!$loop->last), @endif
                            @endforeach
                        </td>
                    </tr>
                @endif

                {{-- ✅ CORRECTION : Antibiotiques résistants (R) --}}
                @if($antibiogramme->antibiotiques_resistants->isNotEmpty())
                    <tr class="antibiogramme-row">
                        <td style="padding-left: {{ ($level + 2) * 20 }}px; font-size: 9pt; color: #666;">
                            Résistant :
                        </td>
                        <td colspan="3" style="font-size: 9pt; font-weight: bold;">
                            @foreach($antibiogramme->antibiotiques_resistants as $resultatAb)
                                {{ $resultatAb->antibiotique->designation ?? 'N/A' }}
                                @if($resultatAb->diametre_mm) ({{ $resultatAb->diametre_mm }}mm) @endif
                                @if(!$loop->last), @endif
                            @endforeach
                        </td>
                    </tr>
                @endif

                {{-- ✅ CORRECTION : Antibiotiques intermédiaires (I) --}}
                @if($antibiogramme->antibiotiques_intermediaires->isNotEmpty())
                    <tr class="antibiogramme-row">
                        <td style="padding-left: {{ ($level + 2) * 20 }}px; font-size: 9pt; color: #666;">
                            Intermédiaire :
                        </td>
                        <td colspan="3" style="font-size: 9pt; font-style: italic;">
                            @foreach($antibiogramme->antibiotiques_intermediaires as $resultatAb)
                                {{ $resultatAb->antibiotique->designation ?? 'N/A' }}
                                @if($resultatAb->diametre_mm) ({{ $resultatAb->diametre_mm }}mm) @endif
                                @if(!$loop->last), @endif
                            @endforeach
                        </td>
                    </tr>
                @endif
            @endforeach
        @endif

        {{-- Conclusion spécifique --}}
        @if($resultat && !empty($resultat->conclusion))
            <tr class="child-row">
                <td class="col-designation" style="padding-left: {{ $level * 20 }}px; font-style: italic; font-size: 9pt;">
                    Conclusion :
                </td>
                <td colspan="3" style="font-size: 9pt; line-height: 1.3;">
                    {!! nl2br(e($resultat->conclusion)) !!}
                </td>
            </tr>
        @endif

        {{-- Récursion pour les sous-enfants --}}
        @if($child->children && $child->children->count() > 0)
            @include('pdf.analyses.children-analyse', ['children' => $child->children, 'level' => $level + 1])
        @endif
    @endif
@endforeach