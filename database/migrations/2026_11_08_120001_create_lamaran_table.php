<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lamaran', function (Blueprint $table) {
            $table->increments('id_lamaran');

            $table->unsignedInteger('id_postingan');
            $table->unsignedInteger('id_pelamar');

            $table->enum('status', ['Menunggu Konfirmasi', 'Diterima', 'Ditolak'])
                ->default('Menunggu Konfirmasi');

            $table->timestamp('tanggal_lamar')->useCurrent();

            // Satu pelamar hanya boleh melamar satu kali ke postingan yang sama
            $table->unique(['id_postingan', 'id_pelamar'], 'uq_lamaran_postingan_pelamar');

            $table->foreign('id_postingan')
                ->references('id_postingan')
                ->on('postingan')
                ->cascadeOnDelete();

            $table->foreign('id_pelamar')
                ->references('id_profil')
                ->on('profil_mahasiswa')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lamaran');
    }
};
