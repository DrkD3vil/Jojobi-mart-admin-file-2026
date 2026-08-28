<?php

namespace App\Models;

use App\Models\Concerns\HasTimeline;
use Illuminate\Database\Eloquent\Model;

class OrderSplit extends Model
{
    use HasTimeline;

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
}
