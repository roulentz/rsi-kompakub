<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" value="Nama Lengkap" />
            <x-text-input id="name" icon="user" class="block w-full" type="text" name="name" :value="old('name')" required autofocus maxlength="100" autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="tanggal_lahir" value="Tanggal Lahir" />
                <x-text-input id="tanggal_lahir" icon="calendar" class="block w-full" type="date" name="tanggal_lahir" :value="old('tanggal_lahir')" required />
                <x-input-error :messages="$errors->get('tanggal_lahir')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="no_telepon" value="Nomor WhatsApp / HP" />
                <x-text-input id="no_telepon" icon="phone" class="block w-full" type="tel" name="no_telepon" :value="old('no_telepon')" required placeholder="081234567890" />
                <x-input-error :messages="$errors->get('no_telepon')" class="mt-2" />
            </div>
        </div>

        <div class="mt-4">
            <x-input-label for="email" value="Email Kampus UB" />
            <x-text-input id="email" icon="mail" class="block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nama@student.ub.ac.id" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Kata Sandi" />
            <x-password-input id="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Ulangi Kata Sandi" />
            <x-password-input id="password_confirmation" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button class="mt-6 w-full">
            Daftar Akun KompakUB
            <x-icon name="arrow-right" class="h-4 w-4" />
        </x-primary-button>

        <p class="mt-4 text-center text-xs text-gray-500">
            Sudah punya akun resmi?
            <a class="kub-link" href="{{ route('login') }}">Masuk di sini</a>
        </p>
    </form>
</x-guest-layout>
