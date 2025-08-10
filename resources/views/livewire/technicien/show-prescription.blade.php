<div class="mt-8 min-h-screen bg-slate-50 dark:bg-slate-950">
    {{-- Header optimisé --}}
    @include('livewire.technicien.partials.header-prescription-technicien')

    {{-- Main Content --}}
    <div class="flex min-h-screen">
        {{-- Sidebar améliorée --}}
        <div class="w-80 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 shadow-lg">
            <div class="overflow-y-auto h-full">
                <livewire:technicien.analyses-sidebar :prescription-id="$prescription->id" />
            </div>
        </div>

        {{-- Main Panel --}}
        <div class="flex-1 bg-slate-50 dark:bg-slate-900/50">
            <div class="p-6">
                {{-- MODE PARENT --}}
                @if($selectedParentId)
                    <div class="bg-white dark:bg-slate-900 rounded-lg shadow-lg border border-slate-200 dark:border-slate-800 overflow-hidden">
                        <div class="p-6">
                            <livewire:technicien.recursive-result-form 
                                :prescription-id="$prescription->id"
                                :parent-id="$selectedParentId"
                                :key="'recursive-form-'.$selectedParentId" />
                        </div>
                    </div>

                {{-- EMPTY STATE inspiré du design d'archive --}}
                @else
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center max-w-md">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-700 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-2">
                                Sélectionnez un panel ou une analyse
                            </h3>
                            <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed mb-4">
                                Utilisez la barre latérale pour commencer la saisie des résultats
                            </p>
                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 rounded-lg border border-primary-200 dark:border-primary-800">
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