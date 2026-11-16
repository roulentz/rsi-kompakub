<?php

namespace App\Http\Controllers;

use App\Models\Postingan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class ModerasiController extends Controller
{
    // MOD-PC01: Hapus postingan
    public function destroy(Postingan $postingan): RedirectResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        try {
            $postingan->delete();

            return redirect('/dashboard')
                ->with('status', 'Postingan berhasil dihapus.');
        } catch (Throwable $e) {
            Log::error('Gagal menghapus postingan', [
                'id_postingan' => $postingan->id_postingan,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'moderasi' => 'Postingan gagal dihapus. Silakan coba lagi.',
            ]);
        }
    }
}
