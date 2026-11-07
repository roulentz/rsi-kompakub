
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->increments('id_skill');
            $table->string('nama_skill', 100)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
