<?php

namespace App\Http\Controllers;

use App\Models\Lamaran;
use App\Models\Notifikasi;
use App\Models\Postingan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PelamarController extends Controller
{
    // Daftar pelamar sebuah postingan (UI03)
    public function index(Postingan $postingan): JsonResponse
    {
        Gate::authorize('kelola', $postingan);

        $daftarLamaran = $postingan->lamaran()
            ->with(['pelamar.user:id,name', 'pelamar.skills'])
            ->orderBy('tanggal_lamar')
            ->orderBy('id_lamaran')
            ->get();

        return response()->json([
            'id_postingan' => $postingan->id_postingan,
            'nama_lomba' => $postingan->nama_lomba,
            'slot_total' => $postingan->jumlah_anggota,
            'slot_terisi' => $postingan->slotTerisi(),
            'jumlah_pelamar' => $daftarLamaran->count(),
            'pelamar' => $daftarLamaran->map(fn ($lamaran) => [
                'id_lamaran' => $lamaran->id_lamaran,
                'id_profil' => $lamaran->pelamar->id_profil,
                'nama' => $lamaran->pelamar->user->name,
                'jurusan' => $lamaran->pelamar->jurusan,
                'angkatan' => $lamaran->pelamar->angkatan,
                'skill' => $lamaran->pelamar->skills->pluck('nama_skill')->values(),
                'status' => $lamaran->status,
            ]),
        ]);
    }

    // PCT-PC02: profil lengkap pelamar (UI04)
    public function show(Lamaran $lamaran): JsonResponse
    {
        Gate::authorize('kelola', $lamaran->postingan);

        $profil = $lamaran->pelamar()->with(['user:id,name', 'skills'])->first();

        $portofolio = $profil->portofolio()
            ->orderBy('urutan_halaman')
            ->get(['urutan_halaman', 'konten', 'tautan_eksternal']);

        return response()->json([
            'id_lamaran' => $lamaran->id_lamaran,
            'status' => $lamaran->status,
            'profil' => [
                'id_profil' => $profil->id_profil,
                'nama' => $profil->user->name,
                'jurusan' => $profil->jurusan,
                'angkatan' => $profil->angkatan,
                'bio' => $profil->bio,
                'foto' => $profil->foto,
                'interest' => $profil->interest,
                'skill' => $profil->skills->pluck('nama_skill')->values(),
                'portofolio' => $portofolio,
            ],
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
