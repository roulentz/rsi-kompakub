<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('dashboard') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Buat Postingan Biasa
            </h2>
        </div>
    </x-slot>

    <div class="py-8 max-w-2xl mx-auto px-4 sm:px-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <form action="{{ route('post-biasa.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- Input Teks Postingan --}}
                <div class="space-y-2">
                    <label for="deskripsi" class="block text-sm font-semibold text-gray-700">
                        Isi Postingan <span class="text-rose-500">*</span>
                    </label>
                    <textarea
                        id="deskripsi"
                        name="deskripsi"
                        rows="6"
                        maxlength="1000"
                        placeholder="Tuliskan pemikiran, pembaruan proyek, atau ide Anda di sini... (maksimal 1000 karakter)"
                        class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm @error('deskripsi') border-rose-400 @enderror"
                        required>{{ old('deskripsi') }}</textarea>
                    <div class="flex justify-between items-center text-xs text-gray-400">
                        <span>Maksimal 1000 karakter</span>
                        @error('deskripsi')
                            <span class="text-rose-500 font-medium">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Upload Foto --}}
                <div class="space-y-2">
                    <label for="foto" class="block text-sm font-semibold text-gray-700">
                        Unggah Foto <span class="text-xs text-gray-400 font-normal">(Opsional, maks. 2 MB)</span>
                    </label>
                    <input
                        id="foto"
                        type="file"
                        name="foto"
                        accept="image/*"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition cursor-pointer">
                    @error('foto')
                        <p class="text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                    <a
                        href="{{ route('dashboard') }}"
                        class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl text-sm transition">
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-sm transition shadow-sm">
                        Publikasikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
