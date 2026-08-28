<?php

namespace Database\Seeders;

use App\Models\BatchStock;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Fills out the catalog with 20 realistic products spread across 20
 * categories and 20 brands (reusing the 3 categories / 2 brands that
 * already existed rather than duplicating them), each with a batch, stock
 * at the store's one location, and a generated placeholder image -- so the
 * storefront has something real to browse instead of 3 items.
 */
class DemoCatalogSeeder extends Seeder
{
    private array $palette = [
        '#B96E10', '#177264', '#7A5C3E', '#3E6B8A', '#8A4A6B',
        '#5C7A3E', '#8A6B3E', '#3E5C8A', '#8A3E4A', '#4A6B3E',
    ];

    public function run(): void
    {
        $location = Location::firstOrCreate(
            ['id' => 1],
            ['name' => 'JOJOBI MART', 'code' => 'SHOP-1', 'type' => 'store', 'is_active' => true]
        );

        $categories = [
            'Drinks' => null, // reuse existing
            'noodles' => null, // reuse existing
            'Room Spray' => null, // reuse existing
            'Snacks & Chips' => null,
            'Dairy & Eggs' => null,
            'Bakery & Breads' => null,
            'Personal Care' => null,
            'Cleaning Supplies' => null,
            'Baby Care' => null,
            'Health & Wellness' => null,
            'Rice & Grains' => null,
            'Cooking Oil & Ghee' => null,
            'Spices & Seasoning' => null,
            'Frozen Foods' => null,
            'Canned & Packaged Foods' => null,
            'Biscuits & Cookies' => null,
            'Mobile Accessories' => null,
            'Tea & Coffee' => null,
            'Chocolates & Candy' => null,
            'Water & Juice' => null,
        ];

        $categoryModels = [];
        foreach (array_keys($categories) as $name) {
            $categoryModels[$name] = Category::firstOrCreate(
                ['name' => $name],
                ['uuid' => (string) Str::uuid(), 'is_active' => true]
            );
        }

        $brands = ['Coca Cola', 'Fay', 'PepsiCo', 'Nestle', 'Aarong Dairy', 'Danish', 'Unilever', 'ACI',
            'Meril', 'Sensodyne', 'Square', 'Rupchanda', 'Radhuni', 'Kazi Farms', 'Teer', 'Olympic',
            'Marks', 'Ispahani', 'Bombay Sweets', 'Fresh'];

        $brandModels = [];
        foreach ($brands as $name) {
            $brandModels[$name] = Brand::firstOrCreate(
                ['name' => $name],
                ['is_active' => true]
            );
        }

        $products = [
            ['Coca Cola Zero Sugar 250ml', 'Drinks', 'Coca Cola', 'pcs', 60, null, 40, 'A crisp, zero-sugar take on the original Coca Cola taste.'],
            ['Lay\'s Classic Salted Chips 40g', 'Snacks & Chips', 'PepsiCo', 'pcs', 35, 30, 60, 'Thin-cut potato chips, salted and fried to a light crunch.'],
            ['Maggi 2-Minute Noodles Masala 75g', 'noodles', 'Nestle', 'pcs', 20, null, 100, 'Instant masala noodles, ready to eat in two minutes.'],
            ['Aarong Full Cream Milk 1L', 'Dairy & Eggs', 'Aarong Dairy', 'pcs', 150, null, 25, 'Pasteurised full cream milk, packed fresh daily.'],
            ['Danish Sliced White Bread 400g', 'Bakery & Breads', 'Danish', 'pcs', 60, 50, 20, 'Soft sliced white bread, baked fresh every morning.'],
            ['Sunsilk Herbal Shampoo 200ml', 'Personal Care', 'Unilever', 'pcs', 190, null, 35, 'Herbal-infused shampoo for smooth, manageable hair.'],
            ['Fay Ocean Breeze Room Spray 300ml', 'Room Spray', 'Fay', 'pcs', 240, 210, 30, 'A light, fresh scent that clears a room in seconds.'],
            ['ACI Aerosol Insect Killer 400ml', 'Cleaning Supplies', 'ACI', 'pcs', 260, null, 22, 'Fast-acting spray for mosquitoes and household insects.'],
            ['Meril Baby Diapers Size M (30pcs)', 'Baby Care', 'Meril', 'pcs', 650, 590, 15, 'Soft, absorbent diapers for all-day comfort.'],
            ['Sensodyne Sensitive Toothpaste 100g', 'Health & Wellness', 'Sensodyne', 'pcs', 220, null, 40, 'Everyday relief and protection for sensitive teeth.'],
            ['Square Miniket Rice 1kg', 'Rice & Grains', 'Square', 'kg', 140, null, 50, 'Fine-grain Miniket rice, cleaned and polished.'],
            ['Rupchanda Soybean Oil 1L', 'Cooking Oil & Ghee', 'Rupchanda', 'l', 190, null, 45, 'Refined soybean oil, light and cholesterol-free.'],
            ['Radhuni Turmeric Powder 200g', 'Spices & Seasoning', 'Radhuni', 'pcs', 85, null, 55, 'Pure ground turmeric with a rich, deep colour.'],
            ['Kazi Farms Frozen Chicken Nuggets 400g', 'Frozen Foods', 'Kazi Farms', 'pcs', 320, 290, 18, 'Breaded chicken nuggets, ready in minutes from frozen.'],
            ['Teer Canned Baked Beans 400g', 'Canned & Packaged Foods', 'Teer', 'pcs', 95, null, 28, 'Baked beans in a rich tomato sauce.'],
            ['Olympic Energy Plus Biscuits 200g', 'Biscuits & Cookies', 'Olympic', 'pcs', 40, null, 70, 'Crunchy glucose biscuits for a quick energy boost.'],
            ['Marks Wired Earphones', 'Mobile Accessories', 'Marks', 'pcs', 150, 120, 24, 'In-ear wired earphones with built-in mic.'],
            ['Ispahani Mirzapore Tea 400g', 'Tea & Coffee', 'Ispahani', 'pcs', 210, null, 33, 'A full-bodied black tea blend, brewed strong.'],
            ['Bombay Sweets Chocolate Wafer Bar', 'Chocolates & Candy', 'Bombay Sweets', 'pcs', 25, null, 90, 'Crisp wafer layers coated in milk chocolate.'],
            ['Fresh Drinking Water 1.5L', 'Water & Juice', 'Fresh', 'l', 25, null, 80, 'Purified drinking water, sealed for safety.'],
        ];

        Storage::disk('public')->makeDirectory('products');

        foreach ($products as $i => [$name, $categoryName, $brandName, $unit, $price, $discounted, $stock, $description]) {
            $barcode = 'JJB' . str_pad((string) ($i + 100), 6, '0', STR_PAD_LEFT);

            $product = Product::firstOrCreate(
                ['barcode' => $barcode],
                [
                    'name' => $name,
                    'description' => $description,
                    'category_id' => $categoryModels[$categoryName]->id,
                    'brand_id' => $brandModels[$brandName]->id,
                    'is_active' => true,
                ]
            );

            if ($product->images()->count() === 0) {
                $path = $this->makePlaceholderImage($name, $this->palette[$i % count($this->palette)]);
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => true,
                ]);
            }

