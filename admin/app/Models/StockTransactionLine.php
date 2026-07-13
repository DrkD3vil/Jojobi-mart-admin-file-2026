<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransactionLine extends Model
{
    protected $fillable = [
        'stock_transaction_id',
        'product_id','product_batch_id',
        'qty','unit','unit_cost','unit_price',
        'meta',
    ];

    protected $casts = [
        'qty' => 'decimal:4',
        'unit_cost' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'meta' => 'array',
    ];

    public function tx()
    {
        return $this->belongsTo(StockTransaction::class, 'stock_transaction_id');
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
