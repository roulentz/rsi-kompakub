
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portofolio', function (Blueprint $table) {
            $table->increments('id_portofolio');

            $table->unsignedInteger('id_profil');

            $table->text('konten');
            $table->integer('urutan_halaman');
            $table->string('tautan_eksternal', 255)->nullable();
            $table->decimal('ukuran_file', 10, 2);

            $table->foreign('id_profil')
                ->references('id_profil')
                ->on('profil_mahasiswa')
                ->cascadeOnDelete();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portofolio');
    }
};
