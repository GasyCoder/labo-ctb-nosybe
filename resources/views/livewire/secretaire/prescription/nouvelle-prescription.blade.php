<div>
{{-- resources/views/livewire/secretaire/prescription/nouvelle-prescription.blade.php --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    {{-- 🎯 HEADER AVEC PROGRESSION --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-bold text-gray-900">
                    Nouvelle Prescription
                </h1>
                <div class="flex items-center space-x-3">
                    @if($patientSelectionne)
                        <div class="bg-blue-50 px-3 py-1 rounded-full">
                            <span class="text-sm font-medium text-blue-700">
                                {{ $patientSelectionne->reference }}
                            </span>
                        </div>
                    @endif
                    <div class="text-sm text-gray-500">
                        {{ now()->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
            
            {{-- BARRE DE PROGRESSION --}}
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                     style="width: {{ $progression }}%"></div>
            </div>
            
            {{-- ÉTAPES --}}
            <div class="flex items-center justify-between mt-4">
                @php
                    $etapes = [
                        'recherche_patient' => ['icône' => 'search', 'label' => 'Patient'],
                        'saisie_prescription' => ['icône' => 'clipboard-list', 'label' => 'Prescription'],
                        'selection_analyses' => ['icône' => 'beaker', 'label' => 'Analyses'],
                        'recapitulatif_paiement' => ['icône' => 'credit-card', 'label' => 'Paiement'],
                        'confirmation' => ['icône' => 'check-circle', 'label' => 'Confirmation']
                    ];
                @endphp
                
                @foreach($etapes as $etape => $config)
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center
                            {{ $etapeActuelle === $etape ? 'bg-blue-600 text-white' : 
                               (in_array($etape, $etapesValidees) ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600') }}">
                            <i class="fas fa-{{ $config['icône'] }} text-sm"></i>
                        </div>
                        <span class="text-xs mt-1 font-medium
                            {{ $etapeActuelle === $etape ? 'text-blue-600' : 'text-gray-500' }}">
                            {{ $config['label'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- 🔍 CONTENU PRINCIPAL SELON ÉTAPE --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- COLONNE PRINCIPALE --}}
        <div class="lg:col-span-2">
            
            {{-- ÉTAPE 1: RECHERCHE PATIENT --}}
            @if($etapeActuelle === 'recherche_patient')
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-search text-blue-600 mr-2"></i>
                            Rechercher un Patient
                        </h2>
                    </div>
                    <div class="p-6">
                        @livewire('secretaire.prescription.recherche-patient')
                    </div>
                </div>
            @endif

            {{-- ÉTAPE 2: SAISIE PRESCRIPTION --}}
            @if($etapeActuelle === 'saisie_prescription')
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-clipboard-list text-blue-600 mr-2"></i>
                            Informations Prescription
                        </h2>
                    </div>
                    <div class="p-6">
                        <form wire:submit.prevent="allerEtape('selection_analyses')" class="space-y-6">
                            
                            {{-- PRESCRIPTEUR --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Prescripteur *
                                </label>
                                <select wire:model="prescripteurId" 
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Sélectionner un prescripteur</option>
                                    @foreach($prescripteurs as $prescripteur)
                                        <option value="{{ $prescripteur->id }}">
                                            Dr {{ $prescripteur->nom }} {{ $prescripteur->prenom }}
                                            @if($prescripteur->specialite)
                                                - {{ $prescripteur->specialite }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('prescripteurId') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>

                            {{-- TYPE PATIENT --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Type de Patient *
                                </label>
                                <div class="grid grid-cols-2 gap-3">
                                    @foreach(['EXTERNE' => 'Externe', 'HOSPITALISE' => 'Hospitalisé', 'URGENCE-JOUR' => 'Urgence Jour', 'URGENCE-NUIT' => 'Urgence Nuit'] as $value => $label)
                                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50
                                            {{ $patientType === $value ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                                            <input type="radio" wire:model="patientType" value="{{ $value }}" class="sr-only">
                                            <div class="w-4 h-4 rounded-full border-2 mr-3
                                                {{ $patientType === $value ? 'border-blue-500 bg-blue-500' : 'border-gray-300' }}">
                                                @if($patientType === $value)
                                                    <div class="w-2 h-2 bg-white rounded-full mx-auto mt-0.5"></div>
                                                @endif
                                            </div>
                                            <span class="text-sm font-medium">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- ÂGE ET POIDS --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Âge *
                                    </label>
                                    <div class="flex space-x-2">
                                        <input type="number" wire:model="age" 
                                               class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               min="0" max="150">
                                        <select wire:model="uniteAge" 
                                                class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="Ans">Ans</option>
                                            <option value="Mois">Mois</option>
                                            <option value="Jours">Jours</option>
                                        </select>
                                    </div>
                                    @error('age') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Poids (kg)
                                    </label>
                                    <input type="number" wire:model="poids" step="0.1"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="Optionnel">
                                </div>
                            </div>

                            {{-- RENSEIGNEMENT CLINIQUE --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Renseignements Cliniques
                                </label>
                                <textarea wire:model="renseignementClinique" rows="3"
                                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                          placeholder="Symptômes, antécédents, indications..."></textarea>
                            </div>

                            {{-- BOUTONS ACTION --}}
                            <div class="flex justify-between pt-4">
                                <button type="button" wire:click="allerEtape('recherche_patient')"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                                    <i class="fas fa-arrow-left mr-2"></i>Retour
                                </button>
                                <button type="submit"
                                        class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                    Continuer<i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            {{-- ÉTAPE 3: SÉLECTION ANALYSES --}}
            @if($etapeActuelle === 'selection_analyses')
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-beaker text-blue-600 mr-2"></i>
                            Sélection des Analyses
                        </h2>
                    </div>
                    <div class="p-6">
                        @livewire('secretaire.prescription.selection-analyses')
                    </div>
                </div>
            @endif

            {{-- ÉTAPE 4: RÉCAPITULATIF PAIEMENT --}}
            @if($etapeActuelle === 'recapitulatif_paiement')
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-credit-card text-blue-600 mr-2"></i>
                            Récapitulatif et Paiement
                        </h2>
                    </div>
                    <div class="p-6">
                        @livewire('secretaire.prescription.recapitulatif-paiement', [
                            'analysesPanier' => $analysesPanier,
                            'montantTotal' => $montantTotal
                        ])
                    </div>
                </div>
            @endif

            {{-- ÉTAPE 5: CONFIRMATION --}}
            @if($etapeActuelle === 'confirmation')
                <div class="bg-white rounded-lg shadow-sm border border-green-200">
                    <div class="px-6 py-4 bg-green-50 border-b border-green-200">
                        <h2 class="text-lg font-semibold text-green-900">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            Prescription Créée avec Succès
                        </h2>
                    </div>
                    <div class="p-6 text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-check text-2xl text-green-600"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Prescription enregistrée</h3>
                        <p class="text-gray-600 mb-6">La prescription a été créée et le paiement enregistré.</p>
                        
                        <div class="flex justify-center space-x-4">
                            <button wire:click="nouveauPrescription"
                                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                <i class="fas fa-plus mr-2"></i>Nouvelle Prescription
                            </button>
                            <button class="px-6 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                                <i class="fas fa-print mr-2"></i>Imprimer Reçu
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- 🛒 SIDEBAR DROITE --}}
        <div class="space-y-6">
            
            {{-- PATIENT SÉLECTIONNÉ --}}
            @if($patientSelectionne)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-4 py-3 bg-blue-50 border-b border-blue-200">
                        <h3 class="font-medium text-blue-900">Patient Sélectionné</h3>
                    </div>
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="font-semibold text-gray-900">
                                    {{ $patientSelectionne->nom }} {{ $patientSelectionne->prenom }}
                                </p>
                                <p class="text-sm text-gray-500">{{ $patientSelectionne->reference }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                {{ $patientSelectionne->statut === 'VIP' ? 'bg-purple-100 text-purple-800' : 
                                   ($patientSelectionne->statut === 'FIDELE' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $patientSelectionne->statut }}
                            </span>
                        </div>
                        
                        <div class="text-sm text-gray-600 space-y-1">
                            @if($patientSelectionne->telephone)
                                <p><i class="fas fa-phone w-4"></i> {{ $patientSelectionne->telephone }}</p>
                            @endif
                            @if($patientSelectionne->email)
                                <p><i class="fas fa-envelope w-4"></i> {{ $patientSelectionne->email }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- PANIER ANALYSES --}}
            @if(count($analysesPanier) > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-4 py-3 bg-green-50 border-b border-green-200">
                        <h3 class="font-medium text-green-900">
                            Panier ({{ count($analysesPanier) }})
                        </h3>
                    </div>
                    <div class="p-4">
                        <div class="space-y-3">
                            @foreach($analysesPanier as $analyse)
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900 text-sm">{{ $analyse['nom'] }}</p>
                                        @if($analyse['parent_nom'])
                                            <p class="text-xs text-gray-500">{{ $analyse['parent_nom'] }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right ml-2">
                                        <p class="font-medium text-gray-900">{{ number_format($analyse['prix'], 0) }} Ar</p>
                                        <button wire:click="retirerAnalyse({{ $analyse['id'] }})" 
                                                class="text-red-500 hover:text-red-700 text-xs">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="border-t border-gray-200 mt-4 pt-4">
                            @if($remise > 0)
                                <div class="flex justify-between text-sm text-gray-600">
                                    <span>Sous-total</span>
                                    <span>{{ number_format($montantTotal + $remise, 0) }} Ar</span>
                                </div>
                                <div class="flex justify-between text-sm text-green-600">
                                    <span>Remise</span>
                                    <span>-{{ number_format($remise, 0) }} Ar</span>
                                </div>
                            @endif
                            <div class="flex justify-between font-bold text-lg">
                                <span>Total</span>
                                <span>{{ number_format($montantTotal, 0) }} Ar</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- HISTORIQUE PATIENT (si disponible) --}}
            @if(!empty($historiquePatient))
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-4 py-3 bg-yellow-50 border-b border-yellow-200">
                        <h3 class="font-medium text-yellow-900">Historique Récent</h3>
                    </div>
                    <div class="p-4 max-h-80 overflow-y-auto">
                        <div class="space-y-3">
                            @foreach(array_slice($historiquePatient, 0, 5) as $prescription)
                                <div class="border-l-2 border-gray-200 pl-3">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($prescription['date'])->format('d/m/Y') }}
                                    </p>
                                    <p class="text-xs text-gray-500 mb-1">
                                        {{ count($prescription['analyses']) }} analyse(s)
                                    </p>
                                    <div class="text-xs text-gray-600">
                                        @foreach(array_slice($prescription['analyses'], 0, 3) as $analyse)
                                            <span class="inline-block bg-gray-100 rounded px-2 py-1 mr-1 mb-1">
                                                {{ $analyse['nom'] }}
                                            </span>
                                        @endforeach
                                        @if(count($prescription['analyses']) > 3)
                                            <span class="text-gray-400">+{{ count($prescription['analyses']) - 3 }} autres</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- 📱 STYLES MOBILE RESPONSIVE --}}
<style>
@media (max-width: 768px) {
    .grid-cols-1.lg\\:grid-cols-3 {
        grid-template-columns: 1fr;
    }
}
</style>
</div>