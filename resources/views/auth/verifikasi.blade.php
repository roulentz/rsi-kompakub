<x-guest-layout>
    <x-flash />

    <p class="mb-4 text-sm text-gray-600">
        Masukkan kode 6 digit yang dikirim ke <strong>{{ $email }}</strong>. Kode berlaku 10 menit.
    </p>

    <form method="POST" action="{{ route('verifikasi.proses') }}">
        @csrf
        <x-input-label for="kode" value="Kode verifikasi" />
        <x-text-input id="kode" class="block mt-1 w-full tracking-widest text-center text-lg" type="text" name="kode" inputmode="numeric" maxlength="6" required autofocus />
        <x-input-error :messages="$errors->get('kode')" class="mt-2" />

        <div class="mt-4 flex justify-end">
            <x-primary-button>Verifikasi</x-primary-button>
        </div>
    </form>

    <form method="POST" action="{{ route('verifikasi.kirim-ulang') }}" class="mt-3">
        @csrf
        <button class="underline text-sm text-gray-600 hover:text-gray-900">Kirim ulang kode</button>
    </form>
</x-guest-layout>
