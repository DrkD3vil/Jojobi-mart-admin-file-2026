<?php

namespace App\Http\Controllers;

use App\Models\BatchStock;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\CustomerRewardLedger;
use App\Models\Location;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Services\CartGiftService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CartController extends Controller
{
    private const POINT_RATE = 1.0; // 1 point = 1 amount (redeem)
    private const EARN_RATE  = 1.0; // ✅ earn points per paid amount (change if you want)

    /**
     * Allowed payment methods by channel
     */
    public const METHODS = [
        'offline' => ['cash', 'card', 'bank', 'cheque'],
        'online'  => ['bkash', 'nagad', 'rocket', 'upay', 'stripe', 'paypal', 'sslcommerz'],
    ];

    /* ===========================
       PAGES
    =========================== */

    public function index(Request $request)
    {
        $locationId = $this->currentLocationId($request);

        $cart = $this->getCart();
        $cart->load($this->cartLoad());

        $locations = Location::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('cart.index', compact('cart', 'locations', 'locationId'));
    }

    public function setLocation(Request $request)
    {
        $request->validate([
            'location_id' => ['required', 'integer', 'min:1'],
        ]);

        session(['location_id' => (int) $request->location_id]);

        return response()->json([
            'success' => true,
            'location_id' => (int) $request->location_id,
        ]);
    }

    /* ===========================
       LOCATION (source of truth)
    =========================== */

    private function currentLocationId(?Request $request = null): int
    {
        $rid = $request ? (int) $request->input('location_id', 0) : 0;
        if ($rid > 0) {
            session(['location_id' => $rid]);
            return $rid;
        }

        $sid = (int) session('location_id', 0);
        if ($sid > 0) return $sid;

        return 1;
    }

    /* ===========================
       PRODUCT SEARCH (LOCATION STOCK)
    =========================== */

    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        if (mb_strlen($q) < 2) return response()->json([]);

        $locationId = $this->currentLocationId($request);

        $products = Product::query()
            ->select(['id', 'name', 'barcode'])
            ->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                    ->orWhere('barcode', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get();

        if ($products->isEmpty()) return response()->json([]);

        $productIds = $products->pluck('id')->all();

        $batches = ProductBatch::query()
            ->select([
                'product_batches.id',
                'product_batches.product_id',
                'product_batches.batch_sku',
                'product_batches.manufacture_date',
                'product_batches.expiry_date',
                'product_batches.unit', // ✅ include batch unit

                'bs.on_hand as stock_on_hand',

                'product_batches.sell_price',
                'product_batches.whole_sell_price',
                'product_batches.customer_whole_price',

                'product_batches.original_sell_price',
                'product_batches.discounted_price',
                'product_batches.discount_percentage',

                'product_batches.whole_sell_min_qty',
                'product_batches.whole_sell_max_qty',
                'product_batches.customer_whole_min_qty',
                'product_batches.customer_whole_max_qty',
            ])
            ->join('batch_stocks as bs', 'bs.product_batch_id', '=', 'product_batches.id')
            ->where('bs.location_id', $locationId)
            ->where('bs.on_hand', '>', 0)
            ->whereIn('product_batches.product_id', $productIds)
            ->where('product_batches.is_active', true)
            ->orderByRaw('product_batches.manufacture_date IS NULL, product_batches.manufacture_date ASC')
            ->orderBy('product_batches.id')
            ->limit(60)
            ->get()
            ->groupBy('product_id');

        $images = DB::table('product_images')
            ->select(['id', 'product_id', 'image_path', 'is_primary'])
            ->whereIn('product_id', $productIds)
            ->get()
            ->groupBy('product_id');

        $results = [];
        foreach ($products as $p) {
            foreach (($batches[$p->id] ?? collect()) as $b) {

                $imgs = ($images[$p->id] ?? collect())
                    ->map(fn($img) => [
                        'image_path' => asset($img->image_path),
                        'is_primary' => (int) $img->is_primary,
                    ])->values()->all();

                $results[] = [
                    'batch_id' => (int) $b->id,
                    'product_id' => (int) $p->id,
                    'name' => $p->name,
                    'barcode' => $p->barcode,
                    'batch_sku' => $b->batch_sku,
                    'manufacture_date' => $b->manufacture_date,
                    'expiry_date' => $b->expiry_date,

                    'unit' => $b->unit ?? 'pcs', // ✅ batch unit for UI

                    'location_id' => $locationId,
                    'quantity' => (float) $b->stock_on_hand,

                    'sell_price' => (float) $b->sell_price,
                    'whole_sell_price' => (float) $b->whole_sell_price,
                    'customer_whole_price' => (float) $b->customer_whole_price,

                    'whole_sell_min_qty' => $b->whole_sell_min_qty !== null ? (float) $b->whole_sell_min_qty : null,
                    'whole_sell_max_qty' => $b->whole_sell_max_qty !== null ? (float) $b->whole_sell_max_qty : null,
                    'customer_whole_min_qty' => $b->customer_whole_min_qty !== null ? (float) $b->customer_whole_min_qty : null,
                    'customer_whole_max_qty' => $b->customer_whole_max_qty !== null ? (float) $b->customer_whole_max_qty : null,

                    'images' => $imgs,
                ];
            }
        }

        return response()->json($results);
    }

    /* ===========================
       CART MUTATIONS (UNIT + LOCATION STOCK)
    =========================== */

    public function add(Request $request)
    {
        $data = $request->validate([
            'batch_id'     => 'required|exists:product_batches,id',
            'quantity'     => 'required|numeric|min:0.0001', // ✅ allow decimals for g/ml
            'unit'         => 'nullable|string|max:10',      // ✅ kg/g/l/ml/pcs
            'price_type'   => 'nullable|string|in:retail,whole,customer_whole',
            'location_id'  => 'nullable|exists:locations,id',
        ]);

        $saleQty  = (float) $data['quantity'];
        $type     = $data['price_type'] ?? 'retail';
        $locationId = $this->currentLocationId($request);

        try {
            return DB::transaction(function () use ($data, $saleQty, $type, $locationId) {

                $cart = $this->lockActiveCart();

                $batch = ProductBatch::with([
                    'product:id,name,barcode',
                    'product.images:id,product_id,image_path,is_primary'
                ])->lockForUpdate()->findOrFail($data['batch_id']);

                // ✅ unit conversion (sale -> batch unit qty)
                $saleUnit = $this->normalizeSaleUnit($data['unit'] ?? null, (string)($batch->unit ?? 'pcs'));

                $qtyBatch = $this->toBatchQty($saleQty, $saleUnit, (string)($batch->unit ?? 'pcs'));
                if ($qtyBatch <= 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid unit/quantity',
                        'errors' => ['quantity' => ['Invalid unit/quantity conversion']]
                    ], 422);
                }

                // ✅ Validate stock using BatchStock (location-based) in batch unit
                $this->assertBatchStockAvailable($batch->id, $cart->id, $locationId, $qtyBatch);

                $primary = $batch->product->images->firstWhere('is_primary', 1) ?? $batch->product->images->first();
                $imageId = $primary?->id;

                $hint = null;

                // ✅ pricing rules based on batch-unit qty
                $calc = $this->computeLineBatch($batch, $qtyBatch, $type);

                if (!$calc['eligible']) {
                    $hint = $this->rangeHint($calc) . ' Added as Retail.';
                    $type = 'retail';
                    $calc = $this->computeLineBatch($batch, $qtyBatch, 'retail');
                }

                // Find existing PAID row (same batch+type+unit+location)
                $itemQ = CartItem::where('cart_id', $cart->id)
                    ->where('product_batch_id', $batch->id)
                    ->where('price_type', $type)
                    ->where(function ($q) {
                        $q->whereNull('is_gift')->orWhere('is_gift', false);
                    })
                    ->lockForUpdate();

                if (Schema::hasColumn('cart_items', 'location_id')) {
                    $itemQ->where('location_id', $locationId);
                }
                if (Schema::hasColumn('cart_items', 'unit')) {
                    $itemQ->where('unit', $saleUnit);
                }

                $item = $itemQ->first();
                $flashId = null;

                if ($item) {
                    $newSaleQty  = (float) $item->quantity + $saleQty;
                    $newBatchQty = (float) ($item->qty_in_batch_unit ?? $item->quantity) + $qtyBatch;

                    // validate delta only
                    $this->assertBatchStockAvailable($batch->id, $cart->id, $locationId, $qtyBatch, $item->id);

                    $newCalc = $this->computeLineBatch($batch, $newBatchQty, $type);

                    if ($newCalc['eligible']) {
                        $item->discount_amount  = $newCalc['discount_amount'];
                        $item->discount_percent = $newCalc['discount_percent'];
                        $item->discount_label   = $newCalc['discount_label'];
                    } else {
                        $hint = $this->rangeHint($newCalc);
                    }

                    // ✅ total based on batch qty, unit_price shown per sale unit
                    $item->quantity = $newSaleQty;

                    if (Schema::hasColumn('cart_items', 'qty_in_batch_unit')) {
                        $item->qty_in_batch_unit = $newBatchQty;
                    }
                    if (Schema::hasColumn('cart_items', 'unit')) {
                        $item->unit = $saleUnit;
                    }

                    if (!$item->product_image_id && $imageId) $item->product_image_id = $imageId;

                    $item->total_price = (float) $newCalc['total_price'];
                    $item->unit_price  = $newSaleQty > 0 ? (float) round($item->total_price / $newSaleQty, 4) : 0.0;

                    $item->save();

                    $flashId = $item->id;
                } else {
                    $payload = [
                        'cart_id'          => $cart->id,
                        'product_id'       => $batch->product_id,
                        'product_batch_id' => $batch->id,
                        'product_image_id' => $imageId,
                        'price_type'       => $type,

                        // ✅ store sale qty + batch qty
                        'quantity'         => $saleQty,
                        'unit_price'       => $saleQty > 0 ? (float) round($calc['total_price'] / $saleQty, 4) : 0.0,
                        'total_price'      => (float) $calc['total_price'],

                        'discount_amount'  => $calc['discount_amount'],
                        'discount_percent' => $calc['discount_percent'],
                        'discount_label'   => $calc['discount_label'],

                        'is_gift'          => false,
                        'gift_source'      => null,
                        'gift_source_id'   => null,
                        'parent_cart_item_id' => null,
                    ];

                    if (Schema::hasColumn('cart_items', 'location_id')) {
                        $payload['location_id'] = $locationId;
                    }
                    if (Schema::hasColumn('cart_items', 'unit')) {
                        $payload['unit'] = $saleUnit;
                    }
                    if (Schema::hasColumn('cart_items', 'qty_in_batch_unit')) {
                        $payload['qty_in_batch_unit'] = $qtyBatch;
                    }

                    $item = CartItem::create($payload);
                    $flashId = $item->id;
                }

                // $sync = app(CartGiftService::class)->sync($cart);

                $cart->loadMissing(['items.batch', 'items.product']); // ✅ IMPORTANT
$sync = app(CartGiftService::class)->sync($cart);

                $this->recalcCart($cart);
                $cart->load($this->cartLoad());

                return response()->json([
                    'success' => true,
                    'cart' => $this->payload($cart),
                    'hint' => $hint ? "✨ {$hint}" : null,
                    'flash_item_id' => $flashId,
                    'gift_hints' => $sync['hints'] ?? [],
                ]);
            });
        } catch (QueryException $e) {
            if (($e->errorInfo[1] ?? null) == 1062) {
                return response()->json([
                    'success' => false,
                    'message' => 'This item/gift already exists in the cart. Please refresh and try again.',
                ], 409);
            }
            throw $e;
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => 'Not enough stock'], 422);
        }
    }

    public function updateItem(Request $request)
    {
        $data = $request->validate([
            'item_id'     => 'required|exists:cart_items,id',
            'price_type'  => 'required|string|in:retail,whole,customer_whole',
            'quantity'    => 'required|numeric|min:0.0001', // ✅ decimals allowed
            'unit'        => 'nullable|string|max:10',      // ✅ new
            'location_id' => 'nullable|exists:locations,id',
        ]);

        $locationId = $this->currentLocationId($request);

        return DB::transaction(function () use ($data, $locationId) {

            $cart = $this->lockActiveCart();

            $item = CartItem::where('id', $data['item_id'])
                ->where('cart_id', $cart->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($item->is_gift) {
                return response()->json(['success' => false, 'message' => 'Gift item cannot be edited'], 403);
            }

            $batch = ProductBatch::lockForUpdate()->findOrFail($item->product_batch_id);

            $newSaleQty = (float) $data['quantity'];
            $newType    = $data['price_type'];

            $saleUnit = $this->normalizeSaleUnit(
                $data['unit'] ?? ($item->unit ?? null),
                (string)($batch->unit ?? 'pcs')
            );

            $newBatchQty = $this->toBatchQty($newSaleQty, $saleUnit, (string)($batch->unit ?? 'pcs'));
            if ($newBatchQty <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid unit/quantity',
                ], 422);
            }

            $oldBatchQty = (float) ($item->qty_in_batch_unit ?? $item->quantity);
            $deltaNeeded = max(0.0, $newBatchQty - $oldBatchQty);
            if ($deltaNeeded > 0) {
                $this->assertBatchStockAvailable($batch->id, $cart->id, $locationId, $deltaNeeded, $item->id);
            }

            $calc = $this->computeLineBatch($batch, $newBatchQty, $newType);

            if (!$calc['eligible']) {
                // Save qty anyway, but keep pricing unchanged
                $item->quantity = $newSaleQty;

                if (Schema::hasColumn('cart_items', 'qty_in_batch_unit')) {
                    $item->qty_in_batch_unit = $newBatchQty;
                }
                if (Schema::hasColumn('cart_items', 'unit')) {
                    $item->unit = $saleUnit;
                }

                $item->total_price = (float) $item->unit_price * (float) $item->quantity;
                $item->save();

                // $sync = app(CartGiftService::class)->sync($cart);
$cart->loadMissing(['items.batch', 'items.product']); // ✅ ensure service sees latest rows
$sync = app(CartGiftService::class)->sync($cart);

                $this->recalcCart($cart);
                $cart->load($this->cartLoad());

                return response()->json([
                    'success' => false,
                    'message' => $calc['hint'] ?? 'Quantity condition not met',
                    'invalid_item_id' => $item->id,
                    'required_min' => $calc['required_min'],
                    'required_max' => $calc['required_max'],
                    'required_type' => $newType,
                    'cart_total' => (float) $cart->total,
                    'cart' => $this->payload($cart),
                    'gift_hints' => $sync['hints'] ?? [],
                ], 422);
            }

            // merge into existing paid row of same batch+type+unit(+location)
            $targetQ = CartItem::where('cart_id', $cart->id)
                ->where('product_batch_id', $item->product_batch_id)
                ->where('price_type', $newType)
                ->where(function ($q) {
                    $q->whereNull('is_gift')->orWhere('is_gift', false);
                })
                ->lockForUpdate();

            if (Schema::hasColumn('cart_items', 'location_id')) {
                $targetQ->where('location_id', $locationId);
            }
            if (Schema::hasColumn('cart_items', 'unit')) {
                $targetQ->where('unit', $saleUnit);
            }

            $target = $targetQ->first();
            $flashId = null;

            if ($target && $target->id !== $item->id) {
                $targetSaleQty  = (float) $target->quantity;
                $targetBatchQty = (float) ($target->qty_in_batch_unit ?? $target->quantity);

                $mergedSaleQty  = $targetSaleQty + $newSaleQty;
                $mergedBatchQty = $targetBatchQty + $newBatchQty;

                // need extra vs existing target
                $needExtra = max(0.0, $mergedBatchQty - $targetBatchQty);
                if ($needExtra > 0) {
                    $this->assertBatchStockAvailable($batch->id, $cart->id, $locationId, $needExtra, $item->id);
                }

                $mergedCalc = $this->computeLineBatch($batch, $mergedBatchQty, $newType);
                if (!$mergedCalc['eligible']) {
                    return response()->json(['success' => false, 'message' => 'Quantity condition not met after merge'], 422);
                }

                $target->discount_amount  = $mergedCalc['discount_amount'];
                $target->discount_percent = $mergedCalc['discount_percent'];
                $target->discount_label   = $mergedCalc['discount_label'];

                $target->quantity = $mergedSaleQty;
                if (Schema::hasColumn('cart_items', 'qty_in_batch_unit')) {
                    $target->qty_in_batch_unit = $mergedBatchQty;
                }
                if (Schema::hasColumn('cart_items', 'unit')) {
                    $target->unit = $saleUnit;
                }

                $target->total_price = (float) $mergedCalc['total_price'];
                $target->unit_price  = $mergedSaleQty > 0 ? (float) round($target->total_price / $mergedSaleQty, 4) : 0.0;
                $target->save();

                $item->delete();
                $flashId = $target->id;
            } else {
                $item->price_type       = $newType;

                $item->discount_amount  = $calc['discount_amount'];
                $item->discount_percent = $calc['discount_percent'];
                $item->discount_label   = $calc['discount_label'];

                $item->quantity = $newSaleQty;

                if (Schema::hasColumn('cart_items', 'qty_in_batch_unit')) {
                    $item->qty_in_batch_unit = $newBatchQty;
                }
                if (Schema::hasColumn('cart_items', 'unit')) {
                    $item->unit = $saleUnit;
                }
                if (Schema::hasColumn('cart_items', 'location_id')) {
                    $item->location_id = $locationId;
                }

                $item->total_price = (float) $calc['total_price'];
                $item->unit_price  = $newSaleQty > 0 ? (float) round($item->total_price / $newSaleQty, 4) : 0.0;

                $item->save();
                $flashId = $item->id;
            }

            $sync = app(CartGiftService::class)->sync($cart);
            $this->recalcCart($cart);
            $cart->load($this->cartLoad());

            return response()->json([
                'success' => true,
                'cart' => $this->payload($cart),
                'flash_item_id' => $flashId,
                'hint' => null,
                'gift_hints' => $sync['hints'] ?? [],
            ]);
        });
    }

    public function removeItem(Request $request, CartItem $item)
    {
        return DB::transaction(function () use ($item) {
            $cart = $this->lockActiveCart();

            if ($item->cart_id !== $cart->id) {
                return response()->json(['success' => false, 'message' => 'Invalid item'], 403);
            }

            if ($item->is_gift && $item->gift_source === 'batch_offer') {
                return response()->json(['success' => false, 'message' => 'Auto gift cannot be removed directly'], 403);
            }

            $item->delete();

            $sync = app(CartGiftService::class)->sync($cart);

            $this->recalcCart($cart);
            $cart->load($this->cartLoad());

            return response()->json([
                'success' => true,
                'cart' => $this->payload($cart),
                'gift_hints' => $sync['hints'] ?? [],
            ]);
        });
    }

    public function clear()
    {
        return DB::transaction(function () {
            $cart = $this->lockActiveCart();

            // CartItem::where('cart_id', $cart->id)->delete();

            $cart->total = 0;
            $cart->rewards_points_used = 0;
            $cart->rewards_amount_used = 0;
            if ($this->cartHasColumn($cart, 'order_discount')) $cart->order_discount = 0;
            $cart->save();

            $cart->load($this->cartLoad());

            return response()->json([
                'success' => true,
                'cart' => $this->payload($cart),
            ]);
        });
    }

    /* ===========================
       CUSTOMER
    =========================== */

    public function setCustomer(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        return DB::transaction(function () use ($data) {
            $cart = $this->lockActiveCart();

            $cart->customer_id = $data['customer_id'] ?? null;
            $cart->rewards_points_used = 0;
            $cart->rewards_amount_used = 0;

            if ($this->cartHasColumn($cart, 'order_discount') && $cart->order_discount === null) {
                $cart->order_discount = 0;
            }
            $cart->save();

            $customer = null;
            if (!empty($data['customer_id'])) {
                $customer = Customer::select('id', 'name', 'phone', 'due_balance', 'advance_balance', 'reward_points')
                    ->lockForUpdate()
                    ->find($data['customer_id']);
            }

            return response()->json([
                'success' => true,
                'customer' => $customer ? [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'due_balance' => (float) $customer->due_balance,
                    'advance_balance' => (float) $customer->advance_balance,
                    'reward_points' => (float) $customer->reward_points,
                ] : null,
            ]);
        });
    }

    /* ===========================
       CHECKOUT (DEDUCT BATCH_STOCKS LOCATION-WISE)
       + ✅ Earn reward AFTER payment (when paid)
    =========================== */

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'order_discount'       => 'nullable|numeric|min:0',
            'rewards_points_used'  => 'nullable|numeric|min:0',
            'rewards_amount_used'  => 'nullable|numeric|min:0',

            'payment_note' => 'nullable|string|max:2000',
            'payments' => 'nullable|array|min:1',

            'payments.*.channel' => 'required_with:payments|string|in:offline,online',
            'payments.*.method'  => 'required_with:payments|string',
            'payments.*.amount'  => 'required_with:payments|numeric|min:0.0001',
            'payments.*.trx_id'  => 'nullable|string|max:80',
            'payments.*.account_label' => 'nullable|string|max:120',

            'apply_balance_mode' => 'nullable|string|in:auto,none',

            'location_id' => 'nullable|exists:locations,id',
        ]);

        $locationId = $this->currentLocationId($request);

        return DB::transaction(function () use ($data, $locationId) {

            $cart = $this->lockActiveCart();

            app(CartGiftService::class)->sync($cart);

            $cart->load([
                'customer',
                'items.product',
                'items.batch',
                'items' => fn($q) => $q->lockForUpdate(),
            ]);

            if ($cart->items->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Cart is empty'], 422);
            }

            // ✅ Validate AGAIN before checkout (avoid race) using batch qty
            foreach ($cart->items as $ci) {
                $needBatch = (float) ($ci->qty_in_batch_unit ?? $ci->quantity);
                $this->assertBatchStockAvailable((int)$ci->product_batch_id, (int)$cart->id, $locationId, $needBatch);
            }

            $this->recalcCart($cart);
            $cartTotal = (float) $cart->total;

            $customer = null;
            if ($cart->customer_id) {
                $customer = Customer::whereKey($cart->customer_id)->lockForUpdate()->first();
            }

            $applyMode = $data['apply_balance_mode'] ?? 'auto';
            if (!$customer) $applyMode = 'none';

            $oldDue     = $customer ? max(0, (float) $customer->due_balance) : 0.0;
            $oldAdvance = $customer ? max(0, (float) $customer->advance_balance) : 0.0;

            $orderDiscount = max(0, (float) ($data['order_discount'] ?? 0));
            if ($orderDiscount > $cartTotal) $orderDiscount = $cartTotal;

            $usedPoints = max(0, (float) ($data['rewards_points_used'] ?? 0));
            if (!$customer) $usedPoints = 0;

            $rewardAmountReq = array_key_exists('rewards_amount_used', $data)
                ? max(0, (float) ($data['rewards_amount_used'] ?? 0))
                : ($usedPoints * self::POINT_RATE);

            $rewardAmount = 0.0;

            // ✅ redeem points
            if ($customer && $usedPoints > 0) {
                if ((float) $customer->reward_points < $usedPoints) {
                    return response()->json(['success' => false, 'message' => 'Not enough reward points'], 422);
                }

                $maxAmount    = $usedPoints * self::POINT_RATE;
                $rewardAmount = min($rewardAmountReq, $maxAmount);

                $customer->reward_points = (float) $customer->reward_points - $usedPoints;
                $customer->save();

                CustomerRewardLedger::create([
                    'customer_id' => $customer->id,
                    'action' => 'redeem',
                    'direction' => 'subtract',
                    'points' => $usedPoints,
                    'ref_type' => 'order',
                    'ref_id' => null,
                    'channel' => 'pos',
                    'terminal_id' => null,
                    'created_by' => auth()->id(),
                    'idempotency_key' => null,
                    'note' => 'Redeemed on checkout',
                ]);
            }

            $discountTotal = $rewardAmount + $orderDiscount;
            if ($discountTotal > $cartTotal) $discountTotal = $cartTotal;

            $orderPayable = max(0, $cartTotal - $discountTotal);

            $advanceUsed = 0.0;
            if ($customer && $applyMode === 'auto' && $orderPayable > 0 && $oldAdvance > 0) {
                $advanceUsed = min($oldAdvance, $orderPayable);
                $orderPayable = $orderPayable - $advanceUsed;
            }

            $netCollect = $orderPayable;
            if ($customer && $applyMode === 'auto' && $oldDue > 0) {
                $netCollect += $oldDue;
            }

            $payments = $data['payments'] ?? null;

            if ($netCollect > 0) {
                if (is_array($payments)) {
                    foreach ($payments as $p) {
                        $channel = $p['channel'];
                        $method  = $p['method'];
                        $allowed = self::METHODS[$channel] ?? [];

                        if (!in_array($method, $allowed, true)) {
                            return response()->json([
                                'success' => false,
                                'message' => "Invalid method '{$method}' for '{$channel}'"
                            ], 422);
                        }
                        if ($channel === 'online' && empty($p['trx_id'])) {
                            return response()->json([
                                'success' => false,
                                'message' => "Trx ID required for online payment ({$method})"
                            ], 422);
                        }
                    }
                }
            } else {
                $payments = null;
            }

            $order = Order::create([
                'order_no' => 'ORD-' . now()->format('YmdHis') . '-' . random_int(100, 999),
                'session_id' => $cart->session_id,
                'customer_id' => $cart->customer_id,
                 'location_id' => $locationId, // ✅ add this
                'subtotal' => $cartTotal,
                'discount_total' => $discountTotal,
                'payable_total' => $netCollect,

                'rewards_points_used' => $usedPoints,
                'rewards_amount_used' => $rewardAmount,

                'paid_total' => 0,
                'due_total' => $netCollect,
                'change_total' => 0,
                'payment_status' => $netCollect > 0 ? 'unpaid' : 'paid',
                'payment_note' => $data['payment_note'] ?? null,

                'status' => $netCollect > 0 ? 'pending' : 'completed',
            ]);

            // attach redeem ledger to order
            if ($customer && $usedPoints > 0) {
                CustomerRewardLedger::where('customer_id', $customer->id)
                    ->where('action', 'redeem')
                    ->where('direction', 'subtract')
                    ->whereNull('ref_id')
                    ->latest('id')
                    ->take(1)
                    ->update(['ref_id' => $order->id]);
            }

            // ✅ Create order items + reduce stock in batch_stocks using batch qty
            foreach ($cart->items as $ci) {

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $ci->product_id,
                    'product_batch_id' => $ci->product_batch_id,
                    'product_name' => $ci->product?->name,
                    'barcode' => $ci->product?->barcode,
                    'price_type' => $ci->price_type,
                    'unit_price' => (float) $ci->unit_price,     // per sale unit (display)
                    'quantity' => (float) $ci->quantity,         // sale qty (display)
                    'unit' => $ci -> unit,
                    'discount_amount' => (float) ($ci->discount_amount ?? 0),
                    'total_price' => (float) $ci->total_price,
                ]);

                $needBatch = (float) ($ci->qty_in_batch_unit ?? $ci->quantity);

                $stock = BatchStock::query()
                    ->where('product_batch_id', (int)$ci->product_batch_id)
                    ->where('location_id', $locationId)
                    ->lockForUpdate()
                    ->first();

                if (!$stock || (float)$stock->on_hand < $needBatch) {
                    return response()->json([
                        'success' => false,
                        'message' => "Not enough location stock for batch {$ci->product_batch_id} (need {$needBatch})"
                    ], 422);
                }

                $stock->on_hand = (float)$stock->on_hand - $needBatch;
                $stock->save();

                $this->syncLegacyBatchQuantity((int)$ci->product_batch_id);
            }

            // payments rows
            if ($netCollect > 0 && is_array($payments) && !empty($payments)) {
                foreach ($payments as $p) {
                    Payment::create([
                        'order_id' => $order->id,
                        'channel' => $p['channel'],
                        'method' => $p['method'],
                        'trx_id' => $p['trx_id'] ?? null,
                        'account_label' => $p['account_label'] ?? null,
                        'amount' => (float) $p['amount'],
                        'status' => 'captured',
                        'meta' => null,
                    ]);
                }
            }

            $paid = (float) Payment::where('order_id', $order->id)
                ->where('status', 'captured')
                ->sum('amount');

            $due = 0.0;
            $change = 0.0;
            $paymentStatus = 'unpaid';

            if ($netCollect <= 0) {
                $paid = 0;
                $due = 0;
                $change = 0;
                $paymentStatus = 'paid';
            } elseif ($paid <= 0) {
                $due = $netCollect;
                $paymentStatus = 'unpaid';
            } elseif ($paid < $netCollect) {
                $due = $netCollect - $paid;
                $paymentStatus = 'partial';
            } else {
                $due = 0;
                $change = $paid - $netCollect;
                $paymentStatus = 'paid';
            }

            $order->paid_total = $paid;
            $order->due_total = $due;
            $order->change_total = $change;
            $order->payment_status = $paymentStatus;
            $order->status = ($paymentStatus === 'paid') ? 'completed' : 'pending';
            $order->save();

            // ✅ Earn reward points AFTER payment (only if fully paid)
            // policy: earn = floor(paid_total * EARN_RATE), only if customer exists and order is paid
            if ($customer && $paymentStatus === 'paid' && $paid > 0) {
                $earnPoints = (float) floor($paid * self::EARN_RATE);

                if ($earnPoints > 0) {
                    $customer->reward_points = (float) $customer->reward_points + $earnPoints;
                    $customer->save();

                    CustomerRewardLedger::create([
                        'customer_id' => $customer->id,
                        'action' => 'earn',
                        'direction' => 'add',
                        'points' => $earnPoints,
                        'ref_type' => 'order',
                        'ref_id' => $order->id,
                        'channel' => 'pos',
                        'terminal_id' => null,
                        'created_by' => auth()->id(),
                        'idempotency_key' => null,
                        'note' => "Earned on paid order ({$order->order_no})",
                    ]);
                }
            }

            $cart->rewards_points_used = $usedPoints;
            $cart->rewards_amount_used = $rewardAmount;
            $cart->payable_total = $netCollect;
            $cart->save();

            // CartItem::where('cart_id', $cart->id)->delete();

            $order->refresh();

            return response()->json([
                'success' => true,
                'message' => $order->payment_status === 'paid'
                    ? 'Checkout completed and payment received.'
                    : 'Checkout completed. Payment remaining.',
                'order' => [
                    'id' => $order->id,
                    'order_no' => $order->order_no,
                    'status' => $order->status,
                    'subtotal' => (float) $order->subtotal,
                    'discount_total' => (float) $order->discount_total,
                    'payable_total' => (float) $order->payable_total,
                    'paid_total' => (float) $order->paid_total,
                    'due_total' => (float) $order->due_total,
                    'change_total' => (float) $order->change_total,
                    'payment_status' => $order->payment_status,
                    'applied_mode' => $applyMode,
                    'old_due_included' => ($customer && $applyMode === 'auto') ? $oldDue : 0.0,
                    'advance_used' => ($customer && $applyMode === 'auto') ? $advanceUsed : 0.0,
                    'location_id' => $locationId,
                ],
                'guest' => $customer ? false : true,
                'invoice_url' => route('invoice.show', $order->id),
            ]);
        });
    }

    /* ===========================
       MANUAL GIFT (LOCATION STOCK)
       ✅ gifts also should use batch qty (unit fixed to batch unit)
    =========================== */

    public function addManualGift(Request $request)
    {
        $data = $request->validate([
            'product_id'   => 'required|exists:products,id',
            'quantity'     => 'nullable|numeric|min:0.0001',
            'location_id'  => 'nullable|exists:locations,id',
        ]);

        $locationId = $this->currentLocationId($request);

        return DB::transaction(function () use ($data, $locationId) {

            $cart = $this->lockActiveCart();
            $saleQty = (float) ($data['quantity'] ?? 1);

            $giftBatch = ProductBatch::query()
                ->select('product_batches.*')
                ->join('batch_stocks as bs', 'bs.product_batch_id', '=', 'product_batches.id')
                ->where('product_batches.product_id', (int)$data['product_id'])
                ->where('product_batches.is_active', true)
                ->where('bs.location_id', $locationId)
                ->where('bs.on_hand', '>', 0)
                ->orderByRaw('product_batches.expiry_date is null')
                ->orderBy('product_batches.expiry_date', 'asc')
                ->orderBy('product_batches.id', 'asc')
                ->lockForUpdate()
                ->first();

            if (!$giftBatch) {
                return response()->json(['success' => false, 'message' => 'Gift product is out of stock in this location'], 422);
            }

            // Gifts: keep unit same as batch unit (no switching)
            $saleUnit = $this->normalizeSaleUnit(null, (string)($giftBatch->unit ?? 'pcs'));
            $qtyBatch = $this->toBatchQty($saleQty, $saleUnit, (string)($giftBatch->unit ?? 'pcs'));

            if ($qtyBatch <= 0) {
                return response()->json(['success' => false, 'message' => 'Invalid gift quantity'], 422);
            }

            $this->assertBatchStockAvailable($giftBatch->id, $cart->id, $locationId, $qtyBatch);

            $existingQ = CartItem::where('cart_id', $cart->id)
                ->where('product_batch_id', $giftBatch->id)
                ->where('is_gift', true)
                ->where('gift_source', 'manual')
                ->lockForUpdate();

            if (Schema::hasColumn('cart_items', 'location_id')) {
                $existingQ->where('location_id', $locationId);
            }
            if (Schema::hasColumn('cart_items', 'unit')) {
                $existingQ->where('unit', $saleUnit);
            }

            $existing = $existingQ->first();

            if ($existing) {
                $this->assertBatchStockAvailable($giftBatch->id, $cart->id, $locationId, $qtyBatch, $existing->id);

                $existing->quantity = (float) $existing->quantity + $saleQty;

                if (Schema::hasColumn('cart_items', 'qty_in_batch_unit')) {
                    $existing->qty_in_batch_unit = (float) ($existing->qty_in_batch_unit ?? $existing->quantity) + $qtyBatch;
                }

                $existing->save();
            } else {
                $payload = [
                    'cart_id' => $cart->id,
                    'product_id' => (int) $data['product_id'],
                    'product_batch_id' => (int) $giftBatch->id,
                    'product_image_id' => null,
                    'price_type' => 'gift',
                    'unit_price' => 0,
                    'quantity' => $saleQty,
                    'discount_amount' => 0,
                    'discount_percent' => null,
                    'discount_label' => 'Manual Gift',
                    'total_price' => 0,
                    'is_gift' => true,
                    'gift_source' => 'manual',
                    'gift_source_id' => null,
                    'parent_cart_item_id' => null,
                ];

                if (Schema::hasColumn('cart_items', 'location_id')) {
                    $payload['location_id'] = $locationId;
                }
                if (Schema::hasColumn('cart_items', 'unit')) {
                    $payload['unit'] = $saleUnit;
                }
                if (Schema::hasColumn('cart_items', 'qty_in_batch_unit')) {
                    $payload['qty_in_batch_unit'] = $qtyBatch;
                }

                CartItem::create($payload);
            }

            app(CartGiftService::class)->sync($cart);

            $this->recalcCart($cart);
            $cart->load($this->cartLoad());

            return response()->json([
                'success' => true,
                'cart' => $this->payload($cart),
            ]);
        });
    }

    public function removeManualGift(Request $request, CartItem $item)
    {
        return DB::transaction(function () use ($item) {
            $cart = $this->lockActiveCart();

            if ($item->cart_id !== $cart->id) {
                return response()->json(['success' => false, 'message' => 'Invalid item'], 403);
            }

            if (!$item->is_gift || $item->gift_source !== 'manual') {
                return response()->json(['success' => false, 'message' => 'Not a manual gift'], 422);
            }

            $item->delete();

            app(CartGiftService::class)->sync($cart);

            $this->recalcCart($cart);
            $cart->load($this->cartLoad());

            return response()->json([
                'success' => true,
                'cart' => $this->payload($cart),
            ]);
        });
    }

    /* ===========================
       HELPERS
    =========================== */

    private function getCart(): Cart
    {
        $sessionId = session()->getId();

        $cart = Cart::where('session_id', $sessionId)
            ->whereNull('payable_total')
            ->latest('id')
            ->first();

        if ($cart) return $cart;

        return Cart::create([
            'session_id' => $sessionId,
            'total' => 0,
            'customer_id' => null,
            'rewards_points_used' => 0,
            'rewards_amount_used' => 0,
        ]);
    }

    private function lockActiveCart(): Cart
    {
        $sessionId = session()->getId();

        $cart = Cart::where('session_id', $sessionId)
            ->whereNull('payable_total')
            ->latest('id')
            ->lockForUpdate()
            ->first();

        if ($cart) return $cart;

        return Cart::create([
            'session_id' => $sessionId,
            'total' => 0,
            'customer_id' => null,
            'rewards_points_used' => 0,
            'rewards_amount_used' => 0,
        ]);
    }

    private function cartLoad(): array
    {
        return [
            'items.product:id,name,barcode',
            'items.batch:id,product_id,batch_sku,unit,manufacture_date,expiry_date,original_sell_price,sell_price,whole_sell_price,customer_whole_price,discounted_price,discount_percentage,whole_sell_min_qty,whole_sell_max_qty,customer_whole_min_qty,customer_whole_max_qty,free_product_id,free_buy_qty,free_qty,is_free_offer_active',
            'items.image:id,image_path,is_primary',
            'customer:id,name,phone,due_balance,advance_balance,reward_points',
        ];
    }

    private function recalcCart(Cart $cart): void
    {
        $cart->total = (float) CartItem::where('cart_id', $cart->id)->sum('total_price');
        $cart->save();
    }

    private function payload(Cart $cart): array
    {
        $locationId = (int) session('location_id', 1);

        $batchIds = $cart->items->pluck('product_batch_id')->filter()->unique()->values()->all();

        $stockMap = empty($batchIds)
            ? collect()
            : BatchStock::query()
            ->whereIn('product_batch_id', $batchIds)
            ->where('location_id', $locationId)
            ->get(['product_batch_id', 'on_hand'])
            ->keyBy('product_batch_id');

        return [
            'id' => $cart->id,
            'total' => (float) $cart->total,
            'location_id' => $locationId,
            'items' => $cart->items->map(function ($i) use ($stockMap) {
                $stock = $stockMap[(int)$i->product_batch_id] ?? null;

                return [
                    'id' => $i->id,
                    'batch_id' => $i->product_batch_id,
                    'product_id' => $i->product_id,
                    'name' => $i->product?->name,
                    'barcode' => $i->product?->barcode,
                    'batch_sku' => $i->batch?->batch_sku,

                    // ✅ show batch unit + selected sale unit
                    'batch_unit' => $i->batch?->unit ?? 'pcs',
                    'unit' => $i->unit ?? ($i->batch?->unit ?? 'pcs'),

                    // ✅ location stock (batch unit)
                    'stock' => $stock ? (float)$stock->on_hand : 0.0,

                    'price_type' => $i->price_type,
                    'unit_price' => (float) $i->unit_price,       // per selected unit
                    'quantity' => (float) $i->quantity,           // in selected unit

                    // internal
                    'qty_in_batch_unit' => (float) ($i->qty_in_batch_unit ?? $i->quantity),

                    'discount_amount' => (float) ($i->discount_amount ?? 0),
                    'discount_percent' => $i->discount_percent !== null ? (float) $i->discount_percent : null,
                    'discount_label' => $i->discount_label,

                    'total_price' => (float) $i->total_price,
                    'image' => $i->image ? asset($i->image->image_path) : null,

                    'is_gift' => (bool) $i->is_gift,
                    'gift_source' => $i->gift_source,
                    'gift_source_id' => $i->gift_source_id,
                    'parent_cart_item_id' => $i->parent_cart_item_id,
                ];
            })->values()->all(),
        ];
    }

    private function within(?float $min, ?float $max, float $qty): bool
    {
        if ($min !== null && $qty < $min) return false;
        if ($max !== null && $qty > $max) return false;
        return true;
    }

    private function rangeHint(array $calc): string
    {
        $t = str_replace('_', ' ', (string) ($calc['required_type'] ?? ''));
        $min = $calc['required_min'];
        $max = $calc['required_max'];

        if ($min !== null && $max !== null) return "Need Min {$min} & Max {$max} for {$t}.";
        if ($min !== null) return "Need Min {$min} for {$t}.";
        if ($max !== null) return "Need Max {$max} for {$t}.";
        return "Quantity condition required for {$t}.";
    }

    /**
     * ✅ Pricing rules happen in BATCH UNIT qty
     * returns TOTAL in money for batch qty
     */
    private function computeLineBatch(ProductBatch $batch, float $qtyBatch, string $type): array
    {
        $original = (float) ($batch->original_sell_price ?? 0);
        $sell     = (float) ($batch->sell_price ?? 0);
        $whole    = (float) ($batch->whole_sell_price ?? 0);
        $cust     = (float) ($batch->customer_whole_price ?? 0);

        $wholeMin = $batch->whole_sell_min_qty !== null ? (float) $batch->whole_sell_min_qty : null;
        $wholeMax = $batch->whole_sell_max_qty !== null ? (float) $batch->whole_sell_max_qty : null;

        $custMin  = $batch->customer_whole_min_qty !== null ? (float) $batch->customer_whole_min_qty : null;
        $custMax  = $batch->customer_whole_max_qty !== null ? (float) $batch->customer_whole_max_qty : null;

        $discountNum = $batch->discounted_price !== null ? (float) $batch->discounted_price : null;
        $discountPct = $batch->discount_percentage !== null ? (float) $batch->discount_percentage : null;

        $unitBatchPrice = 0.0;
        $discountAmount = 0.0;
        $discountPercent = null;
        $label = null;

        $eligible = true;
        $hint = null;
        $requiredMin = null;
        $requiredMax = null;

        if ($type === 'retail') {
            $unitBatchPrice = $sell;

            if ($discountNum !== null && $discountNum > 0) {
                // discounted_price here assumed "per batch unit discount amount" in your current design
                $discountAmount = $qtyBatch * $discountNum;
                $label = 'Batch Discount';
            } elseif ($discountPct !== null && $discountPct > 0) {
                $discountPercent = $discountPct;
                $label = 'Batch % Discount';
            }
        } elseif ($type === 'whole') {
            $requiredMin = $wholeMin;
            $requiredMax = $wholeMax;

            if ($whole > 0 && $this->within($wholeMin, $wholeMax, $qtyBatch)) {
                $unitBatchPrice = $whole;
                $discountAmount = max(0, ($original - $whole) * $qtyBatch);
                $label = 'Wholesale Benefit';
            } else {
                $eligible = false;
                $hint = 'Wholesale quantity condition not met';
                $unitBatchPrice = $whole > 0 ? $whole : $sell;
            }
        } elseif ($type === 'customer_whole') {
            $requiredMin = $custMin;
            $requiredMax = $custMax;

            if ($cust > 0 && $this->within($custMin, $custMax, $qtyBatch)) {
                $unitBatchPrice = $cust;
                $discountAmount = max(0, ($original - $cust) * $qtyBatch);
                $label = 'Customer Benefit';
            } else {
                $eligible = false;
                $hint = 'Customer quantity condition not met';
                $unitBatchPrice = $cust > 0 ? $cust : $sell;
            }
        }

        // ✅ Total based on batch qty
        $total = $unitBatchPrice * $qtyBatch;

        // If percent discount, apply to total (keep your existing style)
        if ($discountPercent !== null && $discountPercent > 0) {
            $discountAmount = ($total * ($discountPercent / 100));
            $total = max(0, $total - $discountAmount);
        } else {
            // fixed discount subtract
            $total = max(0, $total - $discountAmount);
        }

        return [
            'eligible' => $eligible,
            'unit_price_batch' => $unitBatchPrice,
            'discount_amount' => (float) $discountAmount,
            'discount_percent' => $discountPercent,
            'discount_label' => $label,
            'total_price' => (float) $total,
            'hint' => $hint,
            'required_min' => $requiredMin,
            'required_max' => $requiredMax,
            'required_type' => $type,
        ];
    }

    private function cartHasColumn(Cart $cart, string $column): bool
    {
        return array_key_exists($column, $cart->getAttributes());
    }

    /**
     * ✅ Reserved qty in cart for a batch (paid + gift) for this location.
     * Uses qty_in_batch_unit if present, else fallback to quantity (legacy).
     */
    private function reservedQtyForBatch(int $cartId, int $batchId, int $locationId, ?int $excludeItemId = null): float
    {
        $q = CartItem::where('cart_id', $cartId)
            ->where('product_batch_id', $batchId);

        if ($excludeItemId) $q->where('id', '!=', $excludeItemId);

        if (Schema::hasColumn('cart_items', 'location_id')) {
            $q->where('location_id', $locationId);
        }

        // ✅ prefer qty_in_batch_unit
        if (Schema::hasColumn('cart_items', 'qty_in_batch_unit')) {
            return (float) $q->sum('qty_in_batch_unit');
        }

        return (float) $q->sum('quantity');
    }

    /**
     * ✅ Ensures stock is available from batch_stocks for location,
     * considering all reserved in current cart.
     *
     * $deltaQtyBatch is always in BATCH UNIT.
     */
    private function assertBatchStockAvailable(int $batchId, int $cartId, int $locationId, float $deltaQtyBatch, ?int $excludeItemId = null): void
    {
        $stock = BatchStock::query()
            ->where('product_batch_id', $batchId)
            ->where('location_id', $locationId)
            ->lockForUpdate()
            ->first();

        $onHand = $stock ? (float) $stock->on_hand : 0.0;

        $reserved = $this->reservedQtyForBatch($cartId, $batchId, $locationId, $excludeItemId);
        $needTotal = $reserved + $deltaQtyBatch;

        if ($needTotal > $onHand) {
            throw new \RuntimeException("OUT_OF_STOCK");
        }
    }

    /**
     * Optional: keep product_batches.quantity = sum(batch_stocks.on_hand)
     * so old screens still show correct stock.
     */
    private function syncLegacyBatchQuantity(int $batchId): void
    {
        $sum = (float) BatchStock::where('product_batch_id', $batchId)->sum('on_hand');

        ProductBatch::whereKey($batchId)->update([
            'quantity' => $sum
        ]);
    }

    /* ===========================
       ✅ UNIT CONVERSION (minimal, safe)
       Stock remains in BATCH UNIT.
    =========================== */

    private function normalizeSaleUnit(?string $saleUnit, string $batchUnit): string
    {
        $batchUnit = strtolower(trim($batchUnit ?: 'pcs'));
        $saleUnit  = strtolower(trim((string)($saleUnit ?: $batchUnit)));

        $allowed = $this->allowedUnitsForBatch($batchUnit);

        // if user unit not allowed, fallback to batch unit (safe)
        if (!in_array($saleUnit, $allowed, true)) {
            return $batchUnit;
        }

        return $saleUnit;
    }


    private function allowedUnitsForBatch(string $batchUnit): array
    {
        $u = strtolower(trim($batchUnit ?: 'pcs'));

        return match ($u) {
            'kg', 'g'  => ['kg', 'g'],
            'l', 'ml'  => ['l', 'ml'],
            'pcs', 'dozen', 'box' => ['pcs', 'dozen', 'box'],
            default    => ['pcs'],
        };
    }


    /**
     * Convert sale qty (saleUnit) -> batch unit qty
     * returns qty in batch unit (used for stock + pricing rules)
     */
    private const PCS_PER_DOZEN = 12;
    private const PCS_PER_BOX   = 1; // change if 1 box = 24 pcs etc

    private function toBatchQty(float $saleQty, string $saleUnit, string $batchUnit): float
    {
        $saleUnit  = strtolower(trim($saleUnit ?: 'pcs'));
        $batchUnit = strtolower(trim($batchUnit ?: 'pcs'));

        if ($saleQty <= 0) return 0.0;

        if ($saleUnit === $batchUnit) return $saleQty;

        // pcs family conversions
        $pcsUnits = ['pcs', 'dozen', 'box'];
        if (in_array($saleUnit, $pcsUnits, true) && in_array($batchUnit, $pcsUnits, true)) {

            // sale -> pcs
            $pcsQty = match ($saleUnit) {
                'pcs'   => $saleQty,
                'dozen' => $saleQty * self::PCS_PER_DOZEN,
                'box'   => $saleQty * self::PCS_PER_BOX,
            };

            // pcs -> batch
            return match ($batchUnit) {
                'pcs'   => $pcsQty,
                'dozen' => $pcsQty / self::PCS_PER_DOZEN,
                'box'   => $pcsQty / self::PCS_PER_BOX,
            };
        }

        // kg<->g
        if ($batchUnit === 'kg' && $saleUnit === 'g')  return $saleQty / 1000.0;
        if ($batchUnit === 'g'  && $saleUnit === 'kg') return $saleQty * 1000.0;

        // l<->ml
        if ($batchUnit === 'l'  && $saleUnit === 'ml') return $saleQty / 1000.0;
        if ($batchUnit === 'ml' && $saleUnit === 'l')  return $saleQty * 1000.0;

        return 0.0;
    }
}
