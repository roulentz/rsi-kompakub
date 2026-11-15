@props([
    'name',                    // nama input: foto, halaman, halaman[] ...
    'multiple' => false,
    'maks' => 2,               // batas ukuran per gambar (MB), harus sama dengan aturan di controller
    'maksFile' => 20,          // batas jumlah gambar (hanya untuk multiple)
    'judul' => 'Pilih gambar',
    'petunjuk' => null,
    'webp' => false,           // true jika server menerima WebP
    'bentuk' => 'kotak',       // kotak = area besar (profil, portofolio), tombol = tombol kecil (komposer postingan)
])

{{--
    Unggah gambar dengan pratinjau: gambar yang dipilih langsung tampil, bisa dihapus sebelum dikirim.
    Logikanya di resources/js/unggah-gambar.js. Pesan galat dari server tampil lewat <x-input-error>.
--}}
<div x-data="unggahGambar({ multiple: @js((bool) $multiple), maksMb: {{ (int) $maks }}, maksFile: {{ (int) $maksFile }}, webp: @js((bool) $webp) })" {{ $attributes->only('class') }}>
    <input x-ref="input" type="file" name="{{ $name }}" accept="{{ $webp ? 'image/png,image/jpeg,image/webp' : 'image/png,image/jpeg' }}" @if ($multiple) multiple @endif
           class="sr-only" @change="pilih($event)">

    {{-- Pemicu --}}
    @if ($bentuk === 'tombol')
        <button type="button" @click="buka()" class="kub-btn kub-btn-soft kub-btn-sm">
            <x-icon name="image" class="h-4 w-4" />
            <span x-text="items.length ? 'Ganti Foto' : @js($judul)"></span>
        </button>
    @else
        <label x-show="multiple || items.length === 0" @click.prevent="buka()"
               @dragover.prevent="terseret = true" @dragleave.prevent="terseret = false" @drop.prevent="jatuh($event)"
               :class="terseret ? 'seret' : ''" class="kub-dropzone">
            <span class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-navy shadow-sm">
                <x-icon name="upload" class="h-6 w-6" />
            </span>
            <span class="text-sm font-semibold text-navy">{{ $judul }}</span>
            <span class="text-xs text-gray-500">{{ $petunjuk ?? 'Klik di sini atau seret gambar ke sini. '.($webp ? 'JPG, PNG, atau WebP' : 'JPG atau PNG').', maksimal '.$maks.' MB.' }}</span>
        </label>
    @endif

    {{-- Pratinjau --}}
    <div x-show="items.length" x-cloak class="mt-3 grid gap-3" :class="items.length > 1 ? 'grid-cols-2 sm:grid-cols-3' : 'grid-cols-1'">
        <template x-for="(item, i) in items" :key="item.url">
            <figure class="group relative overflow-hidden rounded-xl bg-surface-tint shadow-sm ring-1 ring-navy/10">
                <img :src="item.url" :alt="'Pratinjau ' + item.nama" class="w-full object-cover" :class="items.length > 1 ? 'aspect-[4/3]' : 'max-h-80 object-contain'">
                <figcaption class="flex items-center justify-between gap-2 bg-white/90 px-3 py-1.5 text-[11px] text-gray-600">
                    <span class="truncate" x-text="item.nama"></span>
                    <span class="shrink-0 font-medium" x-text="item.ukuran + ' MB'"></span>
                </figcaption>
                <button type="button" @click="hapus(i)" aria-label="Hapus gambar"
                        class="absolute right-2 top-2 flex h-8 w-8 items-center justify-center rounded-full bg-navy-deep/80 text-white shadow transition hover:bg-red-600">
                    <x-icon name="x" class="h-4 w-4" />
                </button>
            </figure>
        </template>
    </div>

    <p x-show="galat" x-cloak x-text="galat" class="kub-error mt-2" role="alert"></p>
    {{ $slot }}
</div>
