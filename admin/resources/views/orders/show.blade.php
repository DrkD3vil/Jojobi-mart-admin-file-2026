@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <style>
        :root {
            --order-primary: #3b82f6;
            --order-success: #22c55e;
            --order-warning: #eab308;
            --order-danger: #ef4444;
            --order-info: #8b5cf6;
            --order-bg: #f8fafc;
        }

        /* Order Information Page Styles */
        .order-wrap {
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px;
        }

        /* Header Section */
        .order-header {
            background: var(--card);
            border-radius: 16px;
            padding: 24px 32px;
            margin-bottom: 32px;
            border: 1px solid var(--border);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .order-title-section {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .order-title-section h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }

        .order-badge {
            padding: 6px 16px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .order-badge-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .order-badge-completed {
            background: #d1fae5;
            color: #065f46;
        }

        .order-badge-paid {
            background: #dbeafe;
            color: #1e40af;
        }

        .order-badge-refunded {
            background: #e0e7ff;
            color: #3730a3;
        }

        .order-badge-returned {
            background: #f3e8ff;
            color: #5b21b6;
        }

        .order-badge-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .order-action-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        /* Stats Grid */
        .order-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 32px;
        }

        .order-stat-card {
            background: var(--card);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: all 0.2s;
        }

        .order-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .order-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .order-stat-content {
            flex: 1;
        }

        .order-stat-label {
            font-size: 13px;
            color: var(--muted-foreground);
            margin-bottom: 4px;
        }

        .order-stat-value {
            font-size: 20px;
            font-weight: 700;
        }

        /* Main Content Grid */
        .order-content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }

        @media (max-width: 1024px) {
            .order-content-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Card Components */
        .order-card {
            background: var(--card);
            border-radius: 12px;
            border: 1px solid var(--border);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .order-card-header {
            padding: 16px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--muted);
        }

        .order-card-header h3 {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .order-card-body {
            padding: 24px;
        }

        /* Customer Info */
        .order-customer-info {
            display: flex;
            gap: 16px;
            align-items: flex-start;
        }

        .order-customer-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: var(--order-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 600;
            flex-shrink: 0;
        }

        .order-customer-details {
            flex: 1;
        }

        .order-customer-name {
            font-size: 18px;
            font-weight: 600;
            margin: 0 0 4px 0;
        }

        .order-customer-detail {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--muted-foreground);
            font-size: 14px;
            margin: 4px 0;
        }

        .order-customer-detail i {
            width: 16px;
        }

        /* Order Items Table */
        .order-items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .order-items-table th {
            text-align: left;
            padding: 12px 12px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--muted-foreground);
            background: var(--muted);
            border-bottom: 1px solid var(--border);
        }

        .order-items-table td {
            padding: 12px 12px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .order-items-table tr:hover td {
            background: var(--muted);
        }

        .order-item-product {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .order-item-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: var(--muted);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .order-item-name {
            font-weight: 500;
        }

        .order-item-sku {
            font-size: 12px;
            color: var(--muted-foreground);
        }

        .order-item-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .order-item-returned {
            background: #fee2e2;
            color: #991b1b;
        }

        .order-item-price {
            font-weight: 600;
        }

        .order-total-row td {
            font-weight: 700;
            border-top: 2px solid var(--border);
        }

        /* Timeline */
        .order-timeline {
            position: relative;
            padding-left: 32px;
        }

        .order-timeline::before {
            content: '';
            position: absolute;
            left: 8px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--border);
        }

        .order-timeline-item {
            position: relative;
            padding-bottom: 24px;
        }

        .order-timeline-item:last-child {
            padding-bottom: 0;
        }

        .order-timeline-icon {
            position: absolute;
            left: -24px;
            top: 0;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--card);
            border: 2px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .order-timeline-icon.created {
            border-color: var(--order-primary);
            color: var(--order-primary);
        }

        .order-timeline-icon.pending {
            border-color: var(--order-warning);
            color: var(--order-warning);
        }

        .order-timeline-icon.completed {
            border-color: var(--order-success);
            color: var(--order-success);
        }

        .order-timeline-icon.paid {
            border-color: var(--order-info);
            color: var(--order-info);
        }

        .order-timeline-icon.refunded {
            border-color: #8b5cf6;
            color: #8b5cf6;
        }

        .order-timeline-icon.returned {
            border-color: #6d28d9;
            color: #6d28d9;
        }

        .order-timeline-icon.cancelled {
            border-color: var(--order-danger);
            color: var(--order-danger);
        }

        .order-timeline-content {
            padding-top: 4px;
        }

        .order-timeline-title {
            font-weight: 600;
            margin: 0 0 2px 0;
        }

        .order-timeline-description {
            font-size: 14px;
            color: var(--muted-foreground);
            margin: 0;
        }

        .order-timeline-time {
            font-size: 12px;
            color: var(--muted-foreground);
            display: block;
            margin-top: 4px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .order-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .order-title-section {
                flex-wrap: wrap;
            }

            .order-stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .order-items-table {
                font-size: 13px;
            }

            .order-items-table th,
            .order-items-table td {
                padding: 8px;
            }
        }
    </style>

    <div class="order-wrap">
        <!-- Header -->
        <div class="order-header">
            <div class="order-title-section">
                <h1>
                    <i class="fas fa-shopping-cart" style="color: var(--order-primary);"></i>
                    Order #{{ $order->order_no ?? $order->id }}
                </h1>
                <span class="order-badge order-badge-{{ $order->status ?? 'pending' }}">
                    {{ ucfirst($order->status ?? 'Pending') }}
                </span>
                <span style="font-size: 14px; color: var(--muted-foreground);">
                    <i class="far fa-calendar-alt"></i>
                    {{ $order->created_at->format('M d, Y H:i') }}
                </span>
            </div>
            <div class="order-action-buttons">
                @if (in_array($order->status, ['pending', 'unpaid']))
                    <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                @endif
                @if ((float) ($order->due_total ?? 0) > 0 && in_array($order->status, ['pending', 'processing']))
                    <a href="{{ route('payments.create', $order) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-money-bill-wave"></i> Add Payment
                    </a>
                @endif
                <a href="{{ route('invoice.show', $order) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                    <i class="fas fa-file-invoice"></i> Invoice
                </a>
                <a href="{{ route('orders.print', $order) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                    <i class="fas fa-print"></i> Print
                </a>
                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="order-stats-grid">
            <div class="order-stat-card">
                <div class="order-stat-icon" style="background: #dbeafe; color: #3b82f6;">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="order-stat-content">
                    <div class="order-stat-label">Total Items</div>
                    <div class="order-stat-value">{{ $order->items->sum('quantity') }}</div>
                </div>
            </div>
            <div class="order-stat-card">
                <div class="order-stat-icon" style="background: #d1fae5; color: #22c55e;">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="order-stat-content">
                    <div class="order-stat-label">Total Amount</div>
                    <div class="order-stat-value">tk.{{ number_format($order->payable_total, 2) }}</div>
                </div>
            </div>
            <div class="order-stat-card">
                <div class="order-stat-icon" style="background: #fef3c7; color: #eab308;">
                    <i class="fas fa-tag"></i>
                </div>
                <div class="order-stat-content">
                    <div class="order-stat-label">Discount</div>
                    <div class="order-stat-value">tk.{{ number_format($order->discount_amount ?? 0, 2) }}</div>
                </div>
            </div>
            <div class="order-stat-card">
                <div class="order-stat-icon" style="background: #f3e8ff; color: #8b5cf6;">
                    <i class="fas fa-undo-alt"></i>
                </div>
                <div class="order-stat-content">
                    <div class="order-stat-label">Returned</div>
                    <div class="order-stat-value">{{ $exchangeReturn->sum() ?? 0 }}</div>
                </div>
            </div>
        </div>


        {{-- Add this to orders/show.blade.php in the appropriate section --}}
<div class="d-flex gap-2 flex-wrap mb-3">
    <!-- Existing buttons -->



    <style>
        /* Add these styles to your app.css for better integration */

:root {
    --split-primary: oklch(0.488 0.243 264.376);
    --split-success: oklch(0.696 0.17 162.48);
    --split-warning: oklch(0.769 0.188 70.08);
    --split-danger: oklch(0.704 0.191 22.216);
    --split-info: oklch(0.488 0.243 264.376);
}

/* Currency styling */
.currency-bdt {
    font-family: 'Tahoma', 'Arial', sans-serif;
}

.currency-bdt::before {
    content: '৳ ';
}

/* Split animation */
.split-animate {
    animation: splitPulse 0.6s ease-in-out;
}

@keyframes splitPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); background: var(--split-primary); }
    100% { transform: scale(1); }
}

