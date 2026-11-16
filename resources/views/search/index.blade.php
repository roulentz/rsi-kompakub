
<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-navy">
            Hasil Pencarian
        </h1>

        <p class="mt-1 text-sm text-muted">
            {{ $type === 'people' ? 'Profil Mahasiswa' : 'Postingan' }}
            @if ($keyword !== '')
                untuk "{{ $keyword }}"
            @endif
        </p>
    </x-slot>

    <div class="mx-auto max-w-[920px] px-4 py-5 sm:px-6">
        @if ($keyword === '')
            <div class="rounded-2xl bg-white p-10 text-center shadow-sm">
                <p class="text-sm text-gray-500">
                    Gunakan kolom pencarian di atas untuk mencari postingan atau profil mahasiswa.
                </p>
            </div>

        @elseif ($hasil->isEmpty())
            <div class="rounded-2xl bg-white p-10 text-center shadow-sm">
                <h2 class="font-semibold text-navy">
                    Hasil tidak ditemukan
                </h2>
                <p class="mt-2 text-sm text-gray-500">
                    Tidak ada hasil untuk "{{ $keyword }}".
                </p>
            </div>

        @else
            <p class="mb-4 text-sm text-muted">
                {{ $hasil->total() }} hasil ditemukan
            </p>

            <div class="space-y-4">
                @if ($type === 'people')
                    @foreach ($hasil as $profil)
                        <div class="rounded-2xl bg-white p-5 shadow-sm">
                            <div class="flex flex-wrap items-center gap-4">
                                @if ($profil->user)
                                    <a href="{{ route('profil.lihat', $profil->id_profil) }}">
                                        <x-avatar
                                            :user="$profil->user"
                                            size="h-14 w-14"
                                        />
                                    </a>
                                @endif

                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('profil.lihat', $profil->id_profil) }}"
                                       class="font-semibold text-navy hover:underline">
                                        {{ $profil->user?->name ?? 'Mahasiswa' }}
                                    </a>

                                    <p class="text-sm text-gray-500">
                                        {{ $profil->jurusan ?? 'Jurusan belum diisi' }}
                                        @if ($profil->angkatan)
                                            • {{ $profil->angkatan }}
                                        @endif
                                    </p>

                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @foreach ($profil->skills->take(5) as $skill)
                                            <span class="rounded-full bg-lavender px-3 py-1 text-xs text-navy">
                                                {{ $skill->nama_skill }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                @if ($profil->user && $profil->user_id != Auth::id())
                                    <a href="{{ route('dm.mulai', $profil->user_id) }}"
                                       class="inline-flex items-center rounded-full bg-brand px-4 py-2 text-sm font-semibold text-white transition hover:opacity-90">
                                        Kirim Pesan
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    @foreach ($hasil as $post)
                        <div class="rounded-2xl bg-white p-5 shadow-sm">
                            <a href="{{ route('postingan.show', $post->id_postingan) }}"
                               class="block">

                                <p class="text-sm font-semibold text-navy">
                                    {{ $post->pembuat?->user?->name ?? 'Mahasiswa' }}
                                </p>

                                <p class="mt-1 text-xs text-gray-500">
                                    {{ $post->created_at?->format('d M Y') ?? '' }}
                                </p>

                                @if ($post->nama_lomba)
                                    <h2 class="mt-3 text-lg font-bold text-navy">
                                        {{ $post->nama_lomba }}
                                    </h2>
                                @endif

                                <p class="mt-3 whitespace-pre-line text-sm text-gray-700">
                                    {{ $post->deskripsi }}
                                </p>

                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="rounded-full bg-lavender px-3 py-1 text-xs text-navy">
                                        {{ $post->kategori }}
                                    </span>

                                    @if ($post->tag_skill)
                                        <span class="text-xs text-gray-500">
                                            {{ $post->tag_skill }}
                                        </span>
                                    @endif
                                </div>
                            </a>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="mt-6">
                {{ $hasil->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
