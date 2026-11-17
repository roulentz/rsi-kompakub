<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostinganRequest;
use App\Models\Notifikasi;
use App\Models\Postingan;
use App\Models\RiwayatKolaborasi;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PostinganController extends Controller
{
    // PCT-PC01: simpan postingan cari tim lalu terbitkan
    public function store(StorePostinganRequest $request): RedirectResponse
    {
        $data = collect($request->validated())->except('foto')->all();

        $postingan = Postingan::create([
            ...$data,
            'id_pembuat' => $request->user()->profil->id_profil,
            'kategori' => 'Cari Tim',
        ]);

        $postingan->simpanFoto(Arr::wrap($request->file('foto')));

        return redirect()->route('postingan.show', $postingan->id_postingan)->with([
            'status' => 'Postingan berhasil diterbitkan',
            'id_postingan' => $postingan->id_postingan,
        ]);
    }

    // PCT-PC01: daftar rekomendasi kandidat untuk postingan cari tim
    public function rekomendasi(Postingan $postingan): View
    {
        Gate::authorize('kelola', $postingan);
        abort_unless($postingan->kategori === 'Cari Tim', 404);

        return view('postingan.rekomendasi', [
            'postingan' => $postingan,
            'slotTerisi' => $postingan->slotTerisi(),
            'kandidat' => $postingan->rekomendasiKandidat(),
        ]);
    }

    // PCT-PC05: tandai proyek selesai (pada/setelah tanggal lomba) lalu catat riwayat dan kirim notifikasi ke anggota
    public function selesai(Request $request, Postingan $postingan): RedirectResponse
    {
        Gate::authorize('kelola', $postingan);
        abort_unless($postingan->kategori === 'Cari Tim', 404);

        $data = $request->validate([
            'pencapaian' => ['required', Rule::in(Postingan::PENCAPAIAN)],
            'nama_proyek' => ['nullable', 'string', 'max:150'],
            'peran_saya' => ['nullable', 'string', 'max:100'],
        ], [
            'pencapaian.required' => 'Pilih pencapaian tim (atau Peserta bila belum menang).',
            'pencapaian.in' => 'Pencapaian yang dipilih tidak dikenal.',
            'nama_proyek.max' => 'Nama proyek maksimal 150 karakter.',
            'peran_saya.max' => 'Peran maksimal 100 karakter.',
        ]);

        $galat = DB::transaction(function () use ($postingan, $data) {
            $terkini = Postingan::lockForUpdate()->find($postingan->id_postingan);

            if ($terkini->status === 'Selesai') {
                return 'Proyek tidak dapat ditandai selesai karena sudah selesai';
            }

            if (! $terkini->lombaSudahLewat()) {
                return 'Proyek baru bisa ditandai selesai pada atau setelah tanggal lomba ('
                    .$terkini->waktu_perlombaan->locale('id')->translatedFormat('d F Y').')';
            }

            $idAnggota = array_map('intval', $terkini->lamaran()->where('status', 'Diterima')->pluck('id_pelamar')->all());

            if ($idAnggota === []) {
                return 'Proyek tidak dapat ditandai selesai karena belum ada anggota yang diterima';
            }

            $terkini->update([
                'status' => 'Selesai',
                'pencapaian' => $data['pencapaian'],
                'nama_proyek' => trim($data['nama_proyek'] ?? '') ?: null,
            ]);

            // Tanggal selesai di riwayat = tanggal lomba (bila ada).
            RiwayatKolaborasi::catatProyekSelesai(
                $terkini->id_postingan,
                (string) $terkini->nama_lomba,
                $terkini->id_pembuat,
                $idAnggota,
                $terkini->waktu_perlombaan?->toDateString(),
                trim($data['peran_saya'] ?? '') ?: null,
            );

            foreach (array_diff($idAnggota, [$terkini->id_pembuat]) as $idAnggotaProfil) {
                Notifikasi::kirim(
                    $idAnggotaProfil,
                    'Proyek Selesai',
                    "Proyek \"{$terkini->nama_lomba}\" selesai dengan pencapaian {$terkini->pencapaian}. Atur perananmu di Riwayat Kolaborasi dan beri rekomendasi untuk rekan tim.",
                    route('profil.show', [], false).'#riwayat',
                );
            }

            return null;
        });

        if ($galat !== null) {
            return back()->withErrors(['selesai' => $galat]);
        }

        return back()->with('status', 'Proyek berhasil ditandai selesai');
    }

    // Koreksi pencapaian atau nama proyek setelah selesai (misalnya salah pilih juara). Hanya pembuat.
    public function perbaruiDetail(Request $request, Postingan $postingan): RedirectResponse
    {
        Gate::authorize('kelola', $postingan);
        abort_unless($postingan->kategori === 'Cari Tim' && $postingan->status === 'Selesai', 404);

        $data = $request->validate([
            'pencapaian' => ['required', Rule::in(Postingan::PENCAPAIAN)],
            'nama_proyek' => ['nullable', 'string', 'max:150'],
        ]);

        $postingan->update([
            'pencapaian' => $data['pencapaian'],
            'nama_proyek' => trim($data['nama_proyek'] ?? '') ?: null,
        ]);

        return back()->with('status', 'Detail proyek diperbarui');
    }
}
