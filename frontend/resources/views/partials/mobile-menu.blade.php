@php($customer = auth('customer')->user())

<div
    x-data="{ open: false }"
    @open-mobile-menu.window="open = true"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 lg:hidden"
>
    <div
        x-show="open" x-transition.opacity @click="open = false"
        class="absolute inset-0 bg-ink/50 backdrop-blur-sm"
    ></div>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="absolute left-0 top-0 bottom-0 w-[85%] max-w-sm bg-surface flex flex-col"
        @keydown.escape.window="open = false"
    >
        <div class="flex items-center justify-between p-4 border-b border-line">
            <span class="font-display font-semibold text-lg">JOJOBI MART</span>
            <button @click="open = false" class="w-8 h-8 flex items-center justify-center rounded-full text-ink-soft" aria-label="Close menu">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto p-4 space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-bg-2">Home</a>
            <a href="{{ route('products.index') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-bg-2">Shop all</a>

            <p class="label px-3 pt-4 pb-1">Categories</p>
            @foreach ($navCategories ?? [] as $cat)
                <a href="{{ route('categories.show', $cat) }}" class="block px-3 py-2.5 rounded-lg text-sm text-ink-soft hover:text-ink hover:bg-bg-2">{{ $cat->name }}</a>
            @endforeach

            <a href="{{ route('brands.index') }}" class="block px-3 py-2.5 mt-4 rounded-lg text-sm font-medium hover:bg-bg-2 border-t border-line pt-4">Brands</a>

            @auth('customer')
                <div class="border-t border-line mt-4 pt-4 space-y-1">
                    <a href="{{ route('account.dashboard') }}" class="block px-3 py-2.5 rounded-lg text-sm hover:bg-bg-2">My account</a>
                    <a href="{{ route('account.orders') }}" class="block px-3 py-2.5 rounded-lg text-sm hover:bg-bg-2">My orders</a>
                    <a href="{{ route('wishlist.index') }}" class="block px-3 py-2.5 rounded-lg text-sm hover:bg-bg-2">Wishlist</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="w-full text-left px-3 py-2.5 rounded-lg text-sm text-danger hover:bg-bg-2">Sign out</button>
                    </form>
                </div>
            @else
                <div class="border-t border-line mt-4 pt-4 flex gap-2">
                    <a href="{{ route('login') }}" class="btn btn-cut flex-1 justify-center !text-xs">Sign in</a>
                    <a href="{{ route('register') }}" class="btn btn-stamp flex-1 justify-center !text-xs">Create account</a>
                </div>
            @endauth
        </nav>

        <div class="p-4 border-t border-line flex items-center justify-between">
            <span class="label">Theme</span>
            <div class="flex border border-line rounded-full overflow-hidden font-mono text-[10px] tracking-widest">
                <button @click="$store.theme.set('light')" :class="$store.theme.mode === 'light' ? 'bg-accent text-accent-ink font-semibold' : 'text-ink-soft'" class="px-3 py-1.5">LIGHT</button>
                <button @click="$store.theme.set('dark')" :class="$store.theme.mode === 'dark' ? 'bg-accent text-accent-ink font-semibold' : 'text-ink-soft'" class="px-3 py-1.5">DARK</button>
            </div>
        </div>
    </div>
</div>
