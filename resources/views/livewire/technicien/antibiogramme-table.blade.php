{{-- resources/views/livewire/technicien/antibiogramme-table.blade.php --}}

<div class="border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden bg-white dark:bg-slate-900"
     onclick="event.stopPropagation();">
    
    {{-- Header --}}
    <div class="p-4 bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-700 border-b border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-green-500 dark:bg-green-600 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Antibiogramme</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">Test de sensibilité aux antibiotiques</p>
            </div>
        </div>
    </div>

    {{-- ✅ Formulaire d'ajout avec événements isolés --}}
    <div class="p-6 border-b border-slate-200 dark:border-slate-700 bg-primary-50 dark:bg-primary-900/20"
         onclick="event.stopPropagation();">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-end">
            <div class="lg:col-span-5">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Antibiotique</label>
                <select wire:model="newAntibiotique" 
                        onclick="event.stopPropagation();"
                        class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-colors">
                    <option value="">Choisir un antibiotique...</option>
                    @foreach($antibiotiques as $antibiotique)
                        <option value="{{ $antibiotique->id }}">{{ $antibiotique->designation }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="lg:col-span-3">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Interprétation</label>
                <select wire:model="newInterpretation" 
                        onclick="event.stopPropagation();"
                        class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-colors">
                    <option value="S">S (Sensible)</option>
                    <option value="I">I (Intermédiaire)</option>
                    <option value="R">R (Résistant)</option>
                </select>
            </div>
            
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Diamètre (mm)</label>
                <input type="number" wire:model="newDiametre" step="0.1" min="0" max="50"
                       onclick="event.stopPropagation();"
                       class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-colors"
                       placeholder="Ex: 15.5">
            </div>
            
            <div class="lg:col-span-2">
                {{-- ✅ BOUTON AVEC ÉVÉNEMENTS ISOLÉS --}}
                <button wire:click.stop.prevent="addAntibiotique" 
                        onclick="event.stopPropagation(); event.preventDefault();"
                        class="w-full px-4 py-2.5 bg-primary-600 hover:bg-primary-700 dark:bg-primary-700 dark:hover:bg-primary-600 text-white rounded-lg font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-slate-800 transition-all duration-200 shadow-sm">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Ajouter
                    </span>
                </button>
            </div>
        </div>
        
        {{-- Messages d'erreur... (reste identique) --}}
    </div>

    {{-- ✅ Table avec événements isolés --}}
    @if(count($resultats) > 0)
        <div class="overflow-x-auto" onclick="event.stopPropagation();">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-800">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                            Antibiotique
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                            Interprétation
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                            Diamètre (mm)
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-900 divide-y divide-slate-200 dark:divide-slate-700">
                    @foreach($resultats as $resultat)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-slate-100">
                                {{ $resultat['antibiotique']['designation'] }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                {{-- ✅ SELECT AVEC ÉVÉNEMENTS ISOLÉS --}}
                                <select wire:change.stop="updateResultat({{ $resultat['id'] }}, 'interpretation', $event.target.value)"
                                        onclick="event.stopPropagation();"
                                        class="px-3 py-2 border rounded-lg text-sm font-medium transition-colors focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-slate-800
                                               @if($resultat['interpretation'] === 'S') 
                                                   bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 border-green-200 dark:border-green-800 focus:ring-green-500
                                               @elseif($resultat['interpretation'] === 'I') 
                                                   bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 border-yellow-200 dark:border-yellow-800 focus:ring-yellow-500
                                               @else 
                                                   bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 border-red-200 dark:border-red-800 focus:ring-red-500
                                               @endif">
                                    <option value="S" {{ $resultat['interpretation'] === 'S' ? 'selected' : '' }}>S (Sensible)</option>
                                    <option value="I" {{ $resultat['interpretation'] === 'I' ? 'selected' : '' }}>I (Intermédiaire)</option>
                                    <option value="R" {{ $resultat['interpretation'] === 'R' ? 'selected' : '' }}>R (Résistant)</option>
                                </select>
                            </td>
                            <td class="px-6 py-4 text-center">
                                {{-- ✅ INPUT AVEC ÉVÉNEMENTS ISOLÉS --}}
                                <input type="number" 
                                       value="{{ $resultat['diametre_mm'] }}"
                                       wire:change.stop="updateResultat({{ $resultat['id'] }}, 'diametre_mm', $event.target.value)"
                                       onclick="event.stopPropagation();"
                                       step="0.1" min="0" max="50"
                                       class="w-24 px-3 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-sm text-center text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-colors"
                                       placeholder="--">
                            </td>
                            <td class="px-6 py-4 text-center">
                                {{-- ✅ BOUTON SUPPRESSION AVEC ÉVÉNEMENTS ISOLÉS --}}
                                <button wire:click.stop.prevent="removeResultat({{ $resultat['id'] }})"
                                        wire:confirm="Êtes-vous sûr de vouloir retirer cet antibiotique ?"
                                        onclick="event.stopPropagation(); event.preventDefault();"
                                        class="p-2 text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all duration-200"
                                        title="Supprimer cet antibiotique">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        {{-- Empty State --}}
        <div class="p-8 text-center">
            <div class="w-16 h-16 mx-auto mb-4 bg-slate-100 dark:bg-slate-800 rounded-lg flex items-center justify-center">
                <svg class="w-8 h-8 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2"></path>
                </svg>
            </div>
            <h4 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-2">Aucun antibiotique testé</h4>
            <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                Utilisez le formulaire ci-dessus pour ajouter des antibiotiques et effectuer les tests de sensibilité.
            </p>
        </div>
    @endif

    {{-- Messages de feedback --}}
    @if (session()->has('message'))
        <div class="p-4 bg-green-50 dark:bg-green-900/20 border-t border-green-200 dark:border-green-800">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-green-700 dark:text-green-300 text-sm font-medium">{{ session('message') }}</span>
            </div>
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="p-4 bg-red-50 dark:bg-red-900/20 border-t border-red-200 dark:border-red-800">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-red-700 dark:text-red-300 text-sm font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif
</div>