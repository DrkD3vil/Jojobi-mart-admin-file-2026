<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Events\ProductStatusUpdated;

class ProductStatus extends Model
{
    protected $fillable = [
        'uuid',
        'product_id',
        'name',
        'slug',
        'badge_text',
        'badge_color',
        'description',
        'is_active',
    ];

    protected static function booted()
    {
        static::creating(function ($status) {
            $status->uuid = Str::uuid();

            // Only generate slug if not set
            if (empty($status->slug)) {
                $status->slug = $status->generateUniqueSlug($status->name, $status->product_id);
            }
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Unique slug per product or global for template
    public function generateUniqueSlug($name, $productId = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        if ($productId) {
            // Unique per product
            while (ProductStatus::where('product_id', $productId)->where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }
        } else {
            // Unique globally for templates
            while (ProductStatus::whereNull('product_id')->where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }
        }

        return $slug;
    }



}
