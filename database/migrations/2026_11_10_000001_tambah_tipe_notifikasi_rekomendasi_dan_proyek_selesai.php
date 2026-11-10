<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function isMysql(): bool
    {
        return in_array(DB::getDriverName(), ['mysql', 'mariadb'], true);
    }

    public function up(): void
    {
        if ($this->isMysql()) {
            DB::statement("ALTER TABLE notifikasi MODIFY tipe ENUM('Pelamar Baru','Diterima','Ditolak','Rekomendasi Baru','Proyek Selesai') NOT NULL");
        }
    }

    public function down(): void
    {
        if ($this->isMysql()) {
            DB::table('notifikasi')->whereIn('tipe', ['Rekomendasi Baru', 'Proyek Selesai'])->delete();
            DB::statement("ALTER TABLE notifikasi MODIFY tipe ENUM('Pelamar Baru','Diterima','Ditolak') NOT NULL");
        }
    }
};
