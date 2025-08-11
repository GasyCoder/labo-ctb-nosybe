{{-- show-prescription --}}
<div class="mt-8 min-h-screen bg-gray-50 dark:bg-slate-900">
    {{-- Header optimisé --}}
    @include('livewire.technicien.partials.header-prescription-technicien')

    {{-- Main Content --}}
    <div class="flex min-h-screen dark:bg-slate-900">
        {{-- Sidebar améliorée --}}
        <div class="w-80 bg-white dark:bg-slate-900">
            <div class="overflow-y-auto h-full">
                <livewire:technicien.analyses-sidebar 
                    :prescription-id="$prescription->id" 
                    :selected-parent-id="$selectedParentId"
                    wire:key="sidebar-{{ $prescription->id }}" />
            </div>
        </div>

        {{-- Main Panel --}}
        <div class="flex-1 bg-gray-50">
            <div class="p-6">
                {{-- MODE PARENT - Formulaire de saisie --}}
                @if($selectedParentId)
                    <div class="bg-white dark:bg-slate-900 rounded-lg overflow-hidden">
                        <div class="p-6">
                            <livewire:technicien.recursive-result-form 
                                :prescription-id="$prescription->id"
                                :parent-id="$selectedParentId"
                                :key="'recursive-form-'.$selectedParentId" />
                        </div>
                    </div>
 
                {{-- EMPTY STATE - Invite à sélectionner --}}
                @else
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center max-w-md">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                Sélectionnez une analyse
                            </h3>
                            <p class="text-gray-600 text-sm leading-relaxed mb-4">
                                Utilisez la barre latérale pour commencer la saisie des résultats d'analyses
                            </p>
                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 rounded-lg border border-blue-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                <span class="text-sm font-medium">Cliquez pour démarrer</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>