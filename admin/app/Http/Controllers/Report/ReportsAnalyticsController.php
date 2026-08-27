<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Report\Concerns\ReportFilters;
use App\Models\Location;
use App\Services\Report\TabularExporter;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Deeper analytics beyond the two existing financial dashboards: product &
 * category profitability, customer profitability, location breakdown, and
 * period-over-period trends. Built on the same ReportFilters foundation
 * (valid-order rules, refund source, COGS join) so numbers here always
 * agree with the Today/Analysis dashboards for the same period.
 */
class ReportsAnalyticsController extends Controller
{
    use ReportFilters;

    private const CACHE_VERSION = 'reports_v2_returns_scoped_to_order_period';
    private const TTL = 30;

    private function filters(Request $request): array
    {
        $range = (string) $request->get('date_range', 'this_month');
        [$start, $end] = $this->resolveDateRange($range, $request->start_date, $request->end_date);

        return [
            'range'       => $range,
            'start'       => $start,
            'end'         => $end,
            'location_id' => $request->filled('location_id') ? (int) $request->get('location_id') : null,
            'start_raw'   => $request->start_date,
            'end_raw'     => $request->end_date,
        ];
    }

    private function cacheKey(string $part, array $f, array $extra = []): string
    {
        $bits = array_merge([
            self::CACHE_VERSION,
            $part,
            $f['start']->format('Ymd'),
            $f['end']->format('Ymd'),
            $f['location_id'] ?? 'all',
        ], $extra);

        return 'rep:' . implode(':', $bits);
    }

    /**
     * Orders base query: valid orders (no cancelled/void, no split children)
     * within the period, optionally scoped to a location.
     */
    private function validOrdersInRange(array $f, string $alias = 'o')
    {
        return DB::table("orders as {$alias}")
            ->tap(fn ($q) => $this->excludeSplitChildren($q, $alias))
            ->tap(fn ($q) => $this->excludeInvalidStatuses($q, $alias))
            ->whereBetween("{$alias}.created_at", [$f['start'], $f['end']])
            ->when($f['location_id'], fn ($q) => $q->where("{$alias}.location_id", $f['location_id']));
    }

    /* =========================
     | PAGE
     ========================= */
    public function index(Request $request)
    {
        $f = $this->filters($request);

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

        $locations = Cache::remember('fin:active_locations:' . self::CACHE_VERSION, 3600, function () {
            return Location::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        });

        return view('reports.analytics', [
            'locations'    => $locations,
            'dateRanges'   => $dateRanges,
            'currentRange' => $f['range'],
            'startDate'    => $f['start']->toDateString(),
            'endDate'      => $f['end']->toDateString(),
            'locationId'   => $f['location_id'],
        ]);
    }

