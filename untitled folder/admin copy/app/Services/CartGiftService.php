<?php

namespace App\Services;

use App\Models\BatchStock;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductBatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// class CartGiftService
// {
//     /**
//      * Sync gifts based on batch offer fields:
//      * product_batches.free_product_id, free_buy_qty, free_qty, is_free_offer_active
//      *
//      * Returns: ['hints' => [...]]
//      */
//     // public function sync(Cart $cart): array
//     // {
//     //     $hints = [];

//     //     // Load items with batch
//     //     $cart->loadMissing(['items.batch']);

//     //     // 1) Remove old auto gifts that are no longer valid
//     //     $this->removeInvalidAutoGifts($cart);

//     //     // 2) Add/update gifts for eligible items
//     //     foreach ($cart->items as $item) {
//     //         if ($item->is_gift) continue; // ignore gift rows

//     //         $batch = $item->batch;
//     //         if (!$batch) continue;

//     //         // Offer must be active and have config
//     //         if (
//     //             !(bool)($batch->is_free_offer_active ?? false) ||
//     //             empty($batch->free_product_id) ||
//     //             empty($batch->free_buy_qty) ||
//     //             empty($batch->free_qty)
//     //         ) {
//     //             // If this paid item had gifts earlier, remove them
//     //             CartItem::where('cart_id', $cart->id)
//     //                 ->where('is_gift', true)
//     //                 ->where('gift_source', 'batch_offer')
//     //                 ->where('parent_cart_item_id', $item->id)
//     //                 ->delete();
//     //             continue;
//     //         }

//     //         $buyQty = (float) $batch->free_buy_qty;
//     //         $freeQty = (float) $batch->free_qty;

//     //         if ($buyQty <= 0 || $freeQty <= 0) continue;

//     //         $paidQty = (float) $item->quantity;
//     //         $times = (int) floor($paidQty / $buyQty);

//     //         // If not eligible, remove auto gift rows
//     //         if ($times <= 0) {
//     //             CartItem::where('cart_id', $cart->id)
//     //                 ->where('is_gift', true)
//     //                 ->where('gift_source', 'batch_offer')
//     //                 ->where('parent_cart_item_id', $item->id)
//     //                 ->delete();
//     //             continue;
//     //         }

//     //         $giftTotalQty = $times * $freeQty;

//     //         // Find a gift batch with stock (FIFO expiry)
//     //         $giftBatchId = ProductBatch::query()
//     //             ->where('product_id', (int) $batch->free_product_id)
//     //             ->where('is_active', true)
//     //             ->where('quantity', '>', 0)
//     //             ->orderByRaw('expiry_date is null')
//     //             ->orderBy('expiry_date', 'asc')
//     //             ->orderBy('id', 'asc')
//     //             ->value('id');

//     //         if (!$giftBatchId) {
//     //             // No stock for free product => remove existing auto gift and show hint
//     //             CartItem::where('cart_id', $cart->id)
//     //                 ->where('is_gift', true)
//     //                 ->where('gift_source', 'batch_offer')
//     //                 ->where('parent_cart_item_id', $item->id)
//     //                 ->delete();

//     //             $hints[] = "Gift out of stock for offer on {$item->product?->name}";
//     //             continue;
//     //         }

//     //         // Upsert the gift row linked to this paid item
//     //         $giftRow = CartItem::query()
//     //             ->where('cart_id', $cart->id)
//     //             ->where('is_gift', true)
//     //             ->where('gift_source', 'batch_offer')
//     //             ->where('parent_cart_item_id', $item->id)
//     //             ->first();

//     //         if ($giftRow) {
//     //             $giftRow->product_id = (int) $batch->free_product_id;
//     //             $giftRow->product_batch_id = (int) $giftBatchId;
//     //             $giftRow->quantity = (float) $giftTotalQty;
//     //             $giftRow->unit_price = 0;
//     //             $giftRow->total_price = 0;
//     //             $giftRow->discount_amount = 0;
//     //             $giftRow->discount_percent = null;
//     //             $giftRow->discount_label = 'Free Gift';
//     //             $giftRow->gift_source_id = (int) $batch->id; // or offer id
//     //             $giftRow->save();
//     //         } else {
//     //             CartItem::create([
//     //                 'cart_id' => $cart->id,
//     //                 'product_id' => (int) $batch->free_product_id,
//     //                 'product_batch_id' => (int) $giftBatchId,
//     //                 'product_image_id' => null,
//     //                 'price_type' => 'gift',
//     //                 'unit_price' => 0,
//     //                 'quantity' => (float) $giftTotalQty,
//     //                 'discount_amount' => 0,
//     //                 'discount_percent' => null,
//     //                 'discount_label' => 'Free Gift',
//     //                 'total_price' => 0,

//     //                 'is_gift' => true,
//     //                 'gift_source' => 'batch_offer',
//     //                 'gift_source_id' => (int) $batch->id,
//     //                 'parent_cart_item_id' => (int) $item->id,
//     //             ]);
//     //         }

//     //         $hints[] = "Offer applied: +{$giftTotalQty} gift for {$item->product?->name}";
//     //     }

//     //     return ['hints' => $hints];
//     // }

//     // private function removeInvalidAutoGifts(Cart $cart): void
//     // {
//     //     // Remove auto gifts whose parent paid item no longer exists
//     //     $paidIds = CartItem::where('cart_id', $cart->id)
//     //         ->where(function ($q) {
//     //             $q->whereNull('is_gift')->orWhere('is_gift', false);
//     //         })
//     //         ->pluck('id')
//     //         ->all();

//     //     CartItem::where('cart_id', $cart->id)
//     //         ->where('is_gift', true)
//     //         ->where('gift_source', 'batch_offer')
//     //         ->whereNotIn('parent_cart_item_id', $paidIds)
//     //         ->delete();
//     // }
// }








