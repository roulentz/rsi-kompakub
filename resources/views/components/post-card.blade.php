@props(['item', 'profil' => null])

{{--
    Kartu satu postingan di feed. Kategori Biasa tidak diberi lencana. Cari Tim menampilkan
    Lomba, Tanggal, Slot Anggota, dan Skill Required seperti di desain.
--}}
@php
    $pembuat = $item->pembuat;
    $cariTim = $item->kategori === 'Cari Tim';
    $infoLomba = $item->kategori === 'Info Lomba';
    $slot = max((int) $item->jumlah_anggota, 1);
    $terisi = min($slot, 1 + $item->lamaran->where('status', 'Diterima')->count());
    $skillTag = collect(explode(',', (string) $item->tag_skill))->map(fn ($s) => trim($s))->filter();
@endphp

<article x-data="{ menuModerasi: false, konfirmasiHapus: false }"
         class="kub-card kub-card-hover space-y-4">
    <header class="flex items-start justify-between gap-3">
        <div class="flex min-w-0 items-center gap-3">
            <x-avatar :user="$pembuat?->user" :foto="$pembuat?->fotoUrl()" size="h-11 w-11 text-sm" />
            <div class="min-w-0">
                @if ($pembuat)
                    <a href="{{ route('profil.lihat', $pembuat) }}" class="block truncate text-sm font-semibold text-navy-deep hover:underline">{{ $pembuat->user->name }}</a>
                @else
                    <span class="block truncate text-sm font-semibold text-navy-deep">Mahasiswa UB</span>
                @endif
                <p class="truncate text-xs text-gray-500">{{ $pembuat->jurusan ?? 'Mahasiswa' }} &bull; {{ $item->created_at->diffForHumans() }}</p>
            </div>
        </div>


<div class="flex shrink-0 items-center gap-2">
    @if ($cariTim || $infoLomba)
        <div class="flex items-center gap-1.5">
            @if ($cariTim && $item->status === 'Selesai')
                <x-chip class="!bg-emerald-50 !text-emerald-700">
                    Selesai
                </x-chip>
            @endif

            <x-chip :tone="$cariTim ? 'brand' : 'navy'">
                {{ $item->kategori }}
            </x-chip>
        </div>
    @endif

    @if (Auth::user()->isAdmin())
        <div class="relative">
            <button
                type="button"
                @click="menuModerasi = !menuModerasi"
                :aria-expanded="menuModerasi"
                aria-label="Opsi postingan"
                class="rounded-full p-2 text-navy transition hover:bg-lavender/50"
            >
                <span class="text-xl leading-none">⋯</span>
            </button>

            <div
                x-show="menuModerasi"
                x-cloak
                @click.outside="menuModerasi = false"
                @keydown.escape.window="menuModerasi = false"
                class="absolute right-0 top-full z-20 mt-2 w-44 rounded-xl border border-gray-100 bg-white p-1 shadow-lg"
            >
                <button
                    type="button"
                    @click="menuModerasi = false; konfirmasiHapus = true"
                    class="w-full rounded-lg px-4 py-2.5 text-left text-sm font-medium text-red-600 hover:bg-red-50"
                >
                    Hapus Postingan
                </button>
            </div>
        </div>
    @endif
