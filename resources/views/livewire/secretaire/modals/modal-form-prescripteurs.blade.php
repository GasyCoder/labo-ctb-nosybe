<!-- Modal Ajout/Édition Prescripteur -->
@if($showPrescripteurModal)
<div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-[9999]" wire:click="closePrescripteurModal">
    <div class="fixed inset-0 z-[9999] w-screen">
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl" wire:click.stop>
                    <form wire:submit="savePrescripteur">
                        <div class="bg-white dark:bg-gray-800 px-6 py-6">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                                    {{ $prescripteurId ? 'Modifier le prescripteur' : 'Nouveau prescripteur' }}
                                </h2>
                                <button 
                                    type="button"
                                    wire:click="closePrescripteurModal" 
                                    class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 rounded-lg p-2 hover:bg-gray-100 dark:hover:bg-gray-700"
                                >
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Formulaire -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nom -->
                                <div>
                                    <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Nom <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        wire:model="nom"
                                        id="nom"
                                        class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 @error('nom') border-red-500 @enderror"
                                    >
                                    @error('nom')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Prénom -->
                                <div>
                                    <label for="prenom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Prénom</label>
                                    <input 
                                        type="text" 
                                        wire:model="prenom"
                                        id="prenom"
                                        class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 @error('prenom') border-red-500 @enderror"
                                    >
                                    @error('prenom')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Grade (Radio buttons) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Grade</label>
                                    <div class="space-y-2">
                                        @foreach($grades as $value => $label)
                                            <label class="inline-flex items-center mr-6">
                                                <input 
                                                    type="radio" 
                                                    wire:model="grade" 
                                                    value="{{ $value }}"
                                                    class="form-radio h-4 w-4 text-primary-600 border-gray-300 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:focus:ring-primary-400"
                                                >
                                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                            </label>
                                        @endforeach
                                        <label class="inline-flex items-center mr-6">
                                            <input 
                                                type="radio" 
                                                wire:model="grade" 
                                                value=""
                                                class="form-radio h-4 w-4 text-primary-600 border-gray-300 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:focus:ring-primary-400"
                                            >
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Aucun</span>
                                        </label>
                                    </div>
                                    @error('grade')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Spécialité -->
                                <div>
                                    <label for="specialite" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Spécialité</label>
                                    <input 
                                        type="text" 
                                        wire:model="specialite"
                                        id="specialite"
                                        class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 @error('specialite') border-red-500 @enderror"
                                    >
                                    @error('specialite')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Status (Radio buttons) -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                        Statut <span class="text-red-500">*</span>
                                    </label>
                                    <div class="flex flex-wrap gap-6">
                                        @php $statusOptions = App\Models\Prescripteur::getStatusDisponibles(); @endphp
                                        @foreach($statusOptions as $value => $label)
                                            <label class="inline-flex items-center">
                                                <input 
                                                    type="radio" 
                                                    wire:model="status" 
                                                    value="{{ $value }}"
                                                    class="form-radio h-4 w-4 text-primary-600 border-gray-300 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:focus:ring-primary-400"
                                                >
                                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('status')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Téléphone -->
                                <div>
                                    <label for="telephone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Téléphone</label>
                                    <input 
                                        type="text" 
                                        wire:model="telephone"
                                        id="telephone"
                                        class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 @error('telephone') border-red-500 @enderror"
                                    >
                                    @error('telephone')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                                    <input 
                                        type="email" 
                                        wire:model="email"
                                        id="email"
                                        class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 @error('email') border-red-500 @enderror"
                                    >
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Adresse -->
                                <div class="md:col-span-2">
                                    <label for="adresse" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Adresse</label>
                                    <textarea 
                                        wire:model="adresse"
                                        id="adresse"
                                        rows="2"
                                        class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 @error('adresse') border-red-500 @enderror"
                                    ></textarea>
                                    @error('adresse')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Ville -->
                                <div>
                                    <label for="ville" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ville</label>
                                    <input 
                                        type="text" 
                                        wire:model="ville"
                                        id="ville"
                                        class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 @error('ville') border-red-500 @enderror"
                                    >
                                    @error('ville')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Code postal -->
                                <div>
                                    <label for="code_postal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Code postal</label>
                                    <input 
                                        type="text" 
                                        wire:model="code_postal"
                                        id="code_postal"
                                        class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 @error('code_postal') border-red-500 @enderror"
                                    >
                                    @error('code_postal')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Notes -->
                                <div class="md:col-span-2">
                                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                                    <textarea 
                                        wire:model="notes"
                                        id="notes"
                                        rows="3"
                                        class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 @error('notes') border-red-500 @enderror"
                                    ></textarea>
                                    @error('notes')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Actif/Inactif (Checkbox) -->
                                <div class="md:col-span-2">
                                    <div class="flex items-center">
                                        <input 
                                            type="checkbox" 
                                            wire:model="is_active"
                                            id="is_active"
                                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700"
                                        >
                                        <label for="is_active" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Prescripteur actif
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions du modal -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                            <button 
                                type="button"
                                wire:click="closePrescripteurModal"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800 transition-colors"
                            >
                                Annuler
                            </button>
                            <button 
                                type="submit"
                                class="px-4 py-2 bg-primary-600 hover:bg-primary-700 border border-transparent rounded-md shadow-sm text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800 transition-colors"
                            >
                                {{ $prescripteurId ? 'Mettre à jour' : 'Ajouter' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endif