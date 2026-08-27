<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class KycUser extends Model
{
    protected $table = 'KYC_user';
    protected $fillable = [
        'user_id',
        'phone',
        'gender',
        'date_of_birth',
        'city',
        'address_1',
        'address_2',
        'profile_image',
        'metadata'
    ];
    protected $casts = ['metadata' => 'array', 'date_of_birth' => 'date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
