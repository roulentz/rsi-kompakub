@props(['skills', 'dipilih' => []])

{{-- Pemilih skill berbentuk chip dengan kolom cari. Isian dikirim sebagai skills[] (nilai id_skill). --}}
<div x-data="{ q: '', dipilih: @js(array_map('strval', $dipilih)) }">
    <div class="kub-soft flex items-center justify-between">
        <p class="text-sm font-semibold text-navy">Keahlian dipilih (<span x-text="dipilih.length"></span>)</p>
        <p class="text-xs text-gray-500">Klik chip untuk memilih atau melepas</p>
    </div>

    <div class="relative mt-3">
        <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
        <input type="search" x-model="q" placeholder="Cari keahlian (contoh: UI/UX, Python, Copywriting)..." class="kub-input pl-10">
    </div>

    <div class="mt-3 flex flex-wrap gap-2">
        @foreach ($skills as $s)
            <label x-show="@js(mb_strtolower($s->nama_skill)).includes(q.toLowerCase())" class="cursor-pointer">
                <input type="checkbox" name="skills[]" value="{{ $s->id_skill }}" x-model="dipilih" class="peer sr-only">
                <span class="inline-flex items-center rounded-full border border-navy/10 bg-white px-3.5 py-1.5 text-sm font-medium text-navy shadow-sm transition hover:border-brand hover:bg-brand-soft/50 peer-checked:border-navy peer-checked:bg-navy peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-brand">
                    {{ $s->nama_skill }}
                </span>
            </label>
        @endforeach
    </div>
</div>
