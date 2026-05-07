<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->decimal('amount', 10, 0);
            $table->enum('method', ['wave', 'orange_money', 'cash']);
            $table->enum('status', ['pending', 'success', 'failed'])
                  ->default('pending');
            $table->string('transaction_id')->nullable(); // ID retourné par CinetPay
            $table->string('cinetpay_ref')->nullable();   // Référence CinetPay
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};