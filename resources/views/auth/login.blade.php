<x-guest-layout>
    <div class="mb-5">
        <h1 class="kub-heading">Masuk ke KompakUB</h1>
        <p class="kub-muted">Gunakan akun mahasiswa UB kamu.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Email Kampus UB" />
            <x-text-input id="email" icon="mail" class="block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@student.ub.ac.id" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Kata Sandi" />
            <x-password-input id="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me + Lupa sandi -->
        <div class="mt-4 flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-brand shadow-sm focus:ring-brand" name="remember">
                <span class="ms-2 text-sm text-gray-600">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a class="kub-link text-sm" href="{{ route('password.request') }}">Lupa kata sandi?</a>
            @endif
        </div>

        <x-primary-button class="mt-6 w-full">
            Masuk
            <x-icon name="arrow-right" class="h-4 w-4" />
        </x-primary-button>

        @if (Route::has('register'))
            <p class="mt-4 text-center text-xs text-gray-500">
                Belum punya akun?
                <a class="kub-link" href="{{ route('register') }}">Daftar di sini</a>
            </p>
        @endif
    </form>
</x-guest-layout>
