<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * This storefront reads the same `devil` database the admin app already
     * populates (products, categories, brands, stock) -- there is nothing
     * for this app to seed.
     */
    public function run(): void
    {
        //
    }
}
