<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Ganti kata sandi ini sebelum aplikasi dipublikasikan.
        User::firstOrNew(['email' => 'admin@kompakub.test'])
            ->forceFill([
                'name' => 'Administrator',
                'tanggal_lahir' => '1990-01-01',
                'no_telepon' => '081234567890',
                'password' => 'Admin12345',
                'role' => 'admin',
                'email_verified_at' => now(),
            ])->save();
    }
}
