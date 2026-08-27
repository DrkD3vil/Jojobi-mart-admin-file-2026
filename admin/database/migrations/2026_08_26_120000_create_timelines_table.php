<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('timelines', function (Blueprint $table) {
            $table->id();

            $table->string('timelineable_type');
            $table->unsignedBigInteger('timelineable_id');

            $table->string('event', 60);
            $table->string('title');
            $table->text('description')->nullable();

            $table->string('from_value', 60)->nullable();
            $table->string('to_value', 60)->nullable();

            $table->string('icon', 40)->nullable();

            $table->foreignId('caused_by')->nullable()->constrained('users')->nullOnDelete();

            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['timelineable_type', 'timelineable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timelines');
    }
};
