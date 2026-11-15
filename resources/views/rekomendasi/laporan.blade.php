<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-navy-deep">Laporan Rekomendasi</h2>
        <p class="kub-muted mt-0.5">Tinjau rekomendasi yang dilaporkan oleh penerimanya.</p>
    </x-slot>

    <div class="mx-auto w-full max-w-4xl space-y-4 px-4 sm:px-6 lg:px-8">
        <x-flash />

        @forelse ($laporan as $l)
            <article class="kub-card space-y-3">
                <div class="flex items-center gap-3">
                    <x-avatar :user="$l->pemberi->user" :foto="$l->pemberi->fotoUrl()" size="h-10 w-10 text-xs" />
                    <p class="text-sm text-gray-500">
                        Dari <strong class="text-navy-deep">{{ $l->pemberi->user->name }}</strong> untuk <strong class="text-navy-deep">{{ $l->penerima->user->name }}</strong>
                        &bull; {{ $l->tanggal_dikirim->translatedFormat('d M Y') }}
                    </p>
                </div>

                <p class="kub-soft text-sm text-navy-deep">“{{ $l->isi_rekomendasi }}”</p>

                <div class="flex gap-2">
                    <form method="POST" action="{{ route('admin.rekomendasi.tinjau', $l) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="aksi" value="pertahankan">
                        <button class="kub-btn kub-btn-outline kub-btn-sm"><x-icon name="check" class="h-4 w-4" /> Pertahankan</button>
                    </form>
                    <form method="POST" action="{{ route('admin.rekomendasi.tinjau', $l) }}" onsubmit="return confirm('Hapus rekomendasi ini?')">
                        @csrf @method('PATCH')
                        <input type="hidden" name="aksi" value="hapus">
                        <button class="kub-btn kub-btn-danger kub-btn-sm"><x-icon name="trash" class="h-4 w-4" /> Hapus</button>
                    </form>
                </div>
            </article>
        @empty
            <x-empty-state title="Tidak ada laporan yang menunggu" icon="shield" />
        @endforelse
    </div>
</x-app-layout>
