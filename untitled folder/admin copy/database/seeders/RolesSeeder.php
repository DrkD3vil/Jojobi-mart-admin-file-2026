<?php

namespace Database\Seeders;

use HasinHayder\Tyro\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Add default roles
        Role::create(['name' => 'Admin', 'slug' => 'admin']);
        Role::create(['name' => 'User', 'slug' => 'user']);
        Role::create(['name' => 'Super Admin', 'slug' => 'super-admin']);
        Role::create(['name' => 'Editor', 'slug' => 'editor']);
        Role::create(['name' => 'Tester', 'slug' => 'tester']);



    }
}
