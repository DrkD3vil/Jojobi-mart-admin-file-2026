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

        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')->unique();

            $table->string('name');
            $table->string('phone', 30)->nullable()->index();
            $table->string('email')->nullable()->index();

            $table->string('type')->default('regular')->index(); // regular|vip|wholesale|staff etc.
            $table->boolean('is_active')->default(true)->index();

            $table->text('address')->nullable();
            $table->text('notes')->nullable();

            // Fast balances (denormalized, updated via ledger writes)
            $table->decimal('due_balance', 12, 2)->default(0);      // customer owes you
            $table->decimal('advance_balance', 12, 2)->default(0);  // you owe customer
            $table->decimal('reward_points', 12, 2)->default(0);    // points balance

            $table->timestamps();

            // Prevent duplicates (optional but helpful)
            $table->unique(['phone', 'email'], 'customers_phone_email_unique');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
