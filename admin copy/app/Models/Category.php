<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// REMOVE: use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Category extends Model
{
    // REMOVE: use HasUuids;

    protected $fillable = [
        'uuid',
        'name',
        'barcode',
        'barcode_svg',
        'image',
        'parent_id',
        'is_active'
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }
}
