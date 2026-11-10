<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
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

    // PCT-PC01: profil yang skill-nya cocok dengan tag_skill (maksimal $batas profil)
    public function rekomendasiKandidat(int $batas = 10): Collection
    {
        $namaSkill = collect(explode(',', (string) $this->tag_skill))
            ->map(fn ($skill) => trim($skill))
            ->filter()
            ->unique()
            ->values();

        if ($namaSkill->isEmpty()) {
            return new Collection();
        }

        return ProfilMahasiswa::query()
            ->where('id_profil', '!=', $this->id_pembuat)
            ->whereHas('skills', fn ($query) => $query->whereIn('nama_skill', $namaSkill))
            ->withCount([
                'skills as skill_cocok' => fn ($query) => $query->whereIn('nama_skill', $namaSkill),
            ])
            ->with(['user:id,name', 'skills'])
            ->orderByDesc('skill_cocok')
            ->orderBy('id_profil')
            ->limit($batas)
            ->get();
    }
}
