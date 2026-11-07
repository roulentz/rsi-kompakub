
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profil_skill', function (Blueprint $table) {
            $table->unsignedInteger('id_profil');
            $table->unsignedInteger('id_skill');

            $table->primary(['id_profil', 'id_skill']);

            $table->foreign('id_profil')
                ->references('id_profil')
                ->on('profil_mahasiswa')
                ->cascadeOnDelete();

            $table->foreign('id_skill')
                ->references('id_skill')
                ->on('skills')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profil_skill');
    }
};
