<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usulan_pokir', function (Blueprint $table) {
            $table->index('status');
            $table->index('jenis_bantuan');
        });

        Schema::table('dokumen_arsip', function (Blueprint $table) {
            $table->index('usulan_id');
        });

        Schema::table('notifikasis', function (Blueprint $table) {
            $table->index(['user_id', 'is_read']);
        });

        Schema::table('anggota_kube', function (Blueprint $table) {
            $table->index('nik');
        });

        Schema::table('log_audit_trail', function (Blueprint $table) {
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('usulan_pokir', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['jenis_bantuan']);
        });

        Schema::table('dokumen_arsip', function (Blueprint $table) {
            $table->dropIndex(['usulan_id']);
        });

        Schema::table('notifikasis', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_read']);
        });

        Schema::table('anggota_kube', function (Blueprint $table) {
            $table->dropIndex(['nik']);
        });

        Schema::table('log_audit_trail', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });
    }
};
