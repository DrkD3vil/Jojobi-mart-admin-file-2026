<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'unit',  // Corrected this line
        'discount_amount',
        'total_price',
        'returned_qty',
        'returned_amount',
        'note'
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'total_price' => 'decimal:4',
         'returned_qty' => 'decimal:4',
         'returned_amount' => 'decimal:4',
        'note' => 'array',
    ];


    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class); // optionally: ->withTrashed()
    }

public function batch(): BelongsTo
{
    return $this->belongsTo(ProductBatch::class, 'product_batch_id')->withTrashed();
}

 // ✅ correct: product_batch_id -> product_batches.id
    public function productBatch(): BelongsTo
    {
        return $this->belongsTo(ProductBatch::class, 'product_batch_id');
    }



    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
