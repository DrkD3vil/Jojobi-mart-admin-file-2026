{{-- resources/views/orders/split-history.blade.php --}}
@extends('layouts.app')

@php
    if (!function_exists('currency_bdt')) {
        function currency_bdt($amount) {
            return '৳ ' . number_format((float) $amount, 2);
        }
    }
@endphp

@section('content')
<div class="container py-4">
    <style>
        :root {
            --background: oklch(0.145 0 0);
            --foreground: oklch(0.985 0 0);
            --card: oklch(0.205 0 0);
            --card-foreground: oklch(0.985 0 0);
            --primary: oklch(0.488 0.243 264.376);
            --secondary: oklch(0.269 0 0);
            --border: oklch(0.269 0 0);
            --success: oklch(0.696 0.17 162.48);
            --warning: oklch(0.769 0.188 70.08);
            --danger: oklch(0.704 0.191 22.216);
            --info: oklch(0.488 0.243 264.376);
            --radius: 0.625rem;
        }

        .cardx {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        .cardx-hd {
            padding: 14px 20px;
            border-bottom: 1px solid var(--border);
            background: var(--secondary);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .cardx-body {
            padding: 20px;
        }

        .badgex {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 12px;
        }

        .badgex-success {
            background: color-mix(in oklch, var(--success) 20%, transparent 80%);
            color: var(--success);
            border: 1px solid var(--success);
        }

        .badgex-warning {
            background: color-mix(in oklch, var(--warning) 20%, transparent 80%);
            color: var(--warning);
            border: 1px solid var(--warning);
        }

        .badgex-danger {
            background: color-mix(in oklch, var(--danger) 20%, transparent 80%);
            color: var(--danger);
            border: 1px solid var(--danger);
        }

        .badgex-info {
            background: color-mix(in oklch, var(--info) 20%, transparent 80%);
            color: var(--info);
            border: 1px solid var(--info);
        }

        .badgex-secondary {
            background: var(--secondary);
            color: var(--foreground);
            border: 1px solid var(--border);
        }

        .tablex {
            width: 100%;
            border-collapse: collapse;
        }

        .tablex th {
            background: var(--secondary);
            color: var(--muted-foreground);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            font-weight: 900;
            padding: 10px 12px;
            border-bottom: 1px solid var(--border);
        }

        .tablex td {
            padding: 10px 12px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .tablex tr:hover {
            background: color-mix(in oklch, var(--primary) 10%, transparent 90%);
        }

        .btnx {
            border: 1px solid transparent;
            padding: 6px 12px;
            border-radius: calc(var(--radius) - 4px);
            font-weight: 700;
            cursor: pointer;
            transition: all 150ms ease;
            font-size: 12px;
        }

        .btnx-primary {
            background: var(--primary);
            color: #fff;
        }

        .btnx-primary:hover {
            background: color-mix(in oklch, var(--primary) 80%, transparent 20%);
        }

        .btnx-warning {
            background: var(--warning);
            color: #000;
        }

        .btnx-warning:hover {
            background: color-mix(in oklch, var(--warning) 80%, transparent 20%);
        }

        .btnx-secondary {
            background: var(--secondary);
            color: var(--foreground);
            border: 1px solid var(--border);
        }

        .btnx-secondary:hover {
            background: color-mix(in oklch, var(--secondary) 80%, transparent 20%);
        }

        .btnx-danger {
            background: var(--danger);
            color: #fff;
        }

        .btnx-danger:hover {
            background: color-mix(in oklch, var(--danger) 80%, transparent 20%);
        }

        .btnx-sm {
            padding: 4px 10px;
            font-size: 11px;
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0">
                <i class="fas fa-history" style="color: var(--primary);"></i>
                Split History
            </h3>
            <small class="text-muted">Order #{{ $order->order_no }}</small>
        </div>
        <a href="{{ route('orders.show', $order) }}" class="btnx btnx-secondary">
            <i class="fas fa-arrow-left"></i> Back to Order
        </a>
    </div>

    <div class="cardx">
        <div class="cardx-hd">
            <span class="fw-bold">All Splits</span>
            <span class="badgex badgex-info">{{ $splits->count() }} split(s) found</span>
        </div>
        <div class="cardx-body">
            @if($splits->isEmpty())
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-code-branch fa-3x d-block mb-3" style="opacity:0.3;"></i>
                    <p>No split history found for this order.</p>
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table class="tablex">
                        <thead>
                            <tr>
                                <th>Split Date</th>
                                <th>Parent Order</th>
                                <th>Child Order</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($splits as $split)
                                @php
                                    $parent = $split->parentOrder;
                                    $child = $split->childOrder;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $split->split_at->format('Y-m-d') }}</strong>
                                        <div class="text-muted small">{{ $split->split_at->format('H:i') }}</div>
                                    </td>
                                    <td>
                                        <a href="{{ route('orders.show', $parent) }}" style="color: var(--primary);">
                                            #{{ $parent->order_no }}
                                        </a>
                                        @if($parent->id === $order->id)
                                            <span class="badgex badgex-success" style="font-size:10px;">Current</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('orders.show', $child) }}" style="color: var(--primary);">
                                            #{{ $child->order_no }}
                                        </a>
                                        @if($child->id === $order->id)
                                            <span class="badgex badgex-success" style="font-size:10px;">Current</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ currency_bdt($split->split_amount) }}</strong>
                                        <div class="text-muted small">
                                            {{ number_format(($split->split_amount / $split->originalOrder->payable_total * 100), 1) }}%
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badgex badgex-info">
                                            {{ str_replace('_', ' ', ucfirst($split->split_type)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($child->status === 'merged')
                                            <span class="badgex badgex-secondary">Merged</span>
                                        @elseif($child->status === 'completed')
                                            <span class="badgex badgex-success">Completed</span>
                                        @elseif($child->status === 'cancelled')
                                            <span class="badgex badgex-danger">Cancelled</span>
                                        @else
                                            <span class="badgex badgex-warning">{{ ucfirst($child->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($child->status !== 'merged' && $child->status !== 'completed' && $child->status !== 'cancelled')
                                            <form action="{{ route('orders.split.merge', [$split->parent_order_id, $split->child_order_id]) }}"
                                                  method="POST"
                                                  style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btnx btnx-warning btnx-sm"
                                                        onclick="return confirm('Merge this child order back to parent?')">
                                                    <i class="fas fa-merge"></i> Merge
                                                </button>
                                            </form>
                                        @endif

                                        @if($split->split_by)
                                            <div class="text-muted small mt-1">
                                                By: {{ $split->createdBy?->name ?? 'System' }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @if($order->isSplitParent())
        <div class="cardx mt-4">
            <div class="cardx-hd">
                <span class="fw-bold">Split Summary</span>
            </div>
            <div class="cardx-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <small class="text-muted d-block">Total Split Amount</small>
                        <strong>{{ currency_bdt($splits->sum('split_amount')) }}</strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Number of Children</small>
                        <strong>{{ $splits->count() }}</strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Active Children</small>
                        <strong>{{ $splits->filter(fn($s) => $s->childOrder->status !== 'merged')->count() }}</strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Merged Children</small>
                        <strong>{{ $splits->filter(fn($s) => $s->childOrder->status === 'merged')->count() }}</strong>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
