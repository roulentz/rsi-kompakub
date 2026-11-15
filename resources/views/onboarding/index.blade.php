<x-layout-fokus>
    <div x-data="{ langkah: {{ $errors->any() ? 1 : 0 }}, total: 3 }">
        <form action="{{ route('onboarding.proses') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Pertanyaan awal --}}
            <section x-show="langkah === 0" class="kub-hero rounded-3xl p-8 shadow-angkat sm:p-12">
                <div class="relative z-10 flex min-h-[18rem] flex-col justify-end">
                    <h1 class="max-w-md text-3xl font-bold leading-tight sm:text-5xl">Lengkapi Profilmu Sekarang?</h1>
                    <p class="mt-3 max-w-md text-sm text-white/70">Profil yang lengkap membantu rekan tim menemukanmu. Kamu bisa mengubahnya kapan saja.</p>
                    <div class="mt-7 flex gap-3">
                        <button type="submit" name="aksi" value="lewati" class="kub-btn border border-white/30 text-white hover:bg-white/10">Lewati</button>
                        <button type="button" @click="langkah = 1" class="kub-btn kub-btn-brand min-w-32">Ya</button>
                    </div>
                </div>
            </section>

            {{-- Langkah 1 sampai 3 --}}
            <section x-show="langkah > 0" x-cloak class="kub-card !p-6 sm:!p-8">
                @if ($errors->any())
                    <div class="mb-5 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-200" role="alert">
                        <ul class="list-inside list-disc">
                            @foreach ($errors->all() as $galat)
                                <li>{{ $galat }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-3 gap-2" aria-hidden="true">
                    @foreach ([1, 2, 3] as $n)
                        <div class="h-1.5 rounded-full transition-colors" :class="langkah >= {{ $n }} ? 'bg-brand' : 'bg-surface-tint'"></div>
                    @endforeach
                </div>
                <p class="mt-5 text-center text-xs font-semibold uppercase tracking-wider text-brand-dark">Langkah <span x-text="langkah"></span> dari 3</p>

                <div x-show="langkah === 1" class="mt-2">
                    <h2 class="text-center text-2xl font-bold text-navy-deep">Keahlian Apa yang Kamu Bawa ke Tim?</h2>
                    <p class="mb-5 mt-1 text-center text-sm text-gray-500">Pilih minimal 1 kemampuan teknis atau non-teknis agar rekan tim menemukanmu.</p>
                    <x-pilih-skill :skills="$skills" :dipilih="old('skills', [])" />
                </div>

                <div x-show="langkah === 2" x-cloak class="mt-2">
                    <h2 class="text-center text-2xl font-bold text-navy-deep">Apa Minat Kolaborasimu?</h2>
                    <p class="mb-5 mt-1 text-center text-sm text-gray-500">Tulis topik atau jenis proyek yang ingin kamu kerjakan.</p>
                    <x-input-label for="interest" value="Minat / Bidang Ketertarikan" />
                    <x-text-input id="interest" name="interest" maxlength="255" :value="old('interest')" placeholder="Contoh: UI/UX, Web Development, Data Science" />
                </div>

                <div x-show="langkah === 3" x-cloak class="mt-2">
                    <h2 class="text-center text-2xl font-bold text-navy-deep">Punya Portofolio? (Opsional)</h2>
                    <p class="mb-5 mt-1 text-center text-sm text-gray-500">Maksimal 20 halaman, JPG atau PNG, 5 MB per halaman. Gambar yang dipilih langsung terlihat di bawah.</p>
                    <x-unggah-gambar name="halaman[]" :multiple="true" :maks="5" :maks-file="20" judul="Pilih halaman portofolio" />
                </div>

                <div class="mt-8 flex items-center justify-between gap-3">
                    <button type="button" @click="langkah--" class="kub-btn kub-btn-soft kub-btn-sm"><x-icon name="arrow-left" class="h-4 w-4" /> Kembali</button>
                    <div class="flex items-center gap-3">
                        <button type="submit" name="aksi" value="lewati" class="text-xs font-medium text-muted underline hover:text-navy">Lewati untuk sekarang</button>
                        <button type="button" x-show="langkah < total" @click="langkah++" class="kub-btn kub-btn-brand kub-btn-sm">Lanjut <x-icon name="arrow-right" class="h-4 w-4" /></button>
                        <button type="submit" name="aksi" value="lengkapi" x-show="langkah === total" x-cloak class="kub-btn kub-btn-navy kub-btn-sm">Simpan & Lanjut ke Beranda</button>
                    </div>
                </div>
            </section>
        </form>
    </div>
</x-layout-fokus>
