<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Percakapan extends Model
{
    protected $table = 'percakapan';

    protected $fillable = ['pengirim_id', 'penerima_id'];

    public function pengirim()
    {
        return $this->belongsTo(User::class, 'pengirim_id');
    }

    public function penerima()
    {
        return $this->belongsTo(User::class, 'penerima_id');
    }

    public function pesan()
    {
        return $this->hasMany(Pesan::class, 'percakapan_id');
    }
}