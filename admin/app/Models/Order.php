<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'order_no',
        'session_id',
        'customer_id',
        'location_id',
        'subtotal',
        'discount_total',
        'payable_total',
        'rewards_points_used',
        'rewards_amount_used',
        'status',



                // ✅ payment fields
        'paid_total',
        'due_total',
        'change_total',
        'payment_status',
        'payment_note',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function payments()
{
    return $this->hasMany(\App\Models\Payment::class);
}

public function location(): BelongsTo
{
    return $this->belongsTo(Location::class);
}

}
