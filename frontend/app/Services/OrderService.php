<?php

namespace App\Services;

use App\Models\BatchStock;
use App\Models\Customer;
use App\Models\CustomerRewardLedger;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ProductBatch;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Converts the active cart into a real order -- same `orders` / `order_items`
 * / `payments` tables and the same on-hand-decrement mechanic the admin POS
 * checkout uses, so stock and reporting stay correct for both channels.
 */
class OrderService
{
    public function __construct(private CartService $cartService)
    {
    }

    public function placeOrder(array $shipping, string $paymentKey, ?int $resolvedCustomerId = null): Order
    {
        $methods = config('store.payment_methods');
        $payMeta = $methods[$paymentKey] ?? null;

        if (!$payMeta) {
            throw new RuntimeException('Please choose a valid payment method.');
        }

        return DB::transaction(function () use ($shipping, $payMeta, $resolvedCustomerId) {
            $cart = $this->cartService->lockCurrent();
            $cart->load(['items.product']);

            if ($cart->items->isEmpty()) {
                throw new RuntimeException('Your cart is empty.');
            }

            $locationId = $this->cartService->locationId();

            // Authoritative stock check, row-locked, right before we commit.
            foreach ($cart->items as $ci) {
                $stock = BatchStock::where('product_batch_id', $ci->product_batch_id)
                    ->where('location_id', $locationId)
                    ->lockForUpdate()
                    ->first();

                if (!$stock || (float) $stock->on_hand < (float) $ci->quantity) {
                    $name = $ci->product?->name ?? 'An item';
                    throw new RuntimeException("\"{$name}\" no longer has enough stock. Please update your cart.");
                }
            }

            $subtotal = (float) $cart->items->sum('total_price');
            // Guests have no session id -- an id resolved via CustomerMatcher
            // (see CheckoutController::store()) during this same request only
            // applies when nobody is actually logged in.
            $customerId = Auth::guard('customer')->id() ?? $resolvedCustomerId;

            // Reward points, if any were redeemed against this cart (see
            // CartController::applyRewards()), get re-checked here under lock
            // -- mirrors admin's CartController::checkout() double-check --
            // since the points could have been spent elsewhere (another tab,
            // an in-store sale) between "apply" and "place order".
            $rewardsPointsUsed = 0.0;
            $rewardsAmountUsed = 0.0;
            $customer = null;

            if ((float) $cart->rewards_points_used > 0) {
                if (!$customerId) {
                    throw new RuntimeException('Please log in to use your reward points.');
                }

                $customer = Customer::whereKey($customerId)->lockForUpdate()->first();
                $pointsRequested = (float) $cart->rewards_points_used;

                if (!$customer || (float) $customer->reward_points < $pointsRequested) {
                    throw new RuntimeException('You no longer have enough reward points for this redemption. Please update it and try again.');
                }

                $rewardsPointsUsed = $pointsRequested;
                // Clamp to the current subtotal too -- the cart may have
                // shrunk since the points were applied.
                $rewardsAmountUsed = min((float) $cart->rewards_amount_used, $subtotal);
            }

            $discountTotal = min($rewardsAmountUsed, $subtotal);
            $payable = max(0, $subtotal - $discountTotal);

            $order = $this->createOrderWithRetry(
                $cart,
                $customerId,
                $locationId,
                $subtotal,
                $shipping,
                $discountTotal,
                $payable,
                $rewardsPointsUsed,
                $rewardsAmountUsed
            );

            if ($rewardsPointsUsed > 0 && $customer) {
                $customer->reward_points = (float) $customer->reward_points - $rewardsPointsUsed;
                $customer->save();

                CustomerRewardLedger::create([
                    'customer_id' => $customer->id,
                    'action' => 'redeem',
                    'direction' => 'subtract',
                    'points' => $rewardsPointsUsed,
                    'ref_type' => 'order',
                    'ref_id' => $order->id,
                    'channel' => 'online',
                    'terminal_id' => null,
                    'created_by' => null,
                    'idempotency_key' => null,
                    'note' => 'Redeemed on storefront checkout',
                ]);
            }

            $order->recordTimeline(
                'created',
                'Order placed',
                "Order #{$order->order_no} placed via the storefront.",
                null,
                $order->status,
                'shopping-bag'
            );

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
                    'discount_amount' => (float) $ci->discount_amount,
                    'total_price' => (float) $ci->total_price,
                ]);

                $stock = BatchStock::where('product_batch_id', $ci->product_batch_id)
                    ->where('location_id', $locationId)
                    ->lockForUpdate()
                    ->first();

                $stock->on_hand = (float) $stock->on_hand - (float) $ci->quantity;
                $stock->save();

                $sum = (float) BatchStock::where('product_batch_id', $ci->product_batch_id)->sum('on_hand');
                ProductBatch::whereKey($ci->product_batch_id)->update(['quantity' => $sum]);
            }

            Payment::create([
                'order_id' => $order->id,
                'channel' => $payMeta['channel'],
                'method' => $payMeta['method'],
                'trx_id' => $shipping['trx_id'] ?? null,
                'account_label' => null,
                'amount' => $payable,
                'status' => 'pending',
                'meta' => ['source' => 'storefront'],
            ]);

            // Retire this cart (matches the admin POS convention: a non-null
            // payable_total marks it converted, and it stores the actual
            // post-discount amount) so the next add-to-cart starts a fresh
            // one.
            $cart->payable_total = $payable;
            $cart->rewards_points_used = $rewardsPointsUsed;
            $cart->rewards_amount_used = $rewardsAmountUsed;
            $cart->save();

            return $order->refresh();
        });
    }

    private function createOrderWithRetry(
        \App\Models\Cart $cart,
        ?int $customerId,
        int $locationId,
        float $subtotal,
        array $shipping,
        float $discountTotal = 0.0,
        float $payableTotal = 0.0,
        float $rewardsPointsUsed = 0.0,
        float $rewardsAmountUsed = 0.0
    ): Order {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            try {
                return Order::create([
                    'order_no' => (new Order())->generateOrderNo(),
                    'session_id' => $cart->session_id,
                    'customer_id' => $customerId,
                    'location_id' => $locationId,
                    'channel' => 'online',
                    'subtotal' => $subtotal,
                    'discount_total' => $discountTotal,
                    'payable_total' => $payableTotal,
                    'rewards_points_used' => $rewardsPointsUsed,
                    'rewards_amount_used' => $rewardsAmountUsed,
                    'paid_total' => 0,
                    'due_total' => $payableTotal,
                    'change_total' => 0,
                    'payment_status' => 'unpaid',
                    'status' => 'pending',
                    'shipping_name' => $shipping['name'],
                    'shipping_phone' => $shipping['phone'],
                    'shipping_address' => $shipping['address'],
                    'shipping_note' => $shipping['note'] ?? null,
                ]);
            } catch (QueryException $e) {
                if ((int) ($e->errorInfo[1] ?? 0) !== 1062 || $attempt === 4) {
                    throw $e;
                }
            }
        }

        throw new RuntimeException('Could not place order, please try again.');
    }
}
