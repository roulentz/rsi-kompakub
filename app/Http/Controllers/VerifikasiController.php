<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class VerifikasiController extends Controller
{
    public function form(Request $request): View|RedirectResponse
    {
        $email = $request->session()->get('verif_email');

        return $email
            ? view('auth.verifikasi', ['email' => $email])
            : redirect()->route('login');
    }

    /** verifikasiKodeAkun() */
    public function proses(Request $request): RedirectResponse
    {
        $request->validate(['kode' => ['required', 'digits:6']]);

        $user = User::where('email', $request->session()->get('verif_email'))->first();

        if (! $user || $user->email_verified_at !== null) {
            return redirect()->route('login');
        }

        if (! $user->cocokKode($request->string('kode')->toString())) {
            throw ValidationException::withMessages([
                'kode' => 'Kode salah atau sudah kedaluwarsa (berlaku 10 menit). Minta kode baru bila perlu.',
            ]);
        }

        $user->tandaiTerverifikasi();
        $request->session()->forget('verif_email');

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('onboarding.form')
            ->with('status', 'Email terverifikasi. Silakan lengkapi profil Anda.');
    }

    public function kirimUlang(Request $request): RedirectResponse
    {
        $user = User::where('email', $request->session()->get('verif_email'))->first();

        if (! $user || $user->email_verified_at !== null) {
            return redirect()->route('login');
        }

        $user->kirimKodeVerifikasi();

        return back()->with('status', 'Kode baru telah dikirim.');
    }
}
