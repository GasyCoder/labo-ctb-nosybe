<div class="flex flex-col h-full">
    {{-- Header Section --}}
    <div class="flex-shrink-0 bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-900/50 dark:to-slate-800/50 border-b border-slate-200 dark:border-slate-800 p-4">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-600 dark:from-primary-600 dark:to-primary-700 rounded-lg flex items-center justify-center shadow-sm">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Saisie des Résultats</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">Complétez les analyses ci-dessous</p>
            </div>
        </div>
    </div>

    {{-- Analysis Tree Content --}}
    <div class="flex-1 overflow-hidden bg-white dark:bg-slate-900">
        <div class="h-full overflow-y-auto">
            <div class="p-6">
                @forelse($roots as $root)
                    <div class="space-y-4">
                        <x-analyse-node
                            :node="$root"
                            :results="$results"
                            :familles="$familles"
                            :bacteries-by-famille="$bacteriesByFamille"
                            wire:key="node-{{ $root->id }}"
                        />
                    </div>
                @empty
                    {{-- Empty State --}}
                    <div class="h-full flex items-center justify-center min-h-[400px]">
                        <div class="text-center max-w-md mx-auto">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-700 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-2">
                                Aucune analyse rattachée
                            </h4>
                            <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                                Aucune analyse n'a été trouvée pour cette prescription. Vérifiez la configuration ou contactez l'administrateur.
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Actions Footer --}}
    <div class="flex-shrink-0 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 p-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            {{-- Messages Section --}}
            <div class="flex items-center gap-3">
                @if (session()->has('message'))
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded-lg border border-green-200 dark:border-green-800">
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm font-medium">{{ session('message') }}</span>
                    </div>
                @endif
                
                @if (session()->has('error'))
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 rounded-lg border border-red-200 dark:border-red-800">
                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                @endif
            </div>
            
            {{-- Action Buttons --}}
            <div class="flex items-center gap-3">
                {{-- Save Draft Button --}}
                <button type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-slate-800 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    Brouillon
                </button>

                {{-- Save All Button --}}
                <button wire:click="saveAll"
                        class="inline-flex items-center gap-2 px-6 py-2 bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600 text-white text-sm font-medium rounded-lg border border-green-600 dark:border-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:focus:ring-offset-slate-800 transition-all duration-200 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Enregistrer tout
                </button>
            </div>
        </div>
        
        {{-- Progress Indicator --}}
        <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-800">
            <div class="flex items-center justify-between text-xs text-slate-600 dark:text-slate-400 mb-1">
                <span>Progression de la saisie</span>
                <span>{{ $completedCount ?? 0 }}/{{ $totalCount ?? 0 }}</span>
            </div>
            <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5">
                @php
                    $progress = ($totalCount ?? 0) > 0 ? (($completedCount ?? 0) / ($totalCount ?? 0)) * 100 : 0;
                @endphp
                <div class="bg-gradient-to-r from-primary-500 to-green-500 h-1.5 rounded-full transition-all duration-500" 
                     style="width: {{ $progress }}%"></div>
            </div>
        </div>
    </div>
</div>