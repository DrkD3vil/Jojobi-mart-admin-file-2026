<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Analytics scoped to channel=online orders only -- how the storefront is
 * performing, separate from the till. Same cache-remembered-aggregate
 * pattern as FinancialDashboardController, just over a narrower slice.
 */
class EcommerceDashboardController extends Controller
{
    private const TTL = 60;

    public function index()
    {
        return view('ecommerce.dashboard');
    }

    public function metrics(): JsonResponse
    {
        $data = Cache::remember('ecommerce_dashboard:metrics', self::TTL, function () {
            $base = Order::onlineChannel();

            $total = (clone $base)->count();
            $revenue = (clone $base)->whereNotIn('status', ['cancelled'])->sum('payable_total');
            $avgOrder = $total > 0 ? $revenue / max(1, (clone $base)->whereNotIn('status', ['cancelled'])->count()) : 0;

            return [
                'total_orders' => $total,
                'pending' => (clone $base)->where('status', 'pending')->count(),
                'processing' => (clone $base)->where('status', 'processing')->count(),
                'completed' => (clone $base)->where('status', 'completed')->count(),
                'cancelled' => (clone $base)->where('status', 'cancelled')->count(),
                'revenue' => (float) $revenue,
                'avg_order_value' => round((float) $avgOrder, 2),
            ];
        });

        return response()->json($data);
    }

    public function charts(): JsonResponse
    {
        $data = Cache::remember('ecommerce_dashboard:charts', self::TTL, function () {
            $since = now()->subDays(13)->startOfDay();

            $daily = Order::onlineChannel()
                ->where('created_at', '>=', $since)
                ->selectRaw('DATE(created_at) as d, COUNT(*) as orders, SUM(payable_total) as revenue')
                ->groupBy('d')
                ->orderBy('d')
                ->get()
                ->keyBy('d');

            $labels = [];
            $orders = [];
            $revenue = [];
            for ($i = 13; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $row = $daily->get($date);
                $labels[] = now()->subDays($i)->format('M j');
                $orders[] = $row ? (int) $row->orders : 0;
                $revenue[] = $row ? (float) $row->revenue : 0.0;
            }

            $topProducts = OrderItem::query()
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.channel', 'online')
                ->whereNotIn('orders.status', ['cancelled'])
                ->select('order_items.product_name')
                ->selectRaw('SUM(order_items.quantity) as qty, SUM(order_items.total_price) as revenue')
                ->groupBy('order_items.product_name')
                ->orderByDesc('qty')
                ->limit(5)
                ->get();

            return [
                'daily' => ['labels' => $labels, 'orders' => $orders, 'revenue' => $revenue],
                'top_products' => $topProducts->map(fn ($r) => [
                    'name' => $r->product_name,
                    'qty' => (float) $r->qty,
                    'revenue' => (float) $r->revenue,
                ])->values(),
            ];
        });

        return response()->json($data);
    }
}
