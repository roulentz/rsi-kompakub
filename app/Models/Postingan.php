<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Postingan extends Model
{
    protected $table = 'postingan';

    protected $primaryKey = 'id_postingan';

    protected $fillable = [
        'id_pembuat',
        'kategori',
        'nama_lomba',
        'waktu_perlombaan',
        'tag_skill',
        'jumlah_anggota',
        'deskripsi',
        'foto',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'waktu_perlombaan' => 'date',
            'jumlah_anggota' => 'integer',
        ];
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(
            ProfilMahasiswa::class,
            'id_pembuat',
            'id_profil'
        );
    }

    public function lamaran(): HasMany
    {
        return $this->hasMany(
            Lamaran::class,
            'id_postingan',
            'id_postingan'
        );
    }
}
