<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::query()
            ->with('customer:id,name,phone')
            ->select(['id', 'order_no', 'customer_id', 'payable_total', 'status', 'created_at'])
            ->latest('id')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * AJAX list for Orders index (search/filter/pagination)
     */
    public function ajaxIndex(Request $request)
    {
        $q = trim((string)$request->query('q', ''));
        $min = $request->query('min_total', null);
        $max = $request->query('max_total', null);

        $orders = Order::query()
            ->with('customer:id,name,phone')
            ->select(['id','order_no','customer_id','payable_total','status','created_at'])
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('order_no', 'like', "%{$q}%");
                    if (ctype_digit($q)) $w->orWhere('id', (int)$q);
                    $w->orWhereHas('customer', function ($c) use ($q) {
                        $c->where('name', 'like', "%{$q}%")
                          ->orWhere('phone', 'like', "%{$q}%");
                    });
                });
            })
            ->when($min !== null && $min !== '', fn($qq) => $qq->where('payable_total', '>=', (float)$min))
            ->when($max !== null && $max !== '', fn($qq) => $qq->where('payable_total', '<=', (float)$max))
            ->latest('id')
            ->paginate(20);

        $rows = $orders->getCollection()->map(function ($o) {
            return [
                'id' => $o->id,
                'order_no' => $o->order_no,
                'customer_name' => $o->customer?->name ?? 'Guest',
                'customer_phone' => $o->customer?->phone ?? '',
                'payable_total' => (float)$o->payable_total,
                'created_at' => (string)$o->created_at,
                'status' => (string)$o->status,
                'show_url' => route('orders.show', $o),
            ];
        })->values();

        return response()->json([
            'rows' => $rows,
            'count_on_page' => $rows->count(),
            'meta' => "Showing {$orders->count()} of {$orders->total()} (page {$orders->currentPage()} / {$orders->lastPage()})",
            'pagination_html' => $orders->links()->render(),
        ]);
    }

    public function show(Order $order)
    {
        $order->load([
            'customer:id,name,phone',
            'items:id,order_id,product_id,product_batch_id,product_name,barcode,price_type,unit_price,quantity,discount_amount,total_price,returned_qty,returned_amount,note'
        ]);

        // exchange return summary by order_item_id
        $exchangeReturn = DB::table('exchange_lines as el')
            ->join('exchanges as e', 'e.id', '=', 'el.exchange_id')
            ->where('e.order_id', $order->id)
            ->where('e.status', 'POSTED')
            ->where('el.mode', 'RETURN')
            ->selectRaw('el.order_item_id, SUM(el.qty) as qty')
            ->groupBy('el.order_item_id')
            ->pluck('qty', 'order_item_id');

        // exchange issue list (optional UI)
        $exchangeIssue = DB::table('exchange_lines as el')
            ->join('exchanges as e', 'e.id', '=', 'el.exchange_id')
            ->where('e.order_id', $order->id)
            ->where('e.status', 'POSTED')
            ->where('el.mode', 'ISSUE')
            ->orderBy('el.id')
            ->get([
                'el.product_id',
                'el.product_batch_id',
                'el.qty',
                'el.unit_price',
            ]);

        return view('orders.show', compact('order', 'exchangeReturn', 'exchangeIssue'));
    }

    /**
     * Create Order from cart
     */
    public function storeFromCart()
    {
        return DB::transaction(function () {

            $cart = Cart::query()
                ->where('session_id', session()->getId())
                ->whereNull('payable_total')
                ->with(['items.product:id,name,barcode', 'customer:id,name,phone'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($cart->items->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Cart empty'], 422);
            }

            // ensure cart total is correct
            $cart->recalcTotal();

            $order = Order::create([
                'order_no' => 'ORD-' . now()->format('YmdHis') . '-' . random_int(100, 999),
                'session_id' => $cart->session_id,
                'customer_id' => $cart->customer_id,
                'subtotal' => $cart->total,
                'discount_total' => $cart->rewards_amount_used,
                'payable_total' => $cart->payable_total ?? $cart->total,
                'rewards_points_used' => $cart->rewards_points_used,
                'rewards_amount_used' => $cart->rewards_amount_used,
                'status' => 'completed',
            ]);

            // bulk insert (faster)
            $rows = [];
            foreach ($cart->items as $item) {
                $rows[] = [
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_batch_id' => $item->product_batch_id,
                    'product_name' => $item->product?->name,
                    'barcode' => $item->product?->barcode,
                    'price_type' => $item->price_type,
                    'unit_price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'unit' => $item -> unit,
                    'discount_amount' => $item->discount_amount ?? 0,
                    'total_price' => $item->total_price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            OrderItem::query()->insert($rows);

            // clear cart items (but keep cart history)
            CartItem::query()->where('cart_id', $cart->id)->delete();

            $cart->update([
                'total' => 0,
                'payable_total' => null,
                'customer_id' => null,
                'rewards_points_used' => 0,
                'rewards_amount_used' => 0,
            ]);

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'order_no' => $order->order_no,
            ]);
        });
    }
}
