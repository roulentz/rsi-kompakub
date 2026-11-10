<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Beranda Kolaborasi
            </h2>
            <div class="text-sm text-gray-500">
                Kategori aktif: <span class="font-semibold text-indigo-600">{{ $kategoriAktif }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 space-y-6">

        {{-- Notifikasi Sukses / Error --}}
        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm font-medium">
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        {{-- Form Buat Postingan Biasa (MZ - Post Biasa) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <form action="{{ route('post-biasa.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="flex items-start space-x-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 font-bold flex items-center justify-center shrink-0 text-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="flex-1">
                        <textarea
                            name="deskripsi"
                            rows="3"
                            maxlength="1000"
                            placeholder="Bagikan ide, proyek, atau info kolaborasi terbaru Anda... (maks 1000 karakter)"
                            class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm resize-none @error('deskripsi') border-rose-400 @enderror"
                            required>{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                    <label class="inline-flex items-center space-x-2 text-sm text-gray-500 hover:text-indigo-600 cursor-pointer transition">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Tambahkan Foto</span>
                        <input type="file" name="foto" accept="image/*" class="hidden">
                    </label>

                    <button
                        type="submit"
                        class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl text-sm transition shadow-sm">
                        Posting
                    </button>
                </div>
                @error('foto')
                    <p class="text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </form>
        </div>

        {{-- Filter Kategori (EFP-PC02: Semua, Cari Tim, Info Lomba) --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-1">
            @foreach (['Semua', 'Cari Tim', 'Info Lomba'] as $kat)
                <a
                    href="{{ route('dashboard', ['kategori' => $kat]) }}"
                    class="px-4 py-2 rounded-xl text-sm font-semibold transition whitespace-nowrap {{ $kategoriAktif === $kat ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
                    {{ $kat }}
                </a>
            @endforeach
        </div>

        {{-- Feed Daftar Postingan --}}
        <div class="space-y-4">
            @forelse ($postingan as $item)
                <article class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4 hover:border-gray-200 transition">
                    {{-- Header Postingan --}}
                    <div class="flex items-start justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-700 font-semibold flex items-center justify-center text-sm">
                                {{ strtoupper(substr($item->pembuat->user->name ?? 'M', 0, 2)) }}
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 text-sm">
                                    {{ $item->pembuat->user->name ?? 'Mahasiswa UB' }}
                                </h3>
                                <p class="text-xs text-gray-500">
                                    {{ $item->pembuat->jurusan ?? 'Mahasiswa' }} &bull; {{ $item->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        {{-- Badge Kategori --}}
                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $item->kategori === 'Cari Tim' ? 'bg-amber-50 text-amber-700 border border-amber-200' : ($item->kategori === 'Info Lomba' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-blue-50 text-blue-700 border border-blue-200') }}">
                            {{ $item->kategori }}
                        </span>
                    </div>

                    {{-- Informasi Khusus Cari Tim & Info Lomba --}}
                    @if ($item->nama_lomba)
                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-100 space-y-1">
                            <div class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Nama Kompetisi</div>
                            <div class="text-sm font-bold text-gray-900">{{ $item->nama_lomba }}</div>
                            @if ($item->waktu_perlombaan)
                                <div class="text-xs text-gray-600">Pelaksanaan: {{ $item->waktu_perlombaan->format('d M Y') }}</div>
                            @endif
                        </div>
                    @endif

                    {{-- Isi Deskripsi --}}
                    @if ($item->deskripsi)
                        <p class="text-gray-700 text-sm whitespace-pre-line leading-relaxed">
                            {{ $item->deskripsi }}
                        </p>
                    @endif

                    {{-- Foto Postingan --}}
                    @if ($item->foto)
                        <div class="rounded-xl overflow-hidden border border-gray-100 max-h-96">
                            <img src="{{ asset('storage/' . $item->foto) }}" alt="Foto Postingan" class="w-full h-auto object-cover">
                        </div>
                    @endif

                    {{-- Tag Skill (Cari Tim) --}}
                    @if ($item->tag_skill)
                        <div class="flex flex-wrap gap-1.5 pt-1">
                            @foreach (explode(',', $item->tag_skill) as $skill)
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-medium">
                                    #{{ trim($skill) }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    {{-- Footer Aksi --}}
                    <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                        <div class="text-xs text-gray-500">
                            @if ($item->kategori === 'Cari Tim')
                                <span>Kapasitas: {{ $item->slotTerisi() }} / {{ $item->jumlah_anggota }} anggota</span>
                            @endif
                        </div>

                        <div class="flex items-center space-x-2">
                            <a
                                href="{{ route('postingan.show', $item->id_postingan) }}"
                                class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">
                                Lihat Detail
                            </a>

                            {{-- Tombol Lamar Cepat jika Cari Tim --}}
                            @if ($item->kategori === 'Cari Tim' && $item->status === 'Aktif' && $profil && $profil->id_profil !== $item->id_pembuat)
                                <form action="{{ route('lamaran.store', $item->id_postingan) }}" method="POST">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition">
                                        Lamar
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="bg-white rounded-2xl p-12 text-center border border-gray-100 space-y-3">
                    <div class="w-12 h-12 mx-auto rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <h4 class="font-bold text-gray-800 text-base">Belum ada postingan</h4>
                    <p class="text-sm text-gray-500 max-w-sm mx-auto">
                        Belum ada postingan untuk kategori "{{ $kategoriAktif }}". Jadilah yang pertama membagikan postingan!
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($postingan->hasPages())
            <div class="pt-4">
                {{ $postingan->links() }}
            </div>
        @endif

    </div>
</x-app-layout>
