<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('jenis_bantuan', ['KUBE', 'UEP']);
            $table->unsignedSmallInteger('current_step')->default(1);
            $table->json('form_data');
            $table->json('file_paths')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'jenis_bantuan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drafts');
    }
};
