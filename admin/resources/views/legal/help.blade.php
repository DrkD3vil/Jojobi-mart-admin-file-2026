@extends('layouts.app')

@section('content')
<style>
    .hlp-wrap { max-width: 860px; margin: 0 auto; padding: 24px; }
    .hlp-header { margin-bottom: 24px; }
    .hlp-header h1 { font-size: 22px; font-weight: 600; color: var(--text-primary); margin: 0 0 4px 0; }
    .hlp-header p { color: var(--text-secondary); font-size: 14px; margin: 0; }

    .hlp-search {
        width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--border-color);
        background: var(--bg-secondary); color: var(--text-primary); font-size: 14px; margin-bottom: 24px;
    }

    .hlp-section { margin-bottom: 8px; }
    .hlp-card {
        background: var(--card); border: 1px solid var(--border-color); border-radius: 12px;
        margin-bottom: 14px; box-shadow: var(--card-shadow); overflow: hidden;
    }
    .hlp-q {
        padding: 14px 18px; font-weight: 600; color: var(--text-primary); font-size: 14px;
        cursor: pointer; display: flex; justify-content: space-between; align-items: center;
    }
    .hlp-q .chev { transition: transform .15s ease; color: var(--text-secondary); }
    .hlp-card.open .chev { transform: rotate(180deg); }
    .hlp-a { padding: 0 18px 16px 18px; color: var(--text-secondary); font-size: 14px; line-height: 1.6; display: none; }
    .hlp-card.open .hlp-a { display: block; }
    .hlp-a ul { margin: 8px 0 0 18px; padding: 0; }
    .hlp-a li { margin-bottom: 4px; }

    .hlp-group-title { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: var(--text-secondary); margin: 28px 0 12px 0; }
    .hlp-group-title:first-of-type { margin-top: 0; }

    .hlp-contact {
        background: var(--card); border: 1px solid var(--border-color); border-radius: 12px;
        padding: 18px; margin-top: 28px; box-shadow: var(--card-shadow);
    }
    .hlp-contact h3 { margin: 0 0 6px 0; font-size: 15px; color: var(--text-primary); }
    .hlp-contact p { margin: 0; font-size: 14px; color: var(--text-secondary); }
</style>

