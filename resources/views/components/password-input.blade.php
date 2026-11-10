@props(['disabled' => false])

{{-- Input kata sandi dengan ikon gembok dan tombol lihat/sembunyikan. Atribut (id, name, autocomplete) diteruskan ke input. --}}
@php
    $nama = $attributes->get('name');
    $adaError = $nama && $errors->has($nama);
    $kelas = 'block w-full rounded-lg text-sm text-navy-deep placeholder-gray-400 shadow-sm ps-10 pe-10 disabled:opacity-60 '
        .($adaError
            ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-500'
            : 'border-gray-200 bg-surface-tint focus:border-brand focus:ring-brand');
@endphp

<div class="relative mt-1" x-data="{ lihat: false }">
    <span class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3 {{ $adaError ? 'text-red-400' : 'text-gray-400' }}">
        <x-icon name="lock" class="h-4 w-4" />
    </span>
    <input @disabled($disabled) x-bind:type="lihat ? 'text' : 'password'" {{ $attributes->merge(['type' => 'password', 'class' => $kelas]) }}>
    <button type="button" x-on:click="lihat = ! lihat"
            class="absolute inset-y-0 end-0 flex items-center pe-3 text-gray-400 hover:text-navy focus:outline-none"
            x-bind:aria-label="lihat ? 'Sembunyikan kata sandi' : 'Lihat kata sandi'">
        <x-icon name="eye" class="h-4 w-4" x-show="! lihat" />
        <x-icon name="eye-slash" class="h-4 w-4" x-show="lihat" x-cloak />
    </button>
</div>
