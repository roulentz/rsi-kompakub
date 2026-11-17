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
    @if ($cariTim && $item->status === 'Selesai')
        <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 ring-1 ring-gray-200">
            <x-icon name="check-circle" class="h-3.5 w-3.5" /> Lomba telah selesai
        </span>
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
    @endif

    @if ($item->waktu_perlombaan || $cariTim)
        <div class="kub-info-panel grid gap-2.5 sm:grid-cols-2">
            @if ($item->waktu_perlombaan)
                <div class="kub-info flex items-center gap-3 {{ $cariTim ? '' : 'sm:col-span-2' }}">
                    <span class="kub-info-ikon"><x-icon name="calendar" class="h-5 w-5" /></span>
                    <div class="min-w-0">
                        <p class="kub-info-label">Tanggal lomba</p>
                        <p class="text-sm font-semibold">{{ $item->waktu_perlombaan->translatedFormat('d F Y') }}</p>
                    </div>
                </div>
            @endif

            @if ($cariTim)
                <div class="kub-info">
                    <div class="flex items-center gap-3">
                        <span class="kub-info-ikon"><x-icon name="users" class="h-5 w-5" /></span>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <p class="kub-info-label">Slot Anggota</p>
                                <p class="text-sm font-semibold">{{ $terisi }}/{{ $slot }}@if ($terisi >= $slot) <span class="ms-1 text-[10px] font-medium text-gray-500">penuh</span>@endif</p>
                            </div>
                            <div class="mt-1.5 flex gap-1" aria-hidden="true">
                                @for ($i = 1; $i <= $slot; $i++)
                                    <span class="h-1.5 flex-1 rounded-full {{ $i <= $terisi ? 'bg-gradient-to-r from-brand to-brand-dark' : 'bg-navy/10' }}"></span>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>

                @if ($skillTag->isNotEmpty())
                    <div class="kub-info sm:col-span-2">
                        <p class="kub-info-label flex items-center gap-1.5"><x-icon name="tag" class="h-3.5 w-3.5 text-brand-dark" /> Skill Required</p>
                        <div class="mt-2 flex flex-wrap gap-1.5">
                            @foreach ($skillTag as $skill)
                                <span class="rounded-full bg-white px-2.5 py-0.5 text-xs font-medium text-navy shadow-sm ring-1 ring-navy/10">{{ $skill }}</span>
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

    <x-galeri-foto :fotos="$item->daftarFoto()" />

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
