<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);



        // Create 20 categories
        for ($i = 1; $i <= 20; $i++) {
            Category::create([
                'uuid' => Str::uuid(),
                'name' => 'Category ' . $i,
                'barcode' => 'CAT' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'barcode_svg' => null, // You can generate SVG if needed
                'image' => null,
                'parent_id' => null, // Add some parent_id if you want nested categories
                'is_active' => true,
            ]);
        }
    }
}
