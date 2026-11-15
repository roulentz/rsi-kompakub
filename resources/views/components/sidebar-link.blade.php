@props(['active' => false, 'icon' => 'home'])

@php
    $classes = $active
        ? 'bg-gradient-to-r from-brand to-brand-dark text-navy-deep shadow-md shadow-brand/30'
        : 'text-navy hover:bg-white hover:shadow-sm hover:translate-x-0.5';
    $ikon = $active ? 'bg-white/40' : 'bg-white';
@endphp

<a {{ $attributes->merge(['class' => "group flex h-[52px] items-center gap-3.5 rounded-2xl pl-3 pr-4 text-base font-semibold transition duration-150 xl:text-[17px] {$classes}", 'aria-current' => $active ? 'page' : null]) }}>
    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $ikon }}">
        <x-icon :name="$icon" class="h-[19px] w-[19px]" />
    </span>
    <span>{{ $slot }}</span>
</a>
