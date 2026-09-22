<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usulan_pokir', function (Blueprint $table) {
            $table->foreignId('anggota_dewan_id')->nullable()->constrained('master_anggota_dewan')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('usulan_pokir', function (Blueprint $table) {
            $table->dropForeign(['anggota_dewan_id']);
            $table->dropColumn('anggota_dewan_id');
        });
    }
};
