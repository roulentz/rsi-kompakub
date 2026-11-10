<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Laporan Rekomendasi</h2></x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <x-flash />
            @forelse ($laporan as $l)
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <p class="text-sm text-gray-500">
                        Dari <strong>{{ $l->pemberi->user->name }}</strong> untuk <strong>{{ $l->penerima->user->name }}</strong>
                        · {{ $l->tanggal_dikirim->translatedFormat('d M Y') }}
                    </p>
                    <p class="mt-2 text-gray-800">{{ $l->isi_rekomendasi }}</p>
                    <div class="mt-3 flex gap-2">
                        <form method="POST" action="{{ route('admin.rekomendasi.tinjau', $l) }}">@csrf @method('PATCH')
                            <input type="hidden" name="aksi" value="pertahankan">
                            <button class="px-3 py-1 border rounded text-sm">Pertahankan</button>
                        </form>
                        <form method="POST" action="{{ route('admin.rekomendasi.tinjau', $l) }}" onsubmit="return confirm('Hapus rekomendasi ini?')">@csrf @method('PATCH')
                            <input type="hidden" name="aksi" value="hapus">
                            <button class="px-3 py-1 border rounded text-sm text-red-600">Hapus</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white p-6 shadow-sm sm:rounded-lg text-sm text-gray-400">Tidak ada laporan yang menunggu.</div>
            @endforelse
        </div>
    </div>
</x-app-layout>
