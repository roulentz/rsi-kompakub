<?php

namespace App\Http\Controllers;

use App\Models\Percakapan;
use App\Models\Pesan;
use App\Models\User;
use Illuminate\Http\Request;

class DMController extends Controller
{
    // Daftar percakapan milik user yang login
    public function index()
    {
        $saya = auth()->id();

        $daftarPercakapan = Percakapan::with(['pengirim', 'penerima'])
            ->where('pengirim_id', $saya)
            ->orWhere('penerima_id', $saya)
            ->latest('updated_at')
            ->get();

        return view('dm.index', compact('daftarPercakapan'));
    }

    // Tombol Chat: buka percakapan yang sudah ada, atau buat baru
    public function mulai(User $user)
    {
        $saya = auth()->id();

        abort_if($user->id == $saya, 422, 'Tidak bisa mengirim pesan ke diri sendiri.');

        $percakapan = Percakapan::where(function ($q) use ($saya, $user) {
                $q->where('pengirim_id', $saya)->where('penerima_id', $user->id);
            })
            ->orWhere(function ($q) use ($saya, $user) {
                $q->where('pengirim_id', $user->id)->where('penerima_id', $saya);
            })
            ->first();

        if (! $percakapan) {
            $percakapan = Percakapan::create([
                'pengirim_id' => $saya,
                'penerima_id' => $user->id,
            ]);
        }

        return redirect()->route('dm.show', $percakapan);
    }

    // Ruang percakapan
    public function show(Percakapan $percakapan)
    {
        $this->pastikanPeserta($percakapan);

        $lawan = $percakapan->pengirim_id == auth()->id()
            ? $percakapan->penerima
            : $percakapan->pengirim;

        return view('dm.show', compact('percakapan', 'lawan'));
    }

    // DM-PC01 kirimPesan: simpan pesan
    public function kirim(Request $request, Percakapan $percakapan)
    {
        $this->pastikanPeserta($percakapan);

        $data = $request->validate([
            'isi_pesan' => ['required', 'string', 'max:1000'],
        ]);

        Pesan::create([
            'percakapan_id' => $percakapan->id,
            'pengirim_id' => auth()->id(),
            'isi_pesan' => $data['isi_pesan'],
            'waktu_kirim' => now(),
        ]);

        $percakapan->touch();

        return response()->json(['status' => 'terkirim'], 201);
    }

    // Dipakai polling: semua pesan, urut waktu kirim
    public function pesanJson(Percakapan $percakapan)
    {
        $this->pastikanPeserta($percakapan);

        $daftar = $percakapan->pesan()
            ->orderBy('waktu_kirim')
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'isi_pesan' => $p->isi_pesan,
                'milik_saya' => $p->pengirim_id == auth()->id(),
                'waktu_kirim' => $p->waktu_kirim->format('H:i'),
            ]);

        return response()->json($daftar);
    }

    // Hanya dua peserta percakapan yang boleh mengakses
    private function pastikanPeserta(Percakapan $percakapan): void
    {
        abort_unless(
            in_array(auth()->id(), [$percakapan->pengirim_id, $percakapan->penerima_id]),
            403
        );
    }
}