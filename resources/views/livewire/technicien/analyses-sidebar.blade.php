<div class="h-full bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700">
    {{-- Header --}}
    <div class="px-4 py-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-600 dark:bg-blue-700 rounded-lg flex items-center justify-center">
               <em class="text-xl ni ni-folder-list"></em>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Tâche(s) à traiter</h2>
                @php
                    $totalAnalyses = count($analysesParents);
                    $analysesTerminees = collect($analysesParents)->where('status', 'TERMINE')->count();
                @endphp
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $analysesTerminees }}/{{ $totalAnalyses }} terminées</p>
            </div>
        </div>
    </div>

    {{-- Liste des analyses --}}
    <div class="overflow-y-auto h-full">
        <div class="p-3 space-y-2">
            @forelse($analysesParents as $parent)
                <div class="relative group">
                    {{-- Bouton principal d'analyse --}}
                    <button type="button"
                            wire:key="parent-{{ $parent['id'] }}"
                            wire:click.prevent="selectAnalyseParent({{ $parent['id'] }})"
                            class="w-full text-left p-3 rounded-lg border transition-all duration-200
                            {{ $selectedParentId == $parent['id'] 
                                ? 'border-blue-500 dark:border-blue-600 bg-blue-50 dark:bg-blue-900/30 ring-2 ring-blue-200 dark:ring-blue-800' 
                                : 'border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20' }}
                            {{ $parent['status'] === 'TERMINE' 
                                ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-700' 
                                : ($parent['status'] === 'EN_COURS' ? 'bg-orange-50 dark:bg-orange-900/20 border-orange-200 dark:border-orange-700' : 'bg-gray-50 dark:bg-gray-900/20 border-gray-200 dark:border-gray-700') }}">
                        <div class="flex items-start gap-3">
                            {{-- Indicateur de statut --}}
                            <div class="flex-shrink-0 pt-1">
                                @if($parent['status'] === 'TERMINE')
                                    <div class="w-6 h-6 bg-green-600 dark:bg-green-700 rounded-md flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @elseif($parent['status'] === 'EN_COURS')
                                    <div class="w-6 h-6 bg-orange-500 dark:bg-orange-600 rounded-md flex items-center justify-center">
                                       <em class="text-xl ni ni-loader"></em>
                                    </div>
                                @else
                                    {{-- VIDE state --}}
                                    <div class="w-6 h-6 bg-gray-400 dark:bg-gray-600 rounded-md flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            {{-- Contenu principal --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                        {{ $parent['code'] }}
                                    </h3>
                                    
                                    {{-- Badge statut --}}
                                    <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full
                                        {{ $parent['status'] === 'TERMINE' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                        {{ $parent['status'] === 'EN_COURS' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : '' }}
                                        {{ $parent['status'] === 'VIDE' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' : '' }}">
                                        {{ $parent['status'] === 'TERMINE' ? 'Terminé' : ($parent['status'] === 'EN_COURS' ? 'En cours' : 'À faire') }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                                    {{ $parent['designation'] }}
                                </p>
                                {{-- Progression --}}
                                @if($parent['enfants_count'] > 0)
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                            <div class="h-1.5 rounded-full transition-all duration-300
                                                {{ $parent['status'] === 'TERMINE' ? 'bg-green-500' : ($parent['status'] === 'EN_COURS' ? 'bg-orange-500' : 'bg-gray-400') }}"
                                                style="width: {{ $parent['enfants_count'] > 0 ? ($parent['enfants_completed'] / $parent['enfants_count']) * 100 : 0 }}%">
                                            </div>
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $parent['enfants_completed'] }}/{{ $parent['enfants_count'] }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </button>
                </div>
            @empty
                {{-- État vide --}}
                <div class="text-center py-12">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2"></path>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Aucune analyse disponible</p>
                </div>
            @endforelse

            {{-- ✅ BOUTON DE FINALISATION GLOBALE CORRIGÉ --}}
            @if(count($analysesParents) > 0 && auth()->user()->type === 'technicien')
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700 mt-4">
                    @if($this->canFinalizePrescription())
                        {{-- ✅ Prescription déjà terminée --}}
                        <div class="p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm font-medium text-green-800 dark:text-green-200">
                                    Prescription terminée
                                </span>
                            </div>
                        </div>
                    @elseif($this->isReadyToFinalize())
                        {{-- ✅ Prêt à finaliser - Bouton actif --}}
                        <button wire:click="markPrescriptionAsCompleted" 
                                wire:loading.attr="disabled"
                                wire:target="markPrescriptionAsCompleted"
                                class="w-full bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold py-3 px-4 rounded-lg transition-colors shadow-sm">
                            <span wire:loading.remove wire:target="markPrescriptionAsCompleted">
                                <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Finaliser la prescription
                            </span>
                            <span wire:loading wire:target="markPrescriptionAsCompleted" class="flex items-center justify-center">
                                <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Finalisation...
                            </span>
                        </button>
                    @else
                        {{-- ✅ Pas encore prêt - Message informatif amélioré --}}
                        <div class="p-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        En attente de finalisation
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Complétez tous les résultats d'analyses pour pouvoir finaliser la prescription
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>