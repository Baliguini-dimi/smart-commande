<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');   // Si l'user est supprimé, le restaurant aussi
            $table->foreignId('plan_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('set null');
            $table->string('name');                          // Nom du restaurant
            $table->string('slug')->unique();                // URL unique ex: chez-kouame
            $table->string('logo')->nullable();              // Chemin vers le logo
            $table->string('address')->nullable();           // Adresse
            $table->string('phone')->nullable();             // Téléphone
            $table->string('description')->nullable();       // Description courte
            $table->string('primary_color')->default('#1B4FE4'); // Couleur personnalisée
            $table->boolean('is_active')->default(true);
            $table->timestamp('subscription_expires_at')->nullable(); // Date expiration abonnement
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};