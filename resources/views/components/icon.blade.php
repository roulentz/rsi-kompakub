{{-- resources/views/components/icon.blade.php --}}
{{--
    Komponen ikon SVG — mengambil dari sprite #i-{name}.
    Semua ikon didefinisikan di layouts/navigation.blade.php.

    Props:
    - $name : string — nama ikon (home, search, message, user, dll)
--}}
@props(['name'])

<svg {{ $attributes->merge([
    'class' => 'flex-none fill-none stroke-current stroke-2 w-[18px] h-[18px]',
]) }} stroke-linecap="round" stroke-linejoin="round">
    <use href="#i-{{ $name }}" />
</svg>
