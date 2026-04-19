<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts: Manrope + Inter -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap"
        rel="stylesheet">
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">
    <style>
        /* Minimal global overrides: no tailwind config, only font utilities and icon baseline */
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .font-headline,
        h1,
        h2,
        h3,
        .font-manrope {
            font-family: 'Manrope', system-ui, sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
            display: inline-block;
        }

        /* hide scrollbar for custom side nav if needed */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
    </style>
    @livewireStyles
</head>

<body x-data="{ sidebarOpen: true }" class="min-h-screen">
    <livewire:elements.sidebar />
    <livewire:elements.navbar />
    <main class="transition-all duration-500 bg-[#f7f9fb]" :class="sidebarOpen ? 'lg:ml-64 px-0' : 'lg:ml-0 px-4'">
        {{ $slot }}
    </main>

    @livewireScripts
    @stack('scripts')
</body>

</html>
