    <div class="bg-white dark:bg-slate-900 shadow-lg border-b border-slate-200 dark:border-slate-800">
        <div class="px-6 py-6">
            {{-- Top Section: Title & Actions --}}
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <div class="flex items-center gap-4">
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 dark:from-primary-600 dark:to-primary-700 rounded-xl shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl lg:text-xl font-bold text-slate-900 dark:text-slate-100">
                            {{ $prescription->reference }}
                        </h1>
                        <p class="text-slate-600 dark:text-slate-400 text-sm">Détail de la prescription médicale</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    {{-- Retour Button --}}
                    <a href="{{ route('technicien.index')}}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-slate-800 transition-all duration-200 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Retour
                    </a>
                    {{-- Status Badge --}}
                    <div class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold border
                        @if($prescription->status === 'EN_ATTENTE') 
                            bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-300 border-yellow-200 dark:border-yellow-700
                        @elseif($prescription->status === 'EN_COURS') 
                            bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 border-primary-200 dark:border-primary-700
                        @elseif($prescription->status === 'TERMINE') 
                            bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 border-green-200 dark:border-green-700
                        @else 
                            bg-slate-50 dark:bg-slate-900/20 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700
                        @endif">
                        <div class="w-2.5 h-2.5 rounded-full 
                            @if($prescription->status === 'EN_ATTENTE') bg-yellow-500
                            @elseif($prescription->status === 'EN_COURS') bg-primary-500
                            @elseif($prescription->status === 'TERMINE') bg-green-500
                            @else bg-slate-500
                            @endif">
                        </div>
                        {{ $prescription->status_label }}
                    </div>
                </div>
            </div>
            
            {{-- Info Cards Section --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Patient --}}
                <div class="group flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-primary-300 dark:hover:border-primary-600 transition-all duration-200">
                    <div class="flex-shrink-0 w-10 h-10 bg-primary-500 dark:bg-primary-600 rounded-lg flex items-center justify-center group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-primary-600 dark:text-primary-400 uppercase tracking-wider mb-1">Patient</p>
                        <p class="font-semibold text-slate-900 dark:text-slate-100 truncate">
                            {{ $prescription->patient->civilite. ' ' . $prescription->patient->nom }} {{ $prescription->patient->prenom }}</p>
                    </div>
                </div>
                
                {{-- Prescripteur --}}
                <div class="group flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-green-300 dark:hover:border-green-600 transition-all duration-200">
                    <div class="flex-shrink-0 w-10 h-10 bg-green-500 dark:bg-green-600 rounded-lg flex items-center justify-center group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wider mb-1">Prescripteur</p>
                        <p class="font-semibold text-slate-900 dark:text-slate-100 truncate">
                            {{$prescription->prescripteur->nom_complet }}</p>
                    </div>
                </div>
                
                {{-- Date --}}
                <div class="group flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-cyan-300 dark:hover:border-cyan-600 transition-all duration-200">
                    <div class="flex-shrink-0 w-10 h-10 bg-cyan-500 dark:bg-cyan-600 rounded-lg flex items-center justify-center group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-cyan-600 dark:text-cyan-400 uppercase tracking-wider mb-1">Date</p>
                        <p class="font-semibold text-slate-900 dark:text-slate-100">{{ $prescription->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>