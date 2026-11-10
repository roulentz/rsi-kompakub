<?php

namespace App\Http\Controllers;

use App\Models\Portofolio;
use App\Services\PortofolioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PortofolioController extends Controller
{
    public function __construct(private PortofolioService $layanan) {}

    /** getPortofolio() */
    public function edit(Request $request): View
    {
        $profil = $request->user()->profil;

        return view('profil.portofolio', [
            'halaman' => $profil->portofolio()->orderBy('urutan_halaman')->get(),
            'maks' => PortofolioService::MAKS_HALAMAN,
        ]);
    }

    /** simpanPortofolio() */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'halaman' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
            'tautan_eksternal' => ['nullable', 'url', 'max:255'],
        ], [
            'halaman.max' => 'Setiap halaman portofolio maksimal 5 MB.',
            'halaman.mimes' => 'Halaman portofolio harus berformat JPG atau PNG.',
        ]);

        $this->layanan->tambah($request->user()->profil, $request->file('halaman'), $data['tautan_eksternal'] ?? null);

        return back()->with('status', 'Halaman portofolio ditambahkan.');
    }

    public function destroy(Request $request, Portofolio $portofolio): RedirectResponse
    {
        $profil = $request->user()->profil;
        abort_unless($portofolio->id_profil === $profil->id_profil, 403);

        Storage::disk('public')->delete($portofolio->konten);
        $portofolio->delete();
        $this->layanan->rapikanUrutan($profil);

        return back()->with('status', 'Halaman portofolio dihapus.');
    }

    public function geser(Request $request, Portofolio $portofolio): RedirectResponse
    {
        $profil = $request->user()->profil;
        abort_unless($portofolio->id_profil === $profil->id_profil, 403);

        $arah = $request->validate(['arah' => ['required', 'in:naik,turun']])['arah'];
        $urutan = $portofolio->urutan_halaman;
        $targetUrutan = $arah === 'naik' ? $urutan - 1 : $urutan + 1;

        $tetangga = $profil->portofolio()->where('urutan_halaman', $targetUrutan)->first();

        if ($tetangga) {
            $tetangga->update(['urutan_halaman' => $urutan]);
            $portofolio->update(['urutan_halaman' => $targetUrutan]);
        }

        return back();
    }
}
