{{-- resources/views/layouts/navigation.blade.php --}}
{{-- Navbar KompakUB — tinggi 85px, bg navy, 4 elemen: Logo | Search | Bell | Avatar --}}

{{-- SVG Sprite ikon (disembunyikan, dipakai lewat <use href="#i-xxx" />) --}}
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
    <symbol id="i-home" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2zM9 22V12h6v10"/></symbol>
    <symbol id="i-search" viewBox="0 0 24 24"><path d="M11 3a8 8 0 1 0 0 16 8 8 0 0 0 0-16zM21 21l-4.35-4.35"/></symbol>
    <symbol id="i-message" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></symbol>
    <symbol id="i-user" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 3a4 4 0 1 0 0 8 4 4 0 0 0 0-8z"/></symbol>
    <symbol id="i-plus" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></symbol>
    <symbol id="i-plus-circle" viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zM12 8v8M8 12h8"/></symbol>
    <symbol id="i-filter" viewBox="0 0 24 24"><path d="M3 6h18M6 12h12M10 18h4"/></symbol>
    <symbol id="i-bell" viewBox="0 0 24 24"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 0 1-3.46 0"/></symbol>
    <symbol id="i-award" viewBox="0 0 24 24"><path d="M12 1a7 7 0 1 0 0 14 7 7 0 0 0 0-14zM8.21 13.89L7 23l5-3 5 3-1.21-9.12"/></symbol>
    <symbol id="i-calendar" viewBox="0 0 24 24"><path d="M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2zM16 2v4M8 2v4M3 10h18"/></symbol>
    <symbol id="i-users" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 3a4 4 0 1 0 0 8 4 4 0 0 0 0-8zM23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></symbol>
    <symbol id="i-briefcase" viewBox="0 0 24 24"><path d="M4 7h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2zM16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></symbol>
    <symbol id="i-zap" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></symbol>
    <symbol id="i-userplus" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M8.5 3a4 4 0 1 0 0 8 4 4 0 0 0 0-8zM20 8v6M23 11h-6"/></symbol>
    <symbol id="i-send" viewBox="0 0 24 24"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4z"/></symbol>
    <symbol id="i-chevron" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></symbol>
    <symbol id="i-check-circle" viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zM8 12l3 3 5-6"/></symbol>
    <symbol id="i-check" viewBox="0 0 24 24"><path d="M5 12l5 5 9-10"/></symbol>
    <symbol id="i-close" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18"/></symbol>
    <symbol id="i-eye" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8zM12 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/></symbol>
</svg>

<header class="flex items-start bg-navy text-white" style="height:85px;padding:24px 25px 0 0">
    {{-- Logo --}}
    <div style="width:411px;padding-left:95px">
        <a href="{{ route('dashboard') }}" class="text-lavender text-logo no-underline">
            KompakUB
        </a>
    </div>

    {{-- Search bar --}}
    <div class="flex items-center gap-[21px] text-sm" style="width:617px;height:37px;padding-left:21px;border:1px solid #fff;border-radius:60px;background:rgba(217,225,255,.2)">
        <x-icon name="search" class="w-[18px] h-[18px]" />
        <span class="pt-1">Cari tim, rekan, informasi lomba ....</span>
    </div>

    {{-- Bell (notifikasi) --}}
    <a href="{{ route('notifikasi.index') }}" class="flex ml-auto" aria-label="Notifikasi">
        <x-icon name="bell" class="w-[29px] h-[33px]" style="margin:2px 13px 0 0" />
    </a>

    {{-- Avatar --}}
    <x-avatar :navbar="true" />

    {{-- Mobile hamburger (responsive) --}}
    <div class="lg:hidden ml-4" x-data="{ open: false }">
        <button @click="open = !open" class="p-2">
            <x-icon name="filter" class="w-5 h-5" />
        </button>
        {{-- Mobile dropdown di bawah --}}
        <div x-show="open" x-cloak @click.away="open = false"
             class="absolute top-[85px] left-0 right-0 bg-navy p-4 z-50 lg:hidden">
            <x-sidebar-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="home">Beranda</x-sidebar-link>
            <x-sidebar-link href="#" icon="search">Pencarian</x-sidebar-link>
            <x-sidebar-link href="{{ route('percakapan.index') }}" icon="message">Pesan</x-sidebar-link>
            <x-sidebar-link href="{{ route('profil.show') }}" icon="user">Profil</x-sidebar-link>
        </div>
    </div>
</header>
