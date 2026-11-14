{{-- Ringkasan postingan Cari Tim (judul, tanggal, pill, bar slot). Dipakai Daftar Pelamar, Profil Pelamar, dan Rekomendasi. --}}
<article class="kub-card">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <h1 class="truncate text-xl font-bold text-navy">{{ $postingan->nama_lomba }}</h1>
            <p class="text-xs text-muted">
                Pelaksanaan {{ $postingan->waktu_perlombaan ? $postingan->waktu_perlombaan->locale('id')->translatedFormat('d F Y') : '-' }}
            </p>
        </div>
        @if (! empty($pill))
            <span class="shrink-0 rounded-full bg-lavender px-3 py-1 text-xs font-medium text-navy">{{ $pill }}</span>
        @endif
    </div>

    <div class="mt-3 rounded-lg bg-white p-2">
        <div class="rounded-lg bg-navy-dark px-3 py-2 text-white">
            <div class="flex items-center justify-between text-xs">
                <span>Slot Anggota</span>
                <span>{{ $slotTerisi }}/{{ $postingan->jumlah_anggota }}</span>
            </div>
            <div class="mt-2 flex gap-1.5" aria-hidden="true">
                @for ($i = 1; $i <= $postingan->jumlah_anggota; $i++)
                    <i class="h-2.5 flex-1 rounded-sm {{ $i <= $slotTerisi ? 'bg-brand' : 'bg-lavender' }}"></i>
                @endfor
            </div>
        </div>
    </div>
</article>
