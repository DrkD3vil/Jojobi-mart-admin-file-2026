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
        Schema::table('orders', function (Blueprint $table) {
            // your orders decimals are (12,4) -> keep same
            $table->decimal('paid_total', 12, 4)->default(0)->after('payable_total');
            $table->decimal('due_total', 12, 4)->default(0)->after('paid_total');
            $table->decimal('change_total', 12, 4)->default(0)->after('due_total');

            // unpaid|partial|paid|refunded|void
            $table->string('payment_status')->default('unpaid')->after('change_total');

            // optional manual note
            $table->text('payment_note')->nullable()->after('payment_status');

            // optional: separate order_discount if you want (not required)
            // $table->decimal('order_discount', 12, 4)->default(0)->after('discount_total');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'paid_total',
                'due_total',
                'change_total',
                'payment_status',
                'payment_note',
                // 'order_discount',
            ]);
        });
    }
};
