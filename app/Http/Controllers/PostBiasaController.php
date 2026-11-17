<?php

namespace App\Http\Controllers;

use App\Models\Postingan;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class PostBiasaController extends Controller
{
    // Formulir buat postingan kini ada di Beranda (komposer dengan pilihan kategori).
    public function create()
    {
        return redirect()->route('dashboard')->withFragment('buat-postingan');
    }

    // Menyimpan postingan umum (kategori Biasa) atau Info Lomba.
    // Teks maksimal 1000 karakter, foto opsional. Cari Tim disimpan oleh PostinganController (SH).
    public function store(Request $request)
    {
        $request->validate([
            'kategori' => ['nullable', 'in:Biasa,Info Lomba'],
            'deskripsi' => ['required', 'string', 'max:1000'],
            'nama_lomba' => ['required_if:kategori,Info Lomba', 'nullable', 'string', 'max:100'],
            'waktu_perlombaan' => ['required_if:kategori,Info Lomba', 'nullable', 'date'],
            ...Postingan::aturanFoto($request),
        ], [
            'deskripsi.required' => 'Isi postingan tidak boleh kosong.',
            'deskripsi.max' => 'Isi postingan maksimal 1000 karakter.',
            'nama_lomba.required_if' => 'Nama lomba wajib diisi untuk Info Lomba.',
            'waktu_perlombaan.required_if' => 'Tanggal lomba wajib diisi untuk Info Lomba.',
            ...Postingan::pesanFoto($request),
        ]);

        $profil = Auth::user()->profil;

        if (! $profil) {
            return redirect()->route('dashboard')->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        $infoLomba = $request->input('kategori') === 'Info Lomba';

        $postingan = Postingan::create([
            'id_pembuat' => $profil->id_profil,
            'kategori' => $infoLomba ? 'Info Lomba' : 'Biasa',
            'nama_lomba' => $infoLomba ? $request->nama_lomba : null,
            'waktu_perlombaan' => $infoLomba ? $request->waktu_perlombaan : null,
            'deskripsi' => $request->deskripsi,
            'status' => 'Aktif',
        ]);

        $postingan->simpanFoto(Arr::wrap($request->file('foto')));

        // Tampilkan tab Semua agar postingan baru langsung terlihat di paling atas.
        return redirect()->route('dashboard', ['kategori' => 'Semua'])
            ->with('status', $infoLomba ? 'Info lomba berhasil dibagikan!' : 'Postingan berhasil dibagikan!');
    }
}
