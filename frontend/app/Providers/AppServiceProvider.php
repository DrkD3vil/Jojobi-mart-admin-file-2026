<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderSplit;
use App\Models\Payment;
use App\Models\Wishlist;
use App\Services\CartService;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Must match the admin app's morph map exactly (see admin's
        // AppServiceProvider::boot()) -- both apps read/write the same
        // `timelines` table, keyed by these short aliases rather than raw
        // class names, and only for models that carry HasTimeline on both
        // sides. Without this, frontend's Order::timeline() (and Cart's /
        // Payment's / OrderSplit's) silently returns nothing: Eloquent
        // falls back to querying `timelineable_type = 'App\Models\Order'`
        // (frontend's own FQCN) while every row admin writes is stamped
        // with the short alias 'order' instead, so the two never match.
        Relation::enforceMorphMap([
            'order' => Order::class,
            'payment' => Payment::class,
            'cart' => Cart::class,
            'order_split' => OrderSplit::class,
        ]);

        View::composer(['partials.header', 'partials.mobile-menu'], function ($view) {
            $customerId = Auth::guard('customer')->id();

            $view->with([
                'navCategories' => Category::active()->whereNull('parent_id')->orderBy('name')->get(),
                'cartCount' => app(CartService::class)->count(),
                'wishlistCount' => $customerId ? Wishlist::where('customer_id', $customerId)->count() : 0,
            ]);
        });
    }
}
