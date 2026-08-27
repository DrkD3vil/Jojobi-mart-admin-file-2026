<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();

            // Types: SALE_OUT, RETURN_IN, REPLACEMENT_OUT, REPLACEMENT_IN, TRANSFER, ADJUSTMENT_IN, ADJUSTMENT_OUT
            $table->string('type', 40);

            // DRAFT, POSTED, CANCELLED
            $table->string('status', 20)->default('DRAFT');

            // For transfer (from -> to) OR general use
            $table->foreignId('from_location_id')->nullable()->constrained('locations');
            $table->foreignId('to_location_id')->nullable()->constrained('locations');

            // Reference to order/return/exchange etc.
            $table->string('ref_type', 40)->nullable(); // e.g. "order", "return", "exchange", "manual"
            $table->unsignedBigInteger('ref_id')->nullable();

            // Idempotency (POS/API retries)
            $table->string('idempotency_key')->nullable()->unique();

            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            $table->index(['ref_type', 'ref_id']);
            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
