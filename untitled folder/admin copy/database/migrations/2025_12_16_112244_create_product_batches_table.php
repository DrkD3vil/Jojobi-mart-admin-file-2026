<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();

            // Product relationship
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            // Batch info
            $table->string('batch_sku', 100)
                ->unique()
                ->comment('Unique SKU per product batch');
            $table->string('batch_no')->nullable()->comment('Optional batch number');

            // Stock quantity (supports fractional quantities like 0.100kg)
            $table->decimal('quantity', 12, 4)->default(0)->comment('Stock quantity with up to 4 decimal places');

            // Unit (Kg, Gram, Liter, Piece, etc.)
            $table->enum('unit', ['pcs', 'dozen', 'box', 'kg', 'g', 'l', 'ml'])
                ->default('pcs')
                ->comment('Unit of measurement');

            // Purchase price
            $table->decimal('buy_price', 12, 4)->default(0)->comment('Cost price per unit');

            // Retail prices
            $table->decimal('original_sell_price', 12, 4)->default(0)->comment('Original retail price');
            $table->decimal('discounted_price', 12, 4)->nullable()->comment('Price after discount');
            $table->decimal('discount_percentage', 5, 2)->nullable()->comment('Discount %');
            $table->decimal('sell_price', 12, 4)->default(0)->comment('Current retail price | After discounted');

            // Wholesale pricing
            $table->decimal('whole_sell_price', 12, 4)->nullable()->comment('Price for wholesale');
            $table->decimal('whole_sell_min_qty', 12, 4)->nullable()->comment('Minimum quantity for wholesale');
            $table->decimal('whole_sell_max_qty', 12, 4)->nullable()->comment('Maximum quantity for wholesale');

            // Customer-specific pricing condition
            $table->decimal('customer_whole_price', 12, 4)->nullable()->comment('Customer-specific price');
            $table->decimal('customer_whole_min_qty', 12, 4)->nullable()->comment('Min quantity for customer-specific price');
            $table->decimal('customer_whole_max_qty', 12, 4)->nullable()->comment('Max quantity for customer-specific price');

            // Dates
            $table->date('manufacture_date')->nullable();
            $table->date('expiry_date')->nullable();

            // Availability flags
            $table->boolean('is_online')->default(true)->comment('Available in online store');
            $table->boolean('is_offline')->default(true)->comment('Available in offline store');
            $table->boolean('is_pos')->default(true)->comment('Available in POS');
            $table->boolean('is_active')->default(true)->comment('Active batch');

            $table->softDeletes(); // adds deleted_at
            $table->index('deleted_at');

            // Optional metadata
            $table->text('notes')->nullable()->comment('Extra notes or metadata');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_batches');
    }
};
