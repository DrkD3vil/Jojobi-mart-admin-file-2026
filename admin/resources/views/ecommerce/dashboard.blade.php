{{-- resources/views/ecommerce/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
.ecod-wrap{max-width:1280px;margin:0 auto;padding:16px;color:var(--foreground);}
.ecod-top{margin-bottom:18px;}
.ecod-title{font-size:1.55rem;font-weight:900;display:flex;align-items:center;gap:10px;}
.ecod-sub{color:var(--text-secondary);font-size:.95rem;margin-top:6px;}
.ecod-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:18px;}
.ecod-grid{display:grid;grid-template-columns:2fr 1fr;gap:14px;}
@media(max-width:900px){.ecod-grid{grid-template-columns:1fr;}}
.ecod-card{background:var(--card);border:1px solid var(--border-color);border-radius:var(--radius);padding:16px;box-shadow:var(--card-shadow);}
.ecod-card h3{margin:0 0 12px;font-size:.95rem;font-weight:800;display:flex;align-items:center;gap:8px;}
.ecod-top-row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px dashed var(--border-color);font-size:.85rem;}
.ecod-top-row:last-child{border-bottom:none;}
.ecod-top-row .n{font-weight:800;}
.ecod-mini{color:var(--text-secondary);font-size:.8rem;}
</style>

<div class="ecod-wrap">
    <div class="ecod-top" data-reveal>
        <div class="ecod-title"><i class="fas fa-chart-line"></i> Ecommerce Dashboard</div>
        <div class="ecod-sub">How the online storefront is performing, separate from till sales.</div>
    </div>

    <div class="ecod-stats" id="statGrid" data-reveal>
        <x-stat title="Online Orders" value="…" />
        <x-stat title="Revenue" value="…" />
        <x-stat title="Avg Order Value" value="…" />
        <x-stat title="Pending" value="…" />
        <x-stat title="Processing" value="…" />
        <x-stat title="Completed" value="…" />
    </div>

    <div class="ecod-grid" data-reveal>
        <div class="ecod-card">
            <h3><i class="fas fa-chart-area"></i> Orders & Revenue (last 14 days)</h3>
            <canvas id="trendChart" height="110"></canvas>
        </div>
        <div class="ecod-card">
            <h3><i class="fas fa-ranking-star"></i> Top Products (online)</h3>
            <div id="topProducts"><div class="ecod-mini">Loading…</div></div>
        </div>
    </div>
</div>

<script>
(() => {
    const money = (n) => '৳' + Number(n || 0).toLocaleString(undefined, { maximumFractionDigits: 2 });
    const statGrid = document.getElementById('statGrid');

    function setStats(m) {
        const cards = statGrid.querySelectorAll('h2');
        const values = [m.total_orders, money(m.revenue), money(m.avg_order_value), m.pending, m.processing, m.completed];
        cards.forEach((el, i) => { if (values[i] !== undefined) el.textContent = values[i]; });
    }

    async function loadMetrics() {
        const res = await fetch('{{ route("ecommerce.dashboard.metrics") }}', { headers: { Accept: 'application/json' } });
        setStats(await res.json());
    }

    async function loadCharts() {
        const res = await fetch('{{ route("ecommerce.dashboard.charts") }}', { headers: { Accept: 'application/json' } });
        const data = await res.json();

        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: data.daily.labels,
                datasets: [
                    { label: 'Orders', data: data.daily.orders, borderColor: '#3B6FC4', backgroundColor: 'rgba(59,111,196,.12)', tension: .3, yAxisID: 'y' },
                    { label: 'Revenue', data: data.daily.revenue, borderColor: '#177264', backgroundColor: 'rgba(23,114,100,.10)', tension: .3, yAxisID: 'y1' },
                ],
            },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: { position: 'left', beginAtZero: true, ticks: { precision: 0 } },
                    y1: { position: 'right', beginAtZero: true, grid: { drawOnChartArea: false } },
                },
            },
        });

        const el = document.getElementById('topProducts');
        if (!data.top_products.length) {
            el.innerHTML = '<div class="ecod-mini">No online sales yet.</div>';
            return;
        }
        el.innerHTML = data.top_products.map(p => `
            <div class="ecod-top-row">
                <span>${p.name}</span>
                <span class="n">${p.qty} · ${money(p.revenue)}</span>
            </div>
        `).join('');
    }

    loadMetrics();
    loadCharts();
})();
</script>
@endsection
