{{-- resources/views/components/avatar.blade.php --}}
{{--
    Avatar lingkaran dengan ikon SVG user (bukan inisial teks).
    Ukuran default 37×37px, border 3px solid.

    Props:
    - $navbar : bool  — jika true, border lavender + text putih (untuk di navbar)
    - $size   : string — 'sm' | 'default' | 'lg'
    - $class  : string — kelas tambahan
--}}
@props([
    'navbar' => false,
    'size'   => 'default',
    'class'  => '',
])

@php
    $sizeClasses = match($size) {
        'sm'  => 'w-[30px] h-[30px]',
        'lg'  => 'w-[80px] h-[80px] border-4',
        default => 'w-[37px] h-[37px]',
    };

    $iconSize = match($size) {
        'sm'  => 'w-[10px] h-[12px]',
        'lg'  => 'w-[28px] h-[32px]',
        default => 'w-[12px] h-[15px]',
    };

    $borderColor = $navbar
        ? 'border-lavender text-white'
        : 'border-navy text-navy';
@endphp

<div {{ $attributes->merge([
    'class' => "grid place-items-center flex-none rounded-full border-[3px] {$sizeClasses} {$borderColor} {$class}"
]) }}>
    <svg class="flex-none fill-none stroke-current stroke-2 {{ $iconSize }}" stroke-linecap="round" stroke-linejoin="round">
        <use href="#i-user" />
    </svg>
</div>
