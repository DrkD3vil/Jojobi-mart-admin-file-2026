<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->order_no ?? $order->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background-color: #fff;
            display: flex;
            align-items: center;
            color: #000000;
        }

        .invoice {
            width: 100%;
            padding: 5px;
            background-color: #fff;
            border: none;
            text-align: center;
            font-size: 12px;
            color: #000000;
            box-sizing: border-box;
        }

        .shop_details, .bill_details, .customer_details, .footer {
            text-align: center;
            font-size: 12px;
            margin-bottom: 5px;
            color: #000000;
        }

        .shop_logo img {
            display: block;
            margin: 0 auto 6px;
            max-height: 60px;
            max-width: 60%;
        }

        .shop_name {
            font-size: 15px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .shop_location, .shop_number {
            font-size: 12px;
            color: #000000;
        }

        .section-divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .invoice-barcode img,
        .qrcode img {
            display: block;
            margin: 5px auto;
        }

        .invoice-barcode img {
            max-width: 100%;
        }

        .qrcode img {
            max-width: 30%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            color: #000000;
        }

        table th, table td {
            padding: 4px;
            border-bottom: 1px solid #000;
            text-align: center;
            color: #000000;
        }

        table td:last-child,
        .footer_item span:last-child {
            text-align: right;
        }

        .table_footer {
            font-size: 12px;
            text-align: left;
            margin-top: 5px;
            color: #000000;
        }

        .footer_item {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            color: #000000;
        }

        .footer_item span:first-child {
            font-weight: 600;
        }

        .footer_item.total {
            font-size: 15px;
            font-weight: 900;
            border-top: 1px dashed #000;
            margin-top: 4px;
            padding-top: 6px;
        }

        .footer {
            margin-top: 10px;
            color: #000000;
        }

        .thank-you-message {
            font-size: 14px;
            font-weight: bold;
            color: #000000;
        }

        .scan-hint {
            font-size: 10px;
            color: #444;
        }

        @media print {
            body {
                margin: 0;
                text-align: center;
                color: #000000;
            }

            .invoice {
                width: 100%;
                margin: 0 auto;
                color: #000000;
            }

            .invoice-barcode img {
                max-width: 100%;
                color: #000000;
            }

            table th, table td {
                font-size: 11px;
                color: #000000;
            }
        }
    </style>
</head>
<body>
    @php
        // Shop identity is per-branch: prefer the order's location (name/address),
        // falling back to the global Settings values when the order has no
        // location or the location has no address on file.
        $storeName = $order->location->name ?? \App\Models\Setting::get('store_name');
        $storeAddress = $order->location->address ?? \App\Models\Setting::get('store_address');
        $storePhone = \App\Models\Setting::get('store_phone');
        $footerNote = \App\Models\Setting::get('invoice_footer_note') ?: 'Thank you for your visit!';

        $itemDiscount = $order->items->sum('discount_amount');
    @endphp

    <div class="invoice">
        @if($logoImage)
            <div class="shop_logo">
                <img src="{{ $logoImage }}" alt="{{ $storeName ?: 'Shop logo' }}">
            </div>
        @endif

        <div class="shop_details">
            <div class="shop_name">{{ $storeName ?: 'Sales Invoice' }}</div>
            @if($storeAddress)<div class="shop_location">{{ $storeAddress }}</div>@endif
            @if($storePhone)<div class="shop_number">Mobile: {{ $storePhone }}</div>@endif
        </div>

        <div class="section-divider"></div>

        <div class="bill_details">
            <div><strong>Invoice No:</strong> {{ $order->order_no ?? $order->id }}</div>
            <div><strong>Date:</strong> {{ $order->created_at->format('d M Y, h:i A') }}</div>
            @if($cashierName)
                <div><strong>Served By:</strong> {{ $cashierName }}</div>
            @endif
        </div>

        <div class="invoice-barcode">
            <img src="{{ $barcodeImage }}" alt="Barcode">
        </div>

        <div class="section-divider"></div>

        <div class="customer_details">
            @if($order->customer)
                <div><strong>Customer:</strong> {{ $order->customer->name }}</div>
                @if($order->customer->phone)
                    <div><strong>Phone:</strong> {{ $order->customer->phone }}</div>
                @endif
            @else
                <div><strong>Customer:</strong> Walk-in Customer</div>
            @endif
        </div>

        <div class="section-divider"></div>

        <table style="margin-top: 10px;">
            <thead>
                <tr>
                    <th>#</th>
                    <th style="text-align: left;">Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $k => $it)
                    <tr>
                        <td>{{ $k + 1 }}</td>
                        <td style="text-align: left;">{{ $it->product_name }}</td>
                        <td>{{ number_format((float) $it->quantity, 0) }}</td>
                        <td>{{ number_format((float) $it->unit_price, 2) }}</td>
                        <td>{{ number_format((float) $it->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="table_footer">
            <div class="footer_item"><span>Total Items</span><span>{{ $order->items->count() }}</span></div>
            <div class="footer_item"><span>Subtotal</span><span>{{ format_currency($order->subtotal) }}</span></div>

            @if($itemDiscount > 0)
                <div class="footer_item"><span>Item Discount</span><span>{{ format_currency($itemDiscount) }}</span></div>
            @endif
            @if($order->discount_total > 0)
                <div class="footer_item"><span>Bill Discount</span><span>{{ format_currency($order->discount_total) }}</span></div>
            @endif

            <div class="footer_item total"><span>Net Amount</span><span>{{ format_currency($order->payable_total) }}</span></div>
            <div class="footer_item"><span>Paid</span><span>{{ format_currency($order->paid_total) }}</span></div>

            @if($order->due_total > 0)
                <div class="footer_item"><span>Due</span><span>{{ format_currency($order->due_total) }}</span></div>
            @endif
            @if($order->change_total > 0)
                <div class="footer_item"><span>Change</span><span>{{ format_currency($order->change_total) }}</span></div>
            @endif
        </div>

        @if($order->payments->count() > 0)
            <div class="table_footer">
                <strong>Payment History</strong>
                @foreach($order->payments as $p)
                    <div class="footer_item">
                        <span>{{ ucfirst($p->method ?? $p->channel) }} &bull; {{ optional($p->created_at)->format('d M, h:i A') }}</span>
                        <span>{{ format_currency($p->amount) }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="qrcode">
            <img src="{{ $qrImage }}" alt="Track your order">
            <div class="scan-hint">Scan to track your order</div>
        </div>

        <div class="footer">
            <div class="thank-you-message">{{ $footerNote }}</div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            setTimeout(() => {
                window.print();
            }, 500);

            document.addEventListener("keydown", function (event) {
                if (event.key === "F11") {
                    event.preventDefault();
                    window.print();
                }
            });
        });
    </script>
</body>
</html>
