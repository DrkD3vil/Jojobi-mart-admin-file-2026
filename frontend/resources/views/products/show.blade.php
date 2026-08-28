@php
    $images = $product->images->sortByDesc('is_primary')->values();
    $onSale = $batch->isOnSale();
@endphp

<x-layout :title="$product->name" :description="\Illuminate\Support\Str::limit(strip_tags($product->description ?? $product->name), 155)">

    <div class="container-page py-8 sm:py-10">
        <nav class="text-xs text-ink-soft font-mono mb-6 flex flex-wrap gap-1">
            <a href="{{ route('home') }}" class="hover:text-ink">Home</a> /
            @if ($product->category)
                <a href="{{ route('categories.show', $product->category) }}" class="hover:text-ink">{{ $product->category->name }}</a> /
            @endif
            <span class="text-ink">{{ $product->name }}</span>
        </nav>

        <div class="grid lg:grid-cols-2 gap-10 xl:gap-16">
            <div x-data="{ active: 0 }">
                <div class="aspect-square rounded-2xl overflow-hidden bg-bg-2 card">
                    @forelse ($images as $i => $img)
                        <img
                            x-show="active === {{ $i }}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            src="{{ asset('storage/' . $img->image_path) }}" alt="{{ $product->name }}"
                            class="w-full h-full object-cover"
                        >
                    @empty
                        <img src="https://placehold.co/640x640/EAE3D2/5A6570?text={{ urlencode($product->name) }}" class="w-full h-full object-cover">
                    @endforelse
                </div>

                @if ($images->count() > 1)
                    <div class="flex gap-3 mt-4 overflow-x-auto scrollbar-none">
                        @foreach ($images as $i => $img)
                            <button
                                @click="active = {{ $i }}"
                                class="shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 transition"
                                :class="active === {{ $i }} ? 'border-accent' : 'border-transparent opacity-70 hover:opacity-100'"
                            >
                                <img src="{{ asset('storage/' . $img->image_path) }}" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div x-data="{ qty: 1, max: {{ (float) $available ?: 0 }} }">
                @if ($product->brand)
                    <a href="{{ route('brands.show', $product->brand) }}" class="eyebrow">{{ $product->brand->name }}</a>
                @endif
                <h1 class="font-display text-3xl sm:text-4xl mt-2 leading-tight">{{ $product->name }}</h1>

                @if ($product->reviews_count > 0)
                    <div class="flex items-center gap-2 mt-3">
                        <x-star-rating :rating="$product->average_rating" />
                        <span class="text-sm text-ink-soft">{{ number_format($product->average_rating, 1) }} ({{ $product->reviews_count }} review{{ $product->reviews_count === 1 ? '' : 's' }})</span>
                    </div>
                @endif

                <div class="flex items-baseline gap-3 mt-5">
                    <span class="price text-3xl">৳{{ number_format($batch->displayPrice(), 2) }}</span>
                    @if ($onSale)
                        <span class="text-ink-soft line-through">৳{{ number_format($batch->original_sell_price, 2) }}</span>
                        <span class="badge badge-bad">-{{ round((1 - $batch->displayPrice() / $batch->original_sell_price) * 100) }}%</span>
                    @endif
                    <span class="text-ink-soft text-sm">/ {{ $batch->unit }}</span>
                </div>

                <div class="mt-3">
                    @if ($available > 0)
                        <span class="badge badge-good"><span class="w-1.5 h-1.5 rounded-full bg-current"></span> In stock — {{ rtrim(rtrim(number_format($available, 2), '0'), '.') }} {{ $batch->unit }} left</span>
                    @else
                        <span class="badge badge-bad">Out of stock</span>
                    @endif
                </div>

                <p class="text-ink-soft leading-relaxed mt-6 max-w-lg">{{ $product->description ?: 'No description available for this product yet.' }}</p>

                <div class="flex items-center gap-4 mt-8">
                    <div class="flex items-center border border-line rounded-full">
                        <button @click="qty = Math.max(1, qty - 1)" class="w-11 h-11 flex items-center justify-center text-ink-soft hover:text-ink text-lg">−</button>
                        <input type="number" x-model.number="qty" min="1" :max="max" class="w-12 text-center bg-transparent font-mono text-sm">
                        <button @click="qty = Math.min(max || qty + 1, qty + 1)" class="w-11 h-11 flex items-center justify-center text-ink-soft hover:text-ink text-lg">+</button>
                    </div>

                    <button
                        @click="
                            fetch('{{ route('cart.add') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' }, body: JSON.stringify({ batch_id: {{ $batch->id }}, quantity: qty }) })
                                .then(r => r.json())
                                .then(res => {
                                    $store.toast.push(res.message, res.success ? 'good' : 'bad');
                                    if (res.success) { $store.cart.set(res.count); window.dispatchEvent(new CustomEvent('open-cart-drawer')); }
                                });
                        "
                        {{ $available <= 0 ? 'disabled' : '' }}
                        class="btn btn-stamp flex-1 justify-center disabled:opacity-40 disabled:pointer-events-none"
                    >
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9.5" cy="20.5" r="1.3"/><circle cx="18" cy="20.5" r="1.3"/><path d="M2.5 3h2.6l2.3 12.2a2 2 0 002 1.6h8a2 2 0 002-1.8L21 7.5H6.3"/></svg>
                        Add to cart
                    </button>

                    @auth('customer')
                        <button
                            x-data="{ wished: {{ in_array($product->id, $wishedIds ?? []) ? 'true' : 'false' }} }"
                            @click="
                                wished = !wished;
                                fetch('{{ route('wishlist.toggle', $product) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' } })
                                    .then(r => r.json()).then(res => $store.toast.push(res.wished ? 'Added to wishlist' : 'Removed from wishlist'));
                            "
                            class="w-11 h-11 shrink-0 flex items-center justify-center rounded-full border border-line transition"
                            :class="wished ? 'text-danger border-danger/40' : 'text-ink-soft'"
                            aria-label="Toggle wishlist"
                        >
                            <svg class="w-4 h-4" viewBox="0 0 24 24" :fill="wished ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2"><path d="M12 21s-7-4.6-9.5-9C.8 8.4 2.4 4.5 6 4c2-.3 3.7.8 6 3 2.3-2.2 4-3.3 6-3 3.6.5 5.2 4.4 3.5 8-2.5 4.4-9.5 9-9.5 9z"/></svg>
                        </button>
                    @endauth
                </div>

                <div class="grid grid-cols-2 gap-4 mt-8 pt-8 border-t border-line text-sm">
                    <div class="flex items-center gap-2 text-ink-soft"><svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="1.5" y="7" width="13" height="10" rx="1"/><path d="M14.5 10h4l3 3v4h-7z"/></svg> Delivered in 24–48h</div>
                    <div class="flex items-center gap-2 text-ink-soft"><svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg> Cash on delivery</div>
                    <div class="flex items-center gap-2 text-ink-soft"><svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M4 10a8 8 0 0114-5.3M20 14a8 8 0 01-14 5.3"/></svg> Easy returns</div>
                    <div class="flex items-center gap-2 text-ink-soft font-mono text-xs">SKU {{ $batch->batch_sku }}</div>
                </div>
            </div>
        </div>

        <section class="mt-20 pt-12 border-t border-line grid lg:grid-cols-[1fr_320px] gap-12">
            <div>
                <h2 class="font-display text-2xl mb-6">Reviews {{ $product->reviews_count ? '(' . $product->reviews_count . ')' : '' }}</h2>

                @if ($reviews->isEmpty())
                    <p class="text-sm text-ink-soft">No reviews yet — be the first to share your thoughts.</p>
                @else
                    <ul class="space-y-6">
                        @foreach ($reviews as $review)
                            <li class="pb-6 border-b border-line last:border-0">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-7 h-7 rounded-full bg-teal text-bg font-mono text-[10px] font-semibold flex items-center justify-center">{{ strtoupper(substr($review->customer->name ?? '?', 0, 1)) }}</span>
                                        <span class="text-sm font-medium">{{ $review->customer->name ?? 'Customer' }}</span>
                                    </div>
                                    <span class="text-xs text-ink-soft font-mono">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                <x-star-rating :rating="$review->rating" size="w-3.5 h-3.5" />
                                @if ($review->title)
                                    <p class="text-sm font-medium mt-2">{{ $review->title }}</p>
                                @endif
                                @if ($review->comment)
                                    <p class="text-sm text-ink-soft mt-1 leading-relaxed">{{ $review->comment }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div>
                @auth('customer')
                    @if ($canReview)
                        <div class="card rounded-xl p-5" x-data="{ rating: 5 }">
                            <p class="font-medium text-sm mb-4">Write a review</p>
                            <form method="POST" action="{{ route('reviews.store', $product) }}" class="space-y-3">
                                @csrf
                                <div class="flex gap-1">
                                    <template x-for="i in 5" :key="i">
                                        <button type="button" @click="rating = i" class="w-6 h-6">
                                            <svg class="w-6 h-6" :class="i <= rating ? 'text-accent' : 'text-line'" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.5l2.9 6.1 6.6.8-4.9 4.5 1.3 6.6L12 17.6l-5.9 3 1.3-6.6-4.9-4.5 6.6-.8L12 2.5z"/></svg>
                                        </button>
                                    </template>
                                </div>
                                <input type="hidden" name="rating" x-model="rating">
                                <input type="text" name="title" placeholder="Title (optional)" class="input text-sm">
                                <textarea name="comment" rows="3" placeholder="Tell others what you thought…" class="input text-sm"></textarea>
                                <button type="submit" class="btn btn-cut w-full justify-center !text-xs">Submit review</button>
                            </form>
                        </div>
                    @else
                        <div class="card rounded-xl p-5 text-sm text-ink-soft">
                            You can review this product after your order for it is placed.
                        </div>
                    @endif
                @else
                    <div class="card rounded-xl p-5 text-sm text-ink-soft">
                        <a href="{{ route('login') }}" class="text-accent hover:underline">Sign in</a> to write a review.
                    </div>
                @endauth
            </div>
        </section>

        @if ($related->isNotEmpty())
            <section class="mt-20 pt-12 border-t border-line" x-reveal>
                <h2 class="font-display text-2xl mb-6">You might also like</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-x-5 gap-y-10">
                    @foreach ($related as $rp)
                        <x-product-card :product="$rp" :wished="in_array($rp->id, $wishedIds ?? [])" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layout>
