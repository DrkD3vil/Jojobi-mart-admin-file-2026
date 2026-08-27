@extends('layouts.app')

@section('content')
<style>
    .reports-analytics { padding: 20px; max-width: 100%; overflow-x: hidden; }

    .ra-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--border-color);
        flex-wrap: wrap; gap: 12px;
    }
    .ra-header .page-title { font-size: 24px; font-weight: 600; margin: 0 0 4px 0; color: var(--text-primary); }
    .ra-header .page-subtitle { color: var(--text-secondary); font-size: 14px; margin: 0; }
    .ra-header-actions { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }

    .ra-btn {
        padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 500;
        border: 1px solid var(--border-color); background: var(--card); color: var(--text-primary);
        cursor: pointer; transition: all .15s ease; display: inline-flex; align-items: center; gap: 6px;
    }
    .ra-btn:hover { box-shadow: var(--card-shadow-hover); }
    .ra-btn-primary { background: var(--primary, #6366f1); color: #fff; border-color: transparent; }

    .ra-filters {
        display: flex; flex-wrap: wrap; gap: 14px; align-items: flex-end;
        background: var(--card); border: 1px solid var(--border-color); border-radius: 12px;
        padding: 16px; margin-bottom: 20px; box-shadow: var(--card-shadow);
    }
    .ra-filter-group { display: flex; flex-direction: column; gap: 6px; min-width: 160px; }
    .ra-filter-group label { font-size: 12px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; letter-spacing: .03em; }
    .ra-select, .ra-input {
        padding: 8px 10px; border-radius: 8px; border: 1px solid var(--border-color);
        background: var(--bg-secondary); color: var(--text-primary); font-size: 14px;
    }

    .ra-tabs { display: flex; gap: 4px; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); flex-wrap: wrap; }
    .ra-tab {
        padding: 10px 18px; font-size: 14px; font-weight: 500; color: var(--text-secondary);
        cursor: pointer; border-bottom: 2px solid transparent; background: none; border-top: none; border-left: none; border-right: none;
    }
    .ra-tab.active { color: var(--primary, #6366f1); border-bottom-color: var(--primary, #6366f1); }

    .ra-panel { display: none; }
    .ra-panel.active { display: block; }

    .ra-kpis { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; margin-bottom: 20px; }
    .ra-kpi { background: var(--card); border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; box-shadow: var(--card-shadow); }
    .ra-kpi-label { font-size: 12px; color: var(--text-secondary); font-weight: 600; text-transform: uppercase; letter-spacing: .03em; margin-bottom: 6px; }
    .ra-kpi-value { font-size: 22px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; }
    .ra-delta { font-size: 12px; font-weight: 600; padding: 2px 8px; border-radius: 999px; display: inline-block; }
    .ra-delta.up { background: rgba(34,197,94,.15); color: #16a34a; }
    .ra-delta.down { background: rgba(239,68,68,.15); color: #dc2626; }
    .ra-delta.flat { background: rgba(148,163,184,.15); color: #64748b; }

    .ra-card { background: var(--card); border: 1px solid var(--border-color); border-radius: 12px; box-shadow: var(--card-shadow); margin-bottom: 20px; }
    .ra-card-header { display: flex; justify-content: space-between; align-items: center; padding: 16px 18px; border-bottom: 1px solid var(--border-color); flex-wrap: wrap; gap: 10px; }
    .ra-card-header h3 { margin: 0; font-size: 15px; font-weight: 600; color: var(--text-primary); }
    .ra-card-body { padding: 18px; }

    .ra-toggle-group { display: flex; gap: 4px; background: var(--bg-secondary); border-radius: 8px; padding: 3px; }
    .ra-toggle-btn { padding: 6px 12px; font-size: 13px; border-radius: 6px; border: none; background: none; color: var(--text-secondary); cursor: pointer; }
    .ra-toggle-btn.active { background: var(--card); color: var(--text-primary); box-shadow: var(--card-shadow); }

    .ra-table-wrap { overflow-x: auto; }
    .ra-table { width: 100%; border-collapse: collapse; font-size: 14px; min-width: 640px; }
    .ra-table thead th {
        text-align: left; padding: 10px 12px; font-size: 12px; font-weight: 600; color: var(--text-secondary);
        text-transform: uppercase; letter-spacing: .03em; border-bottom: 1px solid var(--border-color); cursor: pointer; white-space: nowrap;
    }
    .ra-table thead th.sortable:hover { color: var(--text-primary); }
    .ra-table tbody td { padding: 10px 12px; border-bottom: 1px solid var(--border-color); color: var(--text-primary); white-space: nowrap; }
    .ra-table tbody tr:hover { background: var(--bg-secondary); }
    .ra-num { text-align: right; font-variant-numeric: tabular-nums; }
    .ra-profit-pos { color: #16a34a; font-weight: 600; }
    .ra-profit-neg { color: #dc2626; font-weight: 600; }
    .ra-badge { font-size: 11px; padding: 2px 8px; border-radius: 999px; background: rgba(99,102,241,.15); color: var(--primary, #6366f1); font-weight: 600; }
    .ra-muted { color: var(--text-secondary); font-size: 13px; }

    .ra-pagination { display: flex; justify-content: space-between; align-items: center; padding: 12px 18px; border-top: 1px solid var(--border-color); font-size: 13px; color: var(--text-secondary); }
    .ra-pagination-btns { display: flex; gap: 6px; }

    .ra-empty { padding: 40px 20px; text-align: center; color: var(--text-secondary); }
    .ra-skeleton { padding: 18px; }
    .ra-skeleton-row { height: 16px; background: linear-gradient(to right, var(--bg-secondary) 4%, var(--border-color) 25%, var(--bg-secondary) 36%); border-radius: 4px; margin-bottom: 10px; animation: ra-shimmer 1.4s infinite linear; background-size: 1000px 100%; }
    @keyframes ra-shimmer { 0% { background-position: -1000px 0; } 100% { background-position: 1000px 0; } }

    .ra-chart-wrap { position: relative; height: 280px; }
</style>

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="reports-analytics" id="reportsAnalytics"
     data-products-url="{{ route('dashboard.reports.products') }}"
     data-customers-url="{{ route('dashboard.reports.customers') }}"
     data-locations-url="{{ route('dashboard.reports.locations') }}"
     data-trends-url="{{ route('dashboard.reports.trends') }}"
     data-export-url="{{ route('dashboard.reports.export') }}">

    <div class="ra-header">
        <div>
            <h1 class="page-title">Reports & Analytics</h1>
            <p class="page-subtitle">Product, customer, location profitability and trends</p>
        </div>
        <div class="ra-header-actions">
            <button class="ra-btn" id="raRefresh"><i data-lucide="refresh-cw" style="width:14px;height:14px"></i> Refresh</button>
            <button class="ra-btn ra-btn-primary" id="raExport"><i data-lucide="download" style="width:14px;height:14px"></i> Export</button>
        </div>
    </div>

    <div class="ra-filters">
        <div class="ra-filter-group">
            <label>Date Range</label>
            <select id="raDateRange" class="ra-select">
                @foreach ($dateRanges as $value => $label)
                    <option value="{{ $value }}" {{ $currentRange == $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="ra-filter-group" id="raCustomRange" style="{{ $currentRange == 'custom' ? '' : 'display:none' }}">
            <label>Start Date</label>
            <input type="date" id="raStartDate" class="ra-input" value="{{ $startDate }}">
        </div>
        <div class="ra-filter-group" id="raCustomRangeEnd" style="{{ $currentRange == 'custom' ? '' : 'display:none' }}">
            <label>End Date</label>
            <input type="date" id="raEndDate" class="ra-input" value="{{ $endDate }}">
        </div>
        <div class="ra-filter-group">
            <label>Location</label>
            <select id="raLocation" class="ra-select">
                <option value="">All Locations</option>
                @foreach ($locations as $location)
                    <option value="{{ $location->id }}" {{ (int) $locationId === (int) $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="ra-tabs">
        <button class="ra-tab active" data-tab="trends">Trends</button>
        <button class="ra-tab" data-tab="products">Products & Categories</button>
        <button class="ra-tab" data-tab="customers">Customers</button>
        <button class="ra-tab" data-tab="locations">Locations</button>
    </div>

    <!-- Trends -->
    <div class="ra-panel active" id="panel-trends">
        <div class="ra-kpis" id="raTrendKpis"></div>
        <div class="ra-card">
            <div class="ra-card-header"><h3>Net Sales & Net Profit Trend</h3></div>
            <div class="ra-card-body">
                <div class="ra-chart-wrap"><canvas id="raTrendChart"></canvas></div>
            </div>
        </div>
    </div>

    <!-- Products -->
    <div class="ra-panel" id="panel-products">
        <div class="ra-card">
            <div class="ra-card-header">
                <h3>Profitability</h3>
                <div class="ra-toggle-group">
                    <button class="ra-toggle-btn active" data-group="product">By Product</button>
                    <button class="ra-toggle-btn" data-group="category">By Category</button>
                </div>
            </div>
            <div class="ra-card-body" style="padding:0" id="raProductsBody"></div>
        </div>
    </div>

    <!-- Customers -->
    <div class="ra-panel" id="panel-customers">
        <div class="ra-card">
            <div class="ra-card-header">
                <h3>Customer Profitability</h3>
                <span class="ra-muted" id="raCustomerSummary"></span>
            </div>
            <div class="ra-card-body" style="padding:0" id="raCustomersBody"></div>
        </div>
    </div>

    <!-- Locations -->
    <div class="ra-panel" id="panel-locations">
        <div class="ra-card">
            <div class="ra-card-header"><h3>Location Breakdown</h3></div>
            <div class="ra-card-body" style="padding:0" id="raLocationsBody"></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function () {
    const root = document.getElementById('reportsAnalytics');
    const urls = {
        products: root.dataset.productsUrl,
        customers: root.dataset.customersUrl,
        locations: root.dataset.locationsUrl,
        trends: root.dataset.trendsUrl,
        export: root.dataset.exportUrl,
    };

    const fmt = (n) => '৳ ' + (Number(n) || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const pct = (n) => (Number(n) || 0).toFixed(2) + '%';

    const state = {
        date_range: document.getElementById('raDateRange').value,
        start_date: document.getElementById('raStartDate').value,
        end_date: document.getElementById('raEndDate').value,
        location_id: document.getElementById('raLocation').value,
        productsGroup: 'product',
        productsSort: 'revenue', productsDir: 'desc', productsPage: 1,
        customersSort: 'revenue', customersDir: 'desc', customersPage: 1,
    };

    function baseParams() {
        const p = { date_range: state.date_range, location_id: state.location_id };
        if (state.date_range === 'custom') { p.start_date = state.start_date; p.end_date = state.end_date; }
        return p;
    }

    function buildUrl(base, params) {
        const usp = new URLSearchParams();
        Object.entries(params).forEach(([k, v]) => { if (v !== '' && v !== null && v !== undefined) usp.set(k, v); });
        return base + '?' + usp.toString();
    }

    function skeleton(el, rows = 5) {
        el.innerHTML = '<div class="ra-skeleton">' + Array.from({ length: rows }).map(() => '<div class="ra-skeleton-row"></div>').join('') + '</div>';
    }

    /* ---------- Trends ---------- */
    let trendChart = null;
    function loadTrends() {
        const kpiEl = document.getElementById('raTrendKpis');
        skeleton(kpiEl, 4);

        fetch(buildUrl(urls.trends, baseParams()))
            .then(r => r.json())
            .then(data => {
                const c = data.current, d = data.deltas;
                const deltaBadge = (v) => {
                    const cls = v > 0 ? 'up' : (v < 0 ? 'down' : 'flat');
                    const arrow = v > 0 ? '▲' : (v < 0 ? '▼' : '—');
                    return `<span class="ra-delta ${cls}">${arrow} ${Math.abs(v).toFixed(1)}% vs last period</span>`;
                };

                kpiEl.innerHTML = `
                    <div class="ra-kpi"><div class="ra-kpi-label">Net Sales</div><div class="ra-kpi-value">${fmt(c.net_sales)}</div>${deltaBadge(d.net_sales)}</div>
                    <div class="ra-kpi"><div class="ra-kpi-label">Net Profit</div><div class="ra-kpi-value">${fmt(c.net_profit)}</div>${deltaBadge(d.net_profit)}</div>
                    <div class="ra-kpi"><div class="ra-kpi-label">Profit Margin</div><div class="ra-kpi-value">${pct(c.profit_margin)}</div>${deltaBadge(d.profit_margin)}</div>
                    <div class="ra-kpi"><div class="ra-kpi-label">Orders</div><div class="ra-kpi-value">${c.total_orders}</div>${deltaBadge(d.total_orders)}</div>
                `;

                const labels = data.daily.map(r => r.date);
                const sales = data.daily.map(r => r.net_sales);
                const profit = data.daily.map(r => r.net_profit);

                const style = getComputedStyle(document.documentElement);
                const c1 = style.getPropertyValue('--chart-1').trim() || '#6366f1';
                const c2 = style.getPropertyValue('--chart-2').trim() || '#22c55e';

                const ctx = document.getElementById('raTrendChart');
                if (trendChart) trendChart.destroy();
                trendChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [
                            { label: 'Net Sales', data: sales, borderColor: c1, backgroundColor: c1 + '22', tension: .3, fill: true },
                            { label: 'Net Profit', data: profit, borderColor: c2, backgroundColor: c2 + '22', tension: .3, fill: true },
                        ],
                    },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } },
                });
            })
            .catch(() => { kpiEl.innerHTML = '<div class="ra-empty">Failed to load trends.</div>'; });
    }

    /* ---------- Products ---------- */
    const productCols = [
        { key: 'name', label: 'Name' },
        { key: 'qty', label: 'Qty Sold', num: true, sort: 'qty' },
        { key: 'revenue', label: 'Revenue', num: true, sort: 'revenue' },
        { key: 'cost', label: 'Cost', num: true },
        { key: 'profit', label: 'Profit', num: true, sort: 'profit' },
        { key: 'margin', label: 'Margin', num: true, sort: 'margin' },
    ];

    function renderTable(el, cols, rows, sortState, onSort, meta, onPage, emptyText) {
        if (!rows.length) { el.innerHTML = `<div class="ra-empty">${emptyText}</div>`; return; }

        const thead = cols.map(c => {
            const sortable = !!c.sort;
            const active = sortable && sortState.sort === c.sort;
            const arrow = active ? (sortState.dir === 'asc' ? ' ↑' : ' ↓') : '';
            const cls = [sortable ? 'sortable' : '', c.num ? 'ra-num' : ''].filter(Boolean).join(' ');
            return `<th class="${cls}" data-sort="${c.sort || ''}">${c.label}${arrow}</th>`;
        }).join('');

        const tbody = rows.map(r => {
            return '<tr>' + cols.map(c => {
                let v = r[c.key];
                if (c.key === 'revenue' || c.key === 'cost') v = fmt(v);
                else if (c.key === 'profit') v = `<span class="${r.profit >= 0 ? 'ra-profit-pos' : 'ra-profit-neg'}">${fmt(r.profit)}</span>`;
                else if (c.key === 'margin') v = `<span class="${r.margin >= 0 ? 'ra-profit-pos' : 'ra-profit-neg'}">${pct(r.margin)}</span>`;
                else if (c.key === 'qty') v = Number(v).toLocaleString();
                else if (c.key === 'name' && r.category_name) v = `${v}<br><span class="ra-muted">${r.category_name}</span>`;
                return `<td class="${c.num ? 'ra-num' : ''}">${v ?? '-'}</td>`;
            }).join('') + '</tr>';
        }).join('');

        el.innerHTML = `
            <div class="ra-table-wrap"><table class="ra-table"><thead><tr>${thead}</tr></thead><tbody>${tbody}</tbody></table></div>
            <div class="ra-pagination">
                <span>Showing ${((meta.current_page - 1) * meta.per_page) + 1}-${Math.min(meta.current_page * meta.per_page, meta.total)} of ${meta.total}</span>
                <div class="ra-pagination-btns">
                    <button class="ra-btn" ${meta.current_page <= 1 ? 'disabled' : ''} data-page="${meta.current_page - 1}">Prev</button>
                    <button class="ra-btn" ${meta.current_page >= meta.last_page ? 'disabled' : ''} data-page="${meta.current_page + 1}">Next</button>
                </div>
            </div>
        `;

        el.querySelectorAll('th.sortable').forEach(th => th.addEventListener('click', () => onSort(th.dataset.sort)));
        el.querySelectorAll('[data-page]').forEach(btn => btn.addEventListener('click', () => onPage(parseInt(btn.dataset.page, 10))));
    }

    function loadProducts() {
        const el = document.getElementById('raProductsBody');
        skeleton(el);
        const params = Object.assign(baseParams(), {
            group_by: state.productsGroup, sort: state.productsSort, dir: state.productsDir, page: state.productsPage,
        });
        fetch(buildUrl(urls.products, params)).then(r => r.json()).then(data => {
            const cols = state.productsGroup === 'category'
                ? [{ key: 'name', label: 'Category' }, { key: 'qty', label: 'Qty Sold', num: true, sort: 'qty' },
                   { key: 'revenue', label: 'Revenue', num: true, sort: 'revenue' }, { key: 'cost', label: 'Cost', num: true },
                   { key: 'profit', label: 'Profit', num: true, sort: 'profit' }, { key: 'margin', label: 'Margin', num: true, sort: 'margin' }]
                : productCols;

            renderTable(el, cols, data.data, { sort: state.productsSort, dir: state.productsDir }, (sort) => {
                state.productsDir = (state.productsSort === sort && state.productsDir === 'desc') ? 'asc' : 'desc';
                state.productsSort = sort; state.productsPage = 1; loadProducts();
            }, data.meta, (page) => { state.productsPage = page; loadProducts(); }, 'No sales in this period.');
        }).catch(() => { el.innerHTML = '<div class="ra-empty">Failed to load products.</div>'; });
    }

    /* ---------- Customers ---------- */
    function loadCustomers() {
        const el = document.getElementById('raCustomersBody');
        skeleton(el);
        const params = Object.assign(baseParams(), { sort: state.customersSort, dir: state.customersDir, page: state.customersPage });

        fetch(buildUrl(urls.customers, params)).then(r => r.json()).then(data => {
            document.getElementById('raCustomerSummary').textContent =
                `${data.summary.total_customers} customers · ${data.summary.repeat_customers} repeat`;

            const cols = [
                { key: 'name', label: 'Customer' },
                { key: 'order_count', label: 'Orders', num: true, sort: 'orders' },
                { key: 'revenue', label: 'Revenue', num: true, sort: 'revenue' },
                { key: 'profit', label: 'Profit', num: true, sort: 'profit' },
                { key: 'margin', label: 'Margin', num: true },
                { key: 'due_balance', label: 'Due', num: true, sort: 'due' },
            ];

            const rows = data.data.map(r => Object.assign({}, r, {
                name: r.name + (r.is_repeat ? ' <span class="ra-badge">Repeat</span>' : ''),
                due_balance: fmt(r.due_balance),
            }));

            renderTable(el, cols, rows, { sort: state.customersSort, dir: state.customersDir }, (sort) => {
                state.customersDir = (state.customersSort === sort && state.customersDir === 'desc') ? 'asc' : 'desc';
                state.customersSort = sort; state.customersPage = 1; loadCustomers();
            }, data.meta, (page) => { state.customersPage = page; loadCustomers(); }, 'No customer activity in this period.');
        }).catch(() => { el.innerHTML = '<div class="ra-empty">Failed to load customers.</div>'; });
    }

    /* ---------- Locations ---------- */
    function loadLocations() {
        const el = document.getElementById('raLocationsBody');
        skeleton(el);
        fetch(buildUrl(urls.locations, baseParams())).then(r => r.json()).then(data => {
            if (!data.multi_location) {
                el.innerHTML = '<div class="ra-empty">Only one active location is configured — location comparison isn\'t meaningful yet.</div>';
                return;
            }
            const cols = [
                { key: 'name', label: 'Location' },
                { key: 'order_count', label: 'Orders', num: true },
                { key: 'net_sales', label: 'Net Sales', num: true },
                { key: 'cogs', label: 'COGS', num: true },
                { key: 'expenses', label: 'Expenses', num: true },
                { key: 'net_profit', label: 'Net Profit', num: true },
                { key: 'margin', label: 'Margin', num: true },
            ];
            const rows = data.data.map(r => Object.assign({}, r, { profit: r.net_profit }));
            renderTable(el, cols, rows, {}, () => {}, { current_page: 1, per_page: rows.length, total: rows.length, last_page: 1 }, () => {}, 'No location activity in this period.');
        }).catch(() => { el.innerHTML = '<div class="ra-empty">Failed to load locations.</div>'; });
    }

    /* ---------- Tabs & filters ---------- */
    const loaders = { trends: loadTrends, products: loadProducts, customers: loadCustomers, locations: loadLocations };
    const loaded = {};

    function activateTab(name) {
        document.querySelectorAll('.ra-tab').forEach(t => t.classList.toggle('active', t.dataset.tab === name));
        document.querySelectorAll('.ra-panel').forEach(p => p.classList.toggle('active', p.id === 'panel-' + name));
        if (!loaded[name]) { loaders[name](); loaded[name] = true; }
    }

    document.querySelectorAll('.ra-tab').forEach(t => t.addEventListener('click', () => activateTab(t.dataset.tab)));

    document.querySelectorAll('[data-group]').forEach(btn => btn.addEventListener('click', () => {
        document.querySelectorAll('[data-group]').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        state.productsGroup = btn.dataset.group;
        state.productsPage = 1;
        loadProducts();
    }));

    function reloadAll() { Object.keys(loaded).forEach(k => delete loaded[k]); Object.keys(loaders).forEach(k => { if (document.getElementById('panel-' + k).classList.contains('active')) { loaders[k](); loaded[k] = true; } else { loaded[k] = false; } }); }

    document.getElementById('raDateRange').addEventListener('change', (e) => {
        state.date_range = e.target.value;
        const isCustom = state.date_range === 'custom';
        document.getElementById('raCustomRange').style.display = isCustom ? '' : 'none';
        document.getElementById('raCustomRangeEnd').style.display = isCustom ? '' : 'none';
        if (!isCustom) reloadAll();
    });
    document.getElementById('raStartDate').addEventListener('change', (e) => { state.start_date = e.target.value; reloadAll(); });
    document.getElementById('raEndDate').addEventListener('change', (e) => { state.end_date = e.target.value; reloadAll(); });
    document.getElementById('raLocation').addEventListener('change', (e) => { state.location_id = e.target.value; reloadAll(); });
    document.getElementById('raRefresh').addEventListener('click', reloadAll);

    document.getElementById('raExport').addEventListener('click', () => {
        const params = new URLSearchParams(baseParams());
        params.set('format', 'csv');
        window.open(urls.export + '?' + params.toString(), '_blank');
    });

    loadTrends();
    loaded.trends = true;
    if (window.lucide) window.lucide.createIcons();
})();
</script>
@endsection
