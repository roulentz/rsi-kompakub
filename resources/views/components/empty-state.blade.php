@props(['title', 'icon' => 'users'])

{{-- Tampilan kosong seperti "Belum ada postingan" di desain. Deskripsi lewat slot. --}}
<div {{ $attributes->merge(['class' => 'kub-card flex flex-col items-center justify-center py-12 text-center']) }}>
    <span class="mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-lavender to-white text-navy shadow-sm">
        <x-icon :name="$icon" class="h-7 w-7" />
    </span>
    <p class="text-sm font-semibold text-navy-deep">{{ $title }}</p>
    @if (trim($slot))
        <p class="mt-1 max-w-sm text-xs text-gray-500">{{ $slot }}</p>
    @endif
</div>
