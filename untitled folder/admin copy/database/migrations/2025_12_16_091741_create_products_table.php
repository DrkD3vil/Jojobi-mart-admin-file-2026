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
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Auto increment INT

            $table->uuid('uuid')->unique(); // Public UUID

            $table->string('barcode', 100)->unique(); // Product barcode
            $table->string('name'); // Product name

            $table->text('description')->nullable(); // Product description
            $table->text('note')->nullable(); // Optional internal note

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            $table->foreignId('brand_id')
                ->nullable()
                ->constrained('brands')
                ->nullOnDelete();

            // Soft delete column
            if (!Schema::hasColumn('products', 'deleted_at')) {
                $table->softDeletes();
            }

            // Track who archived (soft deleted)
            if (!Schema::hasColumn('products', 'deleted_by')) {
                $table->unsignedBigInteger('deleted_by')->nullable();
                $table->index('deleted_by');
            }

            // Optional: store reason
            if (!Schema::hasColumn('products', 'deleted_reason')) {
                $table->string('deleted_reason', 255)->nullable();
            }

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            // If users table exists, add FK
            // (If your users id is BIGINT UNSIGNED, this matches)
            $table->foreign('deleted_by')
                  ->references('id')->on('users')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
