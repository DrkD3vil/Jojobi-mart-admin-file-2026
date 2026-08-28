<x-layout title="Home" description="Shop everyday essentials at JOJOBI MART — fresh stock, fair prices, fast delivery.">

    <section class="relative overflow-hidden border-b border-line">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10">
            <div class="absolute -top-24 -right-24 w-[26rem] h-[26rem] rounded-full bg-accent/10 blur-3xl"></div>
            <div class="absolute top-1/3 -left-28 w-72 h-72 rounded-full bg-teal/10 blur-3xl"></div>
        </div>

        <div class="container-page grid lg:grid-cols-2 gap-10 items-center py-16 sm:py-24">
            <div class="animate-fade-up" style="animation-delay:.05s">
                <p class="eyebrow">JOJOBI MART · Open now</p>
                <h1 class="font-display font-medium text-4xl sm:text-5xl lg:text-6xl leading-[1.05] mt-4">
                    Everyday essentials,<br> stocked fresh.
                </h1>
                <p class="text-ink-soft text-base sm:text-lg mt-5 max-w-md leading-relaxed">
                    One shelf, always in stock. Browse {{ $productCount }}+ products
                    across home, drinks and pantry — with cash on delivery.
                </p>
                <div class="flex flex-wrap gap-3 mt-8">
                    <a href="{{ route('products.index') }}" class="btn btn-stamp">Shop all products</a>
                    <a href="{{ route('categories.index') }}" class="btn btn-cut">Browse categories</a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-line border-t border-line mt-10 pt-6 max-w-lg">
                    <div class="pr-4">
                        <p class="price text-2xl">24h</p>
                        <p class="text-xs text-ink-soft mt-0.5">Order to doorstep</p>
                    </div>
                    <div class="px-4">
                        <p class="price text-2xl">100%</p>
                        <p class="text-xs text-ink-soft mt-0.5">Cash on delivery</p>
                    </div>
                    <div class="px-4">
                        <p class="price text-2xl">★ 4.8</p>
                        <p class="text-xs text-ink-soft mt-0.5">Customer rated</p>
                    </div>
                    <div class="px-4 col-span-2 sm:col-span-1">
                        <p class="price text-2xl">{{ number_format($productCount) }}+</p>
                        <p class="text-xs text-ink-soft mt-0.5">Products in stock</p>
                    </div>
                </div>
            </div>

            <div class="relative animate-fade-up" style="animation-delay:.2s">
                <div class="aspect-[4/5] rounded-2xl bg-bg-2 overflow-hidden card">
                    @php($heroImage = $featured->flatMap->images->first())
                    <img
                        src="{{ $heroImage ? asset('storage/' . $heroImage->image_path) : 'https://placehold.co/640x800/EAE3D2/5A6570?text=JOJOBI+MART' }}"
                        alt="JOJOBI MART" class="w-full h-full object-cover"
                    >
                </div>
                <div class="absolute -bottom-6 -left-6 hidden sm:block card rounded-xl p-4 w-48">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-teal animate-pulse-dot"></span>
                        <span class="font-mono text-[10px] uppercase tracking-widest text-ink-soft">Live stock</span>
                    </div>
                    <p class="text-sm mt-2">Updated the moment an order ships.</p>
                </div>
                <div class="absolute -top-4 -right-4 hidden sm:flex card rounded-full pl-2 pr-3.5 py-2 items-center gap-1.5">
                    <span class="text-accent">★</span>
                    <span class="text-xs font-medium">4.8 rated</span>
                </div>
            </div>
        </div>
    </section>

    @if ($categories->isNotEmpty())
        <section class="container-page py-14" x-reveal>
            <div class="flex items-end justify-between mb-6">
                <div>
                    <p class="eyebrow">Shop by</p>
                    <h2 class="font-display text-2xl sm:text-3xl mt-1">Category</h2>
                </div>
                <a href="{{ route('categories.index') }}" class="text-sm text-ink-soft hover:text-ink flex items-center gap-1">
                    View all
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach ($categories as $cat)
                    <a href="{{ route('categories.show', $cat) }}" class="group hover-lift card rounded-xl p-5 flex flex-col items-center text-center gap-3">
                        @if ($loop->iteration % 2 === 0)
                            <div class="w-12 h-12 rounded-full bg-teal/10 flex items-center justify-center text-teal group-hover:bg-teal/20 transition">
                        @else
                            <div class="w-12 h-12 rounded-full bg-accent/10 flex items-center justify-center text-accent group-hover:bg-accent group-hover:text-accent-ink transition">
                        @endif
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M3.5 7.5L12 3l8.5 4.5L12 12 3.5 7.5z"/><path d="M3.5 7.5v9L12 21l8.5-4.5v-9"/></svg>
                        </div>
                        <span class="text-sm font-medium">{{ $cat->name }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if ($featured->isNotEmpty())
        <section class="container-page py-14 border-t border-line" x-reveal>
            <div class="flex items-end justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div>
                        <p class="eyebrow">Just in</p>
                        <h2 class="font-display text-2xl sm:text-3xl mt-1">New arrivals</h2>
                    </div>
                    <span class="badge badge-good">{{ $featured->count() }} new</span>
                </div>
                <a href="{{ route('products.index') }}" class="text-sm text-ink-soft hover:text-ink hidden sm:block">Shop all →</a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-x-5 gap-y-8">
                @foreach ($featured as $product)
                    <x-product-card :product="$product" :wished="in_array($product->id, auth('customer')->user()?->wishlist->pluck('product_id')->all() ?? [])" />
                @endforeach
            </div>
        </section>
    @endif

    @if ($onSale->isNotEmpty())
        <section class="bg-bg-2 border-y-2 border-accent py-14" x-reveal>
            <div class="container-page">
                <div class="flex items-end justify-between mb-6">
                    <div class="flex items-center gap-2.5">
                        <span class="w-9 h-9 rounded-full bg-accent text-accent-ink flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M13 2L4.5 14h5.4l-1.4 8L19.5 9h-5.4z"/></svg>
                        </span>
                        <div>
                            <p class="font-mono text-[11px] uppercase tracking-widest text-accent font-semibold">Limited time</p>
                            <h2 class="font-display text-2xl sm:text-3xl mt-1">On sale right now</h2>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-x-5 gap-y-8">
                    @foreach ($onSale as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($brands->isNotEmpty())
        <section class="container-page py-14 border-t border-line" x-reveal>
            <p class="eyebrow mb-6">Brands we stock</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 rounded-xl border border-line divide-x divide-y divide-line overflow-hidden">
                @foreach ($brands as $brand)
                    <a href="{{ route('brands.show', $brand) }}" class="flex items-center justify-center text-center px-4 py-6 text-sm font-medium hover:bg-bg-2 hover:text-accent transition">{{ $brand->name }}</a>
                @endforeach
            </div>
        </section>
    @endif

    <section class="border-t border-line" x-reveal>
        <div class="container-page py-14 grid sm:grid-cols-2 lg:grid-cols-4 gap-8 text-center sm:text-left">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-3">
                <span class="w-11 h-11 rounded-full bg-accent/10 flex items-center justify-center text-accent shrink-0">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="1.5" y="7" width="13" height="10" rx="1"/><path d="M14.5 10h4l3 3v4h-7z"/><circle cx="6" cy="18.5" r="1.6"/><circle cx="17.5" cy="18.5" r="1.6"/></svg>
                </span>
                <div>
                    <p class="font-medium text-sm">Fast local delivery</p>
                    <p class="text-xs text-ink-soft mt-1">Ordered today, at your door tomorrow.</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-3">
                <span class="w-11 h-11 rounded-full bg-accent/10 flex items-center justify-center text-accent shrink-0">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                </span>
                <div>
                    <p class="font-medium text-sm">Pay your way</p>
                    <p class="text-xs text-ink-soft mt-1">Cash on delivery, bKash, Nagad or Rocket.</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-3">
                <span class="w-11 h-11 rounded-full bg-accent/10 flex items-center justify-center text-accent shrink-0">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M4 10a8 8 0 0114-5.3M20 14a8 8 0 01-14 5.3"/><path d="M18 3v5h-5M6 21v-5h5"/></svg>
                </span>
                <div>
                    <p class="font-medium text-sm">Easy returns</p>
                    <p class="text-xs text-ink-soft mt-1">Not right? We'll sort it out, no fuss.</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-3">
                <span class="w-11 h-11 rounded-full bg-accent/10 flex items-center justify-center text-accent shrink-0">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M20 7L9 18l-5-5"/></svg>
                </span>
                <div>
                    <p class="font-medium text-sm">{{ number_format($productCount) }}+ products</p>
                    <p class="text-xs text-ink-soft mt-1">Always fresh stock, never stale listings.</p>
                </div>
            </div>
        </div>
    </section>

</x-layout>
