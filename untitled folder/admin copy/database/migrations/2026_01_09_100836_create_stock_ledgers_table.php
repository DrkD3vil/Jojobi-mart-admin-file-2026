<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_ledgers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_batch_id')->constrained('product_batches');
            $table->foreignId('location_id')->constrained('locations');

            $table->string('ref_type', 40)->nullable();
            $table->unsignedBigInteger('ref_id')->nullable();
            $table->unsignedBigInteger('line_id')->nullable(); // line id in stock_transaction_lines

            // IN / OUT
            $table->string('direction', 3);

            $table->decimal('qty', 16, 4);
            $table->string('unit', 20)->nullable();

            $table->json('meta')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            $table->index(['product_batch_id', 'location_id']);
            $table->index(['ref_type', 'ref_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_ledgers');
    }
};
