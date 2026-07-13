<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductBatch extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'product_id',
        'batch_sku',
        'batch_no',
        // 'quantity',
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
        'free_product_id',
        'free_buy_qty',
        'free_qty',
        'is_free_offer_active',



    ];

    protected $casts = [
        // 'quantity' => 'decimal:4',
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

    /**
     * Relationship: ProductBatch belongs to a Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // public function images()
    // {
    //     return $this->hasMany(ProductImage::class);
    // }

    /**
     * Calculate the final price for a given quantity and customer type
     */
    public function calculatePrice(float $qty, bool $isCustomer = false): float
    {
        // Customer-specific wholesale price
        if ($isCustomer && $this->customer_whole_price !== null) {
            if (($this->customer_whole_min_qty === null || $qty >= $this->customer_whole_min_qty) &&
                ($this->customer_whole_max_qty === null || $qty <= $this->customer_whole_max_qty)
            ) {
                return (float) $this->customer_whole_price;
            }
        }

        // Wholesale price
        if ($this->whole_sell_price !== null) {
            if (($this->whole_sell_min_qty === null || $qty >= $this->whole_sell_min_qty) &&
                ($this->whole_sell_max_qty === null || $qty <= $this->whole_sell_max_qty)
            ) {
                return (float) $this->whole_sell_price;
            }
        }

        // Discounted price
        if ($this->discounted_price !== null) {
            return (float) $this->discounted_price;
        }

        // Default sell price
        return (float) $this->original_sell_price;
    }

    /**
     * Check if the batch is available for the given channel
     */
    public function isAvailable(string $channel = 'online'): bool
    {
        return match ($channel) {
            'online' => $this->is_online,
            'offline' => $this->is_offline,
            'pos' => $this->is_pos,
            default => $this->is_active,
        };
    }

    /**
     * Check if sufficient stock is available
     */
    // public function hasStock(float $qty): bool
    // {
    //     return $this->quantity >= $qty;
    // }

    public function images()
    {
        return $this->hasManyThrough(
            ProductImage::class,
            Product::class,
            'id',          // Product id in Product table
            'product_id',  // product_id in ProductImage
            'product_id',  // local product_id in ProductBatch
            'id'           // local id in Product table
        );
    }

    public function freeProduct()
    {
        return $this->belongsTo(Product::class, 'free_product_id');
    }

    public function stocks()
    {
        return $this->hasMany(\App\Models\BatchStock::class, 'product_batch_id');
    }

    public function batchStocks()
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
        if (!$s) return 0.0;
        return (float)$s->on_hand - (float)$s->reserved;
    }

    /**
     * OLD: checked $this->quantity (not valid for multi-location)
     * NEW: check available stock at a specific location
     */
    public function hasStock(float $qty, ?int $locationId = null): bool
    {
        // If location is not provided, fallback to total available (all locations)
        if ($locationId === null) {
            return $this->totalAvailable() >= $qty;
        }

        return $this->availableAt($locationId) >= $qty;
    }

    /**
     * Total on_hand across all locations
     */
    public function totalOnHand(): float
    {
        return (float) $this->stocks()->sum('on_hand');
    }

    /**
     * Total available across all locations (on_hand - reserved)
     */
    public function totalAvailable(): float
    {
        $stocks = $this->stocks()->get(['on_hand', 'reserved']);
        return (float) $stocks->sum(fn($s) => (float)$s->on_hand - (float)$s->reserved);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(\App\Models\OrderItem::class, 'product_batch_id');
    }

    public function returnItems()
    {
        return $this->hasMany(ReturnItem::class, 'product_batch_id');
    }
}
