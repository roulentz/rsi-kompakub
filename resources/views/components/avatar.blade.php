@props(['user' => null, 'size' => 'h-9 w-9'])

{{-- Lingkaran inisial. Foto profil menyusul saat kolom foto tersedia di profil. --}}
@php
    $nama = $user?->name ?? '';
    $inisial = strtoupper(mb_substr($nama, 0, 2)) ?: '?';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex shrink-0 items-center justify-center rounded-full border-2 border-current text-xs font-semibold {$size}"]) }}>
    {{ $inisial }}
</span>
