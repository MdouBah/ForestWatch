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
        Schema::create('especes_arbres', function (Blueprint $table) {
            $table->id();
            $table->string('nom_commun');
            $table->string('nom_scientifique')->unique();
            $table->string('famille');
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->enum('statut', ['commun', 'rare', 'menacé'])->default('commun');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('especes_arbres');
    }
};
