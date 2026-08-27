<?php

namespace Database\Seeders;

use App\Models\User;
use HasinHayder\Tyro\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Find or create a user (for example, the first user)
        $user = User::first(); // Fetch the first user or you can create a user

        // Fetch the 'User' role
        $userRole = Role::where('slug', 'user')->first();

        // Assign the 'User' role to this user
        if ($user && $userRole) {
            $user->roles()->attach($userRole);
        }
    }
}
