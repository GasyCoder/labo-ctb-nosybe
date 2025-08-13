<div class="bg-white dark:bg-slate-900 shadow-lg border-b border-slate-200 dark:border-slate-800">
    <div class="px-6 py-6">
        {{-- Top Section: Title & Actions --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div
                    class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 dark:from-primary-600 dark:to-primary-700 rounded-xl shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl lg:text-2xl font-bold text-slate-900 dark:text-slate-100">
                        {{ $prescription->reference }}
                    </h1>
                    <p class="text-slate-600 dark:text-slate-400 text-sm">Détail de la prescription médicale</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                {{-- Retour Button --}}
                @if (auth()->user()->type === 'technicien')
                    <a href="{{ route('technicien.index') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-slate-800 transition-all duration-200 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                        Retour
                    </a>
                @elseif(auth()->user()->type === 'biologiste')
                    <a href="{{ route('biologiste.analyse.index') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-slate-800 transition-all duration-200 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                        Retour
                    </a>
                @endif

                {{-- Status Badge --}}
                @if (auth()->user()->type === 'technicien')
                    <div
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold border
                    @if ($prescription->status === 'EN_ATTENTE') bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 border-yellow-200 dark:border-yellow-700
                    @elseif($prescription->status === 'EN_COURS') 
                        bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 border-primary-200 dark:border-primary-700
                    @elseif($prescription->status === 'TERMINE') 
                        bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 border-green-200 dark:border-green-700
                    @else 
                        bg-slate-50 dark:bg-slate-800/30 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700 @endif">
                        <div
                            class="w-2.5 h-2.5 rounded-full 
                        @if ($prescription->status === 'EN_ATTENTE') bg-yellow-500
                        @elseif($prescription->status === 'EN_COURS') bg-primary-500
                        @elseif($prescription->status === 'TERMINE') bg-green-500
                        @else bg-slate-500 @endif">
                        </div>
                        {{ $prescription->status_label }}
                    </div>
                @endif

               @if (auth()->user()->type === 'biologiste')
    <button wire:click="markAsToRedo"
        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 border border-blue-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-slate-800 transition-all duration-200 shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
                d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                clip-rule="evenodd" />
        </svg>
        A refaire
    </button>
    <button wire:click="validatePrescription"
        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-red-600 hover:bg-red-700 border border-red-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-slate-800 transition-all duration-200 shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                clip-rule="evenodd" />
        </svg>
        Valider
    </button>
@endif
            </div>
        </div>

        {{-- Rest of your code remains the same --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- ... (le reste du code des cartes d'information reste inchangé) ... -->
        </div>
    </div>
</div>
