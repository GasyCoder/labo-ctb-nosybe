<div class="h-full bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 shadow-xl">
    {{-- Header optimisé --}}
    <div class="px-4 py-6 border-b border-slate-200 dark:border-slate-800 bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-900/50 dark:to-slate-800/50">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 rounded-lg flex items-center justify-center shadow-lg">
                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">Analyses</h2>
        </div>

        @php
            $totalEnfants = collect($analysesParents)->sum('enfants_count');
            $completedEnfants = collect($analysesParents)->sum('enfants_completed');
            $progression = $totalEnfants > 0 ? round(($completedEnfants / $totalEnfants) * 100) : 0;
        @endphp

        {{-- Progression globale améliorée --}}
        <div class="space-y-3">
            <div class="flex items-center justify-between text-sm">
                <span class="text-slate-600 dark:text-slate-400 font-semibold">Progression générale</span>
                <span class="text-blue-600 dark:text-blue-400 font-bold">{{ $completedEnfants }}/{{ $totalEnfants }}</span>
            </div>
            <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-3 overflow-hidden shadow-inner">
                <div class="bg-gradient-to-r from-blue-500 via-purple-500 to-green-500 h-3 rounded-full transition-all duration-1000 ease-out shadow-sm" 
                     style="width: {{ $progression }}%"></div>
            </div>
            <div class="text-center">
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-semibold border border-slate-200 dark:border-slate-700">
                    @if($progression == 100)
                        <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-emerald-600 dark:text-emerald-400">Terminé</span>
                    @else
                        <svg class="w-4 h-4 text-blue-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-blue-600 dark:text-blue-400">{{ $progression }}% complété</span>
                    @endif
                </span>
            </div>
        </div>
    </div>

    {{-- Liste des analyses optimisée --}}
    <div class="overflow-y-auto h-full">
        <div class="p-3 space-y-2">
            @foreach($analysesParents as $parent)
                <button type="button"
                        wire:key="parent-{{ $parent['id'] }}"
                        wire:click.prevent="selectAnalyseParent({{ $parent['id'] }})"
                        onclick="event.preventDefault(); event.stopPropagation(); return false;"
                        class="group w-full text-left p-4 rounded-xl transition-all duration-300 hover:shadow-lg border-2 hover:scale-[1.02] transform
                               {{ $parent['enfants_completed'] == $parent['enfants_count'] && $parent['enfants_count'] > 0 
                                  ? 'bg-gradient-to-r from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20 border-emerald-300 dark:border-emerald-700 hover:from-emerald-100 hover:to-green-100 dark:hover:from-emerald-900/30 dark:hover:to-green-900/30' 
                                  : 'bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800/50 dark:to-slate-700/50 border-slate-200 dark:border-slate-700 hover:from-slate-100 hover:to-slate-200 dark:hover:from-slate-800 dark:hover:to-slate-700' }}">
                    
                    <div class="flex items-start gap-4">
                        {{-- Indicateur de statut amélioré --}}
                        <div class="flex-shrink-0 pt-1">
                            @if($parent['enfants_completed'] == $parent['enfants_count'] && $parent['enfants_count'] > 0)
                                <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-green-500 dark:from-emerald-600 dark:to-green-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @else
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-500 dark:from-blue-600 dark:to-indigo-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Contenu principal amélioré --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 truncate">
                                    {{ $parent['code'] }}
                                </h3>
                                @if($parent['enfants_count'] > 0)
                                    <span class="flex-shrink-0 text-xs font-bold px-3 py-1 rounded-full
                                        {{ $parent['enfants_completed'] == $parent['enfants_count'] 
                                           ? 'bg-emerald-200 dark:bg-emerald-800 text-emerald-800 dark:text-emerald-200 border border-emerald-300 dark:border-emerald-700' 
                                           : 'bg-blue-200 dark:bg-blue-800 text-blue-800 dark:text-blue-200 border border-blue-300 dark:border-blue-700' }}">
                                        {{ $parent['enfants_completed'] }}/{{ $parent['enfants_count'] }}
                                    </span>
                                @endif
                            </div>
                            
                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-tight mb-3 line-clamp-2">
                                {{ $parent['designation'] }}
                            </p>

                            {{-- Barre de progression améliorée --}}
                            @if($parent['enfants_count'] > 0)
                                @php $parentProg = $parent['enfants_count'] > 0 ? ($parent['enfants_completed'] / $parent['enfants_count']) * 100 : 0; @endphp
                                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2 overflow-hidden shadow-inner">
                                    <div class="bg-gradient-to-r from-blue-500 via-purple-500 to-emerald-500 h-2 rounded-full transition-all duration-700 ease-out shadow-sm" 
                                         style="width: {{ $parentProg }}%"></div>
                                </div>
                            @else
                                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2 overflow-hidden shadow-inner">
                                    <div class="bg-slate-400 dark:bg-slate-600 h-2 rounded-full w-full"></div>
                                </div>
                            @endif
                        </div>
                    </div>
                </button>
            @endforeach
            
            {{-- ✅ BOUTON "TERMINER TOUS" --}}
            @if($totalEnfants > 0 && $completedEnfants == $totalEnfants)
                <div class="pt-4 border-t border-slate-200 dark:border-slate-700 mt-4">
                    <button wire:click="markPrescriptionAsCompleted" 
                            wire:loading.attr="disabled"
                            wire:target="markPrescriptionAsCompleted"
                            class="w-full group relative overflow-hidden bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white font-bold py-3 px-4 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed">
                        <div class="absolute inset-0 bg-gradient-to-r from-emerald-400 to-green-400 opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                        <div class="relative flex items-center justify-center">
                            <div wire:loading.remove wire:target="markPrescriptionAsCompleted">
                                <svg class="w-5 h-5 mr-2 group-hover:rotate-12 transition-transform duration-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Terminer la prescription
                            </div>
                            <div wire:loading wire:target="markPrescriptionAsCompleted" class="flex items-center">
                                <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Finalisation...
                            </div>
                        </div>
                    </button>
                </div>
            @endif

            @if(empty($analysesParents))
                <div class="text-center py-12">
                    <div class="w-16 h-16 mx-auto mb-4 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2"></path>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Aucune analyse disponible</p>
                </div>
            @endif
        </div>
    </div>
</div>