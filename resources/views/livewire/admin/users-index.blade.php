<div class="container mx-auto px-4 py-6">
    {{-- En-tête avec recherche --}}
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Gestion des utilisateurs</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Liste des utilisateurs du laboratoire</p>
        </div>
        <div class="w-full md:w-auto">
            <input 
                wire:model.live="search"
                type="text" 
                placeholder="Rechercher un utilisateur..."
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
            >
        </div>
    </div>

    {{-- Statistiques par type --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        @foreach($types as $type => $label)
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow border dark:border-gray-700">
                <div class="flex items-center">
                    <div class="p-2 rounded-full 
                        @if($type === 'admin') bg-purple-100 dark:bg-purple-900
                        @elseif($type === 'secretaire') bg-blue-100 dark:bg-blue-900
                        @elseif($type === 'technicien') bg-green-100 dark:bg-green-900
                        @else bg-yellow-100 dark:bg-yellow-900 @endif">
                        <svg class="w-6 h-6 
                            @if($type === 'admin') text-purple-600 dark:text-purple-300
                            @elseif($type === 'secretaire') text-blue-600 dark:text-blue-300
                            @elseif($type === 'technicien') text-green-600 dark:text-green-300
                            @else text-yellow-600 dark:text-yellow-300 @endif" 
                            fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ $label }}</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats[$type] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Liste des utilisateurs --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Liste des utilisateurs</h2>
            <button 
                wire:click="$set('showEditModal', true)"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition"
            >
                Ajouter un utilisateur
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Utilisateur
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Nom d'utilisateur
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Dernière connexion
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Permissions
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Date de création
                        </th>
                        <th class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full 
                                            @if(isset($lastSessions[$user->id]) && \Carbon\Carbon::createFromTimestamp($lastSessions[$user->id]->last_activity)->diffInMinutes(now()) < 5)
                                                bg-green-100 dark:bg-green-900
                                            @else
                                                bg-indigo-100 dark:bg-indigo-900
                                            @endif
                                            flex items-center justify-center">
                                            <span class="text-sm font-medium 
                                                @if(isset($lastSessions[$user->id]) && \Carbon\Carbon::createFromTimestamp($lastSessions[$user->id]->last_activity)->diffInMinutes(now()) < 5)
                                                    text-green-700 dark:text-green-300
                                                @else
                                                    text-indigo-700 dark:text-indigo-300
                                                @endif">
                                                @php
                                                    $words = explode(' ', $user->name);
                                                    $initials = '';
                                                    foreach ($words as $word) {
                                                        if (!empty($word)) {
                                                            $initials .= strtoupper(substr($word, 0, 1));
                                                        }
                                                    }
                                                    $initials = substr($initials, 0, 2);
                                                @endphp
                                                {{ $initials }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($user->type === 'admin') bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200
                                    @elseif($user->type === 'secretaire') bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200
                                    @elseif($user->type === 'technicien') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                                    @else bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 @endif">
                                    @php
                                        $typeNames = [
                                            'admin' => 'Administrateur',
                                            'secretaire' => 'Secrétaire',
                                            'technicien' => 'Technicien',
                                            'biologiste' => 'Biologiste'
                                        ];
                                    @endphp
                                    {{ $typeNames[$user->type] ?? ucfirst($user->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                {{ $user->username }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @php
                                    $userStatus = $this->getUserStatus($user->id, $sessions);
                                @endphp

                                <div class="flex items-center mb-1">
                                    <span class="h-2 w-2 rounded-full mr-2 bg-{{ $userStatus['color'] }}"></span>
                                    <span class="{{ $userStatus['text_color'] }} text-sm">{{ $userStatus['text'] }}</span>
                                </div>

                                @if($userStatus['show_date'] ?? false)
                                    <div class="text-xs text-gray-400 dark:text-gray-500" title="{{ $userStatus['last_activity_full'] }}">
                                        {{ $userStatus['last_activity_formatted'] }}
                                    </div>
                                @elseif($userStatus['status'] === 'never_connected')
                                    <div class="text-xs text-gray-400 dark:text-gray-500">
                                        Créé {{ $user->created_at->diffForHumans() }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <div class="space-y-1">
                                    @if($user->type === 'admin')
                                        <div class="text-xs text-purple-600 dark:text-purple-400">• Administration</div>
                                    @endif
                                    @if(in_array($user->type, ['admin', 'secretaire']))
                                        <div class="text-xs text-blue-600 dark:text-blue-400">• Prescriptions</div>
                                    @endif
                                    @if(in_array($user->type, ['admin', 'technicien']))
                                        <div class="text-xs text-green-600 dark:text-green-400">• Analyses</div>
                                    @endif
                                    @if(in_array($user->type, ['admin', 'biologiste']))
                                        <div class="text-xs text-yellow-600 dark:text-yellow-400">• Validation</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $user->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button 
                                        wire:click="editUser({{ $user->id }})"
                                        class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300"
                                        title="Modifier"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                    </button>
                                    @php
                                        $adminCount = \App\Models\User::where('type', 'admin')->count();
                                    @endphp
                                    @if($user->type !== 'admin' || $adminCount > 1)
                                        <button 
                                            wire:click="confirmUserDeletion({{ $user->id }})"
                                            class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                                            title="Supprimer"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Aucun utilisateur trouvé</h3>
                                    <p class="mt-1 text-sm">Commencez par créer votre premier utilisateur.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $users->links() }}
        </div>
    </div>

    {{-- Modals --}}
    @if($showEditModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        {{ $userIdBeingEdited ? 'Modifier l\'utilisateur' : 'Ajouter un utilisateur' }}
                    </h3>
                    
                    <form wire:submit.prevent="{{ $userIdBeingEdited ? 'updateUser' : 'createUser' }}">
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom complet</label>
                                <input 
                                    type="text" 
                                    id="name" 
                                    wire:model="user.name"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                                >
                                @error('user.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom d'utilisateur</label>
                                <input 
                                    type="text" 
                                    id="username" 
                                    wire:model="user.username"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="Ex: jdupont, marie.martin..."
                                >
                                @error('user.username') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type d'utilisateur</label>
                                <select 
                                    id="type" 
                                    wire:model="user.type"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                                >
                                    @foreach($types as $type => $label)
                                        <option value="{{ $type }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('user.type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mot de passe</label>
                                <input 
                                    type="password" 
                                    id="password" 
                                    wire:model="user.password"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="{{ $userIdBeingEdited ? 'Laisser vide pour ne pas changer' : '' }}"
                                >
                                @error('user.password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmation du mot de passe</label>
                                <input 
                                    type="password" 
                                    id="password_confirmation" 
                                    wire:model="user.password_confirmation"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                                >
                            </div>
                        </div>
                        
                        <div class="mt-6 flex justify-end space-x-3">
                            <button 
                                type="button" 
                                wire:click="$set('showEditModal', false)"
                                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                            >
                                Annuler
                            </button>
                            <button 
                                type="submit" 
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                            >
                                {{ $userIdBeingEdited ? 'Mettre à jour' : 'Créer' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    
    @if($showDeleteModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Confirmer la suppression</h3>
                    <p class="text-gray-600 dark:text-gray-400">Êtes-vous sûr de vouloir supprimer cet utilisateur? Cette action est irréversible.</p>
                    
                    <div class="mt-6 flex justify-end space-x-3">
                        <button 
                            type="button" 
                            wire:click="$set('showDeleteModal', false)"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                        >
                            Annuler
                        </button>
                        <button 
                            type="button" 
                            wire:click="deleteUser"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                        >
                            Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>