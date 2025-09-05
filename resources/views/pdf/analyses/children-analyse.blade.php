{{-- resources/views/pdf/analyses/children-analyse.blade.php --}}
@foreach($children as $child)
    @php
        // ✅ CORRECTION: Utiliser directement la collection de résultats
        $resultat = $child->resultats->first();
        $hasResult = $resultat && ($resultat->valeur || $resultat->resultats);
        $isPathologique = $resultat && $resultat->est_pathologique;
        $isInfoLine = !$hasResult && $child->designation;
    @endphp

    @if($hasResult || $isInfoLine)
        <tr class="child-row">
            <td class="col-designation {{ $child->is_bold ? 'bold' : '' }}"
                style="padding-left: {{ $level * 20 }}px;">
                {{ $child->designation }}
            </td>
            <td class="col-resultat">
                @if($hasResult)
                    @if($resultat->isLeucocytesType())
                        {{-- Affichage spécial pour les leucocytes --}}
                        @php $leucoData = $resultat->leucocytes_data; @endphp
                        @if($leucoData)
                            {{ $leucoData['valeur'] ?? '' }} /mm³
                            
                            {{-- Afficher les sous-données --}}
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
                                
                                {{-- Reprendre la structure normale --}}
                                <tr><td colspan="4" style="display:none;">
                            @endif
                        @endif
                    @elseif($resultat->isGermeType())
                        {{-- Affichage spécial pour les germes --}}
                        @php $germeData = $resultat->germe_data; @endphp
                        @if($germeData)
                            @if(isset($germeData['bacteries']) && count($germeData['bacteries']) > 0)
                                {{-- Afficher la première bactérie --}}
                                @foreach($germeData['bacteries'] as $bacteriaName => $bacteriaData)
                                    <i>{{ $bacteriaName }}</i>
                                    @break
                                @endforeach
                            @elseif(isset($germeData['options_speciales']))
                                {{ implode(', ', array_map('ucfirst', $germeData['options_speciales'])) }}
                            @endif
                        @endif
                    @else
                        {{-- ✅ CORRECTION: Utiliser la méthode display_value_pdf du modèle Resultat --}}
                        @if($resultat->display_value_pdf)
                            {!! $resultat->display_value_pdf !!}
                        @else
                            {{-- Fallback vers la logique précédente --}}
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
                        
                        {{-- Ajouter l'unité si disponible --}}
                        @if($child->unite && !str_contains($resultat->display_value_pdf ?? '', $child->unite))
                            {{ $child->unite }}
                        @endif
                        
                        {{-- Ajouter le suffixe si disponible --}}
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

        {{-- ✅ NOUVEAU: Afficher la conclusion spécifique à cette analyse enfant --}}
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