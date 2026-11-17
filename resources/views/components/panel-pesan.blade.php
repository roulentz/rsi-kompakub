
@php
    $saya = auth()->id();

    $daftarPercakapan = $saya
        ? \App\Models\Percakapan::with([
            'pengirim.profil',
            'penerima.profil',
            'pesan',
        ])
            ->where(function ($query) use ($saya) {
                $query->where('pengirim_id', $saya)
                    ->orWhere('penerima_id', $saya);
            })
            ->latest('updated_at')
            ->limit(15)
            ->get()
        : collect();
@endphp

<section aria-labelledby="panel-pesan-judul" class="kub-card !p-4">
    <h2 id="panel-pesan-judul"
        class="mb-4 flex items-center gap-2.5 text-base font-semibold text-navy">
        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-surface-tint">
            <x-icon name="chat" class="h-[18px] w-[18px]" />
        </span>
        Pesan
    </h2>

    <div class="max-h-[65vh] space-y-2 overflow-y-auto">
        @forelse ($daftarPercakapan as $percakapan)
            @php
                $lawan = $percakapan->pengirim_id == $saya
                    ? $percakapan->penerima
                    : $percakapan->pengirim;

                $terakhir = $percakapan->pesan
                    ->sortByDesc('waktu_kirim')
                    ->first();

                $aktif = request()->routeIs('dm.show')
                    && request()->route('percakapan')?->id == $percakapan->id;
            @endphp

            @if ($lawan)
                <a href="{{ route('dm.show', $percakapan) }}"
                   class="flex items-center gap-3 rounded-xl border px-3 py-3 transition hover:bg-surface-tint {{ $aktif ? 'border-brand bg-surface-tint' : 'border-transparent bg-gray-50' }}">

                    <x-avatar
                        :user="$lawan"
                        :foto="$lawan->profil?->fotoUrl()"
                        size="h-11 w-11 text-sm"
                    />

                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-navy-deep">
                            {{ $lawan->name }}
                        </p>

                        <p class="mt-0.5 truncate text-xs text-gray-500">
                            @if ($terakhir)
                                {{ $terakhir->pengirim_id == $saya ? 'Anda: ' : '' }}{{ $terakhir->isi_pesan }}
                            @else
                                Belum ada pesan
                            @endif
                        </p>
                    </div>
                </a>
            @endif
        @empty
            <p class="flex min-h-[55px] items-center justify-center rounded-xl bg-surface-tint px-3 text-center text-sm text-muted">
                Belum ada percakapan.
            </p>
        @endforelse
    </div>
</section>
