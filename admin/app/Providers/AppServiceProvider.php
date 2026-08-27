<?php

namespace App\Providers;

use App\Models\BatchStock;
use App\Models\Cart;
use App\Models\Exchange;
use App\Models\Expense;
use App\Models\Order;
use App\Models\OrderSplit;
use App\Models\Payment;
use App\Models\PrivilegeAccessKey;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductImage;
use App\Models\ProductReturn;
use App\Models\ProductStatus;
use App\Observers\BatchStockObserver;
use HasinHayder\Tyro\Models\Privilege;
use HasinHayder\Tyro\Models\Role;
use Illuminate\Auth\Events\Login;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        BatchStock::observe(BatchStockObserver::class);

        // session()->regenerate() (used throughout the login flow) only
        // rotates the session ID -- it does NOT clear the session's existing
        // data array. Any per-user session data written under an unscoped
        // (non-user-id-namespaced) key would otherwise survive into
        // whichever user logs in next on the same browser/cookie. The AI
        // assistant's chat state is now stored under a user-id-scoped key
        // (see GeminiAssistantService::historyKey()/pendingKey()), so this
        // is just a defense-in-depth sweep for any pre-existing session row
        // that still has the old, unscoped keys from before that fix.
        Event::listen(Login::class, function () {
            session()->forget(['ai_chat.history', 'ai_chat.pending']);
        });

        // Short, stable aliases for the `timelines` table's polymorphic
        // type column, decoupled from namespaces/class names.
        Relation::enforceMorphMap([
            'order' => Order::class,
            'payment' => Payment::class,
            'cart' => Cart::class,
            'return' => ProductReturn::class,
            'exchange' => Exchange::class,
            'order_split' => OrderSplit::class,
            'expense' => Expense::class,
            'access_key_mapping' => PrivilegeAccessKey::class,
        ]);

        /**
         * Share product count with all views
         */
        View::composer('*', function ($view) {
            $productCount = \Illuminate\Support\Facades\Cache::remember('product_count_active', 60, function () {
                return Product::where('is_active', true)->count();
            });
            $view->with('productCount', $productCount);
        });
        /**
         * Share product image count with all views
         */
        View::composer('*', function ($view) {
            $productImageCount = \Illuminate\Support\Facades\Cache::remember('product_image_count', 60, function () {
                return ProductImage::count();
            });
            $view->with('productImageCount', $productImageCount);
        });

        View::composer('*', function ($view) {
            $productBatchCount = \Illuminate\Support\Facades\Cache::remember('product_batch_count', 60, function () {
                return ProductBatch::count();
            });
            $view->with('productBatchCount', $productBatchCount);
        });

        // Share productStatusCount with all views
        View::composer('*', function ($view) {
            $productStatusCount = \Illuminate\Support\Facades\Cache::remember('product_status_count', 60, function () {
                return ProductStatus::count();
            });
            $view->with('productStatusCount', $productStatusCount);
        });

        View::composer('*', function ($view) {
            $navCounts = \Illuminate\Support\Facades\Cache::remember('nav_counts', 60, function () {
                return [
                    'returns' => \App\Models\ProductReturn::count(),
                    'exchanges' => \App\Models\Exchange::count(),
                    'transfers' => \App\Models\StockTransaction::where('type', 'TRANSFER')->count(),
                    'ledger' => \App\Models\StockLedger::count(),
                ];
            });
            $view->with('navCounts', $navCounts);
        });

        View::composer('*', function ($view) {
            // If you only want these on admin layouts, change '*' to 'layouts.*' etc.
            $roleCount = \Illuminate\Support\Facades\Cache::remember('role_count', 60, function () {
                return Role::count();
            });
            $privilegeCount = \Illuminate\Support\Facades\Cache::remember('privilege_count', 60, function () {
                return Privilege::count();
            });
            $view->with('roleCount', $roleCount);
            $view->with('privilegeCount', $privilegeCount);
        });
    }
}
