<?php

namespace App\Http\Controllers;

use App\Models\Postingan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostBiasaController extends Controller
{
    // Post biasa (teks maksimal 1000 karakter dan foto opsional)
    public function store(Request $request)
    {
        $request->validate([
            'deskripsi' => ['required', 'string', 'max:1000'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'deskripsi.required' => 'Isi postingan tidak boleh kosong.',
            'deskripsi.max' => 'Isi postingan maksimal 1000 karakter.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        $profil = Auth::user()->profil;

        if (!$profil) {
            return redirect()->route('dashboard')->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('postingan', 'public');
        }

        Postingan::create([
            'id_pembuat' => $profil->id_profil,
            'kategori' => 'Biasa',
            'deskripsi' => $request->deskripsi,
            'foto' => $fotoPath,
            'status' => 'Aktif',
        ]);

        return redirect()->route('dashboard')->with('status', 'Postingan berhasil dibagikan!');
    }
}
