<?php

use App\Http\Controllers\AccessKeyMappingController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CartGiftAuditController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\ProductBatchController;
use App\Http\Controllers\ProductStatusController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExchangeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Report\FinancialDashboardController;
use App\Http\Controllers\Report\FinancialTodayDashboardController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\ReturnWizardController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\StockLedgerController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\OrderSplitController;
use App\Http\Controllers\PrivilegeController;
use App\Http\Controllers\ProductGiftController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authenticated + Access Controlled Routes
|--------------------------------------------------------------------------
| All routes inside require login + pass tyro.access middleware.
*/

Route::middleware(['auth', 'tyro.access'])->group(function () {

    /*
    |----------------------------------------------------------------------
    | Profile
    |----------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/me', [ProfileController::class, 'me'])->name('me');

    Route::get('/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('password.change.form');
    Route::post('/change-password', [ProfileController::class, 'changePassword'])->name('password.change');


    /*
    |----------------------------------------------------------------------
    | RBAC Core: Roles + Privileges + User Roles + Access Keys
    |----------------------------------------------------------------------
    */

    // Roles
    Route::group(['defaults' => ['access_key' => 'roles']], function () {
        Route::resource('roles', RoleController::class);
    });

    // Privileges
    Route::group(['defaults' => ['access_key' => 'privileges']], function () {
        Route::resource('privileges', PrivilegeController::class);
    });

    // User Roles pages
    Route::group(['defaults' => ['access_key' => 'user_roles']], function () {

        Route::get('/user_roles', [UserRoleController::class, 'index'])->name('user.roles.index');

        Route::get('/user_roles/assign-multiple', [UserRoleController::class, 'assignMultipleRolesForm'])
            ->name('user.roles.assign_multiple');

        Route::post('/user_roles/assign-multiple', [UserRoleController::class, 'assignMultipleRoles'])
            ->name('user.roles.assign_multiples');

        Route::get('/user/{userId}/roles', [UserRoleController::class, 'show'])->name('user.roles.show');
        Route::post('/user/{userId}/roles', [UserRoleController::class, 'store'])->name('user.roles.store');

        Route::delete('/user/{userId}/roles/{roleId}', [UserRoleController::class, 'destroy'])->name('user.roles.destroy');

        Route::post('/role/{roleId}/privileges', [UserRoleController::class, 'assignPrivilegesToRole'])
            ->name('role.privileges.assign');

        Route::delete('/role/{roleId}/privileges/{privilegeId}', [UserRoleController::class, 'removePrivilegeFromRole'])
            ->name('role.privileges.remove');
    });

    // ✅ Access Keys (your new RBAC mapping UI)
// Access Keys Mapping Routes
Route::group(['defaults' => ['access_key' => 'rbac']], function () {
    Route::get('/access-keys', [AccessKeyMappingController::class, 'index'])->name('access_keys.index');
    Route::post('/access-keys', [AccessKeyMappingController::class, 'store'])->name('access_keys.store');
    Route::delete('/access-keys/{id}', [AccessKeyMappingController::class, 'destroy'])->name('access_keys.destroy');
    Route::get('/access-keys/search', [AccessKeyMappingController::class, 'search'])->name('access_keys.search');
    Route::post('/access-keys/bulk-destroy', [AccessKeyMappingController::class, 'bulkDestroy'])->name('access_keys.bulk_destroy');
});


    /*
    |----------------------------------------------------------------------
    | Categories
    |----------------------------------------------------------------------
    */
    Route::group(['defaults' => ['access_key' => 'categories']], function () {
        Route::prefix('categories')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
            Route::get('/create', [CategoryController::class, 'create'])->name('categories.create');
            Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
            Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
            Route::put('/{category}', [CategoryController::class, 'update'])->name('categories.update');
            Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

            Route::post('/generate-barcode', [CategoryController::class, 'generateNewBarcode'])->name('categories.generate.barcode');
            Route::get('/search', [CategoryController::class, 'ajaxSearch'])->name('categories.ajax.search');
            Route::delete('/categories/bulk-destroy', [CategoryController::class, 'bulkDestroy'])->name('categories.bulkDestroy');
        });
    });


    /*
    |----------------------------------------------------------------------
    | Brands
    |----------------------------------------------------------------------
    */
    Route::group(['defaults' => ['access_key' => 'brands']], function () {
        Route::resource('brands', BrandController::class);
        Route::post('/brands/{brand}/toggle-status', [BrandController::class, 'toggleStatus'])->name('brands.toggle-status');
        Route::post('/brands/search', [BrandController::class, 'search'])->name('brands.search');
    });


    /*
    |----------------------------------------------------------------------
    | Products
    |----------------------------------------------------------------------
    */
    Route::group(['defaults' => ['access_key' => 'products']], function () {

        Route::get('/products/live-search', [ProductController::class, 'liveSearch'])->name('products.live-search');
        Route::get('/products/barcode/check', [ProductController::class, 'checkBarcode'])->name('products.barcode.check');

        Route::get('/products/trash', [ProductController::class, 'trash'])->name('products.trash');
        // Bulk restore and delete routes for deleted products
Route::post('/products/bulk-restore', [ProductController::class, 'bulkRestore'])->name('products.bulk-restore');
Route::post('/products/bulk-force-delete', [ProductController::class, 'bulkForceDelete'])->name('products.bulk-force-delete');
        Route::post('/products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
        Route::delete('/products/{id}/force', [ProductController::class, 'forceDelete'])->name('products.forceDelete');

        Route::resource('products', ProductController::class);
        Route::get('/batches/{batch}/barcode/print', [ProductController::class, 'barcodeprint']);
    });


    /*
    |----------------------------------------------------------------------
    | Product Images
    |----------------------------------------------------------------------
    */
    Route::group(['defaults' => ['access_key' => 'product_images']], function () {

        Route::get('/product-images/all', [ProductImageController::class, 'all'])->name('product.images.all');
        Route::get('/product-images/trash', [ProductImageController::class, 'trash'])->name('product-images.trash');
        Route::post('/product-images/{id}/restore', [ProductImageController::class, 'restore'])->name('product-images.restore');
        Route::delete('/product-images/{id}/force', [ProductImageController::class, 'forceDelete'])->name('product-images.forceDelete');

        Route::delete('/product-images/{id}', [ProductImageController::class, 'deleteById'])->name('product-images.deleteById');

        Route::post('/products/{product}/images/{image}/primary-toggle', [ProductImageController::class, 'togglePrimary'])
            ->name('product.images.togglePrimary');

        Route::prefix('products/{product}/images')->group(function () {
            Route::get('/', [ProductImageController::class, 'index'])->name('products.images.index');
            Route::post('/', [ProductImageController::class, 'store'])->name('products.images.store');
            Route::delete('/{image}', [ProductImageController::class, 'destroy'])->name('products.images.destroy');
            Route::post('/{image}/primary', [ProductImageController::class, 'setPrimary'])->name('products.images.primary');
        });
    });


    /*
    |----------------------------------------------------------------------
    | Product Batches
    |----------------------------------------------------------------------
    */
    Route::group(['defaults' => ['access_key' => 'product_batches']], function () {

        Route::prefix('product-batches')->group(function () {

            Route::get('/', [ProductBatchController::class, 'all'])->name('product.batches.all');
            Route::get('create/{product?}', [ProductBatchController::class, 'create'])->name('product.batches.create');
            Route::post('/', [ProductBatchController::class, 'store'])->name('product.batches.store');

            Route::get('/{batch}/edit', [ProductBatchController::class, 'edit'])->name('product.batches.edit');
            Route::put('/{batch}', [ProductBatchController::class, 'update'])->name('product.batches.update');
            Route::delete('/{batch}', [ProductBatchController::class, 'destroy'])->name('product.batches.destroy');

            Route::get('/products/search', [ProductBatchController::class, 'search'])->name('products.search');

            Route::get('/product/{product}', [ProductBatchController::class, 'indexByProduct'])->name('product.batches.by-product');
            Route::get('/view/{batch}', [ProductBatchController::class, 'show'])->name('product.batches.show');

            Route::get('/product-batches/trash', [ProductBatchController::class, 'trash'])->name('product-batches.trash');
            Route::post('/product-batches/{id}/restore', [ProductBatchController::class, 'restore'])->name('product-batches.restore');
            Route::delete('/product-batches/{id}/force', [ProductBatchController::class, 'forceDelete'])->name('product-batches.forceDelete');

            // JSON API
            Route::get('/api/products/search', [ProductBatchController::class, 'quickProductSearch'])->name('products.quick.search');
            Route::get('/api/gifts/search', [ProductBatchController::class, 'quickGiftProductSearch'])->name('products.gift.search');
            Route::get('/api/product/{product}/batches', [ProductBatchController::class, 'batchesJsonByProduct'])->name('product.batches.json.byProduct');
            Route::get('/api/batch/{batch}', [ProductBatchController::class, 'batchJson'])->name('product.batches.json.show');
        });
    });


    /*
    |----------------------------------------------------------------------
    | Product Status
    |----------------------------------------------------------------------
    */
    Route::group(['defaults' => ['access_key' => 'product_statuses']], function () {

        Route::get('product-statuses', [ProductStatusController::class, 'index'])->name('product-statuses.index');
        Route::get('product-statuses/create', [ProductStatusController::class, 'create'])->name('product-statuses.create');
        Route::post('product-statuses', [ProductStatusController::class, 'store'])->name('product-statuses.store');

        Route::delete('product-statuses/{productStatus}', [ProductStatusController::class, 'destroy'])->name('product.status.destroy');
        Route::post('/product/status/{status}/toggle', [ProductStatusController::class, 'toggleActive'])->name('product.status.toggle');

        Route::prefix('product-status')->group(function () {
            Route::get('/product-status', [ProductStatusController::class, 'index'])->name('product.status.index');
            Route::get('/create/{productUuid?}', [ProductStatusController::class, 'create'])->name('product.status.create');
            Route::post('/', [ProductStatusController::class, 'store'])->name('product.status.store');
            Route::get('/{uuid}/edit', [ProductStatusController::class, 'edit'])->name('product.status.edit');
            Route::put('/{uuid}', [ProductStatusController::class, 'update'])->name('product.status.update');
        });
    });


    /*
    |----------------------------------------------------------------------
    | Cart / POS
    |----------------------------------------------------------------------
    */
    Route::group(['defaults' => ['access_key' => 'pos']], function () {

        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::get('/cart/search', [CartController::class, 'search'])->name('cart.search');

        Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
        Route::post('/cart/item/update', [CartController::class, 'updateItem'])->name('cart.item.update');

        Route::delete('/cart/item/{item}', [CartController::class, 'removeItem'])->name('cart.item.remove');
        Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

        Route::post('/cart/customer/set', [CartController::class, 'setCustomer'])->name('cart.customer.set');
        Route::post('/cart/rewards/apply', [CartController::class, 'applyRewards'])->name('cart.rewards.apply');
        Route::post('/cart/rewards/clear', [CartController::class, 'clearRewards'])->name('cart.rewards.clear');
        Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

        Route::get('/products/quick-search', [CartController::class, 'quickProductSearch'])->name('products.quick.search.update');

        Route::post('/cart/gift/manual/add', [CartController::class, 'addManualGift'])->name('cart.manual.gift.add');
        Route::delete('/cart/gift/manual/{item}', [CartController::class, 'removeManualGift'])->name('cart.gift.manual.remove');

        Route::post('/pos/location', [CartController::class, 'setLocation'])->name('pos.location.set');
    });


    /*
    |----------------------------------------------------------------------
    | Gift Audit
    |----------------------------------------------------------------------
    */
    Route::group(['defaults' => ['access_key' => 'gift_audit']], function () {
        Route::get('/product/gift-audit', [ProductGiftController::class, 'index'])->name('product.gift-audit');
        Route::get('/product/gift-audit/export', [ProductGiftController::class, 'export'])->name('product.gift-audit.export');
        Route::post('/product/gift-audit/toggle-status/{batch}', [ProductGiftController::class, 'updateStatus'])->name('product.gift-audit.toggle-status');
    });


    /*
    |----------------------------------------------------------------------
    | Customers
    |----------------------------------------------------------------------
    */
    Route::group(['defaults' => ['access_key' => 'customers']], function () {

        // Route::get('/customers', function () {
        //     return view('customers.index');
        // })->name('customers.index');

        // Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
        // Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
        // Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');

        // Route::post('/customers/{customer}/balance', [CustomerController::class, 'postBalance'])->name('customers.balance.post');
        // Route::post('/customers/{customer}/rewards', [CustomerController::class, 'postRewards'])->name('customers.rewards.post');

        // Route::get('/customers/quick/search', [CustomerController::class, 'quickSearch'])->name('customers.quick.search');

        // Customer routes
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    // Balance & Rewards
    Route::post('/customers/{customer}/balance', [CustomerController::class, 'postBalance'])->name('customers.balance.post');
    Route::post('/customers/{customer}/rewards', [CustomerController::class, 'postRewards'])->name('customers.rewards.post');

    // Quick search
    Route::get('/customers/quick/search', [CustomerController::class, 'quickSearch'])->name('customers.quick.search');

    // Export
    Route::get('/customers/export', [CustomerController::class, 'export'])->name('customers.export');

    // Stats API
    Route::get('/customers/stats', [CustomerController::class, 'getStats'])->name('customers.stats');
    });


    // /*
    // |----------------------------------------------------------------------
    // | Orders / Invoice / Payments
    // |----------------------------------------------------------------------
    // */
    // Route::group(['defaults' => ['access_key' => 'orders']], function () {

    //     Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    //     Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    //     Route::get('/orders/ajax/index', [OrderController::class, 'ajaxIndex'])->name('orders.ajax.index');



    //     Route::get('/orders/{order}/payment', [PaymentController::class, 'create'])->name('payments.create');
    //     Route::post('/orders/{order}/payment', [PaymentController::class, 'store'])->name('payments.store');
    //     Route::get('/orders/{order}/payments', [PaymentController::class, 'index'])->name('payments.index');

    //     Route::get('/invoice/{order}', [InvoiceController::class, 'show'])->name('invoice.show');
    // });


    /*
|----------------------------------------------------------------------
| Orders / Invoice / Payments
|----------------------------------------------------------------------
*/
/*
|----------------------------------------------------------------------
| Orders / Invoice / Payments
|----------------------------------------------------------------------
*/
// Route::group(['defaults' => ['access_key' => 'orders']], function () {

//     // Main order routes
//     Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
//     Route::get('/orders/trash', [OrderController::class, 'trashIndex'])->name('orders.trash');
//     Route::post('/orders/{id}/restore', [OrderController::class, 'restore'])->name('orders.restore');


//     Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
//     Route::get('/orders/ajax/index', [OrderController::class, 'ajaxIndex'])->name('orders.ajax.index');

//     // Status-based order routes
//     Route::get('/orders/status/pending', [OrderController::class, 'pending'])->name('orders.pending');
//     Route::get('/orders/status/completed', [OrderController::class, 'completed'])->name('orders.completed');
//     Route::get('/orders/status/paid', [OrderController::class, 'paid'])->name('orders.paid');
//     Route::get('/orders/status/refunded', [OrderController::class, 'refunded'])->name('orders.refunded');
//     Route::get('/orders/status/returned', [OrderController::class, 'returned'])->name('orders.returned');
//     Route::get('/orders/status/cancelled', [OrderController::class, 'cancelled'])->name('orders.cancelled');

//     // Trash routes
//    // Trash routes (must come before /orders/{order})
//    // Trash routes
//     Route::post('/orders/{order}/trash', [OrderController::class, 'trash'])->name('orders.trash.move');
//     Route::post('/orders/restore-multiple', [OrderController::class, 'restoreMultiple'])->name('orders.restore-multiple');
//     Route::delete('/orders/{id}/force-delete', [OrderController::class, 'forceDelete'])->name('orders.force-delete');
//     Route::delete('/orders/force-delete-multiple', [OrderController::class, 'forceDeleteMultiple'])->name('orders.force-delete-multiple');
//     Route::delete('/orders/empty-trash', [OrderController::class, 'emptyTrash'])->name('orders.empty-trash');


//     // Payment routes
//     Route::get('/orders/{order}/payment', [PaymentController::class, 'create'])->name('payments.create');
//     Route::post('/orders/{order}/payment', [PaymentController::class, 'store'])->name('payments.store');
//     Route::get('/orders/{order}/payments', [PaymentController::class, 'index'])->name('payments.index');

//     // Invoice route
//     Route::get('/invoice/{order}', [InvoiceController::class, 'show'])->name('invoice.show');
// });

Route::prefix('orders')->group(function () {

    // Edit page
    Route::get('{order}/edit', [OrderController::class, 'edit'])
        ->name('orders.edit')
        ->where('order', '[0-9]+');

    // Update order
    Route::put('{order}', [OrderController::class, 'update'])
        ->name('orders.update')
        ->where('order', '[0-9]+');

    // Get order data (AJAX)
    Route::get('{order}/data', [OrderController::class, 'getOrderData'])
        ->name('orders.data')
        ->where('order', '[0-9]+');

    // Get order items (AJAX)
    Route::get('{order}/items', [OrderController::class, 'getItems'])
        ->name('orders.items')
        ->where('order', '[0-9]+');

    // ============================================
    // ORDER ITEM MANAGEMENT
    // ============================================

    // Add item to order
    Route::post('{order}/item', [OrderController::class, 'addItem'])
        ->name('order.add.item')
        ->where('order', '[0-9]+');

    // Update order item
    Route::post('{order}/item/update', [OrderController::class, 'updateItem'])
        ->name('order.item.update')
        ->where('order', '[0-9]+');

    // Remove item from order
    Route::delete('{order}/item/{itemId}', [OrderController::class, 'removeItem'])
        ->name('order.item.remove')
        ->where('order', '[0-9]+')
        ->where('itemId', '[0-9]+');

    // Clear all items from order
    Route::delete('{order}/items/clear', [OrderController::class, 'clearItems'])
        ->name('order.clear.items')
        ->where('order', '[0-9]+');

    // ============================================
    // GIFT MANAGEMENT
    // ============================================

    // Add manual gift
    Route::post('{order}/gift/manual', [OrderController::class, 'addManualGift'])
        ->name('order.gift.manual.add')
        ->where('order', '[0-9]+');

    // Remove manual gift
    Route::delete('{order}/gift/manual/{itemId}', [OrderController::class, 'removeManualGift'])
        ->name('order.gift.manual.remove')
        ->where('order', '[0-9]+')
        ->where('itemId', '[0-9]+');
});



Route::group(['defaults' => ['access_key' => 'orders']], function () {

    // ===== MAIN ORDER ROUTES =====
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/trash', [OrderController::class, 'trashIndex'])->name('orders.trash');
    Route::post('/orders/{id}/restore', [OrderController::class, 'restore'])->name('orders.restore');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    // Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::get('/orders/ajax/index', [OrderController::class, 'ajaxIndex'])->name('orders.ajax.index');



    // AJAX search endpoint
    Route::get('/ajax/search', [OrderController::class, 'ajaxSearch'])->name('orders.ajax.search');





    // ===== ORDER PRINT =====
    Route::get('/orders/{id}/print', [OrderController::class, 'print'])->name('orders.print');




     // Split management
    // Order Split Routes
    Route::get('{order}/split', [OrderSplitController::class, 'index'])
        ->name('orders.split')
        ->where('order', '[0-9]+');

    Route::post('{order}/split/preview', [OrderSplitController::class, 'preview'])
        ->name('orders.split.preview')
        ->where('order', '[0-9]+');

    Route::post('{order}/split/execute', [OrderSplitController::class, 'split'])
        ->name('orders.split.execute')
        ->where('order', '[0-9]+');

    Route::get('{order}/split/history', [OrderSplitController::class, 'history'])
        ->name('orders.split.history')
        ->where('order', '[0-9]+');

    Route::post('{parentOrder}/split/merge/{childOrder}', [OrderSplitController::class, 'merge'])
        ->name('orders.split.merge')
        ->where('parentOrder', '[0-9]+')
        ->where('childOrder', '[0-9]+');




    // ===== STATUS-BASED ORDER ROUTES =====
    Route::get('/orders/status/pending', [OrderController::class, 'pending'])->name('orders.pending');
    Route::get('/orders/status/processing', [OrderController::class, 'processing'])->name('orders.processing');
    Route::get('/orders/status/completed', [OrderController::class, 'completed'])->name('orders.completed');
    Route::get('/orders/status/paid', [OrderController::class, 'paid'])->name('orders.paid');
    Route::get('/orders/status/refunded', [OrderController::class, 'refunded'])->name('orders.refunded');
    Route::get('/orders/status/returned', [OrderController::class, 'returned'])->name('orders.returned');
    Route::get('/orders/status/cancelled', [OrderController::class, 'cancelled'])->name('orders.cancelled');

    // ===== ORDER ACTION ROUTES =====
    Route::post('/orders/{id}/process', [OrderController::class, 'process'])->name('orders.process');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{id}/complete', [OrderController::class, 'complete'])->name('orders.complete');
    Route::post('/orders/{id}/refund', [OrderController::class, 'refund'])->name('orders.refund');

    // ===== TRASH ROUTES =====
    Route::post('/orders/{order}/trash', [OrderController::class, 'trash'])->name('orders.trash.move');
    Route::post('/orders/restore-multiple', [OrderController::class, 'restoreMultiple'])->name('orders.restore-multiple');
    Route::delete('/orders/{id}/force-delete', [OrderController::class, 'forceDelete'])->name('orders.force-delete');
    Route::delete('/orders/force-delete-multiple', [OrderController::class, 'forceDeleteMultiple'])->name('orders.force-delete-multiple');
    Route::delete('/orders/empty-trash', [OrderController::class, 'emptyTrash'])->name('orders.empty-trash');

    // ===== PAYMENT ROUTES =====
    Route::get('/orders/{order}/payment', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/orders/{order}/payment', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/orders/{order}/payments', [PaymentController::class, 'index'])->name('payments.index');

    // ===== INVOICE ROUTE =====
    Route::get('/invoice/{order}', [InvoiceController::class, 'show'])->name('invoice.show');
});


    /*
    |----------------------------------------------------------------------
    | Locations
    |----------------------------------------------------------------------
    */
    Route::group(['defaults' => ['access_key' => 'locations']], function () {
        Route::get('/locations/ajax', [LocationController::class, 'ajaxIndex'])->name('locations.ajax');
        Route::patch('/locations/{location}/toggle', [LocationController::class, 'toggleActive'])->name('locations.toggle');
        Route::delete('/locations/{location}/ajax-delete', [LocationController::class, 'ajaxDestroy'])->name('locations.ajaxDestroy');
        Route::resource('locations', LocationController::class);
    });


    /*
    |----------------------------------------------------------------------
    | Returns / Exchange
    |----------------------------------------------------------------------
    */
    Route::group(['defaults' => ['access_key' => 'returns']], function () {

        Route::get('/returns/create', [ReturnController::class, 'create'])->name('returns.create');
        Route::post('/returns', [ReturnController::class, 'store'])->name('returns.store');

        Route::get('/returns/wizard', [ReturnWizardController::class, 'index'])->name('returns.wizard');
        Route::get('/returns/wizard/order', [ReturnWizardController::class, 'searchOrder'])->name('returns.wizard.order');
        Route::get('/returns/wizard/customer', [ReturnWizardController::class, 'searchCustomer'])->name('returns.wizard.customer');
        Route::get('/returns/wizard/select-order', [ReturnWizardController::class, 'selectOrder'])->name('returns.wizard.selectOrder');

        Route::get('/returns/wizard/ajax/customers', [ReturnWizardController::class, 'ajaxCustomers'])->name('returns.wizard.ajax.customers');
        Route::get('/returns/wizard/ajax/orders', [ReturnWizardController::class, 'ajaxOrders'])->name('returns.wizard.ajax.orders');
        Route::get('/returns/wizard/ajax/order-items', [ReturnWizardController::class, 'ajaxOrderItems'])->name('returns.wizard.ajax.orderItems');

        Route::get('/exchanges/create', [ExchangeController::class, 'create'])->name('exchanges.create');
        Route::post('/exchanges', [ExchangeController::class, 'store'])->name('exchanges.store');
        Route::get('/exchanges/ajax/orders', [ExchangeController::class, 'ajaxOrders'])->name('exchanges.ajax.orders');
        Route::get('/exchanges/ajax/order-items', [ExchangeController::class, 'ajaxOrderItems'])->name('exchanges.ajax.orderItems');
        Route::get('/exchanges/ajax/batches', [ExchangeController::class, 'ajaxBatches'])->name('exchanges.ajax.batches');
        Route::get('/exchanges/ajax/availability', [ExchangeController::class, 'ajaxAvailability'])->name('exchanges.ajax.availability');
    });


    /*
    |----------------------------------------------------------------------
    | Transfers / Stock Ledger
    |----------------------------------------------------------------------
    */
    Route::group(['defaults' => ['access_key' => 'stock']], function () {
        Route::get('/transfers/create', [StockTransferController::class, 'create'])->name('transfers.create');
        Route::post('/transfers', [StockTransferController::class, 'store'])->name('transfers.store');
        Route::get('/transfers/ajax/batches', [StockTransferController::class, 'ajaxBatches'])->name('transfers.ajax.batches');
        Route::get('/transfers/ajax/availability', [StockTransferController::class, 'ajaxAvailability'])->name('transfers.ajax.availability');

        Route::get('/stock-ledger', [StockLedgerController::class, 'index'])->name('stock-ledger.index');
        Route::get('/stock-ledger/ajax', [StockLedgerController::class, 'ajaxIndex'])->name('stock-ledger.ajax');
    });


    /*
    |----------------------------------------------------------------------
    | Dashboards
    |----------------------------------------------------------------------
    */

    Route::middleware(['auth', 'tyro.access'])->group(function () {

    Route::get('/', [FinancialTodayDashboardController::class, 'index'])
        ->name('dashboard.financial.today');

});



    Route::group(['defaults' => ['access_key' => 'dashboard']], function () {

        Route::get('/dashboard/financial/analysis', [FinancialDashboardController::class, 'index'])->name('dashboard.financial');

        Route::get('/dashboard/financial/metrics', [FinancialDashboardController::class, 'metrics'])->name('dashboard.financial.metrics');
        Route::get('/dashboard/financial/charts', [FinancialDashboardController::class, 'charts'])->name('dashboard.financial.charts');
        Route::get('/dashboard/financial/tables', [FinancialDashboardController::class, 'tables'])->name('dashboard.financial.tables');

        Route::get('/dashboard/financial/realtime', [FinancialDashboardController::class, 'realTime'])->name('dashboard.financial.realtime');
        Route::get('/dashboard/financial/stream', [FinancialDashboardController::class, 'stream'])->name('dashboard.financial.stream');

        Route::post('/dashboard/financial/export', [FinancialDashboardController::class, 'export'])->name('dashboard.financial.export');

        // Route::get('/', [FinancialTodayDashboardController::class, 'index'])->name('dashboard.financial.today');
        Route::get('/dashboard/financial/today/data', [FinancialTodayDashboardController::class, 'data'])->name('dashboard.financial.today.data');
        Route::get('/dashboard/financial/today/realtime', [FinancialTodayDashboardController::class, 'realTime'])->name('dashboard.financial.today.realtime');
        Route::get('/dashboard/financial/today/stream', [FinancialTodayDashboardController::class, 'stream'])->name('dashboard.financial.today.stream');
        Route::get('/dashboard/financial/today/recent-orders', [FinancialTodayDashboardController::class, 'recentOrders'])->name('dashboard.financial.today.recent_orders');
    });


    /*
    |----------------------------------------------------------------------
    | Expenses
    |----------------------------------------------------------------------
    */
    Route::group(['defaults' => ['access_key' => 'expenses']], function () {

        Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
        Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
        Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
        Route::get('/expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
        Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
        Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

        Route::get('/expenses-trash', [ExpenseController::class, 'trash'])->name('expenses.trash');
        Route::post('/expenses-trash/{id}/restore', [ExpenseController::class, 'restore'])->name('expenses.restore');
        Route::delete('/expenses-trash/{id}/force', [ExpenseController::class, 'forceDelete'])->name('expenses.forceDelete');
        Route::post('/expenses-trash/empty', [ExpenseController::class, 'emptyTrash'])->name('expenses.emptyTrash');

        Route::get('/expenses-export/csv', [ExpenseController::class, 'exportCsv'])->name('expenses.export.csv');
    });

});
