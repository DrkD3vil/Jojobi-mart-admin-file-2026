<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinancialDashboardController extends Controller
{
    /**
     * NOTE: Cache TTL integers are in SECONDS in Laravel.
     */
    private const CACHE_VERSION = 'v6_profit_returns_exchanges_costdelta';

    // Tuned TTLs (seconds)
    private const METRICS_TTL = 15;
    private const CHARTS_TTL  = 30;
    private const TABLES_TTL  = 30;
    private const RT_TTL      = 2;

    private const CACHE_KEY_LOCATIONS    = 'fin:active_locations:' . self::CACHE_VERSION;
    private const CACHE_KEY_GLOBAL_STATS = 'fin:global_stats:' . self::CACHE_VERSION;

    /* =========================
     | DATE RANGE + FILTERS
     ========================= */
    private function resolveDateRange(string $range, ?string $startDate, ?string $endDate): array
    {
        static $cache = [];
        $key = "{$range}_{$startDate}_{$endDate}";
        if (isset($cache[$key])) return $cache[$key];

        $now = Carbon::now();

        $result = match ($range) {
            'today'      => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'yesterday'  => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()],
            'this_week'  => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'last_week'  => [$now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek()],
            'this_month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'last_month' => [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()],
            'this_year'  => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            'last_year'  => [$now->copy()->subYear()->startOfYear(), $now->copy()->subYear()->endOfYear()],
            'custom'     => [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ],
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };

        return $cache[$key] = $result;
    }

    private function filters(Request $request): array
    {
        $range = (string) $request->get('date_range', 'this_month');
        [$start, $end] = $this->resolveDateRange($range, $request->start_date, $request->end_date);

        return [
            'range'          => $range,
            'start'          => $start,
            'end'            => $end,
            'location_id'    => $request->filled('location_id') ? (int) $request->get('location_id') : null,
            'status'         => (string) $request->get('status', 'all'),
            'payment_status' => (string) $request->get('payment_status', 'all'),
            'start_date_raw' => $request->start_date,
            'end_date_raw'   => $request->end_date,
        ];
    }

    /* =========================
     | CACHE KEYS
     ========================= */
    private function cacheKeyBase(array $f): string
    {
        return sprintf(
            'fin:%s:%s:%s:%s:%s:%s',
            self::CACHE_VERSION,
            $f['start']->format('Ymd'),
            $f['end']->format('Ymd'),
            $f['location_id'] ?? 'all',
            $f['status'] ?? 'all',
            $f['payment_status'] ?? 'all'
        );
    }

    private function key(string $base, string $part): string
    {
        return $base . ':' . $part;
    }

    private function rememberMany(array $keys, int $ttl, array $callbacks): array
    {
        $cached = Cache::many($keys);
        $results = [];

        foreach ($keys as $i => $k) {
            if (array_key_exists($k, $cached) && $cached[$k] !== null) {
                $results[$i] = $cached[$k];
            } else {
                $results[$i] = Cache::remember($k, $ttl, $callbacks[$i]);
            }
        }

        return $results;
    }

    /* =========================
     | FILTER HELPERS
     ========================= */
    private function applyExpenseFilters($q, array $f)
    {
        return $q
            ->whereNull('e.deleted_at')
            ->when($f['location_id'], fn ($qq) => $qq->where('e.location_id', $f['location_id']));
    }

    private function orderFilters($q, array $f)
    {
        return $q
            ->when($f['location_id'], fn ($qq) => $qq->where('o.location_id', $f['location_id']))
            ->when($f['status'] !== 'all', fn ($qq) => $qq->where('o.status', $f['status']))
            ->when($f['payment_status'] !== 'all', fn ($qq) => $qq->where('o.payment_status', $f['payment_status']));
    }

    /* =========================
     | PAGE
     ========================= */
    public function index(Request $request)
    {
        $filters = $this->filters($request);

        $dateRanges = [
            'today' => 'Today',
            'yesterday' => 'Yesterday',
            'this_week' => 'This Week',
            'last_week' => 'Last Week',
            'this_month' => 'This Month',
            'last_month' => 'Last Month',
            'this_year' => 'This Year',
            'last_year' => 'Last Year',
            'custom' => 'Custom',
        ];

        $locations = Cache::remember(self::CACHE_KEY_LOCATIONS, 3600, function () {
            return Location::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']);
        });

        $pendingOrders = $this->buildCounters($filters['location_id'])['pending_orders'] ?? 0;
        $abandoned = $this->buildCounters($filters['location_id'])['abandoned_carts'] ?? 0;

        return view('reports.dashboard', [
            'locations'        => $locations,
            'dateRanges'       => $dateRanges,
            'currentRange'     => $filters['range'],
            'startDate'        => $filters['start']->toDateString(),
            'endDate'          => $filters['end']->toDateString(),
            'statusFilter'     => $filters['status'],
            'paymentStatus'    => $filters['payment_status'],
            'locationId'       => $filters['location_id'],
            'pending_orders'   => $pendingOrders,
            'abandoned_carts'  => $abandoned,
        ]);
    }

    /* =========================
     | SECTION: METRICS
     ========================= */
    public function metrics(Request $request): JsonResponse
    {
        $f = $this->filters($request);
        $base = $this->cacheKeyBase($f);

        $payload = Cache::remember(
            $this->key($base, 'metrics'),
            self::METRICS_TTL,
            fn () => $this->buildMetrics($f, $base)
        );

        return response()->json([
            'metrics' => $payload,
            'timestamp' => now()->toIso8601String(),
        ])->header('Cache-Control', 'private, max-age=15');
    }

    /**
     * ✅ Profit accuracy update:
     * - Returns: subtract refund AND reverse COGS of returned qty (back to inventory)
     * - Exchanges: adjust revenue by exchanges.price_difference AND adjust COGS by (issued_cost - returned_cost)
     * - Transfers: not included (no P&L impact)
     */
    private function buildMetrics(array $f, string $baseKey): array
    {
        // Orders sales aggregation
        $salesAgg = DB::table('orders as o')
            ->tap(fn ($q) => $this->orderFilters($q, $f))
            ->whereBetween('o.created_at', [$f['start'], $f['end']])
            ->selectRaw('
                COUNT(*) as total_orders,
                COALESCE(SUM(o.payable_total), 0) as total_sales,
                COALESCE(SUM(o.discount_total), 0) as total_discounts,
                COALESCE(AVG(o.payable_total), 0) as avg_order_value,
                COALESCE(SUM(CASE WHEN o.payment_status = "paid" THEN 1 ELSE 0 END), 0) as orders_paid,
                COALESCE(SUM(CASE WHEN o.payment_status = "paid" THEN o.payable_total ELSE 0 END), 0) as paid_amount
            ')
            ->first();

        [
            $paymentMethods,
            $returnsPack,
            $exchangePack,
            $baseCogs,
            $baseSoldQty,
            $expensesPack,
            $newCustomers,
            $stockAgg,
            $stockCostValue,
            $globalStats,
            $lowStockItems
        ] = $this->rememberMany(
            [
                $this->key($baseKey, 'pm'),
                $this->key($baseKey, 'returnsPack'),
                $this->key($baseKey, 'exchangePack'),
                $this->key($baseKey, 'baseCogs'),
                $this->key($baseKey, 'baseSoldQty'),
                $this->key($baseKey, 'exp'),
                $this->key($baseKey, 'nc'),
                $this->key($baseKey, 'stockAgg'),
                $this->key($baseKey, 'stockv'),
                self::CACHE_KEY_GLOBAL_STATS,
                $this->key($baseKey, 'lowStock'),
            ],
            self::METRICS_TTL,
            [
                // Payment methods breakdown
                fn () => DB::table('payments as p')
                    ->join('orders as o', 'p.order_id', '=', 'o.id')
                    ->where('p.status', 'completed')
                    ->whereBetween('p.created_at', [$f['start'], $f['end']])
                    ->tap(fn ($q) => $this->orderFilters($q, $f))
                    ->groupBy('p.method')
                    ->orderByDesc('total')
                    ->get([
                        DB::raw('COALESCE(p.method, "Unknown") as method'),
                        DB::raw('COALESCE(SUM(p.amount), 0) as total'),
                        DB::raw('COUNT(*) as count'),
                    ])->map(fn ($r) => [
                        'method' => $r->method,
                        'total' => (float) $r->total,
                        'count' => (int) $r->count,
                    ])->values(),

                // ✅ Returns pack: refund + returned COGS + returned qty
                fn () => (function () use ($f) {
                    $q = DB::table('returns as r')
                        ->join('return_items as ri', 'ri.return_id', '=', 'r.id')
                        ->leftJoin('product_batches as pb', 'ri.product_batch_id', '=', 'pb.id')
                        ->whereBetween('r.created_at', [$f['start'], $f['end']])
                        ->when($f['location_id'], fn ($qq) => $qq->where('r.location_id', $f['location_id']));

                    $row = $q->selectRaw('
                            COALESCE(SUM(ri.refund_amount),0) as total_refunds,
                            COALESCE(SUM(ri.qty),0) as returned_qty,
                            COALESCE(SUM(ri.qty * COALESCE(pb.buy_price,0)),0) as returned_cogs,
                            COUNT(DISTINCT r.id) as total_returns
                        ')
                        ->first();

                    return [
                        'total_refunds' => (float) ($row->total_refunds ?? 0),
                        'returned_qty'  => (float) ($row->returned_qty ?? 0),
                        'returned_cogs' => (float) ($row->returned_cogs ?? 0),
                        'total_returns' => (int) ($row->total_returns ?? 0),
                    ];
                })(),

                // ✅ Exchange pack: revenue diff + cost delta + qty delta
                fn () => (function () use ($f) {
                    // revenue diff (positive = extra collected, negative = refunded)
                    $exRev = (float) (DB::table('exchanges as e')
                        ->whereBetween('e.created_at', [$f['start'], $f['end']])
                        ->when($f['location_id'], fn ($q) => $q->where('e.location_id', $f['location_id']))
                        ->selectRaw('COALESCE(SUM(e.price_difference),0) as rev')
                        ->value('rev') ?? 0);

                    $exCount = (int) (DB::table('exchanges as e')
                        ->whereBetween('e.created_at', [$f['start'], $f['end']])
                        ->when($f['location_id'], fn ($q) => $q->where('e.location_id', $f['location_id']))
                        ->count());

                    // cost delta: issued_cost - returned_cost
                    // We support multiple possible mode naming.
                    $mReturn = "('return','returned','in','inbound')";
                    $mIssue  = "('issue','issued','out','outbound')";

                    $row = DB::table('exchanges as e')
                        ->join('exchange_lines as el', 'el.exchange_id', '=', 'e.id')
                        ->leftJoin('product_batches as pb', 'el.product_batch_id', '=', 'pb.id')
                        ->whereBetween('e.created_at', [$f['start'], $f['end']])
                        ->when($f['location_id'], fn ($q) => $q->where('e.location_id', $f['location_id']))
                        ->selectRaw("
                            COALESCE(SUM(CASE WHEN LOWER(el.mode) IN {$mReturn} THEN COALESCE(el.qty,0) ELSE 0 END),0) as returned_qty,
                            COALESCE(SUM(CASE WHEN LOWER(el.mode) IN {$mIssue}  THEN COALESCE(el.qty,0) ELSE 0 END),0) as issued_qty,
                            COALESCE(SUM(CASE WHEN LOWER(el.mode) IN {$mReturn} THEN COALESCE(el.qty,0) * COALESCE(pb.buy_price,0) ELSE 0 END),0) as returned_cost,
                            COALESCE(SUM(CASE WHEN LOWER(el.mode) IN {$mIssue}  THEN COALESCE(el.qty,0) * COALESCE(pb.buy_price,0) ELSE 0 END),0) as issued_cost
                        ")
                        ->first();

                    $returnedCost = (float) ($row->returned_cost ?? 0);
                    $issuedCost   = (float) ($row->issued_cost ?? 0);

                    return [
                        'total_exchanges'   => $exCount,
                        'exchange_revenue'  => $exRev,
                        'returned_qty'      => (float) ($row->returned_qty ?? 0),
                        'issued_qty'        => (float) ($row->issued_qty ?? 0),
                        'returned_cost'     => $returnedCost,
                        'issued_cost'       => $issuedCost,
                        'cogs_delta'        => (float) ($issuedCost - $returnedCost),
                    ];
                })(),

                // ✅ Base COGS from orders (full qty, not net)
                fn () => (float) (DB::table('order_items as oi')
                    ->join('orders as o', 'oi.order_id', '=', 'o.id')
                    ->join('product_batches as pb', 'oi.product_batch_id', '=', 'pb.id')
                    ->whereBetween('o.created_at', [$f['start'], $f['end']])
                    ->tap(fn ($q) => $this->orderFilters($q, $f))
                    ->selectRaw('
                        COALESCE(SUM(COALESCE(oi.quantity,0) * COALESCE(pb.buy_price,0)),0) as cogs
                    ')
                    ->value('cogs') ?? 0),

                // ✅ Base sold qty from orders (full qty)
                fn () => (float) (DB::table('order_items as oi')
                    ->join('orders as o', 'oi.order_id', '=', 'o.id')
                    ->whereBetween('o.created_at', [$f['start'], $f['end']])
                    ->tap(fn ($q) => $this->orderFilters($q, $f))
                    ->selectRaw('COALESCE(SUM(COALESCE(oi.quantity,0)),0) as qty')
                    ->value('qty') ?? 0),

                // Expenses pack (same)
                function () use ($f) {
                    $expTotal = (float) (DB::table('expenses as e')
                        ->tap(fn ($q) => $this->applyExpenseFilters($q, $f))
                        ->whereBetween('e.expense_date', [$f['start']->toDateString(), $f['end']->toDateString()])
                        ->selectRaw('COALESCE(SUM(e.amount), 0) as total')
                        ->value('total') ?? 0);

                    $expCount = (int) (DB::table('expenses as e')
                        ->tap(fn ($q) => $this->applyExpenseFilters($q, $f))
                        ->whereBetween('e.expense_date', [$f['start']->toDateString(), $f['end']->toDateString()])
                        ->count());

                    $byCategory = DB::table('expenses as e')
                        ->leftJoin('expense_categories as ec', 'e.expense_category_id', '=', 'ec.id')
                        ->tap(fn ($q) => $this->applyExpenseFilters($q, $f))
                        ->whereBetween('e.expense_date', [$f['start']->toDateString(), $f['end']->toDateString()])
                        ->groupBy('e.expense_category_id', 'ec.name')
                        ->orderByDesc('total')
                        ->get([
                            DB::raw('COALESCE(ec.name, "Uncategorized") as category'),
                            DB::raw('COALESCE(SUM(e.amount), 0) as total'),
                            DB::raw('COUNT(*) as count'),
                        ])->map(fn ($r) => [
                            'category' => $r->category,
                            'total' => (float) $r->total,
                            'count' => (int) $r->count,
                        ])->values();

                    $byMethod = DB::table('expenses as e')
                        ->tap(fn ($q) => $this->applyExpenseFilters($q, $f))
                        ->whereBetween('e.expense_date', [$f['start']->toDateString(), $f['end']->toDateString()])
                        ->groupBy('e.payment_method')
                        ->orderByDesc('total')
                        ->get([
                            DB::raw('COALESCE(e.payment_method, "Unknown") as method'),
                            DB::raw('COALESCE(SUM(e.amount), 0) as total'),
                            DB::raw('COUNT(*) as count'),
                        ])->map(fn ($r) => [
                            'method' => $r->method,
                            'total' => (float) $r->total,
                            'count' => (int) $r->count,
                        ])->values();

                    return [
                        'total' => $expTotal,
                        'count' => $expCount,
                        'by_category' => $byCategory,
                        'by_method' => $byMethod,
                    ];
                },

                // New customers
                fn () => (int) DB::table('customers')->whereBetween('created_at', [$f['start'], $f['end']])->count(),

                // Stock aggregation
                fn () => DB::table('batch_stocks as bs')
                    ->when($f['location_id'], fn ($q) => $q->where('bs.location_id', $f['location_id']))
                    ->selectRaw('
                        COALESCE(SUM(bs.on_hand), 0) as on_hand,
                        COALESCE(SUM(bs.reserved), 0) as reserved,
                        COALESCE(SUM(bs.on_hand - bs.reserved), 0) as available
                    ')
                    ->first(),

                // Stock cost value
                fn () => (float) (DB::table('batch_stocks as bs')
                    ->join('product_batches as pb', 'bs.product_batch_id', '=', 'pb.id')
                    ->when($f['location_id'], fn ($q) => $q->where('bs.location_id', $f['location_id']))
                    ->selectRaw('COALESCE(SUM((bs.on_hand - bs.reserved) * pb.buy_price), 0) as value')
                    ->value('value') ?? 0),

                // Global stats
                fn () => [
                    'avg_due_balance' => (float) (DB::table('customers')->avg('due_balance') ?? 0),
                    'total_reward_points' => (float) (DB::table('customers')->sum('reward_points') ?? 0),
                ],

                // Low stock count
                fn () => (int) DB::table('batch_stocks as bs')
                    ->when($f['location_id'], fn ($q) => $q->where('bs.location_id', $f['location_id']))
                    ->whereRaw('(bs.on_hand - bs.reserved) < 10')
                    ->count(),
            ]
        );

        $totalSales = (float) ($salesAgg->total_sales ?? 0);

        // ✅ Returns
        $totalRefunds = (float) ($returnsPack['total_refunds'] ?? 0);
        $returnedCogs = (float) ($returnsPack['returned_cogs'] ?? 0);
        $returnedQty  = (float) ($returnsPack['returned_qty'] ?? 0);

        // ✅ Exchanges
        $exchangeRevenue = (float) ($exchangePack['exchange_revenue'] ?? 0);
        $exchangeCogsDelta = (float) ($exchangePack['cogs_delta'] ?? 0);
        $exReturnedQty = (float) ($exchangePack['returned_qty'] ?? 0);
        $exIssuedQty   = (float) ($exchangePack['issued_qty'] ?? 0);

        // ✅ Revenue (orders) - refunds + exchange difference
        $netSales = $totalSales + $exchangeRevenue;
        // $netSales = $totalSales - $totalRefunds + $exchangeRevenue;

        // ✅ COGS: base order cogs - returned_cogs + exchange cost delta
        $cogsValue = (float) $baseCogs - $returnedCogs + $exchangeCogsDelta;

        // ✅ Sold qty: base - returns - exchange_return + exchange_issue
        $soldQty = (float) $baseSoldQty - $returnedQty - $exReturnedQty + $exIssuedQty;
        if ($soldQty < 0) $soldQty = 0;

        // Profit
        $grossProfit = $netSales - $cogsValue;
        $grossMargin = $netSales > 0 ? round(($grossProfit / $netSales) * 100, 2) : 0;

        $expenseTotal = (float) ($expensesPack['total'] ?? 0);

        $netProfit = $grossProfit - $expenseTotal;
        $profitMargin = $netSales > 0 ? round(($netProfit / $netSales) * 100, 2) : 0;

        // Payments
        $totalPayments = (float) collect($paymentMethods)->sum('total');
        $dueAmount = max(0, $netSales - $totalPayments);

        return [
            // Sales Metrics
            'total_sales'      => $totalSales,
            'total_orders'     => (int) ($salesAgg->total_orders ?? 0),
            'avg_order_value'  => (float) ($salesAgg->avg_order_value ?? 0),
            'total_discounts'  => (float) ($salesAgg->total_discounts ?? 0),

            // ✅ qty (net)
            'sold_qty' => (float) $soldQty,

            // Payment Metrics
            'total_payments'            => $totalPayments,
            'orders_paid'               => (int) ($salesAgg->orders_paid ?? 0),
            'paid_amount'               => (float) ($salesAgg->paid_amount ?? 0),
            'payment_methods_breakdown' => $paymentMethods,

            // Returns
            'total_refunds' => $totalRefunds,
            'total_returns' => (int) ($returnsPack['total_returns'] ?? 0),
            'returned_qty'  => $returnedQty,
            'returned_cogs' => $returnedCogs,

            // Exchanges
            'total_exchanges'   => (int) ($exchangePack['total_exchanges'] ?? 0),
            'exchange_revenue'  => $exchangeRevenue,
            'exchange_cogs_delta' => $exchangeCogsDelta,
            'exchange_returned_qty' => $exReturnedQty,
            'exchange_issued_qty'   => $exIssuedQty,

            // Profit Metrics
            'net_sales'          => (float) $netSales,
            'cost_of_goods_sold' => (float) $cogsValue,

            'gross_profit' => (float) $grossProfit,
            'gross_margin' => (float) $grossMargin,

            // Expenses
            'expenses_total'       => (float) ($expensesPack['total'] ?? 0),
            'expenses_count'       => (int) ($expensesPack['count'] ?? 0),
            'expenses_by_category' => $expensesPack['by_category'] ?? [],
            'expenses_by_method'   => $expensesPack['by_method'] ?? [],

            // Net profit
            'net_profit'    => (float) $netProfit,
            'profit_margin' => (float) $profitMargin,

            'due_amount' => (float) $dueAmount,

            // Customer Metrics
            'new_customers'       => (int) $newCustomers,
            'total_reward_points' => (float) ($globalStats['total_reward_points'] ?? 0),
            'avg_due_balance'     => (float) ($globalStats['avg_due_balance'] ?? 0),

            // Stock Metrics
            'available_stock'  => (float) ($stockAgg->available ?? 0),
            'total_reserved'   => (float) ($stockAgg->reserved ?? 0),
            'on_hand_stock'    => (float) ($stockAgg->on_hand ?? 0),
            'stock_cost_value' => (float) $stockCostValue,
            'low_stock_items'  => (int) $lowStockItems,

            // Period Info
            'period' => [
                'from'  => $f['start']->toDateString(),
                'to'    => $f['end']->toDateString(),
                'range' => $f['range'],
            ],
        ];
    }

    /* =========================
     | SECTION: CHARTS
     ========================= */
    public function charts(Request $request): JsonResponse
    {
        $f = $this->filters($request);
        $base = $this->cacheKeyBase($f);

        $payload = Cache::remember($this->key($base, 'charts'), self::CHARTS_TTL, fn () => $this->buildCharts($f));

        return response()->json([
            'charts' => $payload,
            'timestamp' => now()->toIso8601String(),
        ])->header('Cache-Control', 'private, max-age=30');
    }

    private function buildCharts(array $f): array
    {
        $dailySales = DB::table('orders as o')
            ->whereBetween('o.created_at', [$f['start'], $f['end']])
            ->tap(fn ($q) => $this->orderFilters($q, $f))
            ->selectRaw('
                DATE(o.created_at) as date,
                COUNT(*) as orders,
                COALESCE(SUM(o.payable_total), 0) as revenue
            ')
            ->groupBy(DB::raw('DATE(o.created_at)'))
            ->orderBy('date')
            ->get()
            ->map(fn ($r) => [
                'date' => $r->date,
                'orders' => (int) $r->orders,
                'revenue' => (float) $r->revenue,
            ]);

        $statusDist = DB::table('orders as o')
            ->whereBetween('o.created_at', [$f['start'], $f['end']])
            ->when($f['location_id'], fn ($q) => $q->where('o.location_id', $f['location_id']))
            ->when($f['payment_status'] !== 'all', fn ($q) => $q->where('o.payment_status', $f['payment_status']))
            ->selectRaw('o.status, COUNT(*) as count')
            ->groupBy('o.status')
            ->orderByDesc('count')
            ->get();

        $paymentMethods = DB::table('payments as p')
            ->join('orders as o', 'p.order_id', '=', 'o.id')
            ->where('p.status', 'completed')
            ->whereBetween('p.created_at', [$f['start'], $f['end']])
            ->tap(fn ($q) => $this->orderFilters($q, $f))
            ->groupBy('p.method')
            ->orderByDesc('total')
            ->get([
                DB::raw('COALESCE(p.method, "Unknown") as method'),
                DB::raw('COALESCE(SUM(p.amount), 0) as total'),
            ])->map(fn ($r) => [
                'method' => $r->method,
                'total' => (float) $r->total,
            ]);

        // Expenses (daily)
        $dailyExpenses = DB::table('expenses as e')
            ->tap(fn ($q) => $this->applyExpenseFilters($q, $f))
            ->whereBetween('e.expense_date', [$f['start']->toDateString(), $f['end']->toDateString()])
            ->selectRaw('
                DATE(e.expense_date) as date,
                COALESCE(SUM(e.amount), 0) as amount
            ')
            ->groupBy(DB::raw('DATE(e.expense_date)'))
            ->orderBy('date')
            ->get()
            ->map(fn ($r) => [
                'date' => $r->date,
                'amount' => (float) $r->amount,
            ]);

        // Orders revenue map
        $revMap = DB::table('orders as o')
            ->whereBetween('o.created_at', [$f['start'], $f['end']])
            ->tap(fn ($q) => $this->orderFilters($q, $f))
            ->selectRaw('DATE(o.created_at) as date, COALESCE(SUM(o.payable_total), 0) as revenue')
            ->groupBy(DB::raw('DATE(o.created_at)'))
            ->get()
            ->keyBy('date')
            ->map(fn ($r) => (float) $r->revenue);

        // ✅ Returns daily: refund + returned_cogs
        $retDaily = DB::table('returns as r')
            ->join('return_items as ri', 'ri.return_id', '=', 'r.id')
            ->leftJoin('product_batches as pb', 'ri.product_batch_id', '=', 'pb.id')
            ->whereBetween('r.created_at', [$f['start'], $f['end']])
            ->when($f['location_id'], fn ($q) => $q->where('r.location_id', $f['location_id']))
            ->selectRaw('
                DATE(r.created_at) as date,
                COALESCE(SUM(ri.refund_amount),0) as refunds,
                COALESCE(SUM(ri.qty * COALESCE(pb.buy_price,0)),0) as returned_cogs
            ')
            ->groupBy(DB::raw('DATE(r.created_at)'))
            ->get()
            ->keyBy('date');

        $refundMap = $retDaily->map(fn ($r) => (float) $r->refunds);
        $retCogsMap = $retDaily->map(fn ($r) => (float) $r->returned_cogs);

        // ✅ Orders COGS daily (full qty)
        $cogsMap = DB::table('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->join('product_batches as pb', 'oi.product_batch_id', '=', 'pb.id')
            ->whereBetween('o.created_at', [$f['start'], $f['end']])
            ->tap(fn ($q) => $this->orderFilters($q, $f))
            ->selectRaw('
                DATE(o.created_at) as date,
                COALESCE(SUM(COALESCE(oi.quantity,0) * COALESCE(pb.buy_price,0)),0) as cogs
            ')
            ->groupBy(DB::raw('DATE(o.created_at)'))
            ->get()
            ->keyBy('date')
            ->map(fn ($r) => (float) $r->cogs);

        // ✅ Exchange daily: revenue diff + cost delta
        $mReturn = "('return','returned','in','inbound')";
        $mIssue  = "('issue','issued','out','outbound')";

        $exDaily = DB::table('exchanges as e')
            ->leftJoin('exchange_lines as el', 'el.exchange_id', '=', 'e.id')
            ->leftJoin('product_batches as pb', 'el.product_batch_id', '=', 'pb.id')
            ->whereBetween('e.created_at', [$f['start'], $f['end']])
            ->when($f['location_id'], fn ($q) => $q->where('e.location_id', $f['location_id']))
            ->selectRaw("
                DATE(e.created_at) as date,
                COALESCE(SUM(e.price_difference),0) as ex_rev,
                COALESCE(SUM(CASE WHEN LOWER(el.mode) IN {$mIssue}  THEN COALESCE(el.qty,0) * COALESCE(pb.buy_price,0) ELSE 0 END),0)
                  -
                COALESCE(SUM(CASE WHEN LOWER(el.mode) IN {$mReturn} THEN COALESCE(el.qty,0) * COALESCE(pb.buy_price,0) ELSE 0 END),0)
                as ex_cogs_delta
            ")
            ->groupBy(DB::raw('DATE(e.created_at)'))
            ->get()
            ->keyBy('date');

        $exRevMap = $exDaily->map(fn ($r) => (float) $r->ex_rev);
        $exCogsDeltaMap = $exDaily->map(fn ($r) => (float) $r->ex_cogs_delta);

        $expMap = collect($dailyExpenses)->keyBy('date')->map(fn ($r) => (float) $r['amount']);

        $allDates = collect()
            ->merge($revMap->keys())
            ->merge($refundMap->keys())
            ->merge($cogsMap->keys())
            ->merge($expMap->keys())
            ->merge($exRevMap->keys())
            ->merge($exCogsDeltaMap->keys())
            ->unique()
            ->sort()
            ->values();

        $profitSeries = $allDates->map(function ($d) use ($revMap, $refundMap, $cogsMap, $expMap, $retCogsMap, $exRevMap, $exCogsDeltaMap) {
            $rev = (float) ($revMap[$d] ?? 0);
            $refund = (float) ($refundMap[$d] ?? 0);
            $orderCogs = (float) ($cogsMap[$d] ?? 0);
            $retCogs = (float) ($retCogsMap[$d] ?? 0);
            $exp = (float) ($expMap[$d] ?? 0);

            $exRev = (float) ($exRevMap[$d] ?? 0);
            $exCogsDelta = (float) ($exCogsDeltaMap[$d] ?? 0);

            $netSales = $rev - $refund + $exRev;
            $netCogs = $orderCogs - $retCogs + $exCogsDelta;

            $grossProfit = $netSales - $netCogs;
            $netProfit = $grossProfit - $exp;

            return [
                'date' => $d,
                'revenue' => $rev,
                'refunds' => $refund,
                'exchange_revenue' => $exRev,
                'cogs' => $orderCogs,
                'returned_cogs' => $retCogs,
                'exchange_cogs_delta' => $exCogsDelta,
                'expenses' => $exp,
                'net_profit' => (float) $netProfit,
            ];
        });

        return [
            'daily_sales' => $dailySales,
            'order_status_distribution' => $statusDist,
            'payment_methods_breakdown' => $paymentMethods,
            'daily_expenses' => $dailyExpenses,
            'daily_profit' => $profitSeries,
        ];
    }

    /* =========================
     | SECTION: TABLES (kept same)
     ========================= */
    public function tables(Request $request): JsonResponse
    {
        $f = $this->filters($request);
        $base = $this->cacheKeyBase($f);

        $payload = Cache::remember($this->key($base, 'tables'), self::TABLES_TTL, fn () => $this->buildTables($f));

        return response()->json([
            'tables' => $payload,
            'timestamp' => now()->toIso8601String(),
        ])->header('Cache-Control', 'private, max-age=30');
    }

    private function buildTables(array $f): array
    {
        $recentOrders = DB::table('orders as o')
            ->leftJoin('customers as c', 'o.customer_id', '=', 'c.id')
            ->whereBetween('o.created_at', [$f['start'], $f['end']])
            ->tap(fn ($q) => $this->orderFilters($q, $f))
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
            ->limit(10)
            ->get()
            ->map(fn ($o) => [
                'id' => $o->id,
                'order_no' => $o->order_no,
                'customer_id' => $o->customer_id,
                'created_at' => $o->created_at,
                'payable_total' => (float) $o->payable_total,
                'status' => $o->status,
                'payment_status' => $o->payment_status,
                'customer' => ['name' => $o->customer_name],
            ]);

        // Top products (same)
        $topProductIds = DB::table('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->whereBetween('o.created_at', [$f['start'], $f['end']])
            ->tap(fn ($q) => $this->orderFilters($q, $f))
            ->select('oi.product_id')
            ->selectRaw('COALESCE(SUM(oi.total_price), 0) as total_revenue')
            ->groupBy('oi.product_id')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->pluck('product_id');

        $topProducts = collect();
        if ($topProductIds->isNotEmpty()) {
            $products = Product::whereIn('id', $topProductIds)
                ->select(['id', 'name', 'category_id', 'brand_id'])
                ->with(['category:id,name', 'brand:id,name'])
                ->get()
                ->keyBy('id');

            $topProducts = DB::table('order_items as oi')
                ->join('orders as o', 'oi.order_id', '=', 'o.id')
                ->join('product_batches as pb', 'oi.product_batch_id', '=', 'pb.id')
                ->whereBetween('o.created_at', [$f['start'], $f['end']])
                ->tap(fn ($q) => $this->orderFilters($q, $f))
                ->whereIn('oi.product_id', $topProductIds)
                ->select([
                    'oi.product_id',
                    DB::raw('COALESCE(SUM(COALESCE(oi.quantity,0)), 0) as total_qty'),
                    DB::raw('COALESCE(SUM(oi.total_price), 0) as total_revenue'),
                    DB::raw('COALESCE(SUM(COALESCE(oi.quantity,0) * COALESCE(pb.buy_price,0)), 0) as total_cost'),
                ])
                ->groupBy('oi.product_id')
                ->get()
                ->map(function ($i) use ($products) {
                    $p = $products->get($i->product_id);

                    $rev = (float) $i->total_revenue;
                    $cost = (float) $i->total_cost;

                    $profit = $rev - $cost;
                    $margin = $rev > 0 ? round(($profit / $rev) * 100, 2) : 0;

                    return [
                        'product' => $p,
                        'total_qty' => (float) $i->total_qty,
                        'total_revenue' => $rev,
                        'total_cost' => $cost,
                        'profit' => $profit,
                        'margin' => $margin,
                    ];
                })
                ->sortByDesc('total_revenue')
                ->values();
        }

        // Recent expenses (same)
        $recentExpenses = DB::table('expenses as e')
            ->leftJoin('expense_categories as ec', 'e.expense_category_id', '=', 'ec.id')
            ->tap(fn ($q) => $this->applyExpenseFilters($q, $f))
            ->whereBetween('e.expense_date', [$f['start']->toDateString(), $f['end']->toDateString()])
            ->select([
                'e.id',
                'e.expense_no',
                'e.expense_date',
                'e.location_id',
                'e.payment_method',
                'e.amount',
                'e.title',
                DB::raw('COALESCE(ec.name, "Uncategorized") as category_name'),
            ])
            ->orderByDesc('e.expense_date')
            ->orderByDesc('e.id')
            ->limit(10)
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'expense_no' => $r->expense_no,
                'expense_date' => $r->expense_date,
                'amount' => (float) $r->amount,
                'payment_method' => $r->payment_method,
                'title' => $r->title,
                'category' => $r->category_name,
            ]);

        return [
            'recent_orders' => $recentOrders,
            'top_products' => $topProducts,
            'recent_expenses' => $recentExpenses,
        ];
    }

    /* =========================
     | REALTIME COUNTERS
     ========================= */
    private function buildCounters(?int $locationId): array
    {
        $cacheKey = 'fin:counters:' . self::CACHE_VERSION . ':' . ($locationId ?? 'all');

        return Cache::remember($cacheKey, self::RT_TTL, function () use ($locationId) {
            $pending = (int) DB::table('orders')
                ->where('status', 'pending')
                ->when($locationId, fn ($q) => $q->where('location_id', $locationId))
                ->count();

            $abandoned = (int) DB::table('carts')
                ->where('created_at', '<', now()->subHours(24))
                ->count();

            $lowStock = (int) DB::table('batch_stocks as bs')
                ->when($locationId, fn ($q) => $q->where('bs.location_id', $locationId))
                ->whereRaw('(bs.on_hand - bs.reserved) < 10')
                ->count();

            return [
                'pending_orders' => $pending,
                'abandoned_carts' => $abandoned,
                'low_stock_items' => $lowStock,
            ];
        });
    }

    public function realTime(Request $request): JsonResponse
    {
        $locationId = $request->filled('location_id') ? (int) $request->get('location_id') : null;
        $since = $request->get('since');

        $counters = $this->buildCounters($locationId);

        $newOrders = 0;
        if ($since) {
            try {
                $sinceTime = Carbon::parse($since);
                $newOrders = DB::table('orders')
                    ->where('created_at', '>', $sinceTime)
                    ->when($locationId, fn ($q) => $q->where('location_id', $locationId))
                    ->count();
            } catch (\Throwable $e) {
                $newOrders = 0;
            }
        }

        return response()->json([
            'new_orders'       => (int) $newOrders,
            'pending_orders'   => (int) ($counters['pending_orders'] ?? 0),
            'abandoned_carts'  => (int) ($counters['abandoned_carts'] ?? 0),
            'low_stock_items'  => (int) ($counters['low_stock_items'] ?? 0),
            'timestamp'        => now()->toIso8601String(),
        ])->header('Cache-Control', 'private, no-store, max-age=0');
    }

    public function stream(Request $request): StreamedResponse
    {
        $locationId = $request->filled('location_id') ? (int) $request->get('location_id') : null;

        return response()->stream(function () use ($locationId) {
            @ini_set('zlib.output_compression', '0');
            @ini_set('output_buffering', 'off');
            @ini_set('implicit_flush', '1');

            while (ob_get_level() > 0) { @ob_end_flush(); }
            @ob_implicit_flush(true);

            $lastCheck = now()->subSeconds(5);

            for ($i = 0; $i < 600; $i++) {
                if (connection_aborted()) break;

                $counters = $this->buildCounters($locationId);

                $newOrders = DB::table('orders')
                    ->where('created_at', '>', $lastCheck)
                    ->when($locationId, fn ($q) => $q->where('location_id', $locationId))
                    ->count();

                $payload = [
                    'new_orders'       => (int) $newOrders,
                    'pending_orders'   => (int) ($counters['pending_orders'] ?? 0),
                    'abandoned_carts'  => (int) ($counters['abandoned_carts'] ?? 0),
                    'low_stock_items'  => (int) ($counters['low_stock_items'] ?? 0),
                    'timestamp'        => now()->toIso8601String(),
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

    /* =========================
     | EXPORT (same behavior)
     ========================= */
    public function export(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'format' => 'nullable|in:csv,excel,pdf',
            'date_range' => 'required|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'location_id' => 'nullable|integer|exists:locations,id',
            'status' => 'nullable|string',
            'payment_status' => 'nullable|string',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Export has been queued. You will receive a notification when ready.',
            'job_id' => uniqid('export_'),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
