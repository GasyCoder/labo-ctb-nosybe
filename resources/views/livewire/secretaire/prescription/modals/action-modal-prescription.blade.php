    @if($showDeleteModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-[9999]" wire:click="resetModal">
            <div class="fixed inset-0 z-[9999] w-screen">
                <div class="flex min-h-full items-center justify-center p-4" style="margin-top: 4rem; padding-top: 2rem; padding-bottom: 4rem;">
                    <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all w-full max-w-md" wire:click.stop>
                        <div class="bg-white dark:bg-gray-800 px-6 py-6">
                            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 dark:bg-red-900/30 rounded-full mb-4">
                                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="text-center">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                                    Mettre en corbeille ?
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                                    Cette action peut être annulée depuis la corbeille.
                                </p>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                            <button wire:click="resetModal" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800 transition-colors">
                                Annuler
                            </button>
                            <button wire:click="deletePrescription" class="px-4 py-2 bg-red-600 hover:bg-red-700 border border-transparent rounded-md shadow-sm text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-gray-800 transition-colors">
                                Mettre en corbeille
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal de confirmation de restauration --}}
    @if($showRestoreModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-[9999]" wire:click="resetModal">
            <div class="fixed inset-0 z-[9999] w-screen">
                <div class="flex min-h-full items-center justify-center p-4" style="margin-top: 4rem; padding-top: 2rem; padding-bottom: 4rem;">
                    <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all w-full max-w-md" wire:click.stop>
                        <div class="bg-white dark:bg-gray-800 px-6 py-6">
                            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-amber-100 dark:bg-amber-900/30 rounded-full mb-4">
                                <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                </svg>
                            </div>
                            <div class="text-center">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                                    Restaurer cette prescription ?
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                                    Elle sera remise dans la liste active.
                                </p>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                            <button wire:click="resetModal" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800 transition-colors">
                                Annuler
                            </button>
                            <button wire:click="restorePrescription" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 border border-transparent rounded-md shadow-sm text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-800 transition-colors">
                                Restaurer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal de confirmation de suppression définitive --}}
    @if($showPermanentDeleteModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-[9999]" wire:click="resetModal">
            <div class="fixed inset-0 z-[9999] w-screen">
                <div class="flex min-h-full items-center justify-center p-4" style="margin-top: 4rem; padding-top: 2rem; padding-bottom: 4rem;">
                    <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all w-full max-w-md" wire:click.stop>
                        <div class="bg-white dark:bg-gray-800 px-6 py-6">
                            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 dark:bg-red-900/30 rounded-full mb-4">
                                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="text-center">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                                    Supprimer définitivement ?
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                                    Cette action est irréversible. La prescription sera définitivement supprimée de la base de données.
                                </p>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                            <button wire:click="resetModal" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800 transition-colors">
                                Annuler
                            </button>
                            <button wire:click="permanentDeletePrescription" class="px-4 py-2 bg-red-600 hover:bg-red-700 border border-transparent rounded-md shadow-sm text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-gray-800 transition-colors">
                                Supprimer définitivement
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal de confirmation d'archivage --}}
    @if($showArchiveModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-[9999]" wire:click="resetModal">
            <div class="fixed inset-0 z-[9999] w-screen">
                <div class="flex min-h-full items-center justify-center p-4" style="margin-top: 4rem; padding-top: 2rem; padding-bottom: 4rem;">
                    <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all w-full max-w-md" wire:click.stop>
                        <div class="bg-white dark:bg-gray-800 px-6 py-6">
                            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-gray-100 dark:bg-gray-900/30 rounded-full mb-4">
                                <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                </svg>
                            </div>
                            <div class="text-center">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                                    Archiver cette prescription ?
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                                    Elle sera déplacée vers les archives.
                                </p>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                            <button wire:click="resetModal" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800 transition-colors">
                                Annuler
                            </button>
                            <button wire:click="archivePrescription" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 border border-transparent rounded-md shadow-sm text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-offset-gray-800 transition-colors">
                                Archiver
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal de confirmation de désarchivage --}}
    @if($showUnarchiveModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-[9999]" wire:click="resetModal">
            <div class="fixed inset-0 z-[9999] w-screen">
                <div class="flex min-h-full items-center justify-center p-4" style="margin-top: 4rem; padding-top: 2rem; padding-bottom: 4rem;">
                    <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all w-full max-w-md" wire:click.stop>
                        <div class="bg-white dark:bg-gray-800 px-6 py-6">
                            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-amber-100 dark:bg-amber-900/30 rounded-full mb-4">
                                <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                </svg>
                            </div>
                            <div class="text-center">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                                    Désarchiver cette prescription ?
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                                    Cette action remettra la prescription dans les prescriptions validées.
                                </p>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                            <button wire:click="resetModal" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800 transition-colors">
                                Annuler
                            </button>
                            <button wire:click="unarchivePrescription" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 border border-transparent rounded-md shadow-sm text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-800 transition-colors">
                                Désarchiver
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif