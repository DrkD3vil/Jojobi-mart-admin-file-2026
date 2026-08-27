<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiAccessRequest extends Model
{
    protected $fillable = ['user_id', 'status', 'note', 'resolved_by', 'resolved_at'];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
