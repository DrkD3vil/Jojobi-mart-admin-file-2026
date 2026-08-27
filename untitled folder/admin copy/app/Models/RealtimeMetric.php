<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class RealtimeMetric extends Model
{
    protected $fillable = [
        'location_id',
        'last_order_id',
        'pending_orders',
        'low_stock_items',
        'abandoned_carts',
    ];

    protected $casts = [
        'location_id' => 'integer',
        'last_order_id' => 'integer',
        'pending_orders' => 'integer',
        'low_stock_items' => 'integer',
        'abandoned_carts' => 'integer',
    ];
}
