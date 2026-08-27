<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    //

    protected $fillable = [
        'session_id',
        'customer_id',
        'total',
        'rewards_points_used',
        'rewards_amount_used',
        'payable_total'
    ];

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }


    // ✅ Add this method (fixes your error)
    public function recalcTotal(): void
    {
        $this->total = (float) $this->items()->sum('total_price');
        $this->save();
    }


}
