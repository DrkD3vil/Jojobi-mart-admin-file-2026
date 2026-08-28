@php($customer = auth('customer')->user())

<div class="bg-ink text-bg overflow-hidden">
    <div class="container-page py-2 text-[11px] font-mono uppercase tracking-widest whitespace-nowrap overflow-hidden">
        <div class="flex animate-marquee w-max gap-16">
            @for ($i = 0; $i < 2; $i++)
                <span class="flex gap-16 shrink-0">
                    <span>Free delivery across Dhaka on orders over ৳1000</span>
                    <span>New arrivals every week</span>
                    <span>Cash on delivery available</span>
                    <span>{{ config('store.name') }} — everyday essentials</span>
                </span>
            @endfor
        </div>
    </div>
</div>

<header
    x-data="{ searchOpen: false, accountOpen: false }"
    @click.outside="accountOpen = false"
    class="sticky top-0 z-40 border-b border-line bg-bg/90 backdrop-blur"
>
    <div class="container-page flex items-center gap-4 py-4">
        <button
            @click="$dispatch('open-mobile-menu')"
            class="lg:hidden shrink-0 w-9 h-9 flex items-center justify-center rounded-md border border-line text-ink"
            aria-label="Open menu"
        >
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
        </button>

        <a href="{{ route('home') }}" class="shrink-0 flex items-baseline gap-2">
            <span class="font-display font-semibold text-xl sm:text-2xl">JOJOBI</span>
            <span class="hidden sm:inline font-mono text-[10px] tracking-widest text-accent uppercase">Mart</span>
        </a>

        <nav class="hidden lg:flex items-center gap-6 ml-4 font-medium text-sm">
            <a href="{{ route('products.index') }}" class="text-ink-soft hover:text-ink transition">Shop</a>
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button @click="open = !open" class="flex items-center gap-1 text-ink-soft hover:text-ink transition">
                    Categories
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div x-show="open" x-transition x-cloak class="absolute left-0 top-full mt-2 w-56 card rounded-lg p-2">
                    @foreach ($navCategories ?? [] as $cat)
                        <a href="{{ route('categories.show', $cat) }}" class="block px-3 py-2 rounded-md text-sm text-ink-soft hover:text-ink hover:bg-bg-2 transition">{{ $cat->name }}</a>
                    @endforeach
                    <a href="{{ route('categories.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-accent hover:bg-bg-2 transition">All categories →</a>
                </div>
            </div>
            <a href="{{ route('brands.index') }}" class="text-ink-soft hover:text-ink transition">Brands</a>
        </nav>

        <div class="hidden md:flex flex-1 max-w-md ml-auto" x-data="productSearch()">
            <div class="relative w-full">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-soft pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="10.5" cy="10.5" r="6.5"/><path d="M20 20l-4.8-4.8"/></svg>
                <input
                    type="text" x-model="q" @input.debounce.300ms="run" @focus="focused = true"
                    placeholder="Search products…"
                    class="input pl-9 !bg-bg-2 rounded-full text-sm"
                    autocomplete="off"
                >
                <div
                    x-show="focused && q.length > 1" x-transition x-cloak @click.outside="focused = false"
                    class="absolute left-0 right-0 top-full mt-2 card rounded-lg overflow-hidden max-h-96 overflow-y-auto z-50"
                >
                    <template x-if="loading">
                        <div class="p-4 text-sm text-ink-soft font-mono">Searching…</div>
                    </template>
                    <template x-if="!loading && results.length === 0 && q.length > 1">
                        <div class="p-4 text-sm text-ink-soft">No products match "<span x-text="q"></span>".</div>
                    </template>
                    <template x-for="r in results" :key="r.id">
                        <a :href="r.url" class="flex items-center gap-3 p-3 hover:bg-bg-2 transition">
                            <img :src="r.image ?? 'https://placehold.co/64x64?text=%20'" class="w-10 h-10 rounded object-cover shrink-0 bg-bg-2">
                            <span class="flex-1 text-sm truncate" x-text="r.name"></span>
                            <span class="price text-sm shrink-0" x-text="r.price ? '৳' + r.price : ''"></span>
                        </a>
                    </template>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-1 sm:gap-2 ml-auto md:ml-0 shrink-0">
            <button
                @click="searchOpen = !searchOpen"
                class="md:hidden w-9 h-9 flex items-center justify-center rounded-full text-ink-soft hover:text-ink"
                aria-label="Search"
            >
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="10.5" cy="10.5" r="6.5"/><path d="M20 20l-4.8-4.8"/></svg>
            </button>

            <div class="hidden sm:flex border border-line rounded-full overflow-hidden font-mono text-[10px] tracking-widest">
                <button @click="$store.theme.set('light')" :class="$store.theme.mode === 'light' ? 'bg-accent text-accent-ink font-semibold' : 'text-ink-soft'" class="px-2.5 py-1.5">LIGHT</button>
                <button @click="$store.theme.set('dark')" :class="$store.theme.mode === 'dark' ? 'bg-accent text-accent-ink font-semibold' : 'text-ink-soft'" class="px-2.5 py-1.5">DARK</button>
            </div>

            @auth('customer')
                <div class="relative">
                    <button @click="accountOpen = !accountOpen" class="w-9 h-9 flex items-center justify-center rounded-full bg-teal text-bg font-mono text-xs font-semibold">
                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                    </button>
                    <div x-show="accountOpen" x-transition x-cloak class="absolute right-0 top-full mt-2 w-52 card rounded-lg p-2">
                        <div class="px-3 py-2 border-b border-line mb-1">
                            <p class="text-sm font-medium truncate">{{ $customer->name }}</p>
                            <p class="text-xs text-ink-soft font-mono">{{ $customer->reward_points ?? 0 }} pts</p>
                        </div>
                        <a href="{{ route('account.dashboard') }}" class="block px-3 py-2 rounded-md text-sm text-ink-soft hover:text-ink hover:bg-bg-2">Dashboard</a>
                        <a href="{{ route('account.orders') }}" class="block px-3 py-2 rounded-md text-sm text-ink-soft hover:text-ink hover:bg-bg-2">My orders</a>
                        <a href="{{ route('wishlist.index') }}" class="block px-3 py-2 rounded-md text-sm text-ink-soft hover:text-ink hover:bg-bg-2">Wishlist</a>
                        <a href="{{ route('account.profile') }}" class="block px-3 py-2 rounded-md text-sm text-ink-soft hover:text-ink hover:bg-bg-2">Profile settings</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="w-full text-left px-3 py-2 rounded-md text-sm text-danger hover:bg-bg-2">Sign out</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="w-9 h-9 flex items-center justify-center rounded-full text-ink-soft hover:text-ink" aria-label="Sign in">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-3.5 3.5-6 8-6s8 2.5 8 6"/></svg>
                </a>
            @endauth

            @auth('customer')
                <a href="{{ route('wishlist.index') }}" class="hidden sm:flex relative w-9 h-9 items-center justify-center rounded-full text-ink-soft hover:text-ink" aria-label="Wishlist">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-7-4.6-9.5-9C.8 8.4 2.4 4.5 6 4c2-.3 3.7.8 6 3 2.3-2.2 4-3.3 6-3 3.6.5 5.2 4.4 3.5 8-2.5 4.4-9.5 9-9.5 9z"/></svg>
                    @if (($wishlistCount ?? 0) > 0)
                        <span class="absolute -top-1 -right-1 bg-accent text-accent-ink text-[9px] font-mono font-bold rounded-full w-4 h-4 flex items-center justify-center">{{ $wishlistCount }}</span>
                    @endif
                </a>
            @endauth

            <button @click="$dispatch('open-cart-drawer')" class="relative w-9 h-9 flex items-center justify-center rounded-full text-ink-soft hover:text-ink" aria-label="Cart">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9.5" cy="20.5" r="1.3"/><circle cx="18" cy="20.5" r="1.3"/><path d="M2.5 3h2.6l2.3 12.2a2 2 0 002 1.6h8a2 2 0 002-1.8L21 7.5H6.3"/></svg>
                <span
                    x-show="$store.cart.count > 0" x-cloak
                    x-text="$store.cart.count"
                    class="absolute -top-1 -right-1 bg-accent text-accent-ink text-[9px] font-mono font-bold rounded-full w-4 h-4 flex items-center justify-center"
                ></span>
            </button>
        </div>
    </div>

    <div x-show="searchOpen" x-transition x-cloak class="md:hidden border-t border-line px-4 py-3" x-data="productSearch()">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-soft" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="10.5" cy="10.5" r="6.5"/><path d="M20 20l-4.8-4.8"/></svg>
            <input type="text" x-model="q" @input.debounce.300ms="run" placeholder="Search products…" class="input pl-9 rounded-full text-sm" autocomplete="off">
        </div>
        <div class="mt-2 space-y-1" x-show="q.length > 1">
            <template x-for="r in results" :key="r.id">
                <a :href="r.url" class="flex items-center gap-3 p-2 rounded-lg hover:bg-bg-2">
                    <img :src="r.image ?? 'https://placehold.co/64x64?text=%20'" class="w-9 h-9 rounded object-cover bg-bg-2">
                    <span class="flex-1 text-sm truncate" x-text="r.name"></span>
                    <span class="price text-sm" x-text="r.price ? '৳' + r.price : ''"></span>
                </a>
            </template>
        </div>
    </div>
</header>

<script>
    function productSearch() {
        return {
            q: '', results: [], loading: false, focused: false,
            run() {
                if (this.q.length < 2) { this.results = []; return; }
                this.loading = true;
                fetch('{{ route('products.search') }}?q=' + encodeURIComponent(this.q))
                    .then(r => r.json())
                    .then(data => { this.results = data; this.loading = false; })
                    .catch(() => { this.loading = false; });
            },
        };
    }
</script>
