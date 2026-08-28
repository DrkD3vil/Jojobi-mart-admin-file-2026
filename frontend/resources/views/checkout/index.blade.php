@php($items = $cart->items)

<x-layout title="Checkout" description="Complete your JOJOBI MART order.">
    <div class="container-page py-10">
        <p class="eyebrow">Step 2 of 2</p>
        <h1 class="font-display text-3xl sm:text-4xl mt-1 mb-8">Checkout</h1>

        <form method="POST" action="{{ route('checkout.store') }}" class="grid lg:grid-cols-[1fr_360px] gap-10 items-start" x-data="{ method: 'cod' }">
            @csrf

            <div class="space-y-8">
                <div class="card rounded-2xl p-6">
                    <h2 class="font-display text-xl mb-5">Delivery details</h2>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="field">
                            <label class="label">Full name</label>
                            <input type="text" name="name" required value="{{ old('name', $customer->name ?? '') }}" class="input mt-1.5">
                        </div>
                        <div class="field">
                            <label class="label">Phone number</label>
                            <input type="text" name="phone" required value="{{ old('phone', $customer->phone ?? '') }}" class="input mt-1.5">
                        </div>
                        <div class="sm:col-span-2 field">
                            <label class="label">Delivery address</label>
                            <textarea name="address" rows="3" required class="input mt-1.5">{{ old('address', $customer->address ?? '') }}</textarea>
                        </div>
                        <div class="sm:col-span-2 field">
                            <label class="label">Note for delivery (optional)</label>
                            <input type="text" name="note" value="{{ old('note') }}" placeholder="e.g. call before arriving" class="input mt-1.5">
                        </div>
                        @unless ($customer)
                            <div class="sm:col-span-2 field">
                                <label class="label">Email (optional)</label>
                                <input type="email" name="email" value="{{ old('email') }}" placeholder="For order updates" class="input mt-1.5">
                            </div>
                        @endunless
                    </div>
                    @if ($customer)
                        <p class="text-xs text-ink-soft mt-4">
                            Ordering as <span class="text-ink font-medium">{{ $customer->name }}</span> ({{ $customer->phone }}) — this order will show up in <a href="{{ route('account.orders') }}" class="text-accent hover:underline">your order history</a>.
                        </p>
                    @else
                        <p class="text-xs text-ink-soft mt-4">
                            Checking out as a guest.
                            <a href="{{ route('login') }}" class="text-accent hover:underline">Have an account? Log in for faster checkout</a>
                        </p>
                    @endif
                </div>

                <div class="card rounded-2xl p-6">
                    <h2 class="font-display text-xl mb-5">Payment method</h2>
                    <div class="space-y-3">
                        @foreach ($paymentMethods as $key => $meta)
                            <label class="flex items-center gap-3 p-4 rounded-xl border cursor-pointer transition" :class="method === '{{ $key }}' ? 'border-accent bg-accent/5' : 'border-line'">
                                <input type="radio" name="payment_method" value="{{ $key }}" x-model="method" {{ $loop->first ? 'checked' : '' }} class="accent-current">
                                <span class="flex-1 text-sm font-medium">{{ $meta['label'] }}</span>
                                @if ($key === 'cod')
                                    <span class="badge badge-good">Recommended</span>
                                @endif
                            </label>
                        @endforeach
                    </div>

                    <div x-show="method !== 'cod'" x-cloak x-transition class="mt-4">
                        <label class="label">Transaction ID</label>
                        <input type="text" name="trx_id" value="{{ old('trx_id') }}" placeholder="Send payment first, then paste the transaction ID here" class="input mt-1.5">
                        <p class="text-xs text-ink-soft mt-2">We'll confirm your payment shortly after you place the order.</p>
                    </div>
                </div>
            </div>

            <div class="card rounded-2xl p-6 lg:sticky lg:top-24">
                <h2 class="font-display text-xl mb-4">Order summary</h2>
                <ul class="space-y-3 max-h-64 overflow-y-auto pr-1">
                    @foreach ($items as $item)
                        <li class="flex items-center gap-3 text-sm">
                            <span class="w-8 h-8 rounded-md bg-bg-2 shrink-0 flex items-center justify-center font-mono text-[10px] text-ink-soft">{{ (int) $item->quantity }}×</span>
                            <span class="flex-1 truncate">{{ $item->product?->name }}</span>
                            <span class="font-mono">৳{{ number_format($item->total_price, 2) }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="flex justify-between items-baseline pt-4 mt-4 border-t border-line">
                    <span class="font-medium">Total</span>
                    <span class="price text-2xl">৳{{ number_format($items->sum('total_price'), 2) }}</span>
                </div>
                <button type="submit" class="btn btn-stamp w-full justify-center mt-6">Place order</button>
                <a href="{{ route('cart.index') }}" class="block text-center text-xs text-ink-soft hover:text-ink mt-3">← Back to cart</a>
            </div>
        </form>
    </div>
</x-layout>
