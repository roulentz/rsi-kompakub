{{--
    Kerangka halaman tamu (login, daftar, verifikasi, lupa sandi).
    Layar lebar: panel biru tua dengan slogan di kiri, form di kanan. HP: form saja.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'KompakUB') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="flex min-h-screen">
            <!-- Panel slogan -->
            <div class="relative hidden w-1/2 max-w-[44rem] overflow-hidden bg-navy-deep lg:flex lg:flex-col lg:justify-end lg:p-14">
                <div class="pointer-events-none absolute -left-16 -top-16 h-72 w-72 rounded-full bg-white/5"></div>
                <div class="pointer-events-none absolute -bottom-20 -right-16 h-80 w-80 rounded-full bg-white/5"></div>

                <div class="relative">
                    <h2 class="text-4xl font-semibold leading-tight text-white">
                        Temukan tim tangguh,<br>bangun pengalaman<br>nyata.
                    </h2>
                    <p class="mt-4 max-w-sm text-sm text-white/60">
                        Ruang aman mahasiswa Brawijaya untuk berkolaborasi lintas fakultas
                    </p>
                </div>
            </div>

            <!-- Form -->
            <div class="flex flex-1 flex-col items-center justify-center px-4 py-10 sm:px-8">
                <a href="/" class="mb-6">
                    <x-brand class="text-3xl text-navy" />
                </a>

                <div class="w-full rounded-kub border border-gray-100 bg-white px-6 py-6 shadow-md sm:max-w-md">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
