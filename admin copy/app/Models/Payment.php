<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model

{
    protected $fillable = [
        'order_id','channel','method','trx_id','account_label','amount','status','meta'
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'meta' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
