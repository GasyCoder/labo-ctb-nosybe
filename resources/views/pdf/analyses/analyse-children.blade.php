{{-- resources/views/pdf/analyses/children-analyse.blade.php --}}
@foreach($children as $child)
    @php
        $resultat = $child->resultats->first();
        $hasResult = $resultat && ($resultat->valeur || $resultat->resultats);
        $isPathologique = $resultat && $resultat->est_pathologique;
        $isInfoLine = !$hasResult && $child->designation;
        
        // ✅ CORRECTION : Vérifier la présence d'antibiogrammes pour les enfants
        $hasAntibiogrammes = false;
        if (isset($child->antibiogrammes) && $child->antibiogrammes->isNotEmpty()) {
            $hasAntibiogrammes = true;
        } elseif ($resultat && method_exists($resultat, 'getAntibiogrammesAttribute')) {
            $antibiogrammes = $resultat->antibiogrammes;
            $hasAntibiogrammes = $antibiogrammes && $antibiogrammes->isNotEmpty();
            $child->antibiogrammes = $antibiogrammes;
        }
    @endphp

    @if($hasResult || $isInfoLine || $hasAntibiogrammes)
        <tr class="child-row">
            <td class="col-designation {{ $child->is_bold ? 'bold' : '' }}"
                style="padding-left: {{ $level * 20 }}px;">
                {{ $child->designation }}
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
                        @elseif($selectedOptions)
                            {{ $selectedOptions }}
                        @elseif($autreValeur)
                            <i>{{ $autreValeur }}</i>
                        @endif
                        
                    @elseif($resultat->isLeucocytesType())
                        {{-- ✅ CORRECTION : LEUCOCYTES avec sous-détails --}}
                        @php $leucoData = $resultat->leucocytes_data; @endphp
                        @if($leucoData)
                            {{ $leucoData['valeur'] ?? '' }} /mm³
                            
                            {{-- Si on a des sous-détails, fermer cette cellule et créer de nouvelles lignes --}}
                            @if(isset($leucoData['polynucleaires']) || isset($leucoData['lymphocytes']))
                                </td><td class="col-valref">{{ $child->valeur_ref ?? '' }}</td><td class="col-anteriorite">
                                @if($resultat && isset($resultat->antecedent))
                                    {{ $resultat->antecedent }}
                                @endif
                                </td></tr>
                                
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
                                
                                {{-- Ligne fantôme pour fermer proprement --}}
                                <tr><td colspan="4" style="display:none;">
                            @endif
                        @endif
                    @else
                        {{-- ✅ CORRECTION : AUTRES TYPES avec meilleure gestion --}}
                        @if($resultat->display_value_pdf)
                            {!! $resultat->display_value_pdf !!}
                        @else
                            @php
                                $displayValue = '';
                                
                                if ($child->type && $child->type->name === 'SELECT_MULTIPLE') {
                                    $resultatsArray = $resultat->resultats_pdf;
                                    if (is_array($resultatsArray)) {
                                        $displayValue = implode(', ', $resultatsArray);
                                    }
                                } elseif ($child->type && $child->type->name === 'NEGATIF_POSITIF_3') {
                                    if ($resultat->resultats === 'Positif' && $resultat->valeur) {
                                        $displayValue = $resultat->resultats;
                                        $values = is_array($resultat->valeur) ? 
                                            implode(', ', $resultat->valeur) : 
                                            $resultat->valeur;
                                        $displayValue .= ' (' . $values . ')';
                                    } else {
                                        $displayValue = $resultat->resultats ?: $resultat->valeur;
                                    }
                                } elseif ($child->type && $child->type->name === 'FV') {
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
                                    if (is_array($displayValue)) {
                                        $displayValue = implode(', ', $displayValue);
                                    }
                                }
                                
                                // Ajouter les unités si nécessaire et pas déjà présentes
                                if ($displayValue && $child->unite && !str_contains($displayValue, $child->unite)) {
                                    $displayValue .= ' ' . $child->unite;
                                }
                                
                                if ($displayValue && $child->suffixe && !str_contains($displayValue, $child->suffixe)) {
                                    $displayValue .= ' ' . $child->suffixe;
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
                {{ $child->valeur_ref ?? '' }}
            </td>
            <td class="col-anteriorite">
                @if($resultat && isset($resultat->antecedent))
                    {{ $resultat->antecedent }}
                @endif
            </td>
        </tr>

        {{-- ✅ CORRECTION : Afficher les antibiogrammes pour les enfants --}}
        @if($hasAntibiogrammes && isset($child->antibiogrammes))
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
                @if(isset($antibiogramme->antibiotiques_sensibles) && $antibiogramme->antibiotiques_sensibles->isNotEmpty())
                    <tr class="antibiogramme-row">
                        <td style="padding-left: {{ ($level + 2) * 20 }}px; font-size: 9pt; color: #666;">
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

                {{-- ✅ CORRECTION : Antibiotiques résistants (R) --}}
                @if(isset($antibiogramme->antibiotiques_resistants) && $antibiogramme->antibiotiques_resistants->isNotEmpty())
                    <tr class="antibiogramme-row">
                        <td style="padding-left: {{ ($level + 2) * 20 }}px; font-size: 9pt; color: #666;">
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

                {{-- ✅ CORRECTION : Antibiotiques intermédiaires (I) --}}
                @if(isset($antibiogramme->antibiotiques_intermediaires) && $antibiogramme->antibiotiques_intermediaires->isNotEmpty())
                    <tr class="antibiogramme-row">
                        <td style="padding-left: {{ ($level + 2) * 20 }}px; font-size: 9pt; color: #666;">
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
                @if($antibiogramme->notes)
                    <tr class="antibiogramme-row">
                        <td style="padding-left: {{ ($level + 2) * 20 }}px; font-size: 9pt; color: #666;">
                            Notes :
                        </td>
                        <td colspan="3" style="font-size: 9pt; font-style: italic;">
                            {{ $antibiogramme->notes }}
                        </td>
                    </tr>
                @endif
            @endforeach
        @endif

        {{-- ✅ CORRECTION : Conclusion spécifique du résultat enfant --}}
        @if($hasResult && $resultat && !empty($resultat->conclusion))
            <tr class="conclusion-row">
                <td style="padding-left: {{ ($level + 1) * 20 }}px; font-size: 9pt; color: #666; font-style: italic;">
                    Conclusion :
                </td>
                <td colspan="3" style="font-size: 9pt; line-height: 1.3;">
                    {!! nl2br(e($resultat->conclusion)) !!}
                </td>
            </tr>
        @endif

        {{-- ✅ RÉCURSION : Traiter les sous-enfants --}}
        @if($child->children && $child->children->count() > 0)
            @include('pdf.analyses.analyse-children', ['children' => $child->children, 'level' => $level + 1])
        @endif
    @endif
@endforeach