{{-- resources/views/ecommerce/orders/index.blade.php --}}
@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

<style>
/* Theme tokens come from the shared design system in layouts/app.blade.php. */
.eco-wrap{max-width:1280px;margin:0 auto;padding:16px;color:var(--foreground);}
.eco-top{display:flex;gap:12px;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;margin-bottom:14px;}
.eco-title{font-size:1.55rem;font-weight:900;display:flex;align-items:center;gap:10px;}
.eco-sub{color:var(--text-secondary);font-size:.95rem;margin-top:6px;max-width:760px;}
.eco-tabs{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px;}
.eco-tab{border:1px solid var(--border-color);background:var(--card);padding:8px 14px;border-radius:999px;font-size:.85rem;font-weight:700;text-decoration:none;color:var(--foreground);display:inline-flex;align-items:center;gap:8px;}
.eco-tab.active{background:var(--accent-color);border-color:transparent;color:var(--sidebar-primary-foreground);}
.eco-tab .n{opacity:.75;font-family:ui-monospace,Menlo,monospace;}
.eco-card{background:var(--card);border:1px solid var(--border-color);border-radius:var(--radius);padding:14px;box-shadow:var(--card-shadow);}
.eco-tableWrap{overflow:auto;border:1px solid var(--border-color);border-radius:var(--radius);}
.eco-table{width:100%;border-collapse:collapse;min-width:900px;}
.eco-table thead{background:var(--bg-tertiary);}
.eco-table th,.eco-table td{padding:12px;border-bottom:1px solid var(--border-color);text-align:left;vertical-align:middle;}
.eco-table tbody tr:hover{background:var(--bg-tertiary);}
.eco-mono{font-family:ui-monospace,Menlo,monospace;}
.eco-mini{color:var(--text-secondary);font-size:.85rem;}
.eco-badge{display:inline-flex;align-items:center;gap:6px;border:1px solid var(--border-color);padding:5px 10px;border-radius:999px;font-size:.78rem;font-weight:800;text-transform:capitalize;}
.eco-pending{background:color-mix(in oklch, var(--warning) 30%, var(--card) 70%);}
.eco-processing{background:color-mix(in oklch, var(--accent-color) 30%, var(--card) 70%);}
.eco-completed{background:color-mix(in oklch, var(--success) 30%, var(--card) 70%);}
.eco-cancelled{background:color-mix(in oklch, var(--danger) 30%, var(--card) 70%);}
.eco-actions{display:flex;gap:6px;flex-wrap:wrap;}
.eco-btn{border:1px solid var(--border-color);padding:7px 11px;border-radius:calc(var(--radius) - 2px);cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:6px;background:transparent;color:var(--foreground);font-size:.8rem;font-weight:700;}
.eco-btn:hover{background:var(--bg-tertiary);border-color:var(--accent-color);}
.eco-btn-primary{background:var(--accent-color);border-color:transparent;color:var(--sidebar-primary-foreground);}
.eco-empty{text-align:center;padding:40px 20px;color:var(--text-secondary);}
.eco-packaged{background:color-mix(in oklch, var(--success) 25%, var(--card) 75%);}
.eco-unpackaged{color:var(--text-secondary);}
</style>

