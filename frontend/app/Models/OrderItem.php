<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_batch_id',
        'product_name',
        'barcode',
        'price_type',
        'unit_price',
        'quantity',
        'unit',
        'discount_amount',
        'total_price',
        'note',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'total_price' => 'decimal:4',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function batch()
    {
        return $this->belongsTo(ProductBatch::class, 'product_batch_id')->withTrashed();
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
