<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('master_desil_dtsen');
        Schema::rename('master_desil_dtks', 'master_desil_dtsen');
    }

    public function down(): void
    {
        Schema::rename('master_desil_dtsen', 'master_desil_dtks');
    }
};
