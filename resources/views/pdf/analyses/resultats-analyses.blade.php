{{-- resources/views/pdf/analyses/resultats-analyses.blade.php - LIMITE CORRIGÉE --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Résultats d'analyses - {{ $prescription->reference }}</title>
    @include('pdf.analyses.styles')
</head>
<body>
    @php
        $patientFullName = trim(($prescription->patient->civilite ?? '') . ' ' . 
                                ($prescription->patient->nom ?? 'N/A') . ' ' . 
                                ($prescription->patient->prenom ?? ''));
        
        // LIMITE CORRIGÉE : Plus réaliste pour DomPDF
        $hauteurCumulee = 0;
        $limitePage = 600; // Augmenté ! (~15cm réels)
        
        // Hauteur initiale plus précise
        $hauteurCumulee += 160; // Logo + header + patient info
        
        $sautEffectue = false;
    @endphp

    {{-- En-tête avec logo (première page seulement) --}}
    <div class="header-section">
        <img src="{{ public_path('assets/images/logo.png') }}" alt="LABORATOIRE LA REFERENCE" class="header-logo">
    </div>
    
    <div class="red-line"></div>
    
    <div class="content-wrapper">
        {{-- Informations patient (première page) --}}
        @include('pdf.analyses.header')
        
        {{-- Contenu des examens avec limite corrigée --}}
        @foreach($examens as $examen)
            @php
                $hasValidResults = $examen->analyses->some(function($analyse) {
                    return $analyse->resultats->isNotEmpty() ||
                           ($analyse->children && $analyse->children->some(function($child) {
                               return $child->resultats->isNotEmpty();
                           })) ||
                           ($analyse->antibiogrammes && $analyse->antibiogrammes->isNotEmpty());
                });
                
                if (!$hasValidResults) continue;
                
                // Estimation plus conservative de la hauteur
                $hauteurExamen = 30; // Titre de section
                
                // Compter plus précisément les lignes
                $nombreLignes = 0;
                foreach($examen->analyses as $analyse) {
                    if($analyse->level === 'PARENT' || is_null($analyse->parent_id)) {
                        if($analyse->resultats->isNotEmpty()) {
                            $nombreLignes++;
                            
                            // Enfants
                            if($analyse->children && $analyse->children->count() > 0) {
                                $nombreLignes += $analyse->children->filter(function($child) {
                                    return $child->resultats->isNotEmpty();
                                })->count();
                            }
                            
                            // Antibiogrammes
                            if($analyse->antibiogrammes && $analyse->antibiogrammes->isNotEmpty()) {
                                $nombreLignes += $analyse->antibiogrammes->count() * 3; // 3 lignes par antibiogramme
                            }
                        }
                    }
                }
                
                $hauteurExamen += $nombreLignes * 15; // 15px par ligne
                
                // DÉCISION plus conservative
                $needsPageBreak = false;
                if (!$sautEffectue && ($hauteurCumulee + $hauteurExamen) > $limitePage) {
                    // Vérification supplémentaire : ne pas sauter si c'est le premier examen
                    if ($loop->index > 0) {
                        $needsPageBreak = true;
                        $sautEffectue = true;
                        $hauteurCumulee = 50;
                    }
                }
                
                $hauteurCumulee += $hauteurExamen;
            @endphp

            @if($hasValidResults)
                {{-- SAUT seulement si vraiment nécessaire --}}
                @if($needsPageBreak)
                    <div style="page-break-before: always; margin-top: 0; margin-bottom: 20px; text-align: center; border-bottom: 0.5px solid #0b48eeff; padding-bottom: 10px;">
                        <div style="font-weight: bold; font-size: 10pt; margin-bottom: 3px;">
                            Résultats de {{ $patientFullName }}
                        </div>
                        <div style="font-size: 8pt; color: #666;">
                            Dossier n° {{ $prescription->patient->numero_dossier ?? $prescription->reference }} du {{ $prescription->created_at->format('d/m/Y') }}
                        </div>
                    </div>
                @endif

                {{-- En-tête du tableau --}}
                <table class="main-table">
                    <tr>
                        <td class="col-designation section-title">{{ strtoupper($examen->name) }}</td>
                        <td class="col-resultat header-cols">Résultat</td>
                        <td class="col-valref header-cols">Val Réf</td>
                        <td class="col-anteriorite header-cols">Antériorité</td>
                    </tr>
                </table>
                
                <div class="red-line"></div>
                <div class="spacing"></div>

                {{-- Contenu principal --}}
                <table class="main-table">
                    @foreach($examen->analyses as $analyse)
                        {{-- Afficher seulement les analyses parents ou sans parent --}}
                        @if($analyse->level === 'PARENT' || is_null($analyse->parent_id))
                            @include('pdf.analyses.analyse-row', ['analyse' => $analyse, 'level' => 0])
                            
                            {{-- Afficher les enfants --}}
                            @if($analyse->children && $analyse->children->count() > 0)
                                @include('pdf.analyses.analyse-children', ['children' => $analyse->children, 'level' => 1])
                            @endif
                        @endif
                    @endforeach
                </table>

                {{-- Conclusions pour cet examen --}}
                @include('pdf.analyses.conclusion-examen', ['examen' => $examen])
                
                <div class="spacing"></div>
            @endif
        @endforeach

        {{-- Signature --}}
        <div class="signature">
            <img src="{{ public_path('assets/images/signe.png') }}" alt="Signature" style="max-width: 120px;">
        </div>
    </div>
</body>
</html>