<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Lengkapi Profil Anda</h2></x-slot>

    <div class="py-8" x-data="{ step: 1, n: 0 }">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-flash />
            <form method="POST" action="{{ route('onboarding.proses') }}" enctype="multipart/form-data" class="bg-white p-6 shadow-sm sm:rounded-lg">
                @csrf
                <p class="text-sm text-gray-500 mb-4">Langkah <span x-text="step"></span> dari 3</p>

                <div x-show="step === 1">
                    <h3 class="font-semibold mb-2">1. Pilih skill</h3>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach ($skills as $s)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" name="skills[]" value="{{ $s->id_skill }}" @checked(in_array($s->id_skill, old('skills', []))) class="rounded border-gray-300">
                                {{ $s->nama_skill }}
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('skills.*')" class="mt-2" />
                </div>

                <div x-show="step === 2" x-cloak>
                    <h3 class="font-semibold mb-2">2. Interest</h3>
                    <textarea name="interest" maxlength="255" rows="3" class="w-full rounded-md border-gray-300" placeholder="Contoh: UI design, hackathon, riset data">{{ old('interest') }}</textarea>
                    <x-input-error :messages="$errors->get('interest')" class="mt-2" />
                </div>

                <div x-show="step === 3" x-cloak>
                    <h3 class="font-semibold mb-2">3. Portofolio (opsional)</h3>
                    <p class="text-sm text-gray-500 mb-2">Maksimal 20 halaman, JPG atau PNG, 5 MB per halaman.</p>
                    <input type="file" name="halaman[]" multiple accept=".jpg,.jpeg,.png">
                    <x-input-error :messages="$errors->get('halaman')" class="mt-2" />
                    <x-input-error :messages="$errors->get('halaman.*')" class="mt-2" />
                </div>

                <div class="mt-6 flex items-center justify-between">
                    <button type="submit" name="aksi" value="lewati" class="text-sm underline text-gray-600">Lewati untuk sekarang</button>
                    <div class="flex gap-2">
                        <button type="button" x-show="step > 1" @click="step--" class="px-4 py-2 border rounded-md text-sm">Kembali</button>
                        <button type="button" x-show="step < 3" @click="step++" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm">Lanjut</button>
                        <button type="submit" name="aksi" value="lengkapi" x-show="step === 3" x-cloak class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm">Simpan Profil</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
