<!-- resources/views/livewire/secretaire/prescription/recherche-patient.blade.php -->
<div class="p-4">
 <!-- Search Input and Criteria -->
 <div class="mb-4">
 <div class="flex items-center space-x-4">
 <input 
 wire:model.debounce.500ms="recherche" 
 type="text" 
 class="border p-2 rounded w-full" 
 placeholder="Rechercher un patient par {{ $critereRecherche }}..."
 >
 <select wire:model="critereRecherche" class="border p-2 rounded">
 <option value="nom">Nom</option>
 <option value="reference">Référence</option>
 <option value="telephone">Téléphone</option>
 </select>
 <select wire:model="trierPar" class="border p-2 rounded">
 <option value="recent">Récent</option>
 <option value="nom">Nom</option>
 <option value="statut">Statut</option>
 </select>
 </div>
 <div class="text-sm mt-2">{{ $messageRecherche }}</div>
 @if($rechercheEnCours)
 <div class="text-gray-500">Recherche en cours...</div>
 @endif
 </div>

 <!-- Search Results -->
 @if(!empty($resultatRecherche))
 <div class="mb-4">
 <h3 class="text-lg font-semibold">Résultats de la recherche ({{ $nombreResultats }})</h3>
 <ul class="border rounded divide-y">
 @foreach($resultatRecherche as $patient)
 <li 
 wire:click="selectionnerPatient({{ $patient['id'] }})" 
 class="p-2 hover:bg-gray-100 cursor-pointer"
 >
 {{ $patient['nom'] }} {{ $patient['prenom'] ?? '' }} 
 (Réf: {{ $patient['reference'] }}) 
 - {{ $patient['telephone'] ?? 'N/A' }}
 </li>
 @endforeach
 </ul>
 </div>
 @endif

 <!-- Selected Patient -->
 @if($patientSelectionne)
 <div class="mb-4 p-4 border rounded bg-gray-50">
 <h3 class="text-lg font-semibold">Patient Sélectionné</h3>
 <p><strong>Nom:</strong> {{ $patientSelectionne->nom }} {{ $patientSelectionne->prenom ?? '' }}</p>
 <p><strong>Référence:</strong> {{ $patientSelectionne->reference }}</p>
 <p><strong>Téléphone:</strong> {{ $patientSelectionne->telephone ?? 'N/A' }}</p>
 <p><strong>Statut:</strong> {{ $patientSelectionne->statut }}</p>
 <button 
 wire:click="deselectionnerPatient" 
 class="mt-2 bg-red-500 text-white px-4 py-2 rounded"
 >
 Désélectionner
 </button>
 </div>
 @endif

 <!-- Patient History -->
 @if($afficherHistorique && !empty($historiquePatient))
 <div class="mb-4">
 <h3 class="text-lg font-semibold">Historique du patient</h3>
 <ul class="border rounded divide-y">
 @foreach($historiquePatient as $prescription)
 <li class="p-2">
 Prescription #{{ $prescription['reference'] }} 
 - Date: {{ $prescription['created_at'] }} 
 - Statut: {{ $prescription['status'] }}
 </li>
 @endforeach
 </ul>
 </div>
 @endif

 <!-- Patient Statistics -->
 @if($afficherHistorique && !empty($statistiquesPatient))
 <div class="mb-4">
 <h3 class="text-lg font-semibold">Statistiques du patient</h3>
 <p><strong>Nombre de prescriptions:</strong> {{ $statistiquesPatient['nombre_prescriptions'] }}</p>
 <p><strong>Montant total dépensé:</strong> {{ $statistiquesPatient['montant_total_depense'] }} €</p>
 <p><strong>Dernière visite:</strong> {{ $statistiquesPatient['derniere_visite'] ?? 'N/A' }}</p>
 @if(!empty($statistiquesPatient['analyses_frequentes']))
 <p><strong>Analyses fréquentes:</strong> 
 {{ implode(', ', array_column($statistiquesPatient['analyses_frequentes'], 'nom')) }}
 </p>
 @endif
 </div>
 @endif

 <!-- Recent Patients -->
 @if(!empty($patientsRecents))
 <div class="mb-4">
 <h3 class="text-lg font-semibold">Patients récents</h3>
 <ul class="border rounded divide-y">
 @foreach($patientsRecents as $patient)
 <li 
 wire:click="selectionnerPatient({{ $patient['id'] }})" 
 class="p-2 hover:bg-gray-100 cursor-pointer"
 >
 {{ $patient['nom'] }} {{ $patient['prenom'] ?? '' }} 
 (Réf: {{ $patient['reference'] }}) 
 - Dernière visite: {{ $patient['derniere_visite'] }}
 </li>
 @endforeach
 </ul>
 </div>
 @endif

 <!-- New Patient Button -->
 <button 
 wire:click="afficher FormulaireNouveauPatient" 
 class="bg-blue-500 text-white px-4 py-2 rounded"
 >
 Nouveau Patient
 </button>
</div>