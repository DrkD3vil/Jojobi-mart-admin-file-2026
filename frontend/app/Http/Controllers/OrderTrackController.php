<?php

namespace App\Http\Controllers;

use App\Models\Order;

/**
 * Public, no-login order tracking page for guest checkouts. Reached only via
 * the signed URL handed to a guest on the checkout success page (see
 * CheckoutController::success()). Mirrors admin's PublicOrderController --
 * the `signed` route middleware rejects any tampered URL, so this never
 * becomes a way to browse or enumerate other orders.
 */
class OrderTrackController extends Controller
{
    public function show(Order $order)
    {
        $order->load([
            'items',
            'payments',
        ]);

        $timeline = $order->timeline()->oldest('id')->get();

        return view('track.show', compact('order', 'timeline'));
    }
}
