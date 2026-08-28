<?php

namespace App\Models;

use App\Models\Concerns\HasTimeline;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use SoftDeletes, HasTimeline;

    protected $fillable = [
        'order_no',
        'session_id',
        'customer_id',
        'location_id',
        'channel',
        'subtotal',
        'discount_total',
        'payable_total',
        'rewards_points_used',
        'rewards_amount_used',
        'paid_total',
        'due_total',
        'change_total',
        'payment_status',
        'payment_note',
        'status',
        'shipping_name',
        'shipping_phone',
        'shipping_address',
        'shipping_note',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'payable_total' => 'decimal:2',
        'rewards_points_used' => 'decimal:2',
        'rewards_amount_used' => 'decimal:2',
        'paid_total' => 'decimal:2',
        'due_total' => 'decimal:2',
        'change_total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
        return $this->hasMany(Payment::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function generateOrderNo(): string
    {
        return 'JJB-' . now()->format('ymdHis') . '-' . Str::upper(Str::random(4));
    }

    public function scopeForCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Order placed',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'paid' => 'Paid',
            'refunded' => 'Refunded',
            'returned' => 'Returned',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    /**
     * `channel` is the authoritative source of truth (shared with admin's
     * `orders` table). Legacy rows written before this column existed have
     * no channel value, so fall back to the shipping_address presence check
     * for those.
     */
    public function isOnline(): bool
    {
        return $this->channel === 'online'
            || (is_null($this->channel) && !is_null($this->shipping_address));
    }

    public function channelLabel(): string
    {
        return $this->isOnline() ? 'Online' : 'In-store';
    }
}
