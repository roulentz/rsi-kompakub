<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostinganRequest;
use App\Models\Postingan;
use Illuminate\Http\RedirectResponse;

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
}
