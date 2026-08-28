<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Timeline extends Model
{
    protected $fillable = [
        'timelineable_type',
        'timelineable_id',
        'event',
        'title',
        'description',
        'from_value',
        'to_value',
        'icon',
        'caused_by',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function timelineable(): MorphTo
    {
        return $this->morphTo();
    }
}
