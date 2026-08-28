{{-- resources/views/ecommerce/orders/queue.blade.php --}}
@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

<style>
/* Theme tokens come from the shared design system in layouts/app.blade.php. */
.eco-wrap{max-width:1280px;margin:0 auto;padding:16px;color:var(--foreground);}
.eco-top{display:flex;gap:12px;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;margin-bottom:14px;}
.eco-title{font-size:1.55rem;font-weight:900;display:flex;align-items:center;gap:10px;}
.eco-sub{color:var(--text-secondary);font-size:.95rem;margin-top:6px;max-width:760px;}
.eco-card{background:var(--card);border:1px solid var(--border-color);border-radius:var(--radius);padding:14px;box-shadow:var(--card-shadow);}
.eco-tableWrap{overflow:auto;border:1px solid var(--border-color);border-radius:var(--radius);}
.eco-table{width:100%;border-collapse:collapse;min-width:960px;}
.eco-table thead{background:var(--bg-tertiary);}
.eco-table th,.eco-table td{padding:12px;border-bottom:1px solid var(--border-color);text-align:left;vertical-align:middle;}
.eco-table tbody tr:hover{background:var(--bg-tertiary);}
.eco-mono{font-family:ui-monospace,Menlo,monospace;}
.eco-mini{color:var(--text-secondary);font-size:.85rem;}
.eco-badge{display:inline-flex;align-items:center;gap:6px;border:1px solid var(--border-color);padding:5px 10px;border-radius:999px;font-size:.78rem;font-weight:800;text-transform:capitalize;}
.eco-actions{display:flex;gap:6px;flex-wrap:wrap;}
.eco-btn{border:1px solid var(--border-color);padding:7px 11px;border-radius:calc(var(--radius) - 2px);cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:6px;background:transparent;color:var(--foreground);font-size:.8rem;font-weight:700;}
.eco-btn:hover{background:var(--bg-tertiary);border-color:var(--accent-color);}
.eco-btn-primary{background:var(--accent-color);border-color:transparent;color:var(--sidebar-primary-foreground);}
.eco-empty{text-align:center;padding:40px 20px;color:var(--text-secondary);}
.eco-rank{display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:999px;background:var(--bg-tertiary);font-weight:900;font-size:.78rem;flex-shrink:0;}
.eco-tags{display:flex;gap:6px;flex-wrap:wrap;}
.eco-tag{display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:999px;font-size:.72rem;font-weight:800;white-space:nowrap;}
.eco-tag-age{background:color-mix(in oklch, var(--accent-color) 22%, var(--card) 78%);}
.eco-tag-prepaid{background:color-mix(in oklch, var(--success) 25%, var(--card) 75%);}
.eco-tag-lowstock{background:color-mix(in oklch, var(--danger) 25%, var(--card) 75%);}
.eco-score{font-family:ui-monospace,Menlo,monospace;font-weight:800;}
</style>

<div class="eco-wrap">
    <div class="eco-top" data-reveal>
        <div>
            <div class="eco-title"><i class="fas fa-list-check"></i> Pick Queue</div>
            <div class="eco-sub">Online orders that are processing but not yet packaged, ranked by how urgently they should be picked next -- older orders, prepaid orders, and orders holding scarce stock rank higher.</div>
        </div>
        <a href="{{ route('ecommerce.orders.index') }}" class="eco-btn"><i class="fas fa-arrow-left"></i> All Online Orders</a>
    </div>

    <div class="eco-card" data-reveal>
        @if($orders->isEmpty())
            <div class="eco-empty"><i class="fas fa-circle-check" style="font-size:1.6rem;display:block;margin-bottom:8px;"></i> Nothing waiting to be packaged. Queue is clear.</div>
        @else
            <div class="eco-tableWrap">
                <table class="eco-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Priority</th>
                            <th>Score</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td><span class="eco-rank">{{ $loop->iteration }}</span></td>
                                <td>
                                    <a href="{{ route('orders.show', $order) }}" class="eco-mono"><b>{{ $order->order_no }}</b></a>
                                    @if($order->location)<div class="eco-mini">{{ $order->location->name }}</div>@endif
                                </td>
                                <td>
                                    <div>{{ $order->shipping_name ?: ($order->customer->name ?? 'Guest') }}</div>
                                    <div class="eco-mini">{{ $order->shipping_phone ?: ($order->customer->phone ?? '') }}</div>
                                </td>
                                <td>{{ $order->items->count() }}</td>
                                <td><b>{{ format_currency($order->payable_total) }}</b></td>
                                <td>
                                    <div class="eco-tags">
                                        <span class="eco-tag eco-tag-age"><i class="fas fa-clock"></i> {{ (int) floor($order->queue_age_hours) }}h waiting</span>
                                        @if($order->payment_status === 'paid')
                                            <span class="eco-tag eco-tag-prepaid"><i class="fas fa-money-bill-wave"></i> Prepaid</span>
                                        @endif
                                        @if($order->queue_has_scarce_item)
                                            <span class="eco-tag eco-tag-lowstock"><i class="fas fa-triangle-exclamation"></i> Low stock</span>
                                        @endif
                                    </div>
                                </td>
                                <td><span class="eco-score">{{ number_format($order->queue_score, 1) }}</span></td>
                                <td>
                                    <div class="eco-actions">
                                        <form method="POST" action="{{ route('ecommerce.orders.package', $order->id) }}">
                                            @csrf
                                            <button type="submit" class="eco-btn eco-btn-primary"><i class="fas fa-box"></i> Mark Packaged</button>
                                        </form>
                                        <a href="{{ route('orders.show', $order) }}" class="eco-btn"><i class="fas fa-eye"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
