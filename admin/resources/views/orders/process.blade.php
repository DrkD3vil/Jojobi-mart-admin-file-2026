@extends('layouts.app')

@section('content')
<div class="process-order-wrap">
    {{-- Header --}}
    <div class="process-header">
        <div class="process-header-left">
            <a href="{{ route('orders.show', $order) }}" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Order
            </a>
            <div class="process-title-section">
                <h1 class="process-title">Process Order</h1>
                <p class="process-subtitle">Review and process order #{{ $order->order_no ?? 'ORD-' . $order->id }}</p>
            </div>
        </div>
        <div class="process-header-right">
            <span class="order-status-badge status-{{ $order->status ?? 'pending' }}">
                <span class="status-dot"></span>
                {{ ucfirst($order->status ?? 'Pending') }}
            </span>
            <span class="payment-status-badge payment-{{ $order->payment_status ?? 'unpaid' }}">
                <i class="fas fa-credit-card"></i>
                {{ ucfirst($order->payment_status ?? 'Unpaid') }}
            </span>
        </div>
    </div>

    {{-- Status Timeline --}}
    <div class="process-timeline">
        <div class="timeline-step {{ in_array($order->status, ['pending', 'processing', 'completed']) ? 'completed' : '' }}">
            <div class="timeline-icon"><i class="fas fa-shopping-bag"></i></div>
            <div class="timeline-content">
                <div class="timeline-title">Order Created</div>
                <div class="timeline-date">{{ $order->created_at->format('M d, Y H:i') }}</div>
            </div>
        </div>
        <div class="timeline-connector {{ in_array($order->status, ['processing', 'completed']) ? 'active' : '' }}"></div>
        <div class="timeline-step {{ $order->status == 'processing' ? 'active' : ($order->status == 'completed' ? 'completed' : '') }}">
            <div class="timeline-icon"><i class="fas fa-cogs"></i></div>
            <div class="timeline-content">
                <div class="timeline-title">Processing</div>
                <div class="timeline-date">{{ $order->status == 'processing' ? 'In Progress' : ($order->status == 'completed' ? 'Completed' : 'Pending') }}</div>
            </div>
        </div>
        <div class="timeline-connector {{ $order->status == 'completed' ? 'active' : '' }}"></div>
        <div class="timeline-step {{ $order->status == 'completed' ? 'completed' : '' }}">
            <div class="timeline-icon"><i class="fas fa-check-circle"></i></div>
            <div class="timeline-content">
                <div class="timeline-title">Completed</div>
                <div class="timeline-date">{{ $order->status == 'completed' ? 'Done' : 'Pending' }}</div>
            </div>
        </div>
    </div>

    <div class="process-grid">
        {{-- Left Column - Order Details --}}
        <div class="process-left">
            {{-- Customer Information --}}
            <div class="process-card">
                <div class="card-header">
                    <h3><i class="fas fa-user"></i> Customer Information</h3>
                </div>
                <div class="card-body">
                    <div class="customer-info">
                        <div class="customer-avatar">
                            {{ strtoupper(substr($order->customer?->name ?? 'G', 0, 1)) }}
                        </div>
                        <div class="customer-details">
                            <h4>{{ $order->customer?->name ?? 'Guest' }}</h4>
                            <p><i class="fas fa-phone"></i> {{ $order->customer?->phone ?? 'N/A' }}</p>
                            <p><i class="fas fa-envelope"></i> {{ $order->customer?->email ?? 'N/A' }}</p>
                            <p><i class="fas fa-map-marker-alt"></i> {{ $order->customer?->address ?? 'No address' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Order Items --}}
            <div class="process-card">
                <div class="card-header">
                    <h3><i class="fas fa-shopping-cart"></i> Order Items</h3>
                    <span class="item-count">{{ $order->items->count() }} items</span>
                </div>
                <div class="card-body">
                    <div class="items-table-wrap">
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th class="text-right">Price</th>
                                    <th class="text-right">Qty</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="item-name">{{ $item->product_name }}</div>
                                            @if($item->barcode)
                                                <div class="item-barcode"><i class="fas fa-barcode"></i> {{ $item->barcode }}</div>
                                            @endif
                                        </td>
                                        <td class="text-right">BDT {{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-right">{{ $item->quantity }}</td>
                                        <td class="text-right"><strong>BDT {{ number_format($item->total_price, 2) }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-right"><strong>Subtotal:</strong></td>
                                    <td class="text-right"><strong>BDT {{ number_format($order->subtotal, 2) }}</strong></td>
                                </tr>
                                @if($order->discount_total > 0)
                                    <tr>
                                        <td colspan="4" class="text-right"><span style="color: #10B981;">Discount:</span></td>
                                        <td class="text-right"><span style="color: #10B981;">- BDT {{ number_format($order->discount_total, 2) }}</span></td>
                                    </tr>
                                @endif
                                @if($order->rewards_amount_used > 0)
                                    <tr>
                                        <td colspan="4" class="text-right"><span style="color: #8B5CF6;">Rewards Used:</span></td>
                                        <td class="text-right"><span style="color: #8B5CF6;">- BDT {{ number_format($order->rewards_amount_used, 2) }}</span></td>
                                    </tr>
                                @endif
                                <tr class="total-row">
                                    <td colspan="4" class="text-right"><strong>Total Payable:</strong></td>
                                    <td class="text-right"><strong class="total-amount">BDT {{ number_format($order->payable_total, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Payment Summary --}}
            <div class="process-card">
                <div class="card-header">
                    <h3><i class="fas fa-credit-card"></i> Payment Summary</h3>
                </div>
                <div class="card-body">
                    <div class="payment-summary">
                        <div class="payment-item">
                            <span class="payment-label">Total Payable</span>
                            <span class="payment-value">BDT {{ number_format($order->payable_total, 2) }}</span>
                        </div>
                        <div class="payment-item">
                            <span class="payment-label">Paid Amount</span>
                            <span class="payment-value paid">BDT {{ number_format($order->paid_total ?? 0, 2) }}</span>
                        </div>
                        <div class="payment-item">
                            <span class="payment-label">Due Amount</span>
                            <span class="payment-value due">BDT {{ number_format($order->due_total ?? $order->payable_total, 2) }}</span>
                        </div>
                        @if($order->change_total > 0)
                            <div class="payment-item">
                                <span class="payment-label">Change</span>
                                <span class="payment-value change">BDT {{ number_format($order->change_total, 2) }}</span>
                            </div>
                        @endif
                        <div class="payment-item">
                            <span class="payment-label">Payment Status</span>
                            <span class="payment-status-badge payment-{{ $order->payment_status ?? 'unpaid' }}">
                                {{ ucfirst($order->payment_status ?? 'Unpaid') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column - Actions --}}
        <div class="process-right">
            {{-- Order Summary --}}
            <div class="process-card summary-card">
                <div class="card-header">
                    <h3><i class="fas fa-file-alt"></i> Order Summary</h3>
                </div>
                <div class="card-body">
                    <div class="summary-grid">
                        <div class="summary-item">
                            <span class="summary-label">Order ID</span>
                            <span class="summary-value">#{{ $order->id }}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Order No</span>
                            <span class="summary-value">{{ $order->order_no ?? 'N/A' }}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Status</span>
                            <span class="order-status-badge status-{{ $order->status ?? 'pending' }}" style="font-size: 0.7rem;">
                                <span class="status-dot"></span>
                                {{ ucfirst($order->status ?? 'Pending') }}
                            </span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Payment</span>
                            <span class="payment-status-badge payment-{{ $order->payment_status ?? 'unpaid' }}" style="font-size: 0.65rem;">
                                {{ ucfirst($order->payment_status ?? 'Unpaid') }}
                            </span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Created</span>
                            <span class="summary-value">{{ $order->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Total Items</span>
                            <span class="summary-value">{{ $order->items->count() }}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Total Amount</span>
                            <span class="summary-value total">BDT {{ number_format($order->payable_total, 2) }}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Paid</span>
                            <span class="summary-value paid">BDT {{ number_format($order->paid_total ?? 0, 2) }}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Due</span>
                            <span class="summary-value due">BDT {{ number_format($order->due_total ?? $order->payable_total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="process-card actions-card">
                <div class="card-header">
                    <h3><i class="fas fa-tasks"></i> Actions</h3>
                </div>
                <div class="card-body">
                    @php
                        $currentStatus = $order->status ?? 'pending';
                        $paymentStatus = $order->payment_status ?? 'unpaid';
                    @endphp

                    @if($currentStatus === 'pending')
                        <div class="action-group">
                            <form action="{{ route('orders.process', $order->id) }}" method="POST" class="action-form">
                                @csrf
                                <button type="submit" class="action-btn primary full-width"
                                        onclick="return confirm('Are you sure you want to process this order?')">
                                    <i class="fas fa-play"></i> Process Order
                                </button>
                            </form>
                            <div class="action-divider">or</div>
                            <div class="action-grid">
                                <form action="{{ route('orders.cancel', $order->id) }}" method="POST" class="action-form">
                                    @csrf
                                    <button type="submit" class="action-btn danger full-width"
                                            onclick="return confirm('Are you sure you want to cancel this order?')">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                </form>
                                <a href="{{ route('payments.create', $order) }}" class="action-btn info full-width">
                                    <i class="fas fa-credit-card"></i> Add Payment
                                </a>
                            </div>
                        </div>
                    @elseif($currentStatus === 'processing')
                        <div class="action-group">
                            <form action="{{ route('orders.complete', $order->id) }}" method="POST" class="action-form">
                                @csrf
                                <button type="submit" class="action-btn success full-width"
                                        onclick="return confirm('Are you sure you want to mark this order as completed?')">
                                    <i class="fas fa-check-circle"></i> Complete Order
                                </button>
                            </form>
                            <div class="action-divider">or</div>
                            <div class="action-grid">
                                <form action="{{ route('orders.cancel', $order->id) }}" method="POST" class="action-form">
                                    @csrf
                                    <button type="submit" class="action-btn danger full-width"
                                            onclick="return confirm('Are you sure you want to cancel this order?')">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                </form>
                                <a href="{{ route('payments.create', $order) }}" class="action-btn info full-width">
                                    <i class="fas fa-credit-card"></i> Add Payment
                                </a>
                            </div>
                        </div>
                    @elseif($currentStatus === 'completed')
                        <div class="action-group">
                            <div class="action-completed">
                                <div class="completed-icon"><i class="fas fa-check-circle"></i></div>
                                <h4>Order Completed</h4>
                                <p>This order has been completed successfully.</p>
                            </div>
                            <div class="action-grid">
                                <a href="{{ route('invoice.show', $order) }}" target="_blank" class="action-btn info full-width">
                                    <i class="fas fa-file-invoice"></i> View Invoice
                                </a>
                                <a href="{{ route('orders.print', $order->id) }}" target="_blank" class="action-btn primary full-width">
                                    <i class="fas fa-print"></i> Print
                                </a>
                                @if($order->payment_status !== 'refunded')
                                    <form action="{{ route('orders.refund', $order->id) }}" method="POST" class="action-form full-width">
                                        @csrf
                                        <div class="refund-input-group">
                                            <input type="number" name="amount" class="refund-input"
                                                   placeholder="Refund Amount" step="0.01"
                                                   max="{{ $order->payable_total }}" required>
                                            <button type="submit" class="action-btn warning full-width"
                                                    onclick="return confirm('Are you sure you want to refund this order?')">
                                                <i class="fas fa-undo-alt"></i> Refund
                                            </button>
                                        </div>
                                        <div class="refund-reason">
                                            <textarea name="reason" class="refund-textarea"
                                                      placeholder="Reason for refund (optional)" rows="2"></textarea>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @elseif($currentStatus === 'cancelled')
                        <div class="action-group">
                            <div class="action-cancelled">
                                <div class="cancelled-icon"><i class="fas fa-times-circle"></i></div>
                                <h4>Order Cancelled</h4>
                                @if($order->delete_reason)
                                    <p><strong>Reason:</strong> {{ $order->delete_reason }}</p>
                                @endif
                            </div>
                            <div class="action-grid">
                                <form action="{{ route('orders.restore', $order->id) }}" method="POST" class="action-form">
                                    @csrf
                                    <button type="submit" class="action-btn primary full-width"
                                            onclick="return confirm('Are you sure you want to restore this order?')">
                                        <i class="fas fa-undo"></i> Restore Order
                                    </button>
                                </form>
                                <a href="{{ route('orders.show', $order) }}" class="action-btn info full-width">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                            </div>
                        </div>
                    @elseif($currentStatus === 'refunded')
                        <div class="action-group">
                            <div class="action-refunded">
                                <div class="refunded-icon"><i class="fas fa-undo-alt"></i></div>
                                <h4>Order Refunded</h4>
                                @if($order->delete_reason)
                                    <p><strong>Reason:</strong> {{ $order->delete_reason }}</p>
                                @endif
                            </div>
                            <div class="action-grid">
                                <form action="{{ route('orders.restore', $order->id) }}" method="POST" class="action-form">
                                    @csrf
                                    <button type="submit" class="action-btn primary full-width"
                                            onclick="return confirm('Are you sure you want to restore this order?')">
                                        <i class="fas fa-undo"></i> Restore Order
                                    </button>
                                </form>
                                <a href="{{ route('orders.show', $order) }}" class="action-btn info full-width">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Additional Actions --}}
                    @if(!in_array($currentStatus, ['cancelled', 'refunded']))
                        <div class="action-divider">more actions</div>
                        <div class="action-grid">
                            <form action="{{ route('orders.trash.move', $order->id) }}" method="POST" class="action-form">
                                @csrf
                                <button type="submit" class="action-btn danger outline full-width"
                                        onclick="return confirm('Are you sure you want to move this order to trash?')">
                                    <i class="fas fa-trash"></i> Move to Trash
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="process-card stats-card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-bar"></i> Quick Stats</h3>
                </div>
                <div class="card-body">
                    <div class="stats-grid">
                        <div class="stat-item">
                            <span class="stat-icon">📦</span>
                            <div class="stat-info">
                                <span class="stat-number">{{ $order->items->count() }}</span>
                                <span class="stat-label">Items</span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <span class="stat-icon">💰</span>
                            <div class="stat-info">
                                <span class="stat-number">BDT {{ number_format($order->payable_total, 2) }}</span>
                                <span class="stat-label">Total</span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <span class="stat-icon">🕐</span>
                            <div class="stat-info">
                                <span class="stat-number">{{ $order->created_at->diffForHumans() }}</span>
                                <span class="stat-label">Created</span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <span class="stat-icon">💳</span>
                            <div class="stat-info">
                                <span class="stat-number">{{ ucfirst($order->payment_status ?? 'Unpaid') }}</span>
                                <span class="stat-label">Payment</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .process-order-wrap {
        padding: 1.5rem 2rem;
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Header */
    .process-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .process-header-left {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 0.875rem;
        transition: color 0.3s;
    }

    .back-btn:hover {
        color: var(--text-primary);
    }

    .process-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }

    .process-subtitle {
        font-size: 0.95rem;
        color: var(--text-secondary);
        margin: 0;
    }

    .process-header-right {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    /* Status Badges */
    .order-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.85rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
    }

    .order-status-badge.status-pending {
        background: #FEF3C7;
        color: #92400E;
    }
    .order-status-badge.status-pending .status-dot {
        background: #F59E0B;
    }

    .order-status-badge.status-processing {
        background: #E0E7FF;
        color: #3730A3;
    }
    .order-status-badge.status-processing .status-dot {
        background: #6366F1;
    }

    .order-status-badge.status-completed {
        background: #D1FAE5;
        color: #065F46;
    }
    .order-status-badge.status-completed .status-dot {
        background: #10B981;
    }

    .order-status-badge.status-cancelled {
        background: #FEE2E2;
        color: #991B1B;
    }
    .order-status-badge.status-cancelled .status-dot {
        background: #EF4444;
    }

    .order-status-badge.status-refunded {
        background: #FCE7F3;
        color: #831843;
    }
    .order-status-badge.status-refunded .status-dot {
        background: #EC4899;
    }

    .payment-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.85rem;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .payment-status-badge.payment-paid {
        background: #D1FAE5;
        color: #065F46;
    }

    .payment-status-badge.payment-unpaid {
        background: #FEE2E2;
        color: #991B1B;
    }

    .payment-status-badge.payment-refunded {
        background: #FCE7F3;
        color: #831843;
    }

    .payment-status-badge.payment-partial {
        background: #FEF3C7;
        color: #92400E;
    }

    /* Timeline */
    .process-timeline {
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--bg-secondary);
        padding: 1.5rem 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        border: 1px solid var(--border-color);
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .timeline-step {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        opacity: 0.4;
        transition: all 0.4s;
        padding: 0.5rem 1rem;
        border-radius: 8px;
    }

    .timeline-step.active {
        opacity: 1;
        background: rgba(79, 70, 229, 0.08);
    }

    .timeline-step.completed {
        opacity: 1;
        color: #10B981;
    }

    .timeline-step.completed .timeline-icon {
        background: #10B981;
        color: white;
    }

    .timeline-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--bg-tertiary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: var(--text-secondary);
        transition: all 0.4s;
        flex-shrink: 0;
    }

    .timeline-step.active .timeline-icon {
        background: linear-gradient(135deg, #4F46E5, #7C3AED);
        color: white;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }

    .timeline-step.completed .timeline-icon {
        background: #10B981;
        color: white;
    }

    .timeline-connector {
        width: 40px;
        height: 2px;
        background: var(--border-color);
        flex-shrink: 0;
        transition: background 0.4s;
    }

    .timeline-connector.active {
        background: #4F46E5;
    }

    .timeline-content .timeline-title {
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--text-primary);
    }

    .timeline-content .timeline-date {
        font-size: 0.7rem;
        color: var(--text-muted);
    }

    /* Grid */
    .process-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 1.5rem;
    }

    /* Cards */
    .process-card {
        background: var(--bg-secondary);
        border-radius: 12px;
        border: 1px solid var(--border-color);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .card-header {
        padding: 1rem 1.25rem;
        background: var(--bg-tertiary);
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header h3 {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .card-header .item-count {
        font-size: 0.75rem;
        color: var(--text-muted);
        padding: 0.15rem 0.5rem;
        background: var(--bg-secondary);
        border-radius: 999px;
    }

    .card-body {
        padding: 1.25rem;
    }

    /* Customer Info */
    .customer-info {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }

    .customer-avatar {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        background: linear-gradient(135deg, #4F46E5, #7C3AED);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        font-weight: 700;
        flex-shrink: 0;
    }

    .customer-details h4 {
        margin: 0 0 0.25rem 0;
        font-size: 1rem;
        color: var(--text-primary);
    }

    .customer-details p {
        margin: 0.15rem 0;
        font-size: 0.85rem;
        color: var(--text-secondary);
    }

    .customer-details p i {
        width: 16px;
        color: var(--text-muted);
    }

    /* Items Table */
    .items-table-wrap {
        overflow-x: auto;
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
    }

    .items-table th {
        padding: 0.5rem 0.75rem;
        text-align: left;
        font-size: 0.7rem;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid var(--border-color);
    }

    .items-table td {
        padding: 0.5rem 0.75rem;
        border-bottom: 1px solid var(--border-color);
    }

    .items-table tfoot td {
        padding: 0.5rem 0.75rem;
        border-top: 1px solid var(--border-color);
        font-weight: 500;
    }

    .items-table .total-row td {
        border-top: 2px solid var(--border-color);
        font-weight: 700;
        font-size: 0.95rem;
    }

    .items-table .total-amount {
        color: #4F46E5;
        font-size: 1.1rem;
    }

    .item-name {
        font-weight: 500;
        color: var(--text-primary);
    }

    .item-barcode {
        font-size: 0.7rem;
        color: var(--text-muted);
    }

    .text-right {
        text-align: right;
    }

    /* Payment Summary */
    .payment-summary {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
    }

    .payment-item {
        display: flex;
        justify-content: space-between;
        padding: 0.4rem 0;
        border-bottom: 1px solid var(--border-color);
    }

    .payment-item:last-child {
        border-bottom: none;
    }

    .payment-label {
        color: var(--text-secondary);
        font-size: 0.85rem;
    }

    .payment-value {
        font-weight: 600;
        font-size: 0.85rem;
    }

    .payment-value.paid {
        color: #10B981;
    }

    .payment-value.due {
        color: #EF4444;
    }

    .payment-value.change {
        color: #3B82F6;
    }

    /* Summary Grid */
    .summary-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
    }

    .summary-item {
        display: flex;
        flex-direction: column;
        gap: 0.1rem;
    }

    .summary-label {
        font-size: 0.65rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .summary-value {
        font-size: 0.85rem;
        font-weight: 500;
        color: var(--text-primary);
    }

    .summary-value.total {
        font-size: 1rem;
        font-weight: 700;
        color: #4F46E5;
    }

    .summary-value.paid {
        color: #10B981;
    }

    .summary-value.due {
        color: #EF4444;
    }

    /* Action Buttons */
    .action-group {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .action-form {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .action-form.full-width {
        width: 100%;
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.6rem 1.25rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        min-height: 42px;
    }

    .action-btn.full-width {
        width: 100%;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .action-btn:active {
        transform: scale(0.97);
    }

    .action-btn.primary {
        background: linear-gradient(135deg, #4F46E5, #7C3AED);
        color: white;
    }

    .action-btn.success {
        background: linear-gradient(135deg, #10B981, #059669);
        color: white;
    }

    .action-btn.danger {
        background: linear-gradient(135deg, #EF4444, #DC2626);
        color: white;
    }

    .action-btn.danger.outline {
        background: transparent;
        color: #EF4444;
        border: 1px solid #EF4444;
    }

    .action-btn.danger.outline:hover {
        background: #FEE2E2;
        color: #991B1B;
        border-color: #991B1B;
    }

    .action-btn.info {
        background: linear-gradient(135deg, #3B82F6, #2563EB);
        color: white;
    }

    .action-btn.warning {
        background: linear-gradient(135deg, #F59E0B, #D97706);
        color: white;
    }

    .action-divider {
        text-align: center;
        font-size: 0.7rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        position: relative;
    }

    .action-divider::before,
    .action-divider::after {
        content: '';
        position: absolute;
        top: 50%;
        width: 30%;
        height: 1px;
        background: var(--border-color);
    }

    .action-divider::before {
        left: 0;
    }

    .action-divider::after {
        right: 0;
    }

    .action-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
    }

    .action-completed,
    .action-cancelled,
    .action-refunded {
        text-align: center;
        padding: 0.5rem;
    }

    .completed-icon,
    .cancelled-icon,
    .refunded-icon {
        font-size: 2.5rem;
        display: block;
        margin-bottom: 0.5rem;
    }

    .completed-icon {
        color: #10B981;
    }

    .cancelled-icon {
        color: #EF4444;
    }

    .refunded-icon {
        color: #F59E0B;
    }

    .action-completed h4,
    .action-cancelled h4,
    .action-refunded h4 {
        margin: 0 0 0.25rem 0;
        color: var(--text-primary);
    }

    .action-completed p,
    .action-cancelled p,
    .action-refunded p {
        margin: 0;
        font-size: 0.85rem;
        color: var(--text-secondary);
    }

    /* Refund Input */
    .refund-input-group {
        display: flex;
        gap: 0.5rem;
        width: 100%;
    }

    .refund-input {
        flex: 1;
        padding: 0.6rem 0.85rem;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        background: var(--input);
        color: var(--text-primary);
        font-size: 0.85rem;
        min-height: 42px;
    }

    .refund-input:focus {
        outline: none;
        border-color: #4F46E5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .refund-textarea {
        width: 100%;
        padding: 0.6rem 0.85rem;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        background: var(--input);
        color: var(--text-primary);
        font-size: 0.85rem;
        resize: vertical;
    }

    .refund-textarea:focus {
        outline: none;
        border-color: #4F46E5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    /* Stats Card */
    .stats-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: var(--bg-tertiary);
        border-radius: 8px;
    }

    .stat-icon {
        font-size: 1.5rem;
        line-height: 1;
    }

    .stat-info {
        display: flex;
        flex-direction: column;
    }

    .stat-number {
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--text-primary);
    }

    .stat-label {
        font-size: 0.65rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .process-grid {
            grid-template-columns: 1fr;
        }

        .process-order-wrap {
            padding: 1rem;
        }
    }

    @media (max-width: 768px) {
        .process-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .process-header-right {
            width: 100%;
            justify-content: flex-start;
        }

        .process-timeline {
            padding: 1rem;
            gap: 0.25rem;
        }

        .timeline-step {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }

        .timeline-icon {
            width: 32px;
            height: 32px;
            font-size: 0.8rem;
        }

        .timeline-connector {
            width: 20px;
        }

        .summary-grid {
            grid-template-columns: 1fr;
        }

        .payment-summary {
            grid-template-columns: 1fr;
        }

        .action-grid {
            grid-template-columns: 1fr;
        }

        .refund-input-group {
            flex-direction: column;
        }

        .stats-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 480px) {
        .process-order-wrap {
            padding: 0.5rem;
        }

        .process-title {
            font-size: 1.25rem;
        }

        .customer-info {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .items-table {
            font-size: 0.75rem;
        }
    }
</style>
@endsection
