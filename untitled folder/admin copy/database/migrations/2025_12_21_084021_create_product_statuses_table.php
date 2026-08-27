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
        Schema::create('product_statuses', function (Blueprint $table) {

            $table->id(); // Primary key

            $table->uuid('uuid')->unique(); // Public UUID

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();

            $table->string('name'); // Offer, Featured, Discounted

            $table->string('slug'); // auto-generated, not globally unique

            $table->string('badge_text')->nullable(); // SALE, HOT

            $table->string('badge_color')->nullable(); // danger, success

            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Prevent duplicate status per product
            $table->unique(['product_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_statuses');
    }
};
