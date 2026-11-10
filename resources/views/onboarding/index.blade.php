<x-guest-layout>
    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-bold text-navy">Lengkapi Profil Anda</h2>
            <p class="text-sm text-muted mt-1">
                Langkah awal untuk memulai kolaborasi di KompakUB.
            </p>
        </div>

        @if ($errors->any())
            <div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-xs space-y-1">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('onboarding.proses') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Minat / Interest --}}
            <div>
                <x-input-label for="interest" value="Minat / Bidang Ketertarikan" />
                <x-text-input id="interest" name="interest" type="text" class="mt-1 block w-full"
                    placeholder="Contoh: UI/UX, Web Development, Data Science"
                    value="{{ old('interest') }}" />
            </div>

            {{-- Pilihan Skill --}}
            <div>
                <x-input-label value="Skill yang Dikuasai" />
                <div class="mt-2 max-h-48 overflow-y-auto border border-gray-100 rounded-lg p-3 space-y-2 bg-surface">
                    @forelse($skills as $skill)
                        <label class="flex items-center gap-2 text-sm text-navy cursor-pointer">
                            <input type="checkbox" name="skills[]" value="{{ $skill->id_skill }}"
                                class="rounded border-gray-300 text-brand focus:ring-brand"
                                {{ in_array($skill->id_skill, old('skills', [])) ? 'checked' : '' }}>
                            <span>{{ $skill->nama_skill }}</span>
                        </label>
                    @empty
                        <p class="text-xs text-muted">Belum ada daftar skill yang tersedia.</p>
                    @endforelse
                </div>
            </div>

            {{-- Upload Portofolio --}}
            <div>
                <x-input-label for="halaman" value="Unggah Portofolio (Opsional, JPG/PNG maks 5MB)" />
                <input id="halaman" name="halaman[]" type="file" multiple accept="image/png,image/jpeg"
                    class="mt-1 block w-full text-xs text-muted file:mr-4 file:py-2 file:px-4 file:rounded-pill file:border-0 file:text-xs file:font-semibold file:bg-lavender file:text-navy hover:file:bg-brand hover:file:text-white file:cursor-pointer" />
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <button type="submit" name="aksi" value="lewati"
                    class="text-xs text-muted hover:text-navy underline">
                    Lewati langkah ini
                </button>

                <x-primary-button name="aksi" value="lengkapi">
                    Simpan & Lanjut ke Dashboard
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
