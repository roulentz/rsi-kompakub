{{-- resources/views/components/card.blade.php --}}
{{--
    Kartu abu-abu generik (postingan, box).
    border-radius: 5px (sesuai mockup).
--}}
@props(['class' => ''])

<div {{ $attributes->merge(['class' => "kub-card {$class}"]) }}>
    {{ $slot }}
</div>
