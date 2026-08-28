<x-layout title="My account" description="Your JOJOBI MART account.">
    <div class="container-page py-10">
        <p class="eyebrow">My account</p>
        <h1 class="font-display text-3xl sm:text-4xl mt-1 mb-8">Hi, {{ explode(' ', $customer->name)[0] }}</h1>

        <div class="grid lg:grid-cols-[240px_1fr] gap-8">
            @include('partials.account-nav')

            <div class="space-y-8">
                <div class="grid sm:grid-cols-4 gap-4">
                    <div class="card rounded-2xl p-5">
                        <p class="label">Orders</p>
                        <p class="price text-2xl mt-1">{{ $customer->orders()->count() }}</p>
                        <p class="text-[11px] text-ink-soft font-mono mt-1">{{ $onlineOrderCount }} online · {{ $inStoreOrderCount }} in-store</p>
                    </div>
                    <div class="card rounded-2xl p-5">
                        <p class="label">Reward points</p>
                        <p class="price text-2xl mt-1 text-teal">{{ number_format($customer->reward_points, 0) }}</p>
                        <p class="text-[11px] text-ink-soft font-mono mt-1">worth ৳{{ number_format($customer->reward_points, 0) }}</p>
                    </div>
                    <div class="card rounded-2xl p-5">
                        <p class="label">Store credit</p>
                        <p class="price text-2xl mt-1">৳{{ number_format($customer->advance_balance, 2) }}</p>
                        <p class="text-[11px] text-ink-soft font-mono mt-1">available to spend</p>
                    </div>
                    <div class="card rounded-2xl p-5">
                        <p class="label">Wishlist</p>
                        <p class="price text-2xl mt-1">{{ $wishlistCount }}</p>
                        <a href="{{ route('wishlist.index') }}" class="text-[11px] text-accent hover:underline">view saved items</a>
                    </div>
                </div>

                @if ($recommendations->isNotEmpty())
                    <div class="card rounded-2xl overflow-hidden">
                        <div class="p-5 border-b border-line">
                            <h2 class="font-display text-lg">Redeem your points on</h2>
                            <p class="text-xs text-ink-soft mt-0.5">Priced within your {{ number_format($customer->reward_points, 0) }}-point balance.</p>
                        </div>
                        <div class="p-5 grid grid-cols-2 sm:grid-cols-4 gap-6">
                            @foreach ($recommendations as $product)
                                <x-product-card :product="$product" />
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ((float) $customer->due_balance > 0)
                    <div class="card rounded-2xl p-4 flex items-center gap-3 border-accent/40">
                        <span class="badge badge-warn shrink-0">Balance due</span>
                        <p class="text-sm text-ink-soft">You have <span class="text-ink font-medium">৳{{ number_format($customer->due_balance, 2) }}</span> outstanding on past orders.</p>
                    </div>
                @endif

                <div class="card rounded-2xl overflow-hidden">
                    <div class="flex items-center justify-between p-5 border-b border-line">
                        <div>
                            <h2 class="font-display text-lg">Recent orders</h2>
                            <p class="text-xs text-ink-soft mt-0.5">Online and in-store purchases, all in one place.</p>
                        </div>
                        <a href="{{ route('account.orders') }}" class="text-sm text-accent hover:underline shrink-0">View all</a>
                    </div>
                    @if ($recentOrders->isEmpty())
                        <div class="p-10 text-center">
                            <p class="text-sm text-ink-soft mb-4">You haven't placed any orders yet.</p>
                            <a href="{{ route('products.index') }}" class="btn btn-stamp">Start shopping</a>
                        </div>
                    @else
                        <ul class="divide-y divide-line">
                            @foreach ($recentOrders as $order)
                                <li>
                                    <a href="{{ route('account.orders.show', $order) }}" class="flex flex-wrap items-center gap-3 justify-between p-5 hover:bg-bg-2 transition">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <p class="font-mono text-sm">#{{ $order->order_no }}</p>
                                                <span class="badge {{ $order->isOnline() ? 'badge-good' : 'badge-warn' }}">{{ $order->channelLabel() }}</span>
                                            </div>
                                            <p class="text-xs text-ink-soft mt-1">{{ $order->created_at->format('d M Y') }} · {{ $order->items_count }} item{{ $order->items_count === 1 ? '' : 's' }}</p>
                                        </div>
                                        <span class="badge {{ $order->status === 'completed' ? 'badge-good' : ($order->status === 'cancelled' ? 'badge-bad' : 'badge-warn') }}">{{ $order->statusLabel() }}</span>
                                        <span class="price">৳{{ number_format($order->payable_total, 2) }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layout>
