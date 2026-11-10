<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Alias "onboarded": mahasiswa yang belum melewati onboarding diarahkan ke wizard.
 * Pasang di semua route fitur yang butuh profil (Beranda, Lamar, DM, dst).
 */
class EnsureProfilAda
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role !== 'admin' && ! $user->profil()->exists()) {
            return redirect()->route('onboarding.form');
        }

        return $next($request);
    }
}
