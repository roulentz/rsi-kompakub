<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('mahasiswa')->after('no_telepon');
            $table->string('kode_verifikasi', 6)->nullable()->after('email_verified_at');
            $table->timestamp('kode_kadaluarsa')->nullable()->after('kode_verifikasi');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'kode_verifikasi', 'kode_kadaluarsa']);
        });
    }
};
