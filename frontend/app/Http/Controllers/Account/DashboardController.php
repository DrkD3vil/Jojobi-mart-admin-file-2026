<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Services\RewardRecommendationService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private RewardRecommendationService $rewardRecommendationService)
    {
    }

    public function index()
    {
        $customer = Auth::guard('customer')->user();
        $recentOrders = $customer->orders()->withCount('items')->latest()->take(5)->get();
        $wishlistCount = $customer->wishlist()->count();
        $recommendations = ($customer && (float) $customer->reward_points > 0)
            ? $this->rewardRecommendationService->forCustomer($customer)
            : collect();

        // `channel` is authoritative; legacy rows without a channel value
        // fall back to the shipping_address proxy (see Order::isOnline()).
        $onlineOrderCount = $customer->orders()
            ->where(function ($q) {
                $q->where('channel', 'online')
                    ->orWhere(function ($q2) {
                        $q2->whereNull('channel')->whereNotNull('shipping_address');
                    });
            })
            ->count();
        $inStoreOrderCount = $customer->orders()
            ->where(function ($q) {
                $q->where('channel', 'pos')
                    ->orWhere(function ($q2) {
                        $q2->whereNull('channel')->whereNull('shipping_address');
                    });
            })
            ->count();

        return view('account.dashboard', compact(
            'customer', 'recentOrders', 'wishlistCount', 'onlineOrderCount', 'inStoreOrderCount', 'recommendations'
        ));
    }
}
