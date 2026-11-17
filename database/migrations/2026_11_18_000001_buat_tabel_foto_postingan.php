<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Postingan bisa memuat sampai 10 foto. Kolom lama postingan.foto tetap dipakai sebagai foto sampul (yang pertama),
// dan foto lama disalin ke tabel baru supaya postingan lama tetap tampil.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('foto_postingan', function (Blueprint $table) {
            $table->id('id_foto');
            $table->unsignedInteger('id_postingan')->index();
            $table->string('path');
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();

            $table->foreign('id_postingan')->references('id_postingan')->on('postingan')->cascadeOnDelete();
        });

        foreach (DB::table('postingan')->whereNotNull('foto')->get(['id_postingan', 'foto']) as $baris) {
            DB::table('foto_postingan')->insert([
                'id_postingan' => $baris->id_postingan,
                'path' => $baris->foto,
                'urutan' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('foto_postingan');
    }
};
