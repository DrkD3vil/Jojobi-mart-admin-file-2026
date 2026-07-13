<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Location extends Model
{
    protected $fillable = [
        'name',
        'code',
        'type',
        'is_active',
        'address',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
