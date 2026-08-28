<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `orders` is shared with the admin app, which already adds this column via
 * its own migration (2026_08_27_200358_add_channel_and_shipping_address_to_orders_table).
 * This is guarded so it's a no-op wherever admin's migration already ran,
 * while still bringing a fresh database (a new dev setup, CI, etc.) that only
 * runs frontend's migrations up to the same schema.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // 'pos' = placed by staff at the till, 'online' = customer
            // self-checkout through the public storefront.
            if (!Schema::hasColumn('orders', 'channel')) {
                $table->enum('channel', ['pos', 'online'])->default('pos')->after('location_id')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'channel')) {
                $table->dropColumn('channel');
            }
        });
    }
};
