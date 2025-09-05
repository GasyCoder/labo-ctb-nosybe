{{-- resources/views/pdf/analyses/analyse-row.blade.php --}}
@php
    $resultat = $analyse->resultats->first();
    $hasResult = $resultat && ($resultat->valeur || $resultat->resultats);
    $isPathologique = $resultat && $resultat->est_pathologique; // ✅ Utiliser l'accessor du modèle
    $isInfoLine = !$analyse->result_disponible && $analyse->designation && ($analyse->prix == 0 || $analyse->level === 'PARENT');
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
                @php
                    $displayValue = '';
                    
                    // ✅ Utiliser les méthodes du modèle Resultat
                    if ($resultat->isGermeType()) {
                        $germeData = $resultat->germe_data;
                        if ($germeData && isset($germeData['options_speciales'])) {
                            $displayValue = implode(', ', array_map('ucfirst', $germeData['options_speciales']));
                        }
                        // Afficher les bactéries
                        if ($germeData && isset($germeData['bacteries'])) {
                            foreach ($germeData['bacteries'] as $bacteriaName => $bacteriaData) {
                                $displayValue = '<i>' . $bacteriaName . '</i>';
                                break; // Premier seulement
                            }
                        }
                    } elseif ($resultat->isLeucocytesType()) {
                        // ✅ Utiliser l'accessor du modèle
                        $leucoData = $resultat->leucocytes_data;
                        if ($leucoData && isset($leucoData['valeur'])) {
                            $displayValue = $leucoData['valeur'] . ' /mm³';
                        }
                    } else {
                        // ✅ Utiliser l'accessor valeur_pdf du modèle
                        $displayValue = $resultat->valeur_pdf ?: $resultat->resultats_pdf;
                        
                        // Cas spéciaux
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
                        }
                    }
                    
                    // ✅ Utiliser l'accessor du modèle pour déterminer si pathologique
                    if ($resultat->est_pathologique && $displayValue) {
                        $displayValue = '<strong>' . $displayValue . '</strong>';
                    }
                @endphp
                
                {!! $displayValue !!}
            @endif
        </td>
        <td class="col-valref">
            {{ $analyse->valeur_ref ?? '' }}
        </td>
        <td class="col-anteriorite">
            {{-- Vous pouvez ajouter un champ antecedent dans le modèle si nécessaire --}}
        </td>
    </tr>
@endif