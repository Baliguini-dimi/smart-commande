<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->string('number');                    // Numéro ou nom : "Table 5", "Terrasse A"
            $table->string('zone')->nullable();          // Zone : Intérieur, Terrasse, Bar
            $table->string('qr_code_path')->nullable();  // Chemin vers l'image QR
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_tables');
    }
};