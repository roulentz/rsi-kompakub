@props(['profil' => null])

{{--
    Formulir buat postingan di Beranda. Pilih kategori dulu: Umum (tampil di tab Semua) atau Info Lomba, keduanya
    dikirim ke post-biasa.store (MZ). Pilihan Cari Tim membuka dialog Make A Post milik SH (postingan/_form-cari-tim).
    Foto (sampai 10) memakai <x-unggah-gambar> sehingga pratinjaunya langsung terlihat.
--}}
@php
    $kategoriAwal = old('kategori_komposer', 'Biasa');
@endphp

<section id="buat-postingan" class="kub-card scroll-mt-28 !p-0"
         x-data="komposer({ kategori: @js($kategoriAwal), panjang: {{ mb_strlen((string) old('deskripsi')) }},
                            aksi: @js(['biasa' => route('post-biasa.store')]) })"
         x-init="if (location.hash === '#buat-postingan') { $nextTick(() => fokus()) }">
    <form :action="urlAksi" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="kategori" :value="kategori">
        <input type="hidden" name="kategori_komposer" :value="kategori">

        {{-- Pilihan kategori --}}
        <div class="flex flex-wrap items-center gap-3 border-b border-navy/5 px-5 pb-3 pt-4">
            <p class="text-sm font-semibold text-navy">Buat postingan di</p>
            <div class="kub-filter" role="group" aria-label="Kategori postingan">
                @foreach (['Biasa' => 'Umum', 'Info Lomba' => 'Info Lomba'] as $nilai => $label)
                    <button type="button" @click="pilih('{{ $nilai }}')" :class="kategori === '{{ $nilai }}' ? 'aktif' : ''"
                            :aria-pressed="kategori === '{{ $nilai }}'" class="kub-filter-item">{{ $label }}</button>
                @endforeach
                {{-- Cari Tim punya formulir sendiri (skill, jumlah anggota) di dialog milik SH --}}
                <button type="button" x-on:click="$dispatch('open-modal', 'buat-postingan')" class="kub-filter-item">Cari Tim</button>
            </div>
        </div>

        <div class="space-y-4 px-5 py-4">
            @if ($errors->any() || session('pesan_peringatan'))
                <div class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-200" role="alert">
                    @if (session('pesan_peringatan'))
                        <p class="font-semibold">{{ session('pesan_peringatan') }}</p>
                    @endif
                    <ul class="list-inside list-disc">
                        @foreach ($errors->all() as $galat)
                            <li>{{ $galat }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Isi --}}
            <div class="flex items-start gap-3">
                <x-avatar :user="Auth::user()" :foto="$profil?->fotoUrl()" size="h-11 w-11 text-sm" />
                <div class="min-w-0 flex-1">
                    <textarea x-ref="teks" name="deskripsi" rows="3" required :maxlength="maks" @input="panjang = $event.target.value.length"
                              :placeholder="kategori === 'Info Lomba' ? 'Bagikan detail lomba untuk teman-teman (maks. 1000 karakter)' : 'Bagikan ide, proyek, atau info kolaborasi terbaru Anda... (maks. 1000 karakter)'"
                              class="kub-input resize-none">{{ old('deskripsi') }}</textarea>
                    <p class="mt-1 text-right text-[11px] text-gray-400"><span x-text="panjang"></span> / <span x-text="maks"></span></p>
                </div>
            </div>

            {{-- Kolom tambahan Info Lomba --}}
            <div x-show="kategori === 'Info Lomba'" x-cloak x-transition class="grid gap-3 rounded-xl bg-surface-tint/70 p-4 sm:grid-cols-2">
                <div>
                    <label for="nama_lomba" class="kub-label">Nama Lomba</label>
                    <input id="nama_lomba" type="text" name="nama_lomba" maxlength="100" value="{{ old('nama_lomba') }}" placeholder="Contoh: Techpreneur 2026"
                           :disabled="kategori !== 'Info Lomba'" :required="kategori === 'Info Lomba'" class="kub-input !bg-white">
                </div>
                <div>
                    <label for="waktu_perlombaan" class="kub-label">Tanggal</label>
                    <input id="waktu_perlombaan" type="date" name="waktu_perlombaan" value="{{ old('waktu_perlombaan') }}"
                           :disabled="kategori !== 'Info Lomba'" :required="kategori === 'Info Lomba'" class="kub-input !bg-white">
                </div>
            </div>

            {{-- Foto dengan pratinjau --}}
            <x-unggah-gambar name="foto[]" bentuk="tombol" judul="Tambahkan Foto" :multiple="true" :maks="10" :maksFile="10" :kompres="true" :webp="true" />
            @error('foto')<p class="kub-error">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center justify-end rounded-b-kub bg-surface-tint/50 px-5 py-3">
            <button type="submit" class="kub-btn kub-btn-brand">
                <x-icon name="send" class="h-4 w-4" />
                Posting
            </button>
        </div>
    </form>
</section>
