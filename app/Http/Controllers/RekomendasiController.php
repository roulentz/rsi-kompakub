<?php

namespace App\Http\Controllers;

use App\Models\ProfilMahasiswa;
use App\Models\Rekomendasi;
use App\Models\RiwayatKolaborasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RekomendasiController extends Controller
{
    /** kirimRekomendasi(): maks 500 karakter, bukan untuk diri sendiri, hanya rekan satu tim. */
    public function store(Request $request, ProfilMahasiswa $profil): RedirectResponse
    {
        $data = $request->validate([
            'isi_rekomendasi' => ['required', 'string', 'max:500'],
        ], [
            'isi_rekomendasi.max' => 'Rekomendasi maksimal 500 karakter.',
        ]);

        $saya = $request->user()->profil;
        abort_if($saya === null, 403);

        if ($saya->id_profil === $profil->id_profil) {
            return back()->withErrors(['isi_rekomendasi' => 'Anda tidak dapat memberi rekomendasi untuk diri sendiri.']);
        }

        if (! RiwayatKolaborasi::rekanSatuTim($saya->id_profil, $profil->id_profil)) {
            return back()->withErrors(['isi_rekomendasi' => 'Rekomendasi hanya dapat diberikan kepada rekan satu tim pada proyek yang sudah selesai.']);
        }

        Rekomendasi::create([
            'id_profil_pemberi' => $saya->id_profil,
            'id_profil_penerima' => $profil->id_profil,
            'isi_rekomendasi' => $data['isi_rekomendasi'],
            'tanggal_dikirim' => now()->toDateString(),
            'status_laporan' => 'tidak_dilaporkan',
        ]);

        return back()->with('status', 'Rekomendasi terkirim.');
    }

    /** kirimLaporanKeAdmin(): hanya penerima rekomendasi yang boleh melapor. */
    public function laporkan(Request $request, Rekomendasi $rekomendasi): RedirectResponse
    {
        abort_unless($rekomendasi->id_profil_penerima === $request->user()->profil?->id_profil, 403);

        if ($rekomendasi->status_laporan !== 'tidak_dilaporkan') {
            return back()->with('status', 'Rekomendasi ini sudah pernah dilaporkan.');
        }

        $rekomendasi->update(['status_laporan' => 'dilaporkan']);

        return back()->with('status', 'Laporan dikirim ke administrator.');
    }

    /** getLaporanMasuk() (admin) */
    public function laporan(): View
    {
        return view('rekomendasi.laporan', [
            'laporan' => Rekomendasi::where('status_laporan', 'dilaporkan')
                ->with(['pemberi.user', 'penerima.user'])
                ->orderBy('id_rekomendasi')
                ->get(),
        ]);
    }

    /** tinjauLaporanRekomendasi() (admin): pertahankan atau hapus. */
    public function tinjau(Request $request, Rekomendasi $rekomendasi): RedirectResponse
    {
        $aksi = $request->validate(['aksi' => ['required', 'in:pertahankan,hapus']])['aksi'];

        if ($aksi === 'hapus') {
            $rekomendasi->delete();
            $pesan = 'Rekomendasi dihapus.';
        } else {
            $rekomendasi->update(['status_laporan' => 'ditinjau_dipertahankan']);
            $pesan = 'Rekomendasi dipertahankan.';
        }

        return back()->with('status', $pesan);
    }
}
