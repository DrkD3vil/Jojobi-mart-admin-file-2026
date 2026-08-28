<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\CustomerConflictException;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerOtpCode;
use App\Services\CartService;
use App\Services\CustomerMatcher;
use App\Services\OtpService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use RuntimeException;

/**
 * The second step of both the login and registration flows: a 6-digit
 * email code has already been sent (by AuthenticatedSessionController or
 * RegisteredCustomerController), and its id is stashed in the session --
 * never passed as a route/query parameter, so it can't be guessed or
 * shared by URL. No customer session is established until the code checks
 * out.
 */
class OtpVerificationController extends Controller
{
    public function showRegister(Request $request)
    {
        return $this->show($request, 'register');
    }

    public function showLogin(Request $request)
    {
        return $this->show($request, 'login');
    }

    /**
     * Verifies the code, then actually creates/claims the `customers` row
     * via CustomerMatcher -- nothing was written to that table until now.
     */
    public function verifyRegister(Request $request, OtpService $otpService, CustomerMatcher $matcher, CartService $cartService)
    {
        $pending = $this->pendingOtp($request, 'register');
        if (! $pending) {
            return $this->expiredRedirect('register');
        }

        $data = $request->validate(['code' => 'required|string']);

        $verified = $otpService->verify($pending->id, $data['code']);
        $payload = $verified->payload ?? [];
        // Captured at issue() time from CustomerMatcher::find() -- tells us
        // whether this is claiming an existing (e.g. POS-created) row or
        // creating a brand new one, for the success message below.
        $wasExisting = $verified->customer_id !== null;

        try {
            $customer = $matcher->resolve(
                name: $payload['name'] ?? '',
                phone: $payload['phone'] ?? null,
                email: $payload['email'] ?? $verified->email,
                passwordHash: $payload['password_hash'] ?? null,
                isRegistration: true,
            );
        } catch (CustomerConflictException $e) {
            $this->clearPending($request);

            return redirect()->route('register')->withErrors(['phone' => $e->getMessage()]);
        } catch (ValidationException $e) {
            // Deliberately only discovered here, after the OTP round-trip
            // proved this person owns the email -- not earlier in
            // RegisteredCustomerController::store(), so a registration
            // attempt can't be used to probe whether an email/phone
            // already has an account.
            $this->clearPending($request);

            return redirect()->route('login')->withErrors($e->errors());
        }

        // The OTP round-trip just proved this person controls that inbox.
        $customer->email_verified_at = now();
        $customer->save();

        event(new Registered($customer));

        // Session regeneration below changes the session id, which would
        // otherwise orphan any cart the visitor built while still a guest
        // (carts are keyed by session id until claimed) -- carry it over
        // to the now-known customer first.
        $preLoginSessionId = $request->session()->getId();
        Auth::guard('customer')->login($customer);
        $request->session()->regenerate();
        $cartService->claimGuestCart($preLoginSessionId, $customer->id);

        $this->clearPending($request);

        $message = $wasExisting
            ? 'Welcome back, ' . $customer->name . '! Your in-store account is now linked -- past orders and rewards are all here.'
            : 'Welcome to JOJOBI MART, ' . $customer->name . '!';

        return redirect()->intended(route('home'))->with('success', $message);
    }

    public function verifyLogin(Request $request, OtpService $otpService, CartService $cartService)
    {
        $pending = $this->pendingOtp($request, 'login');
        if (! $pending) {
            return $this->expiredRedirect('login');
        }

        $data = $request->validate(['code' => 'required|string']);

        $verified = $otpService->verify($pending->id, $data['code']);
        $customer = Customer::findOrFail($verified->customer_id);
        $remember = (bool) $request->session()->get('pending_remember', false);

        $preLoginSessionId = $request->session()->getId();
        Auth::guard('customer')->login($customer, $remember);
        $request->session()->regenerate();
        $cartService->claimGuestCart($preLoginSessionId, $customer->id);

        $this->clearPending($request);

        return redirect()->intended(route('home'))->with('success', 'Welcome back!');
    }

    public function resendRegister(Request $request, OtpService $otpService)
    {
        return $this->resend($request, 'register', $otpService);
    }

    public function resendLogin(Request $request, OtpService $otpService)
    {
        return $this->resend($request, 'login', $otpService);
    }

    private function show(Request $request, string $purpose)
    {
        $pending = $this->pendingOtp($request, $purpose);
        if (! $pending) {
            return $this->expiredRedirect($purpose);
        }

        return view('auth.verify-otp', [
            'purpose' => $purpose,
            'email' => $pending->email,
            'verifyRoute' => route($purpose === 'register' ? 'register.verify.submit' : 'login.verify.submit'),
            'resendRoute' => route($purpose === 'register' ? 'register.verify.resend' : 'login.verify.resend'),
            'backRoute' => route($purpose === 'register' ? 'register' : 'login'),
        ]);
    }

    private function resend(Request $request, string $purpose, OtpService $otpService)
    {
        $pending = $this->pendingOtp($request, $purpose);
        if (! $pending) {
            return $this->expiredRedirect($purpose);
        }

        try {
            $otpService->resend($pending->id);
        } catch (RuntimeException $e) {
            return back()->withErrors(['code' => $e->getMessage()]);
        }

        return back()->with('success', 'A new code has been sent.');
    }

    private function pendingOtp(Request $request, string $purpose): ?CustomerOtpCode
    {
        if ($request->session()->get('pending_otp_purpose') !== $purpose) {
            return null;
        }

        $id = $request->session()->get('pending_otp_id');
        if (! $id) {
            return null;
        }

        return CustomerOtpCode::where('id', $id)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->first();
    }

    private function clearPending(Request $request): void
    {
        $request->session()->forget(['pending_otp_id', 'pending_otp_purpose', 'pending_remember']);
    }

    private function expiredRedirect(string $purpose)
    {
        return redirect()->route($purpose)
            ->withErrors(['code' => 'Your verification session has expired. Please start over.']);
    }
}
