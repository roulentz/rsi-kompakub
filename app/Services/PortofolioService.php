<?php

namespace App\Services;

use App\Models\Portofolio;
use App\Models\ProfilMahasiswa;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class PortofolioService
{
    public const MAKS_HALAMAN = 20;

    public function tambah(ProfilMahasiswa $profil, UploadedFile $file, ?string $tautan = null): Portofolio
    {
        $jumlah = $profil->portofolio()->count();

        if ($jumlah >= self::MAKS_HALAMAN) {
            throw ValidationException::withMessages([
                'halaman' => 'Portofolio maksimal '.self::MAKS_HALAMAN.' halaman.',
            ]);
        }

        $ukuranMb = round($file->getSize() / 1048576, 2);

        return $profil->portofolio()->create([
            'konten' => $file->store('portofolio', 'public'),
            'urutan_halaman' => $jumlah + 1,
            'tautan_eksternal' => $tautan,
            'ukuran_file' => $ukuranMb,
        ]);
    }

    public function rapikanUrutan(ProfilMahasiswa $profil): void
    {
        $profil->portofolio()->orderBy('urutan_halaman')->get()
            ->each(fn (Portofolio $p, int $i) => $p->update(['urutan_halaman' => $i + 1]));
    }
}
