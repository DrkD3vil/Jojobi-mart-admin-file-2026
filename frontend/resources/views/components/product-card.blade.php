@props(['product', 'wished' => false])

@php
    $image = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
    $batch = $product->batches->sortBy('sell_price')->first();
    $available = $batch ? $batch->availableAt(config('store.location_id')) : 0;
    $onSale = $batch && $batch->isOnSale();
@endphp

<div class="group relative" x-data="{ wished: @js($wished) }">
    <div class="relative aspect-square rounded-xl overflow-hidden bg-bg-2">
        <a href="{{ route('products.show', $product) }}">
            <img
                src="{{ $image ? asset('storage/' . $image->image_path) : 'https://placehold.co/480x480/EAE3D2/5A6570?text=' . urlencode($product->name) }}"
                alt="{{ $product->name }}"
                loading="lazy"
                class="w-full h-full object-cover transition duration-500 group-hover:scale-105"
            >
        </a>

        @if ($onSale)
            <span class="absolute top-3 left-3 badge badge-bad !bg-danger !text-bg !border-transparent">Sale</span>
        @endif

        @if ($available <= 0)
            <div class="absolute inset-0 bg-bg/70 backdrop-blur-[1px] flex items-center justify-center">
                <span class="badge bg-ink text-bg">Out of stock</span>
            </div>
        @endif

        @auth('customer')
            <button
                @click.prevent="
                    wished = !wished;
                    fetch('{{ route('wishlist.toggle', $product) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' } })
                        .then(r => r.json()).then(res => $store.toast.push(res.wished ? 'Added to wishlist' : 'Removed from wishlist'));
                "
                class="absolute top-3 right-3 w-8 h-8 rounded-full bg-surface/90 backdrop-blur flex items-center justify-center transition hover:scale-110"
                :class="wished ? 'text-danger' : 'text-ink-soft'"
                aria-label="Toggle wishlist"
            >
                <svg class="w-4 h-4" viewBox="0 0 24 24" :fill="wished ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2"><path d="M12 21s-7-4.6-9.5-9C.8 8.4 2.4 4.5 6 4c2-.3 3.7.8 6 3 2.3-2.2 4-3.3 6-3 3.6.5 5.2 4.4 3.5 8-2.5 4.4-9.5 9-9.5 9z"/></svg>
            </button>
        @endauth

        <button
            x-data
            @click.prevent="
                fetch('{{ route('cart.add') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' }, body: JSON.stringify({ batch_id: {{ $batch->id ?? 'null' }}, quantity: 1 }) })
                    .then(r => r.json())
                    .then(res => {
                        $store.toast.push(res.message, res.success ? 'good' : 'bad');
                        if (res.success) { $store.cart.set(res.count); window.dispatchEvent(new CustomEvent('open-cart-drawer')); }
                    });
            "
            {{ (!$batch || $available <= 0) ? 'disabled' : '' }}
            class="absolute bottom-3 right-3 w-9 h-9 rounded-full bg-ink text-bg flex items-center justify-center opacity-0 translate-y-2 group-hover:opacity-100 group-hover:translate-y-0 transition disabled:opacity-0"
            aria-label="Add to cart"
        >
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
        </button>
    </div>

    <div class="mt-3">
        @if ($product->brand)
            <p class="text-[11px] font-mono uppercase tracking-wider text-ink-soft">{{ $product->brand->name }}</p>
        @endif
        <a href="{{ route('products.show', $product) }}" class="block text-sm font-medium line-clamp-2 mt-0.5 hover:text-accent transition">{{ $product->name }}</a>

        <div class="flex items-center justify-between mt-1.5">
            <div class="flex items-baseline gap-2">
                <span class="price text-base">৳{{ number_format($batch?->displayPrice() ?? 0, 2) }}</span>
                @if ($onSale)
                    <span class="text-xs text-ink-soft line-through">৳{{ number_format($batch->original_sell_price, 2) }}</span>
                @endif
            </div>
        </div>
    </div>
</div>
