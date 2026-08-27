<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    //

    protected $fillable = [
        'cart_id',
        'product_id',
        'product_batch_id',
        'product_image_id',
        'price_type',
        'unit_price',
        'quantity',
        'unit',
   'qty_in_batch_unit',
        'discount_amount',
        'discount_percent',
        'discount_label',
        'total_price',
        'is_gift',
        'gift_source',
        'gift_source_id',
        'parent_cart_item_id',



    ];


    protected $casts = [
        'is_gift' => 'boolean',
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'total_price' => 'decimal:4',
        'qty_in_batch_unit' => 'decimal:4',
    ];


public function batch()
{
    return $this->belongsTo(ProductBatch::class, 'product_batch_id')->withTrashed();
}


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function image()
    {
        return $this->belongsTo(ProductImage::class, 'product_image_id');
    }
    public function parent()
    {
        return $this->belongsTo(CartItem::class, 'parent_cart_item_id');
    }

    public function gifts()
    {
        return $this->hasMany(CartItem::class, 'parent_cart_item_id');
    }
}
