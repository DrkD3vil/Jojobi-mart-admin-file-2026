<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['created_at'], 'idx_orders_created_at');
            $table->index(['location_id', 'created_at'], 'idx_orders_loc_created');
            $table->index(['status', 'created_at'], 'idx_orders_status_created');
            $table->index(['payment_status', 'created_at'], 'idx_orders_paystatus_created');
            $table->index(['location_id', 'status', 'payment_status', 'created_at'], 'idx_orders_loc_status_pay_created');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index(['order_id'], 'idx_order_items_order');
            $table->index(['created_at'], 'idx_order_items_created');
            $table->index(['product_batch_id'], 'idx_order_items_batch');
            $table->index(['product_id'], 'idx_order_items_product');
            $table->index(['order_id', 'created_at'], 'idx_order_items_order_created');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index(['created_at'], 'idx_payments_created');
            $table->index(['order_id'], 'idx_payments_order');
            $table->index(['status', 'created_at'], 'idx_payments_status_created');
        });

        Schema::table('returns', function (Blueprint $table) {
            $table->index(['created_at'], 'idx_returns_created');
            $table->index(['location_id', 'created_at'], 'idx_returns_loc_created');
        });

        Schema::table('exchanges', function (Blueprint $table) {
            $table->index(['created_at'], 'idx_exchanges_created');
            $table->index(['location_id', 'created_at'], 'idx_exchanges_loc_created');
        });

        Schema::table('batch_stocks', function (Blueprint $table) {
            $table->index(['location_id', 'product_batch_id'], 'idx_batchstocks_loc_batch');
            $table->index(['product_batch_id'], 'idx_batchstocks_batch');
        });

        Schema::table('product_batches', function (Blueprint $table) {
            $table->index(['id'], 'idx_product_batches_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_created_at');
            $table->dropIndex('idx_orders_loc_created');
            $table->dropIndex('idx_orders_status_created');
            $table->dropIndex('idx_orders_paystatus_created');
            $table->dropIndex('idx_orders_loc_status_pay_created');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('idx_order_items_order');
            $table->dropIndex('idx_order_items_created');
            $table->dropIndex('idx_order_items_batch');
            $table->dropIndex('idx_order_items_product');
            $table->dropIndex('idx_order_items_order_created');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('idx_payments_created');
            $table->dropIndex('idx_payments_order');
            $table->dropIndex('idx_payments_status_created');
        });

        Schema::table('returns', function (Blueprint $table) {
            $table->dropIndex('idx_returns_created');
            $table->dropIndex('idx_returns_loc_created');
        });

        Schema::table('exchanges', function (Blueprint $table) {
            $table->dropIndex('idx_exchanges_created');
            $table->dropIndex('idx_exchanges_loc_created');
        });

        Schema::table('batch_stocks', function (Blueprint $table) {
            $table->dropIndex('idx_batchstocks_loc_batch');
            $table->dropIndex('idx_batchstocks_batch');
        });

        Schema::table('product_batches', function (Blueprint $table) {
            $table->dropIndex('idx_product_batches_id');
        });
    }
};
