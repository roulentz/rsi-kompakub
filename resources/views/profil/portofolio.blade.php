@php use Illuminate\Support\Facades\Storage; @endphp
<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('profil.show') }}" class="inline-flex items-center gap-1 text-sm text-muted hover:text-navy">
            <x-icon name="arrow-left" class="h-4 w-4" /> Profil
        </a>
        <h2 class="mt-1 text-2xl font-bold text-navy-deep">Pengaturan Portofolio Mahasiswa</h2>
        <p class="kub-muted mt-0.5">Dokumentasikan hasil karya, studi kasus, dan proposal kompetisi dalam format slide ringkas.</p>
    </x-slot>

    <div class="mx-auto w-full max-w-3xl space-y-5 px-4 sm:px-6 lg:px-8">
        <x-flash />

        <section class="kub-card">
            <div class="mb-2 flex items-center justify-between text-sm">
                <h3 class="kub-heading">Halaman Slide</h3>
                <span class="text-gray-500">{{ $halaman->count() }} dari {{ $maks }} halaman terpakai</span>
            </div>
            <div class="mb-4 h-2 overflow-hidden rounded-full bg-surface-tint">
                <span class="block h-full rounded-full bg-brand" style="width: {{ min(100, $halaman->count() / max($maks, 1) * 100) }}%"></span>
            </div>

            <div class="space-y-2">
                @forelse ($halaman as $h)
                    <div class="flex items-center gap-3 rounded-xl bg-surface-tint/70 p-2.5">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white text-sm font-bold text-navy shadow-sm">{{ $h->urutan_halaman }}</span>
                        <img src="{{ Storage::disk('public')->url($h->konten) }}" alt="Halaman {{ $h->urutan_halaman }}" loading="lazy"
                             class="h-16 w-24 shrink-0 rounded-lg bg-white object-cover shadow-sm">
                        <div class="min-w-0 flex-1 text-xs text-gray-500">
                            {{ $h->ukuran_file }} MB
                            @if ($h->tautan_eksternal)
                                <a href="{{ $h->tautan_eksternal }}" target="_blank" rel="noopener" class="kub-link block truncate">{{ $h->tautan_eksternal }}</a>
                            @endif
                        </div>

                        @foreach (['naik' => 'arrow-up', 'turun' => 'arrow-down'] as $arah => $ikon)
                            <form method="POST" action="{{ route('portofolio.geser', $h) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="arah" value="{{ $arah }}">
                                <button class="flex h-9 w-9 items-center justify-center rounded-full bg-white text-navy shadow-sm transition hover:bg-lavender" aria-label="Geser {{ $arah }}">
                                    <x-icon :name="$ikon" class="h-4 w-4" />
                                </button>
                            </form>
                        @endforeach

                        <form method="POST" action="{{ route('portofolio.destroy', $h) }}" onsubmit="return confirm('Hapus halaman ini?')">
                            @csrf @method('DELETE')
                            <button class="flex h-9 w-9 items-center justify-center rounded-full bg-white text-red-600 shadow-sm transition hover:bg-red-50" aria-label="Hapus halaman">
                                <x-icon name="trash" class="h-4 w-4" />
                            </button>
                        </form>
                    </div>
                @empty
                    <x-empty-state title="Belum ada halaman" icon="image" class="!shadow-none !ring-0 bg-surface-tint/60 py-8">Unggah slide pertamamu di bawah.</x-empty-state>
                @endforelse
            </div>
        </section>

        @if ($halaman->count() < $maks)
            <form method="POST" action="{{ route('portofolio.store') }}" enctype="multipart/form-data" class="kub-card space-y-4">
                @csrf
                <h3 class="kub-heading">Tambah Halaman Slide</h3>

                <x-unggah-gambar name="halaman" :maks="5" judul="Unggah gambar slide" petunjuk="Klik atau seret gambar ke sini. JPG atau PNG, maksimal 5 MB.">
                    <x-input-error :messages="$errors->get('halaman')" class="mt-2" />
                </x-unggah-gambar>

                <div>
                    <x-input-label for="tautan_eksternal" value="Tautan eksternal (opsional)" />
                    <x-text-input id="tautan_eksternal" name="tautan_eksternal" :value="old('tautan_eksternal')" placeholder="mis. https://figma.com/... atau https://github.com/..." />
                    <x-input-error :messages="$errors->get('tautan_eksternal')" class="mt-1" />
                </div>

                <x-primary-button><x-icon name="upload" class="h-4 w-4" /> Unggah Halaman</x-primary-button>
            </form>
        @endif
    </div>
</x-app-layout>
