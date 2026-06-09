<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('zone_cours_eau', function (Blueprint $table) {
            $table->foreignId('zone_forestiere_id')->constrained('zones_forestieres')->cascadeOnDelete();
            $table->foreignId('cours_eau_id')->constrained('cours_eaux')->cascadeOnDelete();
            $table->primary(['zone_forestiere_id', 'cours_eau_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zone_cours_eau');
    }
};
