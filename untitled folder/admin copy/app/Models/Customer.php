<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
    use Illuminate\Database\Eloquent\Relations\HasMany;


class Customer extends Model
{
    //
    protected $fillable = [
        'uuid','name','phone','email','type','is_active','address','notes',
        'due_balance','advance_balance','reward_points',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'due_balance' => 'decimal:2',
        'advance_balance' => 'decimal:2',
        'reward_points' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($m) {
            if (empty($m->uuid)) $m->uuid = (string) Str::uuid();
        });
    }

    public function balanceLedgers()
    {
        return $this->hasMany(CustomerBalance::class);
    }

    public function rewardLedgers()
    {
        return $this->hasMany(CustomerRewardLedger::class);
    }


public function orders(): HasMany
{
    return $this->hasMany(\App\Models\Order::class);
    // if FK is not customer_id, pass it:
    return $this->hasMany(Order::class, 'customer_id');
}
}
