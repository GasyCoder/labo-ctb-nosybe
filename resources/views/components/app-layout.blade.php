<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ config('app.name') }}</title>
    @livewireStyles
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <header class="px-4 py-4 bg-white shadow">
            {{ $header ?? '' }}
        </header>

        <main class="p-6">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