            if ($product->batches()->count() === 0) {
                $batch = ProductBatch::create([
                    'product_id' => $product->id,
                    'batch_sku' => 'SKU-' . Str::upper(Str::slug($name, '')) . '-01',
                    'batch_no' => 'B' . now()->format('ym') . '-' . str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                    'unit' => $unit,
                    'buy_price' => round($price * 0.75, 2),
                    'original_sell_price' => $price,
                    'discounted_price' => $discounted,
                    'sell_price' => $discounted ?? $price,
                    'is_online' => true,
                    'is_offline' => true,
                    'is_pos' => true,
                    'is_active' => true,
                ]);

                BatchStock::create([
                    'product_batch_id' => $batch->id,
                    'location_id' => $location->id,
                    'on_hand' => $stock,
                    'reserved' => 0,
                ]);

                $batch->update(['quantity' => $stock]);
            }
        }

        $this->command?->info('Demo catalog seeded: ' . count($categoryModels) . ' categories, '
            . count($brandModels) . ' brands, ' . count($products) . ' products.');
    }

    private function makePlaceholderImage(string $name, string $bg): string
    {
        $lines = $this->wrapText($name, 16);
        $lineHeight = 52;
        $startY = 400 - (count($lines) - 1) * $lineHeight / 2;

        $text = '';
        foreach ($lines as $j => $line) {
            $y = (int) round($startY + $j * $lineHeight);
            $text .= sprintf(
                '<text x="400" y="%d" font-family="Arial, Helvetica, sans-serif" font-size="40" font-weight="600" fill="#FFFFFF" text-anchor="middle" dominant-baseline="middle">%s</text>',
                $y,
                htmlspecialchars($line, ENT_QUOTES)
            );
        }

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="800" height="800" viewBox="0 0 800 800">
  <rect width="800" height="800" fill="{$bg}"/>
  <circle cx="400" cy="400" r="260" fill="#FFFFFF" fill-opacity="0.08"/>
  {$text}
</svg>
SVG;

        $filename = 'products/' . Str::random(32) . '.svg';
        Storage::disk('public')->put($filename, $svg);

        return $filename;
    }

    private function wrapText(string $text, int $maxCharsPerLine): array
    {
        $words = explode(' ', $text);
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $candidate = trim($current . ' ' . $word);
            if (strlen($candidate) > $maxCharsPerLine && $current !== '') {
                $lines[] = $current;
                $current = $word;
            } else {
                $current = $candidate;
            }
        }
        if ($current !== '') {
            $lines[] = $current;
        }

        return array_slice($lines, 0, 3);
    }
}
