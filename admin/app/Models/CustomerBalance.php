<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerBalance extends Model
{
    //
    protected $fillable = [
        'customer_id','kind','direction','amount',
        'ref_type','ref_id',
        'channel','terminal_id','created_by',
        'idempotency_key','note'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
