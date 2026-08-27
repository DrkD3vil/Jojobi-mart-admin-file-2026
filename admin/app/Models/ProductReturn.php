<?php

namespace App\Models;

use App\Models\Concerns\HasTimeline;
use Illuminate\Database\Eloquent\Model;

class ProductReturn extends Model
{
    use HasTimeline;

    protected $table = 'returns';

    protected $fillable = [
        'return_no','order_id','customer_id','location_id',
        'status','refund_method','refund_amount',
        'idempotency_key','note','created_by',
    ];

    protected $casts = [
        'refund_amount' => 'decimal:4',
    ];

    public function items()
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
