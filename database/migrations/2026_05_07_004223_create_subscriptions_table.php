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
            $table->unsignedBigInteger('restaurant_id');
            $table->unsignedBigInteger('plan_id');
            $table->datetime('starts_at');      // datetime au lieu de timestamp
            $table->datetime('expires_at');     // datetime au lieu de timestamp
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->string('payment_ref')->nullable();
            $table->decimal('amount_paid', 10, 0);
            $table->timestamps();

            $table->foreign('restaurant_id')
                  ->references('id')->on('restaurants')
                  ->onDelete('cascade');

            $table->foreign('plan_id')
                  ->references('id')->on('plans')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};