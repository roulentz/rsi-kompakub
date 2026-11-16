
{{--
    Navbar atas: logo, pencarian, notifikasi, menu akun, dan menu HP.
    $menu berasal dari layouts/app.blade.php.
--}}
@php($admin = Auth::user()->role === 'admin')

<header x-data="{ open: false }" class="sticky top-0 z-40 bg-gradient-to-r from-navy via-navy to-navy-dark text-white shadow-lg shadow-navy/25 ring-1 ring-white/5">
    <div class="flex h-14 items-center gap-3 px-4 sm:px-6 lg:h-[85px] lg:pl-0 lg:pr-8">

        <!-- Logo -->
        <a href="{{ url('/dashboard') }}" class="shrink-0 lg:w-[260px] lg:pl-10 xl:w-[320px] xl:pl-14">
            <x-brand class="text-xl font-extrabold text-lavender lg:text-[29px]" />
        </a>

        <!-- SCH: Pencarian -->
        @unless ($admin)
            <form
                method="GET"
                action="{{ url('/search') }}"
                role="search"
                x-data="{
                    filter: '{{ request('type') === 'people' ? 'people' : 'posts' }}',
                    showFilter: false
                }"
                class="mx-auto min-w-0 flex-1 sm:max-w-[617px] lg:absolute lg:left-1/2 lg:w-[617px] lg:max-w-[calc(100%-640px)] lg:-translate-x-1/2"
            >
                <input type="hidden" name="type" x-model="filter">

                <label for="cari-global" class="sr-only">Cari</label>

                <div class="relative">
                    <div class="flex h-[37px] items-center gap-3 rounded-full border border-white/70 bg-white/10 pl-[21px] pr-3 backdrop-blur transition focus-within:border-brand focus-within:bg-white/20">

                        <x-icon
                            name="search"
                            class="h-[18px] w-[18px] shrink-0 text-white"
                        />

                        <input
                            id="cari-global"
                            type="search"
                            name="q"
                            value="{{ request()->is('search*') ? request('q') : '' }}"
                            placeholder="Cari informasi atau orang di sekitar"
                            maxlength="100"
                            class="w-full min-w-0 border-0 bg-transparent p-0 text-sm text-white placeholder-white focus:ring-0"
                        />

                        <button
                            type="button"
                            @click="showFilter = !showFilter"
                            @keydown.escape.window="showFilter = false"
                            :aria-expanded="showFilter"
                            aria-controls="filter-pencarian"
                            aria-label="Filter pencarian"
                            class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full transition hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-brand"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-[19px] w-[19px]"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M4 7h16M7 12h13M10 17h10"
                                />
                            </svg>
                        </button>
                    </div>

                    <!-- Filter Pencarian -->
                    <div
                        id="filter-pencarian"
                        x-show="showFilter"
                        x-cloak
                        x-transition.origin.top.right
                        @click.outside="showFilter = false"
                        class="absolute right-0 top-full z-50 mt-2 w-52 overflow-hidden rounded-xl border border-gray-100 bg-white py-2 text-navy shadow-xl"
                    >
                        <p class="px-4 pb-2 pt-1 text-xs font-semibold text-gray-500">
                            Filter Pencarian
                        </p>

                        <button
                            type="button"
                            @click="filter = 'posts'; showFilter = false"
                            class="flex w-full items-center justify-between px-4 py-3 text-left text-sm transition hover:bg-lavender/40"
                            :class="filter === 'posts' ? 'font-semibold text-navy' : 'text-gray-600'"
                        >
                            <span>Postingan</span>
                            <span x-show="filter === 'posts'" aria-hidden="true" class="text-brand">✓</span>
                        </button>

                        <button
                            type="button"
                            @click="filter = 'people'; showFilter = false"
                            class="flex w-full items-center justify-between px-4 py-3 text-left text-sm transition hover:bg-lavender/40"
                            :class="filter === 'people' ? 'font-semibold text-navy' : 'text-gray-600'"
                        >
                            <span>Profil Mahasiswa</span>
                            <span x-show="filter === 'people'" aria-hidden="true" class="text-brand">✓</span>
                        </button>
                    </div>
                </div>
            </form>
        @else
            <div class="flex-1"></div>
        @endunless

        <div class="ms-auto flex shrink-0 items-center gap-1 sm:gap-2">

            <!-- Notifikasi -->
            @unless ($admin)
                <a
                    href="{{ url('/notifikasi') }}"
                    class="rounded-full p-2 transition hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-brand"
                    aria-label="Notifikasi"
                >
                    <x-icon name="bell" class="h-[29px] w-[29px]" />
                </a>
            @endunless

            <!-- Menu akun -->
            <div class="hidden sm:block">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="flex items-center rounded-full p-1 transition hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-brand"
                            aria-label="Menu akun"
                        >
                            <x-avatar
                                :user="Auth::user()"
                                size="h-[40px] w-[40px] !border-[3px] border-lavender text-sm"
                            />
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="border-b border-gray-100 px-4 py-2">
                            <div class="truncate text-sm font-semibold text-navy-deep">
                                {{ Auth::user()->name }}
                            </div>
                            <div class="truncate text-xs text-gray-500">
                                {{ Auth::user()->email }}
                            </div>
                        </div>

                        @unless ($admin)
                            <x-dropdown-link :href="url('/profil')">
                                Profil Saya
                            </x-dropdown-link>
                        @endunless

                        <x-dropdown-link :href="url('/profile')">
                            Pengaturan Akun
                        </x-dropdown-link>

                        <form method="POST" action="{{ url('/logout') }}">
                            @csrf
                            <x-dropdown-link
                                :href="url('/logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                            >
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Menu HP -->
            <button
                @click="open = !open"
                class="rounded-full p-2 transition hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-brand lg:hidden"
                aria-label="Buka menu"
                :aria-expanded="open"
            >
                <x-icon name="bars" class="h-6 w-6" x-show="!open" />
                <x-icon name="x" class="h-6 w-6" x-show="open" x-cloak />
            </button>
        </div>
    </div>

    <!-- Menu HP -->
    <div
        x-show="open"
        x-cloak
        x-transition
        class="border-t border-white/10 bg-navy lg:hidden"
    >
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
                    <div class="truncate text-sm font-semibold">
                        {{ Auth::user()->name }}
                    </div>
                    <div class="truncate text-xs text-white/70">
                        {{ Auth::user()->email }}
                    </div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="url('/profile')">
                    Pengaturan Akun
                </x-responsive-nav-link>

                <form method="POST" action="{{ url('/logout') }}">
                    @csrf
                    <x-responsive-nav-link
                        :href="url('/logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();"
                    >
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</header>
