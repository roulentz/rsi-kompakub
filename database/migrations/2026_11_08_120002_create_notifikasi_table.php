<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->increments('id_notifikasi');

            $table->unsignedInteger('id_penerima');

            $table->enum('tipe', ['Pelamar Baru', 'Diterima', 'Ditolak']);
            $table->text('pesan');
            $table->boolean('status_baca')->default(false);

            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_penerima')
                ->references('id_profil')
                ->on('profil_mahasiswa')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
