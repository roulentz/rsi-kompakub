<?php

namespace App\Http\Controllers;

use App\Models\ProfilMahasiswa;
use App\Models\RiwayatKolaborasi;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function show(Request $request): RedirectResponse
    {
        $profil = $request->user()->profil;
        abort_if($profil === null, 404);

        return redirect()->route('profil.lihat', $profil);
    }

    /** getHalamanProfil(): profil sendiri maupun profil orang lain. */
    public function lihat(Request $request, ProfilMahasiswa $profil): View
    {
        $profil->load([
            'user',
            'skills',
            'portofolio' => fn ($q) => $q->orderBy('urutan_halaman'),
        ]);

        $saya = $request->user()->profil;
        $pemilik = $saya && $saya->id_profil === $profil->id_profil;

        return view('profil.show', [
            'profil' => $profil,
            'pemilik' => $pemilik,
            'riwayat' => $profil->riwayat()->orderByDesc('tanggal_selesai')->get(),
            'rekomendasi' => $profil->rekomendasiDiterima()->with('pemberi.user')->orderByDesc('id_rekomendasi')->get(),
            'bolehRekomendasi' => $saya && ! $pemilik
                && RiwayatKolaborasi::rekanSatuTim($saya->id_profil, $profil->id_profil),
        ]);
    }

    /** getFormProfil() */
    public function edit(Request $request): View
    {
        return view('profil.edit', [
            'profil' => $request->user()->profil->load('skills'),
            'skills' => Skill::orderBy('nama_skill')->get(),
        ]);
    }

    /** simpanPerubahanProfil() */
    public function update(Request $request): RedirectResponse
    {
        $profil = $request->user()->profil;

        $data = $request->validate([
            'jurusan' => ['nullable', 'string', 'max:100'],
            'angkatan' => ['nullable', 'string', 'max:10'],
            'bio' => ['nullable', 'string', 'max:200'],
            'interest' => ['nullable', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['integer', 'exists:skills,id_skill'],
        ], [
            'bio.max' => 'Bio maksimal 200 karakter.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        if ($request->hasFile('foto')) {
            if ($profil->foto) {
                Storage::disk('public')->delete($profil->foto);
            }
            $profil->foto = $request->file('foto')->store('foto', 'public');
        }

        $profil->fill([
            'jurusan' => $data['jurusan'] ?? null,
            'angkatan' => $data['angkatan'] ?? null,
            'bio' => $data['bio'] ?? null,
            'interest' => $data['interest'] ?? null,
            'status_default' => false,
        ]);
        $profil->last_updated = now();
        $profil->save();

        $profil->skills()->sync($data['skills'] ?? []);

        return redirect()->route('profil.show')->with('status', 'Profil berhasil diperbarui.');
    }
}
