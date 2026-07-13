<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    protected $fillable = [
        'type','status',
        'from_location_id','to_location_id',
        'ref_type','ref_id',
        'idempotency_key',
        'note','created_by',
    ];

    public function lines()
    {
        return $this->hasMany(StockTransactionLine::class);
    }

    public function fromLocation()
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation()
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }
}
