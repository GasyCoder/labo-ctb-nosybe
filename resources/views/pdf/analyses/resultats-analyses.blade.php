{{-- resources/views/pdf/analyses/resultats-analyses.blade.php - VERSION SIMPLIFIÉE --}}
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
    @endphp

    {{-- En-tête avec logo --}}
    <div class="header-section">
        <img src="{{ public_path('assets/images/logo.png') }}" alt="LABORATOIRE LA REFERENCE" class="header-logo">
    </div>
    
    <div class="red-line"></div>
    
    <div class="content-wrapper">
        {{-- Informations patient --}}
        @include('pdf.analyses.header')
        
        {{-- Contenu des examens --}}
        @foreach($examens as $examen)
            @php
                $hasValidResults = $examen->analyses->some(function($analyse) {
                    return $analyse->resultats->isNotEmpty() ||
                           ($analyse->children && $analyse->children->some(function($child) {
                               return $child->resultats->isNotEmpty();
                           })) ||
                           ($analyse->antibiogrammes && $analyse->antibiogrammes->isNotEmpty());
                });
            @endphp

            @if($hasValidResults)
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

        {{-- Conclusion générale --}}
        @if(isset($conclusion_generale) && !empty($conclusion_generale))
            <div class="conclusion-section">
                <div class="conclusion-title">Conclusion générale :</div>
                <div class="conclusion-content">
                    {!! nl2br(e($conclusion_generale)) !!}
                </div>
            </div>
        @endif

        {{-- Signature --}}
        <div class="signature">
            <img src="{{ public_path('assets/images/signature.png') }}" alt="Signature" style="max-width: 180px;">
        </div>
    </div>
</body>
</html>