    <!-- Modal de Suppression -->
    @if($showDeleteModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50">
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <div class="bg-white dark:bg-gray-800 px-6 py-6">
                            <div class="flex items-center mb-4">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30">
                                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-center">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Supprimer le prescripteur</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                    Êtes-vous sûr de vouloir supprimer <strong>{{ $prescripteurToDelete?->nom_complet }}</strong> ?
                                    @if($prescripteurToDelete?->prescriptions()->count() > 0)
                                        <br><span class="text-orange-600 dark:text-orange-400 font-medium">
                                            Ce prescripteur a {{ $prescripteurToDelete->prescriptions()->count() }} prescription(s).
                                            Il sera archivé et non supprimé définitivement.
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                            <button 
                                wire:click="$set('showDeleteModal', false)"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800 transition-colors"
                            >
                                Annuler
                            </button>
                            <button 
                                wire:click="deletePrescripteur"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 border border-transparent rounded-md shadow-sm text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-gray-800 transition-colors"
                            >
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif