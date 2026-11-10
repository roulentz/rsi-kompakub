<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Beranda
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto px-4">

        {{-- Filter Kategori --}}
        <div class="flex gap-2 mb-6">
            @foreach(['Semua', 'Cari Tim', 'Info Lomba'] as $kategori)
                <a href="?kategori={{ $kategori }}"
                   class="px-4 py-2 rounded-full text-sm font-medium
                          {{ $filter === $kategori ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    {{ $kategori }}
                </a>
            @endforeach
        </div>

        {{-- Daftar Postingan --}}
        <div class="space-y-4">
            @forelse($postingan as $post)
                <div class="bg-white rounded-lg shadow p-4">
                    <span class="text-xs font-semibold text-blue-600 uppercase">{{ $post->kategori }}</span>
                    <h3 class="text-lg font-semibold mt-1">{{ $post->judul ?? 'Postingan' }}</h3>
                    <p class="text-gray-600 text-sm mt-1 line-clamp-2">{{ $post->isi }}</p>
                    <a href="/postingan/{{ $post->id }}" class="text-blue-500 text-sm mt-2 inline-block hover:underline">
                        Lihat detail →
                    </a>
                </div>
            @empty
                <p class="text-gray-500 text-center py-8">Belum ada postingan untuk kategori ini.</p>
            @endforelse
        </div>

    </div>
</x-app-layout>