<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('privilege_access_keys', function (Blueprint $table) {
            // Marks a row as auto-created from a role's privilege assignment
            // (RoleController) rather than a direct grant made on the Access
            // Keys page. Un-assigning a privilege from a role only ever
            // removes rows this flag is true for, so a manually-added grant
            // on the same access_key/role is never touched.
            $table->boolean('is_auto')->default(false)->after('access_key');
        });
    }

    public function down(): void
    {
        Schema::table('privilege_access_keys', function (Blueprint $table) {
            $table->dropColumn('is_auto');
        });
    }
};
