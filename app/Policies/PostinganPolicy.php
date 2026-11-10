<?php

namespace App\Policies;

use App\Models\Postingan;
use App\Models\User;

class PostinganPolicy
{
    // Hanya pembuat postingan yang boleh mengelola postingannya
    public function kelola(User $user, Postingan $postingan): bool
    {
        return $user->profil?->id_profil === $postingan->id_pembuat;
    }
}
