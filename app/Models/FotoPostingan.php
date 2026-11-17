<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FotoPostingan extends Model
{
    protected $table = 'foto_postingan';

    protected $primaryKey = 'id_foto';

    protected $fillable = ['id_postingan', 'path', 'urutan'];

    public function postingan(): BelongsTo
    {
        return $this->belongsTo(Postingan::class, 'id_postingan', 'id_postingan');
    }
}
