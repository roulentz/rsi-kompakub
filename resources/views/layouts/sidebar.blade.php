{{-- Sidebar kiri (layar lg ke atas). Menu datang dari layouts/app.blade.php lewat $menu. --}}
<aside class="hidden w-[260px] shrink-0 lg:block xl:w-[360px]">
    <div class="sticky top-[85px] flex h-[calc(100vh-85px)] flex-col bg-surface px-2 pt-[25px]">
        <nav class="w-[322px] max-w-full space-y-3" aria-label="Menu utama">
            @foreach ($menu as [$label, $url, $aktif, $ikon])
                <x-sidebar-link :href="$url" :active="$aktif" :icon="$ikon">{{ $label }}</x-sidebar-link>
            @endforeach
        </nav>

        @unless (Auth::user()->role === 'admin')
            {{-- Placeholder: arahkan ke Beranda. Saat form buat postingan jadi modal, ganti href dengan pemicunya. --}}
            <a href="{{ url('/dashboard') }}#buat-postingan"
               class="mx-auto mt-[92px] inline-flex xl:ml-[35px] h-[43px] w-[245px] max-w-full items-center justify-center gap-[13px] rounded-full bg-navy text-lg font-medium text-white shadow-sm transition hover:bg-navy-dark focus:outline-none focus:ring-2 focus:ring-brand xl:text-[21px]">
                <x-icon name="plus" class="h-[18px] w-[18px]" />
                Make A Post
            </a>
        @endunless
    </div>
</aside>
