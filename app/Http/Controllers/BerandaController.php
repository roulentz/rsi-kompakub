<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Postingan;        // model SH — pakai setelah merge
use App\Models\PreferensiUser;

class BerandaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $filter = $request->input('kategori', 'Semua');

        // Simpan filter terakhir ke tabel preferensi_user
        PreferensiUser::updateOrCreate(
            ['user_id' => $user->id],
            ['filter_terakhir' => $filter]
        );

        // Ambil postingan (pakai data dummy dulu sebelum model SH merge)
        if (class_exists(Postingan::class)) {
            $postingan = $filter === 'Semua'
                ? Postingan::latest()->get()
                : Postingan::where('kategori', $filter)->latest()->get();
        } else {
            // Data dummy sementara
            $postingan = collect([
                (object)['id' => 1, 'judul' => 'Cari Tim Hackathon', 'kategori' => 'Cari Tim', 'isi' => 'Butuh developer mobile...', 'created_at' => now()],
                (object)['id' => 2, 'judul' => 'Info Lomba Gemastik', 'kategori' => 'Info Lomba', 'isi' => 'Deadline 20 Desember...', 'created_at' => now()],
            ]);
            if ($filter !== 'Semua') {
                $postingan = $postingan->where('kategori', $filter);
            }
        }

        return view('beranda.index', compact('postingan', 'filter'));
    }
}