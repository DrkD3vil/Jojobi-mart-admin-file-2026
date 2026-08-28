<?php

namespace App\Http\Controllers;

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
use App\Models\ProductReturn;
use App\Models\ReturnItem;
use App\Services\CartGiftService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class OrderController extends Controller
{
    private const POINT_RATE = 1.0;
    private const EARN_RATE  = 1.0;

    public const METHODS = [
        'offline' => ['cash', 'card', 'bank', 'cheque'],
        'online'  => ['bkash', 'nagad', 'rocket', 'upay', 'stripe', 'paypal', 'sslcommerz'],
    ];

    /**
     * Display a listing of the orders
     */
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'items.product']);

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

         // Apply advanced search filters
        $this->applySearchFilters($query, $request);


        $orders = $query->latest()->paginate(10);

        $stats = $this->getOrderStatistics();
        $trashedCount = Order::onlyTrashed()->count();





            return view('orders.index', [
            'orders' => $orders,
            'trashedCount' => $trashedCount,
            'stats' => $stats,
            'title' => 'All Orders',
            'filters' => $request->all()
        ]);

    }


     /**
     * Filter orders by status
     */
    public function filterByStatus(Request $request, $status)
    {
        $query = Order::with(['customer', 'items'])
            ->where('status', $status);

        // Apply additional search filters
        $this->applySearchFilters($query, $request);

        $query->orderBy('created_at', 'desc');

        $orders = $query->paginate(15);
        $stats = $this->getOrderStatistics();

        return view('orders.index', [
            'orders' => $orders,
            'stats' => $stats,
            'status' => $status,
            'title' => ucfirst($status) . ' Orders',
            'filters' => $request->all()
        ]);
    }

    /**
     * AJAX endpoint for advanced search
     */
    public function ajaxSearch(Request $request)
    {
        $query = Order::with(['customer', 'items']);

        // Apply all search filters
        $this->applySearchFilters($query, $request);

        // Apply sorting
        $sortField = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $orders = $query->paginate(15);

        // Build response with HTML
        return response()->json([
            'rows' => $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_no' => $order->order_no,
                    'customer_name' => $order->customer?->name ?? 'Guest',
                    'customer_phone' => $order->customer?->phone ?? 'N/A',
                    'payable_total' => $order->payable_total,
                    'status' => $order->status,
                    'created_at' => $order->created_at->format('M d, Y H:i A'),
                    'created_at_formatted' => $order->created_at->format('M d, Y'),
                    'show_url' => route('orders.show', $order),
                    'edit_url' => route('orders.edit', $order),
                    'can_edit' => in_array($order->status, ['pending', 'unpaid']),
                    'can_split' => $order->canSplit(),
                    'is_split_parent' => $order->isSplitParent(),
                    'split_history_url' => route('orders.split.history', $order),
                ];
            }),
            'pagination_html' => $orders->links('vendor.pagination.custom')->render(),
            'total' => $orders->total(),
            'count_on_page' => $orders->count(),
            'first_item' => $orders->firstItem(),
            'last_item' => $orders->lastItem(),
            'current_page' => $orders->currentPage(),
            'last_page' => $orders->lastPage(),
        ]);
    }

    /**
     * Apply all search filters to the query
     */
    private function applySearchFilters($query, Request $request)
    {
        // Search by term (order number, customer name, phone)
        if ($request->filled('q')) {
            $searchTerm = $request->get('q');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('order_no', 'LIKE', "%{$searchTerm}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($searchTerm) {
                        $customerQuery->where('name', 'LIKE', "%{$searchTerm}%")
                            ->orWhere('phone', 'LIKE', "%{$searchTerm}%");
                    })
                    ->orWhere('id', '=', $searchTerm);
            });
        }

        // Filter by total amount range
        if ($request->filled('min_total')) {
            $query->where('payable_total', '>=', floatval($request->get('min_total')));
        }

        if ($request->filled('max_total')) {
            $query->where('payable_total', '<=', floatval($request->get('max_total')));
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        // Filter by status (multiple)
        if ($request->filled('statuses') && is_array($request->get('statuses'))) {
            $query->whereIn('status', $request->get('statuses'));
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->get('payment_method'));
        }

        // Filter by customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->get('customer_id'));
        }
    }

    /**
     * Show detailed order information
     */
    public function show(Order $order)
    {
        if ($order->trashed()) {
            return redirect()->route('orders.trash')
                ->with('warning', 'This order is in the trash and cannot be viewed.');
        }

        $order->load([
            'customer:id,name,phone,email,address',
            'items:id,order_id,product_id,product_batch_id,product_name,barcode,price_type,unit_price,quantity,discount_amount,total_price,returned_qty,returned_amount,note',
            'location:id,name'
        ]);

        $exchangeReturn = DB::table('exchange_lines as el')
            ->join('exchanges as e', 'e.id', '=', 'el.exchange_id')
            ->where('e.order_id', $order->id)
            ->where('e.status', 'POSTED')
            ->where('el.mode', 'RETURN')
            ->selectRaw('el.order_item_id, SUM(el.qty) as qty')
            ->groupBy('el.order_item_id')
            ->pluck('qty', 'order_item_id');

        $exchangeIssue = DB::table('exchange_lines as el')
            ->join('exchanges as e', 'e.id', '=', 'el.exchange_id')
            ->where('e.order_id', $order->id)
            ->where('e.status', 'POSTED')
            ->where('el.mode', 'ISSUE')
            ->orderBy('el.id')
            ->get([
                'el.product_id',
                'el.product_batch_id',
                'el.qty',
                'el.unit_price',
            ]);

        $timeline = $this->getOrderTimeline($order);
        $payments = $order->payments()->latest()->get();

        return view('orders.show', compact('order', 'exchangeReturn', 'exchangeIssue', 'timeline', 'payments'));
    }

    /**
     * Edit order - Load cart data for editing
     */
    public function edit(Order $order)
    {
        if (!in_array($order->status, ['pending', 'unpaid'])) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Only pending or unpaid orders can be edited.');
        }

        $order->load([
            'items.batch',
            'items.product.images',
            'customer',
            'location'
        ]);

        $locations = Location::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $customer = $order->customer;

        return view('orders.edit', compact('order', 'locations', 'customer'));
    }

    /**
     * Update order - Process order updates from edit
     */
    public function update(Request $request, Order $order)
    {
        if (!in_array($order->status, ['pending', 'unpaid'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending or unpaid orders can be edited.'
            ], 422);
        }

        // A return/exchange can be recorded against an order without ever
        // changing its status (the wizard doesn't touch it), so a "pending"
        // order can still already have return history on one of its items.
        // Rebuilding all order items from scratch below would then try to
        // delete a row return_items still points to and crash with an FK
        // violation -- refuse up front instead, with a clear reason.
        if ($this->orderHasReturnedItems($order)) {
            return response()->json([
                'success' => false,
                'message' => 'This order already has a return/refund recorded against one of its items and can no longer be edited. Use the Return or Exchange flow for further adjustments instead.'
            ], 422);
        }

        $data = $request->validate([
            'order_discount'       => 'nullable|numeric|min:0',
            'rewards_points_used'  => 'nullable|numeric|min:0',
            'rewards_amount_used'  => 'nullable|numeric|min:0',
            'payment_note'         => 'nullable|string|max:2000',
            'payments'             => 'nullable|array|min:1',
            'payments.*.channel'   => 'required_with:payments|string|in:offline,online',
            'payments.*.method'    => 'required_with:payments|string',
            'payments.*.amount'    => 'required_with:payments|numeric|min:0.0001',
            'payments.*.trx_id'    => 'nullable|string|max:80',
            'payments.*.account_label' => 'nullable|string|max:120',
            'apply_balance_mode'   => 'nullable|string|in:auto,none',
            'location_id'          => 'nullable|exists:locations,id',
        ]);

        $locationId = $data['location_id'] ?? $order->location_id ?? 1;

        return DB::transaction(function () use ($request, $order, $data, $locationId) {

            $cart = $this->getOrCreateCartFromOrder($order);
            $this->syncOrderToCart($order, $cart, $locationId);

            if ($cart->items->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot save an order with no items. Add at least one item, or cancel the order instead.'
                ], 422);
            }

            $customer = null;
            if ($order->customer_id) {
                $customer = Customer::whereKey($order->customer_id)->lockForUpdate()->first();
            }

            $this->recalcCart($cart);
            $cartTotal = (float) $cart->total;

            $orderDiscount = max(0, (float) ($data['order_discount'] ?? 0));
            if ($orderDiscount > $cartTotal) $orderDiscount = $cartTotal;

            $usedPoints = max(0, (float) ($data['rewards_points_used'] ?? 0));
            if (!$customer) $usedPoints = 0;

            $rewardAmountReq = array_key_exists('rewards_amount_used', $data)
                ? max(0, (float) ($data['rewards_amount_used'] ?? 0))
                : ($usedPoints * self::POINT_RATE);

            $rewardAmount = 0.0;

            if ($customer && $usedPoints > 0) {
                if ((float) $customer->reward_points < $usedPoints) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Not enough reward points'
                    ], 422);
                }

                $maxAmount = $usedPoints * self::POINT_RATE;
                $rewardAmount = min($rewardAmountReq, $maxAmount);

                $customer->reward_points = (float) $customer->reward_points - $usedPoints;
                $customer->save();

                CustomerRewardLedger::create([
                    'customer_id' => $customer->id,
                    'action' => 'redeem',
                    'direction' => 'subtract',
                    'points' => $usedPoints,
                    'ref_type' => 'order',
                    'ref_id' => $order->id,
                    'channel' => 'pos',
                    'terminal_id' => null,
                    'created_by' => auth()->id(),
                    'idempotency_key' => null,
                    'note' => 'Redeemed on order update',
                ]);
            }

            $discountTotal = $rewardAmount + $orderDiscount;
            if ($discountTotal > $cartTotal) $discountTotal = $cartTotal;

            $orderPayable = max(0, $cartTotal - $discountTotal);

            $applyMode = $data['apply_balance_mode'] ?? 'auto';
            if (!$customer) $applyMode = 'none';

            $oldDue = $customer ? max(0, (float) $customer->due_balance) : 0.0;
            $oldAdvance = $customer ? max(0, (float) $customer->advance_balance) : 0.0;

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

            // Update order
            $order->subtotal = $cartTotal;
            $order->discount_total = $discountTotal;
            $order->payable_total = $netCollect;
            $order->rewards_points_used = $usedPoints;
            $order->rewards_amount_used = $rewardAmount;
            $order->payment_note = $data['payment_note'] ?? null;
            $order->location_id = $locationId;

            // Delete existing order items
            OrderItem::where('order_id', $order->id)->delete();

            // Create new order items from cart
            foreach ($cart->items as $ci) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $ci->product_id,
                    'product_batch_id' => $ci->product_batch_id,
                    'product_name' => $ci->product?->name,
                    'barcode' => $ci->product?->barcode,
                    'price_type' => $ci->price_type,
                    'unit_price' => (float) $ci->unit_price,
                    'quantity' => (float) $ci->quantity,
                    'unit' => $ci->unit,
                    'discount_amount' => (float) ($ci->discount_amount ?? 0),
                    'total_price' => (float) $ci->total_price,
                    'is_gift' => (bool) $ci->is_gift,
                    'gift_source' => $ci->gift_source,
                    'gift_source_id' => $ci->gift_source_id,
                ]);
            }

            // Delete existing payments
            Payment::where('order_id', $order->id)->delete();

            // Create payments
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

            // Clear cart
            CartItem::where('cart_id', $cart->id)->delete();
            $cart->total = 0;
            $cart->save();

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully.',
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
                ],
                'invoice_url' => route('invoice.show', $order->id),
            ]);
        });
    }

    /**
     * Get order data as JSON (AJAX)
     */
    public function getOrderData(Order $order)
    {
        $order->load([
            'customer:id,name,phone,email,address,due_balance,advance_balance,reward_points',
            'location:id,name'
        ]);

        return response()->json([
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
            'payment_note' => $order->payment_note,
            'rewards_points_used' => (float) ($order->rewards_points_used ?? 0),
            'rewards_amount_used' => (float) ($order->rewards_amount_used ?? 0),
            'location_id' => $order->location_id,
            'customer' => $order->customer ? [
                'id' => $order->customer->id,
                'name' => $order->customer->name,
                'phone' => $order->customer->phone,
                'email' => $order->customer->email,
                'due_balance' => (float) $order->customer->due_balance,
                'advance_balance' => (float) $order->customer->advance_balance,
                'reward_points' => (float) $order->customer->reward_points,
            ] : null,
            'location' => $order->location ? [
                'id' => $order->location->id,
                'name' => $order->location->name,
            ] : null,
        ]);
    }

    /**
     * Get order items as JSON (AJAX)
     */
    public function getItems(Order $order)
    {
        $items = $order->items()->with(['batch', 'product.images'])->get();

        return response()->json($items->map(function ($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_batch_id' => $item->product_batch_id,
                'product_name' => $item->product_name ?? $item->product?->name,
                'barcode' => $item->barcode ?? $item->product?->barcode,
                'batch_sku' => $item->batch?->batch_sku,
                'batch_unit' => $item->batch?->unit ?? 'pcs',
                'unit' => $item->unit ?? 'pcs',
                'price_type' => $item->price_type ?? 'retail',
                'unit_price' => (float) $item->unit_price,
                'quantity' => (float) $item->quantity,
                'discount_amount' => (float) ($item->discount_amount ?? 0),
                'total_price' => (float) $item->total_price,
                'image' => $item->product?->images?->first()?->image_path,
                'is_gift' => (bool) $item->is_gift,
                'gift_source' => $item->gift_source,
            ];
        }));
    }

    /**
     * Add item to order (AJAX)
     */
    public function addItem(Request $request, Order $order)
    {
        if (!in_array($order->status, ['pending', 'unpaid'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending or unpaid orders can be modified.'
            ], 422);
        }

        $data = $request->validate([
            'batch_id' => 'required|exists:product_batches,id',
            'quantity' => 'required|numeric|min:0.0001',
            'price_type' => 'nullable|string|in:retail,whole,customer_whole',
            'unit' => 'nullable|string|max:10',
        ]);

        $locationId = $request->location_id ?? $order->location_id ?? 1;

        return DB::transaction(function () use ($order, $data, $locationId) {
            $batch = ProductBatch::with(['product:id,name,barcode'])->lockForUpdate()->findOrFail($data['batch_id']);
            $saleUnit = $data['unit'] ?? ($batch->unit ?? 'pcs');
            $qtyToAdd = (float) $data['quantity'];

            try {
                $this->adjustStock($batch->id, $locationId, -$qtyToAdd);
            } catch (\RuntimeException $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            $priceType = $data['price_type'] ?? 'retail';
            $unitPrice = $this->getUnitPrice($batch, $priceType);

            // Merge into an existing non-gift line for the same batch/price-type/unit
            // instead of fragmenting the order into duplicate rows on repeated "add" clicks.
            $item = OrderItem::where('order_id', $order->id)
                ->where('product_batch_id', $batch->id)
                ->where('price_type', $priceType)
                ->where('unit', $saleUnit)
                ->where('is_gift', false)
                ->lockForUpdate()
                ->first();

            if ($item) {
                $newQuantity = (float) $item->quantity + $qtyToAdd;
                $item->update([
                    'quantity' => $newQuantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $unitPrice * $newQuantity,
                ]);
            } else {
                $item = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $batch->product_id,
                    'product_batch_id' => $batch->id,
                    'product_name' => $batch->product?->name,
                    'barcode' => $batch->product?->barcode,
                    'price_type' => $priceType,
                    'unit_price' => $unitPrice,
                    'quantity' => $qtyToAdd,
                    'unit' => $saleUnit,
                    'discount_amount' => 0,
                    'total_price' => $unitPrice * $qtyToAdd,
                    'is_gift' => false,
                    'gift_source' => null,
                ]);
            }

            $this->updateOrderTotals($order);

            return response()->json([
                'success' => true,
                'message' => 'Item added to order.',
                'item' => $item->fresh(),
            ]);
        });
    }

    /**
     * Update order item (AJAX)
     */
    public function updateItem(Request $request, Order $order)
    {
        if (!in_array($order->status, ['pending', 'unpaid'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending or unpaid orders can be modified.'
            ], 422);
        }

        $data = $request->validate([
            'item_id' => 'required|exists:order_items,id',
            'quantity' => 'required|numeric|min:0.0001',
            'price_type' => 'nullable|string|in:retail,whole,customer_whole',
            'unit' => 'nullable|string|max:10',
        ]);

        return DB::transaction(function () use ($order, $data) {
            $item = OrderItem::where('order_id', $order->id)
                ->where('id', $data['item_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $batch = ProductBatch::findOrFail($item->product_batch_id);

            $priceType = $data['price_type'] ?? $item->price_type ?? 'retail';
            $unitPrice = $this->getUnitPrice($batch, $priceType);
            $quantity = (float) $data['quantity'];
            $totalPrice = $unitPrice * $quantity;

            // Positive delta = quantity went down (restore the difference to stock),
            // negative delta = quantity went up (consume the difference from stock).
            $stockDelta = (float) $item->quantity - $quantity;
            if ($item->product_batch_id) {
                try {
                    $this->adjustStock((int) $item->product_batch_id, $order->location_id ?? 1, $stockDelta);
                } catch (\RuntimeException $e) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage()
                    ], 422);
                }
            }

            $item->update([
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'price_type' => $priceType,
                'unit' => $data['unit'] ?? $item->unit,
            ]);

            $this->updateOrderTotals($order);

            return response()->json([
                'success' => true,
                'message' => 'Item updated.',
                'item' => $item->fresh(),
            ]);
        });
    }

    /**
     * Remove item from order (AJAX)
     */
    public function removeItem(Request $request, Order $order, $itemId)
    {
        if (!in_array($order->status, ['pending', 'unpaid'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending or unpaid orders can be modified.'
            ], 422);
        }

        return DB::transaction(function () use ($order, $itemId) {
            $item = OrderItem::where('order_id', $order->id)
                ->where('id', $itemId)
                ->lockForUpdate()
                ->firstOrFail();

            if ((float) $item->returned_qty > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'This item already has a return/refund recorded against it and cannot be removed.'
                ], 422);
            }

            if ($item->product_batch_id) {
                $this->adjustStock((int) $item->product_batch_id, $order->location_id ?? 1, (float) $item->quantity);
            }

            $item->delete();

            $this->updateOrderTotals($order);

            return response()->json([
                'success' => true,
                'message' => 'Item removed.',
            ]);
        });
    }

    /**
     * Clear all items from order (AJAX)
     */
    public function clearItems(Request $request, Order $order)
    {
        if (!in_array($order->status, ['pending', 'unpaid'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending or unpaid orders can be modified.'
            ], 422);
        }

        if ($this->orderHasReturnedItems($order)) {
            return response()->json([
                'success' => false,
                'message' => 'This order already has a return/refund recorded against one of its items and cannot be cleared.'
            ], 422);
        }

        return DB::transaction(function () use ($order) {
            $locationId = $order->location_id ?? 1;
            $items = OrderItem::where('order_id', $order->id)->lockForUpdate()->get();
            foreach ($items as $item) {
                if ($item->product_batch_id) {
                    $this->adjustStock((int) $item->product_batch_id, $locationId, (float) $item->quantity);
                }
            }

            OrderItem::where('order_id', $order->id)->delete();

            $order->update([
                'subtotal' => 0,
                'discount_total' => 0,
                'payable_total' => 0,
                'paid_total' => 0,
                'due_total' => 0,
                'change_total' => 0,
                'payment_status' => 'unpaid',
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'All items cleared.',
            ]);
        });
    }

    /**
     * Add manual gift to order (AJAX)
     */
    public function addManualGift(Request $request, Order $order)
    {
        if (!in_array($order->status, ['pending', 'unpaid'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending or unpaid orders can be modified.'
            ], 422);
        }

        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|numeric|min:0.0001',
            'location_id' => 'nullable|exists:locations,id',
        ]);

        $locationId = $data['location_id'] ?? $order->location_id ?? 1;

        return DB::transaction(function () use ($order, $data, $locationId) {
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
                return response()->json([
                    'success' => false,
                    'message' => 'Gift product is out of stock in this location.'
                ], 422);
            }

            $qty = (float) ($data['quantity'] ?? 1);

            try {
                $this->adjustStock((int) $giftBatch->id, $locationId, -$qty);
            } catch (\RuntimeException $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            $payload = [
                'order_id' => $order->id,
                'product_id' => (int) $data['product_id'],
                'product_batch_id' => (int) $giftBatch->id,
                'product_name' => $giftBatch->product?->name ?? 'Gift',
                'barcode' => $giftBatch->product?->barcode,
                'price_type' => 'gift',
                'unit_price' => 0,
                'quantity' => $qty,
                'unit' => $giftBatch->unit ?? 'pcs',
                'discount_amount' => 0,
                'total_price' => 0,
                'is_gift' => true,
                'gift_source' => 'manual',
            ];

            OrderItem::create($payload);
            $this->updateOrderTotals($order);

            return response()->json([
                'success' => true,
                'message' => 'Gift added to order.',
            ]);
        });
    }

    /**
     * Remove manual gift from order (AJAX)
     */
    public function removeManualGift(Request $request, Order $order, $itemId)
    {
        if (!in_array($order->status, ['pending', 'unpaid'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending or unpaid orders can be modified.'
            ], 422);
        }

        return DB::transaction(function () use ($order, $itemId) {
            $item = OrderItem::where('order_id', $order->id)
                ->where('id', $itemId)
                ->where('is_gift', true)
                ->where('gift_source', 'manual')
                ->lockForUpdate()
                ->firstOrFail();

            if ((float) $item->returned_qty > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'This gift item already has a return/refund recorded against it and cannot be removed.'
                ], 422);
            }

            if ($item->product_batch_id) {
                $this->adjustStock((int) $item->product_batch_id, $order->location_id ?? 1, (float) $item->quantity);
            }

            $item->delete();
            $this->updateOrderTotals($order);

            return response()->json([
                'success' => true,
                'message' => 'Gift removed.',
            ]);
        });
    }

    /* ================================================================
       TRASH ROUTES
    ================================================================ */

    public function trash(Request $request, Order $order)
    {
        try {
            $request->validate([
                'reason' => 'nullable|string|max:500'
            ]);

            DB::beginTransaction();

            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            // Trashing a live order releases the stock it was holding, since a
            // trashed order is invisible to normal reporting/inventory views.
            // Orders already cancelled/refunded already had their stock restored,
            // so skip them to avoid crediting stock back twice.
            if (!in_array($order->status, ['cancelled', 'refunded'])) {
                $this->restoreStockForOrderItems($order);
            }

            $order->update([
                'deleted_by' => Auth::id(),
                'delete_reason' => $request->reason ?? 'No reason provided',
            ]);
            $order->delete();
            $order->recordTimeline('deleted', 'Order Moved to Trash', $request->reason ?? 'No reason provided', null, null, 'trash-2');

            Log::info('Order moved to trash', [
                'order_id' => $order->id,
                'deleted_by' => Auth::id(),
                'reason' => $request->reason
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Order moved to trash successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to trash order', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to move order to trash: ' . $e->getMessage());
        }
    }

    public function trashIndex(Request $request)
    {
        Log::info('Trash index accessed', ['url' => $request->fullUrl()]);
        $query = Order::onlyTrashed()->with(['customer', 'deletedBy']);

        if ($q = $request->get('q')) {
            $query->where(function ($qry) use ($q) {
                $qry->where('order_no', 'LIKE', "%{$q}%")
                    ->orWhere('id', 'LIKE', "%{$q}%")
                    ->orWhereHas('customer', function ($customer) use ($q) {
                        $customer->where('name', 'LIKE', "%{$q}%")
                            ->orWhere('phone', 'LIKE', "%{$q}%");
                    });
            });
        }

        $trashedOrders = $query->latest('deleted_at')->paginate(20);
        $stats = $this->getOrderStatistics();
        $trashedCount = Order::onlyTrashed()->count();

        return view('orders.trash', compact('trashedOrders', 'stats', 'trashedCount'));
    }

    public function restore($id)
    {
        try {
            DB::beginTransaction();

            $order = Order::onlyTrashed()->lockForUpdate()->findOrFail($id);

            // Mirror trash(): re-claim the stock that was released when this
            // order was trashed, unless it was already cancelled/refunded
            // (whose stock was already restored and should stay that way).
            if (!in_array($order->status, ['cancelled', 'refunded'])) {
                $this->reserveStockForOrderItems($order);
            }

            $order->restore();
            $order->restored_at = now();
            $order->save();
            $order->recordTimeline('restored', 'Order Restored from Trash', null, null, null, 'undo-2');

            Log::info('Order restored from trash', [
                'order_id' => $order->id,
                'restored_by' => Auth::id()
            ]);

            DB::commit();

            return redirect()->route('orders.trash')
                ->with('success', 'Order #' . ($order->order_no ?? $order->id) . ' restored successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to restore order', [
                'order_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to restore order: ' . $e->getMessage());
        }
    }

    public function restoreMultiple(Request $request)
    {
        try {
            $request->validate([
                'order_ids' => 'required|array',
                'order_ids.*' => 'exists:orders,id'
            ]);

            DB::beginTransaction();

            $restoredCount = 0;
            foreach ($request->order_ids as $id) {
                $order = Order::onlyTrashed()->lockForUpdate()->find($id);
                if ($order) {
                    if (!in_array($order->status, ['cancelled', 'refunded'])) {
                        $this->reserveStockForOrderItems($order);
                    }
                    $order->restore();
                    $order->restored_at = now();
                    $order->save();
                    $order->recordTimeline('restored', 'Order Restored from Trash', null, null, null, 'undo-2');
                    $restoredCount++;
                }
            }

            Log::info('Multiple orders restored', [
                'count' => $restoredCount,
                'restored_by' => Auth::id()
            ]);

            DB::commit();

            return redirect()->route('orders.trash')
                ->with('success', $restoredCount . ' orders restored successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to restore multiple orders', [
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to restore orders: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            DB::beginTransaction();

            $order = Order::onlyTrashed()->findOrFail($id);
            $orderNumber = $order->order_no ?? $order->id;

            $order->items()->forceDelete();
            $order->payments()->forceDelete();
            $order->forceDelete();

            Log::info('Order permanently deleted', [
                'order_id' => $id,
                'deleted_by' => Auth::id()
            ]);

            DB::commit();

            return redirect()->route('orders.trash')
                ->with('success', 'Order #' . $orderNumber . ' permanently deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to permanently delete order', [
                'order_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to delete order: ' . $e->getMessage());
        }
    }

    public function forceDeleteMultiple(Request $request)
    {
        try {
            $request->validate([
                'order_ids' => 'required|array',
                'order_ids.*' => 'exists:orders,id'
            ]);

            DB::beginTransaction();

            $deletedCount = 0;
            foreach ($request->order_ids as $id) {
                $order = Order::onlyTrashed()->find($id);
                if ($order) {
                    $order->items()->forceDelete();
                    $order->payments()->forceDelete();
                    $order->forceDelete();
                    $deletedCount++;
                }
            }

            Log::info('Multiple orders permanently deleted', [
                'count' => $deletedCount,
                'deleted_by' => Auth::id()
            ]);

            DB::commit();

            return redirect()->route('orders.trash')
                ->with('success', $deletedCount . ' orders permanently deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to permanently delete multiple orders', [
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to delete orders: ' . $e->getMessage());
        }
    }

    public function emptyTrash()
    {
        try {
            DB::beginTransaction();

            $trashedOrders = Order::onlyTrashed()->get();
            $count = $trashedOrders->count();

            foreach ($trashedOrders as $order) {
                $order->items()->forceDelete();
                $order->payments()->forceDelete();
                $order->forceDelete();
            }

            Log::info('Trash emptied', [
                'count' => $count,
                'deleted_by' => Auth::id()
            ]);

            DB::commit();

            return redirect()->route('orders.trash')
                ->with('success', 'Trash emptied successfully. ' . $count . ' orders removed.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to empty trash', [
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to empty trash: ' . $e->getMessage());
        }
    }

    /* ================================================================
       STATUS ROUTES
    ================================================================ */

    public function pending()
    {
        return $this->statusOrders('pending', 'Pending Orders');
    }

    public function processing()
    {
        return $this->statusOrders('processing', 'Processing Orders');
    }

    public function completed()
    {
        return $this->statusOrders('completed', 'Completed Orders');
    }

    public function paid()
    {
        return $this->statusOrders('paid', 'Paid Orders');
    }

    public function refunded()
    {
        return $this->statusOrders('refunded', 'Refunded Orders');
    }

    public function returned()
    {
        return $this->statusOrders('returned', 'Returned Orders');
    }

    public function cancelled()
    {
        return $this->statusOrders('cancelled', 'Cancelled Orders');
    }

    private function statusOrders($status = null, $title = 'Orders')
    {
        $query = Order::with(['customer', 'items']);

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->latest()->paginate(20);
        $stats = $this->getOrderStatistics();

        return view('orders.index', compact('orders', 'status', 'title', 'stats'));
    }

    /* ================================================================
       ORDER ACTION ROUTES
    ================================================================ */

    public function process(Order $order)
    {
        return DB::transaction(function () use ($order) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($order->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending orders can be processed.');
            }

            $order->update(['status' => 'processing']);
            $order->recordTimeline('processing', 'Order Processing', 'Order marked as processing.', 'pending', 'processing', 'loader');

            return redirect()->back()->with('success', 'Order is now processing.');
        });
    }

    /**
     * Confirmation page for cancelling an order -- shows the order summary
     * and an optional-reason form instead of cancelling on a single click.
     */
    public function cancelForm(Order $order)
    {
        if (!in_array($order->status, ['pending', 'processing'])) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Only pending or processing orders can be cancelled.');
        }

        $order->load(['customer', 'items', 'location']);

        return view('orders.cancel', compact('order'));
    }

    public function cancel(Request $request, Order $order)
    {
        $reason = trim((string) $request->input('reason', ''));

        return DB::transaction(function () use ($order, $reason) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if (!in_array($order->status, ['pending', 'processing'])) {
                return redirect()->back()->with('error', 'Only pending or processing orders can be cancelled.');
            }

            $this->restoreStockForOrderItems($order);

            Payment::where('order_id', $order->id)
                ->where('status', 'captured')
                ->update(['status' => 'void']);

            $fromStatus = $order->status;
            $this->updateOrderTotals($order, preserveStatus: true);
            $order->update(['status' => 'cancelled']);

            $description = $reason !== ''
                ? "Stock restored and any captured payments voided. Reason: {$reason}"
                : 'Stock restored and any captured payments voided.';
            $order->recordTimeline('cancelled', 'Order Cancelled', $description, $fromStatus, 'cancelled', 'circle-x');

            return redirect()->route('orders.show', $order)->with('success', 'Order cancelled and stock restored.');
        });
    }

    public function complete(Order $order)
    {
        return DB::transaction(function () use ($order) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($order->status !== 'processing') {
                return redirect()->back()->with('error', 'Only processing orders can be completed.');
            }

            $order->update(['status' => 'completed']);
            $order->recordTimeline('completed', 'Order Completed', null, 'processing', 'completed', 'badge-check');

            $this->awardOnlineEarnPoints($order);

            return redirect()->back()->with('success', 'Order completed.');
        });
    }

    /**
     * Online orders don't earn reward points at checkout time the way a POS
     * sale does in CartController::checkout() -- "paid" for an online order
     * just means the trx_id has been verified, not that the order is done,
     * so earning is deferred to here instead. Must run inside the caller's
     * already-locked-order transaction. Guarded by an existing
     * action=earn/ref_type=order/ref_id=$order->id ledger row so completing
     * (or re-completing) the same order can never double-award points --
     * completing an already-completed order is already blocked above, but
     * this keeps the guard independent of that in case this method is ever
     * called from elsewhere.
     */
    private function awardOnlineEarnPoints(Order $order): void
    {
        if ($order->channel !== 'online' || $order->payment_status !== 'paid' || !$order->customer_id) {
            return;
        }

        $alreadyEarned = CustomerRewardLedger::where('ref_type', 'order')
            ->where('ref_id', $order->id)
            ->where('action', 'earn')
            ->exists();

        if ($alreadyEarned) {
            return;
        }

        $basis = min((float) $order->paid_total, (float) $order->payable_total);
        $earnPoints = (float) floor($basis * self::EARN_RATE);

        if ($earnPoints <= 0) {
            return;
        }

        $customer = Customer::whereKey($order->customer_id)->lockForUpdate()->first();
        if (!$customer) {
            return;
        }

        $customer->reward_points = (float) $customer->reward_points + $earnPoints;
        $customer->save();

        CustomerRewardLedger::create([
            'customer_id' => $customer->id,
            'action' => 'earn',
            'direction' => 'add',
            'points' => $earnPoints,
            'ref_type' => 'order',
            'ref_id' => $order->id,
            'channel' => 'online',
            'terminal_id' => null,
            'created_by' => Auth::id(),
            'idempotency_key' => null,
            'note' => "Earned on completed online order ({$order->order_no})",
        ]);
    }

    public function refund(Order $order)
    {
        return DB::transaction(function () use ($order) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if (!in_array($order->status, ['completed', 'paid'])) {
                return redirect()->back()->with('error', 'Only completed or paid orders can be refunded.');
            }

            $fromStatus = $order->status;
            $return = $this->createReturnRecordForRefund($order);

            $this->restoreStockForOrderItems($order);

            Payment::where('order_id', $order->id)
                ->where('status', 'captured')
                ->update(['status' => 'refunded']);

            $this->updateOrderTotals($order, preserveStatus: true);
            $order->update(['status' => 'refunded']);

            $order->recordTimeline(
                'refunded',
                'Order Refunded',
                $return ? currency_bdt($return->refund_amount) . ' refunded, payment voided, and stock restored.' : 'Payment voided and stock restored.',
                $fromStatus,
                'refunded',
                'rotate-ccw'
            );

            return redirect()->back()->with('success', 'Order refunded, payment voided, and stock restored.');
        });
    }

    /**
     * Write a returns/return_items record for a full-order refund, mirroring
     * ReturnController::store()'s pattern. Without this, an order refunded
     * here (as opposed to through the return wizard) never appears in the
     * returns tables the financial dashboards read refund totals from,
     * permanently overstating revenue/profit for that order everywhere.
     * Must be called inside the caller's DB::transaction, before stock is
     * restored and payments are voided.
     */
    private function createReturnRecordForRefund(Order $order): ?ProductReturn
    {
        $items = OrderItem::where('order_id', $order->id)->lockForUpdate()->get();

        $lines = [];
        $refundTotal = 0.0;

        foreach ($items as $item) {
            // return_items.product_batch_id is a required FK -- an item with
            // no batch link can't be recorded here (same constraint the
            // return wizard operates under).
            if (!$item->product_batch_id) {
                continue;
            }

            $remainingQty = (float) $item->quantity - (float) ($item->returned_qty ?? 0);
            if ($remainingQty <= 0) {
                continue;
            }

            $unitPrice = (float) $item->unit_price;
            $refund = $remainingQty * $unitPrice;

            $lines[] = [
                'item' => $item,
                'qty' => $remainingQty,
                'unit_price' => $unitPrice,
                'refund_amount' => $refund,
            ];

            $refundTotal += $refund;
        }

        if (empty($lines)) {
            return null;
        }

        $refundMethod = Payment::where('order_id', $order->id)->value('method');

        $return = ProductReturn::create([
            'return_no' => 'RET-' . now()->format('YmdHis') . '-' . rand(100, 999),
            'order_id' => $order->id,
            'customer_id' => $order->customer_id,
            'location_id' => $order->location_id ?? 1,
            'status' => 'REFUNDED',
            'refund_method' => $refundMethod,
            'refund_amount' => $refundTotal,
            'note' => 'Auto-generated from order refund.',
            'created_by' => Auth::id(),
        ]);

        foreach ($lines as $line) {
            $item = $line['item'];

            ReturnItem::create([
                'return_id' => $return->id,
                'order_item_id' => $item->id,
                'product_id' => $item->product_id,
                'product_batch_id' => $item->product_batch_id,
                'qty' => $line['qty'],
                'unit_price' => $line['unit_price'],
                'refund_amount' => $line['refund_amount'],
                'condition' => 'GOOD',
            ]);

            $item->update([
                'returned_qty' => (float) ($item->returned_qty ?? 0) + $line['qty'],
                'returned_amount' => (float) ($item->returned_amount ?? 0) + $line['refund_amount'],
            ]);
        }

        $return->recordTimeline(
            'refunded',
            'Return Refunded',
            currency_bdt($refundTotal) . ' refunded' . ($refundMethod ? " via {$refundMethod}" : '') . " for order #{$order->order_no}.",
            null,
            'REFUNDED',
            'rotate-ccw'
        );

        return $return;
    }

    /* ================================================================
       AJAX INDEX
    ================================================================ */

    public function ajaxIndex(Request $request)
    {
        $query = Order::with(['customer', 'items']);

        if ($q = $request->get('q')) {
            $query->where(function ($qry) use ($q) {
                $qry->where('order_no', 'LIKE', "%{$q}%")
                    ->orWhere('id', 'LIKE', "%{$q}%")
                    ->orWhereHas('customer', function ($customer) use ($q) {
                        $customer->where('name', 'LIKE', "%{$q}%")
                            ->orWhere('phone', 'LIKE', "%{$q}%");
                    });
            });
        }

        if ($min = $request->get('min_total')) {
            $query->where('payable_total', '>=', (float) $min);
        }

        if ($max = $request->get('max_total')) {
            $query->where('payable_total', '<=', (float) $max);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $orders = $query->latest()->paginate(20);

        $paginationHtml = view('vendor.pagination.custom', ['paginator' => $orders])->render();

        return response()->json([
            'rows' => $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_no' => $order->order_no,
                    'customer_name' => $order->customer?->name ?? 'Guest',
                    'customer_phone' => $order->customer?->phone ?? '',
                    'payable_total' => $order->payable_total,
                    'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                    'status' => $order->status,
                    'show_url' => route('orders.show', $order),
                ];
            }),
            'pagination_html' => $paginationHtml,
            'count_on_page' => $orders->count(),
            'meta' => "Showing {$orders->firstItem()}-{$orders->lastItem()} of {$orders->total()}",
            'total' => $orders->total(),
        ]);
    }

    /* ================================================================
       HELPERS
    ================================================================ */

    /**
     * True if any of this order's items already has a return/exchange
     * recorded against it (returned_qty > 0). return_items/exchange_lines
     * both hold a plain FK to order_items.id with no cascade, so deleting
     * (or replacing) such an item throws an FK-constraint SQL error instead
     * of a clean, user-facing message -- callers should check this first.
     */
    private function orderHasReturnedItems(Order $order): bool
    {
        return OrderItem::where('order_id', $order->id)->where('returned_qty', '>', 0)->exists();
    }

    private function getOrderStatistics()
    {
        return [
            'total' => Order::withoutTrashed()->count(),
            'pending' => Order::withoutTrashed()->where('status', 'pending')->count(),
            'processing' => Order::withoutTrashed()->where('status', 'processing')->count(),
            'completed' => Order::withoutTrashed()->where('status', 'completed')->count(),
            'paid' => Order::withoutTrashed()->where('status', 'paid')->count(),
            'refunded' => Order::withoutTrashed()->where('status', 'refunded')->count(),
            'returned' => Order::withoutTrashed()->where('status', 'returned')->count(),
            'cancelled' => Order::withoutTrashed()->where('status', 'cancelled')->count(),
            'total_revenue' => Order::withoutTrashed()->whereNotIn('status', ['cancelled', 'refunded'])->sum('payable_total'),
            'trashed' => Order::onlyTrashed()->count(),
        ];
    }

    private function getOrderTimeline($order)
    {
        $rows = $order->timeline()->oldest('id')->get();

        if ($rows->isNotEmpty()) {
            return $rows->map(fn ($row) => [
                'icon' => $row->icon ?: $this->getStatusIcon($row->to_value ?? ''),
                'title' => $row->title,
                'description' => $row->description ?? '',
                'time' => $row->created_at,
                'type' => $row->to_value ?: $row->event,
            ])->all();
        }

        // Orders created before this timeline feature existed have no
        // persisted rows -- fall back to the old synthesized single-status
        // view so the page never shows an empty timeline for them.
        $timeline = [];

        $timeline[] = [
            'icon' => 'shopping-bag',
            'title' => 'Order Created',
            'description' => 'Order #' . ($order->order_no ?? $order->id) . ' was created',
            'time' => $order->created_at,
            'type' => 'created'
        ];

        if ($order->status) {
            $timeline[] = [
                'icon' => $this->getStatusIcon($order->status),
                'title' => 'Order ' . ucfirst($order->status),
                'description' => 'Order status updated to ' . ucfirst($order->status),
                'time' => $order->updated_at,
                'type' => $order->status
            ];
        }

        if ($order->trashed()) {
            $timeline[] = [
                'icon' => 'trash-2',
                'title' => 'Order Deleted',
                'description' => 'Order moved to trash by ' . ($order->deletedBy?->name ?? 'System'),
                'time' => $order->deleted_at,
                'type' => 'deleted'
            ];
        }

        return $timeline;
    }

    private function getStatusIcon($status)
    {
        $icons = [
            'pending' => 'clock',
            'processing' => 'loader',
            'completed' => 'check-circle',
            'paid' => 'credit-card',
            'refunded' => 'rotate-ccw',
            'returned' => 'undo-2',
            'cancelled' => 'x-circle'
        ];
        return $icons[$status] ?? 'circle';
    }

    private function getUnitPrice(ProductBatch $batch, string $priceType): float
    {
        return match ($priceType) {
            'whole' => (float) ($batch->whole_sell_price ?? 0),
            'customer_whole' => (float) ($batch->customer_whole_price ?? 0),
            default => (float) ($batch->sell_price ?? 0),
        };
    }

    /**
     * Adjust on-hand stock for a batch at a location by a signed delta
     * (negative = consume, positive = restore), under a row lock.
     * Must be called inside an existing DB::transaction(). Throws when a
     * negative delta would push on_hand below zero.
     */
    private function adjustStock(int $batchId, int $locationId, float $delta): void
    {
        if (abs($delta) < 0.00001) {
            return;
        }

        $stock = DB::table('batch_stocks')
            ->where('product_batch_id', $batchId)
            ->where('location_id', $locationId)
            ->lockForUpdate()
            ->first();

        if (!$stock) {
            if ($delta < 0) {
                throw new \RuntimeException('Not enough stock available.');
            }
            DB::table('batch_stocks')->insert([
                'product_batch_id' => $batchId,
                'location_id' => $locationId,
                'on_hand' => $delta,
                'reserved' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return;
        }

        $newOnHand = (float) $stock->on_hand + $delta;
        if ($newOnHand < 0) {
            throw new \RuntimeException('Not enough stock available.');
        }

        DB::table('batch_stocks')
            ->where('product_batch_id', $batchId)
            ->where('location_id', $locationId)
            ->update(['on_hand' => $newOnHand, 'updated_at' => now()]);
    }

    /**
     * Restore stock for every item on an order (used by cancel/refund/trash).
     */
    private function restoreStockForOrderItems(Order $order): void
    {
        $locationId = $order->location_id ?? 1;
        $items = OrderItem::where('order_id', $order->id)->lockForUpdate()->get();

        foreach ($items as $item) {
            if ($item->product_batch_id) {
                $this->adjustStock((int) $item->product_batch_id, $locationId, (float) $item->quantity);
            }
        }
    }

    /**
     * Re-consume stock for every item on an order (used when restoring a
     * trashed order back to an active status). Throws if any item's stock
     * is no longer available, aborting the whole restore under the caller's
     * transaction.
     */
    private function reserveStockForOrderItems(Order $order): void
    {
        $locationId = $order->location_id ?? 1;
        $items = OrderItem::where('order_id', $order->id)->lockForUpdate()->get();

        foreach ($items as $item) {
            if ($item->product_batch_id) {
                $this->adjustStock((int) $item->product_batch_id, $locationId, -(float) $item->quantity);
            }
        }
    }

    private function updateOrderTotals(Order $order, bool $preserveStatus = false): void
    {
        // Net out returned quantity (matches ReturnController::recalcOrderTotals()):
        // items still at their original quantity are unaffected (cancel()/addItem()
        // etc. only ever touch orders with no returns yet), but a refund() that has
        // just set returned_qty on every item now correctly shrinks payable_total
        // too, instead of leaving it at the pre-refund amount forever.
        $subtotal = (float) (OrderItem::where('order_id', $order->id)
            ->selectRaw('COALESCE(SUM(GREATEST(quantity - COALESCE(returned_qty, 0), 0) * unit_price), 0) as total')
            ->value('total') ?? 0);
        $discountTotal = (float) ($order->discount_total ?? 0);
        $payableTotal = max(0, $subtotal - $discountTotal);

        $paidTotal = (float) Payment::where('order_id', $order->id)
            ->where('status', 'captured')
            ->sum('amount');

        $dueTotal = max(0, $payableTotal - $paidTotal);
        $changeTotal = max(0, $paidTotal - $payableTotal);

        $paymentStatus = 'unpaid';
        if ($payableTotal <= 0) {
            $paymentStatus = 'paid';
        } elseif ($paidTotal <= 0) {
            $paymentStatus = 'unpaid';
        } elseif ($paidTotal < $payableTotal) {
            $paymentStatus = 'partial';
        } else {
            $paymentStatus = 'paid';
        }

        $updates = [
            'subtotal' => $subtotal,
            'payable_total' => $payableTotal,
            'paid_total' => $paidTotal,
            'due_total' => $dueTotal,
            'change_total' => $changeTotal,
            'payment_status' => $paymentStatus,
        ];

        // cancel()/refund() set a terminal status themselves right after
        // calling this and don't want it overwritten by the pending/completed
        // auto-transition below (which the item/gift mutation callers do rely on).
        if (!$preserveStatus) {
            $updates['status'] = ($paymentStatus === 'paid') ? 'completed' : 'pending';
        }

        $order->update($updates);
    }

    /**
     * Get or create cart from order (for edit)
     */
    private function getOrCreateCartFromOrder(Order $order): Cart
    {
        $sessionId = session()->getId();

        $cart = Cart::where('session_id', $sessionId)
            ->whereNull('payable_total')
            ->first();

        if ($cart) {
            CartItem::where('cart_id', $cart->id)->delete();
            $cart->total = 0;
            $cart->save();
            return $cart;
        }

        return Cart::create([
            'session_id' => $sessionId,
            'total' => 0,
            'customer_id' => $order->customer_id,
            'rewards_points_used' => 0,
            'rewards_amount_used' => 0,
        ]);
    }

    /**
     * Sync order items to cart
     */
    private function syncOrderToCart(Order $order, Cart $cart, int $locationId): void
    {
        foreach ($order->items as $orderItem) {
            $batch = ProductBatch::find($orderItem->product_batch_id);
            $saleUnit = $orderItem->unit ?? ($batch?->unit ?? 'pcs');
            $qtyBatch = $orderItem->quantity;

            $payload = [
                'cart_id' => $cart->id,
                'product_id' => $orderItem->product_id,
                'product_batch_id' => $orderItem->product_batch_id,
                'product_image_id' => null,
                'price_type' => $orderItem->price_type ?? 'retail',
                'quantity' => (float) $orderItem->quantity,
                'unit_price' => (float) $orderItem->unit_price,
                'total_price' => (float) $orderItem->total_price,
                'discount_amount' => (float) ($orderItem->discount_amount ?? 0),
                'discount_percent' => null,
                'discount_label' => null,
                'is_gift' => (bool) $orderItem->is_gift,
                'gift_source' => $orderItem->gift_source,
                'gift_source_id' => $orderItem->gift_source_id,
                'parent_cart_item_id' => null,
                'unit' => $saleUnit,
                'qty_in_batch_unit' => $qtyBatch,
                'location_id' => $locationId,
            ];

            CartItem::create($payload);
        }

        $this->recalcCart($cart);
    }

    private function recalcCart(Cart $cart): void
    {
        $cart->total = (float) CartItem::where('cart_id', $cart->id)->sum('total_price');
        $cart->save();
    }

    /**
     * Create Order from cart (full checkout flow)
     */
    public function storeFromCart(Request $request)
    {
        $data = $request->validate([
            'order_discount'       => 'nullable|numeric|min:0',
            'rewards_points_used'  => 'nullable|numeric|min:0',
            'rewards_amount_used'  => 'nullable|numeric|min:0',
            'payment_note'         => 'nullable|string|max:2000',
            'payments'             => 'nullable|array|min:1',
            'payments.*.channel'   => 'required_with:payments|string|in:offline,online',
            'payments.*.method'    => 'required_with:payments|string',
            'payments.*.amount'    => 'required_with:payments|numeric|min:0.0001',
            'payments.*.trx_id'    => 'nullable|string|max:80',
            'payments.*.account_label' => 'nullable|string|max:120',
            'apply_balance_mode'   => 'nullable|string|in:auto,none',
            'location_id'          => 'nullable|exists:locations,id',
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
                'location_id' => $locationId,
                'channel' => 'pos',
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

            $order->recordTimeline('created', 'Order Created', "Order #{$order->order_no} created from cart checkout.", null, $order->status, 'shopping-bag');

            if ($customer && $usedPoints > 0) {
                CustomerRewardLedger::where('customer_id', $customer->id)
                    ->where('action', 'redeem')
                    ->where('direction', 'subtract')
                    ->whereNull('ref_id')
                    ->latest('id')
                    ->take(1)
                    ->update(['ref_id' => $order->id]);
            }

            foreach ($cart->items as $ci) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $ci->product_id,
                    'product_batch_id' => $ci->product_batch_id,
                    'product_name' => $ci->product?->name,
                    'barcode' => $ci->product?->barcode,
                    'price_type' => $ci->price_type,
                    'unit_price' => (float) $ci->unit_price,
                    'quantity' => (float) $ci->quantity,
                    'unit' => $ci->unit,
                    'discount_amount' => (float) ($ci->discount_amount ?? 0),
                    'total_price' => (float) $ci->total_price,
                ]);

                $needBatch = (float) ($ci->qty_in_batch_unit ?? $ci->quantity);

                $stock = DB::table('batch_stocks')
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

                DB::table('batch_stocks')
                    ->where('product_batch_id', (int)$ci->product_batch_id)
                    ->where('location_id', $locationId)
                    ->update([
                        'on_hand' => DB::raw('on_hand - ' . $needBatch)
                    ]);

                $this->syncLegacyBatchQuantity((int)$ci->product_batch_id);
            }

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

            // Persist the customer's balance changes. The advance credit was
            // already baked into this order's payable_total above, so it's
            // spent regardless of how much of the remainder gets paid now.
            // Any old due gets collected first out of whatever was actually
            // paid, before the rest counts toward this order's own total.
            if ($customer && $applyMode === 'auto') {
                $dueCollected = $oldDue > 0 ? min($paid, $oldDue) : 0.0;

                if ($advanceUsed > 0 || $dueCollected > 0) {
                    $customer->advance_balance = round($oldAdvance - $advanceUsed, 4);
                    $customer->due_balance = round($oldDue - $dueCollected, 4);
                    $customer->save();
                }
            }

            $order->paid_total = $paid;
            $order->due_total = $due;
            $order->change_total = $change;
            $order->payment_status = $paymentStatus;
            $order->status = ($paymentStatus === 'paid') ? 'completed' : 'pending';
            $order->save();

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

            CartItem::where('cart_id', $cart->id)->delete();
            $cart->total = 0;
            $cart->save();

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

    private function reservedQtyForBatch(int $cartId, int $batchId, int $locationId, ?int $excludeItemId = null): float
    {
        $q = CartItem::where('cart_id', $cartId)
            ->where('product_batch_id', $batchId);

        if ($excludeItemId) $q->where('id', '!=', $excludeItemId);

        if (Schema::hasColumn('cart_items', 'location_id')) {
            $q->where('location_id', $locationId);
        }

        if (Schema::hasColumn('cart_items', 'qty_in_batch_unit')) {
            return (float) $q->sum('qty_in_batch_unit');
        }

        return (float) $q->sum('quantity');
    }

    private function assertBatchStockAvailable(int $batchId, int $cartId, int $locationId, float $deltaQtyBatch, ?int $excludeItemId = null): void
    {
        $stock = DB::table('batch_stocks')
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

    private function syncLegacyBatchQuantity(int $batchId): void
    {
        $sum = (float) DB::table('batch_stocks')
            ->where('product_batch_id', $batchId)
            ->sum('on_hand');

        ProductBatch::whereKey($batchId)->update([
            'quantity' => $sum
        ]);
    }

    /**
     * Print order invoice
     */
    public function print(Order $order)
    {
        $order->load([
            'customer',
            'items.product',
            'items.batch',
            'location'
        ]);

        return view('orders.print', compact('order'));
    }
}
