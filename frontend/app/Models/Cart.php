<?php

namespace App\Models;

use App\Models\Concerns\HasTimeline;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasTimeline;

    protected $fillable = [
        'session_id',
        'customer_id',
        'total',
        'rewards_points_used',
        'rewards_amount_used',
        'payable_total',
    ];

    protected $casts = [
        'total' => 'decimal:4',
        'payable_total' => 'decimal:4',
        'rewards_points_used' => 'decimal:2',
        'rewards_amount_used' => 'decimal:4',
    ];

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function recalcTotal(): void
    {
        $this->total = (float) $this->items()->sum('total_price');
        $this->save();
    }
}
