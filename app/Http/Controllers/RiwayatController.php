<?php

namespace App\Http\Controllers;

use App\Models\RiwayatKolaborasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    /** Anggota mengubah perannya sendiri di riwayat kolaborasi (pencapaian tetap milik tim). */
    public function update(Request $request, RiwayatKolaborasi $riwayat): RedirectResponse
    {
        abort_unless($riwayat->id_profil === $request->user()->profil?->id_profil, 403);

        $data = $request->validate([
            'peran' => ['required', 'string', 'max:100'],
        ], [
            'peran.required' => 'Peran tidak boleh kosong.',
            'peran.max' => 'Peran maksimal 100 karakter.',
        ]);

        $riwayat->update(['peran' => trim($data['peran'])]);

        return redirect()->to(route('profil.show', [], false).'#riwayat')->with('status', 'Peran diperbarui.');
    }
}
