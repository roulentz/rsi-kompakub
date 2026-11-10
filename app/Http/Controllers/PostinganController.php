<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostinganRequest;
use App\Models\Postingan;
use App\Models\RiwayatKolaborasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PostinganController extends Controller
{
    // PCT-PC01: simpan postingan cari tim lalu terbitkan
    public function store(StorePostinganRequest $request): RedirectResponse
    {
        $postingan = Postingan::create([
            ...$request->validated(),
            'id_pembuat' => $request->user()->profil->id_profil,
            'kategori' => 'Cari Tim',
        ]);

        return redirect('/')->with([
            'status' => 'Postingan berhasil diterbitkan',
            'id_postingan' => $postingan->id_postingan,
        ]);
    }

    // PCT-PC01: daftar rekomendasi kandidat untuk postingan cari tim
    public function rekomendasi(Postingan $postingan): JsonResponse
    {
        Gate::authorize('kelola', $postingan);
        abort_unless($postingan->kategori === 'Cari Tim', 404);

        $kandidat = $postingan->rekomendasiKandidat()->map(fn ($profil) => [
            'id_profil' => $profil->id_profil,
            'nama' => $profil->user->name,
            'jurusan' => $profil->jurusan,
            'angkatan' => $profil->angkatan,
            'foto' => $profil->foto,
            'skill' => $profil->skills->pluck('nama_skill')->values(),
            'skill_cocok' => $profil->skill_cocok,
        ]);

        return response()->json([
            'id_postingan' => $postingan->id_postingan,
            'kandidat' => $kandidat,
        ]);
    }

    // PCT-PC05: tandai proyek selesai lalu catat riwayat kolaborasi pembuat dan anggota diterima
    public function selesai(Postingan $postingan): RedirectResponse
    {
        Gate::authorize('kelola', $postingan);
        abort_unless($postingan->kategori === 'Cari Tim', 404);

        $galat = DB::transaction(function () use ($postingan) {
            $terkini = Postingan::lockForUpdate()->find($postingan->id_postingan);

            if ($terkini->status === 'Selesai') {
                return 'Proyek tidak dapat ditandai selesai karena sudah selesai';
            }

            $idAnggota = array_map('intval', $terkini->lamaran()->where('status', 'Diterima')->pluck('id_pelamar')->all());

            if ($idAnggota === []) {
                return 'Proyek tidak dapat ditandai selesai karena belum ada anggota yang diterima';
            }

            $terkini->update(['status' => 'Selesai']);

            RiwayatKolaborasi::catatProyekSelesai(
                $terkini->id_postingan,
                (string) $terkini->nama_lomba,
                $terkini->id_pembuat,
                $idAnggota,
            );

            return null;
        });

        if ($galat !== null) {
            return back()->withErrors(['selesai' => $galat]);
        }

        return back()->with('status', 'Proyek berhasil ditandai selesai');
    }
}
