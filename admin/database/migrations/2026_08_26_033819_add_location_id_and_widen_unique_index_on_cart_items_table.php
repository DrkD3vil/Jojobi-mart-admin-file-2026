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
        // CartController already has fully-written, location-aware reservation
        // logic guarded behind Schema::hasColumn('cart_items', 'location_id')
        // everywhere it matters -- the column itself was just never created,
        // so that logic has always silently been dead code.
        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('location_id')
                ->nullable()
                ->after('product_image_id')
                ->constrained('locations')
                ->nullOnDelete();
        });

        // The existing unique index only covers (cart_id, product_batch_id,
        // price_type), but the app legitimately creates separate lines for the
        // same batch/price_type differentiated by unit (e.g. kg vs pcs) and,
        // now, location -- those inserts were one unique-key violation away
        // from failing. Widen it to match what the app actually enforces.
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique('cart_items_cart_id_product_batch_id_price_type_unique');
            $table->unique(
                ['cart_id', 'product_batch_id', 'price_type', 'unit', 'location_id', 'is_gift'],
                'cart_items_cart_batch_price_unit_location_gift_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique('cart_items_cart_batch_price_unit_location_gift_unique');
            $table->unique(
                ['cart_id', 'product_batch_id', 'price_type'],
                'cart_items_cart_id_product_batch_id_price_type_unique'
            );
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('location_id');
        });
    }
};
