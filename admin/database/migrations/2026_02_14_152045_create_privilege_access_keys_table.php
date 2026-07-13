<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('privilege_access_keys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('privilege_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->string('access_key');
            $table->timestamps();

            // Add a shorter unique index name to avoid exceeding the MySQL limit
            $table->unique(['privilege_id', 'access_key', 'user_id', 'role_id'], 'priv_access_key_user_role_unique'); // Custom short index name

            $table->foreign('privilege_id')->references('id')->on('privileges')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');

            $table->index('access_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('privilege_access_keys');
    }
};
