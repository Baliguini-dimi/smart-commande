<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->foreignId('restaurant_table_id')
                  ->constrained()
                  ->onDelete('cascade');
            // Statut de la commande
            $table->enum('status', [
                'pending',     // En attente
                'preparing',   // En préparation
                'ready',       // Prêt à servir
                'served'       // Servi
            ])->default('pending');
            $table->decimal('total_amount', 10, 0);  // Montant total FCFA
            // Mode de paiement choisi par le client
            $table->enum('payment_method', [
                'wave', 'orange_money', 'cash'
            ])->nullable();
            $table->enum('payment_status', [
                'pending', 'paid', 'failed'
            ])->default('pending');
            $table->text('client_note')->nullable();  // Note du client
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};