<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-navy-deep">Beranda</h2>
        <p class="kub-muted mt-0.5">Temukan tim, info lomba, dan kolaborasi terbaru di kampus.</p>
    </x-slot>

    <div class="mx-auto w-full max-w-3xl space-y-5 px-4 sm:px-6 lg:px-8">
        <x-flash />

        {{-- Buat postingan: pilih kategori Umum, Info Lomba, atau Cari Tim --}}
        <x-komposer-postingan :profil="$profil" />

        {{-- Filter kategori (EFP-PC02) --}}
        <nav class="overflow-x-auto pb-1" aria-label="Filter kategori">
            <div class="kub-filter">
                @foreach (['Semua', 'Umum', 'Info Lomba', 'Cari Tim'] as $kat)
                    <a href="{{ route('dashboard', ['kategori' => $kat]) }}"
                       class="kub-filter-item whitespace-nowrap {{ $kategoriAktif === $kat ? 'aktif' : '' }}"
                       @if ($kategoriAktif === $kat) aria-current="page" @endif>{{ $kat }}</a>
                @endforeach
            </div>
        </nav>

        {{-- Feed --}}
        <div class="space-y-4">
            @forelse ($postingan as $item)
                <div class="kub-masuk" style="animation-delay: {{ min($loop->index, 6) * 60 }}ms">
                    <x-post-card :item="$item" :profil="$profil" />
                </div>
            @empty
                <x-empty-state title="Belum ada postingan" icon="chat">
                    Belum ada postingan untuk kategori "{{ $kategoriAktif }}". Jadilah yang pertama membagikan postingan!
                </x-empty-state>
            @endforelse
        </div>

        @if ($postingan->hasPages())
            <div class="pt-2">{{ $postingan->links() }}</div>
        @endif
    </div>
</x-app-layout>
