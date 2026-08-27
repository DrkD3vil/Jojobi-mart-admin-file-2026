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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();

            // ❌ NOT unique
            $table->string('session_id')->index();

            $table->unsignedBigInteger('customer_id')->nullable()->index();

            $table->decimal('total', 12, 4)->default(0);

            $table->decimal('rewards_points_used', 12, 2)->default(0);
            $table->decimal('rewards_amount_used', 12, 4)->default(0);

            // NULL = active cart, NOT NULL = checked out
            $table->decimal('payable_total', 12, 4)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
