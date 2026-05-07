<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->string('name');             // Ex: Grillades, Boissons, Desserts
            $table->string('icon')->nullable(); // Emoji ou icône
            $table->integer('sort_order')->default(0); // Ordre d'affichage
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};