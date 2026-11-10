{{-- resources/views/components/sidebar-link.blade.php --}}
{{--
    Link navigasi sidebar.
    Mockup: 322×55px, gap 18px, padding-left 26px, radius 15px, font 21px.
    Aktif: bg rgba(250,177,86,.83), text putih.

    Props:
    - $href   : string
    - $active : bool
    - $icon   : string — nama ikon
--}}
@props([
    'href'   => '#',
    'active' => false,
    'icon'   => 'home',
])

@php
    $activeClass = $active
        ? 'text-white'
        : 'text-navy hover:bg-surface-tint';

    $activeBg = $active
        ? 'background:rgba(250,177,86,.83)'
        : '';
@endphp

<a href="{{ $href }}"
   class="flex items-center gap-[18px] text-nav rounded-kub-xl mb-3 no-underline {{ $activeClass }}"
   style="width:322px;height:55px;padding-left:26px;{{ $activeBg }}">
    <x-icon :name="$icon" class="w-[18px] h-[18px]" />
    {{ $slot }}
</a>
