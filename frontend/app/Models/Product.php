<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'barcode',
        'name',
        'description',
        'note',
        'category_id',
        'brand_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function batches()
    {
        return $this->hasMany(ProductBatch::class);
    }

    public function statuses()
    {
        return $this->hasMany(ProductStatus::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function wishedBy()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function getAverageRatingAttribute(): float
    {
        return round((float) $this->reviews()->avg('rating'), 1);
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    /**
     * The batch used to represent this product's price/stock in listings:
     * cheapest active batch that is enabled for the online channel.
     */
    public function onlineBatch(?int $locationId = null)
    {
        $query = $this->batches()
            ->where('is_active', true)
            ->where('is_online', true);

        if ($locationId) {
            $query->whereHas('stocks', fn ($q) => $q->where('location_id', $locationId)->where('on_hand', '>', 0));
        }

        return $query->orderBy('sell_price')->first()
            ?? $this->batches()->where('is_active', true)->where('is_online', true)->orderBy('sell_price')->first();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOnline($query)
    {
        return $query->whereHas('batches', fn ($q) => $q->where('is_active', true)->where('is_online', true));
    }
}
