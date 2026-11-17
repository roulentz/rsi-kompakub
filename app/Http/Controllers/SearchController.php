<?php

namespace App\Http\Controllers;

use App\Models\Postingan;
use App\Models\ProfilMahasiswa;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $keyword = mb_substr(trim((string) $request->query('q', '')), 0, 100);
        $type = $request->query('type', 'posts');

        if (!in_array($type, ['posts', 'people'], true)) {
            $type = 'posts';
        }

        $hasil = null;

        if ($keyword !== '') {
            $pola = '%' . addcslashes($keyword, '\\%_') . '%';

            if ($type === 'people') {
                // Search People
                $hasil = ProfilMahasiswa::with(['user', 'skills'])
                    ->where(function ($query) use ($pola) {
                        $query->whereHas('user', fn ($user) =>
                            $user->where('name', 'like', $pola)
                        )
                        ->orWhere('jurusan', 'like', $pola)
                        ->orWhere('bio', 'like', $pola)
                        ->orWhereHas('skills', fn ($skill) =>
                            $skill->where('nama_skill', 'like', $pola)
                        );
                    })
                    ->orderBy('id_profil')
                    ->paginate(10)
                    ->withQueryString();
            } else {
                // Search Posts
                $hasil = Postingan::with(['pembuat.user', 'lamaran'])
                    ->where(function ($query) use ($pola) {
                        $query->where('nama_lomba', 'like', $pola)
                            ->orWhere('deskripsi', 'like', $pola)
                            ->orWhere('tag_skill', 'like', $pola)
                            ->orWhereHas('pembuat.user', fn ($user) =>
                                $user->where('name', 'like', $pola)
                            );
                    })
                    ->orderByDesc('created_at')
                    ->orderByDesc('id_postingan')
                    ->paginate(10)
                    ->withQueryString();
            }
        }

        $profil = $request->user()?->profil;

        return view('search.index', compact('keyword', 'type', 'hasil', 'profil'));
    }
}
