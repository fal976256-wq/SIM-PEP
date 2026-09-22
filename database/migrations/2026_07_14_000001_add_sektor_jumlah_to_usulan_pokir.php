<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usulan_pokir', function (Blueprint $table) {
            $table->string('sektor_usaha', 100)->nullable()->after('nama_kelompok_usaha');
            $table->string('sektor_usaha_lainnya', 150)->nullable()->after('sektor_usaha');
            $table->unsignedSmallInteger('jumlah_anggota')->nullable()->after('sektor_usaha_lainnya');
        });
    }

    public function down(): void
    {
        Schema::table('usulan_pokir', function (Blueprint $table) {
            $table->dropColumn(['sektor_usaha', 'sektor_usaha_lainnya', 'jumlah_anggota']);
        });
    }
};
