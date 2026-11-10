{{-- resources/views/components/text-input.blade.php --}}
@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' => 'rounded-kub-lg border border-gray-200 bg-white text-navy text-sm shadow-sm focus:border-brand focus:ring-brand'
]) !!}>
