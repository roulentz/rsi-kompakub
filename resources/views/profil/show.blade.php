@php use Illuminate\Support\Facades\Storage; @endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-navy-deep">{{ $pemilik ? 'Profil Saya' : 'Profil '.$profil->user->name }}</h2>
        <p class="kub-muted mt-0.5">Identitas, portofolio, riwayat kolaborasi, dan rekomendasi dari rekan tim.</p>
    </x-slot>

    <div class="mx-auto w-full max-w-4xl space-y-5 px-4 sm:px-6 lg:px-8">
        <x-flash />

        @if ($pemilik && $profil->status_default)
            <div class="flex items-start gap-3 rounded-xl bg-brand-soft px-4 py-3 text-sm text-navy-deep ring-1 ring-brand/40">
                <x-icon name="pencil" class="mt-0.5 h-5 w-5 shrink-0 text-brand-dark" />
                <p>Profilmu masih kosong. <a href="{{ route('profil.edit') }}" class="font-semibold underline">Lengkapi sekarang</a> supaya mudah ditemukan tim.</p>
            </div>
        @endif

        {{-- Identitas --}}
        <section class="kub-card !p-0 overflow-hidden">
            <div class="kub-hero h-28 sm:h-36"></div>

            <div class="px-5 pb-6 sm:px-8">
                <div class="-mt-12 flex flex-col gap-4 sm:-mt-14 sm:flex-row sm:items-end">
                    <x-avatar :user="$profil->user" :foto="$profil->fotoUrl()" size="h-24 w-24 !border-4 text-3xl shadow-angkat sm:h-28 sm:w-28" />

                    <div class="min-w-0 flex-1 sm:pb-1">
                        <h3 class="truncate text-2xl font-bold text-navy-deep">{{ $profil->user->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $profil->jurusan ?: 'Jurusan belum diisi' }}{{ $profil->angkatan ? ' · Angkatan '.$profil->angkatan : '' }}</p>
                    </div>

                    <div class="flex flex-wrap gap-2 sm:pb-1">
                        @if ($pemilik)
                            <a href="{{ route('profil.edit') }}" class="kub-btn kub-btn-brand kub-btn-sm"><x-icon name="pencil" class="h-4 w-4" /> Edit Profil</a>
                        @elseif (Route::has('dm.mulai'))
                            <a href="{{ route('dm.mulai', $profil->id_profil) }}" class="kub-btn kub-btn-navy kub-btn-sm"><x-icon name="chat" class="h-4 w-4" /> Kirim Pesan</a>
                        @endif
                        <button type="button" x-data="{ ok: false }"
                                @click="navigator.clipboard.writeText(@js(route('profil.lihat', $profil))); ok = true; setTimeout(() => ok = false, 2000)"
                                class="kub-btn kub-btn-outline kub-btn-sm">
                            <x-icon name="share" class="h-4 w-4" />
                            <span x-text="ok ? 'Tautan disalin' : 'Bagikan Profil'"></span>
                        </button>
                    </div>
                </div>

                <p class="mt-5 text-sm leading-relaxed text-navy-deep">{{ $profil->bio ?: 'Belum ada bio.' }}</p>
                @if ($profil->interest)
                    <p class="mt-2 text-sm text-gray-600"><span class="font-semibold text-navy">Minat:</span> {{ $profil->interest }}</p>
                @endif

                <div class="mt-4 flex flex-wrap gap-2">
                    @forelse ($profil->skills as $s)
                        <x-chip>{{ $s->nama_skill }}</x-chip>
                    @empty
                        <span class="text-sm text-gray-400">Belum ada skill.</span>
                    @endforelse
                </div>

                <dl class="mt-5 grid grid-cols-3 gap-3">
                    @foreach ([['Proyek Selesai', $riwayat->count()], ['Rekomendasi', $rekomendasi->count()], ['Skill', $profil->skills->count()]] as [$label, $angka])
                        <div class="kub-soft text-center">
                            <dd class="text-2xl font-bold text-navy">{{ $angka }}</dd>
                            <dt class="text-xs text-gray-500">{{ $label }}</dt>
                        </div>
                    @endforeach
                </dl>
            </div>
        </section>

        {{-- Portofolio --}}
        <section class="kub-card">
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
        <section class="kub-card">
            <h3 class="kub-heading">Riwayat Kolaborasi Kampus</h3>
            <p class="mb-4 text-xs text-gray-500">Jejak kontribusi pada kompetisi dan proyek</p>

            <div class="space-y-3">
                @forelse ($riwayat as $r)
                    <div class="flex items-center gap-3 rounded-xl bg-surface-tint/70 p-4">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-brand-soft text-brand-dark"><x-icon name="trophy" class="h-5 w-5" /></span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-semibold text-navy-deep">{{ $r->judul_proyek }}</p>
                            <p class="mt-1 flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                @if ($r->peran)<x-chip>{{ $r->peran }}</x-chip>@endif
                                <span>Selesai {{ $r->tanggal_selesai->translatedFormat('d M Y') }}</span>
                            </p>
                        </div>
                    </div>
                @empty
                    <x-empty-state title="Belum ada riwayat kolaborasi" icon="trophy" class="!shadow-none !ring-0 bg-surface-tint/60 py-8" />
                @endforelse
            </div>
        </section>

        {{-- Rekomendasi --}}
        <section class="kub-card" x-data="{ tulis: {{ $errors->has('isi_rekomendasi') ? 'true' : 'false' }} }">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h3 class="kub-heading">Rekomendasi Rekan Tim</h3>
                    <p class="text-xs text-gray-500">Ulasan dari rekan yang pernah satu tim</p>
                </div>
                @if ($bolehRekomendasi)
                    <button type="button" @click="tulis = true" class="kub-btn kub-btn-brand kub-btn-sm"><x-icon name="pencil" class="h-4 w-4" /> Beri Ulasan</button>
                @endif
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                @forelse ($rekomendasi as $rk)
                    <article class="flex flex-col rounded-xl bg-surface-tint/70 p-4">
                        <p class="flex-1 text-sm leading-relaxed text-navy-deep">“{{ $rk->isi_rekomendasi }}”</p>

                        <div class="mt-4 flex items-center gap-2">
                            <x-avatar :user="$rk->pemberi->user" :foto="$rk->pemberi->fotoUrl()" size="h-9 w-9 text-xs" />
                            <div class="min-w-0 text-xs">
                                <a href="{{ route('profil.lihat', $rk->pemberi) }}" class="block truncate font-semibold text-navy-deep hover:underline">{{ $rk->pemberi->user->name }}</a>
                                <span class="text-gray-500">{{ $rk->tanggal_dikirim->translatedFormat('d M Y') }}</span>
                            </div>
                        </div>

                        @if ($pemilik && $rk->status_laporan === 'dilaporkan')
                            <x-chip tone="brand" class="mt-3 self-start">Sedang ditinjau admin</x-chip>
                        @endif

                        @if ($pemilik && $rk->status_laporan === 'tidak_dilaporkan')
                            <form method="POST" action="{{ route('rekomendasi.laporkan', $rk) }}" class="mt-2" onsubmit="return confirm('Laporkan rekomendasi ini ke admin?')">
                                @csrf
                                <button class="text-xs font-medium text-red-600 hover:underline">Laporkan</button>
                            </form>
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
