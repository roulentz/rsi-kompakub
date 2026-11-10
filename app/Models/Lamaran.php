<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lamaran extends Model
{
    protected $table = 'lamaran';

    protected $primaryKey = 'id_lamaran';

    public $timestamps = false;

    protected $fillable = [
        'id_postingan',
        'id_pelamar',
        'status',
        'tanggal_lamar',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lamar' => 'datetime',
        ];
    }

    public function postingan(): BelongsTo
    {
        return $this->belongsTo(
            Postingan::class,
            'id_postingan',
            'id_postingan'
        );
    }

    public function pelamar(): BelongsTo
    {
        return $this->belongsTo(
            ProfilMahasiswa::class,
            'id_pelamar',
            'id_profil'
        );
    }
}
