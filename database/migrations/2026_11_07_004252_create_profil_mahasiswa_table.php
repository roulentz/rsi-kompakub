
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profil_mahasiswa', function (Blueprint $table) {
            $table->increments('id_profil');

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('jurusan', 100)->nullable();
            $table->string('angkatan', 10)->nullable();
            $table->string('bio', 255)->nullable();
            $table->string('foto', 255)->nullable();
            $table->string('interest', 255)->nullable();
            $table->boolean('status_default')->default(true);

            $table->timestamp('last_updated')
                ->useCurrent()
                ->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profil_mahasiswa');
    }
};