</div>

    </header>

    @if ($item->nama_lomba)
        <h3 class="text-lg font-bold text-navy-deep">{{ $item->nama_lomba }}</h3>

        <div class="grid gap-2 sm:grid-cols-2">
            <div class="kub-info flex items-center gap-3">
                <x-icon name="trophy" class="h-5 w-5 shrink-0 text-brand" />
                <div class="min-w-0"><p class="text-[10px] uppercase tracking-wide text-white/60">Lomba</p><p class="truncate text-sm font-semibold">{{ $item->nama_lomba }}</p></div>
            </div>
            @if ($item->waktu_perlombaan)
                <div class="kub-info flex items-center gap-3">
                    <x-icon name="calendar" class="h-5 w-5 shrink-0 text-brand" />
                    <div><p class="text-[10px] uppercase tracking-wide text-white/60">Tanggal</p><p class="text-sm font-semibold">{{ $item->waktu_perlombaan->translatedFormat('d F Y') }}</p></div>
                </div>
            @endif

            @if ($cariTim)
                <div class="kub-info sm:col-span-2">
                    <div class="flex items-center justify-between">
                        <p class="flex items-center gap-2 text-[10px] uppercase tracking-wide text-white/60"><x-icon name="users" class="h-4 w-4 text-brand" /> Slot Anggota</p>
                        <p class="text-sm font-semibold">{{ $terisi }}/{{ $slot }}</p>
                    </div>
                    <div class="mt-2 flex gap-1.5" aria-hidden="true">
                        @for ($i = 1; $i <= $slot; $i++)
                            <span class="h-1.5 flex-1 rounded-full {{ $i <= $terisi ? 'bg-brand' : 'bg-white/25' }}"></span>
                        @endfor
                    </div>
                </div>

                @if ($skillTag->isNotEmpty())
                    <div class="kub-info sm:col-span-2">
                        <p class="flex items-center gap-2 text-[10px] uppercase tracking-wide text-white/60"><x-icon name="tag" class="h-4 w-4 text-brand" /> Skill Required</p>
                        <div class="mt-2 flex flex-wrap gap-1.5">
                            @foreach ($skillTag as $skill)
                                <span class="rounded-full bg-lavender px-2.5 py-0.5 text-xs font-medium text-navy">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>
    @endif

    @if ($item->deskripsi)
        <p class="whitespace-pre-line text-sm leading-relaxed text-gray-700">{{ $item->deskripsi }}</p>
    @endif

    @if ($item->foto)
        <div class="overflow-hidden rounded-xl bg-surface-tint ring-1 ring-navy/10">
            <img src="{{ '/storage/'.$item->foto }}" alt="Foto postingan" loading="lazy" class="max-h-96 w-full object-cover">
        </div>
    @endif

    <footer class="flex flex-wrap items-center justify-end gap-2 border-t border-navy/5 pt-3">
        <a href="{{ route('postingan.show', $item->id_postingan) }}" class="kub-btn kub-btn-soft kub-btn-sm">Lihat Detail</a>

        @if ($cariTim && $item->status === 'Aktif' && $profil && $profil->id_profil !== $item->id_pembuat)
            {{-- Dialog konfirmasi lamaran (UI05) milik SH --}}
            <button type="button" x-data x-on:click="$dispatch('open-modal', 'konfirmasi-lamaran-{{ $item->id_postingan }}')"
                    class="kub-btn kub-btn-brand kub-btn-sm">Lamar</button>
            @include('lamaran._konfirmasi', ['postingan' => $item, 'profil' => $profil])
        @endif

        @if ($pembuat && $profil && $profil->id_profil !== $item->id_pembuat && Route::has('dm.mulai'))
            <a href="{{ route('dm.mulai', $pembuat->user_id) }}" class="kub-btn kub-btn-outline kub-btn-sm">
                <x-icon name="chat" class="h-4 w-4" /> Kirim Pesan
            </a>
        @endif

    </footer>

    @if (Auth::user()->isAdmin())
        <template x-teleport="body">
            <div
                x-show="konfirmasiHapus"
                x-cloak
                @keydown.escape.window="konfirmasiHapus = false"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4"
                role="presentation"
            >
                <div
                    role="dialog"
                    aria-modal="true"
                    aria-label="Konfirmasi hapus postingan"
                    class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl"
                    @click.outside="konfirmasiHapus = false"
                >
                    <h2 class="text-xl font-bold text-navy">
                        Hapus Postingan?
                    </h2>

                    <p class="mt-3 text-sm leading-relaxed text-gray-600">
                        Apakah kamu yakin ingin menghapus postingan ini?
                        Postingan dan lamaran terkait akan dihapus
                        secara permanen.
                    </p>

                    <form
                        method="POST"
                        action="{{ route('admin.postingan.destroy', $item->id_postingan) }}"
                        class="mt-6 flex justify-end gap-3"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="button"
                            @click="konfirmasiHapus = false"
                            class="rounded-full border border-gray-200 px-5 py-2 text-sm font-semibold text-navy hover:bg-gray-50"
                        >
                            Batal
                        </button>

                        <button
                            type="submit"
                            class="rounded-full bg-red-600 px-5 py-2 text-sm font-semibold text-white hover:bg-red-700"
                        >
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </template>
    @endif
</article>
