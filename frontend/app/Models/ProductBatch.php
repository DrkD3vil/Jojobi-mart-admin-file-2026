<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductBatch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'batch_sku',
        'batch_no',
        'unit',
        'buy_price',
        'original_sell_price',
        'discounted_price',
        'discount_percentage',
        'sell_price',
        'whole_sell_price',
        'whole_sell_min_qty',
        'whole_sell_max_qty',
        'customer_whole_price',
        'customer_whole_min_qty',
        'customer_whole_max_qty',
        'manufacture_date',
        'expiry_date',
        'is_online',
        'is_offline',
        'is_pos',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'buy_price' => 'decimal:4',
        'original_sell_price' => 'decimal:4',
        'discounted_price' => 'decimal:4',
        'discount_percentage' => 'decimal:2',
        'sell_price' => 'decimal:4',
        'whole_sell_price' => 'decimal:4',
        'whole_sell_min_qty' => 'decimal:4',
        'whole_sell_max_qty' => 'decimal:4',
        'customer_whole_price' => 'decimal:4',
        'customer_whole_min_qty' => 'decimal:4',
        'customer_whole_max_qty' => 'decimal:4',
        'manufacture_date' => 'date',
        'expiry_date' => 'date',
        'is_online' => 'boolean',
        'is_offline' => 'boolean',
        'is_pos' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stocks()
    {
        return $this->hasMany(BatchStock::class, 'product_batch_id');
    }

    public function stockAtLocation(int $locationId)
    {
        return $this->stocks()->where('location_id', $locationId)->first();
    }

    public function onHandAt(int $locationId): float
    {
        return (float) ($this->stockAtLocation($locationId)->on_hand ?? 0);
    }

    public function availableAt(int $locationId): float
    {
        $s = $this->stockAtLocation($locationId);
        if (!$s) {
            return 0.0;
        }

        return (float) $s->on_hand - (float) $s->reserved;
    }

    /**
     * Final unit price for a given quantity, honouring wholesale / signed-in
     * customer tiers exactly as the POS does, so prices always match.
     */
    public function calculatePrice(float $qty, bool $isCustomer = false): float
    {
        if ($isCustomer && $this->customer_whole_price !== null) {
            if (($this->customer_whole_min_qty === null || $qty >= $this->customer_whole_min_qty) &&
                ($this->customer_whole_max_qty === null || $qty <= $this->customer_whole_max_qty)
            ) {
                return (float) $this->customer_whole_price;
            }
        }

        if ($this->whole_sell_price !== null) {
            if (($this->whole_sell_min_qty === null || $qty >= $this->whole_sell_min_qty) &&
                ($this->whole_sell_max_qty === null || $qty <= $this->whole_sell_max_qty)
            ) {
                return (float) $this->whole_sell_price;
            }
        }

        if ($this->discounted_price !== null) {
            return (float) $this->discounted_price;
        }

        return (float) $this->original_sell_price;
    }

    public function displayPrice(): float
    {
        return $this->calculatePrice(1, false);
    }

    public function isOnSale(): bool
    {
        return $this->discounted_price !== null && (float) $this->discounted_price < (float) $this->original_sell_price;
    }
}
