{{-- recursive-resultat-form --}}
<div class="flex flex-col h-full">
    {{-- Header Section --}}
    <div class="flex-shrink-0 bg-gray-50 border-b border-gray-200 p-4">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Saisie des Résultats</h3>
                <p class="text-sm text-gray-600">Complétez les analyses ci-dessous</p>
            </div>
        </div>
    </div>

    {{-- Analysis Tree Content --}}
    <div class="flex-1 overflow-hidden bg-white">
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
            {{-- Messages Section --}}
            <div class="flex items-center gap-3">
                @if (session()->has('message'))
                    <div class="inline-flex items-center gap-2 px-3 py-2 bg-green-50 text-green-700 rounded-lg border border-green-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm font-medium">{{ session('message') }}</span>
                    </div>
                @endif
                
                @if (session()->has('error'))
                    <div class="inline-flex items-center gap-2 px-3 py-2 bg-red-50 text-red-700 rounded-lg border border-red-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                @endif
            </div>
            
            {{-- Save Button Simplifié --}}
            <button wire:click="saveAll"
                    wire:loading.attr="disabled"
                    wire:target="saveAll"
                    class="inline-flex items-center gap-2 px-6 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg transition-colors">
                <span wire:loading.remove wire:target="saveAll">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    Enregistrer
                </span>
                <span wire:loading wire:target="saveAll" class="flex items-center">
                    <svg class="animate-spin w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Enregistrement...
                </span>
            </button>
        </div>
    </div>
</div>