    /* =========================
     | PRODUCTS & CATEGORIES
     ========================= */
    public function products(Request $request): JsonResponse
    {
        $f = $this->filters($request);
        $groupBy = $request->get('group_by', 'product') === 'category' ? 'category' : 'product';
        $sort = in_array($request->get('sort'), ['revenue', 'profit', 'margin', 'qty']) ? $request->get('sort') : 'revenue';
        $dir = $request->get('dir') === 'asc' ? 'asc' : 'desc';
        $page = max(1, (int) $request->get('page', 1));
        $perPage = min(100, max(1, (int) $request->get('per_page', 20)));

        $key = $this->cacheKey('products', $f, [$groupBy, $sort, $dir, $page, $perPage]);

        $payload = Cache::remember($key, self::TTL, function () use ($f, $groupBy, $sort, $dir, $page, $perPage) {
            $base = DB::table('order_items as oi')
                ->join('orders as o', 'oi.order_id', '=', 'o.id')
                ->join('products as p', 'oi.product_id', '=', 'p.id')
                ->leftJoin('categories as cat', 'p.category_id', '=', 'cat.id')
                ->leftJoin('product_batches as pb', 'oi.product_batch_id', '=', 'pb.id')
                ->tap(fn ($q) => $this->excludeSplitChildren($q))
                ->tap(fn ($q) => $this->excludeInvalidStatuses($q))
                ->whereBetween('o.created_at', [$f['start'], $f['end']])
                ->when($f['location_id'], fn ($q) => $q->where('o.location_id', $f['location_id']));

            $netQtyExpr = 'SUM(GREATEST(COALESCE(oi.quantity,0) - COALESCE(oi.returned_qty,0), 0))';
            $netRevenueExpr = 'SUM(COALESCE(oi.total_price,0) - COALESCE(oi.returned_amount,0))';
            $netCostExpr = 'SUM(GREATEST(COALESCE(oi.quantity,0) - COALESCE(oi.returned_qty,0), 0) * COALESCE(pb.buy_price,0))';

            if ($groupBy === 'category') {
                $rows = $base
                    ->groupBy('p.category_id', 'cat.name')
                    ->select([
                        'p.category_id as id',
                        DB::raw('COALESCE(cat.name, "Uncategorized") as name'),
                        DB::raw("{$netQtyExpr} as net_qty"),
                        DB::raw("{$netRevenueExpr} as net_revenue"),
                        DB::raw("{$netCostExpr} as net_cost"),
                        DB::raw('COUNT(DISTINCT p.id) as product_count'),
                    ])
                    ->get();
            } else {
                $rows = $base
                    ->groupBy('p.id', 'p.name', 'p.category_id', 'cat.name')
                    ->select([
                        'p.id as id',
                        'p.name as name',
                        'cat.name as category_name',
                        DB::raw("{$netQtyExpr} as net_qty"),
                        DB::raw("{$netRevenueExpr} as net_revenue"),
                        DB::raw("{$netCostExpr} as net_cost"),
                    ])
                    ->get();
            }

            $rows = $rows->map(function ($r) {
                $revenue = (float) $r->net_revenue;
                $cost = (float) $r->net_cost;
                $profit = $revenue - $cost;
                $margin = $revenue > 0 ? round(($profit / $revenue) * 100, 2) : 0;

                return [
                    'id' => $r->id,
                    'name' => $r->name,
                    'category_name' => $r->category_name ?? null,
                    'product_count' => $r->product_count ?? null,
                    'qty' => (float) $r->net_qty,
                    'revenue' => $revenue,
                    'cost' => $cost,
                    'profit' => $profit,
                    'margin' => $margin,
                ];
            });

            $sortKey = ['revenue' => 'revenue', 'profit' => 'profit', 'margin' => 'margin', 'qty' => 'qty'][$sort];
            $rows = $dir === 'asc' ? $rows->sortBy($sortKey) : $rows->sortByDesc($sortKey);
            $rows = $rows->values();

            $total = $rows->count();
            $items = $rows->forPage($page, $perPage)->values();

            return [
                'group_by' => $groupBy,
                'sort' => $sort,
                'dir' => $dir,
                'data' => $items,
                'meta' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => (int) max(1, ceil($total / $perPage)),
                ],
            ];
        });

