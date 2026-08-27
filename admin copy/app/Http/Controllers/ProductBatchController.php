<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Location;
use App\Models\BatchStock;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class ProductBatchController extends Controller
{
    public function all()
    {
        $baseQuery = ProductBatch::query()
            ->with([
                'product.images',
                'product.category',
                'product.brand'
            ]);

        // $batches = (clone $baseQuery)->latest()->paginate(20);



        $batches = ProductBatch::query()
            ->with([
                'product.images',
                'product.category',
                'product.brand',
            ])
            ->withSum('stocks as stock_qty', 'on_hand') // ✅ per-batch quantity
            ->latest()
            ->paginate(20);



        /**
         * ✅ Metrics (location-based)
         * We calculate stock from batch_stocks (sum on_hand), not product_batches.quantity
         */
        $batchIds = (clone $baseQuery)->pluck('id');

        $totalOnHand = (float) BatchStock::whereIn('product_batch_id', $batchIds)->sum('on_hand');

        $totals = (clone $baseQuery)->selectRaw("
            COALESCE(SUM(buy_price), 0) as _dummy_buy,
            COALESCE(SUM(original_sell_price), 0) as _dummy_sell,
            COALESCE(SUM(CASE WHEN is_online = 1 THEN 1 ELSE 0 END), 0) as online_count,
            COALESCE(SUM(CASE WHEN is_offline = 1 THEN 1 ELSE 0 END), 0) as offline_count,
            COALESCE(SUM(CASE WHEN is_pos = 1 THEN 1 ELSE 0 END), 0) as pos_count,
            COALESCE(SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END), 0) as active_count,
            COALESCE(SUM(CASE WHEN is_free_offer_active = 1 THEN 1 ELSE 0 END), 0) as gift_count
        ")->first();

        $expiringSoon = (clone $baseQuery)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>', now())
            ->where('expiry_date', '<=', now()->addDays(30))
            ->count();

        $expiredCount = (clone $baseQuery)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now())
            ->count();

        /**
         * Low/out of stock (location-based):
         * low stock if total on_hand across all locations <= 10 and >0
         */
        $stockAgg = BatchStock::query()
            ->selectRaw('product_batch_id, COALESCE(SUM(on_hand),0) as qty')
            ->whereIn('product_batch_id', $batchIds)
            ->groupBy('product_batch_id')
            ->get()
            ->keyBy('product_batch_id');

        $lowStock = 0;
        $outOfStock = 0;

        foreach ($batchIds as $bid) {
            $q = (float)($stockAgg[$bid]->qty ?? 0);
            if ($q <= 0) $outOfStock++;
            else if ($q <= 10) $lowStock++;
        }

        // Optional: stock value/revenue now needs qty * price
        $totalStockValue = 0.0;
        $potentialRevenue = 0.0;

        foreach ($batches as $b) {
            $qty = (float)($stockAgg[$b->id]->qty ?? 0);
            $totalStockValue += $qty * (float)$b->buy_price;
            $potentialRevenue += $qty * (float)$b->original_sell_price;
        }

        $metrics = [
            'total_stock_value' => (float)$totalStockValue,
            'potential_revenue' => (float)$potentialRevenue,
            'total_on_hand' => $totalOnHand,
            'expiring_soon' => $expiringSoon,
            'expired' => $expiredCount,
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock,
            'channel_distribution' => [
                'online' => (int) $totals->online_count,
                'offline' => (int) $totals->offline_count,
                'pos' => (int) $totals->pos_count,
            ],
            'active_count' => (int) $totals->active_count,
            'gift_count' => (int) $totals->gift_count,
        ];
        $trashedBatchCount = \App\Models\ProductBatch::onlyTrashed()->count();

        return view('products.product-batches.all', compact('batches', 'metrics', 'trashedBatchCount'));
    }

    /**
     * Show form to create a new batch
     */
    public function create(Product $product = null)
    {
        $products = Product::query()
            ->select(['id', 'name', 'barcode'])
            ->orderBy('name')
            ->get();

        $giftProducts = Product::query()
            ->select(['id', 'name', 'barcode'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // ✅ Locations (required for batch stock)
        $locations = Location::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $defaultLocationId = Location::where('type', 'warehouse')->value('id') ?? $locations->first()?->id;

        $selectedProduct = $product;

        return view('products.product-batches.create', compact(
            'products',
            'giftProducts',
            'selectedProduct',
            'locations',
            'defaultLocationId'
        ));
    }

    /**
     * Store a new product batch + create batch stock rows
     */
    public function store(Request $request)
    {
        Log::info('ProductBatchController@store - Start', ['request' => $request->except('_token')]);

        // ✅ Resolve product_id from manual select if needed
        $pid = $request->input('product_id') ?: $request->input('product_id_manual');
        $request->merge(['product_id' => $pid]);

        $rules = [
            'product_id' => 'required|exists:products,id',
            'location_id' => 'required|exists:locations,id', // ✅ NEW

            'batch_no' => 'nullable|string|max:50',
            'quantity' => 'required|numeric|min:0.001',
            'unit' => 'required|in:pcs,dozen,box,kg,g,l,ml',
            'buy_price' => 'required|numeric|min:0',
            'original_sell_price' => 'required|numeric|min:0',
            'discounted_price' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',

            'whole_sell_price' => 'nullable|numeric|min:0',
            'whole_sell_min_qty' => 'nullable|numeric|min:0',
            'whole_sell_max_qty' => 'nullable|numeric|min:0',
            'customer_whole_price' => 'nullable|numeric|min:0',
            'customer_whole_min_qty' => 'nullable|numeric|min:0',
            'customer_whole_max_qty' => 'nullable|numeric|min:0',

            'manufacture_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:manufacture_date',

            'is_online' => 'nullable|boolean',
            'is_offline' => 'nullable|boolean',
            'is_pos' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string',

            // ✅ FREE OFFER
            'is_free_offer_active' => 'nullable|boolean',
            'free_product_id' => 'nullable|exists:products,id',
            'free_buy_qty' => 'nullable|numeric|min:0.0001',
            'free_qty' => 'nullable|numeric|min:0.0001',
        ];

        $validated = $request->validate($rules);
        $isOffer = (bool) ($validated['is_free_offer_active'] ?? false);

        // ✅ Gift rules only when active
        if ($isOffer) {
            if (empty($validated['free_product_id'])) {
                return back()->withInput()->withErrors(['free_product_id' => 'Gift product is required when Free Offer is enabled.']);
            }
            if (empty($validated['free_buy_qty']) || (float)$validated['free_buy_qty'] <= 0) {
                return back()->withInput()->withErrors(['free_buy_qty' => 'Buy quantity must be greater than 0.']);
            }
            if (empty($validated['free_qty']) || (float)$validated['free_qty'] <= 0) {
                return back()->withInput()->withErrors(['free_qty' => 'Free quantity must be greater than 0.']);
            }

            if ((int)$validated['free_product_id'] === (int)$validated['product_id']) {
                return back()->withInput()->withErrors(['free_product_id' => 'Gift product cannot be the same as the batch product.']);
            }

            // ✅ Ensure gift product has stock (location-based)
            $hasGiftStock = BatchStock::query()
                ->where('on_hand', '>', 0)
                ->whereHas('batch', function ($q) use ($validated) {
                    $q->where('product_id', (int)$validated['free_product_id'])
                        ->where('is_active', true);
                })
                ->exists();

            if (!$hasGiftStock) {
                return back()->withInput()->withErrors([
                    'free_product_id' => 'Gift product has no stock in active batches (location based). Please add stock first.'
                ]);
            }
        } else {
            $validated['free_product_id'] = null;
            $validated['free_buy_qty'] = null;
            $validated['free_qty'] = null;
        }

        try {
            DB::beginTransaction();

            // ✅ Batch SKU generator
            $lastBatch = ProductBatch::lockForUpdate()->orderByDesc('id')->first();
            $nextNumber = $lastBatch && $lastBatch->batch_sku
                ? (int) preg_replace('/\D+/', '', $lastBatch->batch_sku) + 1
                : 1;

            $batchSku = 'PB-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // ✅ Sell price calc
            $originalPrice   = (float) $validated['original_sell_price'];
            $discountedPrice = (float) ($validated['discounted_price'] ?? 0);
            $discountPercent = (float) ($validated['discount_percentage'] ?? 0);

            if ($discountedPrice > 0) {
                $sellPrice = $originalPrice - $discountedPrice;
                $validated['discount_percentage'] = null;
            } elseif ($discountPercent > 0) {
                $sellPrice = $originalPrice - ($originalPrice * ($discountPercent / 100));
                $validated['discounted_price'] = null;
            } else {
                $sellPrice = $originalPrice;
                $validated['discounted_price'] = null;
                $validated['discount_percentage'] = null;
            }

            $sellPrice = round($sellPrice, 4);

            // ✅ Create batch
            $batch = ProductBatch::create([
                'product_id' => $validated['product_id'],
                'batch_sku' => $batchSku,
                'batch_no' => $validated['batch_no'] ?? null,

                // keep as legacy
                'quantity' => $validated['quantity'],
                'unit' => $validated['unit'],

                'buy_price' => $validated['buy_price'],
                'original_sell_price' => $validated['original_sell_price'],
                'discounted_price' => $validated['discounted_price'] ?? null,
                'discount_percentage' => $validated['discount_percentage'] ?? null,
                'sell_price' => $sellPrice,

                'whole_sell_price' => $validated['whole_sell_price'] ?? null,
                'whole_sell_min_qty' => $validated['whole_sell_min_qty'] ?? null,
                'whole_sell_max_qty' => $validated['whole_sell_max_qty'] ?? null,

                'customer_whole_price' => $validated['customer_whole_price'] ?? null,
                'customer_whole_min_qty' => $validated['customer_whole_min_qty'] ?? null,
                'customer_whole_max_qty' => $validated['customer_whole_max_qty'] ?? null,

                'manufacture_date' => $validated['manufacture_date'] ?? null,
                'expiry_date' => $validated['expiry_date'] ?? null,

                'is_online' => (bool)($validated['is_online'] ?? true),
                'is_offline' => (bool)($validated['is_offline'] ?? true),
                'is_pos' => (bool)($validated['is_pos'] ?? true),
                'is_active' => (bool)($validated['is_active'] ?? true),

                'notes' => $validated['notes'] ?? null,

                'is_free_offer_active' => $isOffer,
                'free_product_id' => $validated['free_product_id'] ?? null,
                'free_buy_qty' => $validated['free_buy_qty'] ?? null,
                'free_qty' => $validated['free_qty'] ?? null,
            ]);

            // ✅ Create stock rows (0) for all active locations
            $locationIds = Location::where('is_active', true)->pluck('id');

            foreach ($locationIds as $locId) {
                BatchStock::firstOrCreate(
                    ['product_batch_id' => $batch->id, 'location_id' => $locId],
                    ['on_hand' => 0, 'reserved' => 0]
                );
            }

            // ✅ Add initial stock to selected location
            $initialQty = (float) $validated['quantity'];

            $stockRow = BatchStock::where('product_batch_id', $batch->id)
                ->where('location_id', (int)$validated['location_id'])
                ->lockForUpdate()
                ->first();

            if (!$stockRow) {
                // fallback safety
                $stockRow = BatchStock::create([
                    'product_batch_id' => $batch->id,
                    'location_id' => (int)$validated['location_id'],
                    'on_hand' => 0,
                    'reserved' => 0,
                ]);
            }

            $stockRow->on_hand = (float)$stockRow->on_hand + $initialQty;
            $stockRow->save();

            DB::commit();

            return back()->with('success', 'Batch added + Stock created successfully ✅');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error storing product batch', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to store batch ❌ (Server error: ' . $e->getMessage() . ')');
        }
    }

    // Edit batch
    public function edit(ProductBatch $batch)
    {
        $batch->load('product');

        $products = Product::query()
            ->select(['id', 'name', 'barcode'])
            ->orderBy('name')
            ->limit(200)
            ->get();

        $giftProducts = Product::query()
            ->select(['id', 'name', 'barcode'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $locations = Location::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'type']);

        // ✅ where is this batch stock currently stored?
        $batchStockLocationId = BatchStock::query()
            ->where('product_batch_id', $batch->id)
            ->value('location_id');

        return view('products.product-batches.edit', compact(
            'batch',
            'products',
            'giftProducts',
            'locations',
            'batchStockLocationId'
        ));
    }

    public function update(Request $request, ProductBatch $batch)
    {
        Log::info('ProductBatchController@update start', ['batch_id' => $batch->id]);

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'location_id' => 'required|exists:locations,id', // ✅ required

            'batch_sku'  => 'nullable|string|max:100|unique:product_batches,batch_sku,' . $batch->id,
            'batch_no'   => 'nullable|string|max:50',

            // legacy quantity (we will sync stock on_hand with it)
            'quantity' => 'required|numeric|min:0',
            'unit' => ['required', Rule::in(['pcs', 'dozen', 'box', 'kg', 'g', 'l', 'ml'])],

            'buy_price'           => 'required|numeric|min:0',
            'original_sell_price' => 'required|numeric|min:0',
            'discounted_price'    => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',

            'whole_sell_price'   => 'nullable|numeric|min:0',
            'whole_sell_min_qty' => 'nullable|numeric|min:0',
            'whole_sell_max_qty' => 'nullable|numeric|min:0',

            'customer_whole_price'   => 'nullable|numeric|min:0',
            'customer_whole_min_qty' => 'nullable|numeric|min:0',
            'customer_whole_max_qty' => 'nullable|numeric|min:0',

            'manufacture_date' => 'nullable|date',
            'expiry_date'      => 'nullable|date|after_or_equal:manufacture_date',

            // Gift offer
            'free_product_id'      => 'nullable|exists:products,id|different:product_id',
            'free_buy_qty'         => 'nullable|numeric|min:0.0001',
            'free_qty'             => 'nullable|numeric|min:0.0001',
            'is_free_offer_active' => 'nullable|boolean',

            // checkboxes
            'is_online'  => 'nullable|boolean',
            'is_offline' => 'nullable|boolean',
            'is_pos'     => 'nullable|boolean',
            'is_active'  => 'nullable|boolean',

            'notes' => 'nullable|string|max:500',
        ]);

        try {
            return DB::transaction(function () use ($request, $batch, $validated) {

                // ✅ Lock batch row
                $batch = ProductBatch::query()->lockForUpdate()->findOrFail($batch->id);

                // ====== PRICE COMPUTE ======
                $originalPrice   = (float) $validated['original_sell_price'];
                $discountedPrice = (float) ($validated['discounted_price'] ?? 0);
                $discountPercent = (float) ($validated['discount_percentage'] ?? 0);

                $discountType = 'none';
                if ($discountedPrice > 0) {
                    $sellPrice = $originalPrice - $discountedPrice;
                    $discountType = 'fixed_price';
                    $discountPercent = 0;
                } elseif ($discountPercent > 0) {
                    $sellPrice = $originalPrice - ($originalPrice * ($discountPercent / 100));
                    $discountType = 'percentage';
                    $discountedPrice = 0;
                } else {
                    $sellPrice = $originalPrice;
                    $discountedPrice = 0;
                    $discountPercent = 0;
                }

                $sellPrice = round(max(0, $sellPrice), 4);

                // ✅ normalize checkboxes
                $validated['is_online']  = $request->boolean('is_online');
                $validated['is_offline'] = $request->boolean('is_offline');
                $validated['is_pos']     = $request->boolean('is_pos');
                $validated['is_active']  = $request->boolean('is_active');

                // ✅ computed values
                $validated['sell_price'] = $sellPrice;
                $validated['discounted_price'] = $discountedPrice ?: null;
                $validated['discount_percentage'] = $discountPercent ?: null;

                if (Schema::hasColumn('product_batches', 'discount_type')) {
                    $validated['discount_type'] = $discountType;
                }

                // ====== GIFT RULES ======
                $freeProductId = $validated['free_product_id'] ?? null;
                $isGiftActive  = (bool) ($validated['is_free_offer_active'] ?? false);

                if (!$freeProductId) {
                    $validated['free_product_id'] = null;
                    $validated['free_buy_qty'] = null;
                    $validated['free_qty'] = null;
                    $validated['is_free_offer_active'] = false;
                } else {
                    if ($isGiftActive) {
                        if (empty($validated['free_buy_qty']) || empty($validated['free_qty'])) {
                            return back()->withInput()->withErrors([
                                'free_buy_qty' => 'Buy Qty is required when Gift Offer is enabled.',
                                'free_qty'     => 'Free Qty is required when Gift Offer is enabled.',
                            ]);
                        }

                        // ✅ Gift stock exists (location based)
                        $hasGiftStock = BatchStock::query()
                            ->where('on_hand', '>', 0)
                            ->whereHas('batch', function ($q) use ($freeProductId) {
                                $q->where('product_id', (int) $freeProductId)
                                    ->where('is_active', true);
                            })
                            ->exists();

                        if (!$hasGiftStock) {
                            return back()->withInput()->withErrors([
                                'free_product_id' => 'Gift product has no stock in active batches (location based).',
                            ]);
                        }
                    } else {
                        $validated['is_free_offer_active'] = false;
                    }
                }

                // ====== STOCK SYNC (NO product_id column) ======
                $newLocationId = (int) $validated['location_id'];
                $newQty        = (float) $validated['quantity'];

                $stock = BatchStock::query()
                    ->where('product_batch_id', $batch->id)
                    ->lockForUpdate()
                    ->first();

                if (!$stock) {
                    // ✅ create stock row
                    $stock = BatchStock::create([
                        'product_batch_id' => $batch->id,
                        'location_id'      => $newLocationId,
                        'on_hand'          => $newQty,
                    ]);
                } else {
                    // ✅ update location + on_hand
                    $stock->location_id = $newLocationId;
                    $stock->on_hand     = $newQty;
                    $stock->save();
                }

                // ====== UPDATE BATCH (remove location_id because not in product_batches) ======
                $batchUpdate = $validated;
                unset($batchUpdate['location_id']);

                $batch->update($batchUpdate);

                Log::info('ProductBatchController@update success', [
                    'batch_id' => $batch->id,
                    'stock_id' => $stock->id ?? null,
                    'location_id' => $stock->location_id ?? null,
                    'on_hand' => $stock->on_hand ?? null,
                ]);

                return redirect()
                    ->route('product.batches.all')
                    ->with('success', 'Batch updated successfully ✅');
            });
        } catch (\Throwable $e) {
            Log::error('ProductBatchController@update failed', [
                'batch_id' => $batch->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()->with('error', 'Update failed ❌ ' . $e->getMessage());
        }
    }


    public function destroy(ProductBatch $batch)
    {
        $batch->delete();
        return back()->with('success', 'Batch deleted successfully');
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        if (!$query) {
            return response()->json([]);
        }

        $keywords = preg_split('/\s+/', $query);

        $products = Product::where(function ($q) use ($keywords) {
            foreach ($keywords as $keyword) {
                $q->orWhere('barcode', 'like', "%{$keyword}%")
                    ->orWhere('name', 'like', "%{$keyword}%");
            }
        })
            ->orderByRaw("
            CASE
                WHEN barcode LIKE ? THEN 1
                WHEN name LIKE ? THEN 2
                ELSE 3
            END ASC
        ", ["{$query}%", "{$query}%"])
            ->limit(10)
            ->get(['id', 'name', 'barcode']);

        return response()->json($products);
    }

    public function indexByProduct(Product $product)
    {
        $base = ProductBatch::query()
            ->where('product_id', $product->id)
            ->with(['freeProduct:id,name,barcode'])
            ->orderByDesc('id');

        $batches = (clone $base)
            ->orderByRaw('expiry_date IS NULL')
            ->orderBy('expiry_date', 'asc')
            ->paginate(15);

        $batchIds = (clone $base)->pluck('id');

        $stockAgg = BatchStock::query()
            ->selectRaw('product_batch_id, COALESCE(SUM(on_hand),0) as qty')
            ->whereIn('product_batch_id', $batchIds)
            ->groupBy('product_batch_id')
            ->get()
            ->keyBy('product_batch_id');

        $totalStock = (float)$stockAgg->sum(fn($row) => (float)$row->qty);
        $totalBatches = (int)$batchIds->count();

        $expiringSoon = (clone $base)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>', now())
            ->where('expiry_date', '<=', now()->addDays(30))
            ->count();

        $expired = (clone $base)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now())
            ->count();

        $lowStock = 0;
        $outOfStock = 0;

        foreach ($batchIds as $bid) {
            $q = (float)($stockAgg[$bid]->qty ?? 0);
            if ($q <= 0) $outOfStock++;
            else if ($q <= 10) $lowStock++;
        }

        $giftOffers = (clone $base)->where('is_free_offer_active', true)->count();
        $activeCount = (clone $base)->where('is_active', true)->count();

        $metrics = compact('expiringSoon', 'expired', 'lowStock', 'outOfStock', 'giftOffers', 'activeCount');

        return view('products.product-batches.by-product', compact(
            'product',
            'batches',
            'totalStock',
            'totalBatches',
            'metrics'
        ));
    }

    public function show(ProductBatch $batch)
    {
        $batch->load('product.category', 'product.brand');

        $isExpired = $batch->expiry_date ? $batch->expiry_date->isPast() : false;

        if (request()->ajax()) {
            $batch->load(['product.category', 'product.brand', 'product.images']);
            return view('products.product-batches.show', compact('batch'));
        }

        return view('products.product-batches.show', compact('batch', 'isExpired'));
    }

    public function quickProductSearch(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));

        if (mb_strlen($q) < 2) return response()->json([]);

        $keywords = preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY);

        $products = Product::query()
            ->select(['id', 'name', 'barcode'])
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $kw) {
                    $query->orWhere('barcode', 'like', "%{$kw}%")
                        ->orWhere('name', 'like', "%{$kw}%");
                }
            })
            ->orderByRaw("
                CASE
                    WHEN barcode LIKE ? THEN 1
                    WHEN name LIKE ? THEN 2
                    ELSE 3
                END ASC
            ", ["{$q}%", "{$q}%"])
            ->limit(15)
            ->get();

        return response()->json($products);
    }

    public function quickGiftProductSearch(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));

        if (mb_strlen($q) < 2) return response()->json([]);

        $keywords = preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY);

        $products = Product::query()
            ->select(['id', 'name', 'barcode'])
            ->where('is_active', true)
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $kw) {
                    $query->orWhere('barcode', 'like', "%{$kw}%")
                        ->orWhere('name', 'like', "%{$kw}%");
                }
            })
            ->orderByRaw("
                CASE
                    WHEN barcode LIKE ? THEN 1
                    WHEN name LIKE ? THEN 2
                    ELSE 3
                END ASC
            ", ["{$q}%", "{$q}%"])
            ->limit(15)
            ->get();

        return response()->json($products);
    }

    public function batchesJsonByProduct(Request $request, Product $product): JsonResponse
    {
        $onlyActive = (bool) $request->boolean('active', false);

        $query = ProductBatch::query()
            ->where('product_id', $product->id)
            ->orderByRaw('expiry_date IS NULL')
            ->orderBy('expiry_date', 'asc')
            ->orderByDesc('id')
            ->limit(120);

        if ($onlyActive) $query->where('is_active', true);

        $batches = $query->get([
            'id',
            'product_id',
            'batch_sku',
            'batch_no',
            'quantity', // legacy
            'unit',
            'buy_price',
            'original_sell_price',
            'discounted_price',
            'discount_percentage',
            'sell_price',
            'whole_sell_price',
            'whole_sell_min_qty',
            'whole_sell_max_qty',
            'customer_whole_price',
            'customer_whole_min_qty',
            'customer_whole_max_qty',
            'manufacture_date',
            'expiry_date',
            'is_online',
            'is_offline',
            'is_pos',
            'is_active',
            'notes',
            'is_free_offer_active',
            'free_product_id',
            'free_buy_qty',
            'free_qty',
            'created_at',
        ]);

        // attach location-based stock
        $batchIds = $batches->pluck('id');
        $stockAgg = BatchStock::query()
            ->selectRaw('product_batch_id, COALESCE(SUM(on_hand),0) as on_hand, COALESCE(SUM(reserved),0) as reserved')
            ->whereIn('product_batch_id', $batchIds)
            ->groupBy('product_batch_id')
            ->get()
            ->keyBy('product_batch_id');

        $giftIds = $batches->pluck('free_product_id')->filter()->unique()->values();
        $giftMap = $giftIds->isEmpty()
            ? collect()
            : Product::whereIn('id', $giftIds)->get(['id', 'name', 'barcode'])->keyBy('id');

        $batches = $batches->map(function ($b) use ($giftMap, $stockAgg) {
            $gift = $b->free_product_id ? ($giftMap[$b->free_product_id] ?? null) : null;
            $s = $stockAgg[$b->id] ?? null;

            $onHand = (float)($s->on_hand ?? 0);
            $reserved = (float)($s->reserved ?? 0);

            return array_merge($b->toArray(), [
                'stock_on_hand' => $onHand,
                'stock_reserved' => $reserved,
                'stock_available' => $onHand - $reserved,
                'gift_name' => $gift?->name,
                'gift_barcode' => $gift?->barcode,
            ]);
        });

        return response()->json($batches);
    }

    public function batchJson(ProductBatch $batch): JsonResponse
    {
        $s = BatchStock::query()
            ->selectRaw('COALESCE(SUM(on_hand),0) as on_hand, COALESCE(SUM(reserved),0) as reserved')
            ->where('product_batch_id', $batch->id)
            ->first();

        $onHand = (float)($s->on_hand ?? 0);
        $reserved = (float)($s->reserved ?? 0);

        return response()->json(array_merge(
            $batch->only([
                'id',
                'product_id',
                'batch_sku',
                'batch_no',
                'quantity',
                'unit',
                'buy_price',
                'original_sell_price',
                'discounted_price',
                'discount_percentage',
                'sell_price',
                'whole_sell_price',
                'whole_sell_min_qty',
                'whole_sell_max_qty',
                'customer_whole_price',
                'customer_whole_min_qty',
                'customer_whole_max_qty',
                'manufacture_date',
                'expiry_date',
                'is_online',
                'is_offline',
                'is_pos',
                'is_active',
                'notes',
                'is_free_offer_active',
                'free_product_id',
                'free_buy_qty',
                'free_qty',
            ]),
            [
                'stock_on_hand' => $onHand,
                'stock_reserved' => $reserved,
                'stock_available' => $onHand - $reserved,
            ]
        ));
    }

    public function trash()
    {
        $batches = ProductBatch::onlyTrashed()
            ->with(['product' => fn($q) => $q->withTrashed()]) // show product even if product archived
            ->latest('deleted_at')
            ->paginate(20);

        return view('products.product-batches.trash', compact('batches'));
    }

    public function restore($id)
    {
        $batch = ProductBatch::onlyTrashed()->findOrFail($id);
        $batch->restore();

        return redirect()->route('product-batches.trash')
            ->with('success', 'Batch restored successfully.');
    }

    public function forceDelete($id)
    {
        $batch = ProductBatch::onlyTrashed()->findOrFail($id);

        try {
            // ⚠️ will fail if return_items/order_items/ledger still reference batch_id
            $batch->forceDelete();

            return redirect()->route('product-batches.trash')
                ->with('success', 'Batch permanently deleted.');
        } catch (QueryException $e) {
            Log::error('Batch force delete failed', [
                'batch_id' => $id,
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('product-batches.trash')
                ->with('error', 'Cannot permanently delete this batch because it has history (orders/returns/ledger).');
        }
    }
}
