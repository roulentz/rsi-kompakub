{{--
    Form "Make A Post" (UI01) sebagai dialog: buat postingan Cari Tim (PCT-PC01, milik SH).
    Dibuka lewat $dispatch('open-modal', 'buat-postingan') dari tombol Make A Post di sidebar,
    dipasang sekali di layouts/app. Kirim ke route postingan.store (StorePostinganRequest).
    Kalau validasi gagal, dialog terbuka lagi dengan isian lama + pesan peringatan.
--}}
@php
    $gagal = old('_form') === 'postingan';
    $daftarSkill = \App\Models\Skill::orderBy('nama_skill')->pluck('nama_skill')->values();
    $skillLama = $gagal ? collect(explode(',', (string) old('tag_skill')))->map(fn ($s) => trim($s))->filter()->values() : collect();
    $label = 'inline-flex w-full items-center gap-1.5 rounded-full bg-brand px-3 py-1 text-xs font-semibold text-white whitespace-nowrap sm:w-[132px] sm:shrink-0';
    $isian = 'block w-full rounded-full border-0 bg-white px-4 py-1.5 text-xs text-navy placeholder:text-muted/70 focus:ring-2 focus:ring-brand';
@endphp

<x-modal name="buat-postingan" :show="$gagal" maxWidth="xl" focusable>
    <form method="POST" action="{{ route('postingan.store') }}" enctype="multipart/form-data" class="bg-surface p-5" aria-label="Form Buat Postingan Cari Tim"
          x-data="{ dipilih: @js($skillLama), tersedia: @js($daftarSkill), pilih: '' }">
        @csrf
        <input type="hidden" name="_form" value="postingan">

        <div class="space-y-2.5">
            <div class="flex flex-col gap-1.5 sm:flex-row sm:items-center">
                <label for="nama_lomba" class="{{ $label }}">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="10" cy="7.5" r="4"/><path d="m7.5 11 -1 6 3.5-2 3.5 2-1-6"/></svg>
                    Nama Lomba
                </label>
                <input id="nama_lomba" name="nama_lomba" type="text" maxlength="100" value="{{ $gagal ? old('nama_lomba') : '' }}" placeholder="Masukkan nama lomba" class="{{ $isian }}">
            </div>

            <div class="flex flex-col gap-1.5 sm:flex-row sm:items-center">
                <label for="waktu_perlombaan" class="{{ $label }}"><x-icon name="calendar" class="h-3.5 w-3.5" />Tanggal</label>
                <input id="waktu_perlombaan" name="waktu_perlombaan" type="date" value="{{ $gagal ? old('waktu_perlombaan') : '' }}" class="{{ $isian }}">
            </div>

            <div class="flex flex-col gap-1.5 sm:flex-row sm:items-center">
                <label for="jumlah_anggota" class="{{ $label }}"><x-icon name="users" class="h-3.5 w-3.5" />Team</label>
                <input id="jumlah_anggota" name="jumlah_anggota" type="number" min="1" max="10" value="{{ $gagal ? old('jumlah_anggota') : '' }}" placeholder="Jumlah anggota" class="{{ $isian }}">
            </div>

            <div class="flex flex-col gap-1.5 sm:flex-row sm:items-start">
                <label for="pilih_skill" class="{{ $label }} sm:mt-0.5">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 2 4 11h5l-1 7 7-9h-5z"/></svg>
                    Skill Needed
                </label>
                <div class="min-w-0 flex-1">
                    <div class="relative">
                        <select id="pilih_skill" x-model="pilih"
                                x-on:change="if (pilih && !dipilih.includes(pilih)) dipilih.push(pilih); pilih = ''"
                                class="{{ $isian }} appearance-none pr-9">
                            <option value="" selected>Pilih skill</option>
                            <template x-for="s in tersedia.filter(x => !dipilih.includes(x))" :key="s">
                                <option :value="s" x-text="s"></option>
                            </template>
                        </select>
                        <x-icon name="chevron-down" class="pointer-events-none absolute right-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-navy" />
                    </div>
                    <div class="mt-1.5 flex flex-wrap gap-1.5" x-show="dipilih.length" x-cloak>
                        <template x-for="s in dipilih" :key="s">
                            <span class="inline-flex items-center gap-1 rounded-full bg-lavender px-2.5 py-0.5 text-xs font-medium text-navy">
                                <span x-text="s"></span>
                                <button type="button" x-on:click="dipilih = dipilih.filter(x => x !== s)" class="text-muted hover:text-navy" x-bind:aria-label="'Hapus ' + s">&times;</button>
                            </span>
                        </template>
                    </div>
                    <input type="hidden" name="tag_skill" :value="dipilih.join(',')">
                </div>
            </div>

            <textarea name="deskripsi" rows="4" maxlength="500" placeholder="Katakan sesuatu" aria-label="Katakan sesuatu"
                      class="block w-full resize-none rounded-lg border-0 bg-white px-4 py-2.5 text-xs text-navy placeholder:text-muted/70 focus:ring-2 focus:ring-brand">{{ $gagal ? old('deskripsi') : '' }}</textarea>

            {{-- Foto opsional (poster lomba, dsb.) dengan pratinjau --}}
            <x-unggah-gambar name="foto" bentuk="tombol" judul="Tambahkan Foto" :maks="2" :webp="true" />
        </div>

        @if ($gagal && (session('pesan_peringatan') || $errors->any()))
            <div class="mt-3 rounded-lg bg-red-50 px-3 py-2 text-xs font-medium text-red-700" role="alert">
                @if (session('pesan_peringatan'))
                    <p>{{ session('pesan_peringatan') }}</p>
                @else
                    @foreach ($errors->all() as $pesan)
                        <p>{{ $pesan }}</p>
                    @endforeach
                @endif
            </div>
        @endif

        <div class="mt-4 flex justify-end gap-2">
            <button type="button" x-on:click="$dispatch('close')" class="rounded-full bg-navy px-6 py-1.5 text-xs font-medium text-white transition hover:bg-navy-dark">Batal</button>
            <button type="submit" class="rounded-full bg-navy px-6 py-1.5 text-xs font-medium text-white transition hover:bg-navy-dark">Kirim</button>
        </div>
    </form>
</x-modal>
