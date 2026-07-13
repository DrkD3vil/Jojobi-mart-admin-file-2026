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
    <meta name="csrf-token" content="{{ csrf_token() }}">


    {{-- resources/views/reports/dashboard_today.blade.php --}}


    {{-- resources/views/reports/dashboard_today.blade.php --}}

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        // ✅ Safe accessors for controller payload
        $recentPaginated = data_get($dashboard, 'recent_orders_paginated', []);
        $recentRows = data_get($recentPaginated, 'data', []);
        $recentMeta = data_get($recentPaginated, 'meta', []);
        $topProducts = data_get($dashboard, 'top_products', []);
        $periodFrom = data_get($dashboard, 'period.from');
        $periodTo = data_get($dashboard, 'period.to');
    @endphp

    <div class="financial-dashboard">

        <!-- Header -->
        <div class="dashboard-header">
            <div class="header-left">
                <h1 class="page-title">Financial Dashboard (Today)</h1>
                <p class="page-subtitle">
                    Instant today analytics (cached) + realtime counters
                    @if ($periodFrom && $periodTo)
                        <span style="opacity:.7;">| Period: {{ $periodFrom }} → {{ $periodTo }}</span>
                    @endif
                </p>
            </div>

            {{-- ✅ Only location filter, auto-load on change --}}
            <div class="header-actions" style="display:flex; gap:12px; align-items:end; flex-wrap:wrap;">
                <div class="filter-group" style="min-width:240px;">
                    <label style="display:block;">Location</label>
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

                <div style="opacity:.75; font-size:.9em; padding-bottom:6px;">
                    Updated: <span
                        id="advancedUpdatedAt">{{ \Carbon\Carbon::parse($timestamp)->format('Y-m-d H:i:s') }}</span>
                </div>
            </div>
        </div>

        <!-- Real-time Status Bar -->
        <div class="realtime-status">
            <div class="status-item">
                <span class="status-label">Last Update:</span>
                <span class="status-value"
                    id="lastUpdateTime">{{ \Carbon\Carbon::parse($timestamp)->format('H:i:s') }}</span>
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
                <span class="status-value badge danger"
                    id="lowStockCount">{{ (int) ($low_stock_items ?? ($dashboard['low_stock_items'] ?? 0)) }}</span>
            </div>
            <div class="status-item">
                <span class="status-label">Expenses:</span>
                <span class="status-value badge warning" id="expensesTotalStatus">
                    {{ number_format((float) ($dashboard['expenses_total'] ?? 0), 2) }}
                </span>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="metrics-grid">
            <div class="metric-card sales">
                <div class="metric-header">
                    <h3 class="metric-title">Total Sales</h3>
                </div>
                <div class="metric-value" id="m_total_sales">
                    &#2547;{{ number_format((float) ($dashboard['total_sales'] ?? 0), 2) }}</div>
                <div class="metric-details">
                    <div class="detail-item"><span class="detail-label">Orders:</span><span class="detail-value"
                            id="m_total_orders">{{ (int) ($dashboard['total_orders'] ?? 0) }}</span></div>
                    <div class="detail-item"><span class="detail-label">Avg Order:</span><span class="detail-value"
                            id="m_avg_order">&#2547;{{ number_format((float) ($dashboard['avg_order_value'] ?? 0), 2) }}</span>
                    </div>
                    <div class="detail-item"><span class="detail-label">Discounts:</span><span class="detail-value"
                            id="m_total_discounts">&#2547;{{ number_format((float) ($dashboard['total_discounts'] ?? 0), 2) }}</span>
                    </div>
                    <div class="detail-item"><span class="detail-label">Sold Qty:</span><span class="detail-value"
                            id="m_sold_qty">{{ number_format((float) ($dashboard['sold_qty'] ?? 0), 2) }}</span></div>
                </div>
            </div>

            <div class="metric-card profit">
                <div class="metric-header">
                    <h3 class="metric-title">Net Profit</h3>
                    <div class="metric-trend" id="m_profit_badge">
                        <span class="trend-value"
                            id="m_profit_margin">{{ number_format((float) ($dashboard['profit_margin'] ?? 0), 2) }}%</span>
                    </div>
                </div>
                <div class="metric-value" id="m_net_profit">
                    &#2547;{{ number_format((float) ($dashboard['net_profit'] ?? 0), 2) }}</div>
                <div class="metric-details">
                    <div class="detail-item"><span class="detail-label">Net Sales:</span><span class="detail-value"
                            id="m_net_sales">&#2547;{{ number_format((float) ($dashboard['net_sales'] ?? 0), 2) }}</span>
                    </div>
                    <div class="detail-item"><span class="detail-label">COGS:</span><span class="detail-value"
                            id="m_cogs">&#2547;{{ number_format((float) ($dashboard['cost_of_goods_sold'] ?? 0), 2) }}</span>
                    </div>
                    <div class="detail-item"><span class="detail-label">Gross Profit:</span><span class="detail-value"
                            id="m_gross_profit">&#2547;{{ number_format((float) ($dashboard['gross_profit'] ?? 0), 2) }}</span>
                    </div>
                    <div class="detail-item"><span class="detail-label">Gross Margin:</span><span class="detail-value"
                            id="m_gross_margin">{{ number_format((float) ($dashboard['gross_margin'] ?? 0), 2) }}%</span>
                    </div>
                    <div class="detail-item"><span class="detail-label">Expenses:</span><span class="detail-value"
                            id="m_expenses_total">Tk{{ number_format((float) ($dashboard['expenses_total'] ?? 0), 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="metric-card payments">
                <div class="metric-header">
                    <h3 class="metric-title">Payments</h3>
                </div>
                <div class="metric-value" id="m_total_payments">
                    &#2547;{{ number_format((float) ($dashboard['total_payments'] ?? 0), 2) }}</div>
                <div class="metric-details">
                    <div class="detail-item"><span class="detail-label">Paid Orders:</span><span class="detail-value"
                            id="m_orders_paid">{{ (int) ($dashboard['orders_paid'] ?? 0) }}</span></div>
                    <div class="detail-item"><span class="detail-label">Paid Amount:</span><span class="detail-value"
                            id="m_paid_amount">&#2547;{{ number_format((float) ($dashboard['paid_amount'] ?? ($dashboard['total_payments'] ?? 0)), 2) }}</span>
                    </div>
                    <div class="detail-item"><span class="detail-label">Due Amount:</span><span class="detail-value"
                            id="m_due_amount">&#2547;{{ number_format((float) ($dashboard['due_amount'] ?? 0), 2) }}</span>
                    </div>
                    <div class="detail-item"><span class="detail-label">Methods:</span><span class="detail-value"
                            id="m_methods_count">{{ is_array($dashboard['payment_methods_breakdown'] ?? null) ? count($dashboard['payment_methods_breakdown']) : 0 }}</span>
                    </div>
                </div>
            </div>

            <div class="metric-card customers">
                <div class="metric-header">
                    <h3 class="metric-title">Customers</h3>
                </div>
                <div class="metric-value" id="m_new_customers">{{ (int) ($dashboard['new_customers'] ?? 0) }}</div>
                <div class="metric-details">
                    <div class="detail-item"><span class="detail-label">Reward Points:</span><span class="detail-value"
                            id="m_reward_points">{{ number_format((float) ($dashboard['total_reward_points'] ?? 0)) }}</span>
                    </div>
                    <div class="detail-item"><span class="detail-label">Avg Due:</span><span class="detail-value"
                            id="m_avg_due">&#2547;{{ number_format((float) ($dashboard['avg_due_balance'] ?? 0), 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="metric-card returns">
                <div class="metric-header">
                    <h3 class="metric-title">Returns & Refunds</h3>
                </div>
                <div class="metric-value" id="m_total_refunds">
                    &#2547;{{ number_format((float) ($dashboard['total_refunds'] ?? 0), 2) }}</div>
                <div class="metric-details">
                    <div class="detail-item"><span class="detail-label">Returns:</span><span class="detail-value"
                            id="m_total_returns">{{ (int) ($dashboard['total_returns'] ?? 0) }}</span></div>
                    <div class="detail-item"><span class="detail-label">Exchanges:</span><span class="detail-value"
                            id="m_total_exchanges">{{ (int) ($dashboard['total_exchanges'] ?? 0) }}</span></div>
                </div>
            </div>

            <div class="metric-card stock">
                <div class="metric-header">
                    <h3 class="metric-title">Stock</h3>
                </div>
                <div class="metric-value" id="m_stock_value">
                    &#2547;{{ number_format((float) ($dashboard['stock_cost_value'] ?? 0), 2) }}</div>
                <div class="metric-details">
                    <div class="detail-item"><span class="detail-label">Available:</span><span class="detail-value"
                            id="m_available_stock">{{ number_format((float) ($dashboard['available_stock'] ?? 0)) }}</span>
                    </div>
                    <div class="detail-item"><span class="detail-label">Reserved:</span><span class="detail-value"
                            id="m_reserved_stock">{{ number_format((float) ($dashboard['total_reserved'] ?? 0)) }}</span>
                    </div>
                    <div class="detail-item"><span class="detail-label">On Hand:</span><span class="detail-value"
                            id="m_on_hand_stock">{{ number_format((float) ($dashboard['on_hand_stock'] ?? 0)) }}</span>
                    </div>
                </div>
            </div>

            {{-- ✅ Expenses card --}}
            <div class="metric-card expense">
                <div class="metric-header">
                    <h3 class="metric-title">Expenses</h3>
                </div>
                <div class="metric-value" id="m_expenses_total_card">
                    Tk{{ number_format((float) ($dashboard['expenses_total'] ?? 0), 2) }}</div>
                <div class="metric-details">
                    <div class="detail-item"><span class="detail-label">Count:</span><span class="detail-value"
                            id="m_expenses_count">{{ (int) ($dashboard['expenses_count'] ?? 0) }}</span></div>
                    <div class="detail-item"><span class="detail-label">Top Category:</span><span class="detail-value"
                            id="m_expenses_top_category">{{ data_get($dashboard['expenses_by_category'] ?? [], '0.category', '—') }}</span>
                    </div>
                    <div class="detail-item"><span class="detail-label">Top Method:</span><span class="detail-value"
                            id="m_expenses_top_method">{{ data_get($dashboard['expenses_by_method'] ?? [], '0.method', '—') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- More Information (quick insights table) -->
        <div class="tables-section">
            <div class="table-container">
                <div class="table-header"
                    style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
                    <h3 class="table-title">Today Insights</h3>
                    <div style="opacity:.75; font-size:.9em;">
                        Cache: <span id="cacheHint">10s payload / 2s realtime</span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Metric</th>
                                <th class="numeric">Value</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Paid Amount (Cash Received)</td>
                                <td class="numeric" id="a_paid_amount">
                                    &#2547;{{ number_format((float) ($dashboard['paid_amount'] ?? ($dashboard['total_payments'] ?? 0)), 2) }}
                                </td>
                                <td>Sum of completed payments today</td>
                            </tr>
                            <tr>
                                <td>Due Amount</td>
                                <td class="numeric" id="a_due_amount">
                                    &#2547;{{ number_format((float) ($dashboard['due_amount'] ?? 0), 2) }}</td>
                                <td>max(0, net_sales - paid_amount)</td>
                            </tr>
                            <tr>
                                <td>Gross Profit</td>
                                <td class="numeric" id="a_gross_profit">
                                    &#2547;{{ number_format((float) ($dashboard['gross_profit'] ?? 0), 2) }}</td>
                                <td>net_sales - cogs</td>
                            </tr>
                            <tr>
                                <td>Net Profit (After Expenses)</td>
                                <td class="numeric" id="a_net_profit">
                                    &#2547;{{ number_format((float) ($dashboard['net_profit'] ?? 0), 2) }}</td>
                                <td>gross_profit - expenses</td>
                            </tr>
                            <tr>
                                <td>Low Stock Items</td>
                                <td class="numeric" id="a_low_stock_items">
                                    {{ (int) ($dashboard['low_stock_items'] ?? 0) }}</td>
                                <td>Available &lt; 10</td>
                            </tr>
                            <tr>
                                <td>Stock Cost Value</td>
                                <td class="numeric" id="a_stock_cost_value">
                                    &#2547;{{ number_format((float) ($dashboard['stock_cost_value'] ?? 0), 2) }}</td>
                                <td>(available * buy_price)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="charts-section">
            <div class="chart-container large">
                <div class="chart-header">
                    <h3 class="chart-title">Today Sales Trend (Hourly)</h3>
                </div>
                <div class="chart-content"><canvas id="salesChart"></canvas></div>
            </div>

            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="chart-title">Payment Methods</h3>
                </div>
                <div class="chart-content"><canvas id="paymentMethodsChart"></canvas></div>
            </div>

            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="chart-title">Order Status Distribution</h3>
                </div>
                <div class="chart-content"><canvas id="orderStatusChart"></canvas></div>
            </div>

            <div class="chart-container large">
                <div class="chart-header">
                    <h3 class="chart-title">Profit Components (Today)</h3>
                </div>
                <div class="chart-content"><canvas id="componentsChart"></canvas></div>
            </div>

            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="chart-title">Expenses by Category</h3>
                </div>
                <div class="chart-content"><canvas id="expenseCategoryChart"></canvas></div>
            </div>

            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="chart-title">Expenses by Method</h3>
                </div>
                <div class="chart-content"><canvas id="expenseMethodChart"></canvas></div>
            </div>
        </div>

<!-- Recent Orders -->
<div class="tables-section">
    <div class="table-container" style="max-width: 100%; overflow-x: auto;">
        <div class="table-header" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0;">
            <h3 class="table-title" style="font-size: 1.5rem; font-weight: 600;">Recent Orders (Today)</h3>
            <a href="{{ route('orders.index') }}" class="table-link" style="font-size: 1rem; text-decoration: none; color: #007bff;">View All</a>
        </div>

        <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table class="data-table" style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                <thead>
                    <tr style="background-color: #f8f9fa;">
                        <th style="padding: 10px; text-align: left; font-weight: bold;">Order #</th>
                        <th style="padding: 10px; text-align: left; font-weight: bold;">Customer</th>
                        <th style="padding: 10px; text-align: left; font-weight: bold;">Date</th>
                        <th style="padding: 10px; text-align: right; font-weight: bold;">Amount</th>
                        <th style="padding: 10px; text-align: left; font-weight: bold;">Status</th>
                        <th style="padding: 10px; text-align: left; font-weight: bold;">Payment</th>
                    </tr>
                </thead>

                <tbody id="recentOrdersBody">
                    @foreach ($recentRows as $o)
                        <tr style="border-bottom: 1px solid #e9ecef;">
                            <td style="padding: 10px;">{{ $o['order_no'] ?? 'NA' }}</td>
                            <td style="padding: 10px;">
                                {{ data_get($o, 'customer.name', 'Guest') }}
                            </td>
                            <td style="padding: 10px;">
                                {{ \Carbon\Carbon::parse($o['created_at'])->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="numeric" style="padding: 10px; text-align: right;">
                                {{ number_format((float) ($o['payable_total'] ?? 0), 2) }}
                            </td>
                            <td style="padding: 10px;">
                                <span class="badge" style="background-color: #e0e0e0; padding: 5px 10px;">
                                    {{ $o['status'] ?? '' }}
                                </span>
                            </td>
                            <td style="padding: 10px;">
                                <span class="badge" style="background-color: #e0e0e0; padding: 5px 10px;">
                                    {{ $o['payment_status'] ?? '' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination (AJAX) -->
        <div id="paginationContainer" style="display: none;">
            <div class="table-pagination" style="display: flex; gap: 10px; align-items: center; justify-content: space-between; padding: 12px 0;">
                <div id="recentMetaText" style="opacity: 0.75; font-size: 1rem;">
                    Showing {{ $recentMeta['from'] ?? 0 }} - {{ $recentMeta['to'] ?? 0 }} of {{ $recentMeta['total'] ?? 0 }}
                </div>

                <div style="display: flex; gap: 8px; align-items: center;">
                    <button class="btn btn-sm btn-secondary" id="recentPrevBtn" style="padding: 5px 10px; font-size: 0.875rem;">Prev</button>
                    <span style="min-width: 120px; text-align: center;" id="recentPageText" style="font-size: 1rem;">
                        Page {{ $recentMeta['current_page'] ?? 1 }} / {{ $recentMeta['last_page'] ?? 1 }}
                    </span>
                    <button class="btn btn-sm btn-secondary" id="recentNextBtn" style="padding: 5px 10px; font-size: 0.875rem;">Next</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Ensure the correct format for customer names and order dates
    function escapeHtml(str) {
        return str.replace(/[&<>"']/g, function(match) {
            const escape = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;',
            };
            return escape[match];
        });
    }



    function renderRecentOrders(paginated) {
        const rows = Array.isArray(paginated.data) ? paginated.data : [];
        const meta = paginated.meta || {};

        const body = $('recentOrdersBody');
        if (body) {
            body.innerHTML = rows.map(o => `
                <tr>
                    <!-- Order Number - Link to order details -->
                    <td><a href="/orders/${o.id}" style="color: #007bff; text-decoration: none;">${o.order_no ?? ''}</a></td>

                    <!-- Customer Name - Link to customer details -->
                    <td><a href="/customers/${o.customer && o.customer.id ? o.customer.id : ''}" style="color: #007bff; text-decoration: none;">
                        ${(o.customer && o.customer.name) ? escapeHtml(o.customer.name) : 'Guest'}
                    </a></td>

                    <!-- Order Date - Link to order details -->
                    <td><a href="/orders/${o.id}" style="color: #007bff; text-decoration: none;">
                        ${o.created_at ? new Date(o.created_at).toLocaleString() : ''}</a>
                    </td>

                    <!-- Payable Total -->
                    <td class="numeric">${money(o.payable_total)}</td>

                    <!-- Payment Status - Link to payments -->
                    <td><a href="/orders/${o.id}/payments" style="color: #007bff; text-decoration: none;">
                        <span class="badge">${o.payment_status ?? ''}</span></a>
                    </td>

                    <!-- Order Status -->
                    <td><span class="badge">${o.status ?? ''}</span></td>
                </tr>
            `).join('');
        }

        recentPage = Number(meta.current_page || 1);
        recentLastPage = Number(meta.last_page || 1);

        const metaText = $('recentMetaText');
        if (metaText) metaText.textContent =
            `Showing ${meta.from || 0} - ${meta.to || 0} of ${meta.total || 0}`;

        const pageText = $('recentPageText');
        if (pageText) pageText.textContent = `Page ${recentPage} / ${recentLastPage}`;

        const prevBtn = $('recentPrevBtn');
        const nextBtn = $('recentNextBtn');
        if (prevBtn) prevBtn.disabled = recentPage <= 1;
        if (nextBtn) nextBtn.disabled = recentPage >= recentLastPage;

        // Show pagination only if the total count is more than 5
        const paginationContainer = $('paginationContainer');
        if (paginationContainer) {
            if (meta.total > 5) {
                paginationContainer.style.display = 'flex';  // Show pagination
            } else {
                paginationContainer.style.display = 'none';  // Hide pagination
            }
        }
    }
</script>




        <!-- Top Products -->
        <div class="tables-section">
            <div class="table-container">
                <div class="table-header">
                    <h3 class="table-title">Top Products (Today)</h3>
                    <span style="opacity:.7; font-size:.9em;">Top 10 by revenue</span>
                </div>

                <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                    <table class="data-table" style="width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 20px;">
                        <thead>
                            <tr style="background-color: #f8f9fa;">
                                <th style="padding: 10px; text-align: left; font-weight: bold; border-bottom: 1px solid #e9ecef;">Product</th>
                                <th style="padding: 10px; text-align: left; font-weight: bold; border-bottom: 1px solid #e9ecef;">Category</th>
                                <th style="padding: 10px; text-align: left; font-weight: bold; border-bottom: 1px solid #e9ecef;">Brand</th>
                                <th style="padding: 10px; text-align: right; font-weight: bold; border-bottom: 1px solid #e9ecef;" class="numeric">Qty</th>
                                <th style="padding: 10px; text-align: right; font-weight: bold; border-bottom: 1px solid #e9ecef;" class="numeric">Revenue</th>
                                <th style="padding: 10px; text-align: right; font-weight: bold; border-bottom: 1px solid #e9ecef;" class="numeric">Cost</th>
                                <th style="padding: 10px; text-align: right; font-weight: bold; border-bottom: 1px solid #e9ecef;" class="numeric">Profit</th>
                                <th style="padding: 10px; text-align: right; font-weight: bold; border-bottom: 1px solid #e9ecef;" class="numeric">Margin</th>
                            </tr>
                        </thead>
                        <tbody id="topProductsBody">
                            @forelse($topProducts as $tp)
                                @php
                                    $p = data_get($tp, 'product');
                                    $cat = data_get($p, 'category.name', '-');
                                    $brand = data_get($p, 'brand.name', '-');
                                @endphp
                                <tr style="border-bottom: 1px solid #e9ecef;">
                                    <td style="padding: 10px; text-align: left;">{{ data_get($p, 'name', '-') }}</td>
                                    <td style="padding: 10px; text-align: left;">{{ $cat }}</td>
                                    <td style="padding: 10px; text-align: left;">{{ $brand }}</td>
                                    <td class="numeric" style="padding: 10px; text-align: right;">{{ number_format((float) ($tp['total_qty'] ?? 0), 2) }}</td>
                                    <td class="numeric" style="padding: 10px; text-align: right;">&#2547;{{ number_format((float) ($tp['total_revenue'] ?? 0), 2) }}</td>
                                    <td class="numeric" style="padding: 10px; text-align: right;">&#2547;{{ number_format((float) ($tp['total_cost'] ?? 0), 2) }}</td>
                                    <td class="numeric" style="padding: 10px; text-align: right;">&#2547;{{ number_format((float) ($tp['profit'] ?? 0), 2) }}</td>
                                    <td class="numeric" style="padding: 10px; text-align: right;">{{ number_format((float) ($tp['margin'] ?? 0), 2) }}%</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="opacity:.7; text-align: center; padding: 10px;">No top products yet for today.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>

    <!-- ✅ Loading Overlay (Modern + Percentage) -->
    <div id="loadingOverlay" class="loading-overlay" style="display:none;">
        <div class="loading-content">
            <div class="loading-spinner"></div>

            <div class="loading-text">
                Loading today dashboard… <span id="loadingPct">0%</span>
            </div>

            <div class="loading-bar">
                <div class="loading-bar-fill" id="loadingBarFill" style="width:0%"></div>
            </div>

            <div class="loading-subtext" id="loadingHint">Preparing…</div>
        </div>
    </div>

    <style>
        .flash {
            animation: flashPulse 1s ease-in-out 2;
        }

        @keyframes flashPulse {
            0% {
                transform: scale(1)
            }

            50% {
                transform: scale(1.15)
            }

            100% {
                transform: scale(1)
            }
        }

        .loading-overlay {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            background: rgba(15, 23, 42, .65);
            backdrop-filter: blur(8px);
        }

        .loading-content {
            width: min(440px, 92vw);
            padding: 20px 22px;
            border-radius: 18px;
            background: rgba(2, 6, 23, .55);
            border: 1px solid rgba(148, 163, 184, .22);
            color: rgba(226, 232, 240, .92);
            box-shadow: 0 18px 50px rgba(0, 0, 0, .35);
            text-align: center;
        }

        .loading-spinner {
            width: 46px;
            height: 46px;
            border-radius: 999px;
            border: 4px solid rgba(148, 163, 184, .25);
            border-top-color: rgba(226, 232, 240, .92);
            margin: 2px auto 12px;
            animation: spin 0.9s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .loading-text {
            font-size: 14px;
            letter-spacing: .2px;
        }

        .loading-bar {
            width: 320px;
            max-width: 100%;
            height: 10px;
            background: rgba(148, 163, 184, .18);
            border-radius: 999px;
            overflow: hidden;
            margin: 12px auto 0;
        }

        .loading-bar-fill {
            height: 100%;
            width: 0%;
            background: rgba(226, 232, 240, .90);
            border-radius: 999px;
            transition: width .18s ease;
        }

        .loading-subtext {
            margin-top: 10px;
            font-size: 12px;
            opacity: .85;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        (() => {
            const URL_DATA = "{{ route('dashboard.financial.today.data') }}";
            const URL_REALTIME = "{{ route('dashboard.financial.today.realtime') }}";
            const URL_STREAM = "{{ route('dashboard.financial.today.stream') }}";
            const URL_RECENT = "{{ route('dashboard.financial.today.recent_orders') }}";

            let salesChart = null;
            let paymentMethodsChart = null;
            let orderStatusChart = null;
            let componentsChart = null;
            let expenseCategoryChart = null;
            let expenseMethodChart = null;

            let lastServerTimestamp = "{{ $timestamp }}";
            let realtimeTimer = null;
            let stream = null;

            // pagination state
            let recentPage = Number(@json($recentMeta['current_page'] ?? 1));
            let recentLastPage = Number(@json($recentMeta['last_page'] ?? 1));

            // cancel in-flight fetches
            let loadAbort = null;

            // loader state
            let loadingRampTimer = null;
            let loadingProgress = 0;

            const $ = (id) => document.getElementById(id);

            /* =======================
             * Loader (percentage + hints)
             * ======================= */
            function setLoadingProgress(pct, hintText = null) {
                loadingProgress = Math.max(0, Math.min(100, Math.round(pct)));
                const pctEl = $('loadingPct');
                const barEl = $('loadingBarFill');
                const hintEl = $('loadingHint');

                if (pctEl) pctEl.textContent = loadingProgress + '%';
                if (barEl) barEl.style.width = loadingProgress + '%';
                if (hintEl && hintText) hintEl.textContent = hintText;
            }

            function showLoading(hint = 'Starting…') {
                const ov = $('loadingOverlay');
                if (!ov) return;

                ov.style.display = 'flex';
                setLoadingProgress(0, hint);

                if (loadingRampTimer) clearInterval(loadingRampTimer);
                loadingRampTimer = setInterval(() => {
                    // ramp to 88% while waiting
                    if (loadingProgress < 88) setLoadingProgress(loadingProgress + 1);
                }, 110);
            }

            function hideLoading() {
                setLoadingProgress(100, 'Done');
                if (loadingRampTimer) {
                    clearInterval(loadingRampTimer);
                    loadingRampTimer = null;
                }

                setTimeout(() => {
                    const ov = $('loadingOverlay');
                    if (ov) ov.style.display = 'none';
                    setLoadingProgress(0, 'Preparing…');
                }, 180);
            }

            function flash(el) {
                if (!el) return;
                el.classList.add('flash');
                setTimeout(() => el.classList.remove('flash'), 1500);
            }

            function debounce(fn, ms = 250) {
                let t = null;
                return (...args) => {
                    clearTimeout(t);
                    t = setTimeout(() => fn(...args), ms);
                };
            }

            function setLastUpdate(tsIso) {
                lastServerTimestamp = tsIso || new Date().toISOString();
                const d = new Date(lastServerTimestamp);

                const el = $('lastUpdateTime');
                if (el) el.textContent = d.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });

                const adv = $('advancedUpdatedAt');
                if (adv) adv.textContent = d.toLocaleString();
            }

            function money(n) {
                const x = Number(n || 0);
                return ' &#2547;' + x.toFixed(2);
            }

            function moneyTk(n) {
                const x = Number(n || 0);
                return 'Tk' + x.toFixed(2);
            }

            function setText(id, val) {
                const el = $(id);
                if (el) el.textContent = val;
            }

            function escapeHtml(str) {
                return String(str)
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }

            function palette() {
                return {
                    blue: 'rgba(59, 130, 246, 1)',
                    sky: 'rgba(14, 165, 233, 1)',
                    violet: 'rgba(139, 92, 246, 1)',
                    green: 'rgba(34, 197, 94, 1)',
                    amber: 'rgba(245, 158, 11, 1)',
                    rose: 'rgba(244, 63, 94, 1)',
                    slate: 'rgba(100, 116, 139, 1)',
                    grid: 'rgba(148, 163, 184, .25)',
                    tick: 'rgba(148, 163, 184, .9)',
                };
            }

            function gradientFill(ctx, topRGBA, bottomRGBA) {
                const g = ctx.createLinearGradient(0, 0, 0, 240);
                g.addColorStop(0, topRGBA);
                g.addColorStop(1, bottomRGBA);
                return g;
            }

            function baseOptions() {
                const p = palette();
                return {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 450
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            labels: {
                                color: p.tick
                            }
                        },
                        tooltip: {
                            enabled: true
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: p.grid
                            },
                            ticks: {
                                color: p.tick
                            }
                        },
                        y: {
                            grid: {
                                color: p.grid
                            },
                            ticks: {
                                color: p.tick
                            },
                            beginAtZero: true
                        },
                    }
                };
            }

            function getLocationQS(extra = {}) {
                const loc = $('locationFilter')?.value || '';
                const params = new URLSearchParams();
                if (loc) params.set('location_id', loc);
                Object.entries(extra).forEach(([k, v]) => params.set(k, String(v)));
                return params.toString();
            }

            /* =======================
             * Charts
             * ======================= */
            function initCharts(initialDash) {
                const p = palette();

                const ds = Array.isArray(initialDash.daily_sales) ? initialDash.daily_sales : [];
                const pm = Array.isArray(initialDash.payment_methods_breakdown) ? initialDash
                    .payment_methods_breakdown : [];
                const st = Array.isArray(initialDash.order_status_distribution) ? initialDash
                    .order_status_distribution : [];
                const exCat = Array.isArray(initialDash.expenses_by_category) ? initialDash.expenses_by_category : [];
                const exMet = Array.isArray(initialDash.expenses_by_method) ? initialDash.expenses_by_method : [];

                // 1) hourly sales (revenue + orders)
                const sctx = $('salesChart').getContext('2d');
                const revFill = gradientFill(sctx, 'rgba(59,130,246,.35)', 'rgba(59,130,246,0)');
                salesChart = new Chart(sctx, {
                    type: 'line',
                    data: {
                        labels: ds.map(r => r.date),
                        datasets: [{
                                label: 'Revenue',
                                data: ds.map(r => Number(r.revenue || 0)),
                                tension: 0.35,
                                fill: true,
                                backgroundColor: revFill,
                                borderColor: p.blue,
                                borderWidth: 2,
                                pointRadius: 2
                            },
                            {
                                label: 'Orders',
                                data: ds.map(r => Number(r.orders || 0)),
                                tension: 0.35,
                                fill: false,
                                borderColor: p.violet,
                                borderWidth: 2,
                                pointRadius: 2,
                                yAxisID: 'y1'
                            },
                        ]
                    },
                    options: {
                        ...baseOptions(),
                        scales: {
                            ...baseOptions().scales,
                            y1: {
                                position: 'right',
                                grid: {
                                    drawOnChartArea: false
                                },
                                ticks: {
                                    color: p.tick
                                },
                                beginAtZero: true
                            }
                        }
                    }
                });

                // 2) payment methods doughnut
                const pctx = $('paymentMethodsChart').getContext('2d');
                paymentMethodsChart = new Chart(pctx, {
                    type: 'doughnut',
                    data: {
                        labels: pm.map(x => x.method),
                        datasets: [{
                            data: pm.map(x => Number(x.total || 0)),
                            borderWidth: 0,
                            backgroundColor: pm.map((_, i) => ([
                                'rgba(59,130,246,.85)',
                                'rgba(34,197,94,.85)',
                                'rgba(245,158,11,.85)',
                                'rgba(139,92,246,.85)',
                                'rgba(244,63,94,.85)',
                                'rgba(14,165,233,.85)',
                                'rgba(100,116,139,.85)',
                            ])[i % 7])
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '62%',
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });

                // 3) order status bar
                const octx = $('orderStatusChart').getContext('2d');
                orderStatusChart = new Chart(octx, {
                    type: 'bar',
                    data: {
                        labels: st.map(x => x.status),
                        datasets: [{
                            label: 'Orders',
                            data: st.map(x => Number(x.count || 0)),
                            borderRadius: 10,
                            backgroundColor: st.map(s => {
                                const k = String(s.status || '').toLowerCase();
                                if (k === 'completed') return 'rgba(34,197,94,.85)';
                                if (k === 'pending') return 'rgba(245,158,11,.85)';
                                if (k === 'processing') return 'rgba(59,130,246,.85)';
                                if (k === 'cancelled' || k === 'canceled' || k === 'void')
                                    return 'rgba(244,63,94,.85)';
                                return 'rgba(139,92,246,.85)';
                            })
                        }]
                    },
                    options: {
                        ...baseOptions(),
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });

                // 4) profit components bar (snapshot)
                const cctx = $('componentsChart').getContext('2d');
                const netSales = Number(initialDash.net_sales || 0);
                const cogs = Number(initialDash.cost_of_goods_sold || 0);
                const exp = Number(initialDash.expenses_total || 0);

                componentsChart = new Chart(cctx, {
                    type: 'bar',
                    data: {
                        labels: ['Today'],
                        datasets: [{
                                label: 'Net Sales',
                                data: [netSales],
                                borderRadius: 12,
                                backgroundColor: 'rgba(14,165,233,.85)'
                            },
                            {
                                label: 'COGS',
                                data: [cogs],
                                borderRadius: 12,
                                backgroundColor: 'rgba(244,63,94,.80)'
                            },
                            {
                                label: 'Expenses',
                                data: [exp],
                                borderRadius: 12,
                                backgroundColor: 'rgba(245,158,11,.80)'
                            },
                        ]
                    },
                    options: baseOptions()
                });

                // 5) expenses by category doughnut
                const ecctx = $('expenseCategoryChart').getContext('2d');
                expenseCategoryChart = new Chart(ecctx, {
                    type: 'doughnut',
                    data: {
                        labels: exCat.map(x => x.category),
                        datasets: [{
                            data: exCat.map(x => Number(x.total || 0)),
                            borderWidth: 0,
                            backgroundColor: exCat.map((_, i) => ([
                                'rgba(245,158,11,.85)',
                                'rgba(59,130,246,.85)',
                                'rgba(34,197,94,.85)',
                                'rgba(139,92,246,.85)',
                                'rgba(244,63,94,.85)',
                                'rgba(14,165,233,.85)',
                                'rgba(100,116,139,.85)',
                            ])[i % 7])
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '62%',
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });

                // 6) expenses by method doughnut
                const emctx = $('expenseMethodChart').getContext('2d');
                expenseMethodChart = new Chart(emctx, {
                    type: 'doughnut',
                    data: {
                        labels: exMet.map(x => x.method),
                        datasets: [{
                            data: exMet.map(x => Number(x.total || 0)),
                            borderWidth: 0,
                            backgroundColor: exMet.map((_, i) => ([
                                'rgba(34,197,94,.85)',
                                'rgba(59,130,246,.85)',
                                'rgba(245,158,11,.85)',
                                'rgba(139,92,246,.85)',
                                'rgba(244,63,94,.85)',
                                'rgba(14,165,233,.85)',
                                'rgba(100,116,139,.85)',
                            ])[i % 7])
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '62%',
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }

            function updateCharts(d) {
                if (salesChart && Array.isArray(d.daily_sales)) {
                    salesChart.data.labels = d.daily_sales.map(r => r.date);
                    salesChart.data.datasets[0].data = d.daily_sales.map(r => Number(r.revenue || 0));
                    salesChart.data.datasets[1].data = d.daily_sales.map(r => Number(r.orders || 0));
                    salesChart.update('none');
                }

                if (paymentMethodsChart && Array.isArray(d.payment_methods_breakdown)) {
                    paymentMethodsChart.data.labels = d.payment_methods_breakdown.map(x => x.method);
                    paymentMethodsChart.data.datasets[0].data = d.payment_methods_breakdown.map(x => Number(x.total ||
                        0));
                    paymentMethodsChart.update('none');
                }

                if (orderStatusChart && Array.isArray(d.order_status_distribution)) {
                    orderStatusChart.data.labels = d.order_status_distribution.map(x => x.status);
                    orderStatusChart.data.datasets[0].data = d.order_status_distribution.map(x => Number(x.count || 0));
                    orderStatusChart.update('none');
                }

                if (componentsChart) {
                    componentsChart.data.datasets[0].data = [Number(d.net_sales || 0)];
                    componentsChart.data.datasets[1].data = [Number(d.cost_of_goods_sold || 0)];
                    componentsChart.data.datasets[2].data = [Number(d.expenses_total || 0)];
                    componentsChart.update('none');
                }

                if (expenseCategoryChart && Array.isArray(d.expenses_by_category)) {
                    expenseCategoryChart.data.labels = d.expenses_by_category.map(x => x.category);
                    expenseCategoryChart.data.datasets[0].data = d.expenses_by_category.map(x => Number(x.total || 0));
                    expenseCategoryChart.update('none');
                }

                if (expenseMethodChart && Array.isArray(d.expenses_by_method)) {
                    expenseMethodChart.data.labels = d.expenses_by_method.map(x => x.method);
                    expenseMethodChart.data.datasets[0].data = d.expenses_by_method.map(x => Number(x.total || 0));
                    expenseMethodChart.update('none');
                }
            }

            function renderRecentOrders(paginated) {
                const rows = Array.isArray(paginated.data) ? paginated.data : [];
                const meta = paginated.meta || {};

                const body = $('recentOrdersBody');
                if (body) {
                    body.innerHTML = rows.map(o => `
                        <tr>
                            <!-- Order Number - Link to order details -->
                            <td><a href="/orders/${o.id}" style="color: #007bff; text-decoration: none;">${o.order_no ?? ''}</a></td>

                            <!-- Customer Name - Link to customer details -->
                            <td>
                                <a href="/customers/${o.customer && o.customer.id ? o.customer.id : ''}" style="color: #007bff; text-decoration: none;">
                                    ${(o.customer && o.customer.name) ? escapeHtml(o.customer.name) : 'Guest'}
                                </a>
                            </td>

                            <!-- Order Date - Link to order details -->
                            <td><a href="/orders/${o.id}" style="color: #007bff; text-decoration: none;">
                                ${o.created_at ? new Date(o.created_at).toLocaleString() : ''}</a>
                            </td>

                            <!-- Payable Total -->
                            <td class="numeric">&#2547;${money(o.payable_total)}</td>

                            <!-- Payment Status - Link to payments -->
                            <td><a href="/orders/${o.id}/payments" style="color: #007bff; text-decoration: none;">
                                <span class="badge">${o.payment_status ?? ''}</span></a>
                            </td>

                            <!-- Order Status -->
                            <td><span class="badge">${o.status ?? ''}</span></td>
                        </tr>
                    `).join('');
                }

                // Pagination logic
                recentPage = Number(meta.current_page || 1);
                recentLastPage = Number(meta.last_page || 1);

                const metaText = $('recentMetaText');
                if (metaText) metaText.textContent =
                    `Showing ${meta.from || 0} - ${meta.to || 0} of ${meta.total || 0}`;

                const pageText = $('recentPageText');
                if (pageText) pageText.textContent = `Page ${recentPage} / ${recentLastPage}`;

                const prevBtn = $('recentPrevBtn');
                const nextBtn = $('recentNextBtn');
                if (prevBtn) prevBtn.disabled = recentPage <= 1;
                if (nextBtn) nextBtn.disabled = recentPage >= recentLastPage;

                // Show pagination only if the total count is more than 5
                const paginationContainer = $('paginationContainer');
                if (paginationContainer) {
                    if (meta.total > 5) {
                        paginationContainer.style.display = 'flex';  // Show pagination
                    } else {
                        paginationContainer.style.display = 'none';  // Hide pagination
                    }
                }
            }




            function renderTopProducts(list) {
                const body = $('topProductsBody');
                if (!body) return;

                if (!Array.isArray(list) || !list.length) {
                    body.innerHTML = `<tr><td colspan="8" style="opacity:.7;">No top products yet for today.</td></tr>`;
                    return;
                }

                body.innerHTML = list.map(tp => {
                    const p = tp.product || {};
                    const name = p.name || '-';
                    const cat = (p.category && p.category.name) ? p.category.name : '-';
                    const brand = (p.brand && p.brand.name) ? p.brand.name : '-';

                    const qty = Number(tp.total_qty || 0).toFixed(2);
                    const rev = money(tp.total_revenue || 0);
                    const cost = money(tp.total_cost || 0);
                    const profit = money(tp.profit || 0);
                    const margin = Number(tp.margin || 0).toFixed(2) + '%';

                    return `
                <tr>
                    <td>&#2547;{escapeHtml(name)}</td>
                    <td>&#2547;{escapeHtml(cat)}</td>
                    <td>&#2547;{escapeHtml(brand)}</td>
                    <td class="numeric">&#2547;{qty}</td>
                    <td class="numeric">&#2547;{rev}</td>
                    <td class="numeric">&#2547;{cost}</td>
                    <td class="numeric">&#2547;{profit}</td>
                    <td class="numeric">&#2547;{margin}</td>
                </tr>
            `;
                }).join('');
            }

            /* =======================
             * Full payload render
             * ======================= */
            function renderDashboard(payload) {
                const d = payload.dashboard || {};
                const pm = Array.isArray(d.payment_methods_breakdown) ? d.payment_methods_breakdown : [];

                // step 2
                setLoadingProgress(Math.max(loadingProgress, 35), 'Updating metrics…');

                setText('m_total_sales', money(d.total_sales));
                setText('m_total_orders', d.total_orders ?? 0);
                setText('m_avg_order', money(d.avg_order_value));
                setText('m_total_discounts', money(d.total_discounts));
                setText('m_sold_qty', Number(d.sold_qty || 0).toFixed(2));

                setText('m_net_profit', money(d.net_profit));
                setText('m_profit_margin', (Number(d.profit_margin || 0)).toFixed(2) + '%');
                setText('m_net_sales', money(d.net_sales));
                setText('m_cogs', money(d.cost_of_goods_sold));
                setText('m_gross_profit', money(d.gross_profit));
                setText('m_gross_margin', (Number(d.gross_margin || 0)).toFixed(2) + '%');

                setText('m_total_payments', money(d.total_payments));
                setText('m_orders_paid', d.orders_paid ?? 0);
                setText('m_paid_amount', money(d.paid_amount ?? d.total_payments ?? 0));
                setText('m_due_amount', money(d.due_amount ?? 0));
                setText('m_methods_count', pm.length);

                setText('m_new_customers', d.new_customers ?? 0);
                setText('m_reward_points', Number(d.total_reward_points || 0).toLocaleString());
                setText('m_avg_due', money(d.avg_due_balance));

                setText('m_total_refunds', money(d.total_refunds));
                setText('m_total_returns', d.total_returns ?? 0);
                setText('m_total_exchanges', d.total_exchanges ?? 0);

                setText('m_stock_value', money(d.stock_cost_value));
                setText('m_available_stock', Number(d.available_stock || 0).toLocaleString());
                setText('m_reserved_stock', Number(d.total_reserved || 0).toLocaleString());
                setText('m_on_hand_stock', Number(d.on_hand_stock || 0).toLocaleString());

                // expenses
                const expTk = moneyTk(d.expenses_total ?? 0);
                setText('m_expenses_total', expTk);
                setText('m_expenses_total_card', expTk);
                setText('m_expenses_count', d.expenses_count ?? 0);
                setText('m_expenses_top_category', (d.expenses_by_category?.[0]?.category) ?? '—');
                setText('m_expenses_top_method', (d.expenses_by_method?.[0]?.method) ?? '—');
                setText('expensesTotalStatus', expTk);

                // insights table values
                setText('a_paid_amount', money(d.paid_amount ?? d.total_payments ?? 0));
                setText('a_due_amount', money(d.due_amount ?? 0));
                setText('a_gross_profit', money(d.gross_profit ?? 0));
                setText('a_net_profit', money(d.net_profit ?? 0));
                setText('a_low_stock_items', d.low_stock_items ?? 0);
                setText('a_stock_cost_value', money(d.stock_cost_value ?? 0));

                // status bar counters
                setText('pendingOrdersCount', payload.pending_orders ?? 0);
                setText('abandonedCartsCount', payload.abandoned_carts ?? 0);
                setText('lowStockCount', payload.low_stock_items ?? 0);

                setLoadingProgress(Math.max(loadingProgress, 60), 'Updating charts…');
                updateCharts(d);

                setLoadingProgress(Math.max(loadingProgress, 78), 'Updating tables…');
                if (d.recent_orders_paginated) renderRecentOrders(d.recent_orders_paginated);
                if (d.top_products) renderTopProducts(d.top_products);

                setLoadingProgress(Math.max(loadingProgress, 92), 'Finalizing…');
                setLastUpdate(payload.timestamp);
            }

            /* =======================
             * Data loaders
             * ======================= */
            async function loadAll({
                page = 1,
                showLoader = true
            } = {}) {
                if (loadAbort) loadAbort.abort();
                loadAbort = new AbortController();
                const signal = loadAbort.signal;

                if (showLoader) showLoading('Fetching dashboard data…');

                try {
                    const qs = getLocationQS({
                        page
                    });

                    setLoadingProgress(12, 'Requesting payload…');
                    const res = await fetch(URL_DATA + (qs ? ('?' + qs) : ''), {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        cache: 'no-store',
                        signal
                    });

                    if (!res.ok) throw new Error('HTTP ' + res.status);

                    setLoadingProgress(22, 'Parsing response…');
                    const payload = await res.json();

                    renderDashboard(payload);

                } catch (e) {
                    if (e?.name === 'AbortError') return;
                    console.error(e);
                    // keep it quiet but stop loader
                } finally {
                    if (showLoader) hideLoading();
                }
            }

            async function fetchRecentOrders(page) {
                // lightweight loader, but still modern
                showLoading('Loading recent orders…');
                setLoadingProgress(10, 'Requesting orders page…');

                try {
                    const qs = getLocationQS({
                        page
                    });

                    const res = await fetch(URL_RECENT + (qs ? ('?' + qs) : ''), {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        cache: 'no-store'
                    });

                    if (!res.ok) return;

                    setLoadingProgress(55, 'Rendering orders…');
                    const payload = await res.json();

                    if (payload.recent_orders_paginated) renderRecentOrders(payload.recent_orders_paginated);
                    if (payload.timestamp) setLastUpdate(payload.timestamp);

                    setLoadingProgress(90, 'Done…');
                } catch (e) {
                    console.error(e);
                } finally {
                    hideLoading();
                }
            }

            /* =======================
             * Realtime
             * ======================= */
            function stopRealtime() {
                if (realtimeTimer) {
                    clearInterval(realtimeTimer);
                    realtimeTimer = null;
                }
                if (stream) {
                    stream.close();
                    stream = null;
                }
            }

            function startPollingRealtime() {
                const run = async () => {
                    const qs = getLocationQS({
                        since: lastServerTimestamp
                    });

                    try {
                        const res = await fetch(URL_REALTIME + (qs ? ('?' + qs) : ''), {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin',
                            cache: 'no-store'
                        });
                        if (!res.ok) return;

                        const data = await res.json();

                        setText('newOrdersCount', data.new_orders ?? 0);
                        setText('pendingOrdersCount', data.pending_orders ?? 0);
                        setText('abandonedCartsCount', data.abandoned_carts ?? 0);
                        setText('lowStockCount', data.low_stock_items ?? 0);

                        if ((data.new_orders ?? 0) > 0) flash($('newOrdersCount'));
                        setLastUpdate(data.timestamp);

                        // if activity, refresh silently to keep charts/tables accurate
                        if ((data.new_orders ?? 0) > 0) loadAll({
                            page: recentPage || 1,
                            showLoader: false
                        });
                    } catch (e) {}
                };

                run();
                realtimeTimer = setInterval(run, 5000);
            }

            function startRealtime() {
                stopRealtime();

                const qs = getLocationQS(); // includes location_id only
                if (!!window.EventSource) {
                    stream = new EventSource(URL_STREAM + (qs ? ('?' + qs) : ''));

                    stream.addEventListener('counters', (ev) => {
                        const data = JSON.parse(ev.data);

                        setText('newOrdersCount', data.new_orders ?? 0);
                        setText('pendingOrdersCount', data.pending_orders ?? 0);
                        setText('abandonedCartsCount', data.abandoned_carts ?? 0);
                        setText('lowStockCount', data.low_stock_items ?? 0);

                        if ((data.new_orders ?? 0) > 0) flash($('newOrdersCount'));
                        setLastUpdate(data.timestamp);

                        if ((data.new_orders ?? 0) > 0) loadAll({
                            page: recentPage || 1,
                            showLoader: false
                        });
                    });

                    stream.onerror = () => {
                        stopRealtime();
                        startPollingRealtime();
                    };

                    return;
                }

                startPollingRealtime();
            }

            /* =======================
             * Boot
             * ======================= */
            document.addEventListener('DOMContentLoaded', async () => {
                // init charts from server payload instantly (no loader)
                initCharts(@json($dashboard));

                // init table controls from server meta
                renderRecentOrders({
                    data: @json($recentRows),
                    meta: @json($recentMeta)
                });

                // ✅ auto-load on location change (no apply button, no refresh button)
                $('locationFilter')?.addEventListener('change', debounce(async () => {
                    recentPage = 1;
                    await loadAll({
                        page: 1,
                        showLoader: true
                    });
                    startRealtime();
                }, 150));

                // pagination
                $('recentPrevBtn')?.addEventListener('click', async () => {
                    if (recentPage <= 1) return;
                    await fetchRecentOrders(recentPage - 1);
                });

                $('recentNextBtn')?.addEventListener('click', async () => {
                    if (recentPage >= recentLastPage) return;
                    await fetchRecentOrders(recentPage + 1);
                });

                // start realtime now
                startRealtime();
            });
        })();
    </script>
@endsection
