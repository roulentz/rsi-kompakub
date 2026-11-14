<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pesan</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg divide-y">
                @forelse ($daftarPercakapan as $percakapan)
                    @php
                        $lawan = $percakapan->pengirim_id == auth()->id()
                            ? $percakapan->penerima
                            : $percakapan->pengirim;
                    @endphp
                    <a href="{{ route('dm.show', $percakapan) }}" class="block p-4 hover:bg-gray-50">
                        <p class="font-medium text-gray-900">{{ $lawan->name }}</p>
                        <p class="text-sm text-gray-500">Buka percakapan</p>
                    </a>
                @empty
                    <p class="p-6 text-gray-500">Belum ada percakapan. Mulai dari tombol Chat di profil mahasiswa.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>