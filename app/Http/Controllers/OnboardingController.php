<?php

namespace App\Http\Controllers;

use App\Models\ProfilMahasiswa;
use App\Models\Skill;
use App\Services\PortofolioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($request->user()->profil()->exists()) {
            return redirect()->route('profil.show');
        }

        return view('onboarding.index', ['skills' => Skill::orderBy('nama_skill')->get()]);
    }

    /**
     * prosesOnboarding(): data yang sudah diisi di wizard langsung disimpan ke profil, baik lewat
     * "Simpan" maupun "Lewati". Hanya bagian yang dilewati yang kosong. Profil baru berstatus
     * "kosong" (status_default) jika tidak ada satu pun data yang diisi.
     */
    public function proses(Request $request, PortofolioService $portofolio): RedirectResponse
    {
        $user = $request->user();

        if ($user->profil()->exists()) {
            return redirect()->route('profil.show');
        }

        $data = $request->validate([
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'jurusan' => ['nullable', 'string', 'max:100'],
            'angkatan' => ['nullable', 'string', 'max:10'],
            'bio' => ['nullable', 'string', 'max:200'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['integer', 'exists:skills,id_skill'],
            'interest' => ['nullable', 'string', 'max:255'],
            'halaman' => ['nullable', 'array', 'max:'.PortofolioService::MAKS_HALAMAN],
            'halaman.*' => ['file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ], [
            'bio.max' => 'Bio maksimal 200 karakter.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
            'halaman.*.max' => 'Setiap halaman portofolio maksimal 5 MB.',
            'halaman.*.mimes' => 'Halaman portofolio harus berformat JPG atau PNG.',
        ]);

        $jurusan = trim($data['jurusan'] ?? '') ?: null;
        $angkatan = trim($data['angkatan'] ?? '') ?: null;
        $bio = trim($data['bio'] ?? '') ?: null;
        $interest = trim($data['interest'] ?? '') ?: null;
        $skills = $data['skills'] ?? [];
        $halaman = $request->file('halaman', []);

        $adaIsian = $jurusan || $angkatan || $bio || $interest
            || $skills !== [] || $halaman !== [] || $request->hasFile('foto');

        DB::transaction(function () use ($user, $request, $portofolio, $jurusan, $angkatan, $bio, $interest, $skills, $halaman, $adaIsian) {
            $profil = ProfilMahasiswa::create([
                'user_id' => $user->id,
                'jurusan' => $jurusan,
                'angkatan' => $angkatan,
                'bio' => $bio,
                'interest' => $interest,
                'foto' => $request->hasFile('foto') ? $request->file('foto')->store('foto', 'public') : null,
                'status_default' => ! $adaIsian,
            ]);

            $profil->skills()->sync($skills);

            foreach ($halaman as $file) {
                $portofolio->tambah($profil, $file);
            }
        });

        if (! $adaIsian) {
            return redirect()->route('dashboard')
                ->with('status', 'Profil dibuat kosong. Anda dapat melengkapinya kapan saja lewat menu Profil.');
        }

        return redirect()->route('profil.show')->with('status', 'Profil berhasil dibuat.');
    }
}
