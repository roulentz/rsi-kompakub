<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Postingan
        </h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow p-6">

            {{-- Kategori & Judul --}}
            <span class="text-xs font-semibold text-blue-600 uppercase">{{ $post->kategori }}</span>
            <h1 class="text-2xl font-bold mt-1">{{ $post->judul ?? 'Postingan' }}</h1>

            {{-- Penulis & waktu --}}
            <p class="text-gray-500 text-sm mt-1">
                Oleh {{ $post->user->name ?? 'Anonim' }} · {{ $post->created_at->diffForHumans() }}
            </p>

            {{-- Foto (kalau ada) --}}
            @if(!empty($post->foto))
                <img src="{{ asset('storage/' . $post->foto) }}" alt="Foto" class="mt-4 rounded-lg w-full object-cover max-h-64">
            @endif

            {{-- Isi --}}
            <div class="mt-4 text-gray-700 leading-relaxed">
                {{ $post->isi }}
            </div>

            {{-- Tombol aksi (placeholder — disambung Tahap 4) --}}
            <div class="mt-6 flex gap-3">
                <button disabled class="px-5 py-2 bg-blue-600 text-white rounded-lg opacity-60 cursor-not-allowed">
                    Lamar
                </button>
                <button disabled class="px-5 py-2 bg-gray-200 text-gray-700 rounded-lg opacity-60 cursor-not-allowed">
                    Tanya Dulu
                </button>
            </div>

        </div>
    </div>
</x-app-layout>