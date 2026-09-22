<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usulan_pokir', function (Blueprint $table) {
            // Ketua — tambah No KK
            $table->string('no_kk_ketua', 16)->nullable()->after('nik');

            // Sekretaris
            $table->string('nik_sekretaris', 16)->nullable()->after('no_hp');
            $table->string('no_kk_sekretaris', 16)->nullable()->after('nik_sekretaris');
            $table->string('nama_sekretaris')->nullable()->after('no_kk_sekretaris');
            $table->string('no_hp_sekretaris')->nullable()->after('nama_sekretaris');
            $table->integer('desil_sekretaris')->nullable()->after('no_hp_sekretaris');
            $table->boolean('is_desil_sekretaris_valid')->default(false)->after('desil_sekretaris');

            // Bendahara
            $table->string('nik_bendahara', 16)->nullable()->after('is_desil_sekretaris_valid');
            $table->string('no_kk_bendahara', 16)->nullable()->after('nik_bendahara');
            $table->string('nama_bendahara')->nullable()->after('no_kk_bendahara');
            $table->string('no_hp_bendahara')->nullable()->after('nama_bendahara');
            $table->integer('desil_bendahara')->nullable()->after('no_hp_bendahara');
            $table->boolean('is_desil_bendahara_valid')->default(false)->after('desil_bendahara');
        });
    }

    public function down(): void
    {
        Schema::table('usulan_pokir', function (Blueprint $table) {
            $table->dropColumn([
                'no_kk_ketua',
                'nik_sekretaris', 'no_kk_sekretaris', 'nama_sekretaris', 'no_hp_sekretaris',
                'desil_sekretaris', 'is_desil_sekretaris_valid',
                'nik_bendahara', 'no_kk_bendahara', 'nama_bendahara', 'no_hp_bendahara',
                'desil_bendahara', 'is_desil_bendahara_valid',
            ]);
        });
    }
};
