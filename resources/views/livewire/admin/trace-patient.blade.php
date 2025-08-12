<!-- resources/views/livewire/admin/trace-patient.blade.php -->
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <i class="fas fa-trash-restore text-red-600 dark:text-red-400 text-xl mr-3"></i>
                        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Gestion de la Corbeille</h1>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Compteur global -->
                    <div class="text-sm bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 px-3 py-1 rounded-full">
                        <i class="fas fa-users mr-1"></i>
                        {{ $totalCount }} élément(s) supprimé(s)
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="p-6">
       <!-- Onglets -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
            <!-- Onglet Prescriptions (premier) -->
            <button
                wire:click="$set('activeTab', 'prescriptions')"
                class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'prescriptions' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                
                <!-- SVG Prescription -->
                <svg class="w-4 h-4 inline-block mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M6 2v6h.01L6 8.01 10 12l-4 4 .01.01H6V22h12v-5.99h-.01L18 16l-4-4 4-3.99-.01-.01H18V2H6zm10 14.5V20H8v-3.5l4-4 4 4zm0-9V12l-4 4-4-4V7.5h8z"/>
                </svg>
                Prescriptions ({{ $prescriptionsCount }})
            </button>
            
            <!-- Onglet Patients (deuxième) -->
            <button
                wire:click="$set('activeTab', 'patients')"
                class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'patients' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                
                <!-- SVG Patient -->
                <svg class="w-4 h-4 inline-block mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/>
                </svg>
                Patients ({{ $patientsCount }})
            </button>
        </nav>
    </div>
