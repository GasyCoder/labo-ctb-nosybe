{{-- resources/views/pdf/analyses/resultats-analyses.blade.php - CORRIGÉ --}}
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

    {{-- En-tête avec logo (première page seulement) --}}
    <div class="header-section">
        <img src="{{ public_path('assets/images/logo.png') }}" alt="LABORATOIRE LA REFERENCE" class="header-logo">
    </div>
    
    <div class="red-line"></div>
    
    <div class="content-wrapper">
        {{-- Informations patient --}}
        @include('pdf.analyses.header')
        
{{-- ✅ Boucle examens --}}
@foreach($examens as $examen)
    @php
        $hasValidResults = $examen->analyses->some(function($analyse) {
            return $analyse->resultats->isNotEmpty() ||
                   ($analyse->children && $analyse->children->some(fn($child) => $child->resultats->isNotEmpty())) ||
                   ($analyse->antibiogrammes && $analyse->antibiogrammes->isNotEmpty());
        });

        if (!$hasValidResults) continue;
    @endphp

    {{-- ✅ Contenu de chaque examen --}}
    <div class="examen-wrapper">
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

        <table class="main-table">
            @foreach($examen->analyses as $analyse)
                @if($analyse->level === 'PARENT' || is_null($analyse->parent_id))
                    @include('pdf.analyses.analyse-row', ['analyse' => $analyse, 'level' => 1])
                    @if($analyse->children && $analyse->children->count() > 0)
                        @include('pdf.analyses.analyse-children', ['children' => $analyse->children, 'level' => 2])
                    @endif
                @endif
            @endforeach
        </table>

        @include('pdf.analyses.conclusion-examen', ['examen' => $examen])
    </div>
@endforeach

{{-- ✅ Une seule fois à la fin du document --}}
<div class="mini-separator" style="page-break-inside: avoid; margin-top:10px;">
    <div style="text-align: center; margin: 8px 0; padding: 5px 0; border-top: 0.5px solid #e0e0e0;">
        <div style="font-size: 8pt; color: #042379ff;">
            {{ $patientFullName }} - Dossier n° {{ $prescription->patient->numero_dossier ?? $prescription->reference }}
        </div>
    </div>
</div>



                
        {{-- Signature --}}
        <div class="signature">
            <img src="{{ public_path('assets/images/signe.png') }}" alt="Signature" style="max-width: 80px;">
        </div>

    </div>
</body>
</html>