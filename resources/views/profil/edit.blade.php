<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Edit Profil</h2></x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('profil.update') }}" enctype="multipart/form-data" class="bg-white p-6 shadow-sm sm:rounded-lg space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="foto" value="Foto profil (JPG/PNG, maks. 2 MB)" />
                    @if ($profil->fotoUrl())<img src="{{ $profil->fotoUrl() }}" class="h-16 w-16 rounded-full object-cover my-2" alt="Foto">@endif
                    <input id="foto" type="file" name="foto" accept=".jpg,.jpeg,.png">
                    <x-input-error :messages="$errors->get('foto')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="jurusan" value="Jurusan" />
                    <x-text-input id="jurusan" name="jurusan" class="block mt-1 w-full" :value="old('jurusan', $profil->jurusan)" maxlength="100" />
                    <x-input-error :messages="$errors->get('jurusan')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="angkatan" value="Angkatan" />
                    <x-text-input id="angkatan" name="angkatan" class="block mt-1 w-full" :value="old('angkatan', $profil->angkatan)" maxlength="10" placeholder="2024" />
                    <x-input-error :messages="$errors->get('angkatan')" class="mt-1" />
                </div>

                <div x-data="{ n: {{ strlen(old('bio', $profil->bio ?? '')) }} }">
                    <x-input-label for="bio" value="Bio" />
                    <textarea id="bio" name="bio" rows="3" maxlength="200" x-on:input="n = $event.target.value.length" class="w-full mt-1 rounded-md border-gray-300">{{ old('bio', $profil->bio) }}</textarea>
                    <p class="text-xs text-gray-500"><span x-text="n"></span>/200</p>
                    <x-input-error :messages="$errors->get('bio')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="interest" value="Interest" />
                    <textarea id="interest" name="interest" rows="2" maxlength="255" class="w-full mt-1 rounded-md border-gray-300">{{ old('interest', $profil->interest) }}</textarea>
                    <x-input-error :messages="$errors->get('interest')" class="mt-1" />
                </div>

                <div>
                    <x-input-label value="Skill" />
                    @php $dipilih = old('skills', $profil->skills->pluck('id_skill')->all()); @endphp
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        @foreach ($skills as $s)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" name="skills[]" value="{{ $s->id_skill }}" @checked(in_array($s->id_skill, $dipilih)) class="rounded border-gray-300"> {{ $s->nama_skill }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('profil.show') }}" class="px-4 py-2 border rounded-md text-sm">Batal</a>
                    <x-primary-button>Simpan</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
