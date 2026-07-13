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

            $table->string('order_no')->unique();
            $table->string('session_id')->index();

            // Customer (nullable, NO FK yet if customer table may be later)
            $table->foreignId('customer_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            // ✅ Location FK (MATCH types!)
            $table->foreignId('location_id')
                  ->nullable()
                  ->constrained('locations')
                  ->nullOnDelete();

            $table->decimal('subtotal', 12, 4);
            $table->decimal('discount_total', 12, 4)->default(0);
            $table->decimal('payable_total', 12, 4);

            $table->decimal('rewards_points_used', 12, 2)->default(0);
            $table->decimal('rewards_amount_used', 12, 4)->default(0);

            // ✅ ENUM status
            $table->enum('status', [
                'pending',
                'completed',
                'paid',
                'refunded',
                'returned',
                'cancelled'
            ])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
