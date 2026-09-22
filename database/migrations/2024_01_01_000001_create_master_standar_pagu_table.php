<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_standar_pagu', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun_anggaran');
            $table->string('bidang_usaha');
            $table->decimal('pagu_maksimal', 15, 2);
            $table->timestamps();

            $table->unique(['tahun_anggaran', 'bidang_usaha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_standar_pagu');
    }
};
