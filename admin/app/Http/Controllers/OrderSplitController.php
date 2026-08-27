<?php
// app/Http/Controllers/OrderSplitController.php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderSplit;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrderSplitController extends Controller
{
    /**
     * Show split options for an order
     */
    public function index(Order $order)
    {
        if (!$order->canSplit()) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'This order cannot be split.');
        }

        $availableItems = $order->getAvailableForSplit();
        $orderTotal = $order->payable_total;
        $availableTotal = 0;

        foreach ($availableItems as $item) {
            $availableTotal += $item->unit_price * ($item->quantity - ($item->returned_qty ?? 0));
        }

        return view('orders.split-select', compact('order', 'availableItems', 'orderTotal', 'availableTotal'));
    }

    /**
     * Preview split before confirmation
     */
    public function preview(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:order_items,id',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'split_reason' => 'nullable|string|max:500',
            'split_type' => 'nullable|string|in:manual,partial_payment,partial_fulfillment',
            'split_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $splitItems = [];
        $totalAmount = 0;
        $originalAmount = 0;

        foreach ($request->items as $itemData) {
            $orderItem = OrderItem::with('product')
                ->where('order_id', $order->id)
                ->findOrFail($itemData['id']);

            $available = $orderItem->quantity - ($orderItem->returned_qty ?? 0);

            if ($itemData['quantity'] > $available) {
                $message = "Quantity exceeds available for item: {$orderItem->product_name}";
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 422);
                }
                return back()->with('error', $message);
            }

            $itemTotal = $orderItem->unit_price * $itemData['quantity'];
            $originalAmount += $orderItem->unit_price * $orderItem->quantity;

            $splitItems[] = [
                'id' => $orderItem->id,
                'product_name' => $orderItem->product_name ?? $orderItem->product?->name ?? 'Unknown',
                'barcode' => $orderItem->barcode ?? $orderItem->product?->barcode ?? '',
                'quantity' => (float) $itemData['quantity'],
                'unit_price' => (float) $orderItem->unit_price,
                'total_price' => (float) $itemTotal,
                'discount_amount' => (float) ($orderItem->discount_amount ?? 0),
                'original_quantity' => (float) $orderItem->quantity,
                'available_quantity' => $available,
                'price_type' => $orderItem->price_type ?? 'retail',
                'unit' => $orderItem->unit ?? 'pcs',
            ];

            $totalAmount += $itemTotal;
        }

        $remainingTotal = $order->payable_total - $totalAmount;

        if ($request->ajax() || $request->wantsJson()) {
            $html = view('orders.split-preview', compact(
                'order',
                'splitItems',
                'totalAmount',
                'remainingTotal',
                'originalAmount'
            ))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'preview_data' => [
                    'total_amount' => $totalAmount,
                    'remaining_total' => $remainingTotal,
                    'items_count' => count($splitItems),
                ]
            ]);
        }

        return view('orders.split-preview', compact(
            'order',
            'splitItems',
            'totalAmount',
            'remainingTotal',
            'originalAmount'
        ));
    }

    /**
     * Execute order split
     */
    public function split(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:order_items,id',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'split_reason' => 'nullable|string|max:500',
            'split_type' => 'nullable|string|in:manual,partial_payment,partial_fulfillment',
            'split_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            $user = Auth::user();
            $totalSplitAmount = 0;

            // Validate every requested item actually belongs to this order and
            // that the requested quantity doesn't exceed what's really available,
            // under a row lock so nothing else can move these items mid-split.
            foreach ($request->items as $itemData) {
                $orderItem = OrderItem::where('order_id', $order->id)
                    ->where('id', $itemData['id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $available = (float) $orderItem->quantity - (float) ($orderItem->returned_qty ?? 0);
                $qtyRequested = (float) $itemData['quantity'];

                if ($qtyRequested > $available) {
                    throw new \RuntimeException('Quantity exceeds available for item: ' . ($orderItem->product_name ?? $orderItem->id));
                }

                $totalSplitAmount += $orderItem->unit_price * $qtyRequested;
            }

            $childOrder = $this->createChildOrder($order, $request, $totalSplitAmount);
            $this->moveItemsToChildOrder($order, $childOrder, $request->items);

            // Reallocate discount and captured payments while $order's in-memory
            // discount_total/payable_total still reflect the pre-split figures,
            // then recompute both orders' cached totals last so nothing is stale.
            $this->prorateDiscount($order, $childOrder, $totalSplitAmount);
            $this->handleSplitPayments($order, $childOrder, $totalSplitAmount);

            $this->updateOrderTotals($order);
            $this->updateOrderTotals($childOrder);
            $this->createSplitRecord($order, $childOrder, $request, $totalSplitAmount);

            $order->recordTimeline('split', 'Order Split', "Sub-order #{$childOrder->order_no} created (" . currency_bdt($totalSplitAmount) . ').', null, null, 'split');
            $childOrder->recordTimeline('created', 'Sub-Order Created', "Split from order #{$order->order_no}.", null, $childOrder->status, 'shopping-bag');

            if ($order->isOriginal()) {
                $order->update([
                    'split_status' => 'split_parent',
                    'split_sequence' => 1,
                ]);
            }

            DB::commit();

            Log::info('Order split executed', [
                'order_id' => $order->id,
                'child_order_id' => $childOrder->id,
                'split_by' => $user->id,
                'amount' => $totalSplitAmount,
            ]);

            return redirect()->route('orders.show', $order)
                ->with('success', "Order split successfully. Sub-order #{$childOrder->order_no} created.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order split failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('orders.show', $order)
                ->with('error', 'Failed to split order: ' . $e->getMessage());
        }
    }

    /**
     * Create child order
     */
    private function createChildOrder(Order $parentOrder, Request $request, float $totalAmount): Order
    {
        $sequence = $parentOrder->childOrders()->max('split_sequence') + 1;
        $childOrderNo = 'SUB-' . now()->format('YmdHis') . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);

        return Order::create([
            'order_no' => $childOrderNo,
            'session_id' => session()->getId(),
            'customer_id' => $parentOrder->customer_id,
            'location_id' => $parentOrder->location_id,
            'parent_order_id' => $parentOrder->id,
            'original_order_id' => $parentOrder->original_order_id ?? $parentOrder->id,
            'split_reason' => $request->split_reason ?? 'Manual split',
            'split_status' => 'split_child',
            'is_split_child' => true,
            'split_sequence' => $sequence,
            'split_by' => Auth::id(),
            'split_at' => now(),
            'split_notes' => $request->split_notes ?? null,
            'subtotal' => $totalAmount,
            'discount_total' => 0,
            'payable_total' => $totalAmount,
            'rewards_points_used' => 0,
            'rewards_amount_used' => 0,
            'paid_total' => 0,
            'due_total' => $totalAmount,
            'change_total' => 0,
            'payment_status' => 'unpaid',
            'payment_note' => $request->split_notes ?? "Created from split of order #{$parentOrder->order_no}",
            'status' => 'pending',
        ]);
    }

    /**
     * Move items to child order
     */
    private function moveItemsToChildOrder(Order $parentOrder, Order $childOrder, array $items)
    {
        foreach ($items as $itemData) {
            $parentItem = OrderItem::where('order_id', $parentOrder->id)
                ->where('id', $itemData['id'])
                ->lockForUpdate()
                ->firstOrFail();

            $qtyToMove = (float) $itemData['quantity'];
            $available = (float) $parentItem->quantity - (float) ($parentItem->returned_qty ?? 0);

            if ($qtyToMove > $available) {
                throw new \RuntimeException('Quantity exceeds available for item: ' . ($parentItem->product_name ?? $parentItem->id));
            }

            $remainingQty = (float) $parentItem->quantity - $qtyToMove;

            // Moving the full remaining quantity would delete this row below,
            // but return_items/exchange_lines hold a plain FK to it with no
            // cascade -- an item that already has return history can't be
            // fully split away without crashing on that delete.
            if ($remainingQty <= 0 && (float) ($parentItem->returned_qty ?? 0) > 0) {
                throw new \RuntimeException('Item already has a return/refund recorded and cannot be fully split away: ' . ($parentItem->product_name ?? $parentItem->id));
            }

            // Prorate discount/returned tracking by the fraction of quantity
            // moved, instead of copying the parent's figures onto both halves.
            $moveFraction = ((float) $parentItem->quantity) > 0 ? ($qtyToMove / (float) $parentItem->quantity) : 0;
            $movedDiscount = round((float) ($parentItem->discount_amount ?? 0) * $moveFraction, 4);
            $movedReturnedQty = round((float) ($parentItem->returned_qty ?? 0) * $moveFraction, 4);
            $movedReturnedAmount = round((float) ($parentItem->returned_amount ?? 0) * $moveFraction, 4);

            $newItem = $parentItem->replicate();
            $newItem->order_id = $childOrder->id;
            $newItem->quantity = $qtyToMove;
            $newItem->total_price = $parentItem->unit_price * $qtyToMove;
            $newItem->discount_amount = $movedDiscount;
            $newItem->returned_qty = $movedReturnedQty;
            $newItem->returned_amount = $movedReturnedAmount;
            $newItem->created_at = now();
            $newItem->updated_at = now();
            $newItem->save();

            if ($remainingQty > 0) {
                $parentItem->quantity = $remainingQty;
                $parentItem->total_price = $parentItem->unit_price * $remainingQty;
                $parentItem->discount_amount = round((float) ($parentItem->discount_amount ?? 0) - $movedDiscount, 4);
                $parentItem->returned_qty = round((float) ($parentItem->returned_qty ?? 0) - $movedReturnedQty, 4);
                $parentItem->returned_amount = round((float) ($parentItem->returned_amount ?? 0) - $movedReturnedAmount, 4);
                $parentItem->save();
            } else {
                $parentItem->delete();
            }
        }
    }

    /**
     * Prorate the parent order's discount_total onto the child order by the
     * fraction of the pre-split subtotal the split items represented.
     */
    private function prorateDiscount(Order $parentOrder, Order $childOrder, float $totalSplitAmount): void
    {
        $parentDiscount = (float) ($parentOrder->discount_total ?? 0);
        if ($parentDiscount <= 0) {
            return;
        }

        // Items have already moved: parent's current item sum is the remaining
        // (post-split) subtotal, so add back the moved amount to reconstruct
        // the original pre-split subtotal this discount was set against.
        $remainingSubtotal = (float) $parentOrder->items()->sum('total_price');
        $originalSubtotal = $remainingSubtotal + $totalSplitAmount;

        if ($originalSubtotal <= 0) {
            return;
        }

        $childDiscount = round($parentDiscount * ($totalSplitAmount / $originalSubtotal), 4);
        $childDiscount = min($childDiscount, $parentDiscount);

        $parentOrder->discount_total = round($parentDiscount - $childDiscount, 4);
        $parentOrder->save();

        $childOrder->discount_total = $childDiscount;
        $childOrder->save();
    }

    /**
     * Create split record
     */
    private function createSplitRecord(Order $parentOrder, Order $childOrder, Request $request, float $totalAmount)
    {
        $originalOrderId = $parentOrder->original_order_id ?? $parentOrder->id;

        OrderSplit::create([
            'original_order_id' => $originalOrderId,
            'parent_order_id' => $parentOrder->id,
            'child_order_id' => $childOrder->id,
            // OrderSplit::$casts already casts split_items as 'array', which
            // json_encode()s it on save. Passing an already-encoded string
            // here made Eloquent encode it a second time, so the column ended
            // up holding a JSON string-of-a-string instead of a JSON array --
            // getSplitItemsAttribute()'s json_decode() would then hand callers
            // a plain string instead of the expected array.
            'split_items' => $request->items,
            'split_amount' => $totalAmount,
            'split_type' => $request->split_type ?? 'manual',
            'split_reason' => $request->split_reason ?? 'Manual split',
            'split_notes' => $request->split_notes ?? null,
            'created_by' => Auth::id(),
            'split_at' => now(),
        ]);
    }

    /**
     * Update order totals
     */
    private function updateOrderTotals(Order $order)
    {
        $subtotal = (float) $order->items()->sum('total_price');
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

        $order->update([
            'subtotal' => $subtotal,
            'payable_total' => $payableTotal,
            'paid_total' => $paidTotal,
            'due_total' => $dueTotal,
            'change_total' => $changeTotal,
            'payment_status' => $paymentStatus,
            'status' => $order->status === 'completed' ? 'processing' : $order->status,
        ]);
    }

    /**
     * Handle split payments
     */
    private function handleSplitPayments(Order $parentOrder, Order $childOrder, float $totalSplitAmount)
    {
        $parentPayments = Payment::where('order_id', $parentOrder->id)
            ->where('status', 'captured')
            ->lockForUpdate()
            ->get();

        if ($parentPayments->isEmpty()) {
            return;
        }

        // Called before updateOrderTotals() runs for either order, so
        // $parentOrder->payable_total here is still the pre-split total --
        // exactly the base this proportion needs.
        $originalTotal = (float) $parentOrder->payable_total;
        $childProportion = $originalTotal > 0 ? min(1, $totalSplitAmount / $originalTotal) : 0;

        if ($childProportion <= 0) {
            return;
        }

        foreach ($parentPayments as $payment) {
            $allocatedAmount = round((float) $payment->amount * $childProportion, 4);
            if ($allocatedAmount <= 0) {
                continue;
            }

            // True transfer: move the allocated amount out of the parent's
            // payment and onto a new child payment, instead of creating money.
            $payment->amount = round((float) $payment->amount - $allocatedAmount, 4);
            if ($payment->amount <= 0) {
                $payment->status = 'void';
            }
            $payment->save();

            Payment::create([
                'order_id' => $childOrder->id,
                'channel' => $payment->channel,
                'method' => $payment->method,
                'trx_id' => $payment->trx_id ? ($payment->trx_id . '-SUB-' . $childOrder->id) : null,
                'account_label' => $payment->account_label ? ($payment->account_label . ' (Split)') : null,
                'amount' => $allocatedAmount,
                'status' => 'captured',
                'meta' => [
                    'original_payment_id' => $payment->id,
                    'split_from_order_id' => $parentOrder->id,
                    'split_amount' => $allocatedAmount,
                ],
            ]);
        }
    }

    /**
     * Show order split history
     */
    public function history(Order $order)
    {
        $splits = OrderSplit::where('original_order_id', $order->id)
            ->orWhere('parent_order_id', $order->id)
            ->orWhere('child_order_id', $order->id)
            ->with(['originalOrder', 'parentOrder', 'childOrder', 'createdBy'])
            ->orderBy('split_at', 'desc')
            ->get();

        return view('orders.split-history', compact('order', 'splits'));
    }

    /**
     * Merge child order back to parent
     */
    public function merge(Request $request, Order $parentOrder, Order $childOrder)
    {
        if (!$parentOrder->canMergeChild($childOrder)) {
            return redirect()->back()->with('error', 'Cannot merge these orders.');
        }

        try {
            DB::beginTransaction();

            $parentOrder = Order::whereKey($parentOrder->id)->lockForUpdate()->firstOrFail();
            $childOrder = Order::whereKey($childOrder->id)->lockForUpdate()->firstOrFail();

            foreach ($childOrder->items as $item) {
                $this->moveItemToParent($item, $parentOrder);
            }

            $this->mergeChildPayments($childOrder, $parentOrder);

            $parentOrder->discount_total = round((float) ($parentOrder->discount_total ?? 0) + (float) ($childOrder->discount_total ?? 0), 4);
            $parentOrder->save();

            $childFromStatus = $childOrder->status;
            $childOrder->update([
                'status' => 'merged',
                'split_status' => 'merged',
                'split_notes' => ($childOrder->split_notes ?? '') . "\nMerged back to parent order #{$parentOrder->order_no} at " . now(),
            ]);

            $this->updateOrderTotals($parentOrder);

            OrderSplit::where('child_order_id', $childOrder->id)
                ->update([
                    'split_type' => DB::raw("CONCAT(split_type, '_merged')"),
                    'split_notes' => DB::raw("CONCAT(COALESCE(split_notes, ''), ' | Merged back on ', NOW())"),
                ]);

            $parentOrder->recordTimeline('merged', 'Sub-Order Merged Back', "Sub-order #{$childOrder->order_no} merged back into this order.", null, null, 'merge');
            $childOrder->recordTimeline('merged', 'Merged into Parent Order', "Merged back into order #{$parentOrder->order_no}.", $childFromStatus, 'merged', 'merge');

            DB::commit();

            return redirect()->route('orders.show', $parentOrder)
                ->with('success', "Sub-order #{$childOrder->order_no} merged back successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('orders.show', $parentOrder)
                ->with('error', 'Failed to merge order: ' . $e->getMessage());
        }
    }

    /**
     * Move item back to parent (for merge)
     */
    private function moveItemToParent(OrderItem $childItem, Order $parentOrder)
    {
        $parentItem = OrderItem::where('order_id', $parentOrder->id)
            ->where('product_batch_id', $childItem->product_batch_id)
            ->where('price_type', $childItem->price_type)
            ->lockForUpdate()
            ->first();

        // Folding the child item into a matching parent item deletes the
        // child's row below -- but return_items/exchange_lines hold a plain
        // FK to it with no cascade, so an item that already has return
        // history can't be deleted. Keep it as its own row on the parent
        // order instead of consolidating it, the same as when there's no
        // matching parent item to merge into.
        if ($parentItem && (float) ($childItem->returned_qty ?? 0) <= 0) {
            $parentItem->quantity += $childItem->quantity;
            $parentItem->total_price = $parentItem->unit_price * $parentItem->quantity;
            $parentItem->discount_amount = round((float) ($parentItem->discount_amount ?? 0) + (float) ($childItem->discount_amount ?? 0), 4);
            $parentItem->returned_qty = round((float) ($parentItem->returned_qty ?? 0) + (float) ($childItem->returned_qty ?? 0), 4);
            $parentItem->returned_amount = round((float) ($parentItem->returned_amount ?? 0) + (float) ($childItem->returned_amount ?? 0), 4);
            $parentItem->save();
            $childItem->delete();
        } else {
            $childItem->update([
                'order_id' => $parentOrder->id,
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Move the child order's captured payments back onto the parent,
     * reversing the allocation handleSplitPayments() made at split time.
     */
    private function mergeChildPayments(Order $childOrder, Order $parentOrder): void
    {
        $childPayments = Payment::where('order_id', $childOrder->id)
            ->where('status', 'captured')
            ->lockForUpdate()
            ->get();

        foreach ($childPayments as $payment) {
            $originalPaymentId = $payment->meta['original_payment_id'] ?? null;

            $originalPayment = $originalPaymentId
                ? Payment::where('id', $originalPaymentId)
                    ->where('order_id', $parentOrder->id)
                    ->lockForUpdate()
                    ->first()
                : null;

            if ($originalPayment) {
                $originalPayment->amount = round((float) $originalPayment->amount + (float) $payment->amount, 4);
                $originalPayment->status = 'captured';
                $originalPayment->save();
                $payment->delete();
            } else {
                // Not traceable back to a specific parent payment (or the
                // original was removed) -- re-parent it rather than lose it.
                $payment->update(['order_id' => $parentOrder->id]);
            }
        }
    }

    /**
     * Get order split data for API
     */
    public function getSplitData(Order $order)
    {
        $availableItems = $order->getAvailableForSplit();
        $splits = OrderSplit::where('original_order_id', $order->id)
            ->orWhere('parent_order_id', $order->id)
            ->with(['childOrder'])
            ->get();

        return response()->json([
            'available_items' => $availableItems->map(function($item) {
                return [
                    'id' => $item->id,
                    'product_name' => $item->product_name,
                    'barcode' => $item->barcode,
                    'quantity' => $item->quantity,
                    'available' => $item->quantity - ($item->returned_qty ?? 0),
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                ];
            }),
            'splits' => $splits->map(function($split) {
                return [
                    'id' => $split->id,
                    'child_order_no' => $split->childOrder->order_no,
                    'amount' => $split->split_amount,
                    'type' => $split->split_type,
                    'date' => $split->split_at->format('Y-m-d H:i'),
                    'status' => $split->childOrder->status,
                ];
            }),
            'order_total' => $order->payable_total,
            'can_split' => $order->canSplit(),
        ]);
    }
}
