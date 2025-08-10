<div>
    <div class="px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header avec animations --}}
        <div class="mb-8 animate-fade-in">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400 bg-clip-text text-transparent">
                        Traitement des analyses
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-slate-300 text-sm">Saisie et validation des résultats d'analyses</p>
                </div>
                
                {{-- Mode sombre toggle (optionnel) --}}
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                        <div class="absolute inset-0 w-3 h-3 bg-green-400 rounded-full animate-ping"></div>
                    </div>
                    <span class="text-sm text-slate-600 dark:text-slate-300 font-medium">Système en ligne</span>
                </div>
            </div>
        </div>

        {{-- Stats Cards améliorées --}}
        @include('livewire.technicien.partials.statistique-technicien')

        {{-- Filtres améliorés --}}
        @include('livewire.technicien.partials.filtres-technicien')

        {{-- Table améliorée --}}
        <div class="bg-white dark:bg-slate-800 shadow-2xl rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-700">
                        <tr>
                            <th wire:click="sortBy('reference')" 
                                class="group px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors duration-200">
                                <div class="flex items-center space-x-2">
                                    <span>Référence</span>
                                    @if($sortField === 'reference')
                                        <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="{{ $sortDirection === 'asc' ? 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-slate-300 dark:text-slate-600 group-hover:text-slate-500 dark:group-hover:text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Patient
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Prescripteur
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Analyses
                            </th>
                            <th wire:click="sortBy('created_at')" 
                                class="group px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors duration-200">
                                <div class="flex items-center space-x-2">
                                    <span>Date</span>
                                    @if($sortField === 'created_at')
                                        <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="{{ $sortDirection === 'asc' ? 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-slate-300 dark:text-slate-600 group-hover:text-slate-500 dark:group-hover:text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Statut
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($prescriptions as $prescription)
                            <tr class="group hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-all duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-blue-500 rounded-full mr-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                                        <div class="text-sm font-bold text-slate-900 dark:text-slate-100">
                                            {{ $prescription->reference }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3">
                                            {{ substr($prescription->patient->prenom, 0, 1) }}{{ substr($prescription->patient->nom, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                                                {{ $prescription->patient->nom }} {{ $prescription->patient->prenom }}
                                            </div>
                                            @if($prescription->patient->age && $prescription->unite_age)
                                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                                    📅 {{ $prescription->age }} {{ $prescription->unite_age }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-slate-900 dark:text-slate-100">
                                        👨‍⚕️ {{ $prescription->prescripteur->nom_complet }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                                            🧪 {{ $prescription->analyses->count() }} analyse(s)
                                        </div>
                                    </div>
                                    @php
                                        $resultatsCount = $prescription->resultats()->count();
                                        $totalAnalyses = $prescription->analyses->count();
                                        $progression = $totalAnalyses > 0 ? round(($resultatsCount / $totalAnalyses) * 100) : 0;
                                    @endphp
                                    <div class="mt-1">
                                        <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 mb-1">
                                            <span>{{ $resultatsCount }}/{{ $totalAnalyses }}</span>
                                            <span>{{ $progression }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-200 dark:bg-slate-600 rounded-full h-2 overflow-hidden">
                                            <div class="bg-gradient-to-r from-blue-500 to-green-500 h-2 rounded-full transition-all duration-500 ease-out" 
                                                 style="width: {{ $progression }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-slate-900 dark:text-slate-100">
                                        📅 {{ $prescription->created_at->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        🕐 {{ $prescription->created_at->format('H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusConfig = [
                                            'EN_ATTENTE' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-800 dark:text-amber-300', 'icon' => '🟡'],
                                            'EN_COURS' => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-800 dark:text-blue-300', 'icon' => '🔵'],
                                            'TERMINE' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-800 dark:text-emerald-300', 'icon' => '🟢'],
                                        ];
                                        $config = $statusConfig[$prescription->status] ?? ['bg' => 'bg-slate-100 dark:bg-slate-700', 'text' => 'text-slate-800 dark:text-slate-300', 'icon' => '⚪'];
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $config['bg'] }} {{ $config['text'] }} border border-current/20">
                                        <span class="mr-1">{{ $config['icon'] }}</span>
                                        {{ $prescription->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button wire:click="startAnalysis({{ $prescription->id }})" 
                                            wire:loading.attr="disabled"
                                            wire:target="startAnalysis({{ $prescription->id }})"
                                            class="group inline-flex items-center px-4 py-2 border border-transparent text-sm font-bold rounded-xl text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-slate-800 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed">
                                        
                                        {{-- État normal --}}
                                        <div wire:loading.remove wire:target="startAnalysis({{ $prescription->id }})">
                                            <em class="ni ni-account-setting-alt w-4 h-4 mr-1 group-hover:rotate-12 transition-transform duration-300"></em>
                                            Traiter
                                        </div>

                                        {{-- État chargement --}}
                                        <div wire:loading wire:target="startAnalysis({{ $prescription->id }})" class="flex items-center">
                                            <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" 
                                                    d="M4 12a8 8 0 018-8V0C5.373 
                                                        0 0 5.373 0 12h4zm2 5.291A7.962 
                                                        7.962 0 014 12H0c0 3.042 1.135 
                                                        5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Chargement...
                                        </div>
                                    </button>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="text-slate-500 dark:text-slate-400">
                                        <div class="w-24 h-24 mx-auto mb-6 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center">
                                            <svg class="w-12 h-12 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100 mb-2">Aucune prescription à traiter</h3>
                                        <p class="text-slate-600 dark:text-slate-400 max-w-md mx-auto">Il n'y a actuellement aucune prescription correspondant aux critères sélectionnés. Vérifiez vos filtres ou revenez plus tard.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination améliorée --}}
            @if($prescriptions->hasPages())
                <div class="bg-slate-50 dark:bg-slate-700/50 px-6 py-4 border-t border-slate-200 dark:border-slate-600">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-slate-700 dark:text-slate-300">
                            Affichage de {{ $prescriptions->firstItem() }} à {{ $prescriptions->lastItem() }} 
                            sur {{ $prescriptions->total() }} résultats
                        </div>
                        <div class="pagination-links">
                            {{ $prescriptions->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Notifications Toast --}}
    @if (session()->has('message'))
        <div class="fixed top-4 right-4 bg-emerald-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-in-right">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                {{ session('message') }}
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-in-right">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif
</div>