        return response()->json($payload)->header('Cache-Control', 'private, max-age=30');
    }

    /* =========================
     | CUSTOMERS
     ========================= */
    public function customers(Request $request): JsonResponse
    {
        $f = $this->filters($request);
        $sort = in_array($request->get('sort'), ['revenue', 'profit', 'orders', 'due']) ? $request->get('sort') : 'revenue';
        $dir = $request->get('dir') === 'asc' ? 'asc' : 'desc';
        $page = max(1, (int) $request->get('page', 1));
        $perPage = min(100, max(1, (int) $request->get('per_page', 20)));

        $key = $this->cacheKey('customers', $f, [$sort, $dir, $page, $perPage]);

        $payload = Cache::remember($key, self::TTL, function () use ($f, $sort, $dir, $page, $perPage) {
            $costPerOrder = $this->baseCogsQuery()
                ->selectRaw('oi.order_id, SUM(GREATEST(COALESCE(oi.quantity,0) - COALESCE(oi.returned_qty,0), 0) * COALESCE(pb.buy_price,0)) as cost')
                ->groupBy('oi.order_id');

            $rows = DB::table('orders as o')
                ->join('customers as c', 'o.customer_id', '=', 'c.id')
                ->leftJoinSub($costPerOrder, 'costs', 'costs.order_id', '=', 'o.id')
                ->tap(fn ($q) => $this->excludeSplitChildren($q))
                ->tap(fn ($q) => $this->excludeInvalidStatuses($q))
                ->whereBetween('o.created_at', [$f['start'], $f['end']])
                ->when($f['location_id'], fn ($q) => $q->where('o.location_id', $f['location_id']))
                ->groupBy('c.id', 'c.name', 'c.phone', 'c.due_balance')
                ->select([
                    'c.id as id',
                    'c.name as name',
                    'c.phone as phone',
                    'c.due_balance as due_balance',
                    DB::raw('COUNT(DISTINCT o.id) as order_count'),
                    DB::raw('COALESCE(SUM(o.payable_total),0) as revenue'),
                    DB::raw('COALESCE(AVG(o.payable_total),0) as avg_order_value'),
                    DB::raw('COALESCE(SUM(costs.cost),0) as cost'),
                    DB::raw('MIN(o.created_at) as first_order_at'),
                    DB::raw('MAX(o.created_at) as last_order_at'),
                ])
                ->get()
                ->map(function ($r) {
                    $revenue = (float) $r->revenue;
                    $cost = (float) $r->cost;
                    $profit = $revenue - $cost;
                    $margin = $revenue > 0 ? round(($profit / $revenue) * 100, 2) : 0;

                    return [
                        'id' => $r->id,
                        'name' => $r->name,
                        'phone' => $r->phone,
                        'due_balance' => (float) $r->due_balance,
                        'order_count' => (int) $r->order_count,
                        'is_repeat' => $r->order_count > 1,
                        'revenue' => $revenue,
                        'avg_order_value' => (float) $r->avg_order_value,
                        'cost' => $cost,
                        'profit' => $profit,
                        'margin' => $margin,
                        'first_order_at' => $r->first_order_at,
                        'last_order_at' => $r->last_order_at,
                    ];
                });

            $sortKey = ['revenue' => 'revenue', 'profit' => 'profit', 'orders' => 'order_count', 'due' => 'due_balance'][$sort];
            $rows = $dir === 'asc' ? $rows->sortBy($sortKey) : $rows->sortByDesc($sortKey);
            $rows = $rows->values();

            $total = $rows->count();
            $items = $rows->forPage($page, $perPage)->values();

            return [
                'sort' => $sort,
                'dir' => $dir,
                'data' => $items,
                'meta' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => (int) max(1, ceil($total / $perPage)),
                ],
                'summary' => [
                    'total_customers' => $total,
                    'repeat_customers' => $rows->where('is_repeat', true)->count(),
                ],
            ];
        });

        return response()->json($payload)->header('Cache-Control', 'private, max-age=30');
    }

    /* =========================
     | LOCATIONS
     ========================= */
    public function locations(Request $request): JsonResponse
    {
        $f = $this->filters($request);
        $key = $this->cacheKey('locations', $f);

        $payload = Cache::remember($key, self::TTL, function () use ($f) {
            $locations = Location::where('is_active', true)->orderBy('name')->get(['id', 'name']);

            $costPerOrder = $this->baseCogsQuery()
                ->selectRaw('oi.order_id, SUM(GREATEST(COALESCE(oi.quantity,0) - COALESCE(oi.returned_qty,0), 0) * COALESCE(pb.buy_price,0)) as cost')
                ->groupBy('oi.order_id');

            $sales = DB::table('orders as o')
                ->leftJoinSub($costPerOrder, 'costs', 'costs.order_id', '=', 'o.id')
                ->tap(fn ($q) => $this->excludeSplitChildren($q))
                ->tap(fn ($q) => $this->excludeInvalidStatuses($q))
                ->whereBetween('o.created_at', [$f['start'], $f['end']])
                ->groupBy('o.location_id')
                ->select([
                    'o.location_id as location_id',
                    DB::raw('COUNT(*) as order_count'),
                    DB::raw('COALESCE(SUM(o.payable_total),0) as gross_sales'),
                    DB::raw('COALESCE(SUM(costs.cost),0) as cogs'),
                ])
                ->get()
                ->keyBy('location_id');

            // Attributed to the order's own location/date, not the return's --
            // otherwise a return processed elsewhere/later than the sale it
            // belongs to would dock the wrong location's or period's profit.
            $refunds = $this->baseReturnsQuery()
                ->join('orders as o', 'r.order_id', '=', 'o.id')
                ->whereBetween('o.created_at', [$f['start'], $f['end']])
                ->groupBy('o.location_id')
                ->select([
                    'o.location_id as location_id',
                    DB::raw('COALESCE(SUM(ri.refund_amount),0) as refunds'),
                ])
                ->get()
                ->keyBy('location_id');

            $expenses = DB::table('expenses as e')
                ->whereNull('e.deleted_at')
                ->whereBetween('e.expense_date', [$f['start']->toDateString(), $f['end']->toDateString()])
                ->groupBy('e.location_id')
                ->select([
                    'e.location_id as location_id',
                    DB::raw('COALESCE(SUM(e.amount),0) as expenses'),
                ])
                ->get()
                ->keyBy('location_id');

            $rows = $locations->map(function ($loc) use ($sales, $refunds, $expenses) {
                $s = $sales->get($loc->id);
                $grossSales = (float) ($s->gross_sales ?? 0);
                $refundAmt = (float) ($refunds->get($loc->id)->refunds ?? 0);
                $cogs = (float) ($s->cogs ?? 0);
                $expenseAmt = (float) ($expenses->get($loc->id)->expenses ?? 0);

                // $grossSales (o.payable_total) is already net of returns at
                // the source -- don't subtract $refundAmt again (see the
                // matching note in FinancialDashboardController::buildMetrics()).
                $netSales = $grossSales;
                $grossProfit = $netSales - $cogs;
                $netProfit = $grossProfit - $expenseAmt;
                $margin = $netSales > 0 ? round(($netProfit / $netSales) * 100, 2) : 0;

                return [
                    'id' => $loc->id,
                    'name' => $loc->name,
                    'order_count' => (int) ($s->order_count ?? 0),
                    'gross_sales' => $grossSales,
                    'refunds' => $refundAmt,
                    'net_sales' => $netSales,
                    'cogs' => $cogs,
                    'gross_profit' => $grossProfit,
                    'expenses' => $expenseAmt,
                    'net_profit' => $netProfit,
                    'margin' => $margin,
                ];
            })->sortByDesc('net_sales')->values();

            return [
                'data' => $rows,
                'multi_location' => $locations->count() > 1,
            ];
        });

        return response()->json($payload)->header('Cache-Control', 'private, max-age=30');
    }

    /* =========================
     | EXPORT
     ========================= */
    public function export(Request $request)
    {
        $validated = $request->validate([
            'format' => 'nullable|in:csv,xlsx,pdf',
            'type' => 'nullable|in:products,customers,locations,trends',
            'date_range' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'location_id' => 'nullable|integer|exists:locations,id',
            'group_by' => 'nullable|in:product,category',
        ]);

        $format = $validated['format'] ?? 'csv';
        $type = $validated['type'] ?? 'products';
        $f = $this->filters($request);

        // Reuse the exact same JSON endpoints for the export data, just
        // asking for every row on one page instead of a paginated slice.
        $fullRequest = clone $request;
        $fullRequest->merge(['per_page' => 100000, 'page' => 1]);

        $sections = match ($type) {
            'customers' => $this->customersExportSections($fullRequest),
            'locations' => $this->locationsExportSections($fullRequest),
            'trends' => $this->trendsExportSections($fullRequest),
            default => $this->productsExportSections($fullRequest),
        };

        $filename = 'reports_' . $type . '_' . $f['start']->format('Ymd') . '_' . $f['end']->format('Ymd');
        $title = 'Reports & Analytics — ' . ucfirst($type);

        return TabularExporter::respond($format, $filename, $title, $sections);
    }

    private function productsExportSections(Request $request): array
    {
        $data = $this->products($request)->getData(true);
        $isCategory = $data['group_by'] === 'category';
        $money = fn ($v) => currency_bdt($v);

        $rows = collect($data['data'])->map(fn ($r) => $isCategory ? [
            $r['name'], $r['product_count'], $r['qty'], $money($r['revenue']), $money($r['cost']), $money($r['profit']), number_format($r['margin'], 2) . '%',
        ] : [
            $r['name'], $r['category_name'] ?? '-', $r['qty'], $money($r['revenue']), $money($r['cost']), $money($r['profit']), number_format($r['margin'], 2) . '%',
        ])->all();

        return [[
            'heading' => $isCategory ? 'Category Profitability' : 'Product Profitability',
            'columns' => $isCategory
                ? ['Category', 'Products', 'Qty Sold', 'Revenue', 'Cost', 'Profit', 'Margin']
                : ['Product', 'Category', 'Qty Sold', 'Revenue', 'Cost', 'Profit', 'Margin'],
            'rows' => $rows,
        ]];
    }

    private function customersExportSections(Request $request): array
    {
        $data = $this->customers($request)->getData(true);
        $money = fn ($v) => currency_bdt($v);

        $rows = collect($data['data'])->map(fn ($r) => [
            $r['name'], $r['phone'], $r['order_count'], $r['is_repeat'] ? 'Yes' : 'No',
            $money($r['revenue']), $money($r['profit']), number_format($r['margin'], 2) . '%', $money($r['due_balance']),
        ])->all();

        return [[
            'heading' => 'Customer Profitability',
            'columns' => ['Customer', 'Phone', 'Orders', 'Repeat', 'Revenue', 'Profit', 'Margin', 'Due Balance'],
            'rows' => $rows,
        ]];
    }

    private function locationsExportSections(Request $request): array
    {
        $data = $this->locations($request)->getData(true);
        $money = fn ($v) => currency_bdt($v);

        $rows = collect($data['data'])->map(fn ($r) => [
            $r['name'], $r['order_count'], $money($r['gross_sales']), $money($r['refunds']), $money($r['net_sales']),
            $money($r['cogs']), $money($r['expenses']), $money($r['net_profit']), number_format($r['margin'], 2) . '%',
        ])->all();

        return [[
            'heading' => 'Location Breakdown',
            'columns' => ['Location', 'Orders', 'Gross Sales', 'Refunds', 'Net Sales', 'COGS', 'Expenses', 'Net Profit', 'Margin'],
            'rows' => $rows,
        ]];
    }

    private function trendsExportSections(Request $request): array
    {
        $data = $this->trends($request)->getData(true);
        $money = fn ($v) => currency_bdt($v);
        $c = $data['current'];
        $p = $data['previous'];

        $summaryRows = [
            ['Total Orders', $c['total_orders'], $p['total_orders']],
            ['Net Sales', $money($c['net_sales']), $money($p['net_sales'])],
            ['COGS', $money($c['cogs']), $money($p['cogs'])],
            ['Gross Profit', $money($c['gross_profit']), $money($p['gross_profit'])],
            ['Expenses', $money($c['expenses']), $money($p['expenses'])],
            ['Net Profit', $money($c['net_profit']), $money($p['net_profit'])],
            ['Profit Margin', number_format($c['profit_margin'], 2) . '%', number_format($p['profit_margin'], 2) . '%'],
        ];

        $dailyRows = collect($data['daily'])->map(fn ($r) => [
            $r['date'], $money($r['net_sales']), $money($r['cogs']), $money($r['expenses']), $money($r['net_profit']),
        ])->all();

        return [
            [
                'heading' => 'Period Comparison (Current vs Previous)',
                'columns' => ['Metric', 'Current', 'Previous'],
                'rows' => $summaryRows,
            ],
            [
                'heading' => 'Daily Trend',
                'columns' => ['Date', 'Net Sales', 'COGS', 'Expenses', 'Net Profit'],
                'rows' => $dailyRows,
            ],
        ];
    }

    /* =========================
     | TRENDS (period-over-period + daily series)
     ========================= */
    public function trends(Request $request): JsonResponse
    {
        $f = $this->filters($request);
        $key = $this->cacheKey('trends', $f);

        $payload = Cache::remember($key, self::TTL, function () use ($f) {
            [$prevStart, $prevEnd] = $this->precedingRange($f['start'], $f['end']);

            $current = $this->periodTotals($f['start'], $f['end'], $f['location_id']);
            $previous = $this->periodTotals($prevStart, $prevEnd, $f['location_id']);

            $delta = fn ($cur, $prev) => $prev > 0
                ? round((($cur - $prev) / $prev) * 100, 2)
                : ($cur > 0 ? 100.0 : 0.0);

            $daily = $this->dailySeries($f['start'], $f['end'], $f['location_id']);

            return [
                'current' => $current,
                'previous' => $previous,
                'previous_period' => [
                    'from' => $prevStart->toDateString(),
                    'to' => $prevEnd->toDateString(),
                ],
                'deltas' => [
                    'net_sales' => $delta($current['net_sales'], $previous['net_sales']),
                    'net_profit' => $delta($current['net_profit'], $previous['net_profit']),
                    'profit_margin' => $delta($current['profit_margin'], $previous['profit_margin']),
                    'total_orders' => $delta($current['total_orders'], $previous['total_orders']),
                ],
                'daily' => $daily,
            ];
        });

        return response()->json($payload)->header('Cache-Control', 'private, max-age=30');
    }

    private function periodTotals(Carbon $start, Carbon $end, ?int $locationId): array
    {
        $f = ['start' => $start, 'end' => $end, 'location_id' => $locationId];

        $salesAgg = $this->validOrdersInRange($f)
            ->selectRaw('COUNT(*) as total_orders, COALESCE(SUM(o.payable_total),0) as total_sales')
            ->first();

        $refunds = (float) ($this->baseReturnsQuery()
            ->join('orders as o', 'r.order_id', '=', 'o.id')
            ->whereBetween('o.created_at', [$start, $end])
            ->when($locationId, fn ($q) => $q->where('o.location_id', $locationId))
            ->value(DB::raw('COALESCE(SUM(ri.refund_amount),0)')) ?? 0);

        $cogs = (float) ($this->baseCogsQuery()
            ->tap(fn ($q) => $this->excludeSplitChildren($q))
            ->tap(fn ($q) => $this->excludeInvalidStatuses($q))
            ->whereBetween('o.created_at', [$start, $end])
            ->when($locationId, fn ($q) => $q->where('o.location_id', $locationId))
            ->value(DB::raw('COALESCE(SUM(GREATEST(COALESCE(oi.quantity,0) - COALESCE(oi.returned_qty,0), 0) * COALESCE(pb.buy_price,0)),0)')) ?? 0);

        $expenses = (float) (DB::table('expenses as e')
            ->whereNull('e.deleted_at')
            ->whereBetween('e.expense_date', [$start->toDateString(), $end->toDateString()])
            ->when($locationId, fn ($q) => $q->where('e.location_id', $locationId))
            ->value(DB::raw('COALESCE(SUM(e.amount),0)')) ?? 0);

        $totalSales = (float) ($salesAgg->total_sales ?? 0);
        // $totalSales (o.payable_total) is already net of returns at the
        // source -- don't subtract $refunds again (see the matching note in
        // FinancialDashboardController::buildMetrics()). $refunds stays a
        // separate, purely informational figure below.
        $netSales = $totalSales;
        $grossProfit = $netSales - $cogs;
        $netProfit = $grossProfit - $expenses;
        $margin = $netSales > 0 ? round(($netProfit / $netSales) * 100, 2) : 0;

        return [
            'total_orders' => (int) ($salesAgg->total_orders ?? 0),
            'total_sales' => $totalSales,
            'refunds' => $refunds,
            'net_sales' => $netSales,
            'cogs' => $cogs,
            'gross_profit' => $grossProfit,
            'expenses' => $expenses,
            'net_profit' => $netProfit,
            'profit_margin' => $margin,
        ];
    }

    private function dailySeries(Carbon $start, Carbon $end, ?int $locationId): array
    {
        $f = ['start' => $start, 'end' => $end, 'location_id' => $locationId];

        $revMap = $this->validOrdersInRange($f)
            ->selectRaw('DATE(o.created_at) as date, COALESCE(SUM(o.payable_total),0) as revenue')
            ->groupBy(DB::raw('DATE(o.created_at)'))
            ->get()->keyBy('date')->map(fn ($r) => (float) $r->revenue);

        $refundMap = $this->baseReturnsQuery()
            ->join('orders as o', 'r.order_id', '=', 'o.id')
            ->whereBetween('o.created_at', [$start, $end])
            ->when($locationId, fn ($q) => $q->where('o.location_id', $locationId))
            ->selectRaw('DATE(o.created_at) as date, COALESCE(SUM(ri.refund_amount),0) as refunds')
            ->groupBy(DB::raw('DATE(o.created_at)'))
            ->get()->keyBy('date')->map(fn ($r) => (float) $r->refunds);

        $cogsMap = $this->baseCogsQuery()
            ->tap(fn ($q) => $this->excludeSplitChildren($q))
            ->tap(fn ($q) => $this->excludeInvalidStatuses($q))
            ->whereBetween('o.created_at', [$start, $end])
            ->when($locationId, fn ($q) => $q->where('o.location_id', $locationId))
            ->selectRaw('DATE(o.created_at) as date, COALESCE(SUM(GREATEST(COALESCE(oi.quantity,0) - COALESCE(oi.returned_qty,0), 0) * COALESCE(pb.buy_price,0)),0) as cogs')
            ->groupBy(DB::raw('DATE(o.created_at)'))
            ->get()->keyBy('date')->map(fn ($r) => (float) $r->cogs);

        $expMap = DB::table('expenses as e')
            ->whereNull('e.deleted_at')
            ->whereBetween('e.expense_date', [$start->toDateString(), $end->toDateString()])
            ->when($locationId, fn ($q) => $q->where('e.location_id', $locationId))
            ->selectRaw('DATE(e.expense_date) as date, COALESCE(SUM(e.amount),0) as amount')
            ->groupBy(DB::raw('DATE(e.expense_date)'))
            ->get()->keyBy('date')->map(fn ($r) => (float) $r->amount);

        $allDates = collect()
            ->merge($revMap->keys())->merge($refundMap->keys())
            ->merge($cogsMap->keys())->merge($expMap->keys())
            ->unique()->sort()->values();

        return $allDates->map(function ($d) use ($revMap, $refundMap, $cogsMap, $expMap) {
            $rev = (float) ($revMap[$d] ?? 0);
            $refund = (float) ($refundMap[$d] ?? 0);
            $cogs = (float) ($cogsMap[$d] ?? 0);
            $exp = (float) ($expMap[$d] ?? 0);

            // $rev (o.payable_total) is already net of returns -- don't
            // subtract $refund again (see the note in periodTotals() above).
            $netSales = $rev;
            $grossProfit = $netSales - $cogs;
            $netProfit = $grossProfit - $exp;

            return [
                'date' => $d,
                'net_sales' => $netSales,
                'refunds' => $refund,
                'cogs' => $cogs,
                'expenses' => $exp,
                'net_profit' => $netProfit,
            ];
        })->values()->all();
    }
}
