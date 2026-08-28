<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomerConflictException;
use App\Models\Order;
use App\Services\CartService;
use App\Services\CustomerMatcher;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use RuntimeException;

class CheckoutController extends Controller
{
    public function __construct(private CartService $cartService, private OrderService $orderService, private CustomerMatcher $customerMatcher)
    {
    }

    public function index()
    {
        $cart = $this->cartService->peek();
        $cart?->load(['items.product', 'items.batch']);

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Your cart is empty.']);
        }

        $customer = Auth::guard('customer')->user();

        return view('checkout.index', [
            'cart' => $cart,
            'customer' => $customer,
            'paymentMethods' => config('store.payment_methods'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string|max:1000',
            'note' => 'nullable|string|max:1000',
            'payment_method' => 'required|string|in:cod,bkash,nagad,rocket',
            'trx_id' => 'required_unless:payment_method,cod|nullable|string|max:80',
        ]);

        $resolvedCustomerId = null;

        if (!Auth::guard('customer')->check()) {
            try {
                $customer = $this->customerMatcher->resolve(
                    $data['name'],
                    $data['phone'],
                    $data['email'] ?? null,
                    passwordHash: null,
                    isRegistration: false
                );
                $resolvedCustomerId = $customer->id;
            } catch (CustomerConflictException $e) {
                return back()->withInput()->withErrors([
                    'checkout' => $e->getMessage() . ' Please log in instead.',
                ]);
            }
        }

        try {
            $order = $this->orderService->placeOrder([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'note' => $data['note'] ?? null,
                'trx_id' => $data['trx_id'] ?? null,
            ], $data['payment_method'], $resolvedCustomerId);
        } catch (RuntimeException $e) {
            return back()->withInput()->withErrors(['checkout' => $e->getMessage()]);
        }

        // A guest has no session guard to prove ownership with on the
        // success page below, so remember this order's id for this browser
        // session specifically (same idea as a signed order-tracking link,
        // scoped to "whoever's session just placed it").
        if (!Auth::guard('customer')->check()) {
            $request->session()->push('guest_order_ids', $order->id);
        }

        return redirect()->route('checkout.success', $order->order_no);
    }

    // A logged-in customer's receipt just needs to belong to them. A guest
    // has no account to check against, so ownership instead means "this
    // browser session is the one that placed it" (see store() above).
    public function success(string $orderNo)
    {
        $order = Order::where('order_no', $orderNo)->with(['items', 'payments'])->firstOrFail();

        $customerId = Auth::guard('customer')->id();
        $ownsAsGuest = !$customerId && in_array($order->id, session('guest_order_ids', []), true);

        abort_unless($order->customer_id === $customerId || $ownsAsGuest, 403);

        // Guests have no account to view order history in, so hand them a
        // shareable, signed tracking link instead (see OrderTrackController
        // + the /track/{order} route). Logged-in customers already have
        // account.orders.show for this.
        $trackUrl = $ownsAsGuest
            ? URL::signedRoute('track.show', ['order' => $order->id])
            : null;

        return view('checkout.success', compact('order', 'trackUrl'));
    }
}
