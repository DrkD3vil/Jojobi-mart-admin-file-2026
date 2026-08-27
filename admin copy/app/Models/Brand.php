<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];


    // Automatically generate slug when setting the name
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = trim($value);
        $this->attributes['slug'] = Str::slug(trim($value));
    }


    // Relationship with products
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
