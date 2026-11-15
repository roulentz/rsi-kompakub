@props(['skills', 'dipilih' => [], 'mode' => 'id', 'name' => 'tag_skill', 'ringkas' => false])

{{--
    Pemilih skill dengan kolom cari. Yang tampil hanya skill terpilih dan rekomendasi; skill lain muncul saat mencari.
    mode="id"   : dikirim sebagai skills[] berisi id_skill (wizard dan Edit Profil).
    mode="nama" : dikirim sebagai satu isian (name, default tag_skill) berisi nama skill dipisah koma (Cari Tim).
    $skills = koleksi Skill (id_skill, nama_skill). $dipilih = daftar id (mode id) atau nama (mode nama).
    Logikanya di resources/js/pilih-skill.js.
--}}
@php
    $daftar = collect($skills)->map(fn ($s) => ['id' => $s->id_skill, 'nama' => $s->nama_skill])->values();
    $awal = collect($dipilih)->map(fn ($v) => (string) $v)->values();
    $teks = $ringkas ? 'text-xs' : 'text-sm';
    $chip = $ringkas ? 'px-2.5 py-1' : 'px-3.5 py-1.5';
@endphp

<div x-data="pilihSkill({ daftar: @js($daftar), dipilih: @js($awal), populer: @js(\App\Models\Skill::POPULER), mode: @js($mode) })">
    @unless ($ringkas)
        <div class="kub-soft flex items-center justify-between">
            <p class="text-sm font-semibold text-navy">Keahlian dipilih (<span x-text="dipilih.length"></span>)</p>
            <p class="text-xs text-gray-500">Cari lalu klik untuk menambah</p>
        </div>
    @endunless

    {{-- Terpilih --}}
    <div x-show="terpilih.length" x-cloak class="{{ $ringkas ? 'mb-2' : 'mt-3' }} flex flex-wrap gap-2">
        <template x-for="s in terpilih" :key="kunci(s)">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-navy {{ $chip }} {{ $teks }} font-medium text-white shadow-sm">
                <span x-text="s.nama"></span>
                <button type="button" @click="lepas(s)" :aria-label="'Hapus ' + s.nama" class="rounded-full text-white/70 hover:text-white">&times;</button>
            </span>
        </template>
    </div>

    {{-- Cari --}}
    <div class="relative {{ $ringkas ? '' : 'mt-3' }}">
        <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
        <input x-ref="cari" type="search" x-model="q" @keydown.enter.prevent="pilihPertama()" autocomplete="off"
               placeholder="Cari keahlian (contoh: Figma, Python, Copywriting)..."
               class="{{ $ringkas ? 'block w-full rounded-full border-0 bg-white py-1.5 pl-10 pr-4 text-xs text-navy placeholder:text-muted/70 focus:ring-2 focus:ring-brand' : 'kub-input pl-10' }}">
    </div>

    {{-- Hasil atau rekomendasi --}}
    <p class="mb-2 mt-3 text-[11px] font-semibold uppercase tracking-wide text-gray-500"
       x-text="q.trim() ? 'Hasil pencarian (' + cocok.length + ')' : 'Rekomendasi keahlian'"></p>

    <div class="flex flex-wrap gap-2">
        <template x-for="s in hasil" :key="kunci(s)">
            <button type="button" @click="pilih(s)"
                    class="inline-flex items-center gap-1 rounded-full border border-navy/10 bg-white {{ $chip }} {{ $teks }} font-medium text-navy shadow-sm transition hover:border-brand hover:bg-brand-soft/50">
                <x-icon name="plus" class="h-3.5 w-3.5 text-brand-dark" />
                <span x-text="s.nama"></span>
            </button>
        </template>
    </div>

    <p x-show="q.trim() && !hasil.length" x-cloak class="text-xs text-gray-500">
        Tidak ada keahlian &ldquo;<span x-text="q"></span>&rdquo;. Coba kata kunci lain.
    </p>
    <p x-show="q.trim() && cocok.length > hasil.length" x-cloak class="mt-2 text-xs text-gray-400">
        Menampilkan <span x-text="hasil.length"></span> dari <span x-text="cocok.length"></span>. Ketik lebih spesifik untuk mempersempit.
    </p>
    <p x-show="!q.trim()" class="mt-2 text-xs text-gray-400">
        Masih ada <span x-text="daftar.length - hasil.length - terpilih.length"></span> keahlian lain. Ketik di kolom cari untuk menemukannya.
    </p>

    {{-- Isian yang dikirim --}}
    @if ($mode === 'nama')
        <input type="hidden" name="{{ $name }}" :value="terpilih.map(s => s.nama).join(',')">
    @else
        <template x-for="s in terpilih" :key="kunci(s)">
            <input type="hidden" name="skills[]" :value="s.id">
        </template>
    @endif
</div>
