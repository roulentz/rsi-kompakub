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

    /** prosesOnboarding(): aksi "lengkapi" menyimpan data, "lewati" membuat profil default kosong. */
    public function proses(Request $request, PortofolioService $portofolio): RedirectResponse
    {
        $user = $request->user();

        if ($user->profil()->exists()) {
            return redirect()->route('profil.show');
        }

        if ($request->input('aksi') === 'lewati') {
            ProfilMahasiswa::create(['user_id' => $user->id, 'status_default' => true]);

            return redirect()->route('dashboard')
                ->with('status', 'Profil dibuat kosong. Anda dapat melengkapinya kapan saja lewat menu Profil.');
        }

        $data = $request->validate([
            'skills' => ['nullable', 'array'],
            'skills.*' => ['integer', 'exists:skills,id_skill'],
            'interest' => ['nullable', 'string', 'max:255'],
            'halaman' => ['nullable', 'array', 'max:'.PortofolioService::MAKS_HALAMAN],
            'halaman.*' => ['file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ], [
            'halaman.*.max' => 'Setiap halaman portofolio maksimal 5 MB.',
            'halaman.*.mimes' => 'Halaman portofolio harus berformat JPG atau PNG.',
        ]);

        DB::transaction(function () use ($user, $data, $request, $portofolio) {
            $profil = ProfilMahasiswa::create([
                'user_id' => $user->id,
                'interest' => $data['interest'] ?? null,
                'status_default' => false,
            ]);

            $profil->skills()->sync($data['skills'] ?? []);

            foreach ($request->file('halaman', []) as $file) {
                $portofolio->tambah($profil, $file);
            }
        });

        return redirect()->route('profil.show')->with('status', 'Profil berhasil dibuat.');
    }
}
