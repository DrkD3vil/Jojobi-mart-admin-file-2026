<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One-time email codes for the storefront's password + OTP login and
 * registration flows (see App\Services\OtpService). `payload` carries the
 * pending registration data (name/phone/email/hashed password) between the
 * registration form and the moment the code is verified -- nothing is
 * written to `customers` until then.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_otp_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('purpose', 20); // 'login' | 'register'
            $table->string('email');
            $table->string('code_hash');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->unsignedTinyInteger('resend_count')->default(0);
            $table->timestamp('expires_at');
            $table->timestamp('consumed_at')->nullable();
            $table->json('payload')->nullable(); // pending registration data, purpose='register' only
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->index(['email', 'purpose']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_otp_codes');
    }
};
