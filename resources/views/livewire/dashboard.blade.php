<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }} - {{ $user->type_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if($user->type === 'admin')
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">Dashboard Administrateur</h3>
                        <p>Bienvenue {{ $user->name }}</p>
                    </div>
                </div>
            @endif

            @if($user->type === 'secretaire')
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">Dashboard Secrétaire</h3>
                        <p>Bienvenue {{ $user->name }}</p>
                    </div>
                </div>
            @endif

            @if($user->type === 'technicien')
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">Dashboard Technicien</h3>
                        <p>Bienvenue {{ $user->name }}</p>
                    </div>
                </div>
            @endif

            @if($user->type === 'biologiste')
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">Dashboard Biologiste</h3>
                        <p>Bienvenue {{ $user->name }}</p>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>