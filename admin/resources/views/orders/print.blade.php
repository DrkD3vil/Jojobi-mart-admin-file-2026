@extends('layouts.app')

@section('title', 'Print Order')

@section('content')
<div class="container py-4">

<style>
    .inv {
        border: 1px solid var(--border-color, oklch(0.9 0 0));
        border-radius: calc(var(--radius, 0.625rem) + 6px);
        overflow: hidden;
        background: var(--card, oklch(0.205 0 0));
        box-shadow: var(--card-shadow, 0 2px 4px 0 rgb(0 0 0 / 0.25));
    }

    .inv-hd {
        padding: 16px 18px;
        border-bottom: 1px solid var(--border-color, oklch(0.9 0 0));
        display: flex;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        background: var(--accent, oklch(0.269 0 0));
    }

    .inv-hd h3 {
        margin: 0;
        font-weight: 900;
        color: var(--text-primary, oklch(0.985 0 0));
    }

    .muted {
        color: var(--text-muted, oklch(0.708 0 0 / 0.7));
        font-size: 13px;
    }

    .box {
        border: 1px solid var(--border-color, oklch(0.9 0 0));
        border-radius: calc(var(--radius, 0.625rem) - 2px);
        padding: 12px;
        background: var(--glass-base, rgba(255, 255, 255, 0.85));
    }

    .grid2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    @media (max-width: 768px) {
        .grid2 {
            grid-template-columns: 1fr;
        }
    }

    .kv {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 4px 0;
        font-size: 13px;
        border-bottom: 1px dashed var(--border-color, oklch(0.9 0 0));
    }

    .kv:last-child {
        border-bottom: none;
    }

    .kv span {
        color: var(--text-muted, oklch(0.708 0 0 / 0.7));
        font-weight: 600;
    }

    .kv b {
        font-variant-numeric: tabular-nums;
        color: var(--text-primary, oklch(0.985 0 0));
        font-weight: 800;
    }

    .tw {
        overflow: auto;
        border: 1px solid var(--border-color, oklch(0.9 0 0));
        border-radius: calc(var(--radius, 0.625rem) - 2px);
        background: var(--glass-base, rgba(255, 255, 255, 0.85));
    }

    table {
        width: 100%;
        border-collapse: collapse;
        min-width: 820px;
    }

    th, td {
        padding: 10px 12px;
        border-bottom: 1px solid var(--border-color, oklch(0.9 0 0));
        font-size: 13px;
        vertical-align: top;
        color: var(--text-primary, oklch(0.985 0 0));
    }

    th {
        background: var(--accent, oklch(0.269 0 0));
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: .35px;
        color: var(--text-muted, oklch(0.708 0 0 / 0.7));
        font-weight: 900;
    }

    tbody tr:hover {
        background: var(--accent-glow, rgba(37, 99, 235, 0.1));
    }

    tbody tr:last-child td {
        border-bottom: none;
    }

    .money {
        text-align: right;
        font-variant-numeric: tabular-nums;
        font-weight: 800;
    }

    .pill {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 999px;
        border: 1px solid var(--border-color, oklch(0.9 0 0));
        font-size: 12px;
        font-weight: 800;
        background: var(--accent, oklch(0.269 0 0));
        color: var(--text-primary, oklch(0.985 0 0));
    }

    .btns {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        padding: 14px 18px;
        border-top: 1px solid var(--border-color, oklch(0.9 0 0));
        background: var(--accent, oklch(0.269 0 0));
        flex-wrap: wrap;
    }

    .btnx {
        border: 1px solid var(--accent-color, oklch(0.488 0.243 264.376));
        background: var(--accent-color, oklch(0.488 0.243 264.376));
        color: var(--sidebar-primary-foreground, #fff);
        padding: 8px 12px;
        border-radius: 10px;
        font-weight: 800;
        cursor: pointer;
        transition: all var(--transition-fast, 150ms) ease;
    }

    .btnx:hover {
        background: var(--accent-hover, oklch(0.488 0.243 264.376 / 0.8));
        transform: translateY(-1px);
        box-shadow: 0 4px 12px var(--accent-glow, rgba(37, 99, 235, 0.2));
    }

    .btnx-ghost {
        background: var(--glass-base, rgba(255, 255, 255, 0.85));
        color: var(--text-primary, oklch(0.985 0 0));
        border: 1px solid var(--border-color, oklch(0.9 0 0));
    }

    .btnx-ghost:hover {
        background: var(--accent, oklch(0.269 0 0));
        border-color: var(--accent-color, oklch(0.488 0.243 264.376));
        transform: translateY(-1px);
    }

    /* Print styles */
    @media print {
        .no-print {
            display: none !important;
        }

        .container {
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .inv {
            border: none;
            box-shadow: none;
            background: white !important;
            color: black !important;
        }

        .inv-hd, .btns {
            background: #f9fafb !important;
            border-color: #e5e7eb !important;
        }

        th, td {
            border-color: #e5e7eb !important;
            color: black !important;
        }

        .muted, .kv span {
            color: #6b7280 !important;
        }
    }

    @media (max-width: 576px) {
        .inv-hd {
            padding: 12px 14px;
        }

        .btns {
            padding: 12px 14px;
            justify-content: center;
        }

        .btnx, .btnx-ghost {
            flex: 1;
            min-width: 140px;
            text-align: center;
        }
    }
</style>

<div class="inv">
    <div class="inv-hd">
        <div>
            <h3>Order #{{ $order->order_no ?? $order->id }}</h3>
            <div class="muted">
                Date: <b>{{ $order->created_at->format('d M Y, h:i A') }}</b>
                @if($order->location)
                    &bull; Location: <b>{{ $order->location->name }}</b>
                @endif
            </div>
        </div>

        <div class="no-print" style="display:flex; gap:10px; align-items:center;">
            <a class="btnx btnx-ghost" href="{{ route('orders.show', $order) }}">&larr; Back to Order</a>
            <button class="btnx" onclick="window.print()">Print</button>
        </div>
    </div>

    <div style="padding:16px 18px;">
        <div class="grid2">
            <div class="box">
                <div style="font-weight:900;margin-bottom:6px;">Customer</div>
                @if($order->customer)
                    <div class="kv"><span>Name</span><b>{{ $order->customer->name }}</b></div>
                    <div class="kv"><span>Phone</span><b>{{ $order->customer->phone ?? '-' }}</b></div>
                    @if($order->customer->address)
                        <div class="kv"><span>Address</span><b>{{ $order->customer->address }}</b></div>
                    @endif
                @else
                    <div class="kv"><span>Customer</span><b>Guest</b></div>
                @endif
                <div class="kv"><span>Payment Status</span><b>{{ strtoupper($order->payment_status ?? 'unpaid') }}</b></div>
                <div class="kv"><span>Order Status</span><b>{{ strtoupper($order->status ?? 'pending') }}</b></div>
            </div>

            <div class="box">
                <div style="font-weight:900;margin-bottom:6px;">Totals</div>
                <div class="kv"><span>Subtotal</span><b>{{ number_format((float) $order->subtotal, 2) }}</b></div>
                <div class="kv"><span>Discount Total</span><b>{{ number_format((float) $order->discount_total, 2) }}</b></div>
                <div class="kv"><span>Sale Payable</span><b>{{ number_format((float) $order->payable_total, 2) }}</b></div>
                <div class="kv"><span>Paid</span><b>{{ number_format((float) $order->paid_total, 2) }}</b></div>
                <div class="kv"><span>Due</span><b>{{ number_format((float) $order->due_total, 2) }}</b></div>
                <div class="kv"><span>Change/Advance</span><b>{{ number_format((float) $order->change_total, 2) }}</b></div>

                @if(!empty($order->payment_note))
                    <div class="muted" style="margin-top:8px;"><b>Note:</b> {{ $order->payment_note }}</div>
                @endif
            </div>
        </div>

        <div style="height:12px;"></div>

        <div class="tw">
            <table>
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Item</th>
                        <th style="width:120px;">Batch</th>
                        <th style="width:100px;">Type</th>
                        <th class="money" style="width:120px;">Unit</th>
                        <th class="money" style="width:90px;">Qty</th>
                        <th class="money" style="width:120px;">Discount</th>
                        <th class="money" style="width:140px;">Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->items as $k => $it)
                        <tr>
                            <td>{{ $k + 1 }}</td>
                            <td>
                                <div style="font-weight:900;">{{ $it->product_name }}</div>
                                <div class="muted">{{ $it->barcode ?? '' }}</div>
                            </td>
                            <td>{{ $it->batch?->batch_sku ?? '-' }}</td>
                            <td><span class="pill">{{ $it->price_type }}</span></td>
                            <td class="money">{{ number_format((float) $it->unit_price, 2) }}</td>
                            <td class="money">{{ number_format((float) $it->quantity, 2) }}</td>
                            <td class="money">{{ number_format((float) ($it->discount_amount ?? 0), 2) }}</td>
                            <td class="money"><b>{{ number_format((float) $it->total_price, 2) }}</b></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="muted" style="text-align:center; padding: 20px;">No items on this order.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="btns no-print">
        <a class="btnx btnx-ghost" href="{{ route('orders.index') }}">All Orders</a>
        <a class="btnx btnx-ghost" href="{{ route('invoice.show', $order) }}">View Invoice</a>
        <button class="btnx" onclick="window.print()">Print</button>
    </div>
</div>

</div>
@endsection
