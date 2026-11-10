@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex w-full items-center gap-3 rounded-lg bg-brand px-4 py-2.5 text-start text-sm font-medium text-white transition'
            : 'flex w-full items-center gap-3 rounded-lg px-4 py-2.5 text-start text-sm font-medium text-white/90 hover:bg-white/10 transition';
@endphp

{{-- Dipakai di menu HP (latar navy) pada layouts/navigation. --}}
<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
