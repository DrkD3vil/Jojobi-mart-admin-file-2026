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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();

            // offline|online
            $table->string('channel')->index();

            // cash|card|bank|bkash|nagad|rocket|other
            $table->string('method')->index();

            // online trx id (bkash/nagad)
            $table->string('trx_id')->nullable()->index();

            // optional: account label (cash counter / bank name / last4)
            $table->string('account_label')->nullable();

            // match order decimals (12,4)
            $table->decimal('amount', 12, 4);

            // captured|pending|failed|refunded|void
            $table->string('status')->default('captured')->index();

            $table->json('meta')->nullable();
            $table->timestamps();

            // FK
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
