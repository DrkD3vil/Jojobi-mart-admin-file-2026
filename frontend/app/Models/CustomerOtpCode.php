<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A single OTP challenge -- one row per code sent, reused across resends
 * (see OtpService::resend()) until it's consumed or the flow is restarted.
 * `code_hash` is a salted digest, never the raw code (see OtpService).
 */
class CustomerOtpCode extends Model
{
    protected $fillable = [
        'customer_id',
        'purpose',
        'email',
        'code_hash',
        'attempts',
        'resend_count',
        'expires_at',
        'consumed_at',
        'payload',
        'ip_address',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'consumed_at' => 'datetime',
        'payload' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
