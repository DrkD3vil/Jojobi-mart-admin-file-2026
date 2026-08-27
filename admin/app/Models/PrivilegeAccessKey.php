<?php

namespace App\Models;

use App\Models\Concerns\HasTimeline;
use HasinHayder\Tyro\Models\Privilege;
use HasinHayder\Tyro\Models\Role;
use Illuminate\Database\Eloquent\Model;

class PrivilegeAccessKey extends Model
{
    use HasTimeline;

    protected $fillable = ['privilege_id', 'user_id', 'role_id', 'access_key', 'is_auto'];

    protected $casts = ['is_auto' => 'boolean'];

    // Define the relationship to Privilege
    public function privilege()
    {
        return $this->belongsTo(Privilege::class);
    }

    // Define the relationship to Role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    // Define the relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);  // Added the relationship to the User model
    }


}
