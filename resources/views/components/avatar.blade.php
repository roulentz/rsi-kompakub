@props(['user' => null, 'size' => 'h-9 w-9', 'foto' => null])

{{--
    Foto profil jika ada, jika belum ada tampil inisial. Ukuran lewat size, contoh: size="h-12 w-12".
    Di daftar yang panjang (feed), kirim :foto="$profil->fotoUrl()" agar tidak ada query tambahan.
--}}
@php
    $nama = $user?->name ?? '';
    $inisial = strtoupper(mb_substr($nama, 0, 2)) ?: '?';
    $foto ??= $user?->profil?->fotoUrl();
@endphp

@if ($foto)
    <img src="{{ $foto }}" alt="Foto {{ $nama }}" loading="lazy"
         {{ $attributes->merge(['class' => "inline-block shrink-0 rounded-full border-2 border-white bg-lavender object-cover shadow-sm ring-1 ring-navy/10 {$size}"]) }}>
@else
    <span {{ $attributes->merge(['class' => "inline-flex shrink-0 items-center justify-center rounded-full border-2 border-white bg-gradient-to-br from-lavender to-white text-xs font-semibold text-navy shadow-sm {$size}"]) }}>
        {{ $inisial }}
    </span>
@endif
