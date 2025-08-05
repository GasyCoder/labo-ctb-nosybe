{{--  livewire.secretaire.prescription.partials.paiement - VERSION CORRIGÉE  --}}
@if($etape === 'paiement')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <div class="flex items-center mb-6">
                <em class="ni ni-coin text-red-600 text-xl mr-3"></em>
                <h2 class="text-xl font-heading font-semibold text-slate-800 dark:text-slate-100">Paiement</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- RÉCAPITULATIF --}}
                <div>
                    <h3 class="font-medium text-slate-800 mb-4">
                        <em class="ni ni-file-text mr-2"></em>Récapitulatif de la commande
                    </h3>
                    
                    {{-- ANALYSES --}}
                    <div class="space-y-2 mb-4">
                        <h4 class="font-medium text-slate-700 dark:text-slate-300">Analyses :</h4>
                        @foreach($analysesPanier as $analyse)
                            <div class="flex justify-between py-1 border-b border-gray-100 dark:border-slate-600">
                                <div class="flex items-center space-x-2">
                                    <span class="px-1.5 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 rounded font-mono text-xs font-bold">
                                        {{ $analyse['code'] ?? '' }}
                                    </span>
                                    <span class="text-slate-700 dark:text-slate-300">{{ $analyse['designation'] ?? 'N/A' }}</span>
                                </div>
                                <span class="font-medium">
                                    {{-- CORRECTION: Utiliser prix_affiche au lieu de prix --}}
                                    {{ number_format($analyse['prix_affiche'] ?? $analyse['prix_effectif'] ?? 0, 0) }} Ar
                                </span>
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- PRÉLÈVEMENTS --}}
                    @if(count($prelevementsSelectionnes) > 0)
                        <div class="space-y-2 mb-4">
                            <h4 class="font-medium text-slate-700 dark:text-slate-300">Prélèvements :</h4>
                            @foreach($prelevementsSelectionnes as $prelevement)
                                <div class="flex justify-between py-1 border-b border-gray-100 dark:border-slate-600">
                                    <span class="text-slate-700 dark:text-slate-300">
                                        {{ $prelevement['nom'] ?? 'N/A' }} (x{{ $prelevement['quantite'] ?? 1 }})
                                    </span>
                                    <span class="font-medium">
                                        {{ number_format(($prelevement['prix'] ?? 0) * ($prelevement['quantite'] ?? 1), 0) }} Ar
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    
                    {{-- TOTAUX --}}
                    <div class="bg-gray-50 dark:bg-slate-700 p-4 rounded-lg">
                        <div class="flex justify-between mb-2">
                            <span>Sous-total analyses:</span>
                            <span>
                                {{-- CORRECTION: Calculer le sous-total correctement --}}
                                @php
                                    $sousAnalyses = 0;
                                    $parentsTraites = [];
                                    
                                    foreach($analysesPanier as $analyse) {
                                        if (isset($analyse['parent_id']) && $analyse['parent_id'] && !in_array($analyse['parent_id'], $parentsTraites)) {
                                            // Si c'est un enfant d'un parent avec prix, compter le prix du parent une fois
                                            $parent = \App\Models\Analyse::find($analyse['parent_id']);
                                            if ($parent && $parent->prix > 0) {
                                                $sousAnalyses += $parent->prix;
                                                $parentsTraites[] = $analyse['parent_id'];
                                                continue;
                                            }
                                        }
                                        
                                        // Si pas de parent ou parent sans prix
                                        if (!isset($analyse['parent_id']) || !$analyse['parent_id'] || !in_array($analyse['parent_id'], $parentsTraites)) {
                                            $sousAnalyses += $analyse['prix_effectif'] ?? $analyse['prix_original'] ?? 0;
                                        }
                                    }
                                @endphp
                                {{ number_format($sousAnalyses, 0) }} Ar
                            </span>
                        </div>
                        @if(count($prelevementsSelectionnes) > 0)
                            <div class="flex justify-between mb-2">
                                <span>Sous-total prélèvements:</span>
                                <span>{{ number_format(collect($prelevementsSelectionnes)->sum(fn($p) => ($p['prix'] ?? 0) * ($p['quantite'] ?? 1)), 0) }} Ar</span>
                            </div>
                        @endif
                        @if($remise > 0)
                            <div class="flex justify-between mb-2 text-red-600 dark:text-red-400">
                                <span>Remise:</span>
                                <span>-{{ number_format($remise, 0) }} Ar</span>
                            </div>
                        @endif
                        <div class="flex justify-between font-bold text-lg border-t pt-2">
                            <span>Total à payer:</span>
                            <span class="text-red-600 dark:text-red-400">{{ number_format($total, 0) }} Ar</span>
                        </div>
                    </div>
                </div>
                
                {{-- PAIEMENT --}}
                <div>
                    <h3 class="font-medium text-slate-800 mb-4">
                        <em class="ni ni-wallet mr-2"></em>Détails du paiement
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Mode de paiement</label>
                            <select wire:model="modePaiement" 
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg 
                                           bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                           focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                <option value="ESPECES">💵 Espèces</option>
                                <option value="CARTE">💳 Carte bancaire</option>
                                <option value="CHEQUE">📄 Chèque</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Remise (Ar)</label>
                            <input type="number" wire:model.live="remise" min="0" step="100" 
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg 
                                          bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                          focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Montant payé</label>
                            <input type="number" wire:model.live="montantPaye" min="0" step="100" 
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg 
                                          bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                          focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        </div>
                        
                        @if($monnaieRendue > 0)
                            <div class="p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg">
                                <div class="flex items-center">
                                    <em class="ni ni-money text-green-600 dark:text-green-400 mr-2"></em>
                                    <span class="font-medium text-green-800 dark:text-green-200">
                                        Monnaie à rendre : {{ number_format($monnaieRendue, 0) }} Ar
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="flex justify-between mt-8">
                <button wire:click="allerEtape('prelevements')" 
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    <em class="ni ni-arrow-left mr-2"></em>Prélèvements
                </button>
                <button wire:click="validerPaiement" 
                        class="px-8 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                    <em class="ni ni-check mr-2"></em>Valider le paiement
                </button>
            </div>
        </div>
    </div>
@endif