class CartGiftService
{
    /**
     * Sync gifts based on batch offer fields:
     * product_batches.free_product_id, free_buy_qty, free_qty, is_free_offer_active
     *
     * ✅ FIXED for your system:
     * - Stock comes from batch_stocks.on_hand (location wise)
     * - Uses qty_in_batch_unit for offer calculation
     * - Gift rows include location_id + qty_in_batch_unit + unit
     */
    public function sync(Cart $cart): array
    {
        $hints = [];

        // Ensure items + batch + product loaded
        $cart->loadMissing([
            'items.product:id,name,barcode',
            'items.batch:id,product_id,unit,free_product_id,free_buy_qty,free_qty,is_free_offer_active,expiry_date',
        ]);

        // Remove gifts whose parent row no longer exists
        $this->removeInvalidAutoGifts($cart);

        foreach ($cart->items as $paidItem) {
            if ((bool)($paidItem->is_gift ?? false)) continue;

            $batch = $paidItem->batch;
            if (!$batch) continue;

            // location id is mandatory in your POS flow; fallback session-less safe
            $locationId = $this->itemLocationId($paidItem) ?? 1;

            // Offer config validate
            if (
                !(bool)($batch->is_free_offer_active ?? false) ||
                empty($batch->free_product_id) ||
                empty($batch->free_buy_qty) ||
                empty($batch->free_qty)
            ) {
                $this->deleteGiftsForParent($cart->id, $paidItem->id, $locationId);
                continue;
            }

            $buyQty  = (float) $batch->free_buy_qty;
            $freeQty = (float) $batch->free_qty;

            if ($buyQty <= 0 || $freeQty <= 0) {
                $this->deleteGiftsForParent($cart->id, $paidItem->id, $locationId);
                continue;
            }

            // ✅ paid qty in BATCH UNIT (not sale unit)
            $paidQtyBatch = $this->itemQtyBatchUnit($paidItem);

            $times = (int) floor($paidQtyBatch / $buyQty);

            if ($times <= 0) {
                $this->deleteGiftsForParent($cart->id, $paidItem->id, $locationId);
                continue;
            }

            $desiredGiftQtyBatch = (float) ($times * $freeQty);

            // ✅ Find a gift batch WITH STOCK in this location (FIFO expiry)
            $giftBatch = ProductBatch::query()
                ->select('product_batches.*')
                ->join('batch_stocks as bs', 'bs.product_batch_id', '=', 'product_batches.id')
                ->where('product_batches.product_id', (int) $batch->free_product_id)
                ->where('product_batches.is_active', true)
                ->where('bs.location_id', $locationId)
                ->where('bs.on_hand', '>', 0)
                ->orderByRaw('product_batches.expiry_date is null')
                ->orderBy('product_batches.expiry_date', 'asc')
                ->orderBy('product_batches.id', 'asc')
                ->lockForUpdate()
                ->first();

            if (!$giftBatch) {
                $this->deleteGiftsForParent($cart->id, $paidItem->id, $locationId);
                $hints[] = "Gift out of stock (Location #{$locationId}) for offer on {$paidItem->product?->name}";
                continue;
            }

            // Clamp gift qty by remaining stock (consider reserved in this cart)
            $finalGiftQtyBatch = $this->clampGiftQtyByLocationStock(
                cartId: (int) $cart->id,
                giftBatchId: (int) $giftBatch->id,
                locationId: (int) $locationId,
                desiredGiftQtyBatch: (float) $desiredGiftQtyBatch,
                parentPaidItemId: (int) $paidItem->id
            );

            if ($finalGiftQtyBatch <= 0) {
                $this->deleteGiftsForParent($cart->id, $paidItem->id, $locationId);
                $hints[] = "Gift stock reserved/empty (Location #{$locationId}) for offer on {$paidItem->product?->name}";
                continue;
            }

            // Upsert gift row (must include location_id so your stock reserve sees it!)
            $giftRowQ = CartItem::query()
                ->where('cart_id', $cart->id)
                ->where('is_gift', true)
                ->where('gift_source', 'batch_offer')
                ->where('parent_cart_item_id', $paidItem->id);

            if (Schema::hasColumn('cart_items', 'location_id')) {
                $giftRowQ->where('location_id', $locationId);
            }

            $giftRow = $giftRowQ->lockForUpdate()->first();

            $giftUnit = (string) ($giftBatch->unit ?? 'pcs');

            if ($giftRow) {
                $giftRow->product_id       = (int) $giftBatch->product_id;
                $giftRow->product_batch_id = (int) $giftBatch->id;

                // Store gift qty as batch qty (simple & consistent)
                $giftRow->quantity = (float) $finalGiftQtyBatch;

                if (Schema::hasColumn('cart_items', 'qty_in_batch_unit')) {
                    $giftRow->qty_in_batch_unit = (float) $finalGiftQtyBatch;
                }
                if (Schema::hasColumn('cart_items', 'unit')) {
                    $giftRow->unit = $giftUnit;
                }
                if (Schema::hasColumn('cart_items', 'location_id')) {
                    $giftRow->location_id = $locationId;
                }

                $giftRow->price_type       = 'gift';
                $giftRow->unit_price       = 0;
                $giftRow->total_price      = 0;
                $giftRow->discount_amount  = 0;
                $giftRow->discount_percent = null;
                $giftRow->discount_label   = 'Free Gift';
                $giftRow->gift_source_id   = (int) $batch->id;
                $giftRow->save();
            } else {
                $payload = [
                    'cart_id' => (int) $cart->id,
                    'product_id' => (int) $giftBatch->product_id,
                    'product_batch_id' => (int) $giftBatch->id,
                    'product_image_id' => null,
                    'price_type' => 'gift',
                    'unit_price' => 0,
                    'quantity' => (float) $finalGiftQtyBatch,
                    'discount_amount' => 0,
                    'discount_percent' => null,
                    'discount_label' => 'Free Gift',
                    'total_price' => 0,

                    'is_gift' => true,
                    'gift_source' => 'batch_offer',
                    'gift_source_id' => (int) $batch->id,
                    'parent_cart_item_id' => (int) $paidItem->id,
                ];

                if (Schema::hasColumn('cart_items', 'location_id')) {
                    $payload['location_id'] = $locationId;
                }
                if (Schema::hasColumn('cart_items', 'qty_in_batch_unit')) {
                    $payload['qty_in_batch_unit'] = (float) $finalGiftQtyBatch;
                }
                if (Schema::hasColumn('cart_items', 'unit')) {
                    $payload['unit'] = $giftUnit;
                }

                CartItem::create($payload);
            }

            $msg = "Offer applied: +{$finalGiftQtyBatch} gift for {$paidItem->product?->name}";
            if ($finalGiftQtyBatch < $desiredGiftQtyBatch) $msg .= " (limited by stock)";
            $hints[] = $msg;
        }

        return ['hints' => $hints];
    }

