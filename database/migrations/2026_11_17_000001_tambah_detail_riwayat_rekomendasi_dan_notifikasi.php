<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tahap 2: pencapaian proyek, peran bebas, rekomendasi per proyek, dan tautan notifikasi.
// Hanya menambah kolom dan melonggarkan enum menjadi string, jadi data lama tetap aman.
return new class extends Migration
{
    public function up(): void
    {
        // Pencapaian dan nama proyek milik tim, diisi pembuat saat Tandai Selesai.
        Schema::table('postingan', function (Blueprint $table) {
            $table->string('pencapaian', 30)->nullable()->after('status');
            $table->string('nama_proyek', 150)->nullable()->after('pencapaian');
        });

        // Peran bebas ("Lead Designer", dst), sebelumnya enum 'Pembuat Tim' / 'Anggota Tim'.
        Schema::table('riwayat_kolaborasi', function (Blueprint $table) {
            $table->string('peran', 100)->change();
        });

        // Rekomendasi terhubung ke proyek (untuk "Partner di ...") dan maksimal satu per orang per proyek.
        Schema::table('rekomendasi', function (Blueprint $table) {
            $table->unsignedBigInteger('id_postingan')->nullable()->after('id_profil_penerima')->index();
            $table->unique(['id_profil_pemberi', 'id_profil_penerima', 'id_postingan'], 'rekomendasi_unik_per_proyek');
        });

        // Notifikasi: tipe baru (Proyek Selesai, Rekomendasi Baru) dan tautan yang bisa diklik.
        Schema::table('notifikasi', function (Blueprint $table) {
            $table->string('tautan', 255)->nullable()->after('pesan');
        });
        Schema::table('notifikasi', function (Blueprint $table) {
            $table->string('tipe', 30)->change();
        });
    }

    public function down(): void
    {
        // Sengaja kosong: mengembalikan ke enum akan gagal bila sudah ada nilai baru.
    }
};
