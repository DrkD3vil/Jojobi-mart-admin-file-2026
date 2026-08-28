<?php
// app/Models/Order.php

namespace App\Models;

use App\Models\Concerns\HasTimeline;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, SoftDeletes, HasTimeline;

    protected $fillable = [
        'order_no',
        'session_id',
        'customer_id',
        'location_id',

        // Sales channel
        'channel',
        'shipping_name',
        'shipping_phone',
        'shipping_address',
        'shipping_note',

        // Split order fields
        'parent_order_id',
        'split_reason',
        'split_status',
        'is_split_child',
        'split_sequence',
        'split_by',
        'split_at',
        'original_order_id',
        'split_notes',

        // Amount fields
        'subtotal',
        'discount_total',
        'payable_total',
        'rewards_points_used',
        'rewards_amount_used',
        'paid_total',
        'due_total',
        'change_total',

        // Payment
        'payment_status',
        'payment_note',

        // Order status
        'status',

        // Fulfillment
        'packaged_at',
        'packaged_by',

        // Trash management
        'deleted_by',
        'delete_reason',
        'restored_at',
        'deleted_at',
    ];

    protected $casts = [
        // Money
        'subtotal'              => 'decimal:2',
        'discount_total'        => 'decimal:2',
        'payable_total'         => 'decimal:2',
        'rewards_points_used'   => 'decimal:2',
        'rewards_amount_used'   => 'decimal:2',
        'paid_total'            => 'decimal:2',
        'due_total'             => 'decimal:2',
        'change_total'          => 'decimal:2',

        // Boolean
        'is_split_child' => 'boolean',

        // Dates
        'split_at'      => 'datetime',
        'restored_at'   => 'datetime',
        'deleted_at'    => 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'packaged_at'   => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

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

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function packagedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'packaged_by');
    }

    public function parentOrder()
    {
        return $this->belongsTo(Order::class, 'parent_order_id');
    }

    public function childOrders()
    {
        return $this->hasMany(Order::class, 'parent_order_id');
    }

    public function originalOrder()
    {
        return $this->belongsTo(Order::class, 'original_order_id');
    }

    public function splitBy()
    {
        return $this->belongsTo(User::class, 'split_by');
    }

    public function orderSplitsAsParent()
    {
        return $this->hasMany(OrderSplit::class, 'parent_order_id');
    }

    public function orderSplitsAsChild()
    {
        return $this->hasMany(OrderSplit::class, 'child_order_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getDeletedByNameAttribute()
    {
        return $this->deletedBy ? $this->deletedBy->name : 'System';
    }

    public function getOrderTypeLabelAttribute()
    {
        if ($this->isSplitChild()) {
            return 'Sub-Order';
        }
        if ($this->isSplitParent()) {
            return 'Parent Order';
        }
        return 'Main Order';
    }

    public function getOrderTypeBadgeAttribute()
    {
        if ($this->isSplitChild()) {
            return '<span class="badge badge-warning">Sub-Order</span>';
        }
        if ($this->isSplitParent()) {
            return '<span class="badge badge-info">Parent Order</span>';
        }
        return '<span class="badge badge-secondary">Main Order</span>';
    }

    public function getIsSubOrderAttribute()
    {
        return $this->isSplitChild();
    }

    public function getIsParentOrderAttribute()
    {
        return $this->isSplitParent();
    }

    /*
    |--------------------------------------------------------------------------
    | Restore / Delete Helpers
    |--------------------------------------------------------------------------
    */

    public function restoreOrder()
    {
        $this->restore();
        $this->update([
            'restored_at' => now(),
            'deleted_by' => null,
            'delete_reason' => null,
        ]);
    }

    public function forceDeleteOrder()
    {
        $this->forceDelete();
    }

    public function isTrashed()
    {
        return $this->trashed();
    }

    public function isPackaged(): bool
    {
        return !is_null($this->packaged_at);
    }

    /*
    |--------------------------------------------------------------------------
    | Split Order Helpers
    |--------------------------------------------------------------------------
    */

    public function getSplitChildren()
    {
        return $this->childOrders()
            ->where('split_status', 'split_child')
            ->orderBy('split_sequence')
            ->get();
    }

    public function getOriginalOrder()
    {
        return $this->originalOrder ?: $this;
    }

    public function isSplit()
    {
        return $this->split_status === 'split_parent' || $this->is_split_child;
    }

    public function isSplitParent()
    {
        // Trust the flag but also require at least one child to actually
        // exist -- if every child was force-deleted the flag can go stale.
        return $this->split_status === 'split_parent' && $this->childOrders()->exists();
    }

    public function isSplitChild()
    {
        // Require the parent_order_id to actually be set (not just the two
        // status flags) so a "View Parent Order" link is never built from a
        // null/dangling reference.
        return $this->is_split_child
            && $this->split_status === 'split_child'
            && $this->parent_order_id !== null;
    }

    public function isOriginal()
    {
        return $this->split_status === 'original' || is_null($this->split_status);
    }

    public function hasChildren()
    {
        return $this->childOrders()->where('split_status', 'split_child')->count() > 0;
    }

    public function getChildren()
    {
        return $this->childOrders()
            ->where('split_status', 'split_child')
            ->orderBy('split_sequence')
            ->get();
    }

    public function getAvailableForSplit()
    {
        return $this->items()
            ->where('quantity', '>', 0)
            ->where(function($q) {
                $q->whereNull('returned_qty')
                  ->orWhereRaw('returned_qty < quantity');
            })
            ->get();
    }

    public function getSplitTotal(): float
    {
        return (float) OrderSplit::where('original_order_id', $this->original_order_id ?? $this->id)
            ->sum('split_amount');
    }

    public function getRemainingForSplit(): float
    {
        return max(0, $this->payable_total - $this->getSplitTotal());
    }

    public function shouldCalculateProfit(): bool
    {
        return !$this->isSplitChild();
    }

    public function getRootOrder()
    {
        if ($this->original_order_id) {
            return Order::find($this->original_order_id);
        }
        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeOriginalOrders($query)
    {
        return $query->where(function($q) {
            $q->where('split_status', 'original')
              ->orWhereNull('split_status');
        });
    }

    public function scopeSplitParents($query)
    {
        return $query->where('split_status', 'split_parent');
    }

    public function scopeSplitChildren($query)
    {
        return $query->where('is_split_child', true);
    }

    public function scopeAvailableForSplit($query)
    {
        return $query
            ->whereIn('status', ['pending', 'processing', 'paid', 'partial'])
            ->where(function($q) {
                $q->where('split_status', 'original')
                  ->orWhereNull('split_status');
            });
    }

    public function scopeMainOrders($query)
    {
        return $query->where(function($q) {
            $q->where('split_status', 'original')
              ->orWhereNull('split_status');
        })->where('is_split_child', false);
    }

    public function scopeSubOrders($query)
    {
        return $query->where('is_split_child', true);
    }

    public function scopeOnlineChannel($query)
    {
        return $query->where('channel', 'online');
    }

    /**
     * Online orders that have been marked processing but not yet packaged --
     * the staff fulfillment queue (see EcommerceOrderController::queue()).
     */
    public function scopeAwaitingPackaging($query)
    {
        return $query
            ->where('channel', 'online')
            ->where('status', 'processing')
            ->whereNull('packaged_at');
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    public function canSplit(): bool
    {
        return $this->isOriginal()
            && in_array($this->status, ['pending', 'processing', 'paid', 'partial'])
            && $this->items()->count() > 0
            && !$this->is_split_child;
    }

    public function canMergeChild($childOrder): bool
    {
        return $this->isSplitParent()
            && $childOrder->isSplitChild()
            && $childOrder->parent_order_id == $this->id
            && in_array($childOrder->status, ['pending', 'processing', 'completed'])
            && !in_array($this->status, ['completed', 'cancelled']);
    }

    /*
    |--------------------------------------------------------------------------
    | Generate Order Number
    |--------------------------------------------------------------------------
    */

    public function generateOrderNo(): string
    {
        $prefix = $this->isSplitChild() ? 'SUB' : 'ORD';
        return $prefix . '-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6));
    }

    /*
    |--------------------------------------------------------------------------
    | Profit Calculation
    |--------------------------------------------------------------------------
    */

    public function calculateProfit(): array
    {
        if ($this->isSplitChild()) {
            return [
                'profit' => 0,
                'cost' => 0,
                'revenue' => $this->payable_total,
                'message' => 'Sub-orders do not calculate profit separately'
            ];
        }

        $totalCost = 0;
        $totalRevenue = $this->payable_total;

        foreach ($this->items as $item) {
            $costPerUnit = $item->batch->buy_price ?? 0;
            $totalCost += $costPerUnit * $item->quantity;
        }

        $profit = $totalRevenue - $totalCost;

        return [
            'profit' => $profit,
            'cost' => $totalCost,
            'revenue' => $totalRevenue,
        ];
    }
}
