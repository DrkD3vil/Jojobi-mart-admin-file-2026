<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_transaction_lines', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stock_transaction_id')->constrained('stock_transactions')->cascadeOnDelete();

            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('product_batch_id')->constrained('product_batches');

            $table->decimal('qty', 16, 4);
            $table->string('unit', 20)->nullable();

            // Optional - useful for valuation or exchange price difference
            $table->decimal('unit_cost', 16, 4)->nullable();
            $table->decimal('unit_price', 16, 4)->nullable();

            // Extra info: reason, condition, channel, etc.
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['product_batch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transaction_lines');
    }
};
