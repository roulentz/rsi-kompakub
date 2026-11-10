@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-semibold text-navy-deep']) }}>
    {{ $value ?? $slot }}
</label>
