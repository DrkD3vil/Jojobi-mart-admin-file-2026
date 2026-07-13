<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ExpenseCategory extends Model
{
    protected $fillable = ['name', 'slug', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function setNameAttribute($value)
    {
        $name = trim($value);
        $this->attributes['name'] = $name;
        $this->attributes['slug'] = Str::slug($name);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
