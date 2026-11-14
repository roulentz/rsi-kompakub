@props(['title', 'icon' => 'users'])

{{-- Tampilan kosong seperti "Belum ada postingan" di desain. Deskripsi lewat slot. --}}
<div {{ $attributes->merge(['class' => 'kub-card flex flex-col items-center justify-center py-12 text-center']) }}>
    <span class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-surface-tint text-navy">
        <x-icon :name="$icon" class="h-6 w-6" />
    </span>
    <p class="text-sm font-semibold text-navy-deep">{{ $title }}</p>
    @if (trim($slot))
        <p class="mt-1 max-w-sm text-xs text-gray-500">{{ $slot }}</p>
    @endif
</div>
