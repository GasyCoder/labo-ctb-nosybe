@if($activeTab === 'analyses')
    <div class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-sm rounded-2xl shadow-xl shadow-gray-200/50 dark:shadow-gray-900/50 border border-gray-100 dark:border-gray-700/50 overflow-hidden transition-all duration-500">
        
        <!-- Header -->
        <div class="relative px-6 py-4 bg-gradient-to-br from-slate-50 via-white to-blue-50/30 dark:from-gray-800 dark:via-gray-800 dark:to-blue-900/20 border-b border-gray-100/50 dark:border-gray-700/50">
            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent opacity-50"></div>
            
            <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="p-2.5 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg shadow-lg shadow-blue-500/25">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-200 bg-clip-text text-transparent">
                            Prescriptions
                        </h3>
                        @if($searchPrescriptions)
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded-full text-xs font-medium">
                                    {{ $prescriptionsFiltrees->count() }} résultat{{ $prescriptionsFiltrees->count() > 1 ? 's' : '' }}
                                </span>
                            </p>
                        @endif
                    </div>
                </div>
                
                <!-- Barre de recherche compacte -->
                <div class="relative max-w-xs w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        wire:model.live="searchPrescriptions"
                        placeholder="Rechercher..."
                        class="block w-full pl-10 pr-10 py-2 text-sm bg-white/80 dark:bg-gray-700/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-600/50 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all duration-200 shadow-sm hover:shadow-md focus:shadow-lg">
                    
                    @if($searchPrescriptions)
                        <button 
                            wire:click.prevent="resetSearch"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center group z-10"
                            title="Effacer la recherche">
                            <div class="p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600">
                                <svg class="h-3.5 w-3.5 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                        </button>
                    @endif
                </div>
            </div>
            
            <!-- Filtres -->
            @if(!$searchPrescriptions)
                <div class="flex flex-wrap gap-2 mt-4">
                    <button 
                        wire:click.prevent="filtrerParStatut('')"
                        wire:key="filter-toutes"
                        class="group inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 z-10
                            {{ !$filtreStatut ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-md shadow-blue-500/20 ring-1 ring-blue-500/20' : 'bg-white/80 dark:bg-gray-700/80 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 shadow-sm hover:shadow-md border border-gray-200/50 dark:border-gray-600/50' }}"
                        title="Afficher toutes les prescriptions">
                        <span class="w-1.5 h-1.5 rounded-full {{ !$filtreStatut ? 'bg-white/80' : 'bg-blue-500' }} mr-1.5"></span>
                        Toutes
                        <span class="ml-1.5 px-1.5 py-0.5 text-[0.65rem] rounded-full {{ !$filtreStatut ? 'bg-white/20' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' }}">
                            {{ $patient->prescriptions->count() }}
                        </span>
                    </button>
                    <!-- Other filter buttons remain unchanged -->
                    <button 
                        wire:click.prevent="filtrerParStatut('EN_ATTENTE')"
                        wire:key="filter-en-attente"
                        class="group inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 z-10
                            {{ $filtreStatut === 'EN_ATTENTE' ? 'bg-gradient-to-r from-amber-400 to-orange-500 text-white shadow-md shadow-amber-500/20 ring-1 ring-amber-500/20' : 'bg-white/80 dark:bg-gray-700/80 text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20 shadow-sm hover:shadow-md border border-gray-200/50 dark:border-gray-600/50' }}"
                        title="Afficher les prescriptions en attente">
                        <span class="w-1.5 h-1.5 rounded-full {{ $filtreStatut === 'EN_ATTENTE' ? 'bg-white/80' : 'bg-amber-400' }} mr-1.5 animate-pulse"></span>
                        En attente
                        <span class="ml-1.5 px-1.5 py-0.5 text-[0.65rem] rounded-full {{ $filtreStatut === 'EN_ATTENTE' ? 'bg-white/20' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300' }}">
                            {{ $prescriptionsEnAttente }}
                        </span>
                    </button>
                    <button 
                        wire:click.prevent="filtrerParStatut('EN_COURS')"
                        wire:key="filter-en-cours"
                        class="group inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 z-10
                            {{ $filtreStatut === 'EN_COURS' ? 'bg-gradient-to-r from-blue-500 to-cyan-500 text-white shadow-md shadow-blue-500/20 ring-1 ring-blue-500/20' : 'bg-white/80 dark:bg-gray-700/80 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 shadow-sm hover:shadow-md border border-gray-200/50 dark:border-gray-600/50' }}"
                        title="Afficher les prescriptions en cours">
                        <span class="w-1.5 h-1.5 rounded-full {{ $filtreStatut === 'EN_COURS' ? 'bg-white/80' : 'bg-blue-500' }} mr-1.5"></span>
                        En cours
                        <span class="ml-1.5 px-1.5 py-0.5 text-[0.65rem] rounded-full {{ $filtreStatut === 'EN_COURS' ? 'bg-white/20' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' }}">
                            {{ $prescriptionsEnCours }}
                        </span>
                    </button>
                    <button 
                        wire:click.prevent="filtrerParStatut('TERMINE')"
                        wire:key="filter-termine"
                        class="group inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 z-10
                            {{ $filtreStatut === 'TERMINE' ? 'bg-gradient-to-r from-emerald-500 to-green-600 text-white shadow-md shadow-emerald-500/20 ring-1 ring-emerald-500/20' : 'bg-white/80 dark:bg-gray-700/80 text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 shadow-sm hover:shadow-md border border-gray-200/50 dark:border-gray-600/50' }}"
                        title="Afficher les prescriptions terminées">
                        <span class="w-1.5 h-1.5 rounded-full {{ $filtreStatut === 'TERMINE' ? 'bg-white/80' : 'bg-emerald-500' }} mr-1.5"></span>
                        Terminées
                        <span class="ml-1.5 px-1.5 py-0.5 text-[0.65rem] rounded-full {{ $filtreStatut === 'TERMINE' ? 'bg-white/20' : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' }}">
                            {{ $prescriptionsTerminees }}
                        </span>
                    </button>
                </div>
            @endif
        </div>
        
        <!-- Prescription list -->
        <div wire:key="prescriptions-list" class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" wire:loading.remove wire:target="searchPrescriptions,filtrerParStatut,resetSearch">
            @if($prescriptionsFiltrees->count() > 0)
                @foreach($prescriptionsFiltrees as $prescription)
                    <div wire:key="prescription-{{ $prescription->id }}" class="group relative bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-xl border border-gray-100/50 dark:border-gray-700/50 shadow-lg shadow-gray-200/50 dark:shadow-gray-900/50 hover:shadow-xl hover:shadow-gray-200/60 dark:hover:shadow-gray-900/60 transition-all duration-300 hover:scale-[1.02] hover:-translate-y-0.5
                        {{ $prescription->created_at->isAfter(now()->subDays(30)) ? 'ring-1 ring-blue-200/50 dark:ring-blue-800/50 bg-gradient-to-br from-blue-50/30 via-white/60 to-indigo-50/30 dark:from-blue-900/10 dark:via-gray-800/60 dark:to-indigo-900/10' : '' }}">
                        <!-- Existing prescription card content -->
                        @if($prescription->created_at->isAfter(now()->subDays(30)))
                            <div class="absolute -top-2 -right-2 z-10">
                                <div class="relative">
                                    <div class="px-2 py-1 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs font-bold rounded-full shadow-lg shadow-blue-500/30 animate-pulse">
                                        <span class="flex items-center space-x-1">
                                            <span class="w-1.5 h-1.5 bg-white rounded-full"></span>
                                            <span>Récente</span>
                                        </span>
                                    </div>
                                    <div class="absolute inset-0 bg-blue-400 rounded-full blur-md opacity-30 -z-10"></div>
                                </div>
                            </div>
                        @endif
                        <div class="p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white truncate">
                                        @if($searchPrescriptions && stripos($prescription->reference, $searchPrescriptions) !== false)
                                            {!! str_ireplace($searchPrescriptions, '<mark class="bg-gradient-to-r from-yellow-200 to-amber-200 dark:from-yellow-800/50 dark:to-amber-800/50 text-gray-900 dark:text-white px-1 rounded">' . $searchPrescriptions . '</mark>', $prescription->reference) !!}
                                        @else
                                            {{ $prescription->reference }}
                                        @endif
                                    </h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $prescription->created_at->format('d/m/Y') }}
                                    </p>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                    @if($prescription->status === 'EN_ATTENTE') bg-gradient-to-r from-amber-100 to-yellow-100 dark:from-amber-900/30 dark:to-yellow-900/30 text-amber-800 dark:text-amber-300
                                    @elseif($prescription->status === 'EN_COURS') bg-gradient-to-r from-blue-100 to-cyan-100 dark:from-blue-900/30 dark:to-cyan-900/30 text-blue-800 dark:text-blue-300
                                    @elseif($prescription->status === 'TERMINE') bg-gradient-to-r from-emerald-100 to-green-100 dark:from-emerald-900/30 dark:to-green-900/30 text-emerald-800 dark:text-emerald-300
                                    @else bg-gradient-to-r from-gray-100 to-slate-100 dark:from-gray-800 dark:to-slate-800 text-gray-800 dark:text-gray-300 @endif">
                                    @if($prescription->status === 'EN_ATTENTE')
                                        <span class="w-1.5 h-1.5 bg-amber-400 rounded-full mr-1 animate-pulse"></span>
                                    @elseif($prescription->status === 'EN_COURS')
                                        <span class="w-1.5 h-1.5 bg-blue-400 rounded-full mr-1"></span>
                                    @elseif($prescription->status === 'TERMINE')
                                        <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full mr-1"></span>
                                    @else
                                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1"></span>
                                    @endif
                                    {{ $prescription->status_label ?? $prescription->status }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-xs mb-3">
                                <span class="text-gray-600 dark:text-gray-400 font-medium">
                                    Dr. {{ $prescription->prescripteur->nom ?? 'Non spécifié' }}
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 bg-blue-100/80 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded-full">
                                    {{ $prescription->analyses->count() }} analyse{{ $prescription->analyses->count() > 1 ? 's' : '' }}
                                </span>
                            </div>
                            <div class="space-y-2 mb-3">
                                @foreach($prescription->analyses->take(2) as $analyse)
                                    <div class="flex items-center space-x-2">
                                        <div class="flex-shrink-0 w-2 h-2 rounded-full 
                                            @if($prescription->status === 'TERMINE') bg-emerald-500
                                            @elseif($prescription->status === 'EN_COURS') bg-blue-500
                                            @else bg-amber-400 @endif">
                                        </div>
                                        <p class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate">
                                            @if($searchPrescriptions && stripos($analyse->designation, $searchPrescriptions) !== false)
                                                {!! str_ireplace($searchPrescriptions, '<mark class="bg-gradient-to-r from-yellow-200 to-amber-200 dark:from-yellow-800/50 dark:to-amber-800/50 text-gray-900 dark:text-white px-1 rounded">' . $searchPrescriptions . '</mark>', $analyse->designation) !!}
                                            @else
                                                {{ $analyse->designation }}
                                            @endif
                                        </p>
                                    </div>
                                @endforeach
                                @if($prescription->analyses->count() > 2)
                                    <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                                        +{{ $prescription->analyses->count() - 2 }} autres analyses...
                                    </div>
                                @endif
                            </div>
                            <div class="flex items-center justify-between pt-3 border-t border-gray-100/50 dark:border-gray-600/50">
                                @if($prescription->montant_total > 0)
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">
                                        {{ number_format($prescription->montant_total, 0, ',', ' ') }} Ar
                                    </span>
                                @endif
                                <button 
                                    wire:click="voirPrescription({{ $prescription->id }})"
                                    class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors duration-200 flex items-center">
                                    Résultats
                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-span-full py-12 text-center bg-gradient-to-b from-gray-50/30 to-white dark:from-gray-800/30 dark:to-gray-800 rounded-xl">
                    <div class="max-w-xs mx-auto">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto shadow-md shadow-gray-200/50 dark:shadow-gray-900/50 mb-4">
                            @if($searchPrescriptions)
                                <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            @endif
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            @if($searchPrescriptions)
                                Aucun résultat
                            @else
                                Aucune prescription
                            @endif
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            @if($searchPrescriptions)
                                Aucune correspondance pour "{{ $searchPrescriptions }}"
                            @else
                                Ce patient n'a pas encore de prescriptions
                            @endif
                        </p>
                        @if($searchPrescriptions)
                            <button 
                                wire:click.prevent="resetSearch"
                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs font-semibold rounded-lg shadow-md shadow-blue-500/20 hover:shadow-lg transition-all duration-200">
                                Effacer la recherche
                            </button>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif