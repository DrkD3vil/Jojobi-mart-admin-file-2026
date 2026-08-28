<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductBatch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Wraps the same `carts` / `cart_items` / `batch_stocks` tables the admin
 * POS uses, so a cart started online is fully visible and editable from the
 * admin side too. An "active" cart is any row with a null payable_total;
 * checkout stamps that column, which is what retires it.
 */
class CartService
{
    public function locationId(): int
    {
        return (int) config('store.location_id');
    }

    /**
     * Carries a cart built before login over to the now-known customer.
     * Login/registration regenerate the session id for security, which
     * would otherwise orphan a guest cart (it's keyed by session id until
     * claimed). Skipped if the customer already has an open cart of their
     * own, so we never clobber an existing one.
     */
    public function claimGuestCart(string $previousSessionId, ?int $customerId): void
    {
        if (!$customerId) {
            return;
        }

        $alreadyHasCart = Cart::where('customer_id', $customerId)->whereNull('payable_total')->exists();
        if ($alreadyHasCart) {
            return;
        }

        $cart = Cart::where('session_id', $previousSessionId)->whereNull('customer_id')->whereNull('payable_total')->first();
        if ($cart) {
            $cart->customer_id = $customerId;
            $cart->session_id = session()->getId();
            $cart->save();
        }
    }

    /**
     * The open cart for this visitor, without creating one -- safe to call
     * on every page load (e.g. for the header badge).
     */
    public function peek(): ?Cart
    {
        $customerId = Auth::guard('customer')->id();

        if ($customerId) {
            $cart = Cart::where('customer_id', $customerId)->whereNull('payable_total')->latest('id')->first();
            if ($cart) {
                return $cart;
            }
        }

        return Cart::where('session_id', session()->getId())->whereNull('payable_total')->latest('id')->first();
    }

    public function current(): Cart
    {
        $sessionId = session()->getId();
        $customerId = Auth::guard('customer')->id();

        if ($customerId) {
            $cart = Cart::where('customer_id', $customerId)->whereNull('payable_total')->latest('id')->first();
            if ($cart) {
                if ($cart->session_id !== $sessionId) {
                    $cart->session_id = $sessionId;
                    $cart->save();
                }

                return $cart;
            }
        }

        $cart = Cart::where('session_id', $sessionId)->whereNull('payable_total')->latest('id')->first();
        if ($cart) {
            if ($customerId && !$cart->customer_id) {
                $cart->customer_id = $customerId;
                $cart->save();
            }

            return $cart;
        }

        return Cart::create([
            'session_id' => $sessionId,
            'customer_id' => $customerId,
            'total' => 0,
            'rewards_points_used' => 0,
            'rewards_amount_used' => 0,
        ]);
    }

    public function lockCurrent(): Cart
    {
        $sessionId = session()->getId();
        $customerId = Auth::guard('customer')->id();

        if ($customerId) {
            $cart = Cart::where('customer_id', $customerId)->whereNull('payable_total')->latest('id')->lockForUpdate()->first();
            if ($cart) {
                return $cart;
            }
        }

        $cart = Cart::where('session_id', $sessionId)->whereNull('payable_total')->latest('id')->lockForUpdate()->first();
        if ($cart) {
            return $cart;
        }

        return $this->current();
    }

    public function count(): int
    {
        return (int) ($this->peek()?->items()->sum('quantity') ?? 0);
    }

    public function addItem(int $batchId, float $qty): Cart
    {
        $qty = max(0.0001, $qty);

        return DB::transaction(function () use ($batchId, $qty) {
            $cart = $this->lockCurrent();
            $locationId = $this->locationId();

            $batch = ProductBatch::with(['product.images'])->lockForUpdate()->findOrFail($batchId);

            if (!$batch->is_active || !$batch->is_online) {
                throw new RuntimeException('This product is not available online.');
            }

            $existing = CartItem::where('cart_id', $cart->id)
                ->where('product_batch_id', $batch->id)
                ->where('is_gift', false)
                ->lockForUpdate()
                ->first();

            $newQty = $qty + (float) ($existing->quantity ?? 0);
            $available = $batch->availableAt($locationId);

            if ($newQty > $available) {
                throw new RuntimeException($available > 0
                    ? "Only {$this->trimQty($available)} left in stock."
                    : 'This product is currently out of stock.');
            }

            $this->writeItem($cart, $batch, $existing, $newQty, $locationId);
            $cart->recalcTotal();

            return $cart->fresh();
        });
    }

    public function updateItem(int $itemId, float $qty): Cart
    {
        return DB::transaction(function () use ($itemId, $qty) {
            $cart = $this->lockCurrent();
            $item = CartItem::where('cart_id', $cart->id)->where('id', $itemId)->lockForUpdate()->firstOrFail();

            if ($qty <= 0) {
                $item->delete();
                $cart->recalcTotal();

                return $cart->fresh();
            }

            $batch = ProductBatch::lockForUpdate()->findOrFail($item->product_batch_id);
            $available = $batch->availableAt($this->locationId());

            if ($qty > $available) {
                throw new RuntimeException($available > 0
                    ? "Only {$this->trimQty($available)} left in stock."
                    : 'This product is currently out of stock.');
            }

            $this->writeItem($cart, $batch, $item, $qty, (int) $item->location_id);
            $cart->recalcTotal();

            return $cart->fresh();
        });
    }

    public function removeItem(int $itemId): Cart
    {
        $cart = $this->lockCurrent();
        CartItem::where('cart_id', $cart->id)->where('id', $itemId)->delete();
        $cart->recalcTotal();

        return $cart->fresh();
    }

    private function writeItem(Cart $cart, ProductBatch $batch, ?CartItem $item, float $qty, int $locationId): CartItem
    {
        $isCustomer = (bool) Auth::guard('customer')->check();
        $unitPrice = $batch->calculatePrice($qty, $isCustomer);
        $original = (float) $batch->original_sell_price;
        $discountPerUnit = max(0, $original - $unitPrice);

        $payload = [
            'unit' => $batch->unit,
            'quantity' => $qty,
            'unit_price' => $unitPrice,
            'discount_amount' => round($discountPerUnit * $qty, 4),
            'discount_percent' => $original > 0 ? round($discountPerUnit / $original * 100, 2) : 0,
            'total_price' => round($unitPrice * $qty, 4),
        ];

        if ($item) {
            $item->fill($payload);
            $item->save();

            return $item;
        }

        $primary = $batch->product->images->firstWhere('is_primary', true) ?? $batch->product->images->first();

        return CartItem::create($payload + [
            'cart_id' => $cart->id,
            'product_id' => $batch->product_id,
            'product_batch_id' => $batch->id,
            'product_image_id' => $primary?->id,
            'location_id' => $locationId,
            'price_type' => 'retail',
            'is_gift' => false,
        ]);
    }

    private function trimQty(float $qty): string
    {
        return rtrim(rtrim(number_format($qty, 2, '.', ''), '0'), '.');
    }
}
