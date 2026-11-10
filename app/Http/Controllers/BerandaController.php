<?php

namespace App\Http\Controllers;

use App\Models\Postingan;
use App\Models\PreferensiUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BerandaController extends Controller
{
    // EFP-PC01 & EFP-PC02: Mengambil dan menyaring feed beranda
    public function index(Request $request)
    {
        $user = Auth::user();
        $profil = $user->profil;

        // Ambil atau inisialisasi preferensi filter user
        $preferensi = null;
        if ($profil) {
            $preferensi = PreferensiUser::firstOrCreate(
                ['id_profil' => $profil->id_profil],
                ['kategori_terakhir' => 'Semua']
            );
        }

        // Tentukan kategori yang aktif: dari parameter URL (jika ada) atau dari preferensi terakhir
        $kategoriAktif = $request->query('kategori');

        if ($kategoriAktif) {
            $kategoriValid = ['Semua', 'Cari Tim', 'Info Lomba'];
            if (!in_array($kategoriAktif, $kategoriValid)) {
                $kategoriAktif = 'Semua';
            }

            // EFP-PC03: Simpan preferensi filter terakhir
            if ($preferensi && $preferensi->kategori_terakhir !== $kategoriAktif) {
                $preferensi->update(['kategori_terakhir' => $kategoriAktif]);
            }
        } else {
            $kategoriAktif = $preferensi ? $preferensi->kategori_terakhir : 'Semua';
        }

        // Query postingan
        $query = Postingan::with(['pembuat.user', 'lamaran'])
            ->orderByDesc('created_at');

        if ($kategoriAktif !== 'Semua') {
            $query->where('kategori', $kategoriAktif);
        }

        $postingan = $query->paginate(10)->withQueryString();

        return view('beranda.index', compact('postingan', 'kategoriAktif', 'profil'));
    }

    // Detail postingan
    public function show(Postingan $postingan)
    {
        $postingan->load(['pembuat.user', 'pembuat.skills', 'lamaran']);

        $user = Auth::user();
        $profil = $user->profil;

        // Cek apakah user saat ini sudah pernah melamar di postingan ini
        $sudahMelamar = false;
        $statusLamaran = null;
        if ($profil) {
            $lamaranSaya = $postingan->lamaran()
                ->where('id_pelamar', $profil->id_profil)
                ->first();

            if ($lamaranSaya) {
                $sudahMelamar = true;
                $statusLamaran = $lamaranSaya->status;
            }
        }

        return view('beranda.show', compact('postingan', 'profil', 'sudahMelamar', 'statusLamaran'));
    }
}