<div class="eco-wrap">
    <div class="eco-top" data-reveal>
        <div>
            <div class="eco-title"><i class="fas fa-globe"></i> Ecommerce Orders</div>
            <div class="eco-sub">Orders placed by customers through the online storefront. Process, complete, or cancel them the same way as till orders.</div>
        </div>
        <div class="eco-actions">
            <a href="{{ route('ecommerce.orders.queue') }}" class="eco-btn"><i class="fas fa-list-check"></i> Pick Queue</a>
            <a href="{{ route('ecommerce.dashboard') }}" class="eco-btn eco-btn-primary"><i class="fas fa-chart-line"></i> Dashboard</a>
        </div>
    </div>

    <div class="eco-tabs" data-reveal>
        <a href="{{ route('ecommerce.orders.index') }}" class="eco-tab {{ $status === '' ? 'active' : '' }}">All</a>
        <a href="{{ route('ecommerce.orders.index', ['status' => 'pending']) }}" class="eco-tab {{ $status === 'pending' ? 'active' : '' }}">
            <i class="fas fa-clock"></i> Pending <span class="n">{{ $counts['pending'] }}</span>
        </a>
        <a href="{{ route('ecommerce.orders.index', ['status' => 'processing']) }}" class="eco-tab {{ $status === 'processing' ? 'active' : '' }}">
            <i class="fas fa-spinner"></i> Processing <span class="n">{{ $counts['processing'] }}</span>
        </a>
        <a href="{{ route('ecommerce.orders.index', ['status' => 'completed']) }}" class="eco-tab {{ $status === 'completed' ? 'active' : '' }}">
            <i class="fas fa-badge-check"></i> Completed <span class="n">{{ $counts['completed'] }}</span>
        </a>
        <a href="{{ route('ecommerce.orders.index', ['status' => 'cancelled']) }}" class="eco-tab {{ $status === 'cancelled' ? 'active' : '' }}">
            <i class="fas fa-circle-xmark"></i> Cancelled <span class="n">{{ $counts['cancelled'] }}</span>
        </a>
    </div>

    <div class="eco-card" data-reveal>
        @if($orders->isEmpty())
            <div class="eco-empty"><i class="fas fa-inbox" style="font-size:1.6rem;display:block;margin-bottom:8px;"></i> No online orders here yet.</div>
        @else
            <div class="eco-tableWrap">
                <table class="eco-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Packaged</th>
                            <th>Placed</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('orders.show', $order) }}" class="eco-mono"><b>{{ $order->order_no }}</b></a>
                                    @if($order->location)<div class="eco-mini">{{ $order->location->name }}</div>@endif
                                </td>
                                <td>
                                    <div>{{ $order->shipping_name ?: ($order->customer->name ?? 'Guest') }}</div>
                                    <div class="eco-mini">{{ $order->shipping_phone ?: ($order->customer->phone ?? '') }}</div>
                                </td>
                                <td>{{ $order->items_count }}</td>
                                <td><b>{{ format_currency($order->payable_total) }}</b></td>
                                <td><span class="eco-badge eco-{{ $order->status }}">{{ $order->status }}</span></td>
                                <td>
                                    @if($order->packaged_at)
                                        <span class="eco-badge eco-packaged"><i class="fas fa-box"></i> {{ $order->packaged_at->format('d M, h:i A') }}</span>
                                    @else
                                        <span class="eco-mini eco-unpackaged">&mdash;</span>
                                    @endif
                                </td>
                                <td><span class="eco-mini">{{ $order->created_at->format('d M, h:i A') }}</span></td>
                                <td>
                                    <div class="eco-actions">
                                        @if($order->status === 'pending')
                                            <form method="POST" action="{{ route('orders.process', $order->id) }}">
                                                @csrf
                                                <button type="submit" class="eco-btn"><i class="fas fa-play"></i> Process</button>
                                            </form>
                                        @endif
                                        @if($order->channel === 'online' && $order->status === 'processing' && !$order->packaged_at)
                                            <form method="POST" action="{{ route('ecommerce.orders.package', $order->id) }}">
                                                @csrf
                                                <button type="submit" class="eco-btn"><i class="fas fa-box"></i> Mark Packaged</button>
                                            </form>
                                        @endif
                                        @if($order->status === 'processing')
                                            <form method="POST" action="{{ route('orders.complete', $order->id) }}">
                                                @csrf
                                                <button type="submit" class="eco-btn eco-btn-primary"><i class="fas fa-check"></i> Confirm</button>
                                            </form>
                                        @endif
                                        @if(in_array($order->status, ['pending', 'processing']))
                                            <a href="{{ route('orders.cancel.form', $order) }}" class="eco-btn"><i class="fas fa-xmark"></i> Cancel</a>
                                        @endif
                                        <a href="{{ route('orders.show', $order) }}" class="eco-btn"><i class="fas fa-eye"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top:12px;">{{ $orders->links() }}</div>
        @endif
    </div>
</div>
@endsection
