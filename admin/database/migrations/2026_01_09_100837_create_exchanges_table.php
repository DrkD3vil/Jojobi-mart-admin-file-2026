<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exchanges', function (Blueprint $table) {
            $table->id();
            $table->string('exchange_no', 40)->unique();

            $table->foreignId('order_id')->constrained('orders');
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->foreignId('location_id')->constrained('locations');

            // DRAFT, POSTED, CANCELLED
            $table->string('status', 20)->default('DRAFT');

            // price difference after return/issue
            $table->decimal('price_difference', 16, 4)->default(0);

            $table->string('idempotency_key')->nullable()->unique();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchanges');
    }
};
