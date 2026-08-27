<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order {{ $order->order_no ?? $order->id }}</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 24px 16px;
            background: #f1f2f4;
            color: #14202b;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        .wrap { display: flex; justify-content: center; }

        .card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border: 1px solid #e2e4e8;
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,.06);
        }

        .head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; gap: 8px; }
        .store { font-weight: 900; font-size: 17px; }

        .pill { font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 999px; text-transform: uppercase; letter-spacing: .3px; }
        .pill-ok { background: #e2f7ee; color: #12784a; }
        .pill-warn { background: #fef3e0; color: #92600a; }
        .pill-bad { background: #fde3e3; color: #a11f1f; }
        .pill-info { background: #e6f0fe; color: #1d4fa3; }

        .meta { margin-bottom: 14px; }
        .kv { display: flex; justify-content: space-between; gap: 8px; font-size: 13.5px; padding: 3px 0; }
        .kv span { color: #6b7280; }
        .kv.total { font-size: 16px; font-weight: 900; border-top: 1px dashed #e2e4e8; margin-top: 4px; padding-top: 8px; }

        .section-title { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .4px; color: #6b7280; margin: 14px 0 6px; }

        .items { display: flex; flex-direction: column; gap: 8px; }
        .item { border-bottom: 1px dashed #e2e4e8; padding-bottom: 6px; }
        .item-name { font-weight: 700; font-size: 13.5px; }
        .item-row { display: flex; justify-content: space-between; font-size: 13px; color: #444; }

        .footer { margin-top: 16px; text-align: center; font-size: 12.5px; color: #6b7280; }
    </style>
</head>
<body>
    @php
        $storeName = \App\Models\Setting::get('store_name');
        $footerNote = \App\Models\Setting::get('invoice_footer_note') ?: 'Thank you for shopping with us!';

        $statusMap = [
            'pending' => ['label' => 'Pending', 'tone' => 'warn'],
            'processing' => ['label' => 'Processing', 'tone' => 'info'],
            'paid' => ['label' => 'Paid', 'tone' => 'ok'],
            'partial' => ['label' => 'Partially Paid', 'tone' => 'warn'],
            'completed' => ['label' => 'Completed', 'tone' => 'ok'],
            'cancelled' => ['label' => 'Cancelled', 'tone' => 'bad'],
        ];
        $statusInfo = $statusMap[$order->status] ?? ['label' => ucfirst($order->status ?? 'Unknown'), 'tone' => 'info'];
    @endphp

    <div class="wrap">
        <div class="card">
            <div class="head">
                <div class="store">{{ $storeName ?: 'Order Status' }}</div>
                <span class="pill pill-{{ $statusInfo['tone'] }}">{{ $statusInfo['label'] }}</span>
            </div>

            <div class="meta">
                <div class="kv"><span>Order No.</span><b>{{ $order->order_no ?? $order->id }}</b></div>
                <div class="kv"><span>Date</span><b>{{ $order->created_at->format('d M Y, h:i A') }}</b></div>
                <div class="kv"><span>Customer</span><b>{{ $order->customer->name ?? 'Guest' }}</b></div>
            </div>

            <div class="section-title">Items</div>
            <div class="items">
                @foreach($order->items as $it)
                    <div class="item">
                        <div class="item-name">{{ $it->product_name }}</div>
                        <div class="item-row">
                            <span>{{ number_format((float) $it->quantity, 0) }} &times; {{ format_currency($it->unit_price) }}</span>
                            <b>{{ format_currency($it->total_price) }}</b>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="section-title">Summary</div>
            <div class="kv"><span>Subtotal</span><b>{{ format_currency($order->subtotal) }}</b></div>
            @if($order->discount_total > 0)
                <div class="kv"><span>Discount</span><b>{{ format_currency($order->discount_total) }}</b></div>
            @endif
            <div class="kv total"><span>Net Amount</span><b>{{ format_currency($order->payable_total) }}</b></div>
            <div class="kv"><span>Paid</span><b>{{ format_currency($order->paid_total) }}</b></div>
            @if($order->due_total > 0)
                <div class="kv"><span>Balance Due</span><b>{{ format_currency($order->due_total) }}</b></div>
            @endif

            <div class="footer">{{ $footerNote }}</div>
        </div>
    </div>
</body>
</html>
