<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use HasinHayder\Tyro\Concerns\HasTyroRoles;
use HasinHayder\Tyro\Models\Privilege;
use HasinHayder\Tyro\Models\Role;
use HasinHayder\TyroLogin\Traits\HasTwoFactorAuth;



class User extends Authenticatable
{
    use HasApiTokens, HasTyroRoles, HasTwoFactorAuth;


    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function kycDetail()
    {
        return $this->hasOne(KycUser::class);
    }

    public function hasPrivilege(string $slug): bool
    {
        // Admin override (if role slug "admin" exists)
        if ($this->roles()->whereIn('slug', ['admin', 'super-admin'])->exists()) {
            return true;
        }

        // If your Tyro Role model has privileges relationship
        return $this->roles()
            ->whereHas('privileges', fn($q) => $q->where('slug', $slug))
            ->exists();
    }


    // Add method to check for admin role (assuming you're using roles)
    public function hasRole($role)
    {
        return $this->roles()->where('slug', $role)->exists();
    }


    // If you're using permissions
    public function canAssignAccessKey()
    {
        return $this->hasRole('admin');
    }

    public function privilegeAccessKeys()
    {
        return $this->hasMany(PrivilegeAccessKey::class);
    }

    public function hasPrivilegeAccessKey($accessKey)
    {
        return $this->privilegeAccessKeys()->where('access_key', $accessKey)->exists();
    }

    // public function roles()
    // {
    //     return $this->belongsToMany(
    //         Role::class,
    //         'user_roles',
    //         'user_id',
    //         'role_id'
    //     );
    // }

    // Relationship with privileges
    // public function privileges()
    // {
    //     return $this->belongsToMany(
    //         Privilege::class,
    //         'privilege_role',
    //         'role_id',
    //         'privilege_id'
    //     );
    // }

    // Helper methods for managing roles and privileges
    // public function assignRole($role)
    // {
    //     return $this->roles()->attach($role);
    // }

    // public function assignPrivilege($privilege)
    // {
    //     return $this->privileges()->attach($privilege);
    // }
}
