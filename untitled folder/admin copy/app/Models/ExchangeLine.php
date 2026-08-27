<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeLine extends Model
{
    protected $fillable = [
        'exchange_id','mode','order_item_id',
        'product_id','product_batch_id',
        'qty','unit_price','meta',
    ];

    protected $casts = [
        'qty' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'meta' => 'array',
    ];

    public function exchange()
    {
        return $this->belongsTo(Exchange::class);
    }
}
