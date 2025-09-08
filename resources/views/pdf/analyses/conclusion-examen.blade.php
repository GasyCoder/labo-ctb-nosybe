{{-- resources/views/pdf/analyses/conclusion-examen.blade.php --}}
@php
    // Récupérer les conclusions de cet examen
    $conclusionsExamen = collect();
    
    foreach($examen->analyses as $analyse) {
        if($analyse->resultats->isNotEmpty()) {
            foreach($analyse->resultats as $resultat) {
                if(!empty($resultat->conclusion)) {
                    $conclusionsExamen->push([
                        'analyse_designation' => $analyse->designation,
                        'conclusion' => $resultat->conclusion
                    ]);
                }
            }
        }
        
        // Vérifier les enfants aussi
        if($analyse->children && $analyse->children->isNotEmpty()) {
            foreach($analyse->children as $child) {
                if($child->resultats->isNotEmpty()) {
                    foreach($child->resultats as $resultat) {
                        if(!empty($resultat->conclusion)) {
                            $conclusionsExamen->push([
                                'analyse_designation' => $child->designation,
                                'conclusion' => $resultat->conclusion
                            ]);
                        }
                    }
                }
                
                // Vérifier les petits-enfants
                if($child->children && $child->children->isNotEmpty()) {
                    foreach($child->children as $subChild) {
                        if($subChild->resultats->isNotEmpty()) {
                            foreach($subChild->resultats as $resultat) {
                                if(!empty($resultat->conclusion)) {
                                    $conclusionsExamen->push([
                                        'analyse_designation' => $subChild->designation,
                                        'conclusion' => $resultat->conclusion
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    
    // Supprimer les doublons de conclusions
    $conclusionsExamen = $conclusionsExamen->unique('conclusion');
@endphp

@if($conclusionsExamen->isNotEmpty())
    <div class="conclusion-examen">
        <div class="conclusion-examen-title">Commentaire :</div>
        @foreach($conclusionsExamen as $conclusion)
            <div class="conclusion-examen-content">
                {!! nl2br(e($conclusion['conclusion'])) !!}
                @if($conclusionsExamen->count() > 1)
                    <span class="text-fine" style="font-size: 8pt; color: #999;">
                        ({{ $conclusion['analyse_designation'] }})
                    </span>
                @endif
            </div>
            @if(!$loop->last)
                <div style="margin: 5px 0;"></div>
            @endif
        @endforeach
    </div>
@endif