<div class="hlp-wrap">
    <div class="hlp-header">
        <h1>Help & Quick-Start Guide</h1>
        <p>Answers to common questions about using this admin panel.</p>
    </div>

    <input type="text" class="hlp-search" id="hlpSearch" placeholder="Search help topics...">

    <div class="hlp-group-title">Dashboards & Reports</div>

    <div class="hlp-card" data-hlp>
        <div class="hlp-q" onclick="this.parentElement.classList.toggle('open')">
            What's the difference between Dashboard, Analysis, and Reports?
            <i data-lucide="chevron-down" class="chev w-4 h-4"></i>
        </div>
        <div class="hlp-a">
            <ul>
                <li><b>Dashboard</b> (home page) — a live snapshot of today's sales, profit, payments, and stock.</li>
                <li><b>Analysis</b> — the same kind of figures for any date range you choose (this month, last year, a custom range), with charts and filters by location/status.</li>
                <li><b>Reports</b> — deeper breakdowns: profitability by product/category, by customer, by location, and period-over-period trends.</li>
            </ul>
        </div>
    </div>

    <div class="hlp-card" data-hlp>
        <div class="hlp-q" onclick="this.parentElement.classList.toggle('open')">
            Why doesn't a cancelled or refunded order count toward sales?
            <i data-lucide="chevron-down" class="chev w-4 h-4"></i>
        </div>
        <div class="hlp-a">
            Cancelled orders never happened financially, so they're excluded everywhere. Refunded orders keep their sale on record but the refunded amount is netted out, so "Net Sales" always reflects what the business actually kept.
        </div>
    </div>

    <div class="hlp-group-title">Orders</div>

    <div class="hlp-card" data-hlp>
        <div class="hlp-q" onclick="this.parentElement.classList.toggle('open')">
            How do I take an order from Pending to Completed?
            <i data-lucide="chevron-down" class="chev w-4 h-4"></i>
        </div>
        <div class="hlp-a">
            Open the order and use the action buttons: <b>Start Processing</b> moves it to Processing, then <b>Complete Order</b> marks it done. Recording full payment also completes it automatically.
        </div>
    </div>

    <div class="hlp-card" data-hlp>
        <div class="hlp-q" onclick="this.parentElement.classList.toggle('open')">
            What happens when I cancel an order?
            <i data-lucide="chevron-down" class="chev w-4 h-4"></i>
        </div>
        <div class="hlp-a">
            Cancelling opens a confirmation page showing the order summary and an optional reason field. Confirming restores any reserved stock and voids any captured payment. Only Pending or Processing orders can be cancelled.
        </div>
    </div>

    <div class="hlp-card" data-hlp>
        <div class="hlp-q" onclick="this.parentElement.classList.toggle('open')">
            Can I edit an order after it's been returned against?
            <i data-lucide="chevron-down" class="chev w-4 h-4"></i>
        </div>
        <div class="hlp-a">
            No — once any item on an order has a return or exchange recorded against it, that order can no longer be edited, cleared, or have that item removed directly. Use the Return or Exchange flow for further adjustments instead.
        </div>
    </div>

    <div class="hlp-card" data-hlp>
        <div class="hlp-q" onclick="this.parentElement.classList.toggle('open')">
            What's the difference between splitting and merging orders?
            <i data-lucide="chevron-down" class="chev w-4 h-4"></i>
        </div>
        <div class="hlp-a">
            <b>Split</b> moves some items off an order into a new sub-order (e.g. one part ships now, the rest later). <b>Merge</b> reverses that, folding a sub-order's remaining items and payments back into its parent.
        </div>
    </div>

    <div class="hlp-group-title">Returns, Exchanges & Expenses</div>

    <div class="hlp-card" data-hlp>
        <div class="hlp-q" onclick="this.parentElement.classList.toggle('open')">
            How do I process a return or refund?
            <i data-lucide="chevron-down" class="chev w-4 h-4"></i>
        </div>
        <div class="hlp-a">
            Use <b>Return Items</b> from the order page for a partial or full return with restocking — this is the recommended flow. The order's <b>Refund</b> button does a full-order refund in one step for cases where a line-item return isn't needed.
        </div>
    </div>

    <div class="hlp-card" data-hlp>
        <div class="hlp-q" onclick="this.parentElement.classList.toggle('open')">
            How do I track business expenses?
            <i data-lucide="chevron-down" class="chev w-4 h-4"></i>
        </div>
        <div class="hlp-a">
            The Expenses section records spending by category, location, and payment method, with a monthly chart and CSV export. Deleted expenses go to Trash first and can be restored.
        </div>
    </div>

    <div class="hlp-group-title">Products & Inventory</div>

    <div class="hlp-card" data-hlp>
        <div class="hlp-q" onclick="this.parentElement.classList.toggle('open')">
            What's a product batch, and why does stock live there instead of on the product?
            <i data-lucide="chevron-down" class="chev w-4 h-4"></i>
        </div>
        <div class="hlp-a">
            A product can have multiple batches (different buy prices, expiry dates, or locations). Stock, cost, and sell price are all tracked per batch, so a product's overall availability and cost is the sum of its batches.
        </div>
    </div>

    <div class="hlp-group-title">Access & Permissions</div>

    <div class="hlp-card" data-hlp>
        <div class="hlp-q" onclick="this.parentElement.classList.toggle('open')">
            I can't see a section I need — who do I ask?
            <i data-lucide="chevron-down" class="chev w-4 h-4"></i>
        </div>
        <div class="hlp-a">
            Access to each section is controlled by access keys assigned to your role. Ask an administrator to grant you the relevant access key from <b>Access Control → Access Routes</b>.
        </div>
    </div>

    <div class="hlp-contact">
        <h3>Still need help?</h3>
        <p>Contact your system administrator, or reach out at
            @if (\App\Models\Setting::get('store_email'))
                <a href="mailto:{{ \App\Models\Setting::get('store_email') }}">{{ \App\Models\Setting::get('store_email') }}</a>.
            @else
                the email on file for this business.
            @endif
        </p>
    </div>
</div>

<script>
    document.getElementById('hlpSearch').addEventListener('input', function (e) {
        const term = e.target.value.trim().toLowerCase();
        document.querySelectorAll('[data-hlp]').forEach(function (card) {
            const text = card.textContent.toLowerCase();
            card.style.display = (!term || text.includes(term)) ? '' : 'none';
        });
    });
</script>
@endsection
