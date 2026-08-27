<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('batch_stocks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_batch_id')->constrained('product_batches')->cascadeOnDelete();
            $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete();

            $table->decimal('on_hand', 16, 4)->default(0);
            $table->decimal('reserved', 16, 4)->default(0);

            $table->timestamps();

            $table->unique(['product_batch_id', 'location_id'], 'batch_stock_unique');
            $table->index(['location_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_stocks');
    }
};
