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
        Schema::create('order_splits', function (Blueprint $table) {
            $table->id();

            $table->foreignId('original_order_id')
                ->constrained('orders')
                ->onDelete('cascade');

            $table->foreignId('parent_order_id')
                ->constrained('orders')
                ->onDelete('cascade');

            $table->foreignId('child_order_id')
                ->constrained('orders')
                ->onDelete('cascade');

            $table->json('split_items')
                ->nullable()
                ->comment('Items moved from parent to child');

            $table->decimal('split_amount', 15, 2)
                ->default(0);

            $table->string('split_type')
                ->default('manual')
                ->comment('manual, automatic, partial_payment, partial_fulfillment');

            $table->string('split_reason')
                ->nullable();

            $table->text('split_notes')
                ->nullable();

            $table->foreignId('created_by')
                ->constrained('users')
                ->onDelete('cascade');

            $table->timestamp('split_at')
                ->useCurrent();

            $table->timestamps();

            $table->index(['original_order_id', 'child_order_id']);
            $table->index(['parent_order_id', 'child_order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_splits');
    }
};
