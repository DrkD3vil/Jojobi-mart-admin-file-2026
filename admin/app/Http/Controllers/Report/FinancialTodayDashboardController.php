<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Location;
use App\Models\Product;
use App\Support\CartUnit;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinancialTodayDashboardController extends Controller
{
    private const CACHE_VERSION = 'today_v7_expense_profit_sell';
    private const TTL_PAYLOAD = 10;     // dashboard payload cache
    private const TTL_RT = 2;           // realtime counters cache
    private const TTL_LOC = 3600;       // locations cache
    private const TTL_GLOBAL = 600;     // global stats cache

    private const CACHE_KEY_LOCATIONS = 'fin:active_locations:' . self::CACHE_VERSION;
    private const CACHE_KEY_GLOBAL_STATS = 'fin:global_stats:' . self::CACHE_VERSION;

    private const RECENT_PER_PAGE = 10;

    private function todayRange(): array
    {
        return [now()->startOfDay(), now()->endOfDay()];
    }

    private function locationId(Request $r): ?int
    {
        return $r->filled('location_id') ? (int) $r->get('location_id') : null;
    }

    private function applyValidOrders($q)
    {
        // exclude cancelled/void
        return $q->whereNotIn('o.status', ['cancelled', 'canceled', 'void']);
    }

    private function applyValidExpenses($q)
    {
        // Expense uses SoftDeletes, query builder must exclude deleted rows manually
        return $q->whereNull('e.deleted_at');
    }

    private function cacheBase(?int $locationId): string
    {
        $day = now()->format('Ymd');
        return "fin:today:" . self::CACHE_VERSION . ":{$day}:" . ($locationId ?? 'all');
    }

    /* =========================
     | Realtime Counters
     ========================= */
    private function buildCounters(?int $locationId): array
    {
        $key = $this->cacheBase($locationId) . ':counters';

        return Cache::remember($key, self::TTL_RT, function () use ($locationId) {
            $pending = (int) DB::table('orders')
                ->where('status', 'pending')
                ->when($locationId, fn($q) => $q->where('location_id', $locationId))
                ->count();

            $abandoned = (int) DB::table('carts')
                ->where('created_at', '<', now()->subHours(24))
                ->count();

            $lowStock = (int) DB::table('batch_stocks as bs')
                ->when($locationId, fn($q) => $q->where('bs.location_id', $locationId))
                ->whereRaw('(bs.on_hand - bs.reserved) < 10')
                ->count();

            return [
                'pending_orders' => $pending,
                'abandoned_carts' => $abandoned,
                'low_stock_items' => $lowStock,
            ];
        });
    }

    /* =========================
     | Recent Orders Query (Paginated)
     ========================= */
    private function recentOrdersPaginator(?int $locationId, int $page = 1, int $perPage = self::RECENT_PER_PAGE)
    {
        [$start, $end] = $this->todayRange();

        return DB::table('orders as o')
            ->leftJoin('customers as c', 'o.customer_id', '=', 'c.id')
            ->whereBetween('o.created_at', [$start, $end])
            ->when($locationId, fn($q) => $q->where('o.location_id', $locationId))
            ->tap(fn($q) => $this->applyValidOrders($q))
            ->select([
                'o.id',
                'o.order_no',
                'o.customer_id',
                'o.created_at',
                'o.payable_total',
                'o.status',
                'o.payment_status',
                DB::raw('COALESCE(c.name, "Guest") as customer_name'),
            ])
            ->orderByDesc('o.created_at')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    private function normalizePaginator($paginator): array
    {
        return [
            'data' => collect($paginator->items())->map(fn($o) => [
                'id' => $o->id,
                'order_no' => $o->order_no,
                'customer_id' => $o->customer_id,
                'created_at' => $o->created_at,
                'payable_total' => (float) $o->payable_total,
                'status' => $o->status,
                'payment_status' => $o->payment_status,
                'customer' => ['name' => $o->customer_name ?? 'Guest'],
            ])->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ];
    }

    /* =========================
     | Strong Today Payload (with Expense + Sell Qty + Profit after Expense)
     ========================= */
    private function buildTodayPayload(?int $locationId, int $page = 1): array
    {
        $key = $this->cacheBase($locationId) . ":payload:page:{$page}";

        return Cache::remember($key, self::TTL_PAYLOAD, function () use ($locationId, $page) {
            [$start, $end] = $this->todayRange();

            // 1) Orders aggregation (gross sales from orders)
            $ordersAgg = DB::table('orders as o')
                ->whereBetween('o.created_at', [$start, $end])
                ->when($locationId, fn($q) => $q->where('o.location_id', $locationId))
                ->tap(fn($q) => $this->applyValidOrders($q))
                ->selectRaw('
                    COUNT(*) as total_orders,
                    COALESCE(SUM(o.payable_total),0) as gross_sales_order,
                    COALESCE(SUM(o.discount_total),0) as total_discounts,
                    COALESCE(AVG(o.payable_total),0) as avg_order_value
                ')
                ->first();

            $grossSalesOrder = (float) ($ordersAgg->gross_sales_order ?? 0);
            $totalOrders     = (int) ($ordersAgg->total_orders ?? 0);
            $totalDiscounts  = (float) ($ordersAgg->total_discounts ?? 0);
            $avgOrderValue   = (float) ($ordersAgg->avg_order_value ?? 0);

            // 2) Sales fallback (sum order_items.total_price)
            $grossSalesItems = (float) (DB::table('order_items as oi')
                ->join('orders as o', 'oi.order_id', '=', 'o.id')
                ->whereBetween('o.created_at', [$start, $end])
                ->when($locationId, fn($q) => $q->where('o.location_id', $locationId))
                ->tap(fn($q) => $this->applyValidOrders($q))
                ->selectRaw('COALESCE(SUM(oi.total_price),0) as items_sales')
                ->value('items_sales') ?? 0);

            $grossSales = $grossSalesOrder;
            if ($grossSales <= 0 && $grossSalesItems > 0) {
                $grossSales = $grossSalesItems;
                $avgOrderValue = $totalOrders > 0 ? ($grossSales / $totalOrders) : 0;
            }

            // 3) Payments (cash received)
            $paidAmount = (float) (DB::table('payments as p')
                ->join('orders as o', 'p.order_id', '=', 'o.id')
                ->where('p.status', 'completed')
                ->whereBetween('p.created_at', [$start, $end])
                ->when($locationId, fn($q) => $q->where('o.location_id', $locationId))
                ->tap(fn($q) => $this->applyValidOrders($q))
                ->selectRaw('COALESCE(SUM(p.amount),0) as paid_amount')
                ->value('paid_amount') ?? 0);

            $paymentMethods = DB::table('payments as p')
                ->join('orders as o', 'p.order_id', '=', 'o.id')
                ->where('p.status', 'completed')
                ->whereBetween('p.created_at', [$start, $end])
                ->when($locationId, fn($q) => $q->where('o.location_id', $locationId))
                ->tap(fn($q) => $this->applyValidOrders($q))
                ->groupBy('p.method')
                ->orderByDesc('total')
                ->get([
                    DB::raw('COALESCE(p.method, "Unknown") as method'),
                    DB::raw('COALESCE(SUM(p.amount), 0) as total'),
                    DB::raw('COUNT(*) as count'),
                ])->map(fn($r) => [
                    'method' => $r->method,
                    'total' => (float) $r->total,
                    'count' => (int) $r->count,
                ])->values();

            // 4) Paid Orders count (MySQL-safe)
            $paidOrdersCount = (int) DB::table('orders as o')
                ->joinSub(
                    DB::table('payments')
                        ->select('order_id', DB::raw('SUM(amount) as paid_sum'))
                        ->where('status', 'completed')
                        ->whereBetween('created_at', [$start, $end])
                        ->groupBy('order_id'),
                    'pp',
                    'pp.order_id',
                    '=',
                    'o.id'
                )
                ->whereBetween('o.created_at', [$start, $end])
                ->when($locationId, fn($q) => $q->where('o.location_id', $locationId))
                ->whereNotIn('o.status', ['cancelled', 'canceled', 'void'])
                ->whereRaw('pp.paid_sum >= o.payable_total')
                ->count();

            // 5) Refunds: today returns ONLY for today orders
            $returnsAgg = DB::table('returns as r')
                ->join('orders as o', 'r.order_id', '=', 'o.id')
                ->whereBetween('r.created_at', [$start, $end])
                ->whereBetween('o.created_at', [$start, $end])
                ->when($locationId, fn($q) => $q->where('r.location_id', $locationId))
                ->selectRaw('
                    COALESCE(SUM(r.refund_amount), 0) as total_refunds,
                    COUNT(*) as total_returns
                ')
                ->first();

            $totalRefunds = (float) ($returnsAgg->total_refunds ?? 0);
            $totalReturns = (int) ($returnsAgg->total_returns ?? 0);

            // 6) Exchanges
            $totalExchanges = (int) DB::table('exchanges as e')
                ->whereBetween('e.created_at', [$start, $end])
                ->when($locationId, fn($q) => $q->where('e.location_id', $locationId))
                ->count();

            // ✅ 6.5) Expenses (today by expense_date)
            $todayDate = $start->toDateString();

            $expensesAgg = DB::table('expenses as e')
                ->when($locationId, fn($q) => $q->where('e.location_id', $locationId))
                ->tap(fn($q) => $this->applyValidExpenses($q))
                ->whereDate('e.expense_date', $todayDate)
                ->selectRaw('
                    COALESCE(SUM(e.amount), 0) as total_expenses,
                    COUNT(*) as total_expense_rows
                ')
                ->first();

            $totalExpenses = (float) ($expensesAgg->total_expenses ?? 0);
            $totalExpenseRows = (int) ($expensesAgg->total_expense_rows ?? 0);

            $expenseBreakdownByCategory = DB::table('expenses as e')
                ->leftJoin('expense_categories as ec', 'e.expense_category_id', '=', 'ec.id')
                ->when($locationId, fn($q) => $q->where('e.location_id', $locationId))
                ->tap(fn($q) => $this->applyValidExpenses($q))
                ->whereDate('e.expense_date', $todayDate)
                ->groupBy('e.expense_category_id', 'ec.name')
                ->orderByDesc('total')
                ->get([
                    DB::raw('COALESCE(ec.name, "Uncategorized") as category'),
                    DB::raw('COALESCE(SUM(e.amount), 0) as total'),
                    DB::raw('COUNT(*) as count'),
                ])->map(fn($r) => [
                    'category' => $r->category,
                    'total' => (float) $r->total,
                    'count' => (int) $r->count,
                ])->values();

            $expenseBreakdownByMethod = DB::table('expenses as e')
                ->when($locationId, fn($q) => $q->where('e.location_id', $locationId))
                ->tap(fn($q) => $this->applyValidExpenses($q))
                ->whereDate('e.expense_date', $todayDate)
                ->groupBy('e.payment_method')
                ->orderByDesc('total')
                ->get([
                    DB::raw('COALESCE(e.payment_method, "Unknown") as method'),
                    DB::raw('COALESCE(SUM(e.amount), 0) as total'),
                    DB::raw('COUNT(*) as count'),
                ])->map(fn($r) => [
                    'method' => $r->method,
                    'total' => (float) $r->total,
                    'count' => (int) $r->count,
                ])->values();

            // 7) COGS: net qty after returns
            // Step 1: Retrieve the necessary data without unit conversion in the query
            $cogsQuery = DB::table('order_items as oi')
                ->join('orders as o', 'oi.order_id', '=', 'o.id')
                ->leftJoin('product_batches as pb', 'oi.product_batch_id', '=', 'pb.id')
                ->whereBetween('o.created_at', [$start, $end])
                ->when($locationId, fn($q) => $q->where('o.location_id', $locationId))
                ->tap(fn($q) => $this->applyValidOrders($q))
                ->selectRaw('
        oi.quantity, oi.returned_qty, pb.buy_price, oi.unit as sale_unit, pb.unit as batch_unit
    ');

            // Step 2: Calculate COGS in PHP by converting units
            $cogs = 0;
            $cogsQueryResults = $cogsQuery->get();

            foreach ($cogsQueryResults as $item) {
                $quantity = max($item->quantity - $item->returned_qty, 0);
                // Convert sale quantity to batch quantity
                $convertedQty = CartUnit::toBatchQty($quantity, $item->sale_unit, $item->batch_unit);
                $cogs += $convertedQty * $item->buy_price;
            }

            // Now `$cogs` contains the total COGS value
            $cogsValue = (float) $cogs;


            // ✅ Sold qty (net after returns)
            $soldQty = (float) (DB::table('order_items as oi')
                ->join('orders as o', 'oi.order_id', '=', 'o.id')
                ->whereBetween('o.created_at', [$start, $end])
                ->when($locationId, fn($q) => $q->where('o.location_id', $locationId))
                ->tap(fn($q) => $this->applyValidOrders($q))
                ->selectRaw('
                    COALESCE(SUM(GREATEST(COALESCE(oi.quantity,0) - COALESCE(oi.returned_qty,0), 0)), 0) as sold_qty
                ')
                ->value('sold_qty') ?? 0);

            // ✅ 8) Net sales & profit (stronger)
            // $netSales = $grossSales - $totalRefunds;


            $netSales = $grossSales;

            $grossProfit = $netSales - $cogsValue;
            $grossMargin = ($netSales > 0) ? round(($grossProfit / $netSales) * 100, 2) : 0;

            $netProfit = $grossProfit - $totalExpenses;
            $profitMargin = ($netSales > 0) ? round(($netProfit / $netSales) * 100, 2) : 0;

            $dueAmount = max(0, $netSales - $paidAmount);

            // 9) Customers + global stats
            $newCustomers = (int) DB::table('customers')
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $globalStats = Cache::remember(self::CACHE_KEY_GLOBAL_STATS, self::TTL_GLOBAL, function () {
                return [
                    'avg_due_balance' => (float) (DB::table('customers')->avg('due_balance') ?? 0),
                    'total_reward_points' => (float) (DB::table('customers')->sum('reward_points') ?? 0),
                ];
            });

            // 10) Stock + stock value
            $stockAgg = DB::table('batch_stocks as bs')
                ->when($locationId, fn($q) => $q->where('bs.location_id', $locationId))
                ->selectRaw('
                    COALESCE(SUM(bs.on_hand), 0) as on_hand,
                    COALESCE(SUM(bs.reserved), 0) as reserved,
                    COALESCE(SUM(bs.on_hand - bs.reserved), 0) as available
                ')
                ->first();

            $stockCostValue = (float) (DB::table('batch_stocks as bs')
                ->join('product_batches as pb', 'bs.product_batch_id', '=', 'pb.id')
                ->when($locationId, fn($q) => $q->where('bs.location_id', $locationId))
                ->selectRaw('COALESCE(SUM((bs.on_hand - bs.reserved) * pb.buy_price), 0) as value')
                ->value('value') ?? 0);

            $lowStockItems = (int) DB::table('batch_stocks as bs')
                ->when($locationId, fn($q) => $q->where('bs.location_id', $locationId))
                ->whereRaw('(bs.on_hand - bs.reserved) < 10')
                ->count();

            // 11) Hourly sales chart
            $hourlySales = DB::table('orders as o')
                ->whereBetween('o.created_at', [$start, $end])
                ->when($locationId, fn($q) => $q->where('o.location_id', $locationId))
                ->tap(fn($q) => $this->applyValidOrders($q))
                ->selectRaw("
                    DATE_FORMAT(o.created_at, '%H:00') as hour,
                    COUNT(*) as orders,
                    COALESCE(SUM(o.payable_total), 0) as revenue
                ")
                ->groupBy(DB::raw("DATE_FORMAT(o.created_at, '%H:00')"))
                ->orderBy('hour')
                ->get()
                ->map(fn($r) => [
                    'date' => $r->hour,
                    'orders' => (int) $r->orders,
                    'revenue' => (float) $r->revenue,
                ]);

            // 12) Status distribution
            $statusDist = DB::table('orders as o')
                ->whereBetween('o.created_at', [$start, $end])
                ->when($locationId, fn($q) => $q->where('o.location_id', $locationId))
                ->selectRaw('o.status, COUNT(*) as count')
                ->groupBy('o.status')
                ->orderByDesc('count')
                ->get();

            // 13) Recent orders paginated ✅
            $paginator = $this->recentOrdersPaginator($locationId, $page, self::RECENT_PER_PAGE);
            $recentOrders = $this->normalizePaginator($paginator);
// 14) Top products (optional)
$topProductIds = DB::table('order_items as oi')
    ->join('orders as o', 'oi.order_id', '=', 'o.id')
    ->whereBetween('o.created_at', [$start, $end])
    ->when($locationId, fn($q) => $q->where('o.location_id', $locationId))
    ->tap(fn($q) => $this->applyValidOrders($q))
    ->select('oi.product_id')
    ->selectRaw('COALESCE(SUM(oi.total_price), 0) as total_revenue')
    ->groupBy('oi.product_id')
    ->orderByDesc('total_revenue')
    ->limit(10)
    ->pluck('product_id');

// Initialize the $topProducts collection
$topProducts = collect();

if ($topProductIds->isNotEmpty()) {
    // Fetch product details
    $products = Product::whereIn('id', $topProductIds)
        ->select(['id', 'name', 'category_id', 'brand_id'])
        ->with(['category:id,name', 'brand:id,name'])
        ->get()
        ->keyBy('id');

    // Now fetch the details for the top products and perform the required calculations
$topProducts = DB::table('order_items as oi')
    ->join('orders as o', 'oi.order_id', '=', 'o.id')
    ->leftJoin('product_batches as pb', 'oi.product_batch_id', '=', 'pb.id')
    ->whereBetween('o.created_at', [$start, $end])
    ->when($locationId, fn($q) => $q->where('o.location_id', $locationId))
    ->tap(fn($q) => $this->applyValidOrders($q))
    ->whereIn('oi.product_id', $topProductIds)
    ->select([
        'oi.product_id',
        DB::raw('COALESCE(SUM(GREATEST(COALESCE(oi.quantity,0)-COALESCE(oi.returned_qty,0),0)), 0) as total_qty'),
        DB::raw('COALESCE(SUM(oi.total_price), 0) as total_revenue'),
        'oi.unit as order_unit',
        'pb.unit as batch_unit',
        DB::raw('COALESCE(SUM(GREATEST(COALESCE(oi.quantity,0)-COALESCE(oi.returned_qty,0),0) * COALESCE(pb.buy_price,0)), 0) as total_cost')
    ])
    ->groupBy('oi.product_id', 'oi.unit', 'pb.unit')
    ->get()
    ->map(function ($i) use ($products) {
        // Retrieve the product data
        $product = $products->get($i->product_id);

        // Convert quantity based on order and batch units
        $convertedQty = CartUnit::toBatchQty($i->total_qty, $i->order_unit, $i->batch_unit);

        // Calculate total revenue, cost, and profit
        $rev = (float) $i->total_revenue;
        $updatedCost = (float) CartUnit::toBatchQty($i->total_cost, $i->order_unit, $i->batch_unit);
        // $cost = (float) $i->total_cost; // This is already the total cost based on quantities
        $cost = (float) $updatedCost;

        // Calculate profit and margin
        $profit = $rev - $cost;
        $margin = $rev > 0 ? round(($profit / $rev) * 100, 2) : 0;

        // Return the final processed data, including the converted quantity
        return [
            'product' => $product,
            'total_qty' => (float) $i->total_qty,  // Use the converted quantity
            'update_qty' => $convertedQty,
            'total_revenue' => $rev,
            'total_cost' => $cost, // Total cost calculated from query
            'profit' => $profit,
            'margin' => $margin,
        ];
    })
    ->sortByDesc('total_revenue')
    ->values();

}

            $counters = $this->buildCounters($locationId);

            return [
                'dashboard' => [
                    'total_sales' => $grossSales,
                    'total_orders' => $totalOrders,
                    'avg_order_value' => $avgOrderValue,
                    'total_discounts' => $totalDiscounts,

                    // ✅ sell qty
                    'sold_qty' => $soldQty,

                    // payments
                    'total_payments' => $paidAmount,
                    'orders_paid' => $paidOrdersCount,
                    'paid_amount' => $paidAmount,
                    'payment_methods_breakdown' => $paymentMethods,

                    // returns/exchanges
                    'total_refunds' => $totalRefunds,
                    'total_returns' => $totalReturns,
                    'total_exchanges' => $totalExchanges,

                    // profit (strong)
                    'net_sales' => $netSales,
                    'cost_of_goods_sold' => $cogsValue,

                    'gross_profit' => $grossProfit,
                    'gross_margin' => $grossMargin,

                    // ✅ expenses + final net profit
                    'expenses_total' => $totalExpenses,
                    'expenses_count' => $totalExpenseRows,
                    'expenses_by_category' => $expenseBreakdownByCategory,
                    'expenses_by_method' => $expenseBreakdownByMethod,

                    'net_profit' => $netProfit,
                    'profit_margin' => $profitMargin,

                    'due_amount' => $dueAmount,

                    // customers/global stats
                    'new_customers' => $newCustomers,
                    'total_reward_points' => (float) ($globalStats['total_reward_points'] ?? 0),
                    'avg_due_balance' => (float) ($globalStats['avg_due_balance'] ?? 0),

                    // stock
                    'available_stock' => (float) ($stockAgg->available ?? 0),
                    'total_reserved' => (float) ($stockAgg->reserved ?? 0),
                    'on_hand_stock' => (float) ($stockAgg->on_hand ?? 0),
                    'stock_cost_value' => $stockCostValue,
                    'low_stock_items' => $lowStockItems,

                    // charts / lists
                    'daily_sales' => $hourlySales,
                    'order_status_distribution' => $statusDist,

                    'recent_orders_paginated' => $recentOrders,

                    'top_products' => $topProducts,

                    'period' => [
                        'from' => $start->toDateString(),
                        'to' => $end->toDateString(),
                        'range' => 'today',
                    ],
                ],

                'pending_orders' => (int) ($counters['pending_orders'] ?? 0),
                'abandoned_carts' => (int) ($counters['abandoned_carts'] ?? 0),
                'low_stock_items' => (int) ($counters['low_stock_items'] ?? 0),

                'timestamp' => now()->toIso8601String(),
            ];
        });
    }

    /* =========================
     | Endpoints
     ========================= */

    public function index(Request $request)
    {
        $locationId = $this->locationId($request);
        $page = max(1, (int) $request->get('page', 1));

        $locations = Cache::remember(self::CACHE_KEY_LOCATIONS, self::TTL_LOC, function () {
            return Location::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        });

        $payload = $this->buildTodayPayload($locationId, $page);

        return view('reports.dashboard_today', [
            'locations'       => $locations,
            'locationId'      => $locationId,
            'dashboard'       => $payload['dashboard'],
            'pending_orders'  => $payload['pending_orders'],
            'recent_orders' => $payload['dashboard']['recent_orders_paginated'],
            'abandoned_carts' => $payload['abandoned_carts'],
            'low_stock_items' => $payload['low_stock_items'],
            'timestamp'       => $payload['timestamp'],
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $locationId = $this->locationId($request);
        $page = max(1, (int) $request->get('page', 1));

        $payload = $this->buildTodayPayload($locationId, $page);

        return response()->json($payload)
            ->header('Cache-Control', 'private, max-age=10')
            ->header('X-Cache-As-Of', $payload['timestamp']);
    }

    // ✅ only recent orders HTML/JSON for pagination fast
    public function recentOrders(Request $request): JsonResponse
    {
        $locationId = $this->locationId($request);
        $page = max(1, (int) $request->get('page', 1));

        $paginator = $this->recentOrdersPaginator($locationId, $page, self::RECENT_PER_PAGE);

        return response()->json([
            'recent_orders_paginated' => $this->normalizePaginator($paginator),
            'timestamp' => now()->toIso8601String(),
        ])->header('Cache-Control', 'private, max-age=5');
    }

    public function realTime(Request $request): JsonResponse
    {
        $locationId = $this->locationId($request);
        $since = $request->get('since');

        $counters = $this->buildCounters($locationId);

        $newOrders = 0;
        if ($since) {
            try {
                $sinceTime = Carbon::parse($since);
                $newOrders = DB::table('orders')
                    ->where('created_at', '>', $sinceTime)
                    ->when($locationId, fn($q) => $q->where('location_id', $locationId))
                    ->count();
            } catch (\Throwable $e) {
                $newOrders = 0;
            }
        }

        return response()->json([
            'new_orders'      => (int) $newOrders,
            'pending_orders'  => (int) ($counters['pending_orders'] ?? 0),
            'abandoned_carts' => (int) ($counters['abandoned_carts'] ?? 0),
            'low_stock_items' => (int) ($counters['low_stock_items'] ?? 0),
            'timestamp'       => now()->toIso8601String(),
        ])->header('Cache-Control', 'private, no-store, max-age=0');
    }

    public function stream(Request $request): StreamedResponse
    {
        $locationId = $this->locationId($request);

        return response()->stream(function () use ($locationId) {
            @ini_set('zlib.output_compression', '0');
            @ini_set('output_buffering', 'off');
            @ini_set('implicit_flush', '1');

            while (ob_get_level() > 0) {
                @ob_end_flush();
            }
            @ob_implicit_flush(true);

            $lastCheck = now()->subSeconds(5);

            for ($i = 0; $i < 600; $i++) {
                if (connection_aborted()) break;

                $counters = $this->buildCounters($locationId);

                $newOrders = DB::table('orders')
                    ->where('created_at', '>', $lastCheck)
                    ->when($locationId, fn($q) => $q->where('location_id', $locationId))
                    ->count();

                $payload = [
                    'new_orders'      => (int) $newOrders,
                    'pending_orders'  => (int) ($counters['pending_orders'] ?? 0),
                    'abandoned_carts' => (int) ($counters['abandoned_carts'] ?? 0),
                    'low_stock_items' => (int) ($counters['low_stock_items'] ?? 0),
                    'timestamp'       => now()->toIso8601String(),
                ];

                echo "event: counters\n";
                echo "data: " . json_encode($payload) . "\n\n";

                echo "event: ping\n";
                echo "data: " . json_encode(['t' => time()]) . "\n\n";

                $lastCheck = now();
                sleep(3);
            }
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache, no-store, must-revalidate',
            'Pragma'            => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