</div>


        <!-- Statistiques Patients -->
        @if($activeTab === 'patients')
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <!-- Total patients supprimés -->
            <div class="bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 px-4 py-3 rounded-xl border border-red-200 dark:border-red-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-red-600 dark:text-red-400 uppercase tracking-wide">Total patients</p>
                        <p class="text-xl font-bold text-red-800 dark:text-red-300">{{ $patientsCount }}</p>
                    </div>
                    <i class="fas fa-user-injured text-red-600 dark:text-red-400 text-xl"></i>
                </div>
            </div>
            
            <!-- Patients récents -->
            <div class="bg-gradient-to-r from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 px-4 py-3 rounded-xl border border-orange-200 dark:border-orange-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-orange-600 dark:text-orange-400 uppercase tracking-wide">Récents</p>
                        <p class="text-xl font-bold text-orange-800 dark:text-orange-300">{{ $patientsRecentCount }}</p>
                        <p class="text-xs text-orange-700 dark:text-orange-400 mt-1">(7 derniers jours)</p>
                    </div>
                    <i class="fas fa-clock text-orange-600 dark:text-orange-400 text-xl"></i>
                </div>
            </div>
            
            <!-- Patients anciens -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900/20 dark:to-gray-800/20 px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Anciens</p>
                        <p class="text-xl font-bold text-gray-800 dark:text-gray-300">{{ $patientsOldCount }}</p>
                        <p class="text-xs text-gray-700 dark:text-gray-400 mt-1">(plus de 30 jours)</p>
                    </div>
                    <i class="fas fa-history text-gray-600 dark:text-gray-400 text-xl"></i>
                </div>
            </div>

            <!-- Avec prescriptions -->
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 px-4 py-3 rounded-xl border border-blue-200 dark:border-blue-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wide">Avec prescriptions</p>
                        <p class="text-xl font-bold text-blue-800 dark:text-blue-300">{{ $patientsWithPrescriptionsCount }}</p>
                        <p class="text-xs text-blue-700 dark:text-blue-400 mt-1">(à vérifier)</p>
                    </div>
                    <i class="fas fa-file-medical text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>
        @endif

        <!-- Statistiques Prescriptions -->
        @if($activeTab === 'prescriptions')
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <!-- Total prescriptions supprimées -->
            <div class="bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 px-4 py-3 rounded-xl border border-purple-200 dark:border-purple-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-purple-600 dark:text-purple-400 uppercase tracking-wide">Total prescriptions</p>
                        <p class="text-xl font-bold text-purple-800 dark:text-purple-300">{{ $prescriptionsCount }}</p>
                    </div>
                    <i class="fas fa-prescription-bottle-alt text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
            
            <!-- Prescriptions récentes -->
            <div class="bg-gradient-to-r from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 px-4 py-3 rounded-xl border border-orange-200 dark:border-orange-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-orange-600 dark:text-orange-400 uppercase tracking-wide">Récentes</p>
                        <p class="text-xl font-bold text-orange-800 dark:text-orange-300">{{ $prescriptionsRecentCount }}</p>
                        <p class="text-xs text-orange-700 dark:text-orange-400 mt-1">(7 derniers jours)</p>
                    </div>
                    <i class="fas fa-clock text-orange-600 dark:text-orange-400 text-xl"></i>
                </div>
            </div>
            
            <!-- Prescriptions anciennes -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900/20 dark:to-gray-800/20 px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Anciennes</p>
                        <p class="text-xl font-bold text-gray-800 dark:text-gray-300">{{ $prescriptionsOldCount }}</p>
                        <p class="text-xs text-gray-700 dark:text-gray-400 mt-1">(plus de 30 jours)</p>
                    </div>
                    <i class="fas fa-history text-gray-600 dark:text-gray-400 text-xl"></i>
                </div>
            </div>

            <!-- Valeur totale -->
            <div class="bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 px-4 py-3 rounded-xl border border-green-200 dark:border-green-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wide">Valeur perdue</p>
                        <p class="text-xl font-bold text-green-800 dark:text-green-300">{{ number_format($prescriptionsTotalValue, 0, ',', ' ') }} F</p>
                        <p class="text-xs text-green-700 dark:text-green-400 mt-1">(montant total)</p>
                    </div>
                    <i class="fas fa-money-bill-wave text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
        </div>
        @endif

        <!-- Contenu principal -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
            <!-- Barre de recherche et actions -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <!-- Recherche -->
                    <div class="relative flex-1 max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input 
                            type="text" 
                            wire:model.debounce.300ms="search" 
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                            placeholder="Rechercher...">
                    </div>
                    <!-- Actions -->
                    <div class="flex items-center space-x-2">
                        @if($activeTab === 'patients')
                        <!-- Bouton vider la corbeille patients -->
                        <button 
                            wire:click="confirmEmptyPatientsTrash"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors flex items-center space-x-2"
                            @if($patients->isEmpty()) disabled @endif>
                            <i class="fas fa-trash"></i>
                            <span>Vider corbeille patients</span>
                        </button>
                        @else
                        <!-- Bouton vider la corbeille prescriptions -->
                        <button 
                            wire:click="confirmEmptyPrescriptionsTrash"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors flex items-center space-x-2"
                            @if($prescriptions->isEmpty()) disabled @endif>
                            <i class="fas fa-trash"></i>
                            <span>Vider corbeille prescriptions</span>
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tableau Patients -->
            @if($activeTab === 'patients')
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Patient
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Dossier
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Prescriptions liées
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Supprimé le
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @forelse($patients as $patient)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-red-100 dark:bg-red-900/20 rounded-full flex items-center justify-center">
                                        <span class="text-red-800 dark:text-red-200 font-medium">
                                            {{ strtoupper(substr($patient->nom, 0, 1)) }}{{ strtoupper(substr($patient->prenom, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $patient->nom }} {{ $patient->prenom }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $patient->telephone }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $patient->numero_dossier }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($patient->prescriptions_count > 0)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    {{ $patient->prescriptions_count }} prescription(s)
                                </span>
                                @else
                                <span class="text-gray-400 dark:text-gray-500 text-sm">Aucune</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $patient->deleted_at->format('d/m/Y H:i') }}
                                <div class="text-xs text-gray-400 dark:text-gray-500">
                                    {{ $patient->deleted_at->diffForHumans() }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <!-- Bouton Restaurer -->
                                    <button 
                                        wire:click="restorePatient({{ $patient->id }})"
                                        class="p-2 text-green-600 hover:text-green-900 hover:bg-green-100 dark:hover:bg-green-900/20 rounded-full transition-colors"
                                        title="Restaurer">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M3 4v6h6V8H6.41C7.79 5.61 10.68 4 14 4c4.42 0 8 3.58 8 8s-3.58 8-8 8c-2.21 0-4.21-.9-5.65-2.35l-1.42 1.42A9.985 9.985 0 0 0 14 22c5.52 0 10-4.48 10-10S19.52 2 14 2C10.13 2 6.84 4.07 5.17 7H3z"/>
                                        </svg>
                                    </button>
                                    
                                    <!-- Bouton Supprimer -->
                                    <button 
                                        wire:click="confirmForceDeletePatient({{ $patient->id }})"
                                        class="p-2 text-red-600 hover:text-red-900 hover:bg-red-100 dark:hover:bg-red-900/20 rounded-full transition-colors"
                                        title="Supprimer définitivement">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M9 3V4H4V6H5V19C5 20.1 5.9 21 7 21H17C18.1 21 19 20.1 19 19V6H20V4H15V3H9ZM7 6H17V19H7V6ZM9 8V17H11V8H9ZM13 8V17H15V8H13Z"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center justify-center py-12">
                                    <i class="fas fa-user-injured text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                    <p class="text-lg font-medium text-gray-500 dark:text-gray-400">Aucun patient dans la corbeille</p>
                                    @if($search)
                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Aucun résultat pour "{{ $search }}"</p>
                                    <button 
                                        wire:click="$set('search', '')"
                                        class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        Réinitialiser la recherche
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Patients -->
            @if($patients->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 rounded-b-lg">
                {{ $patients->links() }}
            </div>
            @endif
            @endif

            <!-- Tableau Prescriptions -->
            @if($activeTab === 'prescriptions')
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Référence
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Patient
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Prescripteur
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Montant
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Supprimé le
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @forelse($prescriptions as $prescription)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                    {{ $prescription->reference }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $prescription->patient->nom ?? 'N/A' }} {{ $prescription->patient->prenom ?? '' }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $prescription->patient->numero_dossier ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $prescription->prescripteur->nom ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ number_format($prescription->montant_total, 0, ',', ' ') }} F
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $prescription->deleted_at->format('d/m/Y H:i') }}
                                <div class="text-xs text-gray-400 dark:text-gray-500">
                                    {{ $prescription->deleted_at->diffForHumans() }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <!-- Bouton Restaurer -->
                                    <button 
                                        wire:click="restorePrescription({{ $prescription->id }})"
                                        class="p-2 text-green-600 hover:text-green-900 hover:bg-green-100 dark:hover:bg-green-900/20 rounded-full transition-colors"
                                        title="Restaurer">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M3 4v6h6V8H6.41C7.79 5.61 10.68 4 14 4c4.42 0 8 3.58 8 8s-3.58 8-8 8c-2.21 0-4.21-.9-5.65-2.35l-1.42 1.42A9.985 9.985 0 0 0 14 22c5.52 0 10-4.48 10-10S19.52 2 14 2C10.13 2 6.84 4.07 5.17 7H3z"/>
                                        </svg>
                                    </button>
                                    
                                    <!-- Bouton Supprimer -->
                                    <button 
                                        wire:click="confirmForceDeletePrescription({{ $prescription->id }})"
                                        class="p-2 text-red-600 hover:text-red-900 hover:bg-red-100 dark:hover:bg-red-900/20 rounded-full transition-colors"
                                        title="Supprimer définitivement">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M9 3V4H4V6H5V19C5 20.1 5.9 21 7 21H17C18.1 21 19 20.1 19 19V6H20V4H15V3H9ZM7 6H17V19H7V6ZM9 8V17H11V8H9ZM13 8V17H15V8H13Z"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center justify-center py-12">
                                    <i class="fas fa-prescription-bottle-alt text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                    <p class="text-lg font-medium text-gray-500 dark:text-gray-400">Aucune prescription dans la corbeille</p>
                                    @if($search)
                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Aucun résultat pour "{{ $search }}"</p>
                                    <button 
                                        wire:click="$set('search', '')"
                                        class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        Réinitialiser la recherche
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Prescriptions -->
            @if($prescriptions->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 rounded-b-lg">
                {{ $prescriptions->links() }}
            </div>
            @endif
            @endif
        </div>
    </div>

    <!-- Modals de confirmation -->
    @include('livewire.admin.trace-patient.modals')
</div>