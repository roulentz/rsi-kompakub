<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    /** Skill yang tampil sebagai rekomendasi saat kolom cari masih kosong. Sisanya ditemukan lewat pencarian. */
    public const POPULER = [
        'UI/UX Design', 'Graphic Design', 'Web Development', 'Mobile Development', 'Frontend Development',
        'Backend Development', 'Data Analysis', 'Machine Learning', 'Project Management', 'Business Analysis',
        'Digital Marketing', 'Content Writing', 'Video Editing', 'Public Speaking', 'Leadership', 'Teamwork',
    ];

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
