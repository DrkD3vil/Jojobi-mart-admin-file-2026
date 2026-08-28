@php($current = \Illuminate\Support\Facades\Route::currentRouteName())

<nav class="card rounded-2xl p-3 flex lg:flex-col gap-1 overflow-x-auto lg:overflow-visible">
    @foreach ([
        ['account.dashboard', 'Dashboard', 'grid'],
        ['account.orders', 'Orders', 'box'],
        ['wishlist.index', 'Wishlist', 'heart'],
        ['account.profile', 'Profile', 'user'],
    ] as [$route, $label, $icon])
        <a
            href="{{ route($route) }}"
            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm whitespace-nowrap transition {{ $current === $route || str_starts_with($current, $route) ? 'bg-accent text-accent-ink font-medium' : 'text-ink-soft hover:bg-bg-2 hover:text-ink' }}"
        >
            @switch($icon)
                @case('grid')
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                    @break
                @case('box')
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M3.5 7.5L12 3l8.5 4.5L12 12 3.5 7.5z"/><path d="M3.5 7.5v9L12 21l8.5-4.5v-9"/></svg>
                    @break
                @case('heart')
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M12 21s-7-4.6-9.5-9C.8 8.4 2.4 4.5 6 4c2-.3 3.7.8 6 3 2.3-2.2 4-3.3 6-3 3.6.5 5.2 4.4 3.5 8-2.5 4.4-9.5 9-9.5 9z"/></svg>
                    @break
                @default
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-3.5 3.5-6 8-6s8 2.5 8 6"/></svg>
            @endswitch
            {{ $label }}
        </a>
    @endforeach
</nav>
