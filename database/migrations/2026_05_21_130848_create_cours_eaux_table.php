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
        Schema::create('cours_eaux', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->enum('type', ['rivière', 'fleuve', 'lac']);
            $table->float('longueur')->nullable(); // km
            $table->float('debit')->nullable();
            $table->json('coordonnees')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cours_eaux');
    }
};
