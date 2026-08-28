<?php

namespace App\Services;

use App\Mail\CustomerOtpMail;
use App\Models\CustomerOtpCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use RuntimeException;

/**
 * Email-only one-time codes for the storefront's password + OTP login and
 * registration flows. There is no SMS/email gateway beyond the `log` mail
 * driver configured in .env right now -- codes land in
 * storage/logs/laravel.log in this environment. The raw code only ever
 * exists in memory here and inside CustomerOtpMail; everything persisted
 * is a salted hash.
 */
class OtpService
{
    private const MAX_ATTEMPTS = 5;

    private const MAX_RESENDS = 3;

    private const RESEND_COOLDOWN_SECONDS = 60;

    private const EXPIRES_IN_MINUTES = 5;

    private const MAX_PER_EMAIL_PER_HOUR = 5;

    private const MAX_PER_IP_PER_HOUR = 20;

    public function issue(string $purpose, ?int $customerId, string $email, array $payload = [], ?string $ip = null): CustomerOtpCode
    {
        $this->guardRateLimits($email, $ip);

        $code = $this->generateCode();

        $otp = CustomerOtpCode::create([
            'customer_id' => $customerId,
            'purpose' => $purpose,
            'email' => $email,
            'code_hash' => $this->hash($code),
            'attempts' => 0,
            'resend_count' => 0,
            'expires_at' => now()->addMinutes(self::EXPIRES_IN_MINUTES),
            'payload' => $payload,
            'ip_address' => $ip,
        ]);

        $this->send($email, $code, $purpose);

        return $otp;
    }

    /**
     * Re-sends a code on the SAME row (never creates a new one), so a
     * pending flow's session-stashed otp id stays valid.
     */
    public function resend(int $otpId): CustomerOtpCode
    {
        return DB::transaction(function () use ($otpId) {
            $otp = CustomerOtpCode::whereKey($otpId)->lockForUpdate()->first();

            if (! $otp) {
                throw new RuntimeException('This verification session is no longer valid. Please start over.');
            }

            if ($otp->consumed_at) {
                throw new RuntimeException('This code has already been used. Please start over.');
            }

            if ($otp->resend_count >= self::MAX_RESENDS) {
                throw new RuntimeException('Too many resend attempts. Please start over.');
            }

            // Carbon 3's diffInSeconds() defaults to a *signed* diff (unlike
            // Carbon 2) -- pass absolute:true explicitly, otherwise this
            // comes back negative (since updated_at is in the past) and the
            // cooldown check below never triggers correctly.
            $secondsSinceLastSend = $otp->updated_at->diffInSeconds(now(), true);
            if ($secondsSinceLastSend < self::RESEND_COOLDOWN_SECONDS) {
                $wait = (int) ceil(self::RESEND_COOLDOWN_SECONDS - $secondsSinceLastSend);
                throw new RuntimeException("Please wait {$wait}s before requesting another code.");
            }

            // The row's own ip_address (captured when it was first issued)
            // stands in for the caller's IP here, since resend() is only
            // ever invoked with an otp id, not a fresh request.
            $this->guardRateLimits($otp->email, $otp->ip_address);

            $code = $this->generateCode();

            $otp->code_hash = $this->hash($code);
            $otp->expires_at = now()->addMinutes(self::EXPIRES_IN_MINUTES);
            $otp->resend_count += 1;
            $otp->save();

            $this->send($otp->email, $code, $otp->purpose);

            return $otp;
        });
    }

    public function verify(int $otpId, string $rawCode): CustomerOtpCode
    {
        // Deliberately does NOT throw from inside the transaction closure:
        // DB::transaction() rolls back on any exception, which would also
        // discard the attempts++ write on a wrong code below, letting
        // someone retry forever. Instead the closure always returns
        // normally (committing whatever it wrote) and reports failure via
        // $error, which is thrown as a ValidationException afterwards.
        $error = null;

        $otp = DB::transaction(function () use ($otpId, $rawCode, &$error) {
            $otp = CustomerOtpCode::whereKey($otpId)->lockForUpdate()->first();

            if (! $otp) {
                $error = 'This verification session is no longer valid. Please start over.';

                return null;
            }

            if ($otp->consumed_at) {
                $error = 'This code has already been used. Please start over.';

                return $otp;
            }

            if ($otp->expires_at->isPast()) {
                $error = 'This code has expired. Please request a new one.';

                return $otp;
            }

            if ($otp->attempts >= self::MAX_ATTEMPTS) {
                $error = 'Too many incorrect attempts. Please request a new code.';

                return $otp;
            }

            if (! hash_equals($otp->code_hash, $this->hash($rawCode))) {
                $otp->increment('attempts');

                $error = 'That code is incorrect.';

                return $otp;
            }

            $otp->consumed_at = now();
            $otp->save();

            return $otp;
        });

        if ($error !== null) {
            throw ValidationException::withMessages(['code' => $error]);
        }

        return $otp;
    }

    private function guardRateLimits(string $email, ?string $ip): void
    {
        $emailKey = 'otp-send:' . $email;
        if (RateLimiter::tooManyAttempts($emailKey, self::MAX_PER_EMAIL_PER_HOUR)) {
            throw new RuntimeException('Too many verification codes requested for this email. Please try again later.');
        }

        $ipKey = $ip ? 'otp-send:' . $ip : null;
        if ($ipKey && RateLimiter::tooManyAttempts($ipKey, self::MAX_PER_IP_PER_HOUR)) {
            throw new RuntimeException('Too many verification codes requested from this network. Please try again later.');
        }

        RateLimiter::hit($emailKey, 3600);
        if ($ipKey) {
            RateLimiter::hit($ipKey, 3600);
        }
    }

    private function generateCode(): string
    {
        return (string) random_int(100000, 999999);
    }

    private function hash(string $code): string
    {
        return hash('sha256', $code . config('app.key'));
    }

    private function send(string $email, string $code, string $purpose): void
    {
        Mail::to($email)->send(new CustomerOtpMail($code, $purpose));
    }
}
