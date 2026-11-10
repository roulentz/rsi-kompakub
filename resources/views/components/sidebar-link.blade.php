@props(['active' => false, 'icon' => 'home'])

@php
    $classes = $active
        ? 'bg-brand/85 text-white'
        : 'text-navy hover:bg-white/70';
@endphp

<a {{ $attributes->merge(['class' => "flex h-[55px] items-center gap-[18px] rounded-[15px] pl-[26px] text-lg font-medium transition xl:text-[21px] {$classes}", 'aria-current' => $active ? 'page' : null]) }}>
    <x-icon :name="$icon" class="h-[18px] w-[18px] shrink-0" />
    <span>{{ $slot }}</span>
</a>
