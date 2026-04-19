<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts: Manrope (headline) -->
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
        }

        .font-headline {
            font-family: 'Manrope', system-ui, sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
            display: inline-block;
        }

        .bg-architectural-overlay {
            background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBjE1fi4ybPiNRyg7jKukLJC0l-sa5K1oaDQItsXLW5RrDfmH1D0fjmiyi43CKBElBVgvKuRPdQz9lVFZ_zISNJCqzyq5D9pbRZc8ebKZljYVTUZGcUKMcR0uJ6omdMQZbIOqbKQ79dEthBtBXVW5BrvgjctpBWO9wANqqGRICiKmVHNm6riY0rbVQVUlO0bWjQ4xHCS17-cOqHu67ADDZJDII1iTqmSHujXV5fu19rW-8brQNnAEVIkr86m8pLhQB9J1dwZAnvxv4');
            background-size: cover;
            background-position: center;
        }
    </style>
    @livewireStyles
</head>

<body>
    {{ $slot }}

    @livewireScripts
</body>

</html>
