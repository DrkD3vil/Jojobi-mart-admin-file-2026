<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('realtime_metrics', function (Blueprint $table) {
            $table->id();

            // 0 = all locations (global)
            $table->unsignedBigInteger('location_id')->default(0);

            $table->unsignedBigInteger('last_order_id')->default(0);

            $table->unsignedInteger('pending_orders')->default(0);
            $table->unsignedInteger('low_stock_items')->default(0);
            $table->unsignedInteger('abandoned_carts')->default(0);

            $table->timestamps();

            $table->unique('location_id');
            $table->index(['updated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('realtime_metrics');
    }
};
