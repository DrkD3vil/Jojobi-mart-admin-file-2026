<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('privileges', function (Blueprint $table) {
            // Links a privilege to the route access_key it actually controls.
            // Nullable: a privilege can still exist as a plain label with no
            // route tied to it, same as today.
            $table->string('access_key')->nullable()->unique()->after('slug');
        });
    }

    public function down(): void
    {
        Schema::table('privileges', function (Blueprint $table) {
            $table->dropColumn('access_key');
        });
    }
};
