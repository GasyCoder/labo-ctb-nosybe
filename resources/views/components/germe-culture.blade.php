@props(['node', 'path', 'get', 'familles'])

@php
    // 🔎 Lis l'état depuis le composant Livewire parent (pas Filament)
    $lw = app('livewire')->current();
    $selectedOptions = $lw->getSelectedOptions($node->id) ?? [];
    $autreValeur     = $lw->getAutreValeur($node->id) ?? null;

    $standardOptions = ['non-rechercher','en-cours','culture-sterile','absence-germe-pathogene','Autre'];

    $bacteriesSelectionnees = [];
    foreach ($selectedOptions as $opt) {
        if (is_string($opt) && str_starts_with($opt, 'bacterie-')) {
            $bacteriesSelectionnees[] = (int) str_replace('bacterie-', '', $opt);
        }
    }

    $hasStandardOption = !empty(array_intersect($standardOptions, $selectedOptions));
    $hasSelection      = !empty($selectedOptions) || !empty($autreValeur);

    // dirty = il faut cliquer "Synchroniser" pour (re)monter les sous-composants
    $dirty = $lw->isAnalyseDirty($node->id);
@endphp

<div>

    {{-- Header --}}
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

        {{-- Reset (ciblé + pas de propagation) --}}
        @if($hasSelection)
            <button
                type="button"
                wire:click.stop.prevent="clearGermeSelection({{ $node->id }}, '{{ $path }}')"
                wire:loading.attr="disabled"
                wire:target="clearGermeSelection"
                wire:confirm="Êtes-vous sûr de vouloir réinitialiser cette analyse bactériologique ?"
                class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg transition-all duration-200"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Réinitialiser
            </button>
        @endif
    </div>

    {{-- Options standards (exclusives) --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Options standards (exclusives)</label>
        <div class="flex flex-wrap gap-2">
            @foreach($standardOptions as $opt)
                @php $active = in_array($opt, $selectedOptions, true); @endphp
                <button
                    type="button"
                    wire:click.stop.prevent="toggleStandardOption({{ $node->id }}, '{{ $path }}', '{{ $opt }}')"
                    wire:loading.attr="disabled"
                    wire:target="toggleStandardOption"
                    class="px-3 py-1.5 rounded-full text-xs font-medium border transition
                           {{ $active
                                ? 'bg-yellow-600 text-white border-yellow-700'
                                : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-300 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                    {{ $opt === 'Autre' ? 'Autre (préciser)' : Str::headline(str_replace('-', ' ', $opt)) }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Champ "Autre" uniquement si sélectionné (⚠️ plus de wire:model.live) --}}
    @if(in_array('Autre', $selectedOptions, true))
        <div class="mb-4 p-4 bg-white dark:bg-slate-800 rounded-lg border border-yellow-200 dark:border-yellow-700">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                Précisez la bactérie non listée
            </label>
            <input
                type="text"
                wire:model.live="results.{{ $node->id }}.autreValeur"
                class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-yellow-500 dark:focus:ring-yellow-400 focus:border-yellow-500 dark:focus:border-yellow-400 transition-colors"
                placeholder="Ex: Enterococcus faecalis, Candida albicans...">
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                💡 Saisissez le nom scientifique complet de la bactérie ou du micro-organisme.
            </p>
        </div>
    @endif

    {{-- Sélection des bactéries (désactivée si une option standard est active) --}}
    <div class="space-y-2" onclick="event.stopPropagation();">
        @foreach($familles as $famille)
            @if($famille->bacteries->count() > 0)
                <div class="p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg">
                    <div class="text-xs font-semibold text-primary-700 dark:text-primary-300 mb-2">
                        🦠 {{ $famille->designation }}
                    </div>

                    <div class="flex flex-wrap gap-2" onclick="event.stopPropagation();">
                        @foreach($famille->bacteries->where('status', true) as $bacterie)
                            @php
                                $selected = in_array('bacterie-'.$bacterie->id, $selectedOptions, true);
                            @endphp

                            <button
                                type="button"
                                wire:key="chip-{{ $node->id }}-{{ $bacterie->id }}"
                                wire:click.stop.prevent="toggleBacterieOption({{ $node->id }}, '{{ $path }}', {{ $bacterie->id }})"
                                wire:loading.attr="disabled"
                                wire:target="toggleBacterieOption"
                                class="px-3 py-1.5 rounded-full text-xs font-medium border transition
                                       {{ $selected
                                            ? 'bg-green-600 text-white border-green-700'
                                            : ($hasStandardOption
                                                ? 'bg-slate-100 dark:bg-slate-900/40 text-slate-400 border-slate-200 dark:border-slate-700 cursor-not-allowed'
                                                : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-300 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700') }}"
                                {{ $hasStandardOption ? 'disabled aria-disabled=true' : '' }}
                            >
                                {{ $bacterie->designation }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    {{-- Bloc antibiogrammes (uniquement si ≥1 bactérie et aucune option standard) --}}
    @if(!empty($bacteriesSelectionnees) && !$hasStandardOption)
        <div class="border-t border-yellow-200 dark:border-yellow-800 pt-4" onclick="event.stopPropagation();">

            <div class="flex items-center justify-between gap-3 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-green-500 dark:bg-green-600 rounded-lg flex items-center justify-center text-white text-sm font-bold shadow-sm">
                        🦠
                    </div>
                    <div>
                        <h5 class="text-base font-semibold text-green-800 dark:text-green-200">
                            Antibiogrammes ({{ count($bacteriesSelectionnees) }} bactérie{{ count($bacteriesSelectionnees) > 1 ? 's' : '' }})
                        </h5>
                        <p class="text-xs text-green-700 dark:text-green-300">
                            Cliquez sur <strong>Synchroniser</strong> pour charger/mettre à jour les tableaux.
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    wire:click.stop="syncAntibiogrammes({{ $node->id }})"
                    wire:loading.attr="disabled"
                    wire:target="syncAntibiogrammes"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white text-sm font-medium rounded-lg transition-colors"
                >
                    <span wire:loading.remove wire:target="syncAntibiogrammes">Synchroniser</span>
                    <span wire:loading wire:target="syncAntibiogrammes">Synchronisation…</span>
                </button>
            </div>

            {{-- Tant que c'est "dirty", on ne monte pas les sous-composants pour éviter les blocages --}}
            @if($dirty)
                <div class="p-3 text-xs text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/40 rounded">
                    Sélection modifiée — cliquez sur <strong>Synchroniser</strong> pour charger les tableaux d’antibiogramme.
                </div>
            @else
                <div class="space-y-2">
                    @foreach($bacteriesSelectionnees as $index => $bacterieId)
                        @php
                            $bacterie = null;
                            foreach($familles as $fam) {
                                foreach($fam->bacteries as $b) {
                                    if ($b->id == $bacterieId) { $bacterie = $b; break 2; }
                                }
                            }
                        @endphp

                        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden shadow-sm">
                            <button
                                type="button"
                                onclick="toggleAccordion('accordion-{{ $node->id }}-{{ $bacterieId }}')"
                                class="w-full px-6 py-4 bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border-b border-green-200 dark:border-green-800 text-left hover:from-green-100 transition-colors"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-6 h-6 bg-green-500 dark:bg-green-600 rounded-md flex items-center justify-center text-white text-xs font-bold">
                                            🦠
                                        </div>
                                        <div>
                                            <h6 class="font-semibold text-green-800 dark:text-green-200">
                                                {{ $bacterie ? $bacterie->designation : 'Bactérie ID: '.$bacterieId }}
                                            </h6>
                                            @if($bacterie && $bacterie->famille)
                                                <p class="text-xs text-green-700 dark:text-green-300">{{ $bacterie->famille->designation }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <svg
                                        class="w-5 h-5 text-green-600 dark:text-green-400 transition-transform duration-200 accordion-arrow"
                                        id="arrow-{{ $node->id }}-{{ $bacterieId }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </button>

                            <div id="accordion-{{ $node->id }}-{{ $bacterieId }}" class="accordion-content {{ $index === 0 ? '' : 'hidden' }}">
                                {{-- lazy = pas de montage tant que pas visible, et seulement après sync (dirty=false) --}}
                                <livewire:technicien.antibiogramme-table
                                    lazy
                                    :prescription-id="app('livewire')->current()->prescription->id"
                                    :analyse-id="$node->id"
                                    :bacterie-id="$bacterieId"
                                    :compact="true"
                                    :hide-header="true"
                                    :key="'accordion-antibiogramme-'.$node->id.'-'.$bacterieId" />
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

</div>

<script>
    function toggleAccordion(contentId) {
        const content = document.getElementById(contentId);
        const arrow = document.getElementById('arrow-' + contentId.replace('accordion-', ''));
        if (!content || !arrow) return;
        const open = content.classList.contains('hidden');
        if (open) {
            content.classList.remove('hidden');
            arrow.style.transform = 'rotate(180deg)';
        } else {
            content.classList.add('hidden');
            arrow.style.transform = 'rotate(0deg)';
        }
    }
</script>
