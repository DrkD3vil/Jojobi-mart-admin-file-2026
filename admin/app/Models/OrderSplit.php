<?php
// app/Models/OrderSplit.php

namespace App\Models;

use App\Models\Concerns\HasTimeline;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderSplit extends Model
{
    use HasFactory, HasTimeline;

    protected $fillable = [
        'original_order_id',
        'parent_order_id',
        'child_order_id',
        'split_items',
        'split_amount',
        'split_type',
        'split_reason',
        'split_notes',
        'created_by',
        'split_at',
    ];

    protected $casts = [
        'split_items' => 'array',
        'split_amount' => 'decimal:2',
        'split_at' => 'datetime',
    ];

    // withTrashed(): orders can be soft-deleted (trashed) independently of
    // their split history. Without this, a trashed parent/child silently
    // resolves to null here (Order's SoftDeletes global scope excludes it),
    // and route('orders.show', null) then throws building the link -- crashing
    // the split-history page for any split touching a trashed order.
    public function originalOrder()
    {
        return $this->belongsTo(Order::class, 'original_order_id')->withTrashed();
    }

    public function parentOrder()
    {
        return $this->belongsTo(Order::class, 'parent_order_id')->withTrashed();
    }

    public function childOrder()
    {
        return $this->belongsTo(Order::class, 'child_order_id')->withTrashed();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getSplitItemsAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function getFormattedAmountAttribute()
    {
        return '৳ ' . number_format($this->split_amount, 2);
    }

    public function getTypeLabelAttribute()
    {
        return str_replace('_', ' ', ucfirst($this->split_type));
    }
}
