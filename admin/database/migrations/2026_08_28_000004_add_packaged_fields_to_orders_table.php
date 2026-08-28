<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds an orthogonal "packaged" fulfillment step for channel=online orders.
 * Deliberately does NOT touch the `orders.status` enum -- that's load-bearing
 * for the split/merge state machine and status-transition guards elsewhere.
 * This is just a nullable timestamp + FK, guarded the same way as the
 * add_channel_and_shipping_address_to_orders_table migration so it's a
 * no-op if these columns already exist and safe to run on a fresh database.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'packaged_at')) {
                $table->timestamp('packaged_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('orders', 'packaged_by')) {
                $table->foreignId('packaged_by')->nullable()->after('packaged_at')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'packaged_by')) {
                $table->dropConstrainedForeignId('packaged_by');
            }
            if (Schema::hasColumn('orders', 'packaged_at')) {
                $table->dropColumn('packaged_at');
            }
        });
    }
};
