<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usulan_pokir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('tahun_anggaran');
            $table->enum('jenis_bantuan', ['KUBE', 'UEP']);

            // Identitas
            $table->string('nama_kelompok_usaha')->nullable()->comment('KUBE: nama KUBE, UEP: nama usaha');
            $table->string('nama_ketua_individu');
            $table->string('nik', 16);
            $table->string('no_hp')->nullable();

            // Validasi Desil
            $table->integer('desil')->nullable();
            $table->boolean('is_desil_valid')->default(false);

            // Lokasi
            $table->text('alamat_lengkap')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('link_google_maps')->nullable();

            // Bidang & Pagu
            $table->string('bidang_usaha')->nullable();
            $table->decimal('total_rab', 15, 2)->nullable()->comment('NULL untuk UEP');
            $table->string('no_nib')->nullable()->comment('UEP saja');

            // Status
            $table->enum('status', [
                'DRAFT',
                'REVIEW_DINAS',
                'REVISI_UTUSAN',
                'CLEARED_RKA',
                'FINAL_APPROVED',
            ])->default('DRAFT');
            $table->text('catatan_verifikator')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['tahun_anggaran', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('nik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usulan_pokir');
    }
};
