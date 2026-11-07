
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekomendasi', function (Blueprint $table) {
            $table->increments('id_rekomendasi');

            $table->unsignedInteger('id_profil_pemberi');
            $table->unsignedInteger('id_profil_penerima');

            $table->string('isi_rekomendasi', 500);
            $table->date('tanggal_dikirim');

            $table->enum('status_laporan', [
                'tidak_dilaporkan',
                'dilaporkan',
                'ditinjau_dipertahankan'
            ])->default('tidak_dilaporkan');

            $table->foreign('id_profil_pemberi')
                ->references('id_profil')
                ->on('profil_mahasiswa')
                ->cascadeOnDelete();

            $table->foreign('id_profil_penerima')
                ->references('id_profil')
                ->on('profil_mahasiswa')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekomendasi');
    }
};
