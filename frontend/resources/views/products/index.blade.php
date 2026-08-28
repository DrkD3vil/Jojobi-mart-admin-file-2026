<x-layout :title="$pageTitle" description="Browse every product in stock at JOJOBI MART.">

    <div class="container-page py-10">
        <div class="mb-8">
            <p class="eyebrow">Shop</p>
            <h1 class="font-display text-3xl sm:text-4xl mt-1">{{ $pageTitle }}</h1>
        </div>

        <div class="grid lg:grid-cols-[240px_1fr] gap-10">
            <aside class="lg:sticky lg:top-24 self-start" x-data="{ filtersOpen: false }">
                <button @click="filtersOpen = !filtersOpen" class="lg:hidden btn btn-cut w-full justify-center mb-4">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
                    Filters
                </button>

                <form
                    method="GET" action="{{ route('products.index') }}"
                    class="space-y-7 lg:!block"
                    x-show="filtersOpen" x-cloak
                >
                    @if (request('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif

                    <div>
                        <p class="label mb-3">Category</p>
                        <ul class="space-y-2 text-sm">
                            <li><a href="{{ route('products.index', request()->except('category', 'page')) }}" class="{{ !request('category') ? 'text-accent font-medium' : 'text-ink-soft hover:text-ink' }}">All categories</a></li>
                            @foreach ($categories as $cat)
                                <li><a href="{{ route('products.index', array_merge(request()->except('page'), ['category' => $cat->id])) }}" class="{{ (int) request('category') === $cat->id ? 'text-accent font-medium' : 'text-ink-soft hover:text-ink' }}">{{ $cat->name }}</a></li>
                            @endforeach
                        </ul>
                    </div>

                    <div>
                        <p class="label mb-3">Brand</p>
                        <ul class="space-y-2 text-sm">
                            <li><a href="{{ route('products.index', request()->except('brand', 'page')) }}" class="{{ !request('brand') ? 'text-accent font-medium' : 'text-ink-soft hover:text-ink' }}">All brands</a></li>
                            @foreach ($brands as $brand)
                                <li><a href="{{ route('products.index', array_merge(request()->except('page'), ['brand' => $brand->id])) }}" class="{{ (int) request('brand') === $brand->id ? 'text-accent font-medium' : 'text-ink-soft hover:text-ink' }}">{{ $brand->name }}</a></li>
                            @endforeach
                        </ul>
                    </div>

                    <div>
                        <p class="label mb-3">Price (৳)</p>
                        <div class="flex items-center gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="input !py-2 text-sm">
                            <span class="text-ink-soft">–</span>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="input !py-2 text-sm">
                        </div>
                    </div>

                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                    <button type="submit" class="btn btn-stamp w-full justify-center">Apply filters</button>
                    @if (request()->anyFilled(['category', 'brand', 'min_price', 'max_price', 'q']))
                        <a href="{{ route('products.index') }}" class="block text-center text-xs text-ink-soft hover:text-ink">Clear all</a>
                    @endif
                </form>
            </aside>

            <div>
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-line">
                    <p class="text-sm text-ink-soft">{{ $products->total() }} products</p>
                    <form method="GET" x-data @change="$el.submit()">
                        @foreach (request()->except('sort', 'page') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <select name="sort" class="input !py-2 !w-auto text-sm">
                            <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Newest</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: low to high</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: high to low</option>
                            <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name A–Z</option>
                        </select>
                    </form>
                </div>

                @include('partials.product-grid')
            </div>
        </div>
    </div>

</x-layout>
