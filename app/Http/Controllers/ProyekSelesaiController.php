<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Postingan;
use App\Models\RiwayatKolaborasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

/**
 * catatRiwayatKolaborasi(): proyek ditandai selesai oleh pembuat tim.
 * Riwayat dicatat untuk pembuat ('Pembuat Tim') dan semua pelamar berstatus Diterima ('Anggota Tim').
 */
class ProyekSelesaiController extends Controller
{
    public function __invoke(Postingan $postingan): RedirectResponse
    {
        Gate::authorize('kelola', $postingan);
        abort_unless($postingan->kategori === 'Cari Tim', 404);

        if ($postingan->status === 'Selesai') {
            return back()->with('status', 'Proyek ini sudah ditandai selesai.');
        }

        $judul = $postingan->nama_lomba ?: (Str::limit((string) $postingan->deskripsi, 60) ?: 'Proyek tim');

        DB::transaction(function () use ($postingan, $judul) {
            $postingan->update(['status' => 'Selesai']);

            $anggota = $postingan->lamaran()->where('status', 'Diterima')->pluck('id_pelamar')->all();

            RiwayatKolaborasi::catatProyekSelesai($postingan->id_postingan, $judul, $postingan->id_pembuat, $anggota);

            foreach ($anggota as $idProfil) {
                Notifikasi::kirim($idProfil, 'Proyek Selesai', "Proyek \"{$judul}\" telah selesai. Anda kini dapat memberi rekomendasi kepada rekan tim.");
            }
        });

        return back()->with('status', 'Proyek ditandai selesai dan riwayat kolaborasi tercatat.');
    }
}
