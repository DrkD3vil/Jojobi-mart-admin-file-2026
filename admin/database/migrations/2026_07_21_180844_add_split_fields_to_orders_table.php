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
            $table->foreignId('parent_order_id')
                ->nullable()
                ->after('location_id')
                ->constrained('orders')
                ->onDelete('set null');

            $table->string('split_reason')
                ->nullable()
                ->after('parent_order_id');

            $table->string('split_status')
                ->default('original')
                ->after('split_reason')
                ->comment('original, split_parent, split_child, merged');

            $table->boolean('is_split_child')
                ->default(false)
                ->after('split_status');

            $table->integer('split_sequence')
                ->nullable()
                ->after('is_split_child');

            $table->foreignId('split_by')
                ->nullable()
                ->after('split_sequence')
                ->constrained('users')
                ->onDelete('set null');

            $table->timestamp('split_at')
                ->nullable()
                ->after('split_by');

            $table->unsignedBigInteger('original_order_id')
                ->nullable()
                ->after('split_at');

            $table->text('split_notes')
                ->nullable()
                ->after('original_order_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['parent_order_id']);
            $table->dropForeign(['split_by']);
            $table->dropColumn([
                'parent_order_id',
                'split_reason',
                'split_status',
                'is_split_child',
                'split_sequence',
                'split_by',
                'split_at',
                'original_order_id',
                'split_notes'
            ]);
        });
    }
};
