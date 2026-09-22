<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usulan_pokir', function (Blueprint $table) {
            $table->integer('tahun_asal_proposal')->nullable()->after('tahun_anggaran');
        });
    }

    public function down(): void
    {
        Schema::table('usulan_pokir', function (Blueprint $table) {
            $table->dropColumn('tahun_asal_proposal');
        });
    }
};
