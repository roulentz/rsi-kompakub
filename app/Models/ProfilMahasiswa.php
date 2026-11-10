<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class ProfilMahasiswa extends Model
{
    protected $table = 'profil_mahasiswa';

    protected $primaryKey = 'id_profil';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'jurusan',
        'angkatan',
        'bio',
        'foto',
        'interest',
        'status_default',
    ];

    protected function casts(): array
    {
        return [
            'status_default' => 'boolean',
            'last_updated' => 'datetime',
        ];
    }

    public function fotoUrl(): ?string
    {
        return $this->foto ? Storage::disk('public')->url($this->foto) : null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'profil_skill', 'id_profil', 'id_skill', 'id_profil', 'id_skill');
    }

    public function portofolio(): HasMany
    {
        return $this->hasMany(Portofolio::class, 'id_profil', 'id_profil');
    }

    public function riwayat(): HasMany
    {
        return $this->hasMany(RiwayatKolaborasi::class, 'id_profil', 'id_profil');
    }

    public function rekomendasiDiberikan(): HasMany
    {
        return $this->hasMany(Rekomendasi::class, 'id_profil_pemberi', 'id_profil');
    }

    public function rekomendasiDiterima(): HasMany
    {
        return $this->hasMany(Rekomendasi::class, 'id_profil_penerima', 'id_profil');
    }
}
