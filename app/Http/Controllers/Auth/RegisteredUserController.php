<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\EmailKampus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /** registerAkun(): validasi, simpan akun, kirim kode verifikasi 6 digit. */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:100', 'unique:users,email', new EmailKampus],
            'no_telepon' => ['required', 'string', 'regex:/^(\+62|62|0)8[0-9]{8,12}$/', 'max:20'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'no_telepon.regex' => 'Nomor telepon harus berformat Indonesia, misalnya 081234567890.',
        ]);

        $user = User::create($data);
        $user->kirimKodeVerifikasi();

        $request->session()->put('verif_email', $user->email);

        return redirect()->route('verifikasi.form')
            ->with('status', 'Kode verifikasi telah dikirim ke email Anda.');
    }
}
