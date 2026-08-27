@extends('layouts.app')

@section('content')
<style>
    .co-wrap { max-width: 720px; margin: 0 auto; padding: 24px; }

    .co-header { margin-bottom: 20px; }
    .co-header h1 { font-size: 22px; font-weight: 600; color: var(--text-primary); margin: 0 0 4px 0; }
    .co-header p { color: var(--text-secondary); font-size: 14px; margin: 0; }

    .co-warning {
        display: flex; gap: 12px; align-items: flex-start;
        background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.3);
        border-radius: 10px; padding: 14px 16px; margin-bottom: 20px; color: var(--text-primary);
    }
    .co-warning i { color: #ef4444; margin-top: 2px; }
    .co-warning strong { display: block; margin-bottom: 4px; }
    .co-warning span { font-size: 13px; color: var(--text-secondary); }

    .co-card {
        background: var(--card); border: 1px solid var(--border-color); border-radius: 12px;
        overflow: hidden; margin-bottom: 20px; box-shadow: var(--card-shadow);
    }
    .co-card-header { padding: 14px 18px; border-bottom: 1px solid var(--border-color); font-weight: 600; color: var(--text-primary); font-size: 14px; }
    .co-card-body { padding: 18px; }

    .co-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; }
    .co-row .label { color: var(--text-secondary); }
    .co-row .value { color: var(--text-primary); font-weight: 500; }

    .co-items { width: 100%; border-collapse: collapse; font-size: 14px; margin-top: 8px; }
    .co-items th { text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: .03em; color: var(--text-secondary); padding: 6px 0; border-bottom: 1px solid var(--border-color); }
    .co-items td { padding: 8px 0; border-bottom: 1px solid var(--border-color); color: var(--text-primary); }
    .co-items td.num, .co-items th.num { text-align: right; }

    .co-total-row { display: flex; justify-content: space-between; padding-top: 12px; margin-top: 4px; border-top: 1px solid var(--border-color); font-size: 16px; font-weight: 600; color: var(--text-primary); }

    .co-field { margin-bottom: 18px; }
    .co-field label { display: block; font-size: 13px; font-weight: 600; color: var(--text-primary); margin-bottom: 6px; }
    .co-field textarea {
        width: 100%; min-height: 90px; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--border-color);
        background: var(--bg-secondary); color: var(--text-primary); font-size: 14px; font-family: inherit; resize: vertical;
    }

    .co-actions { display: flex; gap: 10px; justify-content: flex-end; }
    .co-btn { padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; border: 1px solid var(--border-color); cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
    .co-btn-ghost { background: var(--card); color: var(--text-primary); }
    .co-btn-ghost:hover { box-shadow: var(--card-shadow-hover); }
    .co-btn-danger { background: #ef4444; color: #fff; border-color: transparent; }
    .co-btn-danger:hover { background: #dc2626; }
</style>

<div class="co-wrap">
    <div class="co-header">
        <h1>Cancel Order #{{ $order->order_no }}</h1>
        <p>Review the order below before confirming the cancellation.</p>
    </div>

    <div class="co-warning">
        <i class="fas fa-triangle-exclamation"></i>
        <div>
            <strong>This will restore stock and void payments</strong>
            <span>Any stock reserved for this order is returned to inventory, and any already-captured payment on it is voided. This cannot be undone from here — a cancelled order can't be reopened.</span>
        </div>
    </div>

    <div class="co-card">
        <div class="co-card-header">Order Summary</div>
        <div class="co-card-body">
            <div class="co-row"><span class="label">Customer</span><span class="value">{{ $order->customer?->name ?? 'Guest' }}</span></div>
            <div class="co-row"><span class="label">Location</span><span class="value">{{ $order->location?->name ?? '—' }}</span></div>
            <div class="co-row"><span class="label">Status</span><span class="value">{{ ucfirst($order->status) }}</span></div>
            <div class="co-row"><span class="label">Placed</span><span class="value">{{ $order->created_at->format('M d, Y H:i') }}</span></div>

            <table class="co-items">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th class="num">Qty</th>
                        <th class="num">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name ?? ('Product #' . $item->product_id) }}</td>
                            <td class="num">{{ rtrim(rtrim(number_format((float) $item->quantity, 2), '0'), '.') }}</td>
                            <td class="num">{{ currency_bdt($item->total_price) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="co-total-row">
                <span>Payable Total</span>
                <span>{{ currency_bdt($order->payable_total) }}</span>
            </div>
        </div>
    </div>

    <form action="{{ route('orders.cancel', $order) }}" method="POST">
        @csrf
        <div class="co-field">
            <label for="reason">Reason (optional)</label>
            <textarea name="reason" id="reason" placeholder="Why is this order being cancelled?"></textarea>
        </div>

        <div class="co-actions">
            <a href="{{ route('orders.show', $order) }}" class="co-btn co-btn-ghost">Go Back</a>
            <button type="submit" class="co-btn co-btn-danger">
                <i class="fas fa-times"></i> Confirm Cancellation
            </button>
        </div>
    </form>
</div>
@endsection
