    {{-- livewire.secretaire.prescription.partials.clinique --}}
    @if($etape === 'clinique')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-6">
                <em class="ni ni-notes text-cyan-600 text-xl mr-3"></em>
                <h2 class="text-xl font-heading font-semibold text-slate-800">Informations Cliniques</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- PRESCRIPTEUR --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        <em class="ni ni-user-md mr-1"></em>Prescripteur *
                    </label>
                    <select wire:model="prescripteurId" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                        <option value="">Sélectionner un prescripteur...</option>
                        @foreach($prescripteurs as $prescripteur)
                            <option value="{{ $prescripteur->id }}">{{ $prescripteur->nom }}</option>
                        @endforeach
                    </select>
                    @error('prescripteurId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                {{-- TYPE PATIENT --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        <em class="ni ni-building mr-1"></em>Type de patient
                    </label>
                    <select wire:model="patientType" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                        <option value="EXTERNE">Externe</option>
                        <option value="HOSPITALISE">Hospitalisé</option>
                        <option value="URGENCE-JOUR">Urgence Jour</option>
                        <option value="URGENCE-NUIT">Urgence Nuit</option>
                    </select>
                </div>
                
                {{-- ÂGE --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        <em class="ni ni-calendar mr-1"></em>Âge *
                    </label>
                    <div class="flex space-x-2">
                        <input type="number" wire:model="age" min="0" max="150"
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                        <select wire:model="uniteAge" 
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                            <option value="Ans">Ans</option>
                            <option value="Mois">Mois</option>
                            <option value="Jours">Jours</option>
                        </select>
                    </div>
                    @error('age') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                {{-- POIDS --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        <em class="ni ni-activity mr-1"></em>Poids (kg)
                    </label>
                    <input type="number" wire:model="poids" step="0.1" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                           placeholder="Optionnel">
                </div>
            </div>
            
            {{-- RENSEIGNEMENTS CLINIQUES --}}
            <div class="mt-6">
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    <em class="ni ni-clipboard mr-1"></em>Renseignements cliniques
                </label>
                <textarea wire:model="renseignementClinique" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                          placeholder="Symptômes, antécédents, indications médicales..."></textarea>
            </div>
            
            <div class="flex justify-between mt-6">
                <button wire:click="allerEtape('patient')" 
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    <em class="ni ni-arrow-left mr-2"></em>Patient
                </button>
                <button wire:click="validerInformationsCliniques" 
                        class="px-6 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700">
                    Analyses<em class="ni ni-arrow-right ml-2"></em>
                </button>
            </div>
        </div>
    @endif