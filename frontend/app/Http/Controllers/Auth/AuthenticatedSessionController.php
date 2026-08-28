<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    private const MAX_ATTEMPTS = 5;

    private const DECAY_SECONDS = 60;

    public function create()
    {
        return view('auth.login');
    }

    /**
     * Checks the password only (Auth::guard('customer')->validate() never
     * establishes a session) and, if it matches, emails a one-time code and
     * sends the visitor on to OtpVerificationController::verifyLogin() --
     * the session isn't created until that code checks out.
     */
    public function store(Request $request, OtpService $otpService)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $throttleKey = 'login:' . $request->ip() . '|' . $credentials['login'];

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'login' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        if (! Auth::guard('customer')->validate([$field => $credentials['login'], 'password' => $credentials['password']])) {
            RateLimiter::hit($throttleKey, self::DECAY_SECONDS);

            throw ValidationException::withMessages([
                'login' => 'Those credentials don\'t match an account.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        /** @var Customer $customer */
        $customer = Auth::guard('customer')->getLastAttempted();

        if (! $customer->email) {
            // Should be rare-to-impossible now that registration requires
            // an email, but a phone-only walk-in row could still have been
            // given a password directly (e.g. by admin tooling) -- OTP has
            // nowhere to go in that case, so fail clearly instead of
            // crashing inside OtpService::issue().
            throw ValidationException::withMessages([
                'login' => 'This account has no email on file, so we can\'t send a sign-in code. Please add one from account settings, or contact support.',
            ]);
        }

        $otp = $otpService->issue(
            purpose: 'login',
            customerId: $customer->id,
            email: $customer->email,
            ip: $request->ip(),
        );

        $request->session()->put([
            'pending_otp_id' => $otp->id,
            'pending_otp_purpose' => 'login',
            'pending_remember' => $request->boolean('remember'),
        ]);

        return redirect()->route('login.verify');
    }

    public function destroy(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been signed out.');
    }
}
