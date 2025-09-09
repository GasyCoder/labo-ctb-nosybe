<!-- Header Patient Optimisé -->
<div class="container mx-auto px-4 py-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <!-- Infos principales compactes -->
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center shadow-lg">
                <span class="text-white font-bold text-sm">
                    {{ strtoupper(substr($patient->nom, 0, 1) . substr($patient->prenom ?? 'X', 0, 1)) }}
                </span>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white truncate">
                    {{ $patient->civilite }} {{ $patient->nom }}{{ $patient->prenom ? ' ' . $patient->prenom : '' }}
                </h1>
                <div class="flex items-center space-x-2 mt-1">
                    <span class="inline-flex items-center px-2 py-1 bg-primary-100 dark:bg-primary-900/30 text-primary-800 dark:text-primary-300 text-xs font-semibold rounded-md">
                        {{ $patient->numero_dossier }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Actions compactes -->
        <div class="flex items-center space-x-2">
            <a href="{{ route('secretaire.patients') }}" 
                class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 border border-gray-300 dark:border-gray-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Retour
            </a>
            <button 
                wire:click="deletePatient"
                wire:confirm="Êtes-vous sûr de vouloir supprimer ce patient ? Cette action est irréversible."
                class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-all duration-200 shadow-sm hover:shadow">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Supprimer
            </button>
        </div>
    </div>
</div>
