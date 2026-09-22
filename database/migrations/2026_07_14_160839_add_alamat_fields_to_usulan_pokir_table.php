<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usulan_pokir', function (Blueprint $table) {
            $table->string('kabupaten', 100)->nullable()->after('alamat_lengkap');
            $table->string('kecamatan', 100)->nullable()->after('kabupaten');
            $table->string('desa_kelurahan', 100)->nullable()->after('kecamatan');
            $table->text('alamat_detail')->nullable()->after('desa_kelurahan');
        });
    }

    public function down(): void
    {
        Schema::table('usulan_pokir', function (Blueprint $table) {
            $table->dropColumn(['kabupaten', 'kecamatan', 'desa_kelurahan', 'alamat_detail']);
        });
    }
};
