<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();

            $table->string('expense_no')->unique(); // EXP-20260123-0001
            $table->date('expense_date');

            $table->foreignId('expense_category_id')->nullable()->constrained('expense_categories')->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete(); // you already have locations
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            $table->string('vendor_name')->nullable();
            $table->string('payment_method')->nullable(); // cash/bkash/bank/card
            $table->string('reference_no')->nullable(); // trx id / voucher no

            $table->decimal('amount', 14, 4)->default(0);
            $table->string('currency', 10)->default('BDT');

            $table->string('receipt_image')->nullable(); // storage path
            $table->json('meta')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['expense_date']);
            $table->index(['location_id', 'expense_date']);
            $table->index(['expense_category_id', 'expense_date']);
            $table->index(['payment_method', 'expense_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
