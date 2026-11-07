<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Portofolio extends Model
{
    protected $table = 'portofolio';

    protected $primaryKey = 'id_portofolio';

    public $timestamps = false;

    protected $fillable = [
        'id_profil',
        'konten',
        'urutan_halaman',
        'tautan_eksternal',
        'ukuran_file',
    ];

    protected function casts(): array
    {
        return [
            'urutan_halaman' => 'integer',
            'ukuran_file' => 'decimal:2',
        ];
    }

    public function profil(): BelongsTo
    {
        return $this->belongsTo(
            ProfilMahasiswa::class,
            'id_profil',
            'id_profil'
        );
    }
}
