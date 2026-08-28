<x-layout title="My orders" description="Your JOJOBI MART order history.">
    <div class="container-page py-10">
        <p class="eyebrow">My account</p>
        <h1 class="font-display text-3xl sm:text-4xl mt-1 mb-8">Order history</h1>

        <div class="grid lg:grid-cols-[240px_1fr] gap-8">
            @include('partials.account-nav')

            <div>
                <div class="flex gap-2 mb-4">
                    <a href="{{ route('account.orders') }}" class="btn {{ !$channel ? 'btn-stamp' : 'btn-cut' }} !text-xs">All</a>
                    <a href="{{ route('account.orders', ['channel' => 'online']) }}" class="btn {{ $channel === 'online' ? 'btn-stamp' : 'btn-cut' }} !text-xs">Online</a>
                    <a href="{{ route('account.orders', ['channel' => 'in_store']) }}" class="btn {{ $channel === 'in_store' ? 'btn-stamp' : 'btn-cut' }} !text-xs">In-store</a>
                </div>

                <div class="card rounded-2xl overflow-hidden">
                    @if ($orders->isEmpty())
                        <div class="text-center py-20">
                            <p class="text-ink-soft mb-6">No orders to show here yet.</p>
                            <a href="{{ route('products.index') }}" class="btn btn-stamp">Start shopping</a>
                        </div>
                    @else
                        <ul class="divide-y divide-line">
                            @foreach ($orders as $order)
                                <li>
                                    <a href="{{ route('account.orders.show', $order) }}" class="flex flex-wrap items-center gap-3 justify-between p-5 hover:bg-bg-2 transition">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <p class="font-mono text-sm">#{{ $order->order_no }}</p>
                                                <span class="badge {{ $order->isOnline() ? 'badge-good' : 'badge-warn' }}">{{ $order->channelLabel() }}</span>
                                            </div>
                                            <p class="text-xs text-ink-soft mt-1">{{ $order->created_at->format('d M Y, g:ia') }} · {{ $order->items_count }} items</p>
                                        </div>
                                        <span class="badge {{ $order->status === 'completed' ? 'badge-good' : ($order->status === 'cancelled' ? 'badge-bad' : 'badge-warn') }}">{{ $order->statusLabel() }}</span>
                                        <span class="price">৳{{ number_format($order->payable_total, 2) }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="p-5 border-t border-line">{{ $orders->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layout>
