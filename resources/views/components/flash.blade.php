{{-- Pesan sukses (session "status") dan pesan galat umum (session "error"). Bisa ditutup. --}}
@foreach (['status' => 'sukses', 'error' => 'galat'] as $kunci => $jenis)
    @if (session($kunci))
        <div x-data="{ buka: true }" x-show="buka" x-transition role="{{ $jenis === 'galat' ? 'alert' : 'status' }}"
             {{ $attributes->merge(['class' => 'mb-4 flex items-start gap-3 rounded-xl px-4 py-3 text-sm shadow-sm ring-1 '.($jenis === 'galat' ? 'bg-red-50 text-red-700 ring-red-200' : 'bg-emerald-50 text-emerald-800 ring-emerald-200')]) }}>
            <x-icon :name="$jenis === 'galat' ? 'x' : 'check'" class="mt-0.5 h-5 w-5 shrink-0" />
            <p class="flex-1">{{ session($kunci) }}</p>
            <button type="button" @click="buka = false" class="opacity-70 hover:opacity-100" aria-label="Tutup pesan">
                <x-icon name="x" class="h-4 w-4" />
            </button>
        </div>
    @endif
@endforeach
