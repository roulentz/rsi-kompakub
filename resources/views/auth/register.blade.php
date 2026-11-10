<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" value="Nama lengkap" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus maxlength="100" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="tanggal_lahir" value="Tanggal lahir" />
            <x-text-input id="tanggal_lahir" class="block mt-1 w-full" type="date" name="tanggal_lahir" :value="old('tanggal_lahir')" required />
            <x-input-error :messages="$errors->get('tanggal_lahir')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" value="Email mahasiswa UB" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nama@student.ub.ac.id" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="no_telepon" value="Nomor telepon" />
            <x-text-input id="no_telepon" class="block mt-1 w-full" type="tel" name="no_telepon" :value="old('no_telepon')" required placeholder="081234567890" />
            <x-input-error :messages="$errors->get('no_telepon')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Kata sandi" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Ulangi kata sandi" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">Sudah punya akun?</a>
            <x-primary-button class="ms-4">Daftar</x-primary-button>
        </div>
    </form>
</x-guest-layout>
