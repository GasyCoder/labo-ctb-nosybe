{{-- recursive-resultat-form --}}
<div class="flex flex-col h-full dark:bg-slate-900">
    {{-- Header Section --}}
    <div class="flex-shrink-0 bg-gray-50 dark:bg-slate-900 border-b border-gray-200 p-4">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 text-gray-900">Saisie des Résultats</h3>
                <p class="text-sm text-gray-600 text-slate-900 dark:text-slate-100">Complétez les analyses ci-dessous</p>
            </div>
        </div>
    </div>

    {{-- Analysis Tree Content --}}
    <div class="flex-1 overflow-hidden">
        <div class="h-full text-slate-900 dark:text-slate-100">
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
                            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">
                                Aucune analyse rattachée
                            </h4>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                Aucune analyse n'a été trouvée pour cette prescription. Vérifiez la configuration ou contactez l'administrateur.
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Actions Footer Simplifié --}}
    <div class="flex-shrink-0 bg-white border-t border-gray-200 p-4">
        <div class="flex items-center justify-between">
            {{-- Save Button Simple Aligné --}}
            <button wire:click="saveAll"
                    wire:loading.attr="disabled"
                    wire:target="saveAll"
                    class="inline-flex items-center gap-2 px-6 py-2 bg-blue-600 hover:bg-blue-700
                        disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-medium
                        rounded-lg transition-colors">
                <span wire:loading.remove wire:target="saveAll">Enregistrer</span>
                <span wire:loading wire:target="saveAll">Enregistrement…</span>
            </button>
        </div>
    </div>
</div>