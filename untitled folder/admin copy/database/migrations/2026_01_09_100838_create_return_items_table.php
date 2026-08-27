<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('return_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('return_id')->constrained('returns')->cascadeOnDelete();

            // Important: returning against a sold line
            $table->foreignId('order_item_id')->constrained('order_items');

            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('product_batch_id')->constrained('product_batches');

            $table->decimal('qty', 16, 4);

            $table->decimal('unit_price', 16, 4)->default(0);
            $table->decimal('refund_amount', 16, 4)->default(0);

            // GOOD / DAMAGED / EXPIRED
            $table->string('condition', 20)->default('GOOD');

            $table->string('reason_code', 40)->nullable();
            $table->text('note')->nullable();

            $table->timestamps();

            $table->index(['order_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_items');
    }
};
