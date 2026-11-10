<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_kolaborasi', function (Blueprint $table) {
            $table->increments('id_riwayat');
            $table->unsignedInteger('id_profil');
            // Tanpa foreign key: tabel postingan milik SH belum tentu sudah ada saat migration ini jalan.
            $table->unsignedBigInteger('id_postingan')->nullable()->index();
            $table->string('judul_proyek', 150);
            $table->enum('peran', ['Pembuat Tim', 'Anggota Tim']);
            $table->date('tanggal_selesai');

            $table->unique(['id_profil', 'id_postingan']);

            $table->foreign('id_profil')
                ->references('id_profil')
                ->on('profil_mahasiswa')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_kolaborasi');
    }
};
