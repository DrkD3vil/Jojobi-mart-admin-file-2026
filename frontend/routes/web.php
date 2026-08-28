<?php

use App\Http\Controllers\Account\DashboardController;
use App\Http\Controllers\Account\OrderController as AccountOrderController;
use App\Http\Controllers\Account\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\OtpVerificationController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredCustomerController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\OrderTrackController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
Route::get('/brands/{brand:slug}', [BrandController::class, 'show'])->name('brands.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/mini', [CartController::class, 'mini'])->name('cart.mini');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{item}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{item}', [CartController::class, 'remove'])->name('cart.remove');

// Redeeming points requires a logged-in customer (applyRewards enforces
// this); clearing is a harmless no-op either way, so both stay outside the
// auth:customer group like the rest of cart-building.
Route::post('/cart/rewards/apply', [CartController::class, 'applyRewards'])->name('cart.rewards.apply');
Route::post('/cart/rewards/clear', [CartController::class, 'clearRewards'])->name('cart.rewards.clear');

Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');

Route::middleware('guest:customer')->group(function () {
    Route::get('/register', [RegisteredCustomerController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredCustomerController::class, 'store']);
    Route::get('/register/verify', [OtpVerificationController::class, 'showRegister'])->name('register.verify');
    Route::post('/register/verify', [OtpVerificationController::class, 'verifyRegister'])->name('register.verify.submit');
    Route::post('/register/verify/resend', [OtpVerificationController::class, 'resendRegister'])->name('register.verify.resend');

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/login/verify', [OtpVerificationController::class, 'showLogin'])->name('login.verify');
    Route::post('/login/verify', [OtpVerificationController::class, 'verifyLogin'])->name('login.verify.submit');
    Route::post('/login/verify/resend', [OtpVerificationController::class, 'resendLogin'])->name('login.verify.resend');

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout')->middleware('auth:customer');

// Checkout is open to guests too -- CheckoutController resolves/claims a
// Customer row via CustomerMatcher for anyone not already logged in, so
// every order still ends up with a real customer_id and order history.
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

// The success page needs no auth of its own -- CheckoutController checks
// the order actually belongs to whoever is viewing it (or, for a guest,
// that this browser session is the one that just placed it).
Route::get('/checkout/success/{orderNo}', [CheckoutController::class, 'success'])->name('checkout.success');

// Guest order tracking (no account) -- reachable only via the signed link
// handed to a guest on the checkout success page. The `signed` middleware
// rejects any tampered URL, so this can't be used to browse other orders.
// Same convention as admin's public.order.show route.
Route::get('/track/{order}', [OrderTrackController::class, 'show'])
    ->name('track.show')
    ->middleware('signed');

Route::middleware('auth:customer')->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::post('/products/{product}/review', [ReviewController::class, 'store'])->name('reviews.store');

    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::get('/orders', [AccountOrderController::class, 'index'])->name('orders');
        Route::get('/orders/{order}', [AccountOrderController::class, 'show'])->name('orders.show');
    });
});
