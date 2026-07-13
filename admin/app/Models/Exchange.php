<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    protected $fillable = [
        'exchange_no','order_id','customer_id','location_id',
        'status','price_difference',
        'idempotency_key','note','created_by'
    ];

    protected $casts = [
        'price_difference' => 'decimal:4',
    ];

    public function lines()
    {
        return $this->hasMany(ExchangeLine::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
