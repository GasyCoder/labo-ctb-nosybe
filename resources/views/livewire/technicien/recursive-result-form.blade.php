{{-- recursive-resultat-form --}}
<div class="flex flex-col h-full bg-white dark:bg-slate-800 transition-colors duration-200 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700">
    {{-- Header Section --}}
    <div class="flex-shrink-0 bg-gray-50 dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 p-4 transition-colors duration-200">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-600 dark:bg-blue-700 rounded-lg flex items-center justify-center transition-colors duration-200">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-100 transition-colors duration-200">
                    Saisie des Résultats
                </h3>
                <p class="text-sm text-gray-600 dark:text-slate-400 transition-colors duration-200">
                    Complétez les analyses ci-dessous
                    {{-- Affichage optionnel des infos patient --}}
                    @if($prescription && $prescription->patient)
                        • Patient: {{ $prescription->patient->nom }} {{ $prescription->patient->prenoms }} 
                        ({{ $prescription->patient->civilite }})
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- Analysis Tree Content --}}
    <div class="flex-1 overflow-hidden">
        <div class="h-full">
            <div class="p-6">
                @forelse($roots as $root)
                    <div class="space-y-4">
                        <x-analyse-node
                            :node="$root"
                            :results="$results"
                            :familles="$familles"
                            :bacteries-by-famille="$bacteriesByFamille"
                            :patient="$prescription->patient ?? null"
                            wire:key="node-{{ $root->id }}"
                        />
                    </div>
                @empty
                    {{-- Empty State amélioré --}}
                    <div class="h-full flex items-center justify-center min-h-[400px]">
                        <div class="text-center max-w-md mx-auto p-6 bg-gray-50 dark:bg-slate-700/30 rounded-lg border border-gray-200 dark:border-slate-700 transition-colors duration-200">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-slate-700 rounded-lg flex items-center justify-center transition-colors duration-200">
                                <svg class="w-8 h-8 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-slate-100 mb-2 transition-colors duration-200">
                                Aucune analyse rattachée
                            </h4>
                            <p class="text-gray-600 dark:text-slate-400 text-sm leading-relaxed transition-colors duration-200">
                                Aucune analyse n'a été trouvée pour cette prescription. Vérifiez la configuration ou contactez l'administrateur.
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Actions Footer --}}
    <div class="flex-shrink-0 bg-white dark:bg-slate-800 border-t border-gray-200 dark:border-slate-700 p-4 transition-colors duration-200">
        <div class="flex items-center justify-between">
            <button wire:click="saveAll"
                    wire:loading.attr="disabled"
                    wire:target="saveAll"
                    class="inline-flex items-center gap-2 px-6 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600
                           disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-medium
                           rounded-lg transition-colors duration-200 shadow-sm">
                <span wire:loading.remove wire:target="saveAll">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </span>
                <span wire:loading wire:target="saveAll">
                    <svg class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </span>
                <span>Enregistrer</span>
            </button>
        </div>
    </div>
</div>