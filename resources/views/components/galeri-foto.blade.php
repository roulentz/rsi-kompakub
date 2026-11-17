@props(['fotos' => [], 'alt' => 'Foto postingan'])

{{--
    Foto postingan: 1 foto penuh, 2 foto berdampingan, 3 foto (satu besar + dua kecil), 4 foto ke atas berupa kisi 2x2
    dengan lapisan "+N" di foto keempat. Klik foto membuka penampil layar penuh yang bisa digeser.
    Logikanya di resources/js/galeri-foto.js.
--}}
@php
    $fotos = array_values($fotos);
    $n = count($fotos);
    $tampil = array_slice($fotos, 0, 4);
    $kisi = match (true) {
        $n === 2 => 'grid-cols-2 h-60',
        $n >= 3 => 'grid-cols-2 grid-rows-2 h-72',
        default => '',
    };
@endphp

@if ($n > 0)
    <div x-data="galeriFoto(@js($fotos))" @keydown.window="if (buka) { if ($event.key === 'Escape') tutup(); else if ($event.key === 'ArrowRight') geser(1); else if ($event.key === 'ArrowLeft') geser(-1); }" {{ $attributes }}>
        <div class="overflow-hidden rounded-xl bg-surface-tint ring-1 ring-navy/10 {{ $n > 1 ? 'grid gap-1 '.$kisi : '' }}">
            @foreach ($tampil as $k => $url)
                <button type="button" @click="tampil({{ $k }})" aria-label="Buka foto {{ $k + 1 }} dari {{ $n }}"
                        class="group relative block overflow-hidden {{ $n === 3 && $k === 0 ? 'row-span-2' : '' }} {{ $n > 1 ? 'h-full w-full' : 'w-full' }}">
                    <img src="{{ $url }}" alt="{{ $alt }} {{ $k + 1 }}" loading="lazy"
                         class="{{ $n > 1 ? 'h-full w-full' : 'max-h-96 w-full' }} object-cover transition duration-300 group-hover:scale-105">

                    @if ($k === 3 && $n > 4)
                        <span class="absolute inset-0 flex items-center justify-center bg-navy-deep/55 text-2xl font-bold text-white backdrop-blur-[1px]">+{{ $n - 4 }}</span>
                    @endif
                </button>
            @endforeach
        </div>

        {{-- Penampil layar penuh --}}
        <template x-teleport="body">
            <div x-show="buka" x-cloak x-transition.opacity class="fixed inset-0 z-[100] flex items-center justify-center bg-navy-deep/95 p-4"
                 role="dialog" aria-modal="true" aria-label="Penampil foto" @click.self="tutup()">
                <button type="button" @click="tutup()" aria-label="Tutup"
                        class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/25">
                    <x-icon name="x" class="h-5 w-5" />
                </button>

                <p class="absolute left-4 top-5 rounded-full bg-white/10 px-3 py-1 text-xs font-medium text-white" x-show="fotos.length > 1">
                    <span x-text="i + 1"></span> / <span x-text="fotos.length"></span>
                </p>

                <button type="button" x-show="fotos.length > 1" @click="geser(-1)" aria-label="Foto sebelumnya"
                        class="absolute left-3 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/25">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12.5 4-6 6 6 6"/></svg>
                </button>

                <img :src="fotos[i]" alt="Foto postingan" draggable="false"
                     class="max-h-[85vh] max-w-full select-none rounded-lg object-contain shadow-2xl" style="touch-action: pan-y"
                     @pointerdown="mulaiSentuh($event)" @pointerup="akhiriSentuh($event)" @pointercancel="awal = null">

                <button type="button" x-show="fotos.length > 1" @click="geser(1)" aria-label="Foto berikutnya"
                        class="absolute right-3 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/25">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m7.5 4 6 6-6 6"/></svg>
                </button>
            </div>
        </template>
    </div>
@endif
