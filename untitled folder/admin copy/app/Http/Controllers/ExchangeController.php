<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExchangeRequest;
use App\Models\Exchange;
use App\Models\ExchangeLine;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\StockTransactionLine;
use App\Services\Inventory\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExchangeController extends Controller
{
    public function create()
    {
        $orders = Order::query()
            ->select(['id', 'order_no', 'customer_id', 'created_at', 'status'])
            ->latest('id')
            ->limit(20)
            ->get();

        $locations = \App\Models\Location::query()
            ->where('is_active', true)
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();

        return view('exchanges.create', compact('orders', 'locations'));
    }

    /**
     * Exchange = RETURN (IN) + ISSUE (OUT) + price diff
     * Also updates order items + totals after successful POSTED.
     */
    public function store(StoreExchangeRequest $request, InventoryService $inventory)
    {
        $data = $request->validated();
        $idemKey = $request->header('X-Idempotency-Key') ?: (string) Str::uuid();

        // ✅ idempotent check (outside transaction)
        $existing = Exchange::query()->where('idempotency_key', $idemKey)->first();
        if ($existing) {
            return redirect()->back()->with('ok', "Already processed: {$existing->exchange_no}");
        }

        return DB::transaction(function () use ($data, $idemKey, $inventory) {

            // ✅ Lock order row to prevent concurrent exchanges corrupting totals
            /** @var Order $order */
            $order = Order::query()
                ->whereKey($data['order_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $order->load('items:id,order_id,product_id,product_batch_id,product_name,barcode,price_type,unit_price,quantity,discount_amount,total_price,returned_qty,returned_amount,note');

            // Create exchange record
            $exchange = Exchange::create([
                'exchange_no' => 'EXC-' . now()->format('YmdHis') . '-' . random_int(100, 999),
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'location_id' => $data['location_id'],
                'status' => 'DRAFT',
                'price_difference' => 0,
                'idempotency_key' => $idemKey,
                'note' => $data['note'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // =========================
            // A) VALIDATE & SAVE LINES
            // =========================
            $returnTotal = 0.0;
            $issueTotal  = 0.0;

            // Preload return order_item_ids
            $returnItemIds = collect($data['return_lines'])->pluck('order_item_id')->unique()->values();
            $orderItemsById = OrderItem::query()
                ->where('order_id', $order->id)
                ->whereIn('id', $returnItemIds)
                ->lockForUpdate() // ✅ lock item rows
                ->get()
                ->keyBy('id');

            // already returned via Return module
            $alreadyReturnedMap = DB::table('return_items as ri')
                ->join('returns as r', 'r.id', '=', 'ri.return_id')
                ->where('r.order_id', $order->id)
                ->groupBy('ri.order_item_id')
                ->pluck(DB::raw('SUM(ri.qty)'), 'ri.order_item_id'); // [order_item_id => sum(qty)]

            // already exchanged RETURN qty (posted only)
            $alreadyExchangedMap = DB::table('exchange_lines as el')
                ->join('exchanges as e', 'e.id', '=', 'el.exchange_id')
                ->where('e.order_id', $order->id)
                ->where('e.status', 'POSTED')
                ->where('el.mode', 'RETURN')
                ->groupBy('el.order_item_id')
                ->pluck(DB::raw('SUM(el.qty)'), 'el.order_item_id');

            // ---- RETURN lines
            foreach ($data['return_lines'] as $rl) {

                $oi = $orderItemsById->get((int)$rl['order_item_id']);
                if (!$oi) {
                    throw new \RuntimeException("Invalid order_item_id={$rl['order_item_id']} for this order.");
                }

                $sold = (float)($oi->quantity ?? 0);
                $alreadyReturned = (float)($alreadyReturnedMap[$oi->id] ?? 0);
                $alreadyExchanged = (float)($alreadyExchangedMap[$oi->id] ?? 0);

                $maxReturnable = max(0, $sold - $alreadyReturned - $alreadyExchanged);

                $qty = (float)$rl['qty'];
                if ($qty <= 0) throw new \RuntimeException("Return qty must be > 0");
                if ($qty > $maxReturnable) {
                    throw new \RuntimeException("Return qty exceeds allowed for order_item_id={$oi->id}. Allowed={$maxReturnable}");
                }

                ExchangeLine::create([
                    'exchange_id' => $exchange->id,
                    'mode' => 'RETURN',
                    'order_item_id' => $oi->id,
                    'product_id' => (int)$rl['product_id'],
                    'product_batch_id' => (int)$rl['product_batch_id'],
                    'qty' => $qty,
                    'unit_price' => (float)$rl['unit_price'],
                    'meta' => ['reason' => 'exchange_return'],
                ]);

                $returnTotal += $qty * (float)$rl['unit_price'];
            }

            // ---- ISSUE lines
            foreach ($data['issue_lines'] as $il) {
                $qty = (float)$il['qty'];
                if ($qty <= 0) throw new \RuntimeException("Issue qty must be > 0");

                ExchangeLine::create([
                    'exchange_id' => $exchange->id,
                    'mode' => 'ISSUE',
                    'order_item_id' => null,
                    'product_id' => (int)$il['product_id'],
                    'product_batch_id' => (int)$il['product_batch_id'],
                    'qty' => $qty,
                    'unit_price' => (float)$il['unit_price'],
                    'meta' => ['reason' => 'exchange_issue'],
                ]);

                $issueTotal += $qty * (float)$il['unit_price'];
            }

            $diff = $issueTotal - $returnTotal;
            $exchange->price_difference = $diff;
            $exchange->save();

            // =========================
            // B) STOCK MOVEMENTS
            // =========================
            $txIn = StockTransaction::create([
                'type' => 'REPLACEMENT_IN',
                'status' => 'DRAFT',
                'to_location_id' => $exchange->location_id,
                'ref_type' => 'exchange',
                'ref_id' => $exchange->id,
                'note' => "Exchange return in {$exchange->exchange_no}",
                'created_by' => auth()->id(),
            ]);

            $returnLines = $exchange->lines()->where('mode', 'RETURN')->get();
            foreach ($returnLines as $l) {
                StockTransactionLine::create([
                    'stock_transaction_id' => $txIn->id,
                    'product_id' => $l->product_id,
                    'product_batch_id' => $l->product_batch_id,
                    'qty' => $l->qty,
                    'unit' => 'pcs',
                    'unit_price' => $l->unit_price,
                    'meta' => ['exchange_line_id' => $l->id, 'mode' => 'RETURN'],
                ]);
            }

            $txOut = StockTransaction::create([
                'type' => 'REPLACEMENT_OUT',
                'status' => 'DRAFT',
                'from_location_id' => $exchange->location_id,
                'ref_type' => 'exchange',
                'ref_id' => $exchange->id,
                'note' => "Exchange issue out {$exchange->exchange_no}",
                'created_by' => auth()->id(),
            ]);

            $issueLines = $exchange->lines()->where('mode', 'ISSUE')->get();
            foreach ($issueLines as $l) {
                StockTransactionLine::create([
                    'stock_transaction_id' => $txOut->id,
                    'product_id' => $l->product_id,
                    'product_batch_id' => $l->product_batch_id,
                    'qty' => $l->qty,
                    'unit' => 'pcs',
                    'unit_price' => $l->unit_price,
                    'meta' => ['exchange_line_id' => $l->id, 'mode' => 'ISSUE'],
                ]);
            }

            // ✅ must post stock before finalizing exchange
            $inventory->post($txIn);
            $inventory->post($txOut);

            // =========================
            // C) UPDATE ORDER DATA
            // =========================
            $this->exch_applyExchangeToOrder($exchange, $order);

            // Finalize exchange
            $exchange->status = 'POSTED';
            $exchange->save();

            return redirect()->back()->with('ok', "Exchange posted: {$exchange->exchange_no}, diff={$diff}");
        });
    }

    /**
     * Apply exchange to order:
     * - RETURN: increment returned_qty / returned_amount on original item
     * - ISSUE: add new OrderItem rows as "exchange" items (so tracking is perfect)
     * - Update totals
     */
    private function exch_applyExchangeToOrder(Exchange $exchange, Order $order): void
    {
        // ---- A) RETURN effect: update returned_qty/returned_amount
        $returnLines = $exchange->lines()->where('mode', 'RETURN')->get();

        foreach ($returnLines as $l) {
            /** @var OrderItem $oi */
            $oi = OrderItem::query()
                ->where('id', $l->order_item_id)
                ->where('order_id', $order->id)
                ->lockForUpdate()
                ->firstOrFail();

            $sold = (float)($oi->quantity ?? 0);
            $already = (float)($oi->returned_qty ?? 0);
            $qty = (float)$l->qty;

            if ($already + $qty > $sold) {
                throw new \RuntimeException("Return qty exceeds sold qty for order_item_id={$oi->id}");
            }

            $oi->returned_qty = $already + $qty;
            $oi->returned_amount = (float)($oi->returned_amount ?? 0) + ($qty * (float)$l->unit_price);

            $note = is_array($oi->note) ? $oi->note : [];
            $note['exchange'][] = [
                'exchange_id' => $exchange->id,
                'exchange_no' => $exchange->exchange_no,
                'mode' => 'RETURN',
                'qty' => $qty,
                'unit_price' => (float)$l->unit_price,
                'at' => now()->toDateTimeString(),
            ];
            $oi->note = $note;

            $oi->save();
        }

        // ---- B) ISSUE effect: create new order items
        $issueLines = $exchange->lines()->where('mode', 'ISSUE')->get();
        if ($issueLines->isNotEmpty()) {
            $productIds = $issueLines->pluck('product_id')->unique()->values();

            $products = Product::query()
                ->whereIn('id', $productIds)
                ->get(['id', 'name', 'barcode'])
                ->keyBy('id');

            foreach ($issueLines as $l) {
                $p = $products->get($l->product_id);

                $qty = (float)$l->qty;
                $unit = (float)$l->unit_price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $l->product_id,
                    'product_batch_id' => $l->product_batch_id,
                    'product_name' => $p?->name ?? ('Product#' . $l->product_id),
                    'barcode' => $p?->barcode,
                    'price_type' => 'exchange',
                    'unit_price' => $unit,
                    'quantity' => $qty,
                    'discount_amount' => 0,
                    'total_price' => $qty * $unit,
                    'returned_qty' => 0,
                    'returned_amount' => 0,
                    'note' => [
                        'exchange_id' => $exchange->id,
                        'exchange_no' => $exchange->exchange_no,
                        'mode' => 'ISSUE',
                        'at' => now()->toDateTimeString(),
                    ],
                ]);
            }
        }

        // ---- C) recompute order totals safely
        // If you want diff logic only, keep payable_total += diff.
        // But safer: recompute subtotal/payable from items (recommended).
        $order->load('items:id,order_id,unit_price,quantity,discount_amount,total_price');

        $subtotal = (float)$order->items->sum(function ($it) {
            return (float)$it->unit_price * (float)$it->quantity;
        });

        $discount = (float)$order->items->sum(fn($it) => (float)($it->discount_amount ?? 0));

        $payable = $subtotal - $discount;

        // If you must incorporate previous “cart rewards” etc, adjust here.
        $order->subtotal = $subtotal;
        $order->discount_total = $discount;
        $order->payable_total = $payable;

        // Optional: flag
        $order->status = $order->status === 'completed' ? 'exchanged' : ($order->status ?? 'exchanged');
        $order->save();
    }

    // =======================
    // AJAX HELPERS (FAST)
    // =======================

    public function ajaxOrders(Request $request)
    {
        $q = trim((string)$request->query('q', ''));
        if ($q === '' || mb_strlen($q) < 2) return response()->json(['data' => []]);

        $rows = DB::table('orders as o')
            ->leftJoin('customers as c', 'c.id', '=', 'o.customer_id')
            ->where(function ($w) use ($q) {
                $w->where('o.order_no', 'like', "%{$q}%")
                    ->orWhere('c.phone', 'like', "%{$q}%")
                    ->orWhere('c.name', 'like', "%{$q}%");
                if (ctype_digit($q)) $w->orWhere('o.id', (int)$q);
            })
            ->orderByDesc('o.id')
            ->limit(20)
            ->select([
                'o.id',
                'o.order_no',
                'o.created_at',
                'o.status',
                DB::raw('COALESCE(c.name, "") as customer_name'),
                DB::raw('COALESCE(c.phone, "") as customer_phone'),
            ])
            ->get();

        return response()->json(['data' => $rows]);
    }

    public function ajaxOrderItems(Request $request)
    {
        $orderId = (int)$request->query('order_id', 0);
        if (!$orderId) return response()->json(['data' => []]);

        // include exchanged return qty too (posted)
        $exMap = DB::table('exchange_lines as el')
            ->join('exchanges as e', 'e.id', '=', 'el.exchange_id')
            ->where('e.order_id', $orderId)
            ->where('e.status', 'POSTED')
            ->where('el.mode', 'RETURN')
            ->groupBy('el.order_item_id')
            ->pluck(DB::raw('SUM(el.qty)'), 'el.order_item_id');

        $items = DB::table('order_items as oi')
            ->where('oi.order_id', $orderId)
            ->orderBy('oi.id')
            ->get([
                'oi.id',
                'oi.product_id',
                'oi.product_batch_id',
                'oi.product_name',
                'oi.barcode',
                'oi.price_type',
                'oi.quantity',
                'oi.returned_qty',
                'oi.unit_price',
            ])
            ->map(function ($it) use ($exMap) {
                $sold = (float)($it->quantity ?? 0);
                $returned = (float)($it->returned_qty ?? 0);
                $exRet = (float)($exMap[$it->id] ?? 0);
                $returnable = max(0, $sold - $returned - $exRet);

                return [
                    'id' => (int)$it->id,
                    'product_id' => (int)$it->product_id,
                    'product_batch_id' => (int)$it->product_batch_id,
                    'product_name' => $it->product_name,
                    'barcode' => $it->barcode,
                    'price_type' => $it->price_type,
                    'qty_sold' => $sold,
                    'qty_returned' => $returned,
                    'qty_exchanged_return' => $exRet,
                    'qty_returnable' => $returnable,
                    'unit_price' => (float)($it->unit_price ?? 0),
                ];
            });

        return response()->json(['data' => $items]);
    }

    public function ajaxBatches(Request $request)
    {
        $q = trim((string)$request->query('q', ''));
        $locationId = (int)$request->query('location_id', 0);

        if ($q === '' || mb_strlen($q) < 2) return response()->json(['data' => []]);

        $rows = DB::table('product_batches as pb')
            ->join('products as p', 'p.id', '=', 'pb.product_id')
            ->leftJoin('batch_stocks as bs', function ($j) use ($locationId) {
                $j->on('bs.product_batch_id', '=', 'pb.id');
                if ($locationId) $j->where('bs.location_id', $locationId);
            })
            ->where(function ($w) use ($q) {
                $w->where('p.name', 'like', "%{$q}%")
                    ->orWhere('p.barcode', 'like', "%{$q}%")
                    ->orWhere('pb.batch_no', 'like', "%{$q}%")
                    ->orWhere('pb.batch_sku', 'like', "%{$q}%");
                if (ctype_digit($q)) $w->orWhere('pb.id', (int)$q);
            })
            ->orderBy('p.name')
            ->limit(25)
            ->select([
                'p.id as product_id',
                'p.name as product_name',
                'p.barcode as product_barcode',
                'pb.id as product_batch_id',
                'pb.batch_no',
                'pb.batch_sku',
                DB::raw('COALESCE(bs.on_hand, 0) as on_hand'),
                DB::raw('COALESCE(bs.reserved, 0) as reserved'),
                DB::raw('(COALESCE(bs.on_hand,0) - COALESCE(bs.reserved,0)) as available'),
            ])
            ->get();

        return response()->json(['data' => $rows]);
    }

    public function ajaxAvailability(Request $request)
    {
        $batchId = (int)$request->query('product_batch_id', 0);
        $locId = (int)$request->query('location_id', 0);

        if (!$batchId || !$locId) {
            return response()->json(['on_hand' => 0, 'reserved' => 0, 'available' => 0]);
        }

        $row = DB::table('batch_stocks')
            ->where('product_batch_id', $batchId)
            ->where('location_id', $locId)
            ->first(['on_hand', 'reserved']);

        $on = (float)($row->on_hand ?? 0);
        $rs = (float)($row->reserved ?? 0);

        return response()->json(['on_hand' => $on, 'reserved' => $rs, 'available' => $on - $rs]);
    }
}
