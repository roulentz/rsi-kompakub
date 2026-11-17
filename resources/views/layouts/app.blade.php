{{--
    Kerangka halaman setelah login: navbar atas, sidebar kiri, konten tengah, panel Pesan kanan.
    File ini dipegang CV. Anggota lain cukup memakai <x-app-layout> dan mengisi $slot,
    jangan edit file di folder layouts/ dan components/ tanpa lewat CV.

    Slot opsional:
      <x-slot name="header"> judul halaman, tampil di atas konten.
      <x-slot name="aside">  isi kolom kanan, menggantikan panel Pesan bawaan.
--}}
@php
    $admin = Auth::user()->role === 'admin';

    // Menu dipakai bersama oleh sidebar dan menu HP. Format: [label, url, aktif?, ikon].
    // Sesuai panduan Tahap 1 langkah 6: link pakai URL biasa (url()), bukan route(), supaya tidak
    // error saat route-nya belum ada. Pencarian dan Pesan masih placeholder: halamannya milik GP.
    $menu = $admin
        ? [['Laporan Rekomendasi', url('/admin/rekomendasi'), request()->routeIs('admin.*'), 'shield']]
        : [
            ['Beranda', url('/dashboard'),
             request()->routeIs('dashboard', 'beranda', 'postingan.*', 'pelamar.*'), 'home'],
            ['Pencarian', url('/search'), request()->is('search*'), 'search'],
            ['Pesan', url('/dm'), request()->is('dm*'), 'chat'],
            ['Profil', url('/profil'), request()->routeIs('profil.*', 'portofolio.*'), 'user'],
        ];
@endphp
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
        <div class="min-h-screen">
            @include('layouts.navigation', ['menu' => $menu])

            {{-- Sidebar menempel ke tepi kiri layar. Hanya isi tengah yang dibatasi lebarnya. --}}
            <div class="flex">
                @include('layouts.sidebar', ['menu' => $menu])

                <div class="flex min-w-0 flex-1 justify-center">
                    <div class="flex w-full max-w-[1280px]">
                        <div class="min-w-0 flex-1">
                            @isset($header)
                                <header class="px-4 pb-1 pt-6 sm:px-6 lg:px-8">
                                    {{ $header }}
                                </header>
                            @endisset

                            <main class="px-0 pb-12 pt-4">
                                {{ $slot }}
                            </main>
                        </div>

                        <aside class="hidden w-[340px] shrink-0 pr-4 pt-[25px] xl:ml-4 xl:block">
                            <div class="sticky top-[110px]">
                                @isset($aside)
                                    {{ $aside }}
                                @else
                                    <x-panel-pesan />
                                @endisset
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dialog Make A Post Cari Tim (milik SH): dibuka dari pilihan "Cari Tim" di komposer Beranda --}}
        @if (! $admin && Auth::user()->profil)
            @include('postingan._form-cari-tim')
        @endif
    </body>
</html>
