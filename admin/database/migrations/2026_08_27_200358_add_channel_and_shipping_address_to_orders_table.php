<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The live database already carries channel/shipping_* columns on `orders`
 * (added directly, outside of any tracked migration, before this file
 * existed) -- every column add below is guarded so this is a no-op there,
 * while still bringing a fresh database (a new dev setup, CI, etc.) up to
 * the same schema.
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
            if (!Schema::hasColumn('orders', 'shipping_name')) {
                $table->string('shipping_name')->nullable()->after('channel');
            }
            if (!Schema::hasColumn('orders', 'shipping_phone')) {
                $table->string('shipping_phone', 30)->nullable()->after('shipping_name');
            }
            if (!Schema::hasColumn('orders', 'shipping_address')) {
                $table->text('shipping_address')->nullable()->after('shipping_phone');
            }
            if (!Schema::hasColumn('orders', 'shipping_note')) {
                $table->text('shipping_note')->nullable()->after('shipping_address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['channel', 'shipping_name', 'shipping_phone', 'shipping_address', 'shipping_note']);
        });
    }
};
