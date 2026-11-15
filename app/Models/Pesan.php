<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesan extends Model
{
    protected $table = 'pesan';

    public $timestamps = false;            

    protected $fillable = ['percakapan_id', 'pengirim_id', 'isi_pesan', 'waktu_kirim'];

    protected $casts = [
        'waktu_kirim' => 'datetime',
    ];

    public function percakapan()
    {
        return $this->belongsTo(Percakapan::class, 'percakapan_id');
    }

    public function pengirim()
    {
        return $this->belongsTo(User::class, 'pengirim_id');
    }
}