    private function removeInvalidAutoGifts(Cart $cart): void
    {
        $paidIds = CartItem::where('cart_id', $cart->id)
            ->where(function ($q) {
                $q->whereNull('is_gift')->orWhere('is_gift', false);
            })
            ->pluck('id')
            ->all();

        CartItem::where('cart_id', $cart->id)
            ->where('is_gift', true)
            ->where('gift_source', 'batch_offer')
            ->whereNotIn('parent_cart_item_id', $paidIds)
            ->delete();
    }

    private function deleteGiftsForParent(int $cartId, int $parentCartItemId, int $locationId): void
    {
        $q = CartItem::where('cart_id', $cartId)
            ->where('is_gift', true)
            ->where('gift_source', 'batch_offer')
            ->where('parent_cart_item_id', $parentCartItemId);

        if (Schema::hasColumn('cart_items', 'location_id')) {
            $q->where('location_id', $locationId);
        }

        $q->delete();
    }

    private function itemQtyBatchUnit(CartItem $item): float
    {
        if (Schema::hasColumn('cart_items', 'qty_in_batch_unit')) {
            return (float) ($item->qty_in_batch_unit ?? $item->quantity ?? 0);
        }
        return (float) ($item->quantity ?? 0);
    }

    private function itemLocationId(CartItem $item): ?int
    {
        if (Schema::hasColumn('cart_items', 'location_id')) {
            return $item->location_id ? (int) $item->location_id : null;
        }
        return null;
    }

    /**
     * Clamp gift qty by batch_stocks.on_hand for this location,
     * considering reserved in this cart for same gift batch.
     */
    private function clampGiftQtyByLocationStock(
        int $cartId,
        int $giftBatchId,
        int $locationId,
        float $desiredGiftQtyBatch,
        int $parentPaidItemId
    ): float {
        if ($desiredGiftQtyBatch <= 0) return 0;

        $stock = BatchStock::query()
            ->where('product_batch_id', $giftBatchId)
            ->where('location_id', $locationId)
            ->lockForUpdate()
            ->first();

        $onHand = $stock ? (float) $stock->on_hand : 0.0;
        if ($onHand <= 0) return 0;

        // Existing gift row for this parent (so we can "add back" when updating)
        $existingGiftQ = CartItem::query()
            ->where('cart_id', $cartId)
            ->where('is_gift', true)
            ->where('gift_source', 'batch_offer')
            ->where('parent_cart_item_id', $parentPaidItemId)
            ->where('product_batch_id', $giftBatchId);

        if (Schema::hasColumn('cart_items', 'location_id')) {
            $existingGiftQ->where('location_id', $locationId);
        }

        $existingGift = $existingGiftQ->first();
        $existingGiftQty = $existingGift ? $this->itemQtyBatchUnit($existingGift) : 0.0;

        // Reserved in this cart for this gift batch (paid+gift)
        $reservedQ = CartItem::query()
            ->where('cart_id', $cartId)
            ->where('product_batch_id', $giftBatchId);

        if (Schema::hasColumn('cart_items', 'location_id')) {
            $reservedQ->where('location_id', $locationId);
        }

        $reservedTotal = (float) $reservedQ->sum(DB::raw('COALESCE(qty_in_batch_unit, quantity)'));

        // remaining = onHand - reservedTotal + existingGiftQty
        $remaining = ($onHand - $reservedTotal) + $existingGiftQty;
        if ($remaining <= 0) return 0;

        return (float) min($desiredGiftQtyBatch, $remaining);
    }
}


