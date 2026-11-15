<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('profil.show') }}" class="inline-flex items-center gap-1 text-sm text-muted hover:text-navy">
            <x-icon name="arrow-left" class="h-4 w-4" /> Profil
        </a>
        <h2 class="mt-1 text-2xl font-bold text-navy-deep">Pengaturan Profil Mahasiswa</h2>
        <p class="kub-muted mt-0.5">Perbarui informasi dan keahlianmu agar rekan tim mudah menemukanmu.</p>
    </x-slot>

    <form method="POST" action="{{ route('profil.update') }}" enctype="multipart/form-data" class="mx-auto w-full max-w-3xl space-y-5 px-4 sm:px-6 lg:px-8">
        @csrf
        @method('PUT')

        <x-flash />

        <section class="kub-card">
            <h3 class="kub-heading mb-4">Foto Profil</h3>
            <div class="flex flex-col gap-5 sm:flex-row sm:items-start">
                <div class="text-center">
                    <x-avatar :user="$profil->user" :foto="$profil->fotoUrl()" size="h-24 w-24 text-3xl shadow-angkat" />
                    <p class="mt-2 text-[11px] text-gray-500">Foto saat ini</p>
                </div>
                <x-unggah-gambar name="foto" :maks="2" judul="Pilih foto baru" petunjuk="Klik atau seret foto ke sini. JPG atau PNG, maksimal 2 MB." class="flex-1">
                    <x-input-error :messages="$errors->get('foto')" class="mt-2" />
                </x-unggah-gambar>
            </div>
        </section>

        <section class="kub-card space-y-4">
            <h3 class="kub-heading">Data Diri</h3>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="jurusan" value="Jurusan & Fakultas" />
                    <x-text-input id="jurusan" name="jurusan" :value="old('jurusan', $profil->jurusan)" maxlength="100" placeholder="Sistem Informasi, FILKOM" />
                    <x-input-error :messages="$errors->get('jurusan')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="angkatan" value="Tahun Angkatan" />
                    <x-text-input id="angkatan" name="angkatan" :value="old('angkatan', $profil->angkatan)" maxlength="10" placeholder="2024" />
                    <x-input-error :messages="$errors->get('angkatan')" class="mt-1" />
                </div>
            </div>

            <div x-data="{ n: {{ mb_strlen(old('bio', $profil->bio ?? '')) }} }">
                <div class="flex items-end justify-between">
                    <x-input-label for="bio" value="Bio Singkat" />
                    <span class="mb-1 text-xs" :class="n >= 200 ? 'font-semibold text-red-600' : 'text-gray-400'"><span x-text="n"></span> / 200 karakter</span>
                </div>
                <textarea id="bio" name="bio" rows="3" maxlength="200" @input="n = $event.target.value.length" class="kub-input">{{ old('bio', $profil->bio) }}</textarea>
                <x-input-error :messages="$errors->get('bio')" class="mt-1" />
            </div>
        </section>

        <section class="kub-card">
            <h3 class="kub-heading">Skill & Keahlian Kompetensi</h3>
            <p class="mb-4 text-xs text-gray-500">Keahlian spesifik yang kamu kuasai untuk melengkapi tim.</p>
            <x-pilih-skill :skills="$skills" :dipilih="old('skills', $profil->skills->pluck('id_skill')->all())" />
            <x-input-error :messages="$errors->get('skills.*')" class="mt-2" />
        </section>

        <section class="kub-card">
            <h3 class="kub-heading">Topik Minat Kolaborasi</h3>
            <p class="mb-3 text-xs text-gray-500">Tulis topik atau jenis proyek yang ingin kamu kerjakan.</p>
            <textarea id="interest" name="interest" rows="2" maxlength="255" class="kub-input" placeholder="Contoh: hackathon, UI design, riset data">{{ old('interest', $profil->interest) }}</textarea>
            <x-input-error :messages="$errors->get('interest')" class="mt-1" />
        </section>

        <div class="flex justify-end gap-3">
            <a href="{{ route('profil.show') }}" class="kub-btn kub-btn-outline">Batal</a>
            <x-primary-button>Simpan Perubahan</x-primary-button>
        </div>
    </form>
</x-app-layout>
