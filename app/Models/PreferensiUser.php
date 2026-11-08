<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreferensiUser extends Model
{
    protected $table = 'preferensi_user';

    protected $primaryKey = 'id_preferensi';

    protected $fillable = [
        'id_profil',
        'kategori_terakhir',
    ];

    public function profil(): BelongsTo
    {
        return $this->belongsTo(ProfilMahasiswa::class, 'id_profil', 'id_profil');
    }
}
