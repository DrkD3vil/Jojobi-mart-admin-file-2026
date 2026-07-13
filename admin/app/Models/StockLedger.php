<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockLedger extends Model
{
    protected $fillable = [
        'product_batch_id','location_id',
        'ref_type','ref_id','line_id',
        'direction','qty','unit',
        'meta','created_by',
    ];

    protected $casts = [
        'qty' => 'decimal:4',
        'meta' => 'array',
    ];
}
