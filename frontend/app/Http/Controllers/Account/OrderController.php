<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Auth::guard('customer')->user()->orders()->withCount('items')->latest();

        $channel = $request->query('channel');
        if ($channel === 'online') {
            $query->where(function ($q) {
                $q->where('channel', 'online')
                    ->orWhere(function ($q2) {
                        $q2->whereNull('channel')->whereNotNull('shipping_address');
                    });
            });
        } elseif ($channel === 'in_store') {
            $query->where(function ($q) {
                $q->where('channel', 'pos')
                    ->orWhere(function ($q2) {
                        $q2->whereNull('channel')->whereNull('shipping_address');
                    });
            });
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('account.orders', compact('orders', 'channel'));
    }

    public function show(Order $order)
    {
        abort_unless($order->customer_id === Auth::guard('customer')->id(), 403);

        $order->load(['items.product.images', 'payments', 'location', 'timeline']);

        return view('account.order-show', compact('order'));
    }
}
