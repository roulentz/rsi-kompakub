{{-- Rekomendasi Kandidat (PCT, milik SH): mahasiswa dengan skill yang cocok dengan kebutuhan postingan Cari Tim. --}}
@php
    $dibutuhkan = collect(explode(',', (string) $postingan->tag_skill))->map(fn ($s) => mb_strtolower(trim($s)))->filter();
@endphp
<x-app-layout>
    <div class="space-y-5 px-4 py-6 sm:px-8">
        <x-flash />

        <a href="{{ route('pelamar.index', $postingan->id_postingan) }}" class="inline-flex items-center gap-1 text-xs font-medium text-muted hover:text-navy">
            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 5-5 5 5 5"/></svg>
            Daftar Pelamar
        </a>

        @include('postingan._ringkasan', ['postingan' => $postingan, 'slotTerisi' => $slotTerisi])

        <div>
            <h2 class="text-base font-bold text-navy">Rekomendasi Kandidat</h2>
            <p class="text-xs text-muted">Mahasiswa dengan skill yang sesuai kebutuhan postinganmu.</p>
        </div>

        <div class="space-y-3">
            @forelse ($kandidat as $profil)
                <article class="kub-card flex items-center gap-3">
                    @include('postingan._avatar', ['profil' => $profil])

                    <div class="min-w-0 flex-1">
                        <p class="truncate text-base font-bold text-navy">{{ $profil->user->name }}</p>
                        <p class="truncate text-xs text-muted">
                            {{ $profil->jurusan ?: 'Jurusan belum diisi' }}{{ $profil->angkatan ? ' - '.$profil->angkatan : '' }}
                        </p>
                        <div class="mt-1.5 flex flex-wrap gap-1.5">
                            @foreach ($profil->skills as $skill)
                                <x-chip :tone="$dibutuhkan->contains(mb_strtolower($skill->nama_skill)) ? 'navy' : 'default'">{{ $skill->nama_skill }}</x-chip>
                            @endforeach
                        </div>
                        <p class="mt-1 text-xs text-muted">{{ $profil->skill_cocok }} skill cocok</p>
                    </div>

                    <a href="{{ route('profil.lihat', $profil->id_profil) }}"
                       class="shrink-0 rounded-full bg-brand px-4 py-1 text-xs font-medium text-white transition hover:bg-brand-dark">Lihat Profil</a>
                </article>
            @empty
                <x-empty-state title="Belum ada kandidat yang cocok">
                    Belum ada mahasiswa dengan skill yang sesuai. Kandidat akan muncul saat ada profil yang cocok.
                </x-empty-state>
            @endforelse
        </div>
    </div>
</x-app-layout>
