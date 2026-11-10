<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const MASA_BERLAKU_KODE_MENIT = 10;

    // role, kode_verifikasi, dan kode_kadaluarsa sengaja tidak fillable (diubah lewat forceFill).
    protected $fillable = [
        'name',
        'tanggal_lahir',
        'email',
        'no_telepon',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'kode_verifikasi',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'kode_kadaluarsa' => 'datetime',
            'tanggal_lahir' => 'date',
            'password' => 'hashed',
        ];
    }

    public function profil(): HasOne
    {
        return $this->hasOne(ProfilMahasiswa::class, 'user_id', 'id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /** Buat kode 6 digit baru (berlaku 10 menit) lalu kirim ke email. */
    public function kirimKodeVerifikasi(): void
    {
        $kode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->forceFill([
            'kode_verifikasi' => $kode,
            'kode_kadaluarsa' => now()->addMinutes(self::MASA_BERLAKU_KODE_MENIT),
        ])->save();

        Mail::raw(
            "Kode verifikasi KompakUB Anda: {$kode}\nKode berlaku ".self::MASA_BERLAKU_KODE_MENIT." menit.",
            fn ($m) => $m->to($this->email)->subject('Kode Verifikasi KompakUB')
        );
    }

    public function cocokKode(string $kode): bool
    {
        return $this->kode_verifikasi !== null
            && hash_equals($this->kode_verifikasi, $kode)
            && $this->kode_kadaluarsa !== null
            && $this->kode_kadaluarsa->isFuture();
    }

    public function tandaiTerverifikasi(): void
    {
        $this->forceFill([
            'email_verified_at' => now(),
            'kode_verifikasi' => null,
            'kode_kadaluarsa' => null,
        ])->save();
    }
}
