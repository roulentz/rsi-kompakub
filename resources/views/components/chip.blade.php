@props(['tone' => 'default'])

{{-- tone: default (lavender), brand (oranye lembut), navy (biru tua) --}}
@php
    $tones = ['default' => '', 'brand' => 'kub-chip-brand', 'navy' => 'kub-chip-navy'];
@endphp

<span {{ $attributes->merge(['class' => 'kub-chip '.($tones[$tone] ?? '')]) }}>{{ $slot }}</span>
