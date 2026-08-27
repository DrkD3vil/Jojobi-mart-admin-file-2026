<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // merge() (OrderSplitController) sets status='merged' on a child order
        // once its items/payments have been folded back into the parent; the
        // enum never included it, so every merge attempt failed with a MySQL
        // "data truncated" error.
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending','processing','completed','paid','refunded','returned','cancelled','merged') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending','processing','completed','paid','refunded','returned','cancelled') NOT NULL");
    }
};
