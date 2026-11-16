{{--
    Blok "Kelola Proyek" untuk pembuat postingan Cari Tim (PCT, milik SH), dipakai di Detail Postingan (UI02):
    Lihat Pelamar, Rekomendasi Kandidat, Tandai Selesai (form pencapaian), dan setelah selesai: ringkasan, ubah detail, ajakan rekomendasi.
--}}
@php
    $postingan->loadMissing('lamaran.pelamar.user');
    $jumlahPelamar = $postingan->lamaran->count();
    $menunggu = $postingan->lamaran->where('status', 'Menunggu Konfirmasi')->count();
    $anggota = $postingan->lamaran->where('status', 'Diterima');
    $diterima = $anggota->count();
    $sudahSelesai = $postingan->status === 'Selesai';
    $lombaLewat = $postingan->lombaSudahLewat();
    $bisaSelesai = ! $sudahSelesai && $diterima > 0 && $lombaLewat;
    $tglLomba = $postingan->waktu_perlombaan?->locale('id')->translatedFormat('d F Y');
    $galatForm = $errors->hasAny(['pencapaian', 'nama_proyek', 'peran_saya']);
    $chipPilih = 'inline-flex rounded-full border border-navy/10 bg-white px-3.5 py-1.5 text-sm font-medium text-navy shadow-sm transition hover:border-brand peer-checked:border-navy peer-checked:bg-navy peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-brand';
@endphp

