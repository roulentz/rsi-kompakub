<?php

namespace App\Http\Controllers;

use App\Models\Lamaran;
use App\Models\Notifikasi;
use App\Models\Postingan;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LamaranController extends Controller
{
    // EFP-PC04 & EFP-PC05: Validasi & simpan lamaran + kirim notifikasi ke pembuat postingan
    public function store(Request $request, int $postingan_id): RedirectResponse
    {
        $profil = $request->user()->profil;
        abort_if($profil === null, 403);

        $postingan = Postingan::with('pembuat.user')->findOrFail($postingan_id);
        abort_unless($postingan->kategori === 'Cari Tim', 404);
        abort_if($postingan->id_pembuat === $profil->id_profil, 403);

        if ($postingan->status === 'Selesai') {
            return back()->withErrors(['lamaran' => 'Postingan ini sudah selesai dan tidak menerima lamaran']);
        }

        try {
            // Lamaran dan notifikasi disimpan bersamaan, atau tidak sama sekali
            DB::transaction(function () use ($postingan, $profil) {
                $lamaran = Lamaran::create([
                    'id_postingan' => $postingan->id_postingan,
                    'id_pelamar' => $profil->id_profil,
                ]);

                Notifikasi::kirim(
                    $postingan->id_pembuat,
                    'Pelamar Baru',
                    'Terdapat pelamar baru pada postingan Anda'
                );

                // Khusus alur demo: saat pelamar MZ mengirim lamaran, sistem otomatis memicu notifikasi keputusan diterima
                // Cek email spesifik agar tidak terpicu di environment pengujian
                if (! app()->runningUnitTests() && $profil->user?->email === 'zikraaditya@student.ub.ac.id') {
                    $lamaran->update(['status' => 'Diterima']);

                    Notifikasi::kirim(
                        $profil->id_profil,
                        'Diterima',
                        'Selamat! Lamaran Anda untuk postingan ' . ($postingan->nama_lomba ?? 'Cari Tim') . ' telah DITERIMA oleh ' . ($postingan->pembuat->user->name ?? 'pembuat tim') . '.',
                        '/postingan/' . $postingan->id_postingan
                    );
                }
            });
        } catch (UniqueConstraintViolationException) {
            return back()->withErrors(['lamaran' => 'Anda sudah melamar ke postingan ini']);
        }

        return back()->with([
            'status' => 'Lamaran berhasil diajukan',
            'status_lamaran' => 'Menunggu Konfirmasi',
        ]);
    }
}
