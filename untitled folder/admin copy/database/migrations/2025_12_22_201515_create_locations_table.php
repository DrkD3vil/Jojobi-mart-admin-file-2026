<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();

            $table->string('name')->index();
            $table->string('code')->nullable()->unique(); // optional: WH-01, SHOP-01
            $table->enum('type', ['warehouse','store','pos','damaged','return_holding'])->default('store');
            $table->boolean('is_active')->default(true);

            $table->text('address')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
