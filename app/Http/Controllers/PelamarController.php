<?php

namespace App\Http\Controllers;

use App\Models\Lamaran;
use App\Models\Notifikasi;
use App\Models\Postingan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PelamarController extends Controller
{
    // Daftar pelamar sebuah postingan (UI03)
    public function index(Postingan $postingan): View
    {
        Gate::authorize('kelola', $postingan);

        $daftarLamaran = $postingan->lamaran()
            ->with(['pelamar.user:id,name', 'pelamar.skills'])
            ->orderBy('tanggal_lamar')
            ->orderBy('id_lamaran')
            ->get();

        return view('pelamar.index', [
            'postingan' => $postingan,
            'slotTerisi' => $postingan->slotTerisi(),
            'daftarLamaran' => $daftarLamaran,
        ]);
    }

    // PCT-PC02: profil lengkap pelamar (UI04). Email dan nomor telepon sengaja tidak ditampilkan.
    public function show(Lamaran $lamaran): View
    {
        Gate::authorize('kelola', $lamaran->postingan);

        $profil = $lamaran->pelamar()->with(['user:id,name', 'skills'])->first();

        return view('pelamar.show', [
            'lamaran' => $lamaran,
            'postingan' => $lamaran->postingan,
            'slotTerisi' => $lamaran->postingan->slotTerisi(),
            'profil' => $profil,
            'portofolio' => $profil->portofolio()
                ->orderBy('urutan_halaman')
                ->get(['urutan_halaman', 'konten', 'tautan_eksternal']),
        ]);
    }

    // PCT-PC03: terima pelamar
    public function terima(Lamaran $lamaran): RedirectResponse
    {
        return $this->putuskan($lamaran, 'Diterima', 'Lamaran Anda telah diterima');
    }

    // PCT-PC04: tolak pelamar
    public function tolak(Lamaran $lamaran): RedirectResponse
    {
        return $this->putuskan($lamaran, 'Ditolak', 'Lamaran Anda belum dapat diterima');
    }

    // Ubah status lamaran lalu kirim notifikasi hasil keputusan ke pelamar
    private function putuskan(Lamaran $lamaran, string $keputusan, string $pesan): RedirectResponse
    {
        Gate::authorize('kelola', $lamaran->postingan);

        $galat = DB::transaction(function () use ($lamaran, $keputusan, $pesan) {
            $postingan = Postingan::lockForUpdate()->find($lamaran->id_postingan);
            $terkini = Lamaran::lockForUpdate()->find($lamaran->id_lamaran);

            if ($terkini->status !== 'Menunggu Konfirmasi') {
                return 'Lamaran ini sudah diputuskan';
            }

            if ($keputusan === 'Diterima' && $postingan->slotTerisi() >= $postingan->jumlah_anggota) {
                return 'Slot anggota sudah penuh';
            }

            $terkini->update(['status' => $keputusan]);
            Notifikasi::kirim($terkini->id_pelamar, $keputusan, $pesan);

            return null;
        });

        if ($galat !== null) {
            return back()->withErrors(['keputusan' => $galat]);
        }

        return back()->with([
            'status' => 'Keputusan berhasil disimpan',
            'status_lamaran' => $keputusan,
        ]);
    }
}
