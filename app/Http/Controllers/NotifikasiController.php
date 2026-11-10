<?php

namespace App\Http\Controllers;

use App\Models\Lamaran;
use App\Models\Notifikasi;
use App\Models\Postingan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotifikasiController extends Controller
{
    // Halaman Permintaan Kolaborasi (UI06): notifikasi, permintaan masuk, dan lamaran terkirim
    public function index(Request $request): View
    {
        $idProfil = $this->idProfil($request);

        $notifikasi = Notifikasi::where('id_penerima', $idProfil)
            ->orderByDesc('created_at')
            ->orderByDesc('id_notifikasi')
            ->get();

        $masuk = Postingan::where('id_pembuat', $idProfil)
            ->where('kategori', 'Cari Tim')
            ->withCount([
                'lamaran as pelamar_baru' => fn ($query) => $query->where('status', 'Menunggu Konfirmasi'),
                'lamaran as jumlah_diterima' => fn ($query) => $query->where('status', 'Diterima'),
            ])
            ->orderByDesc('id_postingan')
            ->get();

        $terkirim = Lamaran::where('id_pelamar', $idProfil)
            ->with('postingan.pembuat.user:id,name')
            ->orderByDesc('tanggal_lamar')
            ->orderByDesc('id_lamaran')
            ->get();

        return view('notifikasi.index', [
            'jumlahBelumDibaca' => $notifikasi->where('status_baca', false)->count(),
            'notifikasi' => $notifikasi,
            'masuk' => $masuk->map(fn ($postingan) => [
                'id_postingan' => $postingan->id_postingan,
                'nama_lomba' => $postingan->nama_lomba,
                'waktu_perlombaan' => $postingan->waktu_perlombaan,
                'slot_terisi' => 1 + $postingan->jumlah_diterima,
                'slot_total' => $postingan->jumlah_anggota,
                'pelamar_baru' => $postingan->pelamar_baru,
                'status' => $postingan->status,
            ]),
            'terkirim' => $terkirim->map(fn ($lamaran) => [
                'id_lamaran' => $lamaran->id_lamaran,
                'id_postingan' => $lamaran->id_postingan,
                'nama_lomba' => $lamaran->postingan->nama_lomba,
                'waktu_perlombaan' => $lamaran->postingan->waktu_perlombaan,
                'pembuat' => [
                    'nama' => $lamaran->postingan->pembuat->user->name,
                    'jurusan' => $lamaran->postingan->pembuat->jurusan,
                    'angkatan' => $lamaran->postingan->pembuat->angkatan,
                ],
                'tanggal_lamar' => $lamaran->tanggal_lamar,
                'status' => $lamaran->status,
            ]),
        ]);
    }

    // Tandai satu notifikasi sudah dibaca (hanya milik penerima)
    public function tandaiDibaca(Request $request, Notifikasi $notifikasi): RedirectResponse
    {
        abort_unless($notifikasi->id_penerima === $this->idProfil($request), 403);

        $notifikasi->update(['status_baca' => true]);

        return back();
    }

    // Tandai semua notifikasi milik sendiri sudah dibaca
    public function tandaiSemuaDibaca(Request $request): RedirectResponse
    {
        Notifikasi::where('id_penerima', $this->idProfil($request))
            ->where('status_baca', false)
            ->update(['status_baca' => true]);

        return back();
    }

    private function idProfil(Request $request): int
    {
        $profil = $request->user()->profil;
        abort_if($profil === null, 403);

        return $profil->id_profil;
    }
}
