{{-- Pill status lamaran: Menunggu Konfirmasi / Diterima / Ditolak. --}}
@php
    $gaya = [
        'Menunggu Konfirmasi' => 'bg-lavender text-navy',
        'Diterima' => 'bg-navy text-white',
        'Ditolak' => 'bg-red-50 text-red-700',
    ][$status] ?? 'bg-lavender text-navy';
@endphp

<span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium {{ $gaya }}">
    @if ($status === 'Diterima')
        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m4 10 4 4 8-8"/></svg>
    @elseif ($status === 'Ditolak')
        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m5 5 10 10M15 5 5 15"/></svg>
    @else
        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="10" cy="10" r="7.5"/><path d="M10 5.5V10l3 2"/></svg>
    @endif
    {{ $status }}
</span>
