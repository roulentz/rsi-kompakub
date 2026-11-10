{{-- resources/views/layouts/sidebar.blade.php --}}
{{-- Sidebar KompakUB — lebar 360px, bg grey, 4 nav items + Make A Post --}}

<nav class="hidden lg:block bg-surface min-h-screen flex-none" style="width:360px;padding:25px 8px 0">
    <x-sidebar-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="home">
        Beranda
    </x-sidebar-link>
    <x-sidebar-link href="#" icon="search">
        Pencarian
    </x-sidebar-link>
    <x-sidebar-link href="{{ route('percakapan.index') }}" :active="request()->routeIs('percakapan.*')" icon="message">
        Pesan
    </x-sidebar-link>
    <x-sidebar-link href="{{ route('profil.show') }}" :active="request()->routeIs('profil.*')" icon="user">
        Profil
    </x-sidebar-link>

    {{-- Make A Post --}}
    <a href="{{ route('postingan.create') }}"
       class="flex items-center gap-[13px] text-nav text-white bg-navy rounded-pill no-underline"
       style="width:245px;height:43px;margin:92px 0 0 35px;padding-left:40px">
        <x-icon name="plus-circle" class="w-[18px] h-[18px]" />
        Make A Post
    </a>
</nav>
