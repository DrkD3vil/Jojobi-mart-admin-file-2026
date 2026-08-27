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
Schema::table('product_batches', function (Blueprint $table) {
    // 🎁 Free offer (gift) fields
    $table->boolean('is_free_offer_active')
        ->default(false)
        ->after('is_active');

    $table->unsignedBigInteger('free_product_id')
        ->nullable()
        ->after('is_free_offer_active');

    // Nullable is IMPORTANT (offer off = NULL)
    $table->decimal('free_buy_qty', 12, 4)
        ->nullable()
        ->after('free_product_id');

    $table->decimal('free_qty', 12, 4)
        ->nullable()
        ->after('free_buy_qty');

    $table->foreign('free_product_id')
        ->references('id')
        ->on('products')
        ->nullOnDelete();
});

    }

    public function down(): void
    {
        Schema::table('product_batches', function (Blueprint $table) {
            $table->dropForeign(['free_product_id']);
            $table->dropColumn(['free_product_id','free_buy_qty','free_qty','is_free_offer_active']);
        });
    }
};
