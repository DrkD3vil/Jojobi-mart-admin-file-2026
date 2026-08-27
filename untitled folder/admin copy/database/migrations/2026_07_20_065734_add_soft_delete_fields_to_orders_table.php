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
        Schema::table('orders', function (Blueprint $table) {
            // Add soft delete columns
            $table->softDeletes()->after('updated_at');

            // Add trash management columns
            $table->unsignedBigInteger('deleted_by')->nullable()->after('deleted_at');
            $table->text('delete_reason')->nullable()->after('deleted_by');
            $table->timestamp('restored_at')->nullable()->after('delete_reason');

            // Add foreign key constraint for deleted_by
            $table->foreign('deleted_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['deleted_by']);
            $table->dropColumn(['deleted_at', 'deleted_by', 'delete_reason', 'restored_at']);
        });
    }
};
