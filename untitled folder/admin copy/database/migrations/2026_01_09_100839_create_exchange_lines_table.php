<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exchange_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exchange_id')->constrained('exchanges')->cascadeOnDelete();

            // RETURN or ISSUE
            $table->string('mode', 10);

            // when returning an item from order
            $table->foreignId('order_item_id')->nullable()->constrained('order_items');

            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('product_batch_id')->constrained('product_batches');

            $table->decimal('qty', 16, 4);
            $table->decimal('unit_price', 16, 4)->default(0);

            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['mode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_lines');
    }
};
