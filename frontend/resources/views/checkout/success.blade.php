<x-layout title="Order placed" description="Your JOJOBI MART order has been placed.">
    <div class="container-page py-16 sm:py-24 max-w-2xl mx-auto text-center">
        <div class="w-16 h-16 rounded-full bg-teal/10 text-teal flex items-center justify-center mx-auto animate-fade-up">
            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M8 12.5l2.5 2.5L16 9.5"/></svg>
        </div>
        <p class="eyebrow mt-6">Order confirmed</p>
        <h1 class="font-display text-3xl sm:text-4xl mt-2">Thanks — your order is in!</h1>
        <p class="text-ink-soft mt-3">
            Order <span class="font-mono text-ink">#{{ $order->order_no }}</span> has been placed. We'll reach out at {{ $order->shipping_phone }} to confirm delivery.
        </p>

        <div class="card rounded-2xl p-6 mt-10 text-left">
            <div class="flex justify-between text-sm py-2 border-b border-line">
                <span class="text-ink-soft">Order number</span>
                <span class="font-mono">{{ $order->order_no }}</span>
            </div>
            <div class="flex justify-between text-sm py-2 border-b border-line">
                <span class="text-ink-soft">Status</span>
                <span class="badge badge-warn">{{ $order->statusLabel() }}</span>
            </div>
            <div class="flex justify-between text-sm py-2 border-b border-line">
                <span class="text-ink-soft">Payment</span>
                <span>{{ ucfirst($order->payments->first()?->method ?? '—') }}</span>
            </div>
            <ul class="mt-3 space-y-2">
                @foreach ($order->items as $item)
                    <li class="flex justify-between text-sm">
                        <span class="text-ink-soft">{{ (int) $item->quantity }}× {{ $item->product_name }}</span>
                        <span class="font-mono">৳{{ number_format($item->total_price, 2) }}</span>
                    </li>
                @endforeach
            </ul>
            <div class="flex justify-between items-baseline pt-4 mt-3 border-t border-line">
                <span class="font-medium">Total</span>
                <span class="price text-xl">৳{{ number_format($order->payable_total, 2) }}</span>
            </div>
        </div>

        @if ($trackUrl)
            <p class="text-xs text-ink-soft mt-6">
                No account needed -- bookmark your tracking link below to check on this order any time.
            </p>
        @endif

        <div class="flex flex-wrap justify-center gap-3 mt-8">
            <a href="{{ route('products.index') }}" class="btn btn-cut">Continue shopping</a>
            <a href="{{ $trackUrl ?? route('account.orders.show', $order) }}" class="btn btn-stamp">Track this order</a>
        </div>
    </div>
</x-layout>