<section class="kub-card" aria-labelledby="judul-kelola">
    <h2 id="judul-kelola" class="text-base font-bold text-navy">Kelola Proyek</h2>
    <p class="text-xs text-muted">Hanya kamu sebagai pembuat yang melihat bagian ini.</p>

    <div class="mt-3 flex flex-wrap items-center gap-2">
        <a href="{{ route('pelamar.index', $postingan->id_postingan) }}"
           class="inline-flex items-center gap-2 rounded-full bg-navy px-4 py-1.5 text-sm font-medium text-white transition hover:bg-navy-dark">
            Lihat Pelamar
            <span class="rounded-full bg-lavender px-2 text-xs text-navy">{{ $jumlahPelamar }}</span>
        </a>
        <a href="{{ route('postingan.rekomendasi', $postingan->id_postingan) }}"
           class="rounded-full bg-lavender px-4 py-1.5 text-sm font-medium text-navy transition hover:bg-navy hover:text-white">Rekomendasi Kandidat</a>

        @if ($sudahSelesai)
            <span class="rounded-full bg-navy px-4 py-1.5 text-sm font-medium text-white">Proyek selesai</span>
            <button type="button" x-data x-on:click="$dispatch('open-modal', 'ubah-detail-proyek')"
                    class="rounded-full bg-lavender px-4 py-1.5 text-sm font-medium text-navy transition hover:bg-navy hover:text-white">Ubah Detail Proyek</button>
        @else
            <button type="button" @disabled(! $bisaSelesai)
                    @if ($diterima > 0 && ! $lombaLewat) title="Aktif mulai {{ $tglLomba }}" @endif
                    x-data x-on:click="$dispatch('open-modal', 'tandai-selesai')"
                    class="rounded-full bg-brand px-4 py-1.5 text-sm font-medium text-white transition hover:bg-brand-dark disabled:cursor-not-allowed disabled:opacity-50">
                Tandai Selesai
            </button>
        @endif
    </div>

    @if ($menunggu > 0)
        <p class="mt-2 text-xs text-muted">{{ $menunggu }} pelamar masih menunggu keputusanmu.</p>
    @endif
    @if (! $sudahSelesai && $diterima === 0)
        <p class="mt-2 text-xs text-muted">Tandai Selesai aktif setelah ada minimal satu anggota yang diterima.</p>
    @elseif (! $sudahSelesai && ! $lombaLewat)
        <p class="mt-2 text-xs text-muted">Tandai Selesai aktif mulai hari lomba ({{ $tglLomba }}), setelah hasilnya diumumkan.</p>
    @endif

    {{-- Ringkasan setelah selesai + ajakan memberi rekomendasi (opsional) --}}
    @if ($sudahSelesai)
        <div class="mt-4 rounded-2xl bg-surface-tint/70 p-4 ring-1 ring-navy/5">
            <p class="flex items-center gap-2 text-sm font-semibold text-navy-deep">
                <x-icon name="trophy" class="h-4 w-4 text-brand-dark" />
                {{ $postingan->pencapaian ?: 'Proyek selesai' }}@if ($postingan->nama_proyek) <span class="font-normal text-navy">&middot; {{ $postingan->nama_proyek }}</span>@endif
            </p>
            <p class="mt-1 text-xs text-muted">Riwayat kolaborasi sudah tercatat. Tiap anggota bisa mengatur perannya sendiri di profilnya.</p>

            @if ($anggota->isNotEmpty())
                <p class="mt-3 text-xs font-semibold text-navy">Beri rekomendasi untuk rekan tim (opsional)</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach ($anggota as $l)
                        <a href="{{ route('profil.lihat', $l->pelamar).'?ulas=1#rekomendasi' }}"
                           class="inline-flex items-center gap-2 rounded-full bg-white py-1 pl-1 pr-3 text-xs font-medium text-navy shadow-sm ring-1 ring-navy/10 transition hover:ring-brand">
                            <x-avatar :user="$l->pelamar->user" :foto="$l->pelamar->fotoUrl()" size="h-6 w-6 text-[9px] !border" />
                            {{ $l->pelamar->user->name }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</section>

{{-- Dialog Tandai Selesai: pencapaian wajib, nama proyek dan peran opsional --}}
@if (! $sudahSelesai && $diterima > 0)
    <x-modal name="tandai-selesai" :show="$galatForm" maxWidth="md" focusable>
        <form method="POST" action="{{ route('postingan.selesai', $postingan->id_postingan) }}" class="p-6">
            @csrf
            <div class="flex items-start gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-brand/20 text-brand-dark">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="10" cy="10" r="7.5"/><path d="m6.8 10.2 2.2 2.2 4.2-4.4"/></svg>
                </span>
                <div class="min-w-0 flex-1">
                    <h3 class="text-base font-bold text-navy">Tandai Proyek Selesai?</h3>
                    <p class="text-xs text-muted">KompakUB Mahasiswa Brawijaya</p>
                </div>
                <button type="button" x-on:click="$dispatch('close')" class="text-muted hover:text-navy" aria-label="Tutup">
                    <x-icon name="x" class="h-4 w-4" />
                </button>
            </div>

            <dl class="mt-4 divide-y divide-gray-100 rounded-lg border border-gray-100 text-sm">
                <div class="flex justify-between gap-3 px-3 py-2"><dt class="text-muted">Proyek | Lomba</dt><dd class="font-semibold text-navy">{{ $postingan->nama_lomba }}</dd></div>
                <div class="flex justify-between gap-3 px-3 py-2"><dt class="text-muted">Anggota Diterima</dt><dd class="font-semibold text-navy">{{ $diterima }} Mahasiswa</dd></div>
                <div class="flex justify-between gap-3 px-3 py-2"><dt class="text-muted">Status Akhir</dt><dd class="font-semibold text-emerald-600">Selesai</dd></div>
            </dl>

            <fieldset class="mt-4">
                <legend class="text-xs font-semibold text-navy">Pencapaian tim <span class="text-red-500">*</span></legend>
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach (\App\Models\Postingan::PENCAPAIAN as $pilihan)
                        <label class="cursor-pointer">
                            <input type="radio" name="pencapaian" value="{{ $pilihan }}" class="peer sr-only" @checked(old('pencapaian') === $pilihan) required>
                            <span class="{{ $chipPilih }}">{{ $pilihan }}</span>
                        </label>
                    @endforeach
                </div>
                <x-input-error :messages="$errors->get('pencapaian')" class="mt-1" />
            </fieldset>

            <div class="mt-4">
                <label for="nama_proyek" class="text-xs font-semibold text-navy">Nama proyek <span class="font-normal text-muted">(opsional)</span></label>
                <input id="nama_proyek" type="text" name="nama_proyek" maxlength="150" value="{{ old('nama_proyek') }}" class="kub-input mt-1"
                       placeholder="Contoh: Platform Pencarian Kost Transparan">
                <x-input-error :messages="$errors->get('nama_proyek')" class="mt-1" />
            </div>

            <div class="mt-3">
                <label for="peran_saya" class="text-xs font-semibold text-navy">Perananmu di tim <span class="font-normal text-muted">(opsional)</span></label>
                <input id="peran_saya" type="text" name="peran_saya" maxlength="100" value="{{ old('peran_saya') }}" class="kub-input mt-1"
                       placeholder="Contoh: Lead Designer">
                <x-input-error :messages="$errors->get('peran_saya')" class="mt-1" />
            </div>

            <p class="mt-3 text-xs text-muted">Riwayat dicatat untukmu dan semua anggota yang diterima. Anggota mengisi perannya sendiri di profil masing-masing. Tindakan ini tidak bisa dibatalkan, tetapi pencapaian masih bisa diperbaiki.</p>

            <div class="mt-5 flex justify-end gap-2">
                <button type="button" x-on:click="$dispatch('close')" class="rounded-full border border-gray-300 bg-white px-5 py-1.5 text-sm font-medium text-navy hover:bg-surface">Batal</button>
                <button type="submit" class="rounded-full bg-brand px-5 py-1.5 text-sm font-medium text-white transition hover:bg-brand-dark">Konfirmasi</button>
            </div>
        </form>
    </x-modal>
@endif

{{-- Dialog Ubah Detail Proyek (koreksi pencapaian setelah selesai) --}}
@if ($sudahSelesai)
    <x-modal name="ubah-detail-proyek" :show="$galatForm" maxWidth="md" focusable>
        <form method="POST" action="{{ route('postingan.detail-proyek', $postingan->id_postingan) }}" class="p-6">
            @csrf
            @method('PATCH')
            <h3 class="text-base font-bold text-navy">Ubah Detail Proyek</h3>
            <p class="text-xs text-muted">Perubahan langsung tampil di riwayat kolaborasi semua anggota.</p>

            <fieldset class="mt-4">
                <legend class="text-xs font-semibold text-navy">Pencapaian tim <span class="text-red-500">*</span></legend>
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach (\App\Models\Postingan::PENCAPAIAN as $pilihan)
                        <label class="cursor-pointer">
                            <input type="radio" name="pencapaian" value="{{ $pilihan }}" class="peer sr-only" @checked(old('pencapaian', $postingan->pencapaian) === $pilihan) required>
                            <span class="{{ $chipPilih }}">{{ $pilihan }}</span>
                        </label>
                    @endforeach
                </div>
                <x-input-error :messages="$errors->get('pencapaian')" class="mt-1" />
            </fieldset>

            <div class="mt-4">
                <label for="nama_proyek_ubah" class="text-xs font-semibold text-navy">Nama proyek <span class="font-normal text-muted">(opsional)</span></label>
                <input id="nama_proyek_ubah" type="text" name="nama_proyek" maxlength="150" value="{{ old('nama_proyek', $postingan->nama_proyek) }}" class="kub-input mt-1">
                <x-input-error :messages="$errors->get('nama_proyek')" class="mt-1" />
            </div>

            <div class="mt-5 flex justify-end gap-2">
                <button type="button" x-on:click="$dispatch('close')" class="rounded-full border border-gray-300 bg-white px-5 py-1.5 text-sm font-medium text-navy hover:bg-surface">Batal</button>
                <button type="submit" class="rounded-full bg-brand px-5 py-1.5 text-sm font-medium text-white transition hover:bg-brand-dark">Simpan</button>
            </div>
        </form>
    </x-modal>
@endif
