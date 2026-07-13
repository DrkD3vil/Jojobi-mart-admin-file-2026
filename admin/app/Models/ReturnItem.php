<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    protected $fillable = [
        'return_id',
        'order_item_id',
        'product_id',
        'product_batch_id',
        'qty',
        'unit_price',
        'refund_amount',
        'condition',
        'reason_code',
        'note',
    ];

    protected $casts = [
        'qty' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'refund_amount' => 'decimal:4',
    ];

    public function parentReturn()
    {
        return $this->belongsTo(ProductReturn::class, 'return_id');
    }
    public function batch()
    {
        return $this->belongsTo(\App\Models\ProductBatch::class, 'product_batch_id')->withTrashed();
    }
}
