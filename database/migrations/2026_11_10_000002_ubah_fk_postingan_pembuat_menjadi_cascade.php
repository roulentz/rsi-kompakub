<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Agar akun yang sudah punya postingan tetap bisa dihapus (postingan ikut terhapus).
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('postingan', function (Blueprint $table) {
            $table->dropForeign(['id_pembuat']);
            $table->foreign('id_pembuat')->references('id_profil')->on('profil_mahasiswa')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('postingan', function (Blueprint $table) {
            $table->dropForeign(['id_pembuat']);
            $table->foreign('id_pembuat')->references('id_profil')->on('profil_mahasiswa')->restrictOnDelete();
        });
    }
};
