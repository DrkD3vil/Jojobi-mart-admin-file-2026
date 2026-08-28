<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\CustomerConflictException;
use App\Http\Controllers\Controller;
use App\Services\CustomerMatcher;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class RegisteredCustomerController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    /**
     * The `customers` table is shared with the in-store POS, which creates a
     * customer record (no login) for every walk-in sale. If someone
     * registers online with a phone number or email that already belongs to
     * one of those records, this "claims" it -- adding a password to the
     * existing customer instead of creating a duplicate -- so their
     * in-store order history and reward points show up in the account they
     * just made.
     *
     * This method deliberately does NOT touch the `customers` table or log
     * anyone in. A one-time code is emailed to prove the address first;
     * CustomerMatcher only actually creates/updates the row once that code
     * comes back verified (see OtpVerificationController::verifyRegister()).
     */
    public function store(Request $request, CustomerMatcher $matcher, OtpService $otpService)
    {
        try {
            $existing = $matcher->find($request->input('phone'), $request->input('email'));
        } catch (CustomerConflictException $e) {
            throw ValidationException::withMessages(['phone' => $e->getMessage()]);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'email' => ['required', 'email', 'max:255', Rule::unique('customers', 'email')->ignore($existing?->id)],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Re-check against the validated values -- fails fast, before any
        // OTP email goes out, if phone and email point at two different
        // existing accounts.
        try {
            $matcher->findConflict($data['phone'], $data['email']);
        } catch (CustomerConflictException $e) {
            throw ValidationException::withMessages(['phone' => $e->getMessage()]);
        }

        // Whether this phone/email already has a password on file (i.e. an
        // existing account, not just a walk-in POS record) is deliberately
        // NOT checked here -- only after the OTP round-trip proves this
        // person actually owns the email (see
        // OtpVerificationController::verifyRegister()), so a registration
        // attempt can't be used to probe whether an email/phone already has
        // an account.
        $otp = $otpService->issue(
            purpose: 'register',
            customerId: $existing?->id,
            email: $data['email'],
            payload: [
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'password_hash' => Hash::make($data['password']),
            ],
            ip: $request->ip(),
        );

        $request->session()->put([
            'pending_otp_id' => $otp->id,
            'pending_otp_purpose' => 'register',
        ]);

        return redirect()->route('register.verify');
    }
}
