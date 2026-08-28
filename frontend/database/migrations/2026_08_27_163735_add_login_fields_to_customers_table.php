<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The `customers` table already exists (admin POS creates a customer per
 * walk-in sale). It never needed login credentials until now -- this adds
 * them additively so the existing table, and every order/cart already
 * pointing at it, is untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'password')) {
                $table->string('password')->nullable()->after('email');
            }
            if (!Schema::hasColumn('customers', 'remember_token')) {
                $table->rememberToken()->after('password');
            }
            if (!Schema::hasColumn('customers', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('remember_token');
            }
        });

        // Nullable-safe: MySQL allows any number of NULLs through a unique
        // index, so the existing walk-in customer (no login yet) is fine.
        Schema::table('customers', function (Blueprint $table) {
            $table->unique('email');
            $table->unique('phone');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->dropUnique(['phone']);
            $table->dropColumn(['password', 'remember_token', 'email_verified_at']);
        });
    }
};
