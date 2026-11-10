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
    <div class="min-h-screen">
        <!-- Navbar -->
        @include('layouts.navigation')

        <!-- Layout 3 kolom: Sidebar | Feed | Inbox -->
        <div class="flex items-start" style="padding-left:11px">

            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Feed (konten utama) -->
            <main class="kub-feed">
                {{ $slot }}
            </main>

            <!-- Inbox / Pesan (kolom kanan, opsional) -->
            @if(isset($inbox))
                <aside class="kub-inbox">
                    {{ $inbox }}
                </aside>
            @else
                @include('components.panel-pesan')
            @endif

        </div>
    </div>
</body>
</html>
