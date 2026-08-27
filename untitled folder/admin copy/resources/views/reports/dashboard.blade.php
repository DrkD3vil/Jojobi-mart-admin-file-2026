@extends('layouts.app')

@section('content')
    <style>
        .financial-dashboard {
            padding: 20px;
            max-width: 100%;
            overflow-x: hidden;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-color);
        }

        .header-left .page-title {
            font-size: 24px;
            font-weight: 600;
            margin: 0 0 4px 0;
            color: var(--text-primary);
        }

        .page-subtitle {
            color: var(--text-secondary);
            font-size: 14px;
            margin: 0;
        }

        .header-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .btn {
            padding: 8px 16px;
            border-radius: var(--radius);
            border: none;
            font-weight: 500;
            cursor: pointer;
            transition: all var(--transition-fast);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--primary-foreground);
        }

        .btn-secondary {
            background: var(--secondary);
            color: var(--secondary-foreground);
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--card-shadow-hover);
        }

        .filters-container {
            background: var(--card);
            border-radius: var(--radius);
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid var(--border-color);
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-group label {
            font-size: 12px;
            font-weight: 500;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-select,
        .filter-input {
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            background: var(--input);
            color: var(--foreground);
            font-size: 14px;
        }

        .realtime-status {
            display: flex;
            gap: 24px;
            padding: 12px 16px;
            background: var(--card);
            border-radius: var(--radius);
            margin-bottom: 20px;
            border: 1px solid var(--border-color);
            flex-wrap: wrap;
        }

        .status-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-label {
            color: var(--text-secondary);
            font-size: 14px;
        }

        .status-value {
            font-weight: 600;
            font-size: 14px;
        }

        .badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge.new {
            background: var(--success);
            color: white;
        }

        .badge.pending {
            background: var(--warning);
            color: white;
        }

        .badge.warning {
            background: var(--warning);
            color: white;
        }

        .badge.danger {
            background: var(--danger);
            color: white;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .metric-card {
            background: var(--card);
            border-radius: var(--radius);
            padding: 20px;
            border: 1px solid var(--border-color);
            transition: all var(--transition-normal);
        }

        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--card-shadow-hover);
        }

        .metric-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .metric-title {
            font-size: 16px;
            font-weight: 500;
            color: var(--text-secondary);
            margin: 0;
        }

        .metric-trend {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            font-weight: 500;
            padding: 2px 8px;
            border-radius: 12px;
        }

        .metric-trend.positive {
            background: var(--success);
            color: white;
        }

        .metric-trend.negative {
            background: var(--danger);
            color: white;
        }

        .metric-value {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 16px;
            color: var(--text-primary);
        }

        .metric-value.positive {
            color: var(--success);
        }

        .metric-value.negative {
            color: var(--danger);
        }

        .metric-details {
            display: flex;
            justify-content: space-between;
            padding-top: 12px;
            border-top: 1px solid var(--border-color);
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .detail-label {
            font-size: 12px;
            color: var(--text-secondary);
        }

        .detail-value {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .charts-section {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        @media (max-width: 1200px) {
            .charts-section {
                grid-template-columns: 1fr;
            }
        }

        .chart-container {
            background: var(--card);
            border-radius: var(--radius);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .chart-container.large {
            grid-column: span 2;
        }

        @media (max-width: 1200px) {
            .chart-container.large {
                grid-column: span 1;
            }
        }

        .chart-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chart-title {
            font-size: 16px;
            font-weight: 500;
            margin: 0;
            color: var(--text-primary);
        }

        .chart-content {
            padding: 20px;
            height: 300px;
        }

        .tables-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        @media (max-width: 768px) {
            .tables-section {
                grid-template-columns: 1fr;
            }
        }

        .table-container {
            background: var(--card);
            border-radius: var(--radius);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .table-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-title {
            font-size: 16px;
            font-weight: 500;
            margin: 0;
            color: var(--text-primary);
        }

        .table-link {
            color: var(--primary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead {
            background: var(--secondary);
        }

        .data-table th {
            padding: 12px 16px;
            text-align: left;
            font-weight: 500;
            font-size: 12px;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border-color);
        }

        .data-table td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        .data-table tbody tr:hover {
            background: var(--secondary);
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .product-thumb {
            width: 40px;
            height: 40px;
            border-radius: var(--radius);
            object-fit: cover;
        }

        .product-name {
            font-weight: 500;
            font-size: 14px;
        }

        .product-sku {
            font-size: 12px;
            color: var(--text-secondary);
        }

        .numeric {
            text-align: right;
            font-family: 'Courier New', monospace;
        }

        .insights-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .insight-card {
            background: var(--card);
            border-radius: var(--radius);
            padding: 20px;
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: all var(--transition-normal);
        }

        .insight-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--card-shadow-hover);
        }

        .insight-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .insight-icon.success {
            background: var(--success);
            color: white;
        }

        .insight-icon.warning {
            background: var(--warning);
            color: white;
        }

        .insight-icon.info {
            background: var(--info);
            color: white;
        }

        .insight-icon.primary {
            background: var(--primary);
            color: var(--primary-foreground);
        }

        .insight-title {
            font-size: 14px;
            font-weight: 500;
            margin: 0 0 4px 0;
            color: var(--text-primary);
        }

        .insight-text {
            font-size: 12px;
            color: var(--text-secondary);
            margin: 0;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--glass-base);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loading-content {
            background: var(--card);
            padding: 30px;
            border-radius: var(--radius);
            text-align: center;
            border: 1px solid var(--border-color);
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid var(--border-color);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 16px auto;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .loading-text {
            color: var(--text-primary);
            font-size: 14px;
        }

        /* Theme Toggle */
        .theme-toggle {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: var(--muted);
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: var(--primary);
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }

        .theme-label {
            font-size: 14px;
            color: var(--text-secondary);
        }
    </style>



{{-- resources/views/reports/dashboard.blade.php --}}
{{-- NOTE: no style changes, only full working Blade + JS matching your controller keys --}}

{{-- resources/views/reports/dashboard.blade.php --}}
{{-- NOTE: no style changes, only full working Blade + JS matching your controller keys --}}


<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="financial-dashboard">

    <!-- Header -->
    <div class="dashboard-header">
        <div class="header-left">
            <h1 class="page-title">Financial Dashboard</h1>
            <p class="page-subtitle">Real-time business analytics & insights</p>
        </div>
        <div class="header-actions">
            <button class="btn btn-secondary" id="refreshBtn">Refresh</button>
            <button class="btn btn-primary" id="exportBtn">Export Report</button>
            <div class="theme-toggle">
                <label class="switch">
                    <input type="checkbox" id="themeToggle">
                    <span class="slider"></span>
                </label>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-container">
        <div class="filters-grid">
            <div class="filter-group">
                <label>Date Range</label>
                <select id="dateRange" class="filter-select">
                    @foreach ($dateRanges as $value => $label)
                        <option value="{{ $value }}" {{ $currentRange == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group" id="customDateRange" style="display: {{ $currentRange == 'custom' ? 'flex' : 'none' }};">
                <div>
                    <label>Start Date</label>
                    <input type="date" id="startDate" class="filter-input" value="{{ $startDate }}">
                </div>
                <div>
                    <label>End Date</label>
                    <input type="date" id="endDate" class="filter-input" value="{{ $endDate }}">
                </div>
            </div>

            <div class="filter-group">
                <label>Order Status</label>
                <select id="statusFilter" class="filter-select">
                    <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>All Statuses</option>
                    <option value="pending" {{ $statusFilter == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ $statusFilter == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="completed" {{ $statusFilter == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $statusFilter == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Payment Status</label>
                <select id="paymentStatus" class="filter-select">
                    <option value="all" {{ $paymentStatus == 'all' ? 'selected' : '' }}>All Payments</option>
                    <option value="paid" {{ $paymentStatus == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="partial" {{ $paymentStatus == 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="due" {{ $paymentStatus == 'due' ? 'selected' : '' }}>Due</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Location</label>
                <select id="locationFilter" class="filter-select">
                    <option value="">All Locations</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}"
                            {{ (int) $locationId === (int) $location->id ? 'selected' : '' }}>
                            {{ $location->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <button id="applyFilters" class="btn btn-primary">Apply Filters</button>
                <button id="resetFilters" class="btn btn-secondary">Reset</button>
            </div>
        </div>
    </div>

    <!-- Real-time Status Bar -->
    <div class="realtime-status">
        <div class="status-item">
            <span class="status-label">Last Update:</span>
            <span class="status-value" id="lastUpdateTime">{{ now()->format('H:i:s') }}</span>
        </div>
        <div class="status-item">
            <span class="status-label">New Orders:</span>
            <span class="status-value badge new" id="newOrdersCount">0</span>
        </div>
        <div class="status-item">
            <span class="status-label">Pending:</span>
            <span class="status-value badge pending" id="pendingOrdersCount">{{ (int) $pending_orders }}</span>
        </div>
        <div class="status-item">
            <span class="status-label">Abandoned Carts:</span>
            <span class="status-value badge warning" id="abandonedCartsCount">{{ (int) $abandoned_carts }}</span>
        </div>
        <div class="status-item">
            <span class="status-label">Low Stock:</span>
            <span class="status-value badge danger" id="lowStockCount">0</span>
        </div>

        {{-- ✅ Expense status --}}
        <div class="status-item">
            <span class="status-label">Expenses:</span>
            <span class="status-value badge warning" id="expensesTotalStatus">৳0.00</span>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="metrics-grid">
        <div class="metric-card sales">
            <div class="metric-header">
                <h3 class="metric-title">Total Sales</h3>
            </div>
            <div class="metric-value" id="m_total_sales">৳0.00</div>
            <div class="metric-details">
                <div class="detail-item"><span class="detail-label">Orders:</span><span class="detail-value" id="m_total_orders">0</span></div>
                <div class="detail-item"><span class="detail-label">Avg Order:</span><span class="detail-value" id="m_avg_order">৳0.00</span></div>
            </div>
        </div>

        <div class="metric-card profit">
            <div class="metric-header">
                <h3 class="metric-title">Net Profit</h3>
                <div class="metric-trend" id="m_profit_badge">
                    <span class="trend-value" id="m_profit_margin">0.0%</span>
                </div>
            </div>
            <div class="metric-value" id="m_net_profit">৳0.00</div>
            <div class="metric-details">
                <div class="detail-item"><span class="detail-label">Revenue:</span><span class="detail-value" id="m_net_sales">৳0.00</span></div>
                <div class="detail-item"><span class="detail-label">COGS:</span><span class="detail-value" id="m_cogs">৳0.00</span></div>
            </div>
        </div>

        <div class="metric-card payments">
            <div class="metric-header">
                <h3 class="metric-title">Payments</h3>
            </div>
            <div class="metric-value" id="m_total_payments">৳0.00</div>
            <div class="metric-details">
                <div class="detail-item"><span class="detail-label">Orders Paid:</span><span class="detail-value" id="m_orders_paid">0</span></div>
                <div class="detail-item"><span class="detail-label">Methods:</span><span class="detail-value" id="m_methods_count">0</span></div>
            </div>
        </div>

        <div class="metric-card customers">
            <div class="metric-header">
                <h3 class="metric-title">Customers</h3>
            </div>
            <div class="metric-value" id="m_new_customers">0</div>
            <div class="metric-details">
                <div class="detail-item"><span class="detail-label">Reward Points:</span><span class="detail-value" id="m_reward_points">0</span></div>
                <div class="detail-item"><span class="detail-label">Avg Due:</span><span class="detail-value" id="m_avg_due">৳0.00</span></div>
            </div>
        </div>

        <div class="metric-card returns">
            <div class="metric-header">
                <h3 class="metric-title">Returns & Refunds</h3>
            </div>
            <div class="metric-value" id="m_total_refunds">৳0.00</div>
            <div class="metric-details">
                <div class="detail-item"><span class="detail-label">Returns:</span><span class="detail-value" id="m_total_returns">0</span></div>
                <div class="detail-item"><span class="detail-label">Exchanges:</span><span class="detail-value" id="m_total_exchanges">0</span></div>
            </div>
        </div>

        <div class="metric-card stock">
            <div class="metric-header">
                <h3 class="metric-title">Stock Value</h3>
            </div>
            <div class="metric-value" id="m_stock_value">৳0.00</div>
            <div class="metric-details">
                <div class="detail-item"><span class="detail-label">Available:</span><span class="detail-value" id="m_available_stock">0</span></div>
                <div class="detail-item"><span class="detail-label">Reserved:</span><span class="detail-value" id="m_reserved_stock">0</span></div>
            </div>
        </div>

        {{-- ✅ Expenses metric card (matches controller metrics keys) --}}
        <div class="metric-card expense">
            <div class="metric-header">
                <h3 class="metric-title">Expenses</h3>
            </div>
            <div class="metric-value" id="m_expenses_total">৳0.00</div>
            <div class="metric-details">
                <div class="detail-item"><span class="detail-label">Count:</span><span class="detail-value" id="m_expenses_count">0</span></div>
                <div class="detail-item"><span class="detail-label">Top Category:</span><span class="detail-value" id="m_expenses_top_category">—</span></div>
                <div class="detail-item"><span class="detail-label">Top Method:</span><span class="detail-value" id="m_expenses_top_method">—</span></div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="charts-section">
        <div class="chart-container large">
            <div class="chart-header">
                <h3 class="chart-title">Daily Sales Trend</h3>
                <div class="chart-controls">
                    {{-- client-side slice of already returned dates --}}
                    <select class="chart-select" id="chartPeriod">
                        <option value="all" selected>All (Selected Range)</option>
                        <option value="7">Last 7 Days</option>
                        <option value="30">Last 30 Days</option>
                        <option value="90">Last 90 Days</option>
                    </select>
                </div>
            </div>
            <div class="chart-content"><canvas id="salesChart"></canvas></div>
        </div>

        <div class="chart-container">
            <div class="chart-header"><h3 class="chart-title">Payment Methods</h3></div>
            <div class="chart-content"><canvas id="paymentMethodsChart"></canvas></div>
        </div>

        <div class="chart-container">
            <div class="chart-header"><h3 class="chart-title">Order Status Distribution</h3></div>
            <div class="chart-content"><canvas id="orderStatusChart"></canvas></div>
        </div>

        {{-- ✅ Extra plots from controller charts payload --}}
        <div class="chart-container large">
            <div class="chart-header"><h3 class="chart-title">Daily Expenses</h3></div>
            <div class="chart-content"><canvas id="expensesChart"></canvas></div>
        </div>

        <div class="chart-container large">
            <div class="chart-header"><h3 class="chart-title">Daily Net Profit</h3></div>
            <div class="chart-content"><canvas id="profitChart"></canvas></div>
        </div>

        <div class="chart-container large">
            <div class="chart-header"><h3 class="chart-title">Profit Components (Net Sales / COGS / Expenses)</h3></div>
            <div class="chart-content"><canvas id="componentsChart"></canvas></div>
        </div>
    </div>

    <!-- Tables -->
    <div class="tables-section">
        <div class="table-container">
            <div class="table-header">
                <h3 class="table-title">Recent Orders</h3>
                <a href="{{ route('orders.index') }}" class="table-link">View All</a>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Payment</th>
                        </tr>
                    </thead>
                    <tbody id="recentOrdersBody"></tbody>
                </table>
                <div class="table-footer" id="paginationSection"></div>
            </div>
        </div>

        {{-- ✅ Recent expenses table (from tables.recent_expenses) --}}
        <div class="table-container">
            <div class="table-header">
                <h3 class="table-title">Recent Expenses</h3>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Expense #</th>
                            <th>Date</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Method</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody id="recentExpensesBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="loading-overlay" style="display:none;">
    <div class="loading-content">
        <div class="loading-spinner"></div>
        <div class="loading-text">
            Loading dashboard data... <span id="loadingPct">0%</span>
        </div>
        <div class="loading-bar">
            <div class="loading-bar-fill" id="loadingBarFill" style="width:0%"></div>
        </div>
        <div class="loading-subtext" id="loadingHint">Preparing…</div>
    </div>
</div>

<style>
    .flash { animation: flashPulse 1s ease-in-out 2; }
    @keyframes flashPulse { 0%{transform:scale(1)} 50%{transform:scale(1.15)} 100%{transform:scale(1)} }
    .loading-bar{ width:260px;height:10px;background:rgba(255,255,255,.18);border-radius:999px;overflow:hidden;margin-top:10px; }
    .loading-bar-fill{ height:100%;width:0%;background:rgba(255,255,255,.85);border-radius:999px;transition:width .2s ease; }
    .loading-subtext{ margin-top:8px;font-size:12px;opacity:.85; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
(() => {
    const URL_METRICS  = "{{ route('dashboard.financial.metrics') }}";
    const URL_CHARTS   = "{{ route('dashboard.financial.charts') }}";
    const URL_TABLES   = "{{ route('dashboard.financial.tables') }}";
    const URL_REALTIME = "{{ route('dashboard.financial.realtime') }}";
    const URL_STREAM   = "{{ route('dashboard.financial.stream') }}";
    const URL_EXPORT   = "{{ route('dashboard.financial.export') }}";

    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    const $ = (id) => document.getElementById(id);

    let salesChart = null;
    let paymentMethodsChart = null;
    let orderStatusChart = null;

    let expensesChart = null;
    let profitChart = null;
    let componentsChart = null;

    let lastServerTimestamp = new Date().toISOString();
    let realtimeTimer = null;
    let stream = null;

    // ✅ prevent overlapping loads
    let loadAbort = null;

    // ✅ keep last payload for client-side chart slicing
    let lastChartsPayload = null;

    /* -------------------------
       Loading UI
    ------------------------- */
    let loadingRampTimer = null;
    let loadingProgress = 0;

    function setLoadingProgress(pct, hintText = null) {
        loadingProgress = Math.max(0, Math.min(100, Math.round(pct)));
        const pctEl = $('loadingPct');
        const barEl = $('loadingBarFill');
        const hintEl = $('loadingHint');
        if (pctEl) pctEl.textContent = loadingProgress + '%';
        if (barEl) barEl.style.width = loadingProgress + '%';
        if (hintEl && hintText) hintEl.textContent = hintText;
    }

    function showLoading() {
        $('loadingOverlay').style.display = 'flex';
        setLoadingProgress(0, 'Starting…');
        if (loadingRampTimer) clearInterval(loadingRampTimer);
        loadingRampTimer = setInterval(() => {
            if (loadingProgress < 90) setLoadingProgress(loadingProgress + 1);
        }, 120);
    }

    function hideLoading() {
        setLoadingProgress(100, 'Done');
        if (loadingRampTimer) clearInterval(loadingRampTimer);
        loadingRampTimer = null;
        setTimeout(() => {
            $('loadingOverlay').style.display = 'none';
            setLoadingProgress(0, 'Preparing…');
        }, 200);
    }

    function flash(el) {
        if (!el) return;
        el.classList.add('flash');
        setTimeout(() => el.classList.remove('flash'), 1500);
    }

    function debounce(fn, ms = 250) {
        let t = null;
        return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
    }

    /* -------------------------
       Filters -> controller inputs
    ------------------------- */
    function getFilters() {
        const params = new URLSearchParams();

        const dateRange = $('dateRange').value;
        params.set('date_range', dateRange);

        if (dateRange === 'custom') {
            const sd = $('startDate').value;
            const ed = $('endDate').value;
            if (sd) params.set('start_date', sd);
            if (ed) params.set('end_date', ed);
        }

        params.set('status', $('statusFilter').value);
        params.set('payment_status', $('paymentStatus').value);

        const loc = $('locationFilter').value;
        if (loc) params.set('location_id', loc);

        return params;
    }

    function setLastUpdate(tsIso) {
        lastServerTimestamp = tsIso || new Date().toISOString();
        const d = new Date(lastServerTimestamp);
        $('lastUpdateTime').textContent = d.toLocaleTimeString([], { hour:'2-digit', minute:'2-digit', second:'2-digit' });
    }

    /* -------------------------
       Formatters (BDT)
    ------------------------- */
    const BDT_FMT = new Intl.NumberFormat('en-BD', {
        style: 'currency',
        currency: 'BDT',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    const NUM_FMT = new Intl.NumberFormat('en-BD');

    function bdt(n) {
        const x = Number(n || 0);
        // Intl will use ৳ for BDT in most environments
        // Force symbol just in case:
        let s = BDT_FMT.format(x);
        if (!s.includes('৳')) s = '৳' + x.toFixed(2);
        return s;
    }

    function compactBdt(n) {
        const x = Number(n || 0);
        const abs = Math.abs(x);
        if (abs >= 10000000) return '৳' + (x/10000000).toFixed(2) + 'Cr';
        if (abs >= 100000)   return '৳' + (x/100000).toFixed(2) + 'L';
        if (abs >= 1000)     return '৳' + (x/1000).toFixed(2) + 'K';
        return bdt(x);
    }

    function pickTopLabel(arr, key) {
        if (!Array.isArray(arr) || arr.length === 0) return '—';
        return arr[0]?.[key] ?? '—';
    }

    /* -------------------------
       Colors
    ------------------------- */
    function palette() {
        return {
            blue:   'rgba(59,130,246,1)',
            sky:    'rgba(14,165,233,1)',
            violet: 'rgba(139,92,246,1)',
            green:  'rgba(34,197,94,1)',
            amber:  'rgba(245,158,11,1)',
            rose:   'rgba(244,63,94,1)',
            slate:  'rgba(100,116,139,1)',
            grid:   'rgba(148,163,184,.25)',
            tick:   'rgba(148,163,184,.9)',
        };
    }

    function gradientFill(ctx, topRGBA, bottomRGBA) {
        const g = ctx.createLinearGradient(0, 0, 0, 240);
        g.addColorStop(0, topRGBA);
        g.addColorStop(1, bottomRGBA);
        return g;
    }

    function tooltipMoneyCallbacks() {
        return {
            callbacks: {
                label: (ctx) => {
                    const label = ctx.dataset?.label ? (ctx.dataset.label + ': ') : '';
                    const v = ctx.parsed?.y ?? ctx.parsed ?? 0;
                    // if dataset is "Orders" show number
                    if ((ctx.dataset?.label || '').toLowerCase().includes('order')) {
                        return label + NUM_FMT.format(Number(v || 0));
                    }
                    return label + bdt(v);
                }
            }
        };
    }

    function baseOptions({ moneyAxis = true } = {}) {
        const p = palette();
        return {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 650, easing: 'easeOutQuart' },
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { labels: { color: p.tick } },
                tooltip: moneyAxis ? tooltipMoneyCallbacks() : { enabled: true }
            },
            scales: {
                x: {
                    grid: { color: p.grid },
                    ticks: {
                        color: p.tick,
                        maxRotation: 0,
                        autoSkip: true,
                        autoSkipPadding: 18
                    }
                },
                y: {
                    grid: { color: p.grid },
                    ticks: {
                        color: p.tick,
                        callback: (v) => moneyAxis ? compactBdt(v) : NUM_FMT.format(v)
                    },
                    beginAtZero: true
                }
            }
        };
    }

    /* -------------------------
       Charts init
    ------------------------- */
    function initCharts() {
        const p = palette();

        // Sales: revenue + orders (2 axis)
        const sctx = $('salesChart').getContext('2d');
        const revFill = gradientFill(sctx, 'rgba(59,130,246,.40)', 'rgba(59,130,246,0)');
        salesChart = new Chart(sctx, {
            type: 'line',
            data: { labels: [], datasets: [
                {
                    label:'Revenue',
                    data:[],
                    tension:.45,
                    cubicInterpolationMode: 'monotone',
                    fill:true,
                    backgroundColor:revFill,
                    borderColor:p.blue,
                    borderWidth:2,
                    pointRadius:0,
                    pointHoverRadius:4,
                },
                {
                    label:'Orders',
                    data:[],
                    tension:.45,
                    cubicInterpolationMode: 'monotone',
                    fill:false,
                    borderColor:p.violet,
                    borderWidth:2,
                    pointRadius:0,
                    pointHoverRadius:4,
                    yAxisID:'y1'
                }
            ]},
            options: {
                ...baseOptions({ moneyAxis: true }),
                plugins: {
                    ...baseOptions({ moneyAxis: true }).plugins,
                    tooltip: {
                        ...tooltipMoneyCallbacks(),
                        callbacks: {
                            label: (ctx) => {
                                const label = ctx.dataset?.label ? (ctx.dataset.label + ': ') : '';
                                const v = ctx.parsed?.y ?? 0;
                                if ((ctx.dataset?.label || '').toLowerCase().includes('order')) {
                                    return label + NUM_FMT.format(Number(v || 0));
                                }
                                return label + bdt(v);
                            }
                        }
                    }
                },
                scales: {
                    ...baseOptions({ moneyAxis: true }).scales,
                    y1: {
                        position:'right',
                        grid:{ drawOnChartArea:false },
                        ticks:{
                            color:p.tick,
                            callback: (v) => NUM_FMT.format(v)
                        },
                        beginAtZero:true
                    }
                }
            }
        });

        // Payment: doughnut (colorful)
        const pctx = $('paymentMethodsChart').getContext('2d');
        paymentMethodsChart = new Chart(pctx, {
            type: 'doughnut',
            data: { labels: [], datasets: [{ data: [], borderWidth: 0 }] },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const label = ctx.label ? (ctx.label + ': ') : '';
                                const v = ctx.parsed ?? 0;
                                return label + bdt(v);
                            }
                        }
                    }
                },
                cutout: '62%',
                animation: { duration: 650, easing: 'easeOutQuart' }
            }
        });

        // Order status: bar (counts)
        const octx = $('orderStatusChart').getContext('2d');
        orderStatusChart = new Chart(octx, {
            type: 'bar',
            data: { labels: [], datasets: [{
                label:'Orders',
                data:[],
                borderRadius:10,
                backgroundColor:[
                    'rgba(59,130,246,.85)',
                    'rgba(34,197,94,.85)',
                    'rgba(245,158,11,.85)',
                    'rgba(139,92,246,.85)',
                    'rgba(244,63,94,.85)',
                    'rgba(14,165,233,.85)'
                ]
            }] },
            options: {
                ...baseOptions({ moneyAxis: false }),
                plugins: { legend: { display: false } },
                scales: {
                    ...baseOptions({ moneyAxis: false }).scales,
                    y: {
                        ...baseOptions({ moneyAxis: false }).scales.y,
                        ticks: { color: palette().tick, callback: (v) => NUM_FMT.format(v) }
                    }
                }
            }
        });

        // Daily expenses: bar (money)
        const ectx = $('expensesChart').getContext('2d');
        expensesChart = new Chart(ectx, {
            type: 'bar',
            data: { labels: [], datasets: [{
                label:'Expenses',
                data:[],
                borderRadius:10,
                backgroundColor: gradientFill(ectx, 'rgba(245,158,11,.70)', 'rgba(245,158,11,.20)')
            }] },
            options: baseOptions({ moneyAxis: true })
        });

        // Daily profit: line (money)
        const prctx = $('profitChart').getContext('2d');
        const profitFill = gradientFill(prctx, 'rgba(34,197,94,.32)', 'rgba(34,197,94,0)');
        profitChart = new Chart(prctx, {
            type: 'line',
            data: { labels: [], datasets: [{
                label:'Net Profit',
                data:[],
                tension:.45,
                cubicInterpolationMode: 'monotone',
                fill:true,
                backgroundColor:profitFill,
                borderColor:p.green,
                borderWidth:2,
                pointRadius:0,
                pointHoverRadius:4
            }] },
            options: baseOptions({ moneyAxis: true })
        });

        // Components: grouped bar (money)
        const cctx = $('componentsChart').getContext('2d');
        componentsChart = new Chart(cctx, {
            type: 'bar',
            data: { labels: [], datasets: [
                { label:'Net Sales', data:[], borderRadius:10, backgroundColor:'rgba(14,165,233,.85)' },
                { label:'COGS',      data:[], borderRadius:10, backgroundColor:'rgba(244,63,94,.80)' },
                { label:'Expenses',  data:[], borderRadius:10, backgroundColor:'rgba(245,158,11,.80)' }
            ]},
            options: baseOptions({ moneyAxis: true })
        });
    }

    /* -------------------------
       Metrics render (matches controller keys)
    ------------------------- */
    function renderMetrics(m) {
        const pm = Array.isArray(m.payment_methods_breakdown) ? m.payment_methods_breakdown : [];

        const ops = [
            ['m_total_sales', bdt(m.total_sales)],
            ['m_total_orders', m.total_orders ?? 0],
            ['m_avg_order', bdt(m.avg_order_value)],

            ['m_net_profit', bdt(m.net_profit)],
            ['m_profit_margin', (Number(m.profit_margin || 0)).toFixed(1) + '%'],
            ['m_net_sales', bdt(m.net_sales)],
            ['m_cogs', bdt(m.cost_of_goods_sold)],

            ['m_total_payments', bdt(m.total_payments)],
            ['m_orders_paid', m.orders_paid ?? 0],
            ['m_methods_count', pm.length],

            ['m_new_customers', m.new_customers ?? 0],
            ['m_reward_points', NUM_FMT.format(Number(m.total_reward_points || 0))],
            ['m_avg_due', bdt(m.avg_due_balance)],

            ['m_total_refunds', bdt(m.total_refunds)],
            ['m_total_returns', m.total_returns ?? 0],
            ['m_total_exchanges', m.total_exchanges ?? 0],

            ['m_stock_value', bdt(m.stock_cost_value)],
            ['m_available_stock', NUM_FMT.format(Number(m.available_stock || 0))],
            ['m_reserved_stock', NUM_FMT.format(Number(m.total_reserved || 0))],

            ['lowStockCount', m.low_stock_items ?? 0],
        ];

        for (const [id, val] of ops) {
            const el = $(id);
            if (el) el.textContent = val;
        }

        // ✅ Expenses card + status bar (controller: expenses_total/count/by_category/by_method)
        const expTotal = bdt(m.expenses_total);
        const expEl = $('m_expenses_total'); if (expEl) expEl.textContent = expTotal;
        const expCountEl = $('m_expenses_count'); if (expCountEl) expCountEl.textContent = m.expenses_count ?? 0;
        const topCatEl = $('m_expenses_top_category'); if (topCatEl) topCatEl.textContent = pickTopLabel(m.expenses_by_category, 'category');
        const topMethodEl = $('m_expenses_top_method'); if (topMethodEl) topMethodEl.textContent = pickTopLabel(m.expenses_by_method, 'method');

        const expStatus = $('expensesTotalStatus'); if (expStatus) expStatus.textContent = expTotal;
    }

    /* -------------------------
       Chart period slicing (client-side)
       Applies to ALL time-series charts (daily_sales, daily_expenses, daily_profit, components)
    ------------------------- */
    function sliceByPeriod(rows, dateKey, periodValue) {
        if (!Array.isArray(rows)) return [];
        const v = String(periodValue || 'all');
        if (v === 'all') return rows;

        const days = Number(v);
        if (!Number.isFinite(days) || days <= 0) return rows;

        // assumes ISO date string "YYYY-MM-DD" and rows are sorted by date from controller
        const last = rows.length ? rows[rows.length - 1] : null;
        const lastDateStr = last?.[dateKey];
        if (!lastDateStr) return rows;

        const lastDate = new Date(lastDateStr + 'T00:00:00');
        const from = new Date(lastDate);
        from.setDate(from.getDate() - (days - 1));

        return rows.filter(r => {
            const d = new Date(String(r[dateKey]) + 'T00:00:00');
            return d >= from && d <= lastDate;
        });
    }

    function applyChartPeriodAndRender() {
        if (!lastChartsPayload) return;
        const period = $('chartPeriod')?.value || 'all';

        const c = lastChartsPayload;

        const dailySales = sliceByPeriod(c.daily_sales, 'date', period);
        const dailyExpenses = sliceByPeriod(c.daily_expenses, 'date', period);
        const dailyProfit = sliceByPeriod(c.daily_profit, 'date', period);

        renderCharts({
            ...c,
            daily_sales: dailySales,
            daily_expenses: dailyExpenses,
            daily_profit: dailyProfit
        });
    }

    /* -------------------------
       Charts render (matches controller charts keys)
    ------------------------- */
    function renderCharts(c) {
        // 1) daily_sales
        const ds = Array.isArray(c.daily_sales) ? c.daily_sales : [];
        if (salesChart) {
            salesChart.data.labels = ds.map(r => r.date);
            salesChart.data.datasets[0].data = ds.map(r => Number(r.revenue || 0));
            salesChart.data.datasets[1].data = ds.map(r => Number(r.orders || 0));
            salesChart.update('none');
        }

        // 2) payment_methods_breakdown
        const pm = Array.isArray(c.payment_methods_breakdown) ? c.payment_methods_breakdown : [];
        if (paymentMethodsChart) {
            paymentMethodsChart.data.labels = pm.map(x => x.method);
            paymentMethodsChart.data.datasets[0].data = pm.map(x => Number(x.total || 0));

            const colors = [
                'rgba(59,130,246,.90)','rgba(34,197,94,.90)','rgba(245,158,11,.90)',
                'rgba(139,92,246,.90)','rgba(244,63,94,.90)','rgba(14,165,233,.90)','rgba(100,116,139,.90)'
            ];
            paymentMethodsChart.data.datasets[0].backgroundColor = pm.map((_, i) => colors[i % colors.length]);
            paymentMethodsChart.update('none');
        }

        // 3) order_status_distribution
        const st = Array.isArray(c.order_status_distribution) ? c.order_status_distribution : [];
        if (orderStatusChart) {
            orderStatusChart.data.labels = st.map(x => x.status);
            orderStatusChart.data.datasets[0].data = st.map(x => Number(x.count || 0));
            orderStatusChart.update('none');
        }

        // 4) daily_expenses
        const de = Array.isArray(c.daily_expenses) ? c.daily_expenses : [];
        if (expensesChart) {
            expensesChart.data.labels = de.map(r => r.date);
            expensesChart.data.datasets[0].data = de.map(r => Number(r.amount || 0));
            expensesChart.update('none');
        }

        // 5) daily_profit (net_profit)
        const dp = Array.isArray(c.daily_profit) ? c.daily_profit : [];
        if (profitChart) {
            profitChart.data.labels = dp.map(r => r.date);
            profitChart.data.datasets[0].data = dp.map(r => Number(r.net_profit || 0));
            profitChart.update('none');
        }

        // 6) components from daily_profit (time-series)
        if (componentsChart) {
            componentsChart.data.labels = dp.map(r => r.date);

            const netSalesArr = dp.map(r => (Number(r.revenue || 0) - Number(r.refunds || 0) + Number(r.exchange_revenue || 0)));
            const netCogsArr  = dp.map(r => (Number(r.cogs || 0) - Number(r.returned_cogs || 0) + Number(r.exchange_cogs_delta || 0)));
            const expArr      = dp.map(r => Number(r.expenses || 0));

            componentsChart.data.datasets[0].data = netSalesArr;
            componentsChart.data.datasets[1].data = netCogsArr;
            componentsChart.data.datasets[2].data = expArr;

            componentsChart.update('none');
        }
    }

    /* -------------------------
       Tables render (matches controller tables keys)
    ------------------------- */
    function renderTables(t) {
        const orders = Array.isArray(t.recent_orders) ? t.recent_orders : [];
        $('recentOrdersBody').innerHTML = orders.map(o => `
            <tr>
                <td>${o.order_no ?? ''}</td>
                <td>${o.customer?.name ?? 'Guest'}</td>
                <td>${o.created_at ? new Date(o.created_at).toLocaleString() : ''}</td>
                <td class="numeric">${bdt(o.payable_total)}</td>
                <td><span class="badge">${o.status ?? ''}</span></td>
                <td><span class="badge">${o.payment_status ?? ''}</span></td>
            </tr>
        `).join('');

        const exps = Array.isArray(t.recent_expenses) ? t.recent_expenses : [];
        const expBody = $('recentExpensesBody');
        if (expBody) {
            expBody.innerHTML = exps.map(e => `
                <tr>
                    <td>${e.expense_no ?? ''}</td>
                    <td>${e.expense_date ? new Date(e.expense_date).toLocaleDateString() : ''}</td>
                    <td>${e.title ?? ''}</td>
                    <td>${e.category ?? 'Uncategorized'}</td>
                    <td>${e.payment_method ?? 'Unknown'}</td>
                    <td class="numeric">${bdt(e.amount)}</td>
                </tr>
            `).join('');
        }
    }

    /* -------------------------
       Load all (metrics + charts + tables)
    ------------------------- */
    async function loadAllSections({ silent = false } = {}) {
        const params = getFilters();
        const qs = params.toString();

        // cancel previous request batch
        if (loadAbort) loadAbort.abort();
        loadAbort = new AbortController();
        const signal = loadAbort.signal;

        if (!silent) showLoading();

        let done = 0;
        const total = 3;
        const step = (hint) => {
            done++;
            const pct = (done / total) * 100;
            setLoadingProgress(Math.max(loadingProgress, pct), hint);
        };

        try {
            const baseHeaders = { 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' };

            const metricsP = fetch(URL_METRICS + '?' + qs, { headers: baseHeaders, credentials:'same-origin', cache:'no-store', signal })
                .then(r => r.json())
                .then(res => { if (res?.metrics) renderMetrics(res.metrics); step('Metrics loaded'); return res; });

            const chartsP = fetch(URL_CHARTS + '?' + qs, { headers: baseHeaders, credentials:'same-origin', cache:'no-store', signal })
                .then(r => r.json())
                .then(res => {
                    if (res?.charts) {
                        lastChartsPayload = res.charts;     // keep original full range
                        applyChartPeriodAndRender();        // render time-series based on chartPeriod
                    }
                    step('Charts loaded');
                    return res;
                });

            const tablesP = fetch(URL_TABLES + '?' + qs, { headers: baseHeaders, credentials:'same-origin', cache:'no-store', signal })
                .then(r => r.json())
                .then(res => { if (res?.tables) renderTables(res.tables); step('Tables loaded'); return res; });

            const [m, c, t] = await Promise.allSettled([metricsP, chartsP, tablesP]);

            // update URL
            window.history.replaceState({}, '', window.location.pathname + '?' + qs);

            // timestamp
            const ts =
                (m.status === 'fulfilled' && m.value?.timestamp) ||
                (c.status === 'fulfilled' && c.value?.timestamp) ||
                (t.status === 'fulfilled' && t.value?.timestamp);

            if (ts) setLastUpdate(ts);

        } catch (e) {
            if (e?.name === 'AbortError') return;
            console.error(e);
            alert('Dashboard load failed.');
        } finally {
            if (!silent) hideLoading();
        }
    }

    /* -------------------------
       Realtime
    ------------------------- */
    function stopRealtime() {
        if (realtimeTimer) { clearInterval(realtimeTimer); realtimeTimer = null; }
        if (stream) { stream.close(); stream = null; }
    }

    function startPollingRealtime() {
        const run = async () => {
            const params = getFilters();
            params.set('since', lastServerTimestamp);

            try {
                const res = await fetch(URL_REALTIME + '?' + params.toString(), {
                    headers: { 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' },
                    credentials:'same-origin',
                    cache:'no-store'
                });
                if (!res.ok) throw new Error('Realtime failed: ' + res.status);

                const data = await res.json();

                $('newOrdersCount').textContent = data.new_orders ?? 0;
                $('pendingOrdersCount').textContent = data.pending_orders ?? 0;
                $('abandonedCartsCount').textContent = data.abandoned_carts ?? 0;
                $('lowStockCount').textContent = data.low_stock_items ?? 0;

                if ((data.new_orders ?? 0) > 0) flash($('newOrdersCount'));
                setLastUpdate(data.timestamp);

                // OPTIONAL: if new orders, refresh metrics/charts silently
                if ((data.new_orders ?? 0) > 0) loadAllSections({ silent: true });

            } catch (e) {
                console.error(e);
            }
        };

        run();
        realtimeTimer = setInterval(run, 5000);
    }

    function startRealtime() {
        stopRealtime();

        const params = getFilters();

        if (!!window.EventSource) {
            const sseUrl = URL_STREAM + '?' + params.toString();
            stream = new EventSource(sseUrl);

            stream.addEventListener('counters', (ev) => {
                const data = JSON.parse(ev.data);

                $('newOrdersCount').textContent = data.new_orders ?? 0;
                $('pendingOrdersCount').textContent = data.pending_orders ?? 0;
                $('abandonedCartsCount').textContent = data.abandoned_carts ?? 0;
                $('lowStockCount').textContent = data.low_stock_items ?? 0;

                if ((data.new_orders ?? 0) > 0) flash($('newOrdersCount'));
                setLastUpdate(data.timestamp);

                // OPTIONAL: refresh metrics/charts silently when activity happens
                if ((data.new_orders ?? 0) > 0) loadAllSections({ silent: true });
            });

            stream.onerror = () => {
                console.warn('SSE error, fallback polling...');
                stopRealtime();
                startPollingRealtime();
            };

            return;
        }

        startPollingRealtime();
    }

    /* -------------------------
       Export
    ------------------------- */
    async function exportReport() {
        showLoading();
        try {
            const body = {
                date_range: $('dateRange').value,
                start_date: $('startDate').value,
                end_date: $('endDate').value,
                status: $('statusFilter').value,
                payment_status: $('paymentStatus').value,
                location_id: $('locationFilter').value || null,
            };

            const res = await fetch(URL_EXPORT, {
                method: 'POST',
                headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept':'application/json' },
                credentials:'same-origin',
                body: JSON.stringify(body)
            });

            const data = await res.json();
            alert(data.message || 'Export queued.');
        } catch (e) {
            console.error(e);
            alert('Export failed');
        } finally {
            hideLoading();
        }
    }

    /* -------------------------
       UI wiring
    ------------------------- */
    function wireUI() {
        $('dateRange').addEventListener('change', () => {
            $('customDateRange').style.display = $('dateRange').value === 'custom' ? 'flex' : 'none';
        });

        $('chartPeriod').addEventListener('change', () => {
            // ✅ update ALL time-series charts according to selected period
            applyChartPeriodAndRender();
        });

        const applyNow = debounce(async () => {
            await loadAllSections();
            startRealtime();
        }, 150);

        $('applyFilters').addEventListener('click', applyNow);

        $('resetFilters').addEventListener('click', async () => {
            $('dateRange').value = 'this_month';
            $('customDateRange').style.display = 'none';
            $('startDate').value = '';
            $('endDate').value = '';
            $('statusFilter').value = 'all';
            $('paymentStatus').value = 'all';
            $('locationFilter').value = '';
            $('chartPeriod').value = 'all';
            await loadAllSections();
            startRealtime();
        });

        $('refreshBtn').addEventListener('click', () => loadAllSections());
        $('exportBtn').addEventListener('click', exportReport);
    }

    document.addEventListener('DOMContentLoaded', async () => {
        initCharts();
        wireUI();
        await loadAllSections({ silent: true });
        startRealtime();
    });
})();
</script>
@endsection
