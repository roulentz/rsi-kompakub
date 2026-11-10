@props(['active' => false, 'icon' => 'home'])

@php
    $classes = $active
        ? 'bg-brand text-white shadow-sm'
        : 'text-navy-deep hover:bg-white/70';
@endphp

<a {{ $attributes->merge(['class' => "flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition {$classes}", 'aria-current' => $active ? 'page' : null]) }}>
    <x-icon :name="$icon" class="h-5 w-5 shrink-0" />
    <span>{{ $slot }}</span>
</a>
