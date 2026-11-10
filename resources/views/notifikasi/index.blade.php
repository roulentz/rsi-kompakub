{{--
    UI06 Permintaan Kolaborasi (PCT, milik SH).
    Tab Masuk: postingan cari tim milikku beserta jumlah pelamar baru.
    Tab Terkirim: lamaran yang aku kirim ke postingan orang lain.
    Tab Notifikasi: pemberitahuan (Pelamar Baru, Diterima, Ditolak) dengan tombol tandai dibaca.
--}}
@php
    $tgl = fn ($tanggal) => $tanggal ? $tanggal->locale('id')->translatedFormat('d F Y') : '-';
    $pillStatus = [
        'Menunggu Konfirmasi' => 'bg-lavender text-navy',
        'Diterima' => 'bg-navy text-white',
        'Ditolak' => 'bg-red-50 text-red-700',
    ];
    $tombolTab = 'rounded-full px-5 py-1 text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-brand';
@endphp
<x-app-layout>
    <div class="px-4 py-6 sm:px-8" x-data="{ tab: 'masuk' }">
        <x-flash />

        <h1 class="text-xl font-bold text-navy">Permintaan Kolaborasi</h1>

        <div class="mt-4 inline-flex gap-1 rounded-full bg-lavender p-1" role="tablist" aria-label="Jenis permintaan">
            <button type="button" role="tab" id="tab-masuk" :aria-selected="tab === 'masuk'" @click="tab = 'masuk'"
                    :class="tab === 'masuk' ? 'bg-brand text-white' : 'text-navy'" class="{{ $tombolTab }}">Masuk</button>
            <button type="button" role="tab" id="tab-terkirim" :aria-selected="tab === 'terkirim'" @click="tab = 'terkirim'"
                    :class="tab === 'terkirim' ? 'bg-brand text-white' : 'text-navy'" class="{{ $tombolTab }}">Terkirim</button>
            <button type="button" role="tab" id="tab-notifikasi" :aria-selected="tab === 'notifikasi'" @click="tab = 'notifikasi'"
                    :class="tab === 'notifikasi' ? 'bg-brand text-white' : 'text-navy'" class="{{ $tombolTab }}">
                Notifikasi
                @if ($jumlahBelumDibaca > 0)
                    <span class="ms-1 rounded-full bg-navy px-1.5 text-xs text-white">{{ $jumlahBelumDibaca }}</span>
                @endif
            </button>
        </div>

        {{-- Masuk --}}
        <section x-show="tab === 'masuk'" class="mt-5 space-y-4" role="tabpanel" aria-labelledby="tab-masuk">
            @forelse ($masuk as $item)
                <article class="kub-card">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h2 class="truncate text-xl font-bold text-navy">{{ $item['nama_lomba'] }}</h2>
                            <p class="text-xs text-muted">Pelaksanaan {{ $tgl($item['waktu_perlombaan']) }}</p>
                        </div>
                        <span class="shrink-0 rounded-full bg-lavender px-3 py-1 text-xs font-medium text-navy">
                            {{ $item['pelamar_baru'] }} Pelamar baru
                        </span>
                    </div>

                    <div class="mt-3 rounded-lg bg-white p-2">
                        <div class="rounded-lg bg-navy-dark px-3 py-2 text-white">
                            <div class="flex items-center justify-between text-xs">
                                <span>Slot Anggota</span>
                                <span>{{ $item['slot_terisi'] }}/{{ $item['slot_total'] }}</span>
                            </div>
                            <div class="mt-2 flex gap-1.5" aria-hidden="true">
                                @for ($i = 1; $i <= $item['slot_total']; $i++)
                                    <i class="h-2.5 flex-1 rounded-sm {{ $i <= $item['slot_terisi'] ? 'bg-brand' : 'bg-lavender' }}"></i>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 flex items-center justify-between">
                        @if ($item['status'] === 'Selesai')
                            <span class="rounded-full bg-navy px-3 py-1 text-xs font-medium text-white">Selesai</span>
                        @else
                            <span></span>
                        @endif
                        <a href="{{ route('pelamar.index', $item['id_postingan']) }}"
                           class="rounded-full bg-brand px-4 py-1 text-xs font-medium text-white transition hover:bg-brand-dark">Lihat Pelamar</a>
                    </div>
                </article>
            @empty
                <x-empty-state title="Belum ada pelamar yang masuk">
                    Pelamar akan muncul di sini saat ada yang mengajukan diri ke postinganmu.
                </x-empty-state>
            @endforelse
        </section>

        {{-- Terkirim --}}
        <section x-show="tab === 'terkirim'" x-cloak class="mt-5 space-y-3" role="tabpanel" aria-labelledby="tab-terkirim">
            @forelse ($terkirim as $item)
                <article class="kub-card flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border-2 border-navy text-xs font-semibold text-navy">
                        {{ strtoupper(mb_substr($item['pembuat']['nama'], 0, 2)) }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-base font-semibold text-navy">{{ $item['pembuat']['nama'] }}</p>
                        <p class="truncate text-xs text-muted">{{ $item['pembuat']['jurusan'] ?: 'Jurusan belum diisi' }}{{ $item['pembuat']['angkatan'] ? ' - '.$item['pembuat']['angkatan'] : '' }}</p>
                        <p class="mt-1 truncate text-sm font-medium text-navy">{{ $item['nama_lomba'] }}</p>
                        <p class="text-xs text-muted">Dilamar {{ $tgl($item['tanggal_lamar']) }}</p>
                    </div>
                    <span class="shrink-0 rounded-full px-3 py-1 text-xs font-medium {{ $pillStatus[$item['status']] ?? 'bg-lavender text-navy' }}">
                        {{ $item['status'] }}
                    </span>
                </article>
            @empty
                <x-empty-state title="Belum ada lamaran terkirim">
                    Lamaran yang kamu ajukan ke postingan orang lain akan muncul di sini.
                </x-empty-state>
            @endforelse
        </section>

        {{-- Notifikasi --}}
        <section x-show="tab === 'notifikasi'" x-cloak class="mt-5" role="tabpanel" aria-labelledby="tab-notifikasi">
            @if ($jumlahBelumDibaca > 0)
                <form method="POST" action="{{ route('notifikasi.dibaca-semua') }}" class="mb-3 flex justify-end">
                    @csrf
                    <button type="submit" class="rounded-full bg-navy px-4 py-1 text-xs font-medium text-white transition hover:bg-navy-dark">
                        Tandai semua dibaca
                    </button>
                </form>
            @endif

            <div class="space-y-3">
                @forelse ($notifikasi as $item)
                    <article class="kub-card flex items-center gap-3 {{ $item->status_baca ? '' : 'border-s-4 border-brand' }}">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm {{ $item->status_baca ? 'text-muted' : 'font-semibold text-navy' }}">{{ $item->pesan }}</p>
                            <p class="mt-0.5 text-xs text-muted">{{ $item->tipe }} · {{ $item->created_at?->locale('id')->translatedFormat('d F Y H:i') }}</p>
                        </div>
                        @unless ($item->status_baca)
                            <form method="POST" action="{{ route('notifikasi.dibaca', $item->id_notifikasi) }}">
                                @csrf
                                <button type="submit" class="rounded-full bg-lavender px-3 py-1 text-xs font-medium text-navy transition hover:bg-brand hover:text-white">
                                    Tandai dibaca
                                </button>
                            </form>
                        @endunless
                    </article>
                @empty
                    <x-empty-state title="Belum ada notifikasi" icon="bell">
                        Pemberitahuan tentang pelamar dan lamaranmu akan muncul di sini.
                    </x-empty-state>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
