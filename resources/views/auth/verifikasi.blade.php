<x-guest-layout>
    <x-flash />

    <div class="mb-4">
        <h1 class="kub-heading">Verifikasi Email</h1>
        <p class="kub-muted mt-1">
            Masukkan kode 6 digit yang dikirim ke <strong class="text-navy-deep">{{ $email }}</strong>. Kode berlaku 10 menit.
        </p>
    </div>

    <form method="POST" action="{{ route('verifikasi.proses') }}">
        @csrf
        <x-input-label for="kode" value="Kode verifikasi" />
        <x-text-input id="kode" class="mt-1 block w-full text-center text-lg tracking-widest" type="text" name="kode" inputmode="numeric" maxlength="6" required autofocus />
        <x-input-error :messages="$errors->get('kode')" class="mt-2" />

        <x-primary-button class="mt-6 w-full">Verifikasi</x-primary-button>
    </form>

    <form method="POST" action="{{ route('verifikasi.kirim-ulang') }}" class="mt-4 text-center">
        @csrf
        <button class="kub-link text-xs">Kirim ulang kode</button>
    </form>
</x-guest-layout>
