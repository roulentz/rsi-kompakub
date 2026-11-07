<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    protected $table = 'skills';

    protected $primaryKey = 'id_skill';

    public $timestamps = false;

    protected $fillable = [
        'nama_skill',
    ];

    public function profilMahasiswa(): BelongsToMany
    {
        return $this->belongsToMany(
            ProfilMahasiswa::class,
            'profil_skill',
            'id_skill',
            'id_profil',
            'id_skill',
            'id_profil'
        );
    }
}
