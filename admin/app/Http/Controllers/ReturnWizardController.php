<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReturnWizardController extends Controller
{
    /**
     * Main wizard page
     * It can show:
     * - search forms
     * - customer list
     * - customer orders
     * - selected order items
     */
    public function index(Request $request)
    {
        $locations = \App\Models\Location::query()->where('is_active', true)->get();

        // Optional: passed data via query
        $order = null;
        $orderItems = collect();
        $customers = collect();
        $customer = null;
        $customerOrders = collect();

        // If order_id exists -> load items
        if ($request->filled('order_id')) {
            $order = Order::with(['customer', 'items'])->find($request->integer('order_id'));
            if ($order) {
                $orderItems = $order->items->map(function ($it) {
                    $sold = (float)$it->quantity;
                    $returned = (float)($it->returned_qty ?? 0);
                    $returnable = max(0, $sold - $returned);

                    return [
                        'id' => $it->id,
                        'product_id' => $it->product_id,
                        'product_batch_id' => $it->product_batch_id,
                        'product_name' => $it->product_name,
                        'barcode' => $it->barcode,
                        'qty_sold' => $sold,
                        'qty_returned' => $returned,
                        'qty_returnable' => $returnable,
                        'unit_price' => (float)$it->unit_price,
                    ];
                });
            }
        }

        // If customer_id exists -> load orders
        if ($request->filled('customer_id')) {
            $customer = Customer::find($request->integer('customer_id'));
            if ($customer) {
                $customerOrders = Order::query()
                    ->where('customer_id', $customer->id)
                    ->latest()
                    ->limit(50)
                    ->get();
            }
        }

        return view('returns.wizard_web', compact(
            'locations',
            'order',
            'orderItems',
            'customers',
            'customer',
            'customerOrders'
        ));
    }

    /**
     * Option 1: Search order by order_no using web GET
     */
    public function searchOrder(Request $request)
    {
        $orderNo = trim((string)$request->query('order_no', ''));
        if ($orderNo === '') {
            return redirect()->route('returns.wizard')->with('err', 'Order no required');
        }

        $order = Order::query()
            ->where('order_no', $orderNo)
            ->first();

        if (!$order) {
            return redirect()->route('returns.wizard')->with('err', 'Order not found: ' . $orderNo);
        }

        // Redirect to wizard with order_id to load items
        return redirect()->route('returns.wizard', ['order_id' => $order->id]);
    }

    /**
     * Option 2: Search customer by phone/name using web GET
     */
    public function searchCustomer(Request $request)
    {
        $q = trim((string)$request->query('q', ''));
        if ($q === '') {
            return redirect()->route('returns.wizard')->with('err', 'Customer search query required');
        }

        $customers = Customer::query()
            ->where(function ($qq) use ($q) {
                $qq->where('phone', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'phone']);

        $locations = \App\Models\Location::query()->where('is_active', true)->get();

        // Render same page with customer list
        $order = null;
        $orderItems = collect();
        $customer = null;
        $customerOrders = collect();

        return view('returns.wizard_web', compact(
            'locations',
            'customers',
            'order',
            'orderItems',
            'customer',
            'customerOrders'
        ))->with('customerQuery', $q);
    }

    /**
     * Select customer order (web GET)
     * /returns/wizard/select-order?order_id=xx&customer_id=yy
     */
    public function selectOrder(Request $request)
    {
        $customerId = $request->integer('customer_id');
        $orderId = $request->integer('order_id');

        if (!$customerId || !$orderId) {
            return redirect()->route('returns.wizard')->with('err', 'Select customer and order');
        }

        // Redirect to wizard with both: shows customer orders + selected order items
        return redirect()->route('returns.wizard', [
            'customer_id' => $customerId,
            'order_id' => $orderId,
        ]);
    }

    public function ajaxCustomers(Request $request): JsonResponse
    {
        $q = trim((string)$request->query('q', ''));
        if ($q === '') {
            return response()->json(['data' => []]);
        }

        $customers = Customer::query()
            ->where(function ($qq) use ($q) {
                $qq->where('phone', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'phone']);

        return response()->json([
            'data' => $customers,
        ]);
    }

    public function ajaxOrders(Request $request): JsonResponse
    {
        $q = trim((string)$request->query('q', ''));
        if ($q === '') {
            return response()->json(['data' => []]);
        }

        $orders = Order::query()
            ->with(['customer:id,name,phone'])
            ->where(function ($qq) use ($q) {
                // match order number
                $qq->where('order_no', 'like', "%{$q}%")
                    // OR match customer phone/name via relation
                    ->orWhereHas('customer', function ($cq) use ($q) {
                        $cq->where('phone', 'like', "%{$q}%")
                            ->orWhere('name', 'like', "%{$q}%");
                    });
            })
            ->latest()
            ->limit(20)
            ->get(['id', 'order_no', 'customer_id', 'status', 'created_at']);

        // shape response a bit for UI
        $data = $orders->map(function ($o) {
            return [
                'id' => $o->id,
                'order_no' => $o->order_no ?? ('Order #' . $o->id),
                'status' => $o->status,
                'date' => optional($o->created_at)->format('M d, Y'),
                'customer' => $o->customer ? [
                    'id' => $o->customer->id,
                    'name' => $o->customer->name,
                    'phone' => $o->customer->phone,
                ] : null,
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function ajaxOrderItems(Request $request): JsonResponse
    {
        $orderId = (int)$request->query('order_id', 0);
        if (!$orderId) {
            return response()->json(['message' => 'order_id required'], 422);
        }

        $order = Order::with(['customer:id,name,phone', 'items'])->find($orderId);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $items = $order->items->map(function ($it) {
            $sold = (float)$it->quantity;
            $returned = (float)($it->returned_qty ?? 0);
            $returnable = max(0, $sold - $returned);

            return [
                'id' => $it->id,
                'product_id' => $it->product_id,
                'product_batch_id' => $it->product_batch_id,
                'product_name' => $it->product_name,
                'barcode' => $it->barcode,
                'qty_sold' => $sold,
                'qty_returned' => $returned,
                'qty_returnable' => $returnable,
                'unit_price' => (float)$it->unit_price,
            ];
        })->values();

        return response()->json([
            'order' => [
                'id' => $order->id,
                'order_no' => $order->order_no ?? ('Order #' . $order->id),
                'status' => $order->status,
                'date' => optional($order->created_at)->format('M d, Y'),
                'customer' => $order->customer ? [
                    'id' => $order->customer->id,
                    'name' => $order->customer->name,
                    'phone' => $order->customer->phone,
                ] : null,
            ],
            'items' => $items,
        ]);
    }
}
