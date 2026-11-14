<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostinganRequest;
use App\Models\Postingan;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
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
}
