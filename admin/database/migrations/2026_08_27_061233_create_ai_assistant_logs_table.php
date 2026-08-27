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
        Schema::create('ai_assistant_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('interaction_ref')->nullable()->index();
            $table->text('prompt');
            $table->json('tool_calls')->nullable();
            $table->json('tool_results')->nullable();
            $table->text('response_text')->nullable();
            $table->boolean('was_write_action')->default(false);
            $table->boolean('was_allowed')->default(true);
            $table->enum('status', ['completed', 'pending_confirmation', 'denied', 'cancelled', 'error'])->default('completed');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_assistant_logs');
    }
};
