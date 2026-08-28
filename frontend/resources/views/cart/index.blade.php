@php($items = $cart?->items ?? collect())

<x-layout title="Your cart" description="Review the items in your JOJOBI MART cart.">
    <div class="container-page py-10">
        <p class="eyebrow">Step 1 of 2</p>
        <h1 class="font-display text-3xl sm:text-4xl mt-1 mb-8">Your cart</h1>

        @if ($items->isEmpty())
            <div class="text-center py-20 card rounded-2xl">
                <svg class="w-14 h-14 mx-auto text-ink-soft mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="9.5" cy="20.5" r="1.3"/><circle cx="18" cy="20.5" r="1.3"/><path d="M2.5 3h2.6l2.3 12.2a2 2 0 002 1.6h8a2 2 0 002-1.8L21 7.5H6.3"/></svg>
                <p class="text-ink-soft mb-6">Your cart is empty right now.</p>
                <a href="{{ route('products.index') }}" class="btn btn-stamp">Start shopping</a>
            </div>
        @else
            <div class="grid lg:grid-cols-[1fr_340px] gap-10 items-start">
                <ul class="divide-y divide-line card rounded-2xl overflow-hidden">
                    @foreach ($items as $item)
                        <li class="flex flex-col sm:flex-row gap-4 p-5">
                            <a href="{{ $item->product ? route('products.show', $item->product) : '#' }}" class="shrink-0">
                                <img
                                    src="{{ $item->image ? asset('storage/' . $item->image->image_path) : 'https://placehold.co/160x160?text=%20' }}"
                                    class="w-24 h-24 rounded-xl object-cover bg-bg-2"
                                >
                            </a>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-3">
                                    <a href="{{ $item->product ? route('products.show', $item->product) : '#' }}" class="font-medium hover:text-accent transition">{{ $item->product?->name ?? 'Product' }}</a>
                                    <form method="POST" action="{{ route('cart.remove', $item) }}">
                                        @csrf @method('DELETE')
                                        <button class="text-ink-soft hover:text-danger" aria-label="Remove">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
                                        </button>
                                    </form>
                                </div>
                                <p class="text-xs text-ink-soft font-mono mt-1">৳{{ number_format($item->unit_price, 2) }} / {{ $item->unit }}</p>

                                <div class="flex items-center justify-between mt-4">
                                    <form method="POST" action="{{ route('cart.update', $item) }}" class="flex items-center border border-line rounded-full">
                                        @csrf @method('PATCH')
                                        <button
                                            onclick="this.form.quantity.value = Math.max(0, parseFloat(this.form.quantity.value) - 1)"
                                            class="w-8 h-8 flex items-center justify-center text-ink-soft hover:text-ink"
                                        >−</button>
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" step="any" min="0" class="w-14 text-center bg-transparent font-mono text-sm" onchange="this.form.submit()">
                                        <button
                                            onclick="this.form.quantity.value = parseFloat(this.form.quantity.value) + 1"
                                            class="w-8 h-8 flex items-center justify-center text-ink-soft hover:text-ink"
                                        >+</button>
                                    </form>
                                    <span class="price">৳{{ number_format($item->total_price, 2) }}</span>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="card rounded-2xl p-6 lg:sticky lg:top-24">
                    <h2 class="font-display text-xl mb-4">Order summary</h2>
                    <div class="flex justify-between text-sm py-2">
                        <span class="text-ink-soft">Subtotal ({{ (int) $items->sum('quantity') }} items)</span>
                        <span class="font-mono">৳{{ number_format($items->sum('total_price'), 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm py-2 text-ink-soft">
                        <span>Delivery</span>
                        <span class="font-mono">Calculated at checkout</span>
                    </div>
                    <div class="flex justify-between items-baseline pt-4 mt-2 border-t border-line">
                        <span class="font-medium">Total</span>
                        <span class="price text-2xl">৳{{ number_format($items->sum('total_price'), 2) }}</span>
                    </div>
                    <a href="{{ route('checkout.index') }}" class="btn btn-stamp w-full justify-center mt-6">
                        {{ auth('customer')->check() ? 'Proceed to checkout' : 'Sign in to checkout' }}
                    </a>
                    @guest('customer')
                        <p class="text-xs text-ink-soft text-center mt-2">
                            You'll sign in or create a free account first — your cart carries over.
                        </p>
                    @endguest
                    <a href="{{ route('products.index') }}" class="btn btn-cut w-full justify-center mt-3">Continue shopping</a>
                </div>
            </div>
        @endif

        @if ($recommendations->isNotEmpty())
            <div class="mt-16">
                <h2 class="font-display text-xl mb-1">You could also afford with your points</h2>
                <p class="text-sm text-ink-soft mb-6">Priced within your {{ number_format(auth('customer')->user()->reward_points, 0) }}-point balance.</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach ($recommendations as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layout>
