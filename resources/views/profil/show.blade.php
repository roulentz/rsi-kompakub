@php use Illuminate\Support\Facades\Storage; @endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-navy-deep">{{ $pemilik ? 'Profil Saya' : 'Profil '.$profil->user->name }}</h2>
        <p class="kub-muted mt-0.5">Identitas, portofolio, riwayat kolaborasi, dan rekomendasi dari rekan tim.</p>
    </x-slot>

    @php
        $tabProfil = [
            'semua' => ['Semua', null],
            'postingan' => ['Postingan', $postingan->count()],
            'portofolio' => ['Portofolio', $profil->portofolio->count()],
            'riwayat' => ['Riwayat Kolaborasi', $riwayat->count()],
            'rekomendasi' => ['Rekomendasi', $rekomendasi->count()],
        ];
    @endphp
    <div class="mx-auto w-full max-w-4xl space-y-5 px-4 sm:px-6 lg:px-8"
         x-data="{ tab: ['postingan', 'portofolio', 'riwayat', 'rekomendasi'].includes(location.hash.slice(1)) ? location.hash.slice(1) : @js($errors->has('isi_rekomendasi') ? 'rekomendasi' : 'semua'),
                   pilih(t) { this.tab = t; history.replaceState(null, '', t === 'semua' ? location.pathname : '#' + t); } }">
        <x-flash />

        @if ($pemilik && $profil->status_default)
            <div class="flex items-start gap-3 rounded-xl bg-brand-soft px-4 py-3 text-sm text-navy-deep ring-1 ring-brand/40">
                <x-icon name="pencil" class="mt-0.5 h-5 w-5 shrink-0 text-brand-dark" />
                <p>Profilmu masih kosong. <a href="{{ route('profil.edit') }}" class="font-semibold underline">Lengkapi sekarang</a> supaya mudah ditemukan tim.</p>
            </div>
        @endif

        {{-- Identitas --}}
        <section class="kub-profil-kartu">
            <div class="flex flex-col items-center gap-5 text-center sm:flex-row sm:items-start sm:text-left">
                <div class="kub-avatar-cincin">
                    <x-avatar :user="$profil->user" :foto="$profil->fotoUrl()" size="h-28 w-28 !border-4 text-4xl sm:h-32 sm:w-32" />
                </div>

                <div class="min-w-0 flex-1">
                    <h3 class="text-2xl font-bold text-navy-deep sm:text-3xl">{{ $profil->user->name }}</h3>

                    <p class="mt-1 flex items-start justify-center gap-2 text-sm text-navy sm:justify-start">
                        <x-icon name="academic-cap" class="mt-0.5 h-4 w-4 shrink-0" />
                        <span>{{ $profil->jurusan ?: 'Jurusan belum diisi' }}{{ $profil->angkatan ? " · Angkatan {$profil->angkatan}" : '' }} · Universitas Brawijaya</span>
                    </p>

                    <p class="mt-4 text-sm leading-relaxed text-navy-deep">{{ $profil->bio ?: 'Belum ada bio.' }}</p>

                    @if ($profil->interest)
                        <p class="mt-2 text-sm text-gray-600"><span class="font-semibold text-navy">Minat:</span> {{ $profil->interest }}</p>
                    @endif

                    <div class="mt-3 flex flex-wrap justify-center gap-2 sm:justify-start">
                        @forelse ($profil->skills as $s)
                            <x-chip>{{ $s->nama_skill }}</x-chip>
                        @empty
                            <span class="text-sm text-gray-400">Belum ada skill.</span>
                        @endforelse
                    </div>

                    <dl class="kub-statistik mt-5 flex justify-center gap-5 sm:justify-start">
                        @foreach ([['Proyek Selesai', $riwayat->count()], ['Rekomendasi', $rekomendasi->count()], ['Skill', $profil->skills->count()]] as [$label, $angka])
                            <div class="text-left">
                                <dd class="text-xl font-bold text-navy">{{ $angka }}</dd>
                                <dt class="text-xs text-gray-500">{{ $label }}</dt>
                            </div>
                        @endforeach
                    </dl>

                    <div class="mt-5 flex flex-wrap items-center justify-center gap-2 sm:justify-start">
                        @if ($pemilik)
                            <a href="{{ route('profil.edit') }}" class="kub-btn kub-btn-brand"><x-icon name="pencil" class="h-4 w-4" /> Edit Profil</a>
                        @endif

                        <button type="button" x-data="{ ok: false }"
                                @click="navigator.clipboard.writeText(@js(route('profil.lihat', $profil))); ok = true; setTimeout(() => ok = false, 2000)"
                                class="kub-btn kub-btn-soft">
                            <x-icon name="share" class="h-4 w-4" />
                            <span x-text="ok ? 'Tautan disalin' : 'Bagikan Profil'"></span>
                        </button>

                        @if (! $pemilik && Route::has('dm.mulai'))
                            <a href="{{ route('dm.mulai', $profil->user_id) }}" class="kub-btn kub-btn-soft !px-3" aria-label="Kirim pesan"><x-icon name="chat" class="h-5 w-5" /></a>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        {{-- Tab bagian profil --}}
        <nav class="overflow-x-auto pb-1" aria-label="Bagian profil">
            <div class="kub-filter" role="tablist">
                @foreach ($tabProfil as $kunci => [$label, $jumlah])
                    <button type="button" role="tab" @click="pilih('{{ $kunci }}')" :class="tab === '{{ $kunci }}' ? 'aktif' : ''"
                            :aria-selected="tab === '{{ $kunci }}'" class="kub-filter-item whitespace-nowrap">
                        {{ $label }}@if ($jumlah !== null)<span class="ms-1.5 text-xs opacity-70">{{ $jumlah }}</span>@endif
                    </button>
                @endforeach
            </div>
        </nav>

        {{-- Postingan pemilik profil --}}
        <section aria-labelledby="judul-postingan" x-show="tab === 'semua' || tab === 'postingan'" x-cloak>
            <div class="mb-3 flex items-end justify-between gap-3 px-1">
                <div>
                    <h3 id="judul-postingan" class="kub-heading">Postingan</h3>
                    <p class="text-xs text-gray-500">{{ $pemilik ? 'Yang pernah kamu bagikan di Beranda' : 'Yang pernah dibagikan di Beranda' }}</p>
                </div>
                <span class="kub-chip">{{ $postingan->count() }} postingan</span>
            </div>

            <div class="space-y-4">
                @forelse ($postingan as $item)
                    <x-post-card :item="$item" :profil="$profilSaya" />
                @empty
                    <x-empty-state title="Belum ada postingan" icon="chat">
                        {{ $pemilik ? 'Postingan yang kamu buat di Beranda akan muncul di sini.' : 'Pengguna ini belum membagikan postingan.' }}
                    </x-empty-state>
                @endforelse
            </div>
        </section>

        {{-- Portofolio --}}
        <section class="kub-card" x-show="tab === 'semua' || tab === 'portofolio'" x-cloak>
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h3 class="kub-heading">Portofolio</h3>
                    <p class="text-xs text-gray-500">Dokumentasi hasil karya</p>
                </div>
                @if ($pemilik)
                    <a href="{{ route('portofolio.edit') }}" class="kub-btn kub-btn-soft kub-btn-sm"><x-icon name="pencil" class="h-4 w-4" /> Kelola</a>
                @endif
            </div>

            @php $slides = $profil->portofolio->map(fn ($p) => ['src' => Storage::disk('public')->url($p->konten), 'link' => $p->tautan_eksternal])->values(); @endphp

            @if ($slides->isEmpty())
                <x-empty-state title="Belum ada portofolio" icon="image" class="!shadow-none !ring-0 bg-surface-tint/60 py-8" />
            @else
                <div x-data="{ i: 0, s: @js($slides) }">
                    <div class="relative overflow-hidden rounded-xl bg-surface-tint ring-1 ring-navy/10">
                        <img :src="s[i].src" :alt="'Halaman ' + (i + 1)" class="mx-auto max-h-[28rem] w-full object-contain">
                        <button type="button" @click="i = (i - 1 + s.length) % s.length" aria-label="Halaman sebelumnya"
                                class="absolute left-3 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-navy shadow-md hover:bg-white">
                            <x-icon name="arrow-left" class="h-4 w-4" />
                        </button>
                        <button type="button" @click="i = (i + 1) % s.length" aria-label="Halaman berikutnya"
                                class="absolute right-3 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-navy shadow-md hover:bg-white">
                            <x-icon name="arrow-right" class="h-4 w-4" />
                        </button>
                        <span class="absolute bottom-3 right-3 rounded-full bg-navy-deep/80 px-3 py-1 text-xs font-medium text-white"><span x-text="i + 1"></span> / <span x-text="s.length"></span></span>
                    </div>

                    <div class="mt-3 flex gap-2 overflow-x-auto pb-1">
                        <template x-for="(sl, n) in s" :key="n">
                            <button type="button" @click="i = n" :aria-label="'Buka halaman ' + (n + 1)" class="shrink-0 overflow-hidden rounded-lg transition"
                                    :class="i === n ? 'ring-2 ring-brand' : 'opacity-60 hover:opacity-100'">
                                <img :src="sl.src" alt="" class="h-14 w-20 object-cover">
                            </button>
                        </template>
                    </div>

                    <template x-if="s[i].link">
                        <a :href="s[i].link" target="_blank" rel="noopener" class="kub-btn kub-btn-navy kub-btn-sm mt-3">
                            <x-icon name="link" class="h-4 w-4" /> Lihat Prototype / Live Demo
                        </a>
                    </template>
                </div>
            @endif
        </section>

        {{-- Riwayat kolaborasi --}}
        <section class="kub-card" x-show="tab === 'semua' || tab === 'riwayat'" x-cloak>
            <div class="mb-4 flex items-start justify-between gap-3">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-surface-tint text-navy"><x-icon name="users" class="h-5 w-5" /></span>
                    <div>
                        <h3 class="kub-heading">Riwayat Kolaborasi</h3>
                        <p class="text-xs text-gray-500">Jejak kontribusi nyata pada kompetisi dan proyek</p>
                    </div>
                </div>
                <span class="kub-chip shrink-0">{{ $riwayat->count() }} Kolaborasi Selesai</span>
            </div>

            <div class="space-y-3">
                @forelse ($riwayat as $r)
                    @php
                        $tim = $timPerProyek->get($r->id_postingan, collect());
                        $jumlahTim = max($tim->count(), 1);
                    @endphp
                    <article class="flex flex-col gap-3 rounded-2xl bg-surface-tint/70 p-4 ring-1 ring-navy/5 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            <p class="flex items-center gap-1.5 text-sm font-semibold text-navy-deep">
                                <x-icon name="check-circle" class="h-4 w-4 shrink-0 text-brand-dark" />
                                <span class="truncate">{{ $r->judul_proyek }}</span>
                            </p>
                            <p class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
                                @if ($r->peran)<span class="inline-flex items-center gap-1"><x-icon name="user" class="h-3.5 w-3.5" /> Peran: <b class="font-semibold text-navy">{{ $r->peran }}</b></span>@endif
                                <span class="inline-flex items-center gap-1"><x-icon name="calendar" class="h-3.5 w-3.5" /> Selesai: {{ $r->tanggal_selesai->translatedFormat('M Y') }}</span>
                            </p>
                        </div>

                        <div class="flex shrink-0 items-center gap-2">
                            <div class="flex -space-x-2">
                                @foreach ($tim->take(3) as $anggota)
                                    <a href="{{ route('profil.lihat', $anggota->profil) }}" title="{{ $anggota->profil->user->name }}" class="transition hover:z-10 hover:-translate-y-0.5">
                                        <x-avatar :user="$anggota->profil->user" :foto="$anggota->profil->fotoUrl()" size="h-8 w-8 text-[10px] !border-2" />
                                    </a>
                                @endforeach
                                @if ($tim->count() > 3)
                                    <span class="relative z-0 flex h-8 w-8 items-center justify-center rounded-full border-2 border-white bg-lavender text-[10px] font-semibold text-navy">+{{ $tim->count() - 3 }}</span>
                                @endif
                            </div>
                            <span class="text-xs text-gray-500">{{ $jumlahTim }} Mahasiswa</span>
                        </div>
                    </article>
                @empty
                    <x-empty-state title="Belum ada riwayat kolaborasi" icon="trophy" class="!shadow-none !ring-0 bg-surface-tint/60 py-8" />
                @endforelse
            </div>
        </section>

        {{-- Rekomendasi --}}
        <section class="kub-card" x-show="tab === 'semua' || tab === 'rekomendasi'" x-cloak
                 x-data="{ tulis: {{ $errors->has('isi_rekomendasi') ? 'true' : 'false' }} }">
            <div class="mb-4 flex items-start justify-between gap-3">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-soft text-brand-dark"><x-icon name="chat" class="h-5 w-5" /></span>
                    <div>
                        <h3 class="kub-heading">Rekomendasi Rekan Tim</h3>
                        <p class="text-xs text-gray-500">Ulasan kejujuran dan etos kerja dari rekan lomba</p>
                    </div>
                </div>
                @if ($bolehRekomendasi)
                    <button type="button" @click="tulis = true" class="kub-btn kub-btn-soft kub-btn-sm shrink-0"><x-icon name="pencil" class="h-4 w-4" /> Beri Ulasan</button>
                @endif
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                @forelse ($rekomendasi as $rk)
                    @php $konteks = $konteksRekomendasi->get($rk->id_profil_pemberi); @endphp
                    <article class="flex flex-col rounded-2xl bg-surface-tint/70 p-4 ring-1 ring-navy/5">
                        <div class="flex items-start gap-3">
                            <x-avatar :user="$rk->pemberi->user" :foto="$rk->pemberi->fotoUrl()" size="h-10 w-10 text-xs" />
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('profil.lihat', $rk->pemberi) }}" class="block truncate text-sm font-semibold text-navy-deep hover:underline">{{ $rk->pemberi->user->name }}</a>
                                <p class="truncate text-xs text-gray-500">{{ $rk->pemberi->jurusan ?: 'Mahasiswa' }}{{ $rk->pemberi->angkatan ? " · '".substr($rk->pemberi->angkatan, -2) : '' }}</p>
                            </div>

                            @if ($pemilik && $rk->status_laporan === 'tidak_dilaporkan')
                                <div class="relative" x-data="{ menu: false }" @click.outside="menu = false" @keydown.escape.window="menu = false">
                                    <button type="button" @click="menu = !menu" class="rounded-full p-1 text-gray-400 hover:bg-white hover:text-navy" aria-label="Menu rekomendasi">
                                        <x-icon name="ellipsis-vertical" class="h-5 w-5" />
                                    </button>
                                    <div x-show="menu" x-cloak x-transition class="absolute right-0 z-10 mt-1 w-36 rounded-xl bg-white py-1 shadow-angkat ring-1 ring-navy/10">
                                        <form method="POST" action="{{ route('rekomendasi.laporkan', $rk) }}" onsubmit="return confirm('Laporkan rekomendasi ini ke admin?')">
                                            @csrf
                                            <button class="block w-full px-4 py-2 text-left text-xs font-medium text-red-600 hover:bg-red-50">Laporkan</button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <p class="mt-3 flex-1 text-sm italic leading-relaxed text-navy-deep">&ldquo;{{ $rk->isi_rekomendasi }}&rdquo;</p>

                        <div class="mt-4 flex items-center justify-between gap-2 text-xs">
                            @if ($konteks)
                                <span class="inline-flex min-w-0 items-center gap-1 font-medium text-brand-dark"><x-icon name="check-circle" class="h-4 w-4 shrink-0" /><span class="truncate">Partner di {{ $konteks }}</span></span>
                            @else
                                <span></span>
                            @endif
                            <span class="shrink-0 text-gray-500">{{ $rk->tanggal_dikirim->translatedFormat('d M Y') }}</span>
                        </div>

                        @if ($pemilik && $rk->status_laporan === 'dilaporkan')
                            <x-chip tone="brand" class="mt-3 self-start">Sedang ditinjau admin</x-chip>
                        @endif
                    </article>
                @empty
                    <div class="sm:col-span-2">
                        <x-empty-state title="Belum ada rekomendasi" icon="chat" class="!shadow-none !ring-0 bg-surface-tint/60 py-8" />
                    </div>
                @endforelse
            </div>

            @if ($bolehRekomendasi)
                <div x-show="tulis" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="tulis = false">
                    <div class="absolute inset-0 bg-navy-deep/60 backdrop-blur-sm" @click="tulis = false"></div>

                    <form method="POST" action="{{ route('rekomendasi.store', $profil) }}" x-data="{ n: {{ mb_strlen(old('isi_rekomendasi', '')) }} }"
                          class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-angkat">
                        @csrf
                        <h3 class="text-xl font-bold text-navy-deep">Tulis Rekomendasi untuk {{ $profil->user->name }}</h3>
                        <p class="mt-1 text-xs text-gray-500">Ceritakan pengalaman bekerja sama dalam proyek yang sudah selesai.</p>

                        <textarea name="isi_rekomendasi" rows="5" maxlength="500" required class="kub-input mt-4" @input="n = $event.target.value.length"
                                  placeholder="Tulis rekomendasi (maks. 500 karakter)">{{ old('isi_rekomendasi') }}</textarea>
                        <div class="mt-1 flex justify-between">
                            <x-input-error :messages="$errors->get('isi_rekomendasi')" />
                            <span class="ms-auto text-xs text-gray-400"><span x-text="n"></span> / 500 karakter</span>
                        </div>

                        <div class="mt-5 flex justify-end gap-2">
                            <button type="button" @click="tulis = false" class="kub-btn kub-btn-soft kub-btn-sm">Lewati</button>
                            <x-primary-button class="kub-btn-sm">Kirim Rekomendasi</x-primary-button>
                        </div>
                    </form>
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
