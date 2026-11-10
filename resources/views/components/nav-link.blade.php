{{-- resources/views/components/nav-link.blade.php --}}
{{-- Link navigasi di navbar (desktop). Sekarang jarang dipakai karena sidebar jadi utama. --}}
@props(['active' => false])

@php
    $classes = $active
        ? 'text-white border-b-2 border-brand font-medium'
        : 'text-lavender hover:text-white border-b-2 border-transparent';
@endphp

<a {{ $attributes->merge(['class' => "inline-flex items-center px-3 pt-1 text-sm leading-5 transition {$classes}"]) }}>
    {{ $slot }}
</a>
