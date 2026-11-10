<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('dashboard') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detail Postingan
                </h2>
            </div>
            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $postingan->kategori === 'Cari Tim' ? 'bg-amber-50 text-amber-700 border border-amber-200' : ($postingan->kategori === 'Info Lomba' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-blue-50 text-blue-700 border border-blue-200') }}">
                {{ $postingan->kategori }}
            </span>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 space-y-6">

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

        <article class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
            {{-- Info Pembuat --}}
            <div class="flex items-center justify-between border-b border-gray-100 pb-5">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-base">
                        {{ strtoupper(substr($postingan->pembuat->user->name ?? 'M', 0, 2)) }}
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">
                            {{ $postingan->pembuat->user->name ?? 'Mahasiswa UB' }}
                        </h3>
                        <p class="text-xs text-gray-500">
                            {{ $postingan->pembuat->jurusan ?? 'Mahasiswa' }} &bull; Dipublikasikan {{ $postingan->created_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                </div>

                <span class="text-xs px-2.5 py-1 rounded-md font-medium {{ $postingan->status === 'Aktif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-600' }}">
                    Status: {{ $postingan->status }}
                </span>
            </div>

            {{-- Info Kompetisi / Lomba --}}
            @if ($postingan->nama_lomba)
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 space-y-2">
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kompetisi</div>
                    <div class="text-lg font-bold text-slate-900">{{ $postingan->nama_lomba }}</div>
                    @if ($postingan->waktu_perlombaan)
                        <div class="text-xs text-slate-600 flex items-center space-x-1.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>Tanggal Lomba: {{ $postingan->waktu_perlombaan->format('d F Y') }}</span>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Deskripsi --}}
            <div class="text-gray-800 text-sm whitespace-pre-line leading-relaxed">
                {{ $postingan->deskripsi }}
            </div>

            {{-- Foto Postingan --}}
            @if ($postingan->foto)
                <div class="rounded-xl overflow-hidden border border-gray-100">
                    <img src="{{ asset('storage/' . $postingan->foto) }}" alt="Foto Postingan" class="w-full h-auto">
                </div>
            @endif

            {{-- Tag Skill yang Dibutuhkan --}}
            @if ($postingan->tag_skill)
                <div class="space-y-2">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Skill yang Dicari / Relevan:</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach (explode(',', $postingan->tag_skill) as $skill)
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-medium border border-indigo-100">
                                #{{ trim($skill) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Kapasitas Tim (Jika Cari Tim) --}}
            @if ($postingan->kategori === 'Cari Tim')
                <div class="p-4 bg-amber-50 rounded-xl border border-amber-200 text-sm flex items-center justify-between">
                    <div>
                        <div class="font-bold text-amber-900">Kebutuhan Anggota Tim</div>
                        <div class="text-xs text-amber-700 mt-0.5">Maksimal {{ $postingan->jumlah_anggota }} orang dalam 1 tim</div>
                    </div>
                    <div class="text-base font-extrabold text-amber-800">
                        {{ $postingan->slotTerisi() }} / {{ $postingan->jumlah_anggota }} Terisi
                    </div>
                </div>
            @endif

            {{-- Tombol Aksi --}}
            <div class="pt-6 border-t border-gray-100 flex items-center justify-between flex-wrap gap-3">
                <a
                    href="{{ route('dashboard') }}"
                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl text-sm transition">
                    &larr; Kembali ke Beranda
                </a>

                <div class="flex items-center space-x-3">
                    {{-- Tombol Tanya Dulu --}}
                    @if ($profil && $profil->id_profil !== $postingan->id_pembuat)
                        <a
                            href="{{ url('/dm?user=' . ($postingan->pembuat->user_id ?? '')) }}"
                            class="px-5 py-2.5 bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 font-semibold rounded-xl text-sm transition">
                            Tanya Dulu
                        </a>
                    @endif

                    {{-- Tombol Lamar (Jika Cari Tim & Bukan Pembuat) --}}
                    @if ($postingan->kategori === 'Cari Tim')
                        @if ($profil && $profil->id_profil === $postingan->id_pembuat)
                            <a
                                href="{{ route('pelamar.index', $postingan->id_postingan) }}"
                                class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-sm transition shadow-sm">
                                Kelola Pelamar
                            </a>
                        @elseif ($sudahMelamar)
                            <span class="px-4 py-2.5 bg-slate-100 text-slate-700 font-semibold rounded-xl text-sm border border-slate-200">
                                Status Lamaran: {{ $statusLamaran }}
                            </span>
                        @elseif ($postingan->status === 'Aktif')
                            <form action="{{ route('lamaran.store', $postingan->id_postingan) }}" method="POST">
                                @csrf
                                <button
                                    type="submit"
                                    class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-sm transition shadow-sm">
                                    Lamar Sekarang
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
        </article>

    </div>
</x-app-layout>
