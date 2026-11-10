{{-- Sidebar kiri (layar lg ke atas). Menu datang dari layouts/app.blade.php lewat $menu. --}}
<aside class="hidden w-60 shrink-0 lg:block">
    <div class="sticky top-14 flex h-[calc(100vh-3.5rem)] flex-col bg-surface p-4">
        <nav class="space-y-1.5" aria-label="Menu utama">
            @foreach ($menu as [$label, $url, $aktif, $ikon])
                <x-sidebar-link :href="$url" :active="$aktif" :icon="$ikon">{{ $label }}</x-sidebar-link>
            @endforeach
        </nav>

        @unless (Auth::user()->role === 'admin')
            {{-- Placeholder: arahkan ke Beranda. Saat form buat postingan jadi modal, ganti href dengan pemicunya. --}}
            <a href="{{ url('/dashboard') }}#buat-postingan"
               class="mx-auto mt-8 inline-flex items-center gap-2 rounded-full bg-navy px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-navy-dark focus:outline-none focus:ring-2 focus:ring-navy focus:ring-offset-2">
                <x-icon name="plus" class="h-4 w-4" />
                Make A Post
            </a>
        @endunless
    </div>
</aside>
