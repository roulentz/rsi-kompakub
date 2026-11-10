@php use Illuminate\Support\Facades\Storage; @endphp
<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Kelola Portofolio</h2></x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <p class="text-sm text-gray-500 mb-3">{{ $halaman->count() }} dari {{ $maks }} halaman terpakai.</p>
                @forelse ($halaman as $h)
                    <div class="flex items-center gap-3 py-2 border-b last:border-0">
                        <span class="w-6 text-sm text-gray-500">{{ $h->urutan_halaman }}</span>
                        <img src="{{ Storage::disk('public')->url($h->konten) }}" class="h-16 w-24 object-cover rounded" alt="Halaman {{ $h->urutan_halaman }}">
                        <div class="flex-1 text-xs text-gray-500">
                            {{ $h->ukuran_file }} MB
                            @if ($h->tautan_eksternal)<br><a href="{{ $h->tautan_eksternal }}" target="_blank" rel="noopener" class="underline">{{ $h->tautan_eksternal }}</a>@endif
                        </div>
                        @foreach (['naik' => '↑', 'turun' => '↓'] as $arah => $ikon)
                            <form method="POST" action="{{ route('portofolio.geser', $h) }}">@csrf @method('PATCH')
                                <input type="hidden" name="arah" value="{{ $arah }}">
                                <button class="px-2 py-1 border rounded text-sm">{{ $ikon }}</button>
                            </form>
                        @endforeach
                        <form method="POST" action="{{ route('portofolio.destroy', $h) }}" onsubmit="return confirm('Hapus halaman ini?')">@csrf @method('DELETE')
                            <button class="px-2 py-1 border rounded text-sm text-red-600">Hapus</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Belum ada halaman.</p>
                @endforelse
            </div>

            @if ($halaman->count() < $maks)
                <form method="POST" action="{{ route('portofolio.store') }}" enctype="multipart/form-data" class="bg-white p-6 shadow-sm sm:rounded-lg space-y-3">
                    @csrf
                    <h3 class="font-semibold">Tambah halaman</h3>
                    <input type="file" name="halaman" accept=".jpg,.jpeg,.png" required>
                    <x-input-error :messages="$errors->get('halaman')" />
                    <div>
                        <x-text-input name="tautan_eksternal" class="block w-full" placeholder="Tautan eksternal (opsional), mis. https://github.com/..." :value="old('tautan_eksternal')" />
                        <x-input-error :messages="$errors->get('tautan_eksternal')" class="mt-1" />
                    </div>
                    <x-primary-button>Unggah</x-primary-button>
                </form>
            @endif

            <a href="{{ route('profil.show') }}" class="text-sm underline">&larr; Kembali ke profil</a>
        </div>
    </div>
</x-app-layout>
