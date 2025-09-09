{{-- Vue principale avec onglets par statut --}}
<div>
    <div class="max-w-12xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Gestion des Analyses
                    </h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Saisie et validation des résultats d'analyses
                    </p>
                </div>
                
                {{-- Indicateur système --}}
                <div class="flex items-center gap-2 px-3 py-1.5 bg-green-50 dark:bg-green-900/30 rounded-full border border-green-200 dark:border-green-800">
                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                    <span class="text-sm font-medium text-green-700 dark:text-green-400">Système en ligne</span>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- TOUTES (EN ATTENTE + EN COURS) -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white shadow-lg flex flex-col justify-between h-20">
                <div class="flex items-center justify-between h-full">
                    <div class="flex flex-col justify-between h-full">
                        <p class="text-blue-100 text-sm font-medium">TOUTES</p>
                        <p class="text-3xl font-bold">{{ $stats['toutes'] ?? 0 }}</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-lg self-start">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- TERMINÉ -->
            <div class="bg-gradient-to-r from-teal-500 to-teal-600 rounded-lg p-6 text-white shadow-lg flex flex-col justify-between h-20">
                <div class="flex items-center justify-between h-full">
                    <div class="flex flex-col justify-between h-full">
                        <p class="text-teal-100 text-sm font-medium">TERMINÉ</p>
                        <p class="text-3xl font-bold">{{ $stats['termine'] ?? 0 }}</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-lg self-start">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- À REFAIRE -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-6 text-white shadow-lg flex flex-col justify-between h-20">
                <div class="flex items-center justify-between h-full">
                    <div class="flex flex-col justify-between h-full">
                        <p class="text-red-100 text-sm font-medium">À REFAIRE</p>
                        <p class="text-3xl font-bold">{{ $stats['a_refaire'] ?? 0 }}</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-lg self-start">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtres --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 mb-8">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Recherche globale --}}
                    <div class="md:col-span-2">
                        <div class="relative">
                            <input type="text"
                                wire:model.live.debounce.300ms="search"
                                placeholder="Rechercher par patient, prescripteur..."
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white dark:bg-gray-700">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Onglets pour les statuts --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            {{-- Navigation des onglets --}}
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex">
                    <button wire:click="$set('activeTab', 'en_attente')" 
                            class="py-4 px-6 text-sm font-medium border-b-2 {{ $activeTab === 'en_attente' ? 'border-blue-500 text-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} transition-colors">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                            Toutes
                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">{{ $stats['toutes'] ?? 0 }}</span>
                        </div>
                    </button>
                    
                    <button wire:click="$set('activeTab', 'termine')" 
                            class="py-4 px-6 text-sm font-medium border-b-2 {{ $activeTab === 'termine' ? 'border-teal-500 text-teal-600 bg-teal-50 dark:bg-teal-900/20' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} transition-colors">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 bg-teal-500 rounded-full"></div>
                            Terminé
                            <span class="bg-teal-100 text-teal-800 text-xs px-2 py-1 rounded-full">{{ $stats['termine'] ?? 0 }}</span>
                        </div>
                    </button>
                    
                    <button wire:click="$set('activeTab', 'a_refaire')" 
                            class="py-4 px-6 text-sm font-medium border-b-2 {{ $activeTab === 'a_refaire' ? 'border-red-500 text-red-600 bg-red-50 dark:bg-red-900/20' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} transition-colors">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                            À refaire
                            <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">{{ $stats['a_refaire'] ?? 0 }}</span>
                        </div>
                    </button>
                </nav>
            </div>

            {{-- Contenu des onglets --}}
            <div class="overflow-x-auto">
                @if($activeTab === 'en_attente')
                    {{-- Tableau Toutes (EN_ATTENTE + EN_COURS) --}}
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-blue-50 dark:bg-blue-900/20">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 dark:text-blue-300 uppercase tracking-wider">Référence</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 dark:text-blue-300 uppercase tracking-wider">Patient</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 dark:text-blue-300 uppercase tracking-wider">Prescripteur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 dark:text-blue-300 uppercase tracking-wider">Analyses</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 dark:text-blue-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 dark:text-blue-300 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 dark:text-blue-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($prescriptionsToutes as $prescription)
                                <tr class="hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $prescription->reference }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center text-orange-600 dark:text-orange-300 font-medium text-sm mr-3">
                                                {{ substr($prescription->patient->prenom, 0, 1) }}
                                                {{ substr($prescription->patient->nom, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ Str::limit(($prescription->patient->nom ?? 'N/A') . ' ' . ($prescription->patient->prenom ?? ''), 18) }}
                                                </div>
                                                @if($prescription->patient->age && $prescription->unite_age)
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $prescription->age }} {{ $prescription->unite_age }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ Str::limit(($prescription->prescripteur->nom ?? 'N/A') . ' ' . ($prescription->prescripteur->prenom ?? 'N/A'), 18) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $prescription->analyses->count() }} analyses
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($prescription->status === 'EN_ATTENTE')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                                <div class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1"></div>
                                                En attente
                                            </span>
                                        @elseif($prescription->status === 'EN_COURS')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                                <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-1"></div>
                                                En cours
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $prescription->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($prescription->status === 'EN_ATTENTE')
                                            <button wire:click="startAnalysis({{ $prescription->id }})" 
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                                                🔬 <span>Traiter</span>
                                            </button>
                                        @elseif($prescription->status === 'EN_COURS')
                                            <button wire:click="continueAnalysis({{ $prescription->id }})" 
                                                class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                                                ▶️ <span>Continuer</span>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        Aucune analyse trouvée
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                @elseif($activeTab === 'termine')
                    {{-- Tableau Terminé --}}
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-teal-50 dark:bg-teal-900/20">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-teal-700 dark:text-teal-300 uppercase tracking-wider">Référence</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-teal-700 dark:text-teal-300 uppercase tracking-wider">Patient</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-teal-700 dark:text-teal-300 uppercase tracking-wider">Prescripteur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-teal-700 dark:text-teal-300 uppercase tracking-wider">Analyses</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-teal-700 dark:text-teal-300 uppercase tracking-wider">Date fin</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($prescriptionsTerminees as $prescription)
                                <tr class="hover:bg-teal-50 dark:hover:bg-teal-900/10 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $prescription->reference }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-teal-100 dark:bg-teal-900 rounded-full flex items-center justify-center text-teal-600 dark:text-teal-300 font-medium text-sm mr-3">
                                                {{ substr($prescription->patient->prenom, 0, 1) }}{{ substr($prescription->patient->nom, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ Str::limit(($prescription->patient->nom ?? 'N/A') . ' ' . ($prescription->patient->prenom ?? ''), 18) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ Str::limit(($prescription->prescripteur->nom ?? 'N/A') . ' ' . ($prescription->prescripteur->prenom ?? 'N/A'), 18) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $prescription->analyses->count() }} analyses
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $prescription->updated_at->format('d/m/Y H:i') }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        Aucune analyse terminée
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                @else
                    {{-- Tableau À refaire --}}
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-red-50 dark:bg-red-900/20">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-red-700 dark:text-red-300 uppercase tracking-wider">Référence</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-red-700 dark:text-red-300 uppercase tracking-wider">Patient</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-red-700 dark:text-red-300 uppercase tracking-wider">Prescripteur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-red-700 dark:text-red-300 uppercase tracking-wider">Analyses</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-red-700 dark:text-red-300 uppercase tracking-wider">Raison</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-red-700 dark:text-red-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($prescriptionsARefaire as $prescription)
                                <tr class="hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $prescription->reference }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center text-red-600 dark:text-red-300 font-medium text-sm mr-3">
                                                {{ substr($prescription->patient->prenom, 0, 1) }}{{ substr($prescription->patient->nom, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ Str::limit(($prescription->patient->nom ?? 'N/A') . ' ' . ($prescription->patient->prenom ?? ''), 18) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ Str::limit(($prescription->prescripteur->nom ?? 'N/A') . ' ' . ($prescription->prescripteur->prenom ?? 'N/A'), 18) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $prescription->analyses->count() }} analyses
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-red-600 dark:text-red-400">
                                            {{ $prescription->commentaire_biologiste ?? 'Résultats à vérifier' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                       <button wire:click="redoAnalysis({{ $prescription->id }})" 
                                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                            Recommencer
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        Aucune analyse à refaire
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @endif
            </div>

            {{-- Pagination --}}
            @if($activeTab === 'en_attente' && isset($prescriptionsToutes))
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $prescriptionsToutes->links() }}
                </div>
            @elseif($activeTab === 'termine' && isset($prescriptionsTerminees))
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $prescriptionsTerminees->links() }}
                </div>
            @elseif($activeTab === 'a_refaire' && isset($prescriptionsARefaire))
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $prescriptionsARefaire->links() }}
                </div>
            @endif
        </div>
    </div>
</div>