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
        Schema::table('order_items', function (Blueprint $table) {
            $table->boolean('is_gift')->default(false)->after('total_price');
            $table->string('gift_source')->nullable()->after('is_gift');
            $table->unsignedBigInteger('gift_source_id')->nullable()->after('gift_source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['is_gift', 'gift_source', 'gift_source_id']);
        });
    }
};
