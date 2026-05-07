<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dishes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->string('name');                       // Nom du plat
            $table->text('description')->nullable();      // Description
            $table->decimal('price', 10, 0);             // Prix en FCFA (pas de centimes)
            $table->string('image')->nullable();          // Photo du plat
            $table->json('allergens')->nullable();        // Allergènes
            $table->boolean('is_available')->default(true); // Disponible ou rupture
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dishes');
    }
};