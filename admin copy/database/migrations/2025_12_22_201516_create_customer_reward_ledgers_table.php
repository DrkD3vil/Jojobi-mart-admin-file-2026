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
        Schema::create('customer_reward_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();

            $table->string('action')->index(); // earn|redeem|adjust
            $table->decimal('points', 12, 2);  // positive for earn/adjust, positive for redeem too (direction handles it)
            $table->string('direction')->index(); // add|subtract

            $table->string('ref_type')->nullable()->index();
            $table->unsignedBigInteger('ref_id')->nullable()->index();

            $table->string('channel')->default('pos')->index(); // pos|online|offline
            $table->string('terminal_id')->nullable()->index();
            $table->string('created_by')->nullable()->index();

            $table->string('idempotency_key')->nullable();
            $table->unique(['customer_id', 'idempotency_key'], 'cust_reward_idempotency_unique');

            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_reward_ledgers');
    }
};
