<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\ProfilMahasiswa;
use App\Models\Rekomendasi;
use App\Models\RiwayatKolaborasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RekomendasiController extends Controller
{
    /**
     * kirimRekomendasi(): maks 500 karakter, bukan untuk diri sendiri, hanya rekan satu tim, dan
     * maksimal satu rekomendasi per rekan per proyek. Proyek dipilih lewat id_postingan (opsional:
     * bila kosong dipakai proyek bersama terbaru yang belum direkomendasikan).
     */
    public function store(Request $request, ProfilMahasiswa $profil): RedirectResponse
    {
        $data = $request->validate([
            'isi_rekomendasi' => ['required', 'string', 'max:500'],
            'id_postingan' => ['nullable', 'integer'],
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

        // Proyek yang dikerjakan bersama, urut dari yang terbaru.
        $bersama = RiwayatKolaborasi::where('id_profil', $profil->id_profil)
            ->whereIn('id_postingan', RiwayatKolaborasi::where('id_profil', $saya->id_profil)->select('id_postingan'))
            ->orderByDesc('tanggal_selesai')
            ->pluck('id_postingan')
            ->all();

        $sudah = Rekomendasi::where('id_profil_pemberi', $saya->id_profil)
            ->where('id_profil_penerima', $profil->id_profil)
            ->whereNotNull('id_postingan')
            ->pluck('id_postingan')
            ->all();

        $idPostingan = $data['id_postingan'] ?? null;

        if ($idPostingan !== null) {
            if (! in_array((int) $idPostingan, $bersama, true)) {
                return back()->withErrors(['isi_rekomendasi' => 'Proyek yang dipilih bukan proyek yang kalian kerjakan bersama.']);
            }
        } else {
            $idPostingan = collect($bersama)->first(fn ($id) => ! in_array($id, $sudah, true)) ?? $bersama[0];
        }

        if (in_array((int) $idPostingan, $sudah, true)) {
            return back()->withErrors(['isi_rekomendasi' => 'Kamu sudah memberi rekomendasi untuk rekan ini pada proyek tersebut.']);
        }

        Rekomendasi::create([
            'id_profil_pemberi' => $saya->id_profil,
            'id_profil_penerima' => $profil->id_profil,
            'id_postingan' => (int) $idPostingan,
            'isi_rekomendasi' => $data['isi_rekomendasi'],
            'tanggal_dikirim' => now()->toDateString(),
            'status_laporan' => 'tidak_dilaporkan',
        ]);

        Notifikasi::kirim(
            $profil->id_profil,
            'Rekomendasi Baru',
            "{$request->user()->name} memberi rekomendasi untukmu.",
            route('profil.show', [], false).'#rekomendasi',
        );

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
