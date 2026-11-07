<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rekomendasi extends Model
{
    protected $table = 'rekomendasi';

    protected $primaryKey = 'id_rekomendasi';

    public $timestamps = false;

    protected $fillable = [
        'id_profil_pemberi',
        'id_profil_penerima',
        'isi_rekomendasi',
        'tanggal_dikirim',
        'status_laporan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_dikirim' => 'date',
        ];
    }

    public function pemberi(): BelongsTo
    {
        return $this->belongsTo(
            ProfilMahasiswa::class,
            'id_profil_pemberi',
            'id_profil'
        );
    }

    public function penerima(): BelongsTo
    {
        return $this->belongsTo(
            ProfilMahasiswa::class,
            'id_profil_penerima',
            'id_profil'
        );
    }
}
