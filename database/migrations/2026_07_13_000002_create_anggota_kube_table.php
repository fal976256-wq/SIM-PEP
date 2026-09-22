<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota_kube', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usulan_id')->constrained('usulan_pokir')->cascadeOnDelete();
            $table->string('nik', 16);
            $table->string('no_kk', 16);
            $table->string('nama');
            $table->string('no_hp')->nullable();
            $table->integer('desil');
            $table->boolean('is_desil_valid')->default(false);
            $table->timestamps();

            $table->index('usulan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota_kube');
    }
};
