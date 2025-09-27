@if($showConfirmModal && $prescriptionToValidate)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        {{-- Overlay --}}
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeConfirmModal"></div>

            {{-- Centrage du modal --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Contenu du modal compact --}}
            <div class="relative inline-block align-bottom bg-white dark:bg-slate-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full">
                {{-- Header --}}
                <div class="bg-white dark:bg-slate-800 px-4 pt-5 pb-4 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-green-100 dark:bg-green-900/20">
                            <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white" id="modal-title">
                                Confirmer la validation
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Valider la prescription {{ $prescriptionToValidate->reference }} ?
                                </p>
                                <div class="mt-2 text-xs text-gray-400 dark:text-gray-500">
                                    Patient: 
                                    <span class="font-bold">{{ $prescriptionToValidate->patient->nom ?? 'N/A' }} {{ $prescriptionToValidate->patient->prenom ?? '' }}</span>
                                    • {{ $prescriptionToValidate->analyses->count() }} analyse(s)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Boutons d'action --}}
                <div class="bg-gray-50 dark:bg-slate-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse sm:gap-3">
                    <button type="button" 
                            wire:click="confirmValidation"
                            wire:loading.attr="disabled"
                            wire:target="confirmValidation"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed sm:ml-3 sm:w-auto">
                        
                        <span wire:loading.remove wire:target="confirmValidation">
                           Oui Confirmer
                        </span>
                        
                        <span wire:loading wire:target="confirmValidation" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            En cours...
                        </span>
                    </button>
                    
                    <button type="button" 
                            wire:click="closeConfirmModal"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-slate-600 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif