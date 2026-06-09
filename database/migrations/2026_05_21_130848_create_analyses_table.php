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
        Schema::create('analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_forestiere_id')->constrained('zones_forestieres')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type_analyse');
            $table->text('resultat')->nullable();
            $table->float('superficie_concernee')->nullable();
            $table->float('taux_deforestation')->nullable(); // pourcentage
            $table->text('observations')->nullable();
            $table->timestamp('date_analyse')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analyses');
    }
};
