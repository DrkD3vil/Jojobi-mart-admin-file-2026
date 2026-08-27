<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerRewardLedger extends Model
{
    //
    protected $fillable = [
        'customer_id','action','direction','points',
        'ref_type','ref_id',
        'channel','terminal_id','created_by',
        'idempotency_key','note'
    ];

    protected $casts = [
        'points' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
