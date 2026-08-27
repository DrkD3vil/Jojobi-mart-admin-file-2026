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
            $orderItem = OrderItem::with('product')->findOrFail($itemData['id']);

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

            $user = Auth::user();
            $totalSplitAmount = 0;

            foreach ($request->items as $itemData) {
                $orderItem = OrderItem::findOrFail($itemData['id']);
                $totalSplitAmount += $orderItem->unit_price * $itemData['quantity'];
            }

            $childOrder = $this->createChildOrder($order, $request, $totalSplitAmount);
            $this->moveItemsToChildOrder($order, $childOrder, $request->items);
            $this->updateOrderTotals($order);
            $this->updateOrderTotals($childOrder);
            $this->createSplitRecord($order, $childOrder, $request, $totalSplitAmount);

            if ($order->isOriginal()) {
                $order->update([
                    'split_status' => 'split_parent',
                    'split_sequence' => 1,
                ]);
            }

            $this->handleSplitPayments($order, $childOrder, $totalSplitAmount);

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
            $parentItem = OrderItem::findOrFail($itemData['id']);
            $qtyToMove = (float) $itemData['quantity'];

            $remainingQty = $parentItem->quantity - $qtyToMove;

            $newItem = $parentItem->replicate();
            $newItem->order_id = $childOrder->id;
            $newItem->quantity = $qtyToMove;
            $newItem->total_price = $parentItem->unit_price * $qtyToMove;
            $newItem->created_at = now();
            $newItem->updated_at = now();
            $newItem->save();

            if ($remainingQty > 0) {
                $parentItem->quantity = $remainingQty;
                $parentItem->total_price = $parentItem->unit_price * $remainingQty;
                $parentItem->save();
            } else {
                $parentItem->delete();
            }
        }
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
            'split_items' => json_encode($request->items),
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
            ->get();

        if ($parentPayments->isNotEmpty()) {
            $parentTotal = $parentOrder->payable_total + $totalSplitAmount;
            $childProportion = $parentTotal > 0 ? $totalSplitAmount / $parentTotal : 0;

            foreach ($parentPayments as $payment) {
                $allocatedAmount = $payment->amount * $childProportion;

                if ($allocatedAmount > 0) {
                    Payment::create([
                        'order_id' => $childOrder->id,
                        'channel' => $payment->channel,
                        'method' => $payment->method,
                        'trx_id' => $payment->trx_id . '-SUB-' . $childOrder->id,
                        'account_label' => $payment->account_label . ' (Split)',
                        'amount' => $allocatedAmount,
                        'status' => 'captured',
                        'meta' => json_encode([
                            'original_payment_id' => $payment->id,
                            'split_order_id' => $parentOrder->id,
                            'split_amount' => $allocatedAmount,
                        ]),
                    ]);
                }
            }
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

            foreach ($childOrder->items as $item) {
                $this->moveItemToParent($item, $parentOrder);
            }

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
            ->first();

        if ($parentItem) {
            $parentItem->quantity += $childItem->quantity;
            $parentItem->total_price = $parentItem->unit_price * $parentItem->quantity;
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
