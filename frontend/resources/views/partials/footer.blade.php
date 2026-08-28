<footer class="border-t border-line bg-bg-2 mt-16">
    <div class="container-page py-14 grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
        <div class="lg:col-span-1">
            <span class="font-display font-semibold text-xl">JOJOBI MART</span>
            <p class="text-sm text-ink-soft mt-3 leading-relaxed max-w-xs">
                Everyday essentials, stocked fresh and delivered fast. One store, one shelf, always in stock.
            </p>
        </div>

        <div>
            <p class="label mb-4">Shop</p>
            <ul class="space-y-2.5 text-sm text-ink-soft">
                <li><a href="{{ route('products.index') }}" class="hover:text-ink transition">All products</a></li>
                <li><a href="{{ route('categories.index') }}" class="hover:text-ink transition">Categories</a></li>
                <li><a href="{{ route('brands.index') }}" class="hover:text-ink transition">Brands</a></li>
                <li><a href="{{ route('products.index', ['sort' => 'price_asc']) }}" class="hover:text-ink transition">Best value</a></li>
            </ul>
        </div>

        <div>
            <p class="label mb-4">Account</p>
            <ul class="space-y-2.5 text-sm text-ink-soft">
                @auth('customer')
                    <li><a href="{{ route('account.dashboard') }}" class="hover:text-ink transition">Dashboard</a></li>
                    <li><a href="{{ route('account.orders') }}" class="hover:text-ink transition">Order history</a></li>
                    <li><a href="{{ route('wishlist.index') }}" class="hover:text-ink transition">Wishlist</a></li>
                @else
                    <li><a href="{{ route('login') }}" class="hover:text-ink transition">Sign in</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-ink transition">Create account</a></li>
                @endauth
                <li><a href="{{ route('cart.index') }}" class="hover:text-ink transition">Cart</a></li>
            </ul>
        </div>

        <div>
            <p class="label mb-4">Stay in the loop</p>
            <p class="text-sm text-ink-soft mb-3">New stock and offers, once in a while — no spam.</p>
            <form
                method="POST" action="{{ route('newsletter.store') }}"
                x-data="{ sent: false }"
                @submit.prevent="
                    fetch($el.action, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' }, body: JSON.stringify({ email: $refs.email.value }) })
                        .then(r => r.json())
                        .then(res => { $store.toast.push(res.message, res.success ? 'good' : 'bad'); if (res.success) { sent = true; $refs.email.value = ''; } });
                "
                class="flex gap-2"
            >
                @csrf
                <input x-ref="email" type="email" name="email" required placeholder="you@example.com" class="input !bg-surface rounded-md text-sm">
                <button type="submit" class="btn btn-stamp !px-4 shrink-0" :disabled="sent">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </button>
            </form>
        </div>
    </div>

    <div class="border-t border-line">
        <div class="container-page py-5 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-ink-soft font-mono">
            <span>© {{ now()->year }} {{ config('store.name') }}. All rights reserved.</span>
            <div class="flex items-center gap-2 uppercase tracking-wider">
                <span class="badge badge-good">Cash on delivery</span>
                <span class="badge badge-warn">bKash</span>
                <span class="badge badge-warn">Nagad</span>
                <span class="badge badge-warn">Rocket</span>
            </div>
        </div>
    </div>
</footer>
