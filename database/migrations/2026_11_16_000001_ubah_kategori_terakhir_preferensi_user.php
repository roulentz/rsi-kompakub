<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tab baru "Umum" di Beranda: kolom enum lama hanya menerima Semua, Cari Tim, dan Info Lomba.
// Diubah menjadi string supaya nilai "Umum" bisa disimpan sebagai preferensi filter terakhir.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('preferensi_user', function (Blueprint $table) {
            $table->string('kategori_terakhir', 20)->default('Semua')->change();
        });
    }

    public function down(): void
    {
        // Sengaja kosong: mengembalikan ke enum akan gagal bila sudah ada nilai "Umum".
    }
};
