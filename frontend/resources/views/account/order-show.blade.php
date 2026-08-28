@php
    // Base 3-step tracker (pending/processing/completed), same as before.
    // Online orders get an extra "Packaged" node spliced in between
    // processing and completed -- packaged is an event (see the `timeline`
    // relation + admin's EcommerceOrderController::package()), not a
    // status, so it can't just be added to $steps and looked up by
    // array_search() the way the other three are.
    $steps = ['pending' => 'Order placed', 'processing' => 'Processing'];
    if ($order->isOnline()) {
        $steps['packaged'] = 'Packaged';
    }
    $steps['completed'] = 'Delivered';
    $stepKeys = array_keys($steps);

    $hasPackagedEvent = $order->isOnline() && $order->timeline->contains(fn ($event) => $event->event === 'packaged');
    $isCancelledLike = in_array($order->status, ['cancelled', 'refunded', 'returned']);

    if ($isCancelledLike) {
        $currentStep = -1;
    } else {
        $baseIndex = ['pending' => 0, 'processing' => 1, 'completed' => 2][$order->status] ?? 0;
        if ($order->isOnline()) {
            // pending/processing keep their position (0/1); completed moves
            // to the end (3) to make room for packaged at 2.
            $currentStep = [0 => 0, 1 => 1, 2 => 3][$baseIndex];
            if ($hasPackagedEvent) {
                $currentStep = max($currentStep, 2);
            }
        } else {
            $currentStep = $baseIndex;
        }
    }
@endphp

<x-layout :title="'Order #' . $order->order_no" description="Track your JOJOBI MART order.">
    <div class="container-page py-10">
        <a href="{{ route('account.orders') }}" class="text-sm text-ink-soft hover:text-ink">← Back to orders</a>

        <div class="flex flex-wrap items-center justify-between gap-3 mt-4 mb-8">
            <div>
                <div class="flex items-center gap-2">
                    <p class="eyebrow">Order</p>
                    <span class="badge {{ $order->isOnline() ? 'badge-good' : 'badge-warn' }}">{{ $order->channelLabel() }}</span>
                </div>
                <h1 class="font-display text-3xl mt-1">#{{ $order->order_no }}</h1>
                <p class="text-sm text-ink-soft mt-1">Placed {{ $order->created_at->format('d M Y, g:ia') }}</p>
            </div>
            <span class="badge {{ $order->status === 'completed' ? 'badge-good' : (in_array($order->status, ['cancelled','refunded','returned']) ? 'badge-bad' : 'badge-warn') }} !text-sm">{{ $order->statusLabel() }}</span>
        </div>

        @if ($currentStep >= 0)
            <div class="card rounded-2xl p-6 mb-8">
                <div class="flex items-center">
                    @foreach ($steps as $key => $label)
                        <div class="flex items-center flex-1 last:flex-none">
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 {{ $loop->index <= $currentStep ? 'bg-accent text-accent-ink' : 'bg-bg-2 text-ink-soft' }}">
                                    @if ($loop->index < $currentStep)
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg>
                                    @else
                                        <span class="text-xs font-mono">{{ $loop->iteration }}</span>
                                    @endif
                                </div>
                                <span class="text-xs text-ink-soft whitespace-nowrap">{{ $label }}</span>
                            </div>
                            @if (!$loop->last)
                                <div class="flex-1 h-0.5 mx-2 {{ $loop->index < $currentStep ? 'bg-accent' : 'bg-line' }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="grid lg:grid-cols-[1fr_320px] gap-8">
            <div class="card rounded-2xl overflow-hidden">
                <ul class="divide-y divide-line">
                    @foreach ($order->items as $item)
                        @php($image = $item->product?->images->firstWhere('is_primary', true) ?? $item->product?->images->first())
                        <li class="flex items-center gap-4 p-5">
                            <img src="{{ $image ? asset('storage/' . $image->image_path) : 'https://placehold.co/96x96?text=%20' }}" class="w-14 h-14 rounded-lg object-cover bg-bg-2">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate">{{ $item->product_name }}</p>
                                <p class="text-xs text-ink-soft font-mono mt-0.5">{{ (int) $item->quantity }} × ৳{{ number_format($item->unit_price, 2) }}</p>
                            </div>
                            <span class="price">৳{{ number_format($item->total_price, 2) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="space-y-6">
                <div class="card rounded-2xl p-5">
                    <p class="label mb-3">Payment summary</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-ink-soft">Subtotal</span><span class="font-mono">৳{{ number_format($order->subtotal, 2) }}</span></div>
                        <div class="flex justify-between"><span class="text-ink-soft">Discount</span><span class="font-mono">−৳{{ number_format($order->discount_total, 2) }}</span></div>
                        <div class="flex justify-between pt-2 border-t border-line font-medium"><span>Total</span><span class="price">৳{{ number_format($order->payable_total, 2) }}</span></div>
                    </div>
                    @if ($order->payments->isNotEmpty())
                        <div class="mt-4 pt-4 border-t border-line">
                            <span class="badge {{ $order->payment_status === 'paid' ? 'badge-good' : 'badge-warn' }}">{{ ucfirst($order->payment_status) }} · {{ ucfirst($order->payments->first()->method) }}</span>
                        </div>
                    @endif
                </div>

                @if ($order->isOnline())
                    <div class="card rounded-2xl p-5">
                        <p class="label mb-3">Delivery to</p>
                        <p class="text-sm font-medium">{{ $order->shipping_name }}</p>
                        <p class="text-sm text-ink-soft mt-1">{{ $order->shipping_phone }}</p>
                        <p class="text-sm text-ink-soft mt-1 leading-relaxed">{{ $order->shipping_address }}</p>
                    </div>
                @else
                    <div class="card rounded-2xl p-5">
                        <p class="label mb-3">Purchased in-store</p>
                        <p class="text-sm text-ink-soft leading-relaxed">
                            Rung up at {{ $order->location?->name ?? 'JOJOBI MART' }}. This order was completed in person, not delivered.
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-8">
            @include('partials.order-timeline', ['timeline' => $order->timeline])
        </div>
    </div>
</x-layout>
