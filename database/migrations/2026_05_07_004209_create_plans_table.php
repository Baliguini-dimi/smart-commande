<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // Starter, Pro, Premium
            $table->integer('price_monthly');          // Prix en FCFA
            $table->integer('max_tables');             // Nombre max de tables
            $table->integer('max_menus');              // Nombre max de menus
            $table->json('features')->nullable();      // Liste des fonctionnalités
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};