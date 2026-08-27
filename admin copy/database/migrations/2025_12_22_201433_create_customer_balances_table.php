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
        Schema::create('customer_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();

            // ledger type: due increases when customer owes, advance increases when customer prepays
            $table->string('kind')->index(); // due|advance
            $table->string('direction')->index(); // debit|credit
            // debit: increases the kind; credit: decreases the kind (like accounting)

            $table->decimal('amount', 12, 2); // always positive

            // For linking (later): sale_id, invoice_id, payment_id etc.
            $table->string('ref_type')->nullable()->index(); // 'sale','payment','adjustment'
            $table->unsignedBigInteger('ref_id')->nullable()->index();

            // Channels
            $table->string('channel')->default('pos')->index(); // pos|online|offline
            $table->string('terminal_id')->nullable()->index(); // POS device/terminal
            $table->string('created_by')->nullable()->index(); // user id or name (string for flexibility)

            // Idempotency to avoid duplicates from offline sync
            $table->string('idempotency_key')->nullable();
            $table->unique(['customer_id', 'idempotency_key'], 'cust_balance_idempotency_unique');

            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'kind', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_balances');
    }
};
