{{--
    Navbar atas biru tua: logo, kolom cari, notifikasi, menu akun, dan menu HP.
    $menu datang dari layouts/app.blade.php.
    Semua link pakai url() sesuai panduan Tahap 1 langkah 6, bukan route().
    Placeholder: kolom cari mengirim GET ke /search (halaman milik GP, belum ada).
--}}
@php($admin = Auth::user()->role === 'admin')
<header x-data="{ open: false }" class="sticky top-0 z-40 bg-navy text-white shadow-sm">
    <div class="mx-auto flex h-14 max-w-[1440px] items-center gap-3 px-4 sm:px-6 lg:h-[85px] lg:pl-0 lg:pr-[25px]">
        <!-- Logo -->
        <a href="{{ url('/dashboard') }}" class="shrink-0 lg:w-[271px] lg:pl-[60px] xl:w-[411px] xl:pl-[95px]">
            <x-brand class="text-xl font-extrabold text-lavender lg:text-[29px]" />
        </a>

        <!-- Cari -->
        @unless ($admin)
            <form method="GET" action="{{ url('/search') }}" role="search" class="mx-auto min-w-0 flex-1 sm:max-w-[617px] lg:mx-0 lg:mr-auto">
                <label for="cari-global" class="sr-only">Cari</label>
                <div class="flex h-[37px] items-center gap-[21px] rounded-full border border-white bg-lavender/20 pl-[21px] pr-4 focus-within:bg-lavender/30">
                    <x-icon name="search" class="h-[18px] w-[18px] shrink-0 text-white" />
                    <input id="cari-global" type="search" name="q" value="{{ request('q') }}"
                           placeholder="Cari tim, rekan, informasi lomba ..."
                           class="w-full min-w-0 border-0 bg-transparent p-0 text-sm text-white placeholder-white focus:ring-0" />
                </div>
            </form>
        @else
            <div class="flex-1"></div>
        @endunless

        <div class="ms-auto flex shrink-0 items-center gap-1 sm:gap-2">
            <!-- Notifikasi -->
            @unless ($admin)
                <a href="{{ url('/notifikasi') }}"
                   class="rounded-full p-2 transition hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-brand"
                   aria-label="Notifikasi">
                    <x-icon name="bell" class="h-[29px] w-[29px]" />
                </a>
            @endunless

            <!-- Menu akun -->
            <div class="hidden sm:block">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center rounded-full p-1 transition hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-brand" aria-label="Menu akun">
                            <x-avatar :user="Auth::user()" size="h-[37px] w-[37px] !border-[3px] border-lavender" />
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="border-b border-gray-100 px-4 py-2">
                            <div class="truncate text-sm font-semibold text-navy-deep">{{ Auth::user()->name }}</div>
                            <div class="truncate text-xs text-gray-500">{{ Auth::user()->email }}</div>
                        </div>

                        @unless ($admin)
                            <x-dropdown-link :href="url('/profil')">Profil Saya</x-dropdown-link>
                        @endunless
                        <x-dropdown-link :href="url('/profile')">Pengaturan Akun</x-dropdown-link>

                        <form method="POST" action="{{ url('/logout') }}">
                            @csrf
                            <x-dropdown-link :href="url('/logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger (HP dan tablet, sidebar disembunyikan di bawah lg) -->
            <button @click="open = ! open" class="rounded-full p-2 transition hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-brand lg:hidden"
                    aria-label="Buka menu" :aria-expanded="open">
                <x-icon name="bars" class="h-6 w-6" x-show="! open" />
                <x-icon name="x" class="h-6 w-6" x-show="open" x-cloak />
            </button>
        </div>
    </div>

    <!-- Menu HP -->
    <div x-show="open" x-cloak x-transition class="border-t border-white/10 bg-navy lg:hidden">
        <nav class="space-y-1 px-4 py-3" aria-label="Menu utama">
            @foreach ($menu as [$label, $url, $aktif, $ikon])
                <x-responsive-nav-link :href="$url" :active="$aktif">
                    <x-icon :name="$ikon" class="h-5 w-5" />
                    {{ $label }}
                </x-responsive-nav-link>
            @endforeach
        </nav>

        <div class="border-t border-white/10 px-4 py-3">
            <div class="flex items-center gap-3 px-1">
                <x-avatar :user="Auth::user()" size="h-9 w-9" />
                <div class="min-w-0">
                    <div class="truncate text-sm font-semibold">{{ Auth::user()->name }}</div>
                    <div class="truncate text-xs text-white/70">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="url('/profile')">Pengaturan Akun</x-responsive-nav-link>
                <form method="POST" action="{{ url('/logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="url('/logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</header>
