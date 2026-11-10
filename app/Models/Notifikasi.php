<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    protected $primaryKey = 'id_notifikasi';

    public $timestamps = false;

    protected $fillable = [
        'id_penerima',
        'tipe',
        'pesan',
        'status_baca',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'status_baca' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function penerima(): BelongsTo
    {
        return $this->belongsTo(
            ProfilMahasiswa::class,
            'id_penerima',
            'id_profil'
        );
    }

    // Kirim notifikasi ke satu profil (belum dibaca)
    public static function kirim(int $idPenerima, string $tipe, string $pesan): self
    {
        return self::create([
            'id_penerima' => $idPenerima,
            'tipe' => $tipe,
            'pesan' => $pesan,
        ]);
    }
}
