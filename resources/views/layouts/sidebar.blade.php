{{-- Sidebar kiri (layar lg ke atas), menempel ke tepi kiri layar. Menu datang dari layouts/app.blade.php lewat $menu. --}}
<aside class="hidden w-[260px] shrink-0 lg:block xl:w-[320px]">
    <div class="sticky top-[85px] flex h-[calc(100vh-85px)] flex-col overflow-y-auto border-r border-navy/5 bg-white/60 px-5 backdrop-blur-md pb-8 pt-7">
        <nav class="space-y-2" aria-label="Menu utama">
            @foreach ($menu as [$label, $url, $aktif, $ikon])
                <x-sidebar-link :href="$url" :active="$aktif" :icon="$ikon">{{ $label }}</x-sidebar-link>
            @endforeach
        </nav>

        @unless (Auth::user()->role === 'admin')
            {{-- Membuka formulir buat postingan di Beranda --}}
            <a href="{{ url('/dashboard') }}#buat-postingan"
               class="kub-btn kub-btn-navy mt-10 h-12 w-full text-base">
                <x-icon name="plus" class="h-5 w-5" />
                Make A Post
            </a>
        @endunless
    </div>
</aside>
