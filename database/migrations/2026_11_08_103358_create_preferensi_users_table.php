<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('preferensi_user', function (Blueprint $table) {
            $table->increments('id_preferensi');
            $table->unsignedInteger('id_profil')->unique();
            $table->enum('kategori_terakhir', ['Semua', 'Cari Tim', 'Info Lomba'])->default('Semua');
            $table->timestamps();

            $table->foreign('id_profil')
                ->references('id_profil')
                ->on('profil_mahasiswa')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preferensi_user');
    }
};
