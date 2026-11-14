@props(['disabled' => false, 'icon' => null])

{{--
    Input standar. Otomatis merah kalau $errors punya error untuk atribut name.
    Pakai icon="mail" (nama ikon dari <x-icon>) untuk ikon di kiri. Dengan icon, jangan beri mt-1 di class input,
    margin sudah ada di pembungkus.
--}}
@php
    $nama = $attributes->get('name');
    $adaError = $nama && $errors->has($nama);
    $kelas = 'rounded-lg text-sm text-navy-deep placeholder-gray-400 shadow-sm disabled:opacity-60 '
        .($adaError
            ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-500'
            : 'border-gray-200 bg-surface-tint focus:border-brand focus:ring-brand');
@endphp

@if ($icon)
    <div class="relative mt-1">
        <span class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3 {{ $adaError ? 'text-red-400' : 'text-gray-400' }}">
            <x-icon :name="$icon" class="h-4 w-4" />
        </span>
        <input @disabled($disabled) {{ $attributes->merge(['class' => $kelas.' ps-10']) }}>
    </div>
@else
    <input @disabled($disabled) {{ $attributes->merge(['class' => $kelas]) }}>
@endif
