<?php

namespace App\Services;

use App\Exceptions\CustomerConflictException;
use App\Models\Customer;
use Illuminate\Validation\ValidationException;

/**
 * The `customers` table is shared with the in-store POS, which creates a
 * customer record (no login) for every walk-in sale. Someone registering
 * (or, in future, using guest checkout) online with a phone number OR
 * email address that already belongs to one of those records should
 * "claim" it -- adding/updating login details on the existing row instead
 * of creating a duplicate -- so their in-store order history and reward
 * points show up in the account they end up with.
 */
class CustomerMatcher
{
    /**
     * Read-only lookup: the existing customer (if any) that `$phone` or
     * `$email` already belongs to. Used before anything is persisted, e.g.
     * to resolve a `Rule::unique(...)->ignore()` target at validation time
     * without going through resolve()'s create/update path.
     *
     * @throws CustomerConflictException if phone and email match two
     *         different existing rows.
     */
    public function find(?string $phone, ?string $email): ?Customer
    {
        $byPhone = $phone ? Customer::where('phone', $phone)->first() : null;
        $byEmail = $email ? Customer::where('email', $email)->first() : null;

        if ($byPhone && $byEmail && $byPhone->id !== $byEmail->id) {
            throw new CustomerConflictException();
        }

        return $byPhone ?? $byEmail;
    }

    /**
     * Same conflict check as find(), without needing the matched row --
     * meant to be called early (before any OTP email goes out) so a doomed
     * registration fails fast instead of after a round-trip.
     */
    public function findConflict(?string $phone, ?string $email): void
    {
        $this->find($phone, $email);
    }

    /**
     * Actually creates or updates the `customers` row. Only call this once
     * the person has proven they own the email/phone they're claiming
     * (e.g. after OTP verification) -- unlike find(), this persists.
     */
    public function resolve(string $name, ?string $phone, ?string $email, ?string $passwordHash = null, bool $isRegistration = false): Customer
    {
        $existing = $this->find($phone, $email);

        if ($existing) {
            if ($isRegistration && $existing->password) {
                throw ValidationException::withMessages([
                    'phone' => 'An account with this phone number already exists. Please sign in instead.',
                ]);
            }

            $existing->name = $name;
            if (!empty($email)) {
                $existing->email = $email;
            }
            if (!empty($phone) && empty($existing->phone)) {
                $existing->phone = $phone;
            }
            $existing->is_active = true;
            if ($passwordHash) {
                $existing->password = $passwordHash;
            }
            $existing->save();

            return $existing;
        }

        $customer = new Customer([
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'type' => 'regular',
            'is_active' => true,
        ]);

        if ($passwordHash) {
            $customer->password = $passwordHash;
        }

        $customer->save();

        return $customer;
    }
}
