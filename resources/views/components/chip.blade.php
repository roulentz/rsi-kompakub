{{-- resources/views/components/chip.blade.php --}}
{{--
    Chip kecil untuk skill tags.
    Props:
    - $variant : 'default' | 'brand' | 'navy'
    - $icon    : string — nama ikon (opsional)
--}}
@props([
    'variant' => 'default',
    'icon'    => 'zap',
])

@php
    $variantClass = match($variant) {
        'brand' => 'kub-chip-brand',
        'navy'  => 'kub-chip-navy',
        default => '',
    };
@endphp

<span {{ $attributes->merge(['class' => "kub-chip {$variantClass}"]) }}>
    @if($icon)
        <x-icon :name="$icon" class="w-[9px] h-[8px]" />
    @endif
    {{ $slot }}
</span>
