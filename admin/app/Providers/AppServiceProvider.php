<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductImage;
use App\Models\ProductStatus;
use HasinHayder\Tyro\Models\Privilege;
use HasinHayder\Tyro\Models\Role;
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
        //
        /**
         * Share product count with all views
         */
        View::composer('*', function ($view) {
            $view->with(
                'productCount',
                Product::where('is_active', true)->count()
            );
        });
        /**
         * Share product image count with all views
         */
        View::composer('*', function ($view) {
            $view->with(
                'productImageCount',
                ProductImage::count()
            );
        });

        View::composer('*', function ($view) {
            $view->with('productBatchCount', ProductBatch::count());
        });

        // Share productStatusCount with all views
        View::composer('*', function ($view) {
            $view->with('productStatusCount', ProductStatus::count());
        });

        View::composer('*', function ($view) {
            $view->with('navCounts', [
                'returns' => \App\Models\ProductReturn::count(),
                'exchanges' => \App\Models\Exchange::count(),
                'transfers' => \App\Models\StockTransaction::where('type', 'TRANSFER')->count(),
                'ledger' => \App\Models\StockLedger::count(),
            ]);
        });

        View::composer('*', function ($view) {
        // If you only want these on admin layouts, change '*' to 'layouts.*' etc.
        $view->with('roleCount', Role::count());
        $view->with('privilegeCount', Privilege::count());
        View::share('roleCount', Role::count());

    });
    }
}
