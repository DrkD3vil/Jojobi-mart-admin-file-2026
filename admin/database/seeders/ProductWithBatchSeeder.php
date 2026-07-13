<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductBatch;

class ProductWithBatchSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // Clear old demo data (optional)
            ProductBatch::truncate();
            Product::truncate();

            // -------------------------
            // PRODUCTS
            // -------------------------
            $products = collect([
                [
                    'name' => 'Rice Premium',
                    'barcode' => 'RICE-001',
                ],
                [
                    'name' => 'Sugar White',
                    'barcode' => 'SUGAR-001',
                ],
                [
                    'name' => 'Milk Powder',
                    'barcode' => 'MILK-001',
                ],
                [
                    'name' => 'Cooking Oil',
                    'barcode' => 'OIL-001',
                ],
                [
                    'name' => 'Tea Packet',
                    'barcode' => 'TEA-001',
                ],
            ])->map(function ($p) {
                return Product::create([
                    'name' => $p['name'],
                    'barcode' => $p['barcode'],
                    'is_active' => true,
                ]);
            });

            // Helper for SKU
            $skuCounter = 1;
            $sku = fn () => 'PB-' . str_pad($skuCounter++, 4, '0', STR_PAD_LEFT);

            // -------------------------
            // BATCHES
            // -------------------------

            // 1️⃣ Rice – Buy 2 Get 1 Sugar (FREE OFFER)
            ProductBatch::create([
                'product_id' => $products[0]->id,
                'batch_sku' => $sku(),
                'batch_no' => 'RICE-B1',
                'quantity' => 100,
                'unit' => 'kg',
                'buy_price' => 45,
                'original_sell_price' => 55,
                'sell_price' => 55,
                'is_active' => true,

                'is_free_offer_active' => true,
                'free_product_id' => $products[1]->id, // Sugar
                'free_buy_qty' => 2,
                'free_qty' => 1,
            ]);

            // 2️⃣ Rice – Normal batch (no offer)
            ProductBatch::create([
                'product_id' => $products[0]->id,
                'batch_sku' => $sku(),
                'batch_no' => 'RICE-B2',
                'quantity' => 80,
                'unit' => 'kg',
                'buy_price' => 44,
                'original_sell_price' => 54,
                'sell_price' => 54,
                'is_active' => true,
            ]);

            // 3️⃣ Sugar – Buy 3 Get 1 Tea
            ProductBatch::create([
                'product_id' => $products[1]->id,
                'batch_sku' => $sku(),
                'batch_no' => 'SUGAR-B1',
                'quantity' => 120,
                'unit' => 'kg',
                'buy_price' => 60,
                'original_sell_price' => 72,
                'sell_price' => 72,
                'is_active' => true,

                'is_free_offer_active' => true,
                'free_product_id' => $products[4]->id, // Tea
                'free_buy_qty' => 3,
                'free_qty' => 1,
            ]);

            // 4️⃣ Milk – No offer
            ProductBatch::create([
                'product_id' => $products[2]->id,
                'batch_sku' => $sku(),
                'batch_no' => 'MILK-B1',
                'quantity' => 60,
                'unit' => 'pcs',
                'buy_price' => 380,
                'original_sell_price' => 450,
                'sell_price' => 450,
                'is_active' => true,
            ]);

            // 5️⃣ Oil – Buy 1 Get 1 Free (Same Product allowed if you want)
            ProductBatch::create([
                'product_id' => $products[3]->id,
                'batch_sku' => $sku(),
                'batch_no' => 'OIL-B1',
                'quantity' => 50,
                'unit' => 'ltr',
                'buy_price' => 700,
                'original_sell_price' => 850,
                'sell_price' => 850,
                'is_active' => true,

                'is_free_offer_active' => true,
                'free_product_id' => $products[3]->id, // Same product
                'free_buy_qty' => 1,
                'free_qty' => 1,
            ]);

            // 6️⃣ Tea – Normal
            ProductBatch::create([
                'product_id' => $products[4]->id,
                'batch_sku' => $sku(),
                'batch_no' => 'TEA-B1',
                'quantity' => 90,
                'unit' => 'pcs',
                'buy_price' => 95,
                'original_sell_price' => 120,
                'sell_price' => 120,
                'is_active' => true,
            ]);
        });
    }
}
