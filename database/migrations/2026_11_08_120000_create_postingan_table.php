<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postingan', function (Blueprint $table) {
            $table->increments('id_postingan');

            $table->unsignedInteger('id_pembuat');

            // Satu tabel untuk semua jenis postingan (keputusan Tahap 0 nomor 2)
            $table->enum('kategori', ['Biasa', 'Cari Tim', 'Info Lomba']);

            // Kolom khusus Cari Tim dan Info Lomba, kosong untuk postingan Biasa
            $table->string('nama_lomba', 100)->nullable();
            $table->date('waktu_perlombaan')->nullable();
            $table->text('tag_skill')->nullable();
            $table->unsignedTinyInteger('jumlah_anggota')->default(1);

            // Maksimal 1000 karakter untuk postingan Biasa, Cari Tim dibatasi 500 di StorePostinganRequest
            $table->string('deskripsi', 1000)->nullable();
            $table->string('foto', 255)->nullable();

            $table->enum('status', ['Aktif', 'Selesai'])->default('Aktif');

            $table->timestamps();

            $table->foreign('id_pembuat')
                ->references('id_profil')
                ->on('profil_mahasiswa')
                ->restrictOnDelete();
        });

        // SQLite (dipakai phpunit) tidak mendukung ALTER TABLE ADD CONSTRAINT
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement(
                'ALTER TABLE postingan ADD CONSTRAINT chk_jumlah_anggota CHECK (jumlah_anggota BETWEEN 1 AND 10)'
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('postingan');
    }
};
