<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->foreignId('plan_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->timestamp('starts_at');
            $table->timestamp('expires_at');
            $table->enum('status', ['active', 'expired', 'cancelled'])
                  ->default('active');
            $table->string('payment_ref')->nullable(); // Référence du paiement
            $table->decimal('amount_paid', 10, 0);    // Montant payé en FCFA
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};