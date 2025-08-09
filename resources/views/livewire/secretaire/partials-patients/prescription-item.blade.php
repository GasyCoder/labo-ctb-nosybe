{{-- <!-- resources/views/livewire/secretaire/partials-patients/prescription-item-modern.blade.php -->
<div class="group relative mx-6 my-4 p-6 bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl border border-gray-100/50 dark:border-gray-700/50 shadow-lg shadow-gray-200/50 dark:shadow-gray-900/50 hover:shadow-xl hover:shadow-gray-200/60 dark:hover:shadow-gray-900/60 transition-all duration-300 hover:scale-[1.01] hover:-translate-y-1
    {{ $isRecent ? 'ring-1 ring-blue-200/50 dark:ring-blue-800/50 bg-gradient-to-br from-blue-50/30 via-white/60 to-indigo-50/30 dark:from-blue-900/10 dark:via-gray-800/60 dark:to-indigo-900/10' : '' }}">

    <!-- Effet de brillance au hover -->
    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-2xl"></div>
    
    <!-- Badge "Récente" flottant -->
    @if($isRecent)
        <div class="absolute -top-2 -right-2 z-10">
            <div class="relative">
                <div class="px-3 py-1 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs font-bold rounded-full shadow-lg shadow-blue-500/30 animate-pulse">
                    <span class="flex items-center space-x-1">
                        <span class="w-1.5 h-1.5 bg-white rounded-full"></span>
                        <span>Récente</span>
                    </span>
                </div>
                <!-- Effet de halo -->
                <div class="absolute inset-0 bg-blue-400 rounded-full blur-md opacity-30 -z-10"></div>
            </div>
        </div>
    @endif

    <!-- Header de la prescription -->
    <div class="relative flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-6">
        <div class="flex items-center space-x-4">
            <!-- Icône avec gradient moderne -->
            <div class="relative">
                <div class="p-3 {{ $isRecent ? 'bg-gradient-to-br from-blue-400 to-indigo-600' : 'bg-gradient-to-br from-gray-400 to-slate-600' }} rounded-xl shadow-lg {{ $isRecent ? 'shadow-blue-500/25' : 'shadow-gray-500/25' }}">
                    @if($isRecent)
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    @else
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    @endif
                </div>
                <!-- Halo pour les récentes -->
                @if($isRecent)
                    <div class="absolute inset-0 bg-blue-400 rounded-xl blur-lg opacity-20 -z-10 animate-pulse"></div>
                @endif
            </div>
            
            <div class="min-w-0 flex-1">
                <!-- Référence avec highlight -->
                <div class="text-lg font-bold text-gray-900 dark:text-white mb-1">
                    @if($searchPrescriptions && stripos($prescription->reference, $searchPrescriptions) !== false)
                        {!! str_ireplace($searchPrescriptions, '<mark class="bg-gradient-to-r from-yellow-200 to-amber-200 dark:from-yellow-800/50 dark:to-amber-800/50 text-gray-900 dark:text-white px-1 rounded">' . $searchPrescriptions . '</mark>', $prescription->reference) !!}
                    @else
                        {{ $prescription->reference }}
                    @endif
                </div>
                
                <!-- Informations secondaires -->
                <div class="flex flex-wrap items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span class="font-medium">{{ $patient->numero_dossier }}</span>
                    </div>
                    
                    <span class="text-gray-300 dark:text-gray-600">•</span>
                    
                    <div class="flex items-center space-x-2 {{ $isRecent ? 'text-blue-600 dark:text-blue-400 font-semibold' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ $prescription->created_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    
                    @if($isRecent)
                        <span class="inline-flex items-center px-2 py-1 bg-blue-100/80 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full text-xs font-semibold">
                            {{ $prescription->created_at->diffForHumans() }}
                        </span>
                    @endif
                    
                    @if($prescription->prescripteur)
                        <span class="text-gray-300 dark:text-gray-600">•</span>
                        <div class="flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="font-medium">Dr {{ $prescription->prescripteur->nom }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Badges de statut et informations -->
        <div class="flex flex-wrap items-center gap-2">
            <!-- Nombre d'analyses -->
            <span class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-blue-100 to-indigo-100 dark:from-blue-900/30 dark:to-indigo-900/30 text-blue-800 dark:text-blue-300 rounded-lg text-sm font-semibold shadow-sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                {{ $prescription->analyses->count() }} analyse{{ $prescription->analyses->count() > 1 ? 's' : '' }}
            </span>
            
            <!-- Statut -->
            @if($prescription->status)
                <span class="inline-flex items-center px-3 py-1.5 text-sm font-semibold rounded-lg shadow-sm
                    @if($prescription->status === 'EN_ATTENTE') bg-gradient-to-r from-amber-100 to-yellow-100 dark:from-amber-900/30 dark:to-yellow-900/30 text-amber-800 dark:text-amber-300
                    @elseif($prescription->status === 'EN_COURS') bg-gradient-to-r from-blue-100 to-cyan-100 dark:from-blue-900/30 dark:to-cyan-900/30 text-blue-800 dark:text-blue-300
                    @elseif($prescription->status === 'TERMINE') bg-gradient-to-r from-emerald-100 to-green-100 dark:from-emerald-900/30 dark:to-green-900/30 text-emerald-800 dark:text-emerald-300
                    @elseif($prescription->status === 'VALIDE') bg-gradient-to-r from-purple-100 to-violet-100 dark:from-purple-900/30 dark:to-violet-900/30 text-purple-800 dark:text-purple-300
                    @else bg-gradient-to-r from-gray-100 to-slate-100 dark:from-gray-800 dark:to-slate-800 text-gray-800 dark:text-gray-300 @endif">
                    
                    @if($prescription->status === 'EN_ATTENTE')
                        <span class="w-2 h-2 bg-amber-400 rounded-full mr-2 animate-pulse"></span>
                    @elseif($prescription->status === 'EN_COURS')
                        <span class="w-2 h-2 bg-blue-400 rounded-full mr-2"></span>
                    @elseif($prescription->status === 'TERMINE')
                        <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2"></span>
                    @elseif($prescription->status === 'VALIDE')
                        <span class="w-2 h-2 bg-purple-400 rounded-full mr-2"></span>
                    @else
                        <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                    @endif
                    
                    {{ $prescription->status_label }}
                </span>
            @endif
            
            <!-- Montant -->
            @if($prescription->montant_total > 0)
                <span class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-emerald-100 to-green-100 dark:from-emerald-900/30 dark:to-green-900/30 text-emerald-800 dark:text-emerald-300 rounded-lg text-sm font-semibold shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                    {{ number_format($prescription->montant_total, 0, ',', ' ') }} Ar
                </span>
            @endif
        </div>
    </div>
    
    <!-- Analyses avec design moderne -->
    @if($prescription->analyses->count() > 0)
        <div class="space-y-3 mb-6">
            @foreach($prescription->analyses->take(3) as $analyse)
                <div class="group/analyse relative p-4 bg-white/40 dark:bg-gray-800/40 backdrop-blur-sm rounded-xl border border-gray-100/50 dark:border-gray-700/50 hover:bg-white/60 dark:hover:bg-gray-800/60 transition-all duration-200 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-gray-900 dark:text-white mb-1">
                                @if($searchPrescriptions && stripos($analyse->designation, $searchPrescriptions) !== false)
                                    {!! str_ireplace($searchPrescriptions, '<mark class="bg-gradient-to-r from-yellow-200 to-amber-200 dark:from-yellow-800/50 dark:to-amber-800/50 text-gray-900 dark:text-white px-1 rounded">' . $searchPrescriptions . '</mark>', $analyse->designation) !!}
                                @else
                                    {{ $analyse->designation }}
                                @endif
                            </div>
                            @if($analyse->code)
                                <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                                    @if($searchPrescriptions && stripos($analyse->code, $searchPrescriptions) !== false)
                                        {!! str_ireplace($searchPrescriptions, '<mark class="bg-gradient-to-r from-yellow-200 to-amber-200 dark:from-yellow-800/50 dark:to-amber-800/50 text-gray-900 dark:text-white px-1 rounded">' . $searchPrescriptions . '</mark>', $analyse->code) !!}
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 bg-gray-100/80 dark:bg-gray-700/80 rounded text-gray-600 dark:text-gray-400">
                                            {{ $analyse->code }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                        
                        @if($analyse->prix)
                            <div class="text-right">
                                <div class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ number_format($analyse->prix, 0, ',', ' ') }} <span class="text-sm text-gray-500">Ar</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
            
            <!-- Bouton pour voir plus d'analyses -->
            @if($prescription->analyses->count() > 3)
                <div class="text-center pt-2">
                    <button 
                        wire:click="togglePrescriptionDetails({{ $prescription->id }})"
                        class="group/expand inline-flex items-center space-x-2 px-4 py-2 bg-white/60 dark:bg-gray-700/60 backdrop-blur-sm text-blue-600 dark:text-blue-400 rounded-lg border border-blue-200/50 dark:border-blue-800/50 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-blue-300/50 dark:hover:border-blue-700/50 transition-all duration-200 text-sm font-semibold shadow-sm hover:shadow-md">
                        
                        @if(in_array($prescription->id, $prescriptionsEtendues ?? []))
                            <svg class="w-4 h-4 transform group-hover/expand:-translate-y-0.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                            </svg>
                            <span>Masquer {{ $prescription->analyses->count() - 3 }} analyse{{ $prescription->analyses->count() - 3 > 1 ? 's' : '' }}</span>
                        @else
                            <svg class="w-4 h-4 transform group-hover/expand:translate-y-0.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                            <span>Voir {{ $prescription->analyses->count() - 3 }} analyse{{ $prescription->analyses->count() - 3 > 1 ? 's' : '' }} de plus</span>
                        @endif
                    </button>
                </div>
                
                <!-- Analyses supplémentaires -->
                @if(in_array($prescription->id, $prescriptionsEtendues ?? []))
                    <div class="mt-4 space-y-3 border-t border-gray-200/50 dark:border-gray-700/50 pt-4">
                        @foreach($prescription->analyses->skip(3) as $analyse)
                            <div class="p-4 bg-white/40 dark:bg-gray-800/40 backdrop-blur-sm rounded-xl border border-gray-100/50 dark:border-gray-700/50">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 min-w-0">
                                        <div class="font-semibold text-gray-900 dark:text-white mb-1">
                                            {{ $analyse->designation }}
                                        </div>
                                        @if($analyse->code)
                                            <div class="text-xs">
                                                <span class="inline-flex items-center px-2 py-0.5 bg-gray-100/80 dark:bg-gray-700/80 rounded text-gray-600 dark:text-gray-400">
                                                    {{ $analyse->code }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    @if($analyse->prix)
                                        <div class="text-lg font-bold text-gray-900 dark:text-white">
                                            {{ number_format($analyse->prix, 0, ',', ' ') }} <span class="text-sm text-gray-500">Ar</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    @endif
    
    <!-- Actions modernes -->
    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200/50 dark:border-gray-700/50">
        <!-- Bouton Modifier -->
        <a href="{{ route('secretaire.prescription.edit', $prescription->id) }}" 
           class="group/btn inline-flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg shadow-lg shadow-blue-500/25 hover:shadow-xl hover:shadow-blue-500/40 transition-all duration-200 transform hover:scale-105 text-sm font-semibold">
            <svg class="w-4 h-4 group-hover/btn:rotate-12 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <span>Modifier</span>
        </a>
        
        <!-- Bouton Facture -->
        @if($prescription->paiements->count() > 0)
            <button 
                wire:click="generateInvoice({{ $prescription->paiements->first()->id }})"
                class="group/btn inline-flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-lg shadow-lg shadow-emerald-500/25 hover:shadow-xl hover:shadow-emerald-500/40 transition-all duration-200 transform hover:scale-105 text-sm font-semibold">
                <svg class="w-4 h-4 group-hover/btn:-translate-y-0.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Facture</span>
            </button>
        @endif
    </div>
</div> --}}