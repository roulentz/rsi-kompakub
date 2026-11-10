<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'KompakUB') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,700,800|plus-jakarta-sans:400,600,700,800" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col items-center bg-navy pt-6 sm:pt-0">
        {{-- Logo KompakUB --}}
        <div class="mt-12 mb-6">
            <a href="/" class="text-lavender text-logo no-underline">
                KompakUB
            </a>
        </div>

        {{-- Konten kartu (login / register / verify) --}}
        <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-md rounded-kub-lg overflow-hidden">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
