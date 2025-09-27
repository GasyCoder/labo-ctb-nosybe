@props([
    'analyse',
    'patient' => null // Nouveau prop pour le patient
])

@php
  $typeLabel = $analyse->type?->name ?? '—';
  $enfants = $analyse->enfantsRecursive ?? collect();
  
  // Utiliser les méthodes du modèle pour obtenir la valeur de référence selon le patient
  $valeurRef = $analyse->getValeurReferenceByPatient($patient);
  $labelValeurRef = $analyse->getLabelValeurReferenceByPatient($patient);
@endphp

<div class="relative">
    {{-- Ligne de connexion hiérarchique --}}
    <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gradient-to-b from-slate-300 to-slate-200 dark:from-slate-600 dark:to-slate-700"></div>
    
    {{-- Contenu principal --}}
    <div class="pl-6 relative">
        {{-- Indicateur de nœud --}}
        <div class="absolute -left-1 top-6 w-2 h-2 bg-primary-500 dark:bg-primary-400 rounded-full border-2 border-white dark:border-slate-900 shadow-sm"></div>
        
        <div class="group bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700 p-4 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-200">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    {{-- Titre et description --}}
                    <div class="mb-3">
                        <div class="flex items-start gap-2">
                            {{-- Icône selon le type --}}
                            <div class="flex-shrink-0 mt-0.5">
                                @if($analyse->level === 'PARENT')
                                    <div class="w-6 h-6 bg-primary-500 dark:bg-primary-600 rounded-md flex items-center justify-center">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-6 h-6 bg-cyan-500 dark:bg-cyan-600 rounded-md flex items-center justify-center">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <h4 class="text-base font-semibold text-slate-900 dark:text-slate-100 {{ $analyse->is_bold ? 'font-bold' : '' }} group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                    {{ $analyse->designation }}
                                </h4>
                                @if($analyse->description)
                                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">{{ $analyse->description }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    {{-- Options prédéfinies --}}
                    @if($analyse->formatted_results && is_array($analyse->formatted_results) && count($analyse->formatted_results))
                        <div class="mb-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2"></path>
                                </svg>
                                <span class="text-xs font-medium text-primary-600 dark:text-primary-400 uppercase tracking-wider">Options disponibles</span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach(array_slice($analyse->formatted_results, 0, 4) as $option)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 border border-primary-200 dark:border-primary-800">
                                        {{ $option }}
                                    </span>
                                @endforeach
                                @if(count($analyse->formatted_results) > 4)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-600">
                                        +{{ count($analyse->formatted_results) - 4 }} autres
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    {{-- Valeurs de référence selon le patient --}}
                    @if($valeurRef)
                        <div class="mb-3 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wider">{{ $labelValeurRef }}</span>
                            </div>
                            <div class="mt-1 flex items-center gap-1">
                                <span class="text-sm font-semibold text-green-700 dark:text-green-300">
                                    {{ $valeurRef }}
                                </span>
                                @if($analyse->unite)
                                    <span class="text-sm text-green-600 dark:text-green-400">{{ $analyse->unite }}</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
                
                {{-- Badges et métadonnées --}}
                <div class="flex flex-col items-end gap-2 ml-4 flex-shrink-0">
                    @if($analyse->level === 'PARENT')
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-semibold bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 border border-primary-200 dark:border-primary-800">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                            </svg>
                            PANEL
                        </span>
                    @elseif($analyse->level === 'CHILD')
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-semibold bg-cyan-50 dark:bg-cyan-900/20 text-cyan-700 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-800">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            SOUS-ANALYSE
                        </span>
                    @endif
                    
                    @if($typeLabel !== '—')
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                            {{ $typeLabel }}
                        </span>
                    @endif
                    
                    @if($analyse->unite && !$valeurRef)
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-slate-50 dark:bg-slate-800/50 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                            {{ $analyse->unite }}
                        </span>
                    @endif
                    
                    {{-- Indicateur d'enfants --}}
                    @if($enfants->count())
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-300 border border-yellow-200 dark:border-yellow-800">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $enfants->count() }} sous-analyses
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Enfants récursifs --}}
        @if($enfants->count())
            <div class="mt-4 space-y-3 pb-2">
                @foreach($enfants as $child)
                    <x-analyse-tree :analyse="$child" :patient="$patient" />
                @endforeach
            </div>
        @endif
    </div>
</div>