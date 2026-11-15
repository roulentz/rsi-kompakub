{{--
    Blok "Kelola Proyek" untuk pembuat postingan Cari Tim (PCT, milik SH), dipakai di Detail Postingan (UI02):
    Lihat Pelamar, Rekomendasi Kandidat, dan Tandai Selesai dengan dialog konfirmasi.
--}}
@php
    $jumlahPelamar = $postingan->lamaran->count();
    $menunggu = $postingan->lamaran->where('status', 'Menunggu Konfirmasi')->count();
    $diterima = $postingan->lamaran->where('status', 'Diterima')->count();
    $sudahSelesai = $postingan->status === 'Selesai';
    $bisaSelesai = ! $sudahSelesai && $diterima > 0;
@endphp

<section class="kub-card" aria-labelledby="judul-kelola">
    <h2 id="judul-kelola" class="text-base font-bold text-navy">Kelola Proyek</h2>
    <p class="text-xs text-muted">Hanya kamu sebagai pembuat yang melihat bagian ini.</p>

    <div class="mt-3 flex flex-wrap items-center gap-2">
        <a href="{{ route('pelamar.index', $postingan->id_postingan) }}"
           class="inline-flex items-center gap-2 rounded-full bg-navy px-4 py-1.5 text-sm font-medium text-white transition hover:bg-navy-dark">
            Lihat Pelamar
            <span class="rounded-full bg-lavender px-2 text-xs text-navy">{{ $jumlahPelamar }}</span>
        </a>
        <a href="{{ route('postingan.rekomendasi', $postingan->id_postingan) }}"
           class="rounded-full bg-lavender px-4 py-1.5 text-sm font-medium text-navy transition hover:bg-navy hover:text-white">Rekomendasi Kandidat</a>

        @if ($sudahSelesai)
            <span class="rounded-full bg-navy px-4 py-1.5 text-sm font-medium text-white">Proyek selesai</span>
        @else
            <button type="button" @disabled(! $bisaSelesai)
                    x-data x-on:click="$dispatch('open-modal', 'tandai-selesai')"
                    class="rounded-full bg-brand px-4 py-1.5 text-sm font-medium text-white transition hover:bg-brand-dark disabled:cursor-not-allowed disabled:opacity-50">
                Tandai Selesai
            </button>
        @endif
    </div>

    @if ($menunggu > 0)
        <p class="mt-2 text-xs text-muted">{{ $menunggu }} pelamar masih menunggu keputusanmu.</p>
    @endif
    @if (! $sudahSelesai && $diterima === 0)
        <p class="mt-2 text-xs text-muted">Tandai Selesai aktif setelah ada minimal satu anggota yang diterima.</p>
    @endif
</section>

@if ($bisaSelesai)
    <x-modal name="tandai-selesai" maxWidth="md" focusable>
        <div class="p-6">
            <div class="flex items-start gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-brand/20 text-brand-dark">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="10" cy="10" r="7.5"/><path d="m6.8 10.2 2.2 2.2 4.2-4.4"/></svg>
                </span>
                <div class="min-w-0 flex-1">
                    <h3 class="text-base font-bold text-navy">Tandai Proyek Selesai?</h3>
                    <p class="text-xs text-muted">KompakUB Mahasiswa Brawijaya</p>
                </div>
                <button type="button" x-on:click="$dispatch('close')" class="text-muted hover:text-navy" aria-label="Tutup">
                    <x-icon name="x" class="h-4 w-4" />
                </button>
            </div>

            <dl class="mt-4 divide-y divide-gray-100 rounded-lg border border-gray-100 text-sm">
                <div class="flex justify-between gap-3 px-3 py-2"><dt class="text-muted">Proyek | Lomba</dt><dd class="font-semibold text-navy">{{ $postingan->nama_lomba }}</dd></div>
                <div class="flex justify-between gap-3 px-3 py-2"><dt class="text-muted">Anggota Diterima</dt><dd class="font-semibold text-navy">{{ $diterima }} Mahasiswa</dd></div>
                <div class="flex justify-between gap-3 px-3 py-2"><dt class="text-muted">Status Akhir</dt><dd class="font-semibold text-emerald-600">Selesai</dd></div>
            </dl>

            <p class="mt-3 text-xs text-muted">Riwayat kolaborasi akan dicatat untukmu dan semua anggota yang diterima. Tindakan ini tidak bisa dibatalkan.</p>

            <div class="mt-5 flex justify-end gap-2">
                <button type="button" x-on:click="$dispatch('close')" class="rounded-full border border-gray-300 bg-white px-5 py-1.5 text-sm font-medium text-navy hover:bg-surface">Batal</button>
                <form method="POST" action="{{ route('postingan.selesai', $postingan->id_postingan) }}">
                    @csrf
                    <button type="submit" class="rounded-full bg-brand px-5 py-1.5 text-sm font-medium text-white transition hover:bg-brand-dark">Konfirmasi</button>
                </form>
            </div>
        </div>
    </x-modal>
@endif
