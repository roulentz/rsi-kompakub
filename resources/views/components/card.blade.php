@props(['variant' => 'surface'])

{{-- variant: surface (abu-abu, kartu postingan) atau panel (putih bergaris) --}}
<div {{ $attributes->merge(['class' => $variant === 'panel' ? 'kub-panel' : 'kub-card']) }}>
    {{ $slot }}
</div>
