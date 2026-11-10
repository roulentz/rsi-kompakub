<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatKolaborasi extends Model
{
    protected $table = 'riwayat_kolaborasi';

    protected $primaryKey = 'id_riwayat';

    public $timestamps = false;

    protected $fillable = [
        'id_profil',
        'id_postingan',
        'judul_proyek',
        'peran',
        'tanggal_selesai',
    ];

    protected function casts(): array
    {
        return ['tanggal_selesai' => 'date'];
    }

    public function profil(): BelongsTo
    {
        return $this->belongsTo(ProfilMahasiswa::class, 'id_profil', 'id_profil');
    }

    /**
     * KONTRAK UNTUK SH: panggil saat proyek ditandai selesai.
     * Semua argumen berupa id_profil (bukan user id): $user->profil->id_profil.
     * Aman dipanggil ulang (tidak membuat riwayat ganda).
     *
     * @param  array<int>  $idProfilAnggota  anggota berstatus diterima
     */
    public static function catatProyekSelesai(int $idPostingan, string $judul, int $idProfilPembuat, array $idProfilAnggota): void
    {
        $tanggal = now()->toDateString();

        $daftar = [$idProfilPembuat => 'Pembuat Tim'];
        foreach ($idProfilAnggota as $id) {
            $daftar[$id] ??= 'Anggota Tim';
        }

        foreach ($daftar as $idProfil => $peran) {
            static::updateOrCreate(
                ['id_profil' => $idProfil, 'id_postingan' => $idPostingan],
                ['judul_proyek' => $judul, 'peran' => $peran, 'tanggal_selesai' => $tanggal]
            );
        }
    }

    /** Dua profil dianggap rekan bila punya riwayat pada postingan yang sama. */
    public static function rekanSatuTim(int $idProfilA, int $idProfilB): bool
    {
        return static::where('id_profil', $idProfilA)
            ->whereIn('id_postingan', static::where('id_profil', $idProfilB)->select('id_postingan'))
            ->exists();
    }
}
