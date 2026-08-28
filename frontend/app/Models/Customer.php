<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * The storefront's registered shoppers. This is the SAME `customers` table
 * the admin POS already writes to (orders/carts key off it) -- login
 * credentials (password/remember_token/email_verified_at) were added on top
 * via migration, everything else (balances, reward points, order history)
 * is shared with the admin side automatically.
 */
class Customer extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword, Notifiable;

    // due_balance, advance_balance and reward_points intentionally stay out
    // of $fillable -- they must only move through ledger-writing code paths,
    // never a bare ->update($request->all()).
    protected $fillable = [
        'uuid', 'name', 'phone', 'email', 'type', 'is_active', 'address', 'notes',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'due_balance' => 'decimal:2',
        'advance_balance' => 'decimal:2',
        'reward_points' => 'decimal:2',
        'email_verified_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($m) {
            if (empty($m->uuid)) {
                $m->uuid = (string) Str::uuid();
            }
            if (empty($m->type)) {
                $m->type = 'regular';
            }
        });
    }

    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = (str_starts_with($value, '$2y$') && strlen($value) === 60)
            ? $value
            : Hash::make($value);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function rewardLedgers()
    {
        return $this->hasMany(CustomerRewardLedger::class);
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }
}
