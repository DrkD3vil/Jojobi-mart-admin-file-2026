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
        Schema::create('order_items', function ($table) {
            $table->id();

            $table->unsignedBigInteger('order_id')->index();




            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_batch_id')->nullable();

            $table->string('product_name');
            $table->string('barcode')->nullable();

            $table->string('price_type');
            $table->decimal('unit_price', 12, 4);
            $table->decimal('quantity', 12, 2);
            // kg / g / l / ml / pcs
            $table->string('unit')->nullable();

            $table->decimal('discount_amount', 12, 4)->default(0);
            $table->decimal('total_price', 12, 4);

            $table->decimal('returned_qty', 16, 4)->default(0);
            $table->decimal('returned_amount', 16, 4)->default(0);

            $table->text('note')->nullable(); // Optional internal note

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
