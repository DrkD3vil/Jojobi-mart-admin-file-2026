<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

/**
 * Suggests catalog products a customer could redeem outright with their
 * current reward-points balance -- surfaced as a nudge on the cart page and
 * account dashboard so points don't just sit unused.
 */
class RewardRecommendationService
{
    // Points -> currency conversion rate used to size the redeemable budget.
    // Keep this in sync with CartController::POINT_RATE (both the admin and
    // frontend apps redeem against the same `reward_points` balance on the
    // shared `customers` table -- a drift here wouldn't break checkout
    // itself, since CartController::applyRewards() is the source of truth
    // there, but it would make these recommendations wrong).
    private const POINT_RATE = 1.0;

    /**
     * Products with at least one active/online batch the customer could
     * fully cover using only their points. Affordability is checked against
     * ProductBatch::displayPrice() (the same calculatePrice()-derived price
     * shown everywhere else in the storefront) rather than the raw
     * `sell_price` column, since discounted/tiered pricing can make that
     * column misleading.
     */
    public function forCustomer(Customer $customer, int $limit = 4): Collection
    {
        $maxAffordable = (float) $customer->reward_points * self::POINT_RATE;

        if ($maxAffordable <= 0) {
            return new Collection();
        }

        return Product::active()->online()
            ->with([
                'images',
                'batches' => fn ($q) => $q->where('is_active', true)->where('is_online', true)->orderByDesc('sell_price'),
            ])
            ->inRandomOrder()
            ->get()
            ->filter(fn (Product $product) => $product->batches->contains(
                fn ($batch) => $batch->displayPrice() <= $maxAffordable
            ))
            ->take($limit)
            ->values();
    }
}
