<?php

namespace App\Http\Controllers;

use App\Models\Order;

/**
 * Public, no-login order status page. Reached only via the signed URL
 * embedded in the QR code printed on a receipt (see InvoiceController::print()).
 * The `signed` route middleware rejects any URL whose signature doesn't
 * match, so this never becomes a way to browse or enumerate other orders.
 */
class PublicOrderController extends Controller
{
    public function show(Order $order)
    {
        $order->load([
            'customer:id,name,phone',
            'items:id,order_id,product_name,price_type,unit_price,quantity,discount_amount,total_price',
            'payments:id,order_id,channel,method,amount,created_at',
        ]);

        return view('public.order', compact('order'));
    }
}
