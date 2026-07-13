<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
      use SoftDeletes;
    //
    protected $fillable = [
        'uuid',
        'barcode',
        'name',
        'description',
        'note',
        'category_id',
        'brand_id',
        'is_active',
        'deleted_by',
        'deleted_reason',
    ];
    /**
     * Get the category that the product belongs to.
     */

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    /**
     * Get the brand that the product belongs to.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the images for the product.
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function batches()
    {
        // Assuming your batch model is named 'ProductBatch'
        // and it has a 'product_id' foreign key.
        return $this->hasMany(ProductBatch::class);
    }

    /**
     * Get the total stock quantity across all active batches.
     */
    // public function totalStockQuantity()
    // {
    //     // Assuming 'quantity' is the stock column in the ProductBatch model
    //     return $this->batches()->sum('quantity');
    // }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    // ✅ When product soft-deletes => soft-delete images too
    protected static function booted()
    {
        static::deleting(function (Product $product) {
            if (! $product->isForceDeleting()) {
                $product->images()->delete(); // soft delete images
            }
        });

        static::restoring(function (Product $product) {
            $product->images()->withTrashed()->restore();
        });
    }


    public function statuses()
    {
        return $this->hasMany(ProductStatus::class);
    }

    public function totalStockQuantity(): float
    {
        $batchIds = $this->batches()->pluck('id');

        return (float) \App\Models\BatchStock::whereIn('product_batch_id', $batchIds)
            ->sum('on_hand');
    }






    public function totalAvailableQuantity(): float
{
    $batchIds = $this->batches()->pluck('id'); // Get all batch IDs related to this product

    $stocks = \App\Models\BatchStock::whereIn('product_batch_id', $batchIds)
        ->get(['on_hand', 'reserved']); // Get on_hand and reserved quantities

    return (float) $stocks->sum(fn($s) => (float)$s->on_hand - (float)$s->reserved);
}




    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
     public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
