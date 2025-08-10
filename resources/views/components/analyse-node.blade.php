@props([
    'node',
    'results' => [],
    'familles' => [],
    'bacteriesByFamille' => [],
])

@php
    $type = strtoupper($node->type->name ?? '');
    $path = "results.{$node->id}";
    $get = fn($k,$d=null)=> data_get($results, "{$node->id}.{$k}", $d);
@endphp

<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg shadow-sm p-6 mb-4 hover:shadow-md transition-shadow duration-200">
    {{-- Header avec badges optimisé --}}
    <div class="flex items-start justify-between mb-4">
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 {{ $node->is_bold ? 'font-bold' : '' }}">
                    {{ $node->designation }}
                </h3>
                <div class="flex gap-2 flex-wrap">
                    @if($node->level === 'PARENT')
                        <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 border border-primary-200 dark:border-primary-800 rounded-lg">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                            </svg>
                            PANEL
                        </span>
                    @elseif($node->level === 'CHILD')
                        <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium bg-cyan-50 dark:bg-cyan-900/20 text-cyan-700 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-800 rounded-lg">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            SOUS-ANALYSE
                        </span>
                    @endif
                    @if($node->is_bold)
                        <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800 rounded-lg">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            Important
                        </span>
                    @endif
                    @if($node->type?->name)
                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-lg">
                            {{ $node->type->name }}
                        </span>
                    @endif
                </div>
            </div>
            @if($node->description)
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{ $node->description }}</p>
            @endif
        </div>

        {{-- Valeurs de référence optimisées --}}
        @if($node->valeur_ref || $node->unite || $node->suffixe)
            <div class="flex-shrink-0 ml-4 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                <div class="text-xs space-y-1">
                    @if($node->valeur_ref)
                        <div class="flex items-center gap-2">
                            <span class="text-slate-500 dark:text-slate-400">Référence:</span>
                            <span class="font-medium text-green-600 dark:text-green-400">{{ $node->valeur_ref }}</span>
                        </div>
                    @endif
                    @if($node->unite)
                        <div class="flex items-center gap-2">
                            <span class="text-slate-500 dark:text-slate-400">Unité:</span>
                            <span class="font-medium text-slate-700 dark:text-slate-300">{{ $node->unite }}</span>
                        </div>
                    @endif
                    @if($node->suffixe)
                        <div class="flex items-center gap-2">
                            <span class="text-slate-500 dark:text-slate-400">Suffixe:</span>
                            <span class="font-medium text-slate-700 dark:text-slate-300">{{ $node->suffixe }}</span>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Contenu récursif ou formulaires --}}
    @if($node->enfantsRecursive?->count())
        <div class="space-y-4 pl-4 border-l-2 border-slate-200 dark:border-slate-700">
            @foreach($node->enfantsRecursive as $child)
                <x-analyse-node
                    :node="$child"
                    :results="$results"
                    :familles="$familles"
                    :bacteries-by-famille="$bacteriesByFamille"
                    wire:key="node-{{ $child->id }}"
                />
            @endforeach
        </div>
    @else
        {{-- Formulaires par type optimisés --}}
        <div class="space-y-4">
            @switch($type)
                @case('LABEL')
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-lg">
                        <p class="text-slate-700 dark:text-slate-200 text-sm leading-relaxed">{{ $node->designation }}</p>
                    </div>
                    @break

                @case('INPUT')
                @case('DOSAGE')
                @case('COMPTAGE')
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="block text-xs text-slate-700 dark:text-slate-300">
                                Valeur {{ $node->unite ? "({$node->unite})" : '' }}
                            </label>
                            <input type="text" wire:model.blur="{{ $path }}.valeur"
                                   class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-colors"
                                   placeholder="Entrez une valeur">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Interprétation</label>
                            <select wire:model="{{ $path }}.interpretation"
                                    class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-colors">
                                <option value="">Sélectionner</option>
                                <option value="NORMAL">Normal</option>
                                <option value="PATHOLOGIQUE">Pathologique</option>
                            </select>
                        </div>
                    </div>
                    @break

                @case('SELECT')
                @case('TEST')
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Sélection</label>
                            @if($node->formatted_results && is_array($node->formatted_results) && count($node->formatted_results))
                                <select wire:model.live="{{ $path }}.resultats"
                                        class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-colors">
                                    <option value="">Veuillez choisir</option>
                                    @foreach($node->formatted_results as $opt)
                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                    @endforeach
                                </select>
                                
                                {{-- Champ Autre si sélectionné --}}
                                @if($get('resultats') === 'Autre')
                                    <div class="mt-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                            </svg>
                                            Précisez votre réponse
                                        </label>
                                        <input type="text" wire:model.blur="{{ $path }}.valeur"
                                               class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-colors"
                                               placeholder="Saisissez votre réponse personnalisée..."
                                               autofocus>
                                    </div>
                                @endif
                            @else
                                <input type="text" wire:model.blur="{{ $path }}.valeur"
                                       class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-colors"
                                       placeholder="Entrez une valeur">
                            @endif
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Interprétation</label>
                            <select wire:model="{{ $path }}.interpretation"
                                    class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-colors">
                                <option value="">Sélectionner</option>
                                <option value="NORMAL">Normal</option>
                                <option value="PATHOLOGIQUE">Pathologique</option>
                            </select>
                        </div>
                    </div>
                    @break

                @case('SELECT_MULTIPLE')
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Sélection multiple</label>
                        @if($node->formatted_results && is_array($node->formatted_results) && count($node->formatted_results))
                            <div class="bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg p-4 max-h-48 overflow-y-auto space-y-2">
                                @foreach($node->formatted_results as $opt)
                                    <label class="flex items-center gap-3 text-slate-900 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-600 p-2 rounded-md cursor-pointer transition-colors">
                                        <input type="checkbox" value="{{ $opt }}" wire:model.live="{{ $path }}.resultats"
                                               class="w-4 h-4 text-primary-600 bg-white dark:bg-slate-600 border-slate-300 dark:border-slate-500 rounded focus:ring-primary-500 dark:focus:ring-primary-400">
                                        <span class="text-sm">{{ $opt }}</span>
                                    </label>
                                @endforeach
                            </div>
                            
                            {{-- Champ Autre pour SELECT_MULTIPLE --}}
                            @if(is_array($get('resultats')) && in_array('Autre', $get('resultats')))
                                <div class="mt-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                        Précisez votre réponse
                                    </label>
                                    <textarea rows="2" wire:model.blur="{{ $path }}.valeurAutre"
                                              class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 resize-none focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-colors"
                                              placeholder="Saisissez votre réponse personnalisée..."
                                              autofocus></textarea>
                                </div>
                            @endif
                        @endif
                    </div>
                    @break

                @case('NEGATIF_POSITIF_1')
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Résultat</label>
                            <div class="flex gap-6">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" value="NEGATIF" wire:model="{{ $path }}.valeur"
                                           class="w-4 h-4 text-green-600 bg-white dark:bg-slate-600 border-slate-300 dark:border-slate-500 focus:ring-green-500 dark:focus:ring-green-400">
                                    <span class="text-green-600 dark:text-green-400 font-medium text-sm group-hover:text-green-700 dark:group-hover:text-green-300 transition-colors">Négatif</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" value="POSITIF" wire:model="{{ $path }}.valeur"
                                           class="w-4 h-4 text-red-600 bg-white dark:bg-slate-600 border-slate-300 dark:border-slate-500 focus:ring-red-500 dark:focus:ring-red-400">
                                    <span class="text-red-600 dark:text-red-400 font-medium text-sm group-hover:text-red-700 dark:group-hover:text-red-300 transition-colors">Positif</span>
                                </label>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Interprétation</label>
                            <select wire:model="{{ $path }}.interpretation"
                                    class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-colors">
                                <option value="">Sélectionner</option>
                                <option value="NORMAL">Normal</option>
                                <option value="PATHOLOGIQUE">Pathologique</option>
                            </select>
                        </div>
                    </div>
                    @break

                @case('NEGATIF_POSITIF_2')
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Résultat</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" value="NEGATIF" wire:model="{{ $path }}.valeur"
                                           class="w-4 h-4 text-green-600 bg-white dark:bg-slate-600 border-slate-300 dark:border-slate-500 focus:ring-green-500 dark:focus:ring-green-400">
                                    <span class="text-green-600 dark:text-green-400 font-medium text-sm group-hover:text-green-700 dark:group-hover:text-green-300 transition-colors">Négatif</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" value="POSITIF" wire:model="{{ $path }}.valeur"
                                           class="w-4 h-4 text-red-600 bg-white dark:bg-slate-600 border-slate-300 dark:border-slate-500 focus:ring-red-500 dark:focus:ring-red-400">
                                    <span class="text-red-600 dark:text-red-400 font-medium text-sm group-hover:text-red-700 dark:group-hover:text-red-300 transition-colors">Positif</span>
                                </label>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs text-slate-700 dark:text-slate-300">
                                Valeur {{ $node->unite ? "({$node->unite})" : '' }}
                            </label>
                            <input type="text" wire:model.blur="{{ $path }}.resultats"
                                   class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-colors"
                                   placeholder="Valeur de référence">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Interprétation</label>
                            <select wire:model="{{ $path }}.interpretation"
                                    class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-colors">
                                <option value="">Sélectionner</option>
                                <option value="NORMAL">Normal</option>
                                <option value="PATHOLOGIQUE">Pathologique</option>
                            </select>
                        </div>
                    </div>
                    @break

                @case('NEGATIF_POSITIF_3')
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div class="space-y-3">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Résultat</label>
                                <div class="flex gap-6">
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="radio" value="NEGATIF" wire:model="{{ $path }}.valeur"
                                               class="w-4 h-4 text-green-600 bg-white dark:bg-slate-600 border-slate-300 dark:border-slate-500 focus:ring-green-500 dark:focus:ring-green-400">
                                        <span class="text-green-600 dark:text-green-400 font-medium text-sm group-hover:text-green-700 dark:group-hover:text-green-300 transition-colors">Négatif</span>
                                    </label>
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="radio" value="POSITIF" wire:model="{{ $path }}.valeur"
                                               class="w-4 h-4 text-red-600 bg-white dark:bg-slate-600 border-slate-300 dark:border-slate-500 focus:ring-red-500 dark:focus:ring-red-400">
                                        <span class="text-red-600 dark:text-red-400 font-medium text-sm group-hover:text-red-700 dark:group-hover:text-red-300 transition-colors">Positif</span>
                                    </label>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Interprétation</label>
                                <select wire:model="{{ $path }}.interpretation"
                                        class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-colors">
                                    <option value="">Sélectionner</option>
                                    <option value="NORMAL">Normal</option>
                                    <option value="PATHOLOGIQUE">Pathologique</option>
                                </select>
                            </div>
                        </div>
                        
                        @if($node->formatted_results && is_array($node->formatted_results) && count($node->formatted_results))
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Sélection multiple</label>
                                <div class="bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg p-4 max-h-48 overflow-y-auto space-y-2">
                                    @foreach($node->formatted_results as $opt)
                                        <label class="flex items-center gap-3 text-slate-900 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-600 p-2 rounded-md cursor-pointer transition-colors">
                                            <input type="checkbox" value="{{ $opt }}" wire:model="{{ $path }}.resultats"
                                                   class="w-4 h-4 text-primary-600 bg-white dark:bg-slate-600 border-slate-300 dark:border-slate-500 rounded focus:ring-primary-500 dark:focus:ring-primary-400">
                                            <span class="text-sm">{{ $opt }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    @break

                @case('ABSENCE_PRESENCE_2')
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Résultat</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" value="ABSENCE" wire:model="{{ $path }}.valeur"
                                           class="w-4 h-4 text-green-600 bg-white dark:bg-slate-600 border-slate-300 dark:border-slate-500 focus:ring-green-500 dark:focus:ring-green-400">
                                    <span class="text-green-600 dark:text-green-400 font-medium text-sm group-hover:text-green-700 dark:group-hover:text-green-300 transition-colors">Absence</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" value="PRESENCE" wire:model="{{ $path }}.valeur"
                                           class="w-4 h-4 text-red-600 bg-white dark:bg-slate-600 border-slate-300 dark:border-slate-500 focus:ring-red-500 dark:focus:ring-red-400">
                                    <span class="text-red-600 dark:text-red-400 font-medium text-sm group-hover:text-red-700 dark:group-hover:text-red-300 transition-colors">Présence</span>
                                </label>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Valeur {{ $node->unite ? "({$node->unite})" : '' }}
                            </label>
                            <input type="text" wire:model.blur="{{ $path }}.resultats"
                                   class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-colors"
                                   placeholder="Préciser la valeur">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Interprétation</label>
                            <select wire:model="{{ $path }}.interpretation"
                                    class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-colors">
                                <option value="">Sélectionner</option>
                                <option value="NORMAL">Normal</option>
                                <option value="PATHOLOGIQUE">Pathologique</option>
                            </select>
                        </div>
                    </div>
                    @break

                @case('INPUT_SUFFIXE')
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Valeur {{ $node->unite ? "({$node->unite})" : '' }}
                            </label>
                            <div class="flex">
                                <input type="text" wire:model.blur="{{ $path }}.valeur"
                                       class="flex-1 px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-l-lg text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-colors"
                                       placeholder="Entrez une valeur">
                                @if($node->suffixe)
                                    <span class="inline-flex items-center px-3 py-2.5 bg-slate-100 dark:bg-slate-800 border border-l-0 border-slate-300 dark:border-slate-600 rounded-r-lg text-slate-700 dark:text-slate-300 text-sm font-medium">
                                        {{ $node->suffixe }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Interprétation</label>
                            <select wire:model="{{ $path }}.interpretation"
                                    class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-colors">
                                <option value="">Sélectionner</option>
                                <option value="NORMAL">Normal</option>
                                <option value="PATHOLOGIQUE">Pathologique</option>
                            </select>
                        </div>
                    </div>
                    @break

                @case('FV')
                    <div class="bg-gradient-to-r from-pink-50 to-pink-100 dark:from-pink-900/20 dark:to-pink-800/20 border border-pink-200 dark:border-pink-800 rounded-xl p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-pink-500 dark:bg-pink-600 rounded-lg flex items-center justify-center text-white text-lg font-bold shadow-sm">
                                🔬
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-pink-800 dark:text-pink-200">Flore Vaginale</h4>
                                <p class="text-sm text-pink-700 dark:text-pink-300">Analyse spécialisée de la flore vaginale</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Résultat</label>
                                @if($node->formatted_results && is_array($node->formatted_results) && count($node->formatted_results))
                                    <select wire:model.live="{{ $path }}.resultats"
                                            class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-pink-500 dark:focus:ring-pink-400 focus:border-pink-500 dark:focus:border-pink-400 transition-colors">
                                        <option value="">Veuillez choisir</option>
                                        @foreach($node->formatted_results as $opt)
                                            <option value="{{ $opt }}">{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <textarea rows="3" wire:model.blur="{{ $path }}.resultats"
                                              class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 resize-none focus:ring-2 focus:ring-pink-500 dark:focus:ring-pink-400 focus:border-pink-500 dark:focus:border-pink-400 transition-colors"
                                              placeholder="Décrivez la flore vaginale..."></textarea>
                                @endif
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Interprétation</label>
                                <select wire:model="{{ $path }}.interpretation"
                                        class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-pink-500 dark:focus:ring-pink-400 focus:border-pink-500 dark:focus:border-pink-400 transition-colors">
                                    <option value="">Sélectionner</option>
                                    <option value="NORMAL">Normal</option>
                                    <option value="PATHOLOGIQUE">Pathologique</option>
                                </select>
                            </div>
                        </div>

                        {{-- Champ Autre pour FV --}}
                        @if($get('resultats') === 'Autre')
                            <div class="mt-4 p-3 bg-white dark:bg-slate-800 rounded-lg border border-pink-200 dark:border-pink-700">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                    Décrivez la flore vaginale
                                </label>
                                <textarea rows="3" wire:model.blur="{{ $path }}.valeur"
                                          class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 resize-none focus:ring-2 focus:ring-pink-500 dark:focus:ring-pink-400 focus:border-pink-500 dark:focus:border-pink-400 transition-colors"
                                          placeholder="Saisissez une description détaillée de la flore vaginale..."
                                          autofocus></textarea>
                            </div>
                        @endif
                    </div>
                    @break

                @case('GERME')
                    @case('CULTURE')
                        <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-yellow-500 dark:bg-yellow-600 rounded-lg flex items-center justify-center text-white text-lg font-bold shadow-sm">
                                        🧫
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200">Analyse bactériologique</h4>
                                        <p class="text-sm text-yellow-700 dark:text-yellow-300">Sélectionnez les germes identifiés</p>
                                    </div>
                                </div>
                                
                                {{-- Bouton Reset --}}
                                @if($get('selectedOptions') || $get('autreValeur'))
                                    <button type="button"
                                            wire:click.prevent="$set('{{ $path }}', [])"
                                            wire:confirm="Êtes-vous sûr de vouloir réinitialiser cette analyse bactériologique ?"
                                            class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg transition-all duration-200"
                                            title="Réinitialiser l'analyse bactériologique">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Réinitialiser
                                    </button>
                                @endif
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Sélection</label>
                                    <select wire:model.live="{{ $path }}.selectedOptions" multiple
                                            wire:ignore.self
                                            class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 h-40 focus:ring-2 focus:ring-yellow-500 dark:focus:ring-yellow-400 focus:border-yellow-500 dark:focus:border-yellow-400 transition-colors"
                                            onclick="event.stopPropagation();">
                                        
                                        <optgroup label="🔬 Options standards" class="text-slate-600 dark:text-slate-400 font-medium">
                                            <option value="non-rechercher" class="py-1">Non recherché</option>
                                            <option value="en-cours" class="py-1">En cours</option>
                                            <option value="culture-sterile" class="py-1">Culture stérile</option>
                                            <option value="absence-germe-pathogene" class="py-1">Absence de germe pathogène</option>
                                            <option value="Autre" class="py-1">Autre</option>
                                        </optgroup>

                                        @foreach($familles as $famille)
                                            @if($famille->bacteries->count() > 0)
                                                <optgroup label="🦠 {{ $famille->designation }}" class="text-primary-600 dark:text-primary-400 font-medium">
                                                    @foreach($famille->bacteries->where('status', true) as $bacterie)
                                                        <option value="bacterie-{{ $bacterie->id }}" class="py-1">{{ $bacterie->designation }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Champ "Autre" pour GERME/CULTURE --}}
                                @if(is_array($get('selectedOptions')) && in_array('Autre', $get('selectedOptions')))
                                    <div class="p-4 bg-white dark:bg-slate-800 rounded-lg border border-yellow-200 dark:border-yellow-700">
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                            </svg>
                                            Précisez la bactérie non listée
                                        </label>
                                        <input type="text" wire:model.blur="{{ $path }}.autreValeur"
                                            class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-yellow-500 dark:focus:ring-yellow-400 focus:border-yellow-500 dark:focus:border-yellow-400 transition-colors"
                                            placeholder="Ex: Enterococcus faecalis, Candida albicans..."
                                            autofocus>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                            💡 Saisissez le nom scientifique complet de la bactérie ou du micro-organisme
                                        </p>
                                    </div>
                                @endif

                                {{-- ✅ ANTIBIOGRAMME CORRIGÉ --}}
                                @php
                                    $selectedOptions = $get('selectedOptions') ?? [];
                                    $bacterieSelectionnee = null;
                                    if (is_array($selectedOptions)) {
                                        foreach($selectedOptions as $option) {
                                            if (str_starts_with($option, 'bacterie-')) {
                                                $bacterieId = (int) str_replace('bacterie-', '', $option);
                                                $bacterieSelectionnee = $bacterieId;
                                                break;
                                            }
                                        }
                                    }
                                    
                                    // Options qui ne déclenchent pas l'antibiogramme
                                    $standardOptions = ['non-rechercher', 'en-cours', 'culture-sterile', 'absence-germe-pathogene', 'Autre'];
                                    $hasStandardOption = is_array($selectedOptions) && !empty(array_intersect($standardOptions, $selectedOptions));
                                @endphp

                                @if($bacterieSelectionnee && !$hasStandardOption)
                                    <div class="border-t border-yellow-200 dark:border-yellow-800 pt-4" 
                                        onclick="event.stopPropagation();">
                                        <div class="flex items-center gap-3 mb-3">
                                            <div class="w-8 h-8 bg-green-500 dark:bg-green-600 rounded-lg flex items-center justify-center text-white text-sm font-bold shadow-sm">
                                                🦠
                                            </div>
                                            <div>
                                                <h5 class="text-base font-semibold text-green-800 dark:text-green-200">Antibiogramme</h5>
                                                <p class="text-xs text-green-700 dark:text-green-300">Test de sensibilité aux antibiotiques</p>
                                            </div>
                                        </div>
                                        
                                        {{-- ✅ Container avec isolation des événements --}}
                                        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden"
                                            wire:ignore.self>
                                            {{-- ✅ KEY STABLE pour éviter le re-rendering --}}
                                            <livewire:technicien.antibiogramme-table
                                                :prescription-id="app('livewire')->current()->prescription->id"
                                                :analyse-id="$node->id"
                                                :bacterie-id="$bacterieSelectionnee"
                                                :key="'stable-antibiogramme-'.$node->id"
                                                wire:ignore.self
                                            />
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @break

                @case('LEUCOCYTES')
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <div class="space-y-2">
                            <label class="block text-xs text-slate-700 dark:text-slate-300">Valeur</label>
                            <input type="number" wire:model.blur="{{ $path }}.valeur"
                                   class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-colors"
                                   placeholder="Valeur">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Polynucléaires (%)</label>
                            <input type="number" min="0" max="100" wire:model.blur="{{ $path }}.polynucleaires"
                                   class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-colors">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Lymphocytes (%)</label>
                            <input type="number" min="0" max="100" wire:model.blur="{{ $path }}.lymphocytes"
                                   class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-colors">
                        </div>
                    </div>
                    @break

                @default
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Résultat</label>
                        
                        {{-- Si valeurs prédéfinies existent --}}
                        @if($node->formatted_results && is_array($node->formatted_results) && count($node->formatted_results))
                            <select wire:model.live="{{ $path }}.resultats"
                                    class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-colors">
                                <option value="">Veuillez choisir</option>
                                @foreach($node->formatted_results as $opt)
                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                @endforeach
                            </select>
                            
                            {{-- Champ Autre pour types génériques --}}
                            @if($get('resultats') === 'Autre')
                                <div class="mt-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                        Précisez votre réponse
                                    </label>
                                    <textarea rows="3" wire:model.blur="{{ $path }}.valeur"
                                              class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 resize-none focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-colors"
                                              placeholder="Saisissez votre réponse personnalisée..."
                                              autofocus></textarea>
                                </div>
                            @endif
                        @else
                            {{-- Saisie libre standard --}}
                            <textarea rows="4" wire:model.blur="{{ $path }}.resultats"
                                      class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 resize-none focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-colors"
                                      placeholder="Saisie libre…"></textarea>
                        @endif
                    </div>
            @endswitch

            {{-- Conclusion optimisée --}}
            @if(!in_array($type, ['LABEL','MULTIPLE','MULTIPLE_SELECTIF']))
                <div class="space-y-2 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Conclusion</label>
                    <textarea rows="3" wire:model.blur="{{ $path }}.conclusion"
                              class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 resize-none focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-colors"
                              placeholder="Conclusion et notes…"></textarea>
                </div>
            @endif
        </div>
    @endif
</div>