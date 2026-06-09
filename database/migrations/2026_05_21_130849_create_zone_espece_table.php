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
        Schema::create('zone_espece', function (Blueprint $table) {
            $table->foreignId('zone_forestiere_id')->constrained('zones_forestieres')->cascadeOnDelete();
            $table->foreignId('espece_arbre_id')->constrained('especes_arbres')->cascadeOnDelete();
            $table->primary(['zone_forestiere_id', 'espece_arbre_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zone_espece');
    }
};
