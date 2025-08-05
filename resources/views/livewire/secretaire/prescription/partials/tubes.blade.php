  {{-- livewire.secretaire.prescription.partials.tubes --}}
  @if($etape === 'tubes')
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center mb-6">
                    <em class="ni ni-printer text-slate-600 text-xl mr-3"></em>
                    <h2 class="text-xl font-heading font-semibold text-slate-800">Tubes et Étiquettes</h2>
                </div>
                
                @if(count($tubesGeneres) > 0)
                    <div class="mb-6">
                        <h3 class="font-medium text-slate-800 mb-4">
                            {{ count($tubesGeneres) }} tube(s) généré(s) avec succès
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($tubesGeneres as $tube)
                                <div class="p-4 border border-gray-200 rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-medium text-slate-800">{{ $tube['numero_tube'] ?? 'Tube #'.$tube['id'] }}</span>
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">{{ $tube['statut'] }}</span>
                                    </div>
                                    <div class="text-sm text-slate-600">
                                        <div>Code-barre: {{ $tube['code_barre'] ?? 'En cours...' }}</div>
                                        <div>Type: {{ $tube['type_tube'] ?? 'Standard' }}</div>
                                        <div>Volume: {{ $tube['volume_ml'] ?? 5 }} ml</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="flex justify-center space-x-4">
                        <button wire:click="imprimerEtiquettes" 
                                class="px-6 py-3 bg-slate-600 text-white rounded-lg hover:bg-slate-700">
                            <em class="ni ni-printer mr-2"></em>Imprimer étiquettes
                        </button>
                        <button wire:click="ignorerEtiquettes" 
                                class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            Ignorer l'impression
                        </button>
                    </div>
                @else
                    <div class="text-center py-8 text-slate-500">
                        <em class="ni ni-alert-circle text-4xl mb-4"></em>
                        <p>Aucun tube généré</p>
                    </div>
                @endif
            </div>
        </div>
    @endif