<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_desil_dtks', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 16);
            $table->string('nama');
            $table->integer('desil')->comment('1-10');
            $table->text('alamat')->nullable();
            $table->string('desa');
            $table->string('kecamatan');
            $table->string('kabupaten');
            $table->integer('tahun_sync');
            $table->timestamps();

            $table->unique(['nik', 'tahun_sync']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_desil_dtks');
    }
};
