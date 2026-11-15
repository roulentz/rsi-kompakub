{{--
    Detail Postingan (UI02). File ini milik MZ; diganti SH karena versi sebelumnya error 500
    ("Undefined variable $post": masih memakai variabel lama judul/isi/user).
    Data dari BerandaController@show: $postingan (+pembuat.user, pembuat.skills, lamaran), $profil, $sudahMelamar, $statusLamaran.
--}}
@php
    $pembuat = $postingan->pembuat;
    $cariTim = $postingan->kategori === 'Cari Tim';
    $milikSaya = $profil && $profil->id_profil === $postingan->id_pembuat;
    $bisaLamar = $cariTim && $postingan->status === 'Aktif' && $profil && ! $milikSaya && ! $sudahMelamar;
    $skillDibutuhkan = collect(explode(',', (string) $postingan->tag_skill))->map(fn ($s) => trim($s))->filter()->values();
    $slotTerisi = $postingan->slotTerisi();
    $tanggal = $postingan->waktu_perlombaan?->locale('id')->translatedFormat('d F Y');
@endphp
<x-app-layout>
    <div class="space-y-5 px-4 py-6 sm:px-8">
        <x-flash />

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700" role="alert">
                @foreach ($errors->all() as $pesan)
                    <p>{{ $pesan }}</p>
                @endforeach
            </div>
        @endif

        <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-1 text-xs font-medium text-muted hover:text-navy">
            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 5-5 5 5 5"/></svg>
            Beranda
        </a>

        <article class="kub-card">
            <div class="flex items-start gap-3">
                @include('postingan._avatar', ['profil' => $pembuat])
                <div class="min-w-0 flex-1">
                    <p class="truncate text-base font-bold text-navy">{{ $pembuat->user->name }}</p>
                    <p class="truncate text-xs text-muted">
                        {{ $pembuat->jurusan ?: 'Jurusan belum diisi' }}{{ $pembuat->angkatan ? ' - '.$pembuat->angkatan : '' }}
                        · {{ $postingan->created_at?->locale('id')->diffForHumans() }}
                    </p>
                </div>
                <x-chip tone="brand">{{ $postingan->kategori }}</x-chip>
            </div>

            @if ($postingan->nama_lomba)
                <h1 class="mt-3 text-xl font-bold text-navy">{{ $postingan->nama_lomba }}</h1>

                <div class="mt-3 space-y-2 rounded-lg bg-white p-2">
                    @if ($tanggal)
                        <div class="kub-info">
                            <p class="opacity-80">Tanggal</p>
                            <p class="mt-0.5 text-sm font-semibold">{{ $tanggal }}</p>
                        </div>
                    @endif

                    @if ($cariTim)
                        <div class="kub-info">
                            <div class="flex items-center justify-between">
                                <span>Slot Anggota</span>
                                <span>{{ $slotTerisi }}/{{ $postingan->jumlah_anggota }}</span>
                            </div>
                            <div class="mt-2 flex gap-1.5" aria-hidden="true">
                                @for ($i = 1; $i <= $postingan->jumlah_anggota; $i++)
                                    <i class="h-2.5 flex-1 rounded-sm {{ $i <= $slotTerisi ? 'bg-brand' : 'bg-lavender' }}"></i>
                                @endfor
                            </div>
                        </div>

                        @if ($skillDibutuhkan->isNotEmpty())
                            <div class="kub-info">
                                <p class="opacity-80">Skill Required</p>
                                <div class="mt-1 flex flex-wrap gap-1.5">
                                    @foreach ($skillDibutuhkan as $skill)
                                        <span class="rounded-full bg-lavender px-2.5 py-0.5 text-xs font-medium text-navy">{{ $skill }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            @endif

            @if ($postingan->deskripsi)
                <p class="mt-3 whitespace-pre-line rounded-lg bg-white p-3 text-sm text-navy">{{ $postingan->deskripsi }}</p>
            @endif

            @if ($postingan->foto)
                <img src="{{ asset('storage/'.$postingan->foto) }}" alt="Foto postingan" class="mt-3 max-h-80 w-full rounded-lg object-cover">
            @endif

            <div class="mt-4 flex flex-wrap items-center gap-2">
                @if ($bisaLamar)
                    <form method="POST" action="{{ route('lamaran.store', $postingan->id_postingan) }}">
                        @csrf
                        <button type="submit" class="rounded-full bg-brand px-5 py-1.5 text-sm font-medium text-white transition hover:bg-brand-dark">Apply</button>
                    </form>
                @elseif ($sudahMelamar)
                    <span class="text-xs text-muted">Lamaranmu:</span>
                    @include('pelamar._status', ['status' => $statusLamaran])
                @elseif ($cariTim && $postingan->status === 'Selesai')
                    <span class="rounded-full bg-navy px-3 py-1 text-xs font-medium text-white">Proyek selesai</span>
                @endif

                @unless ($milikSaya)
                    <button type="button" disabled title="Fitur pesan menyusul" class="rounded-full bg-brand/50 px-5 py-1.5 text-sm font-medium text-white opacity-70">Kirim Pesan</button>
                @endunless
            </div>
        </article>

        @if ($milikSaya && $cariTim)
            @include('postingan._kelola')
        @endif
    </div>
</x-app-layout>
