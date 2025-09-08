<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Dashboard Secrétaire') }}
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                    Vue d'ensemble des activités du laboratoire
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <select wire:model.live="periode" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm w-full sm:w-auto">
                    <option value="7">7 derniers jours</option>
                    <option value="30">30 derniers jours</option>
                    <option value="90">3 derniers mois</option>
                    <option value="365">Dernière année</option>
                </select>
                
                <button wire:click="refreshDashboard" 
                        class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors duration-200 w-full sm:w-auto">
                    <svg wire:loading.remove wire:target="refreshDashboard" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <svg wire:loading wire:target="refreshDashboard" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Actualiser
                </button>
            </div>
        </div>
        
        @if($lastUpdated)
            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                Dernière mise à jour : {{ $lastUpdated->format('d/m/Y à H:i:s') }}
            </div>
        @endif
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- Messages flash --}}
            @if (session()->has('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('message') }}</span>
                </div>
            @endif

            {{-- Cartes de statistiques principales --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                {{-- Patients --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Patients</p>
                                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                                    {{ number_format($stats['patients']['total'] ?? 0) }}
                                </p>
                            </div>
                            <div class="p-2 sm:p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                                <svg class="w-5 sm:w-6 h-5 sm:h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                        </div>
                        @if(isset($stats['patients']['evolution']))
                            <div class="mt-2 sm:mt-4 flex items-center">
                                <span class="text-{{ $stats['patients']['evolution'] >= 0 ? 'green' : 'red' }}-600 text-sm font-medium">
                                    {{ $stats['patients']['evolution'] >= 0 ? '↗' : '↘' }} {{ abs($stats['patients']['evolution']) }}%
                                </span>
                                <span class="text-gray-600 dark:text-gray-400 text-sm ml-1 sm:ml-2">vs période précédente</span>
                            </div>
                        @endif
                        <div class="mt-2">
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                <span class="font-medium text-blue-600">{{ $stats['patients']['nouveaux'] ?? 0 }}</span> nouveaux patients
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Prescriptions --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Prescriptions</p>
                                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                                    {{ number_format($stats['prescriptions']['periode'] ?? 0) }}
                                </p>
                            </div>
                            <div class="p-2 sm:p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                                <svg class="w-5 sm:w-6 h-5 sm:h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                        @if(isset($stats['prescriptions']['evolution']))
                            <div class="mt-2 sm:mt-4 flex items-center">
                                <span class="text-{{ $stats['prescriptions']['evolution'] >= 0 ? 'green' : 'red' }}-600 text-sm font-medium">
                                    {{ $stats['prescriptions']['evolution'] >= 0 ? '↗' : '↘' }} {{ abs($stats['prescriptions']['evolution']) }}%
                                </span>
                                <span class="text-gray-600 dark:text-gray-400 text-sm ml-1 sm:ml-2">vs période précédente</span>
                            </div>
                        @endif
                        <div class="mt-2 flex gap-2 sm:gap-4 text-xs sm:text-sm">
                            <span class="text-yellow-600">⏳ {{ $stats['prescriptions']['en_attente'] ?? 0 }} en attente</span>
                            <span class="text-blue-600">🔄 {{ $stats['prescriptions']['en_cours'] ?? 0 }} en cours</span>
                            <span class="text-green-600">✅ {{ $stats['prescriptions']['terminees'] ?? 0 }} terminées</span>
                        </div>
                    </div>
                </div>

                {{-- Chiffre d'affaires --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Chiffre d'affaires</p>
                                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                                    {{ number_format($stats['financier']['ca_periode'] ?? 0, 2) }}Ar
                                </p>
                            </div>
                            <div class="p-2 sm:p-3 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                                <svg class="w-5 sm:w-6 h-5 sm:h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                        </div>
                        @if(isset($stats['financier']['evolution']))
                            <div class="mt-2 sm:mt-4 flex items-center">
                                <span class="text-{{ $stats['financier']['evolution'] >= 0 ? 'green' : 'red' }}-600 text-sm font-medium">
                                    {{ $stats['financier']['evolution'] >= 0 ? '↗' : '↘' }} {{ abs($stats['financier']['evolution']) }}%
                                </span>
                                <span class="text-gray-600 dark:text-gray-400 text-sm ml-1 sm:ml-2">vs période précédente</span>
                            </div>
                        @endif
                        <div class="mt-2">
                            <div class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                                Commissions : <span class="font-medium text-green-600">{{ number_format($stats['financier']['commissions'] ?? 0, 2) }}Ar</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Prescripteurs actifs --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Prescripteurs</p>
                                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                                    {{ number_format($stats['prescripteurs']['actifs'] ?? 0) }}
                                </p>
                            </div>
                            <div class="p-2 sm:p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                                <svg class="w-5 sm:w-6 h-5 sm:h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-2 sm:mt-4 flex items-center">
                            <span class="text-green-600 text-sm font-medium">Actifs</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Activités récentes --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                {{-- Prescriptions récentes --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-4 sm:mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Prescriptions récentes</h3>
                            <a href="{{ route('secretaire.prescription.index') }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium">Voir toutes</a>
                        </div>
                        
                        @if(count($prescriptionsRecentes) > 0)
                            <div class="space-y-2 sm:space-y-4 max-h-60 sm:max-h-96 overflow-y-auto">
                                @foreach($prescriptionsRecentes as $prescription)
                                    <div class="flex items-center justify-between p-2 sm:p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors cursor-pointer"
                                         onclick="window.location='{{ route('secretaire.patient.detail', $prescription['id']) }}'">
                                        <div class="flex items-center space-x-2 sm:space-x-3">
                                            <div class="w-8 sm:w-10 h-8 sm:h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                                <svg class="w-4 sm:w-5 h-4 sm:h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-white text-sm sm:text-base">{{ $prescription['reference'] }}</p>
                                                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">{{ $prescription['patient'] }} - {{ $prescription['prescripteur'] }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-0.5 rounded-full text-xs font-medium 
                                                @if($prescription['status'] == 'TERMINE') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                @elseif($prescription['status'] == 'EN_COURS') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                @elseif($prescription['status'] == 'EN_ATTENTE') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                                @endif">
                                                @if($prescription['status'] == 'TERMINE') Terminée
                                                @elseif($prescription['status'] == 'EN_COURS') En cours
                                                @elseif($prescription['status'] == 'EN_ATTENTE') En attente
                                                @else {{ $prescription['status'] }}
                                                @endif
                                            </span>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $prescription['created_at']->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-gray-500 dark:text-gray-400 py-4 sm:py-8">
                                <svg class="w-8 sm:w-12 h-8 sm:h-12 mx-auto mb-2 sm:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-sm">Aucune prescription récente</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Paiements récents --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-4 sm:mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Paiements récents</h3>
                            <a href="{{ route('secretaire.prescription.index') }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium">Voir tous</a>
                        </div>
                        
                        @if(count($paiementsRecents) > 0)
                            <div class="space-y-2 sm:space-y-4 max-h-60 sm:max-h-96 overflow-y-auto">
                                @foreach($paiementsRecents as $index => $paiement)
                                    <div class="flex items-center justify-between p-2 sm:p-4 bg-gradient-to-r from-{{ ['green', 'blue', 'yellow', 'purple', 'red'][$index % 5] }}-50 to-{{ ['green', 'blue', 'yellow', 'purple', 'red'][$index % 5] }}-100 dark:from-{{ ['green', 'blue', 'yellow', 'purple', 'red'][$index % 5] }}-900 dark:to-{{ ['green', 'blue', 'yellow', 'purple', 'red'][$index % 5] }}-800 rounded-lg border-l-4 border-{{ ['green', 'blue', 'yellow', 'purple', 'red'][$index % 5] }}-500">
                                        <div class="flex items-center space-x-2 sm:space-x-3">
                                            <div class="w-8 sm:w-10 h-8 sm:h-10 bg-{{ ['green', 'blue', 'yellow', 'purple', 'red'][$index % 5] }}-100 dark:bg-{{ ['green', 'blue', 'yellow', 'purple', 'red'][$index % 5] }}-900 rounded-full flex items-center justify-center">
                                                <svg class="w-4 sm:w-5 h-4 sm:h-5 text-{{ ['green', 'blue', 'yellow', 'purple', 'red'][$index % 5] }}-600 dark:text-{{ ['green', 'blue', 'yellow', 'purple', 'red'][$index % 5] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-white text-sm sm:text-base">{{ number_format($paiement['montant'], 2) }}Ar</p>
                                                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">{{ $paiement['patient'] }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">{{ $paiement['prescription_reference'] }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $paiement['created_at']->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-gray-500 dark:text-gray-400 py-4 sm:py-8">
                                <svg class="w-8 sm:w-12 h-8 sm:h-12 mx-auto mb-2 sm:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                <p class="text-sm">Aucun paiement récent</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Actions rapides --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 sm:mb-6">Actions rapides</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 sm:gap-4">
                        <a href="{{ route('secretaire.patients') }}" class="flex flex-col items-center p-3 sm:p-4 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded-lg hover:from-blue-100 hover:to-blue-200 dark:hover:from-blue-800 dark:hover:to-blue-700 transition-all duration-200 group">
                            <div class="w-10 sm:w-12 h-10 sm:h-12 bg-blue-500 rounded-full flex items-center justify-center mb-2 sm:mb-3 group-hover:scale-110 transition-transform">
                                <svg class="w-5 sm:w-6 h-5 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                            </div>
                            <span class="text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 text-center">Nouveau Patient</span>
                        </a>

                        <a href="{{ route('secretaire.prescription.create') }}" class="flex flex-col items-center p-3 sm:p-4 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 rounded-lg hover:from-green-100 hover:to-green-200 dark:hover:from-green-800 dark:hover:to-green-700 transition-all duration-200 group">
                            <div class="w-10 sm:w-12 h-10 sm:h-12 bg-green-500 rounded-full flex items-center justify-center mb-2 sm:mb-3 group-hover:scale-110 transition-transform">
                                <svg class="w-5 sm:w-6 h-5 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <span class="text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 text-center">Nouvelle Prescription</span>
                        </a>

                        <a href="{{ route('secretaire.prescripteurs') }}" class="flex flex-col items-center p-3 sm:p-4 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900 dark:to-purple-800 rounded-lg hover:from-purple-100 hover:to-purple-200 dark:hover:from-purple-800 dark:hover:to-purple-700 transition-all duration-200 group">
                            <div class="w-10 sm:w-12 h-10 sm:h-12 bg-purple-500 rounded-full flex items-center justify-center mb-2 sm:mb-3 group-hover:scale-110 transition-transform">
                                <svg class="w-5 sm:w-6 h-5 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <span class="text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 text-center">Prescripteurs</span>
                        </a>

                        <a href="{{ route('laboratoire.analyses.listes') }}" class="flex flex-col items-center p-3 sm:p-4 bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900 dark:to-yellow-800 rounded-lg hover:from-yellow-100 hover:to-yellow-200 dark:hover:from-yellow-800 dark:hover:to-yellow-700 transition-all duration-200 group">
                            <div class="w-10 sm:w-12 h-10 sm:h-12 bg-yellow-500 rounded-full flex items-center justify-center mb-2 sm:mb-3 group-hover:scale-110 transition-transform">
                                <svg class="w-5 sm:w-6 h-5 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <span class="text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 text-center">Analyses</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Tableau de bord des tâches --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 sm:mb-6">Tâches à traiter</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6">
                        {{-- Prescriptions en attente --}}
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3 sm:p-4">
                            <div class="flex items-center justify-between mb-2 sm:mb-3">
                                <h4 class="font-medium text-yellow-800 dark:text-yellow-200 text-sm sm:text-base">En attente</h4>
                                <span class="bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-200 text-xs px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full">{{ $stats['prescriptions']['en_attente'] ?? 0 }}</span>
                            </div>
                            <p class="text-sm text-yellow-700 dark:text-yellow-300">Prescriptions à traiter</p>
                            <a href="{{ route('secretaire.prescription.index', ['status' => 'en_attente']) }}" class="inline-flex items-center mt-2 sm:mt-3 text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-200 text-sm font-medium">
                                Voir toutes
                                <svg class="w-3 sm:w-4 h-3 sm:h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>

                        {{-- Prescriptions en cours --}}
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 sm:p-4">
                            <div class="flex items-center justify-between mb-2 sm:mb-3">
                                <h4 class="font-medium text-blue-800 dark:text-blue-200 text-sm sm:text-base">En cours</h4>
                                <span class="bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200 text-xs px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full">{{ $stats['prescriptions']['en_cours'] ?? 0 }}</span>
                            </div>
                            <p class="text-sm text-blue-700 dark:text-blue-300">Analyses en cours</p>                          
                            <a href="{{ route('secretaire.prescription.index', ['status' => 'en_cours']) }}" class="inline-flex items-center mt-2 sm:mt-3 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 text-sm font-medium">                            
                                Voir toutes
                                <svg class="w-3 sm:w-4 h-3 sm:h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>

                        {{-- Résultats prêts --}}
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3 sm:p-4">
                            <div class="flex items-center justify-between mb-2 sm:mb-3">
                                <h4 class="font-medium text-green-800 dark:text-green-200 text-sm sm:text-base">Terminées</h4>
                                <span class="bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200 text-xs px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full">{{ $stats['prescriptions']['terminees'] ?? 0 }}</span>
                            </div>
                            <p class="text-sm text-green-700 dark:text-green-300">Résultats disponibles</p>
                            <a href="{{ route('secretaire.prescription.index', ['status' => 'termine']) }}" class="inline-flex items-center mt-2 sm:mt-3 text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-200 text-sm font-medium">
                                Voir toutes
                                <svg class="w-3 sm:w-4 h-3 sm:h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notes et rappels --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4 sm:mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notes et rappels</h3>
                        <button class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                            Ajouter une note
                        </button>
                    </div>
                    
                    <div class="space-y-2 sm:space-y-4">
                        <div class="flex items-start space-x-2 sm:space-x-3 p-2 sm:p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex-shrink-0 w-2 h-2 bg-red-500 rounded-full mt-1 sm:mt-2"></div>
                            <div class="flex-grow">
                                <p class="text-sm text-gray-900 dark:text-white font-medium">Rappel: Vérifier les résultats de Mme Dubois</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ajouté il y a 2 heures</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-2 sm:space-x-3 p-2 sm:p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-1 sm:mt-2"></div>
                            <div class="flex-grow">
                                <p class="text-sm text-gray-900 dark:text-white font-medium">Nouveau protocole analyses sanguines en vigueur</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ajouté hier</p>
                            </div>
                        </div>
                        
                        <div class="text-center py-2 sm:py-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Vous êtes à jour avec vos tâches</p>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>