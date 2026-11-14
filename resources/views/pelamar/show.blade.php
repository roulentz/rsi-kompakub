{{-- UI04 Profil Lengkap Pelamar (PCT, milik SH): profil, keahlian, portofolio, dan tombol Terima / Tolak. --}}
@php
    $aman = fn ($tautan) => $tautan && \Illuminate\Support\Str::startsWith($tautan, ['http://', 'https://']);
    $slideData = $portofolio->map(fn ($s) => [
        'konten' => $s->konten,
        'tautan' => $aman($s->tautan_eksternal) ? $s->tautan_eksternal : null,
    ])->values();
    $penuh = $slotTerisi >= $postingan->jumlah_anggota;
@endphp
<x-app-layout>
    <div class="space-y-5 px-4 py-6 sm:px-8">
        @if (session('status'))
            <div x-data="{ buka: true }" x-show="buka" class="kub-card flex items-start gap-3" role="status">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand text-white">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m4 10 4 4 8-8"/></svg>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-navy">{{ session('status') }}</p>
                    <p class="text-xs text-muted">Notifikasi status seleksi telah dikirimkan secara otomatis ke pelamar.</p>
                </div>
                <button type="button" @click="buka = false" class="text-muted hover:text-navy" aria-label="Tutup">
                    <x-icon name="x" class="h-4 w-4" />
                </button>
            </div>
        @endif

        @error('keputusan')
            <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700" role="alert">{{ $message }}</div>
        @enderror

        <a href="{{ route('pelamar.index', $postingan->id_postingan) }}" class="inline-flex items-center gap-1 text-xs font-medium text-muted hover:text-navy">
            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 5-5 5 5 5"/></svg>
            Daftar Pelamar · {{ $postingan->nama_lomba }}
        </a>

        {{-- Profil --}}
        <article class="kub-card">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                @include('postingan._avatar', ['profil' => $profil, 'ukuran' => 'h-24 w-24 text-2xl'])

                <div class="min-w-0 flex-1">
                    <h1 class="text-2xl font-bold text-navy">{{ $profil->user->name }}</h1>
                    <p class="mt-0.5 text-xs text-muted">
                        {{ $profil->jurusan ?: 'Jurusan belum diisi' }}{{ $profil->angkatan ? ' • Angkatan '.$profil->angkatan : '' }}
                    </p>
                    <p class="mt-3 rounded-lg bg-white p-3 text-sm text-navy">{{ $profil->bio ?: 'Bio belum diisi.' }}</p>
                </div>
            </div>

            <div class="mt-4">
                <p class="text-sm font-bold text-navy">Keahlian</p>
                <div class="mt-2 flex flex-wrap gap-1.5">
                    @forelse ($profil->skills as $skill)
                        <x-chip>{{ $skill->nama_skill }}</x-chip>
                    @empty
                        <span class="text-xs text-muted">Belum ada skill yang dipilih.</span>
                    @endforelse
                </div>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-2">
                @if ($lamaran->status === 'Menunggu Konfirmasi')
                    <form method="POST" action="{{ route('pelamar.terima', $lamaran->id_lamaran) }}">
                        @csrf
                        <button type="submit" @disabled($penuh) @if ($penuh) title="Slot anggota sudah penuh" @endif
                                class="inline-flex items-center gap-1.5 rounded-full bg-brand px-5 py-1.5 text-sm font-medium text-white transition hover:bg-brand-dark disabled:cursor-not-allowed disabled:opacity-50">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m4 10 4 4 8-8"/></svg>
                            Terima
                        </button>
                    </form>
                    <form method="POST" action="{{ route('pelamar.tolak', $lamaran->id_lamaran) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-full bg-lavender px-5 py-1.5 text-sm font-medium text-navy transition hover:bg-navy hover:text-white">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="m5 5 10 10M15 5 5 15"/></svg>
                            Tolak
                        </button>
                    </form>
                    @if ($penuh)
                        <span class="text-xs text-muted">Slot anggota sudah penuh, pelamar ini hanya bisa ditolak.</span>
                    @endif
                @else
                    @include('pelamar._status', ['status' => $lamaran->status])
                    <span class="text-xs text-muted">Keputusan sudah disimpan dan tidak bisa diubah.</span>
                @endif
            </div>
        </article>

        {{-- Portofolio --}}
        <article class="kub-card" x-data="{ i: 0, slide: @js($slideData) }">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-navy">Portofolio</h2>
                    <p class="text-xs text-muted">Dokumentasi hasil karya</p>
                </div>
                <template x-if="slide.length > 0">
                    <div class="flex shrink-0 items-center gap-2">
                        <span class="rounded-full bg-lavender px-3 py-1 text-xs font-medium text-navy" x-text="'Slide ' + (i + 1) + ' dari ' + slide.length"></span>
                        <button type="button" @click="i = (i - 1 + slide.length) % slide.length" class="flex h-7 w-7 items-center justify-center rounded-full bg-lavender text-navy hover:bg-navy hover:text-white" aria-label="Slide sebelumnya">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 5-5 5 5 5"/></svg>
                        </button>
                        <button type="button" @click="i = (i + 1) % slide.length" class="flex h-7 w-7 items-center justify-center rounded-full bg-lavender text-navy hover:bg-navy hover:text-white" aria-label="Slide berikutnya">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m8 5 5 5-5 5"/></svg>
                        </button>
                    </div>
                </template>
            </div>

            <template x-if="slide.length === 0">
                <p class="mt-4 rounded-lg bg-white p-6 text-center text-sm text-muted">Pelamar belum menambahkan portofolio.</p>
            </template>

            <template x-if="slide.length > 0">
                <div>
                    <template x-if="slide[i].tautan">
                        <a :href="slide[i].tautan" target="_blank" rel="noopener noreferrer"
                           class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-navy px-4 py-1 text-xs font-medium text-white hover:bg-navy-dark">
                            Lihat Prototype / Live Demo
                        </a>
                    </template>

                    <div class="mt-3 flex min-h-[220px] items-center justify-center rounded-lg bg-lavender p-6 text-center">
                        <p class="max-w-md whitespace-pre-line text-base font-semibold text-navy" x-text="slide[i].konten"></p>
                    </div>

                    <div class="mt-3 flex flex-wrap gap-1.5" role="tablist" aria-label="Pilih slide">
                        <template x-for="(s, n) in slide" :key="n">
                            <button type="button" @click="i = n" role="tab" :aria-selected="i === n"
                                    :class="i === n ? 'border-brand text-navy' : 'border-transparent text-muted'"
                                    class="rounded-lg border-2 bg-white px-3 py-1 text-xs font-medium"
                                    x-text="String(n + 1).padStart(2, '0')"></button>
                        </template>
                    </div>
                </div>
            </template>
        </article>
    </div>
</x-app-layout>