/* Child order badge */
.child-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 8px;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    background: var(--split-info);
    color: white;
}

.child-badge::before {
    content: '🔀';
    font-size: 10px;
}

/* Split summary cards */
.split-summary-card {
    background: var(--secondary);
    border-radius: var(--radius);
    padding: 12px 16px;
    border: 1px solid var(--border);
    transition: all 0.2s ease;
}

.split-summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.split-summary-card .label {
    font-size: 11px;
    color: var(--muted-foreground);
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.split-summary-card .value {
    font-size: 18px;
    font-weight: 900;
    margin-top: 4px;
}
    </style>

    @if(!in_array($order->status, ['cancelled', 'merged']) && $order->items->isNotEmpty())
        <a href="{{ route('returns.wizard', ['order_id' => $order->id]) }}" class="btnx btnx-secondary">
            <i class="fas fa-undo-alt"></i> Return Items
        </a>
        <a href="{{ route('exchanges.create') }}" class="btnx btnx-secondary">
            <i class="fas fa-exchange-alt"></i> Exchange Items
        </a>
    @endif

    @if($order->status === 'pending')
        <form action="{{ route('orders.process', $order) }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btnx btnx-info">
                <i class="fas fa-play"></i> Start Processing
            </button>
        </form>
    @endif

    @if(in_array($order->status, ['pending', 'processing']))
        <a href="{{ route('orders.cancel.form', $order) }}" class="btnx btnx-danger">
            <i class="fas fa-times"></i> Cancel Order
        </a>
    @endif

    @if($order->status === 'processing')
        <form action="{{ route('orders.complete', $order) }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btnx btnx-success">
                <i class="fas fa-check"></i> Mark Completed
            </button>
        </form>
    @endif

    @if(in_array($order->status, ['completed', 'paid']))
        <form action="{{ route('orders.refund', $order) }}" method="POST" style="display: inline;"
              onsubmit="return confirm('Refund this order? Stock will be restored and captured payments marked refunded.');">
            @csrf
            <button type="submit" class="btnx btnx-danger">
                <i class="fas fa-undo"></i> Refund Order
            </button>
        </form>
    @endif

    @if($order->canSplit())
        <a href="{{ route('orders.split', $order) }}" class="btnx btnx-warning">
            <i class="fas fa-code-branch"></i> Split Order
        </a>
    @endif

    @if($order->isSplitParent())
        <a href="{{ route('orders.split.history', $order) }}" class="btnx btnx-info">
            <i class="fas fa-history"></i> Split History
        </a>
    @endif

    @if($order->isSplitChild() && $order->parentOrder)
        <a href="{{ route('orders.show', $order->parentOrder) }}" class="btnx btnx-secondary">
            <i class="fas fa-arrow-up"></i> View Parent Order
        </a>
    @endif
</div>

@if($order->childOrders()->count() > 0)
    <div class="cardx mb-3">
        <div class="cardx-hd">
            <span class="fw-bold">Child Orders</span>
            <span class="badgex badgex-info">{{ $order->childOrders()->count() }} child(s)</span>
        </div>
        <div class="cardx-body p-0">
            <div style="overflow-x: auto;">
                <table class="tablex">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Status</th>
                            <th class="text-right">Amount</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->childOrders()->where('split_status', 'split_child')->get() as $child)
                            <tr>
                                <td>
                                    <a href="{{ route('orders.show', $child) }}" style="color: var(--primary);">
                                        #{{ $child->order_no }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badgex {{ $child->status === 'completed' ? 'badgex-success' : ($child->status === 'cancelled' ? 'badgex-danger' : 'badgex-warning') }}">
                                        {{ ucfirst($child->status) }}
                                    </span>
                                </td>
                                <td class="text-right">{{ currency_bdt($child->payable_total) }}</td>
                                <td>{{ $child->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('orders.show', $child) }}" class="btnx btnx-primary btnx-sm">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

        <!-- Main Content -->
        <div class="order-content-grid">
            <!-- Left Column -->
            <div>
                <!-- Customer Information -->
                <div class="order-card">
                    <div class="order-card-header">
                        <h3><i class="fas fa-user" style="color: var(--order-primary);"></i> Customer Information</h3>
                    </div>
                    <div class="order-card-body">
                        <div class="order-customer-info">
                            <div class="order-customer-avatar">
                                {{ $order->customer?->name ? strtoupper(substr($order->customer->name, 0, 1)) : 'G' }}
                            </div>
                            <div class="order-customer-details">
                                <p class="order-customer-name">{{ $order->customer?->name ?? 'Guest Customer' }}</p>
                                @if ($order->customer)
                                    <div class="order-customer-detail">
                                        <i class="fas fa-phone"></i>
                                        {{ $order->customer->phone ?? 'N/A' }}
                                    </div>
                                    <div class="order-customer-detail">
                                        <i class="fas fa-envelope"></i>
                                        {{ $order->customer->email ?? 'N/A' }}
                                    </div>
                                    @if ($order->customer->address)
                                        <div class="order-customer-detail">
                                            <i class="fas fa-map-marker-alt"></i>
                                            {{ $order->customer->address }}
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="order-card">
                    <div class="order-card-header">
                        <h3><i class="fas fa-boxes" style="color: var(--order-primary);"></i> Order Items</h3>
                        <span style="font-size: 14px; color: var(--muted-foreground);">
                            {{ $order->items->count() }} items
                        </span>
                    </div>
                    <div class="order-card-body" style="padding: 0;">
                        <table class="order-items-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-right">Qty</th>
                                    <th class="text-right">Price</th>
                                    <th>Returned</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="order-item-product">
                                                <div class="order-item-icon" style="background: #e0e7ff; color: #4338ca;">
                                                    <i class="fas fa-cube"></i>
                                                </div>
                                                <div>
                                                    <div class="order-item-name">{{ $item->product_name }}</div>
                                                    <div class="order-item-sku">
                                                        SKU: {{ $item->barcode ?? 'N/A' }}
                                                        @if ($item->product_batch_id)
                                                            <span style="margin-left: 8px;">Batch:
                                                                #{{ $item->product_batch_id }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-right">{{ $item->quantity }}</td>
                                        <td class="order-item-price text-right">tk.{{ number_format($item->unit_price, 2) }}</td>
                                        <td>
                                            @if (isset($exchangeReturn[$item->id]))
                                                <span class="order-item-badge order-item-returned">
                                                    {{ $exchangeReturn[$item->id] }} returned
                                                </span>
                                            @else
                                                <span style="color: var(--muted-foreground); font-size: 13px;">-</span>
                                            @endif
                                        </td>
                                        <td class="order-item-price text-right">tk.{{ number_format($item->total_price, 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="order-total-row">
                                    <td colspan="4" style="text-align: right;">Subtotal</td>
                                    <td class="text-right">tk.{{ number_format($order->items->sum('total_price'), 2) }}</td>
                                </tr>
                                @if (($order->discount_amount ?? 0) > 0)
                                    <tr>
                                        <td colspan="4" style="text-align: right; color: var(--order-danger);">
                                            <i class="fas fa-tag"></i> Discount
                                        </td>
                                        <td class="text-right" style="color: var(--order-danger);">
                                            -tk.{{ number_format($order->discount_amount, 2) }}</td>
                                    </tr>
                                @endif
                                <tr class="order-total-row">
                                    <td colspan="4" style="text-align: right; font-size: 16px;">Grand Total</td>
                                    <td class="text-right" style="font-size: 18px; color: var(--order-primary);">
                                        tk.{{ number_format($order->payable_total, 2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div>
                <!-- Order Timeline -->
                <div class="order-card">
                    <div class="order-card-header">
                        <h3><i class="fas fa-clock" style="color: var(--order-primary);"></i> Order Timeline</h3>
                    </div>
                    <div class="order-card-body">
                        <div class="order-timeline">
                            @foreach ($timeline as $event)
                                <div class="order-timeline-item">
                                    <div class="order-timeline-icon {{ $event['type'] }}">
                                        <i class="fas fa-{{ $event['icon'] }}"></i>
                                    </div>
                                    <div class="order-timeline-content">
                                        <p class="order-timeline-title">{{ $event['title'] }}</p>
                                        <p class="order-timeline-description">{{ $event['description'] }}</p>
                                        <span class="order-timeline-time">
                                            <i class="far fa-clock"></i>
                                            {{ $event['time']->diffForHumans() }}
                                            ({{ $event['time']->format('M d, Y H:i') }})
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Exchange Information -->
                @if ($exchangeIssue->isNotEmpty())
                    <div class="order-card">
                        <div class="order-card-header">
                            <h3><i class="fas fa-exchange-alt" style="color: var(--order-info);"></i> Exchange Information
                            </h3>
                        </div>
                        <div class="order-card-body">
                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                <div style="font-size: 14px; color: var(--muted-foreground);">
                                    <i class="fas fa-arrow-right" style="color: var(--order-success);"></i>
                                    Issues: {{ $exchangeIssue->count() }} items
                                </div>
                                @foreach ($exchangeIssue as $issue)
                                    <div
                                        style="display: flex; justify-content: space-between; padding: 8px; background: var(--muted); border-radius: 6px;">
                                        <span>Product #{{ $issue->product_id }}</span>
                                        <span>{{ $issue->qty }} × tk.{{ number_format($issue->unit_price, 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Order Notes -->
                @if ($order->note)
                    <div class="order-card">
                        <div class="order-card-header">
                            <h3><i class="fas fa-sticky-note" style="color: var(--order-warning);"></i> Order Notes</h3>
                        </div>
                        <div class="order-card-body">
                            <p style="margin: 0; color: var(--foreground);">{{ $order->note }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Auto-print functionality (optional)
        document.addEventListener('DOMContentLoaded', function() {
            // Add any JavaScript enhancements here
            console.log('Order information loaded successfully');
        });
    </script>
@endsection
