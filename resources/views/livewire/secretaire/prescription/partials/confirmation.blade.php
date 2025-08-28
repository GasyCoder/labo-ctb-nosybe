@if($etape === 'confirmation')
    <div class="max-w-md mx-auto">
        <!-- Message de succès en haut -->
        <div class="bg-white dark:bg-slate-800 rounded-t-lg shadow-sm border {{ $isEditMode ? 'border-orange-200 dark:border-orange-800' : 'border-green-200 dark:border-green-800' }} p-4 text-center mb-0">
            <div class="w-12 h-12 {{ $isEditMode ? 'bg-orange-50 dark:bg-orange-900/20' : 'bg-green-50 dark:bg-green-900/20' }} rounded-full flex items-center justify-center mx-auto mb-3">
                <em class="ni ni-{{ $isEditMode ? 'edit' : 'check-circle' }} text-xl {{ $isEditMode ? 'text-orange-500 dark:text-orange-400' : 'text-green-500 dark:text-green-400' }}"></em>
            </div>
            
            <h2 class="text-lg font-semibold {{ $isEditMode ? 'text-orange-900 dark:text-orange-100' : 'text-green-900 dark:text-green-100' }} mb-2">
                @if($isEditMode)
                    Prescription modifiée avec succès !
                @else
                    Prescription enregistrée avec succès !
                @endif
            </h2>
            <p class="text-sm text-slate-600 dark:text-slate-300">
                @if($isEditMode)
                    Les modifications ont été sauvegardées.
                @else
                    La nouvelle prescription est prête.
                @endif
            </p>
        </div>

        <!-- Ticket style facture -->
        <div class="bg-white dark:bg-slate-800 rounded-b-lg shadow-sm border {{ $isEditMode ? 'border-orange-200 dark:border-orange-800' : 'border-green-200 dark:border-green-800' }} border-t-0 p-4">
        <!-- En-tête ticket -->
<div class="text-center border-b border-dashed border-gray-200 dark:border-slate-700 pb-2 mb-3">
    <h3 class="font-medium text-slate-800 dark:text-slate-100">
        {{ $this->getTitle() }}
    </h3>
    <p class="text-xs text-slate-500 dark:text-slate-400">
        {{ now()->format('d/m/Y H:i') }}
    </p>
</div>
            <!-- Corps du ticket -->
            <div class="text-sm space-y-2 mb-4">
                <div class="flex justify-between">
                    <span class="font-medium text-slate-700 dark:text-slate-300">Patient:</span>
                    <span class="text-slate-900 dark:text-slate-100">
                        {{ $patient->nom ?? '' }} {{ $patient->prenom ?? ''}}
                        @if($patient->latest_age ?? '')({{ $patient->latest_age ?? ''}} ans)@endif
                    </span>
                </div>

                <div class="flex justify-between">
                    <span class="font-medium text-slate-700 dark:text-slate-300">Analyses:</span>
                    <span class="text-slate-900 dark:text-slate-100">{{ count($analysesPanier) }}</span>
                </div>

                @if(!empty($prelevementsSelectionnes))
                <div class="flex justify-between">
                    <span class="font-medium text-slate-700 dark:text-slate-300">Prélèvements:</span>
                    <span class="text-slate-900 dark:text-slate-100">{{ count($prelevementsSelectionnes) }}</span>
                </div>
                @endif

                @if(!empty($tubesGeneres))
                <div class="flex justify-between">
                    <span class="font-medium text-slate-700 dark:text-slate-300">Tubes:</span>
                    <span class="text-slate-900 dark:text-slate-100">{{ count($tubesGeneres) }}</span>
                </div>
                @endif

                <div class="border-t border-dashed border-gray-200 dark:border-slate-700 pt-2 mt-2">
                    <div class="flex justify-between font-bold">
                        <span class="text-slate-800 dark:text-slate-200">MONTANT TOTAL:</span>
                        <span class="{{ $isEditMode ? 'text-orange-600 dark:text-orange-400' : 'text-green-600 dark:text-green-400' }}">
                            {{ number_format($total, 0) }} Ar
                        </span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mt-4">
                <button wire:click="nouveauPrescription" 
                        class="flex items-center justify-center px-3 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm">
                    <em class="ni ni-plus mr-1 text-xs"></em> Nouvelle
                </button>
                
                <button class="flex items-center justify-center px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 rounded-lg text-sm">
                    <em class="ni ni-printer mr-1 text-xs"></em> Imprimer
                </button>
                
                <a href="{{ route('secretaire.prescription.index') }}" 
                   wire:navigate
                   class="flex items-center justify-center px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 rounded-lg text-sm">
                    <em class="ni ni-list mr-1 text-xs"></em> Liste
                </a>
            </div>
        </div>
    </div>
@endif