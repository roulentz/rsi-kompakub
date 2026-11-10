{{-- resources/views/components/responsive-nav-link.blade.php --}}
@props(['active' => false])

@php
    $classes = $active
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-brand text-start text-base font-medium text-brand bg-brand-soft transition'
        : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-navy hover:text-brand hover:bg-surface hover:border-brand transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
