<div class="h-full bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700">
    {{-- Header simplifié --}}
    <div class="px-4 py-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-600 dark:bg-blue-700 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Analyses</h2>
        </div>

        @php
            $allCompleted = collect($analysesParents)->every(fn($item) => $item['status'] === 'TERMINE');
            $totalEnfants = collect($analysesParents)->sum('enfants_count');
        @endphp
    </div>

    {{-- Liste des analyses améliorée --}}
    <div class="overflow-y-auto h-full">
        <div class="p-3 space-y-2">
            @foreach($analysesParents as $parent)
                <div class="relative group">
                    {{-- Bouton principal d'analyse --}}
                    <button type="button"
                            wire:key="parent-{{ $parent['id'] }}"
                            wire:click.prevent="selectAnalyseParent({{ $parent['id'] }})"
                            class="w-full text-left p-2 pr-2 rounded-lg border transition-colors
                                   {{ $selectedParentId == $parent['id'] 
                                      ? 'border-blue-500 dark:border-blue-600 bg-blue-50 dark:bg-blue-900/30 ring-2 ring-blue-200 dark:ring-blue-800' 
                                      : 'border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20' }}
                                   {{ $parent['status'] === 'TERMINE' 
                                      ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-700' 
                                      : ($parent['status'] === 'EN_COURS' ? 'bg-orange-50 dark:bg-orange-900/20 border-orange-200 dark:border-orange-700' : 'bg-white dark:bg-gray-700') }}">
                        
                        <div class="flex items-start gap-3">
                            {{-- Indicateur de statut avec icônes appropriées --}}
                            <div class="flex-shrink-0 pt-1">
                                @if($parent['status'] === 'TERMINE')
                                    <div class="w-6 h-6 bg-green-600 dark:bg-green-700 rounded-md flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @elseif($parent['status'] === 'EN_COURS')
                                    <div class="w-6 h-6 bg-orange-500 dark:bg-orange-600 rounded-md flex items-center justify-center">
                                        <em class="ni ni-clock text-white text-sm"></em>
                                    </div>
                                @else
                                    <div class="w-6 h-6 bg-gray-400 dark:bg-gray-600 rounded-md flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            {{-- Contenu principal --}}
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white truncate mb-1">
                                    {{ $parent['code'] }}
                                </h3>
                                
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    {{ $parent['designation'] }}
                                </p>
                            </div>
                        </div>
                    </button>

                    {{-- Bouton terminer individuel (visible si en cours ou prêt) --}}
                    @if(($parent['eligible'] ?? false) && $parent['status'] !== 'EN_COURS')
                        <button
                            wire:click="markAnalyseAsCompleted({{ $parent['id'] }})"
                            wire:loading.attr="disabled"
                            wire:target="markAnalyseAsCompleted"
                            class="absolute top-2 right-2 z-10 p-1.5 bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 disabled:opacity-50 text-white rounded-md transition-colors"
                            title="Terminer cette analyse"
                        >
                            <span wire:loading.remove wire:target="markAnalyseAsCompleted">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </span>
                            <span wire:loading wire:target="markAnalyseAsCompleted">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                    @endif

                </div>
            @endforeach
            
            {{-- Bouton terminer ensemble (visible quand toutes les analyses sont complétées) --}}
          
                <div class="pt-4 border-t border-gray-200 mt-4">
                    <button wire:click="markPrescriptionAsCompletedAlternative" 
                            wire:loading.attr="disabled"
                            wire:target="markPrescriptionAsCompletedAlternative"
                            class="w-full bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                        <span wire:loading.remove wire:target="markPrescriptionAsCompletedAlternative">
                            <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Terminer l'analyse
                        </span>
                        <span wire:loading wire:target="markPrescriptionAsCompletedAlternative" class="flex items-center justify-center">
                            <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Finalisation...
                        </span>
                    </button>
                </div>
            

            {{-- Bouton terminer ensemble --}}
                @if (auth()->user()->type === 'technicien')
            <div class="pt-4 border-t border-gray-200 dark:border-gray-700 mt-4">
                <button wire:click="markPrescriptionAsCompletedAlternative" 
                        wire:loading.attr="disabled"
                        wire:target="markPrescriptionAsCompletedAlternative"
                        class="w-full bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                    <span wire:loading.remove wire:target="markPrescriptionAsCompletedAlternative">
                        <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Terminer l'analyse
                    </span>
                    <span wire:loading wire:target="markPrescriptionAsCompletedAlternative" class="flex items-center justify-center">
                        <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Finalisation...
                    </span>
                </button>
            </div>
@endif
            {{-- État vide --}}
            @if(empty($analysesParents))
                <div class="text-center py-12">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2"></path>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Aucune analyse disponible</p>
                </div>
            @endif
        </div>
    </div>
</div>