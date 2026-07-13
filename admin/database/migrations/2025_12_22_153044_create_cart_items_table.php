<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            /*
             |--------------------------------------------------------------------------
             | Relations
             |--------------------------------------------------------------------------
             */
            $table->foreignId('cart_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_batch_id')
                ->constrained('product_batches')
                ->cascadeOnDelete();

            $table->foreignId('product_image_id')
                ->nullable()
                ->constrained('product_images')
                ->nullOnDelete();

            /*
             |--------------------------------------------------------------------------
             | Pricing & Quantity
             |--------------------------------------------------------------------------
             */
            // retail | whole | customer_whole
            $table->string('price_type')->default('retail');

            $table->decimal('unit_price', 12, 4)->default(0);

            // number of selling units
            $table->decimal('quantity', 12, 2)->default(1);

            // kg / g / l / ml / pcs
            $table->string('unit')->nullable();

            // converted quantity in batch base unit
            $table->decimal('qty_in_batch_unit', 18, 4)->nullable();

            /*
             |--------------------------------------------------------------------------
             | Discount (display only – does not affect total_price)
             |--------------------------------------------------------------------------
             */
            $table->decimal('discount_amount', 12, 4)->default(0);
            $table->decimal('discount_percent', 8, 2)->nullable();
            $table->string('discount_label')->nullable();

            /*
             |--------------------------------------------------------------------------
             | Totals
             |--------------------------------------------------------------------------
             */
            // total_price excludes discount fields
            $table->decimal('total_price', 12, 4)->default(0);

            /*
             |--------------------------------------------------------------------------
             | Gift / Offer Logic
             |--------------------------------------------------------------------------
             */
            $table->boolean('is_gift')->default(false);

            // batch_offer | manual
            $table->string('gift_source')->nullable();

            // batch_offer source id (e.g. product_batch_id)
            $table->unsignedBigInteger('gift_source_id')->nullable();

            // link gift item to paid item
            $table->unsignedBigInteger('parent_cart_item_id')->nullable();

            $table->foreign('parent_cart_item_id')
                ->references('id')
                ->on('cart_items')
                ->nullOnDelete();

            /*
             |--------------------------------------------------------------------------
             | Constraints
             |--------------------------------------------------------------------------
             */
            // only one gift per paid item per source
            $table->unique(
                ['cart_id', 'parent_cart_item_id', 'gift_source', 'gift_source_id'],
                'cart_gift_unique'
            );

            // prevent duplicate paid lines
            $table->unique(
                ['cart_id', 'product_batch_id', 'price_type'],
                'cart_items_cart_id_product_batch_id_price_type_unique'
            );

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
