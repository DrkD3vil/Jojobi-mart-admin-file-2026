<div
    x-data="cartDrawer()"
    @open-cart-drawer.window="openAndLoad()"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50"
>
    <div x-show="open" x-transition.opacity @click="open = false" class="absolute inset-0 bg-ink/50 backdrop-blur-sm"></div>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="absolute right-0 top-0 bottom-0 w-full sm:w-[420px] bg-surface flex flex-col"
        @keydown.escape.window="open = false"
    >
        <div class="flex items-center justify-between p-4 border-b border-line">
            <span class="font-display font-semibold text-lg">Your cart</span>
            <button @click="open = false" class="w-8 h-8 flex items-center justify-center rounded-full text-ink-soft" aria-label="Close cart">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto">
            <template x-if="loading">
                <div class="p-4 space-y-3">
                    <div class="skeleton h-16 rounded-lg"></div>
                    <div class="skeleton h-16 rounded-lg"></div>
                </div>
            </template>

            <template x-if="!loading && items.length === 0">
                <div class="p-10 text-center">
                    <svg class="w-10 h-10 mx-auto text-ink-soft mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="9.5" cy="20.5" r="1.3"/><circle cx="18" cy="20.5" r="1.3"/><path d="M2.5 3h2.6l2.3 12.2a2 2 0 002 1.6h8a2 2 0 002-1.8L21 7.5H6.3"/></svg>
                    <p class="text-sm text-ink-soft mb-4">Your cart is empty.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-cut !text-xs">Start shopping</a>
                </div>
            </template>

            <ul class="divide-y divide-line" x-show="!loading && items.length > 0">
                <template x-for="item in items" :key="item.id">
                    <li class="flex gap-3 p-4">
                        <a :href="item.url" class="shrink-0">
                            <img :src="item.image ?? 'https://placehold.co/96x96?text=%20'" class="w-16 h-16 rounded-lg object-cover bg-bg-2">
                        </a>
                        <div class="flex-1 min-w-0">
                            <a :href="item.url" class="text-sm font-medium line-clamp-2 hover:text-accent" x-text="item.name"></a>
                            <p class="text-xs text-ink-soft font-mono mt-1" x-text="'৳' + item.unit_price.toFixed(2) + ' / ' + item.unit"></p>
                            <div class="flex items-center justify-between mt-2">
                                <div class="flex items-center border border-line rounded-full">
                                    <button @click="changeQty(item, item.quantity - 1)" class="w-6 h-6 flex items-center justify-center text-ink-soft hover:text-ink">−</button>
                                    <span class="w-8 text-center text-xs font-mono" x-text="item.quantity"></span>
                                    <button @click="changeQty(item, item.quantity + 1)" class="w-6 h-6 flex items-center justify-center text-ink-soft hover:text-ink">+</button>
                                </div>
                                <span class="price text-sm" x-text="'৳' + item.total_price.toFixed(2)"></span>
                            </div>
                        </div>
                        <button @click="removeItem(item)" class="shrink-0 text-ink-soft hover:text-danger self-start" aria-label="Remove">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2m3 0l-1 14a2 2 0 01-2 2H7a2 2 0 01-2-2L4 6h16z"/></svg>
                        </button>
                    </li>
                </template>
            </ul>
        </div>

        <div class="border-t border-line p-4 space-y-3" x-show="!loading && items.length > 0">
            <div class="flex items-center justify-between text-sm">
                <span class="text-ink-soft">Subtotal</span>
                <span class="price text-lg" x-text="'৳' + total.toFixed(2)"></span>
            </div>
            <a href="{{ route('cart.index') }}" class="btn btn-cut w-full justify-center">View cart</a>
            <a href="{{ route('checkout.index') }}" class="btn btn-stamp w-full justify-center">{{ auth('customer')->check() ? 'Checkout' : 'Sign in to checkout' }}</a>
        </div>
    </div>
</div>

<script>
    function cartDrawer() {
        return {
            open: false, loading: false, items: [], total: 0,
            openAndLoad() {
                this.open = true;
                this.load();
            },
            load() {
                this.loading = true;
                fetch('{{ route('cart.mini') }}')
                    .then(r => r.json())
                    .then(data => {
                        this.items = data.items;
                        this.total = data.total;
                        this.$store.cart.set(data.count);
                        this.loading = false;
                    });
            },
            changeQty(item, qty) {
                if (qty < 0) return;
                fetch(`/cart/${item.id}`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                    body: JSON.stringify({ quantity: qty }),
                })
                    .then(r => r.json())
                    .then(res => {
                        if (!res.success) { this.$store.toast.push(res.message, 'bad'); return; }
                        this.load();
                    });
            },
            removeItem(item) {
                fetch(`/cart/${item.id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                }).then(() => this.load());
            },
        };
    }
</script>
