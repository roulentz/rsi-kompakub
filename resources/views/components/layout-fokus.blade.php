{{--
    Layout satu kolom tanpa sidebar untuk alur yang harus selesai dulu (onboarding).
    Dipakai lewat <x-layout-fokus>...</x-layout-fokus>.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'KompakUB') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <header class="bg-gradient-to-r from-navy via-navy to-navy-dark shadow-md shadow-navy/20">
            <div class="mx-auto flex h-16 max-w-5xl items-center justify-center px-4">
                <x-brand class="text-2xl font-extrabold text-lavender" />
            </div>
        </header>

        <main class="mx-auto w-full max-w-3xl px-4 py-8 sm:px-6">
            {{ $slot }}
        </main>
    </body>
</html>
