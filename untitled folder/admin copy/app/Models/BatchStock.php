<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchStock extends Model
{
    protected $fillable = [
        'product_batch_id',
        'location_id',
        'on_hand',
        'reserved',
    ];

    protected $casts = [
        'on_hand' => 'decimal:4',
        'reserved' => 'decimal:4',
    ];

public function batch()
{
    return $this->belongsTo(ProductBatch::class, 'product_batch_id')->withTrashed();
}


    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function getAvailableAttribute(): float
    {
        return (float)$this->on_hand - (float)$this->reserved;
    }
}
