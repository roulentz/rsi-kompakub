{{-- UI03 Daftar Pelamar (PCT, milik SH): semua pelamar sebuah postingan Cari Tim, khusus pembuat. --}}
<x-app-layout>
    <div class="space-y-5 px-4 py-6 sm:px-8">
        <x-flash />

        <a href="{{ route('notifikasi.index') }}" class="inline-flex items-center gap-1 text-xs font-medium text-muted hover:text-navy">
            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 5-5 5 5 5"/></svg>
            Permintaan Kolaborasi
        </a>

        @include('postingan._ringkasan', ['postingan' => $postingan, 'slotTerisi' => $slotTerisi, 'pill' => $daftarLamaran->count().' Pelamar'])

        <div class="flex items-center justify-between gap-3">
            <h2 class="text-base font-bold text-navy">Daftar Pelamar</h2>
            @if ($postingan->kategori === 'Cari Tim')
                <a href="{{ route('postingan.rekomendasi', $postingan->id_postingan) }}" class="kub-link text-xs font-medium">Lihat rekomendasi kandidat</a>
            @endif
        </div>

        <div class="space-y-3">
            @forelse ($daftarLamaran as $lamaran)
                @php $pelamar = $lamaran->pelamar; @endphp
                <article class="kub-card flex items-center gap-3">
                    @include('postingan._avatar', ['profil' => $pelamar])

                    <div class="min-w-0 flex-1">
                        <p class="truncate text-base font-bold text-navy">{{ $pelamar->user->name }}</p>
                        <p class="truncate text-xs text-muted">
                            {{ $pelamar->jurusan ?: 'Jurusan belum diisi' }}{{ $pelamar->angkatan ? ' - '.$pelamar->angkatan : '' }}
                        </p>
                        @if ($pelamar->skills->isNotEmpty())
                            <div class="mt-1.5 flex flex-wrap gap-1.5">
                                @foreach ($pelamar->skills as $skill)
                                    <x-chip>{{ $skill->nama_skill }}</x-chip>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="flex shrink-0 flex-col items-end gap-2">
                        <a href="{{ route('pelamar.show', $lamaran->id_lamaran) }}"
                           class="rounded-full bg-brand px-4 py-1 text-xs font-medium text-white transition hover:bg-brand-dark">Lihat Profil</a>
                        @include('pelamar._status', ['status' => $lamaran->status])
                    </div>
                </article>
            @empty
                <x-empty-state title="Belum ada pelamar yang masuk">
                    Pelamar akan muncul di sini saat ada yang mengajukan diri ke postinganmu.
                </x-empty-state>
            @endforelse
        </div>
    </div>
</x-app-layout>
