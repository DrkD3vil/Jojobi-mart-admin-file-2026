<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReturnRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductReturn;
use App\Models\ReturnItem;
use App\Models\StockTransaction;
use App\Models\StockTransactionLine;
use App\Services\Inventory\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReturnController extends Controller
{
    /**
     * The 'returns.create' route had no handler at all (a fatal error on
     * every visit) -- the actual, working return flow lives entirely in
     * ReturnWizardController. Redirect here instead of maintaining a
     * second, parallel return form.
     */
    public function create(Request $request)
    {
        return redirect()->route('returns.wizard', $request->only(['order_id', 'customer_id']));
    }

    public function store(StoreReturnRequest $request, InventoryService $inventory)
    {
        $data = $request->validated();

        // The wizard posts a plain HTML form (no JS fetch), which can't set a
        // custom header -- so 'X-Idempotency-Key' was never actually sent by
        // the real UI, and this fell through to a fresh random uuid() on
        // every request, making the "already processed" check below a no-op.
        // A double-click or resubmit created a second return, refund, and
        // stock-in for the same items. The wizard now renders a token into
        // a hidden `idempotency_key` field once per page load, so a repeat
        // submission reuses the same key here.
        $idemKey = $data['idempotency_key'] ?? $request->header('X-Idempotency-Key') ?? (string) Str::uuid();

        $existing = ProductReturn::where('idempotency_key', $idemKey)->first();
        if ($existing) {
            return redirect()->back()->with('ok', "Already processed: {$existing->return_no}");
        }

        return DB::transaction(function () use ($data, $inventory, $idemKey) {

            $order = Order::with('items')->findOrFail($data['order_id']);

            $return = ProductReturn::create([
                'return_no' => 'RET-' . now()->format('YmdHis') . '-' . rand(100, 999),
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'location_id' => $data['location_id'],
                'status' => 'RECEIVED',
                'refund_method' => $data['refund_method'] ?? null,
                'refund_amount' => 0,
                'idempotency_key' => $idemKey,
                'note' => $data['note'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $refundTotal = 0;
            $usedBatchIds = [];

            foreach ($data['items'] as $it) {
                $orderItem = OrderItem::lockForUpdate()->findOrFail($it['order_item_id']);

                // belong to same order
                if ((int)$orderItem->order_id !== (int)$order->id) {
                    throw new \RuntimeException("Order item does not belong to order.");
                }

                // fast rule: use returned_qty column
                $alreadyReturned = (float)($orderItem->returned_qty ?? 0);
                $maxReturnable = (float)$orderItem->quantity - $alreadyReturned;

                if ((float)$it['qty'] > $maxReturnable) {
                    throw new \RuntimeException("Return qty exceeds allowed for order_item_id={$orderItem->id}");
                }

                $unitPrice = (float)$orderItem->unit_price;
                $refund = (float)$it['qty'] * $unitPrice;

                $ri = ReturnItem::create([
                    'return_id' => $return->id,
                    'order_item_id' => $orderItem->id,
                    'product_id' => $it['product_id'],
                    'product_batch_id' => $it['product_batch_id'],
                    'qty' => $it['qty'],
                    'unit_price' => $unitPrice,
                    'refund_amount' => $refund,
                    'condition' => $it['condition'] ?? 'GOOD',
                    'reason_code' => $it['reason_code'] ?? null,
                    'note' => null,
                ]);

                // update returned fields
                $orderItem->returned_qty = (float)($orderItem->returned_qty ?? 0) + (float)$it['qty'];
                $orderItem->returned_amount = (float)($orderItem->returned_amount ?? 0) + (float)$refund;

                // update note with return message
                $this->appendReturnNote($orderItem, [
                    'return_no' => $return->return_no,
                    'return_id' => $return->id,
                    'order_id' => $order->id,
                    'order_no' => $order->order_no,
                    'qty' => (float)$it['qty'],
                    'unit_price' => $unitPrice,
                    'refund' => $refund,
                    'batch_id' => (int)$it['product_batch_id'],
                    'condition' => $it['condition'] ?? 'GOOD',
                    'reason' => $it['reason_code'] ?? null,
                ]);

                $orderItem->save();

                $refundTotal += $refund;
                $usedBatchIds[] = (int)$ri->product_batch_id;
            }

            $return->refund_amount = $refundTotal;
            $return->save();

            $return->recordTimeline(
                'received',
                'Return Received',
                currency_bdt($refundTotal) . " refunded via {$return->refund_method} for order #{$order->order_no}.",
                null,
                $return->status,
                'undo-2'
            );
            $order->recordTimeline(
                'return_received',
                'Return Received',
                currency_bdt($refundTotal) . " refunded ({$return->return_no}).",
                null,
                null,
                'undo-2'
            );

            // Stock tx RETURN_IN
            $tx = StockTransaction::create([
                'type' => 'RETURN_IN',
                'status' => 'DRAFT',
                'to_location_id' => (int)$return->location_id,
                'ref_type' => 'return',
                'ref_id' => $return->id,
                'note' => "Return stock in for {$return->return_no}",
                'created_by' => auth()->id(),
            ]);

            foreach ($return->items()->get() as $ri) {
                StockTransactionLine::create([
                    'stock_transaction_id' => $tx->id,
                    'product_id' => $ri->product_id,
                    'product_batch_id' => $ri->product_batch_id,
                    'qty' => $ri->qty,
                    'unit' => 'pcs',
                    'unit_price' => $ri->unit_price,
                    'meta' => [
                        'condition' => $ri->condition,
                        'reason' => $ri->reason_code,
                        'return_item_id' => $ri->id,
                    ],
                ]);
            }

            $inventory->post($tx);

            // Optional: keep product_batches.quantity synced from batch_stocks
            $usedBatchIds = array_values(array_unique($usedBatchIds));
            foreach ($usedBatchIds as $batchId) {
                $this->syncBatchQuantityFromStocks($batchId);
            }

            // recalc order totals / status after return
            $this->recalcOrderTotals($order);

            return redirect()->back()->with('ok', "Return posted: {$return->return_no} (Refund: {$refundTotal})");
        });
    }

    protected function appendReturnNote(OrderItem $orderItem, array $payload): void
    {
        $note = $orderItem->note ?? [];
        $note['returns'] = $note['returns'] ?? [];
        $note['returns'][] = array_merge($payload, [
            'at' => now()->toDateTimeString(),
        ]);
        $orderItem->note = $note;
    }

    protected function syncBatchQuantityFromStocks(int $batchId): void
    {
        $totalOnHand = \App\Models\BatchStock::where('product_batch_id', $batchId)->sum('on_hand');
        \App\Models\ProductBatch::where('id', $batchId)->update(['quantity' => $totalOnHand]);
    }

    protected function recalcOrderTotals(Order $order): void
    {
        $order = Order::with('items')->findOrFail($order->id);

        $subtotal = 0;
        $discountTotal = 0;

        foreach ($order->items as $it) {
            $sold = (float)$it->quantity;
            $returned = (float)($it->returned_qty ?? 0);
            $netQty = max(0, $sold - $returned);

            $subtotal += $netQty * (float)$it->unit_price;
            $discountTotal += (float)($it->discount_amount ?? 0);
        }

        $order->subtotal = $subtotal;
        $order->discount_total = $discountTotal;
        $payable = max(0, $subtotal - $discountTotal);
        $order->payable_total = $payable;

        // A return shrinks payable_total, so what was already paid must be
        // re-split into due/change against the new (smaller) payable amount
        // -- otherwise the order keeps showing the pre-return due/paid figures.
        $paidTotal = (float) \App\Models\Payment::where('order_id', $order->id)
            ->where('status', 'captured')
            ->sum('amount');

        if ($paidTotal <= 0) {
            $order->due_total = $payable;
            $order->change_total = 0;
            $order->payment_status = 'unpaid';
        } elseif ($paidTotal < $payable) {
            $order->due_total = $payable - $paidTotal;
            $order->change_total = 0;
            $order->payment_status = 'partial';
        } else {
            $order->due_total = 0;
            $order->change_total = $paidTotal - $payable;
            $order->payment_status = 'paid';
        }

        $allReturned = $order->items->every(fn($it) => (float)($it->returned_qty ?? 0) >= (float)$it->quantity);
        $order->status = $allReturned ? 'returned' : ($order->status ?? 'completed');

        $order->save();
    }
}
