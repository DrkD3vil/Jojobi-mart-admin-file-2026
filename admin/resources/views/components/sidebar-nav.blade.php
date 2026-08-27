{{-- Shared nav content for both the desktop and mobile sidebars.
     Extracted so a new nav item only needs to be added once. --}}
<nav class="space-y-1 px-4 mt-4">

    {{-- Today Dashboard --}}
    <a href="{{ route('dashboard.financial.today') }}"
        class="nav-link {{ request()->routeIs('dashboard.financial.today*') ? 'active' : '' }}"
        data-tooltip="Dashboard">
        <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
        <span class="nav-text ml-3">Dashboard</span>
    </a>

    {{-- Financial Analysis --}}
    <a href="{{ route('dashboard.financial') }}"
        class="nav-link {{ request()->routeIs('dashboard.financial') ? 'active' : '' }}"
        data-tooltip="Analysis">
        <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
        <span class="nav-text ml-3">Analysis</span>
    </a>

    {{-- Reports & Analytics --}}
    <a href="{{ route('dashboard.reports') }}"
        class="nav-link {{ request()->routeIs('dashboard.reports*') ? 'active' : '' }}"
        data-tooltip="Reports">
        <i data-lucide="trending-up" class="w-5 h-5"></i>
        <span class="nav-text ml-3">Reports</span>
    </a>

    <a href="{{ route('locations.index') }}"
        class="nav-link {{ request()->routeIs('locations.*') ? 'active' : '' }}" data-tooltip="Locations">
        <i data-lucide="map-pin" class="w-5 h-5"></i>
        <span class="nav-text ml-3">Locations</span>
    </a>

    <!-- Orders -->
    <div class="dropdown">

        <div class="nav-link dropdown-toggle" role="button" tabindex="0" aria-expanded="false"
            data-tooltip="Orders">

            <i data-lucide="shopping-cart" class="w-5 h-5"></i>

            <span class="nav-text ml-3">
                Orders
            </span>

            <i data-lucide="chevron-down" class="dropdown-chevron w-4 h-4 ml-auto"></i>

        </div>

        <div class="dropdown-content">

            <a href="{{ route('orders.index') }}"
                class="nav-link flex items-center {{ request()->routeIs('orders.index') ? 'active' : '' }}">
                <i data-lucide="clipboard-list" class="w-4 h-4 mr-3"></i>
                <span>All Orders</span>
                <span class="ml-auto text-xs text-[var(--muted-foreground)]">
                    {{ \App\Models\Order::count() }}
                </span>
            </a>

            <a href="{{ route('orders.pending') }}"
                class="nav-link flex items-center {{ request()->routeIs('orders.pending') ? 'active' : '' }}">
                <i data-lucide="clock-3" class="w-4 h-4 mr-3 text-yellow-500"></i>
                <span>Pending</span>
                <span class="ml-auto text-xs text-[var(--muted-foreground)]">
                    {{ \App\Models\Order::where('status', 'pending')->count() }}
                </span>
            </a>

            <a href="{{ route('orders.completed') }}"
                class="nav-link flex items-center {{ request()->routeIs('orders.completed') ? 'active' : '' }}">
                <i data-lucide="badge-check" class="w-4 h-4 mr-3 text-green-500"></i>
                <span>Completed</span>
                <span class="ml-auto text-xs text-[var(--muted-foreground)]">
                    {{ \App\Models\Order::where('status', 'completed')->count() }}
                </span>
            </a>

            <a href="{{ route('orders.paid') }}"
                class="nav-link flex items-center {{ request()->routeIs('orders.paid') ? 'active' : '' }}">
                <i data-lucide="wallet" class="w-4 h-4 mr-3 text-emerald-500"></i>
                <span>Paid</span>
                <span class="ml-auto text-xs text-[var(--muted-foreground)]">
                    {{ \App\Models\Order::where('status', 'paid')->count() }}
                </span>
            </a>

            <a href="{{ route('orders.refunded') }}"
                class="nav-link flex items-center {{ request()->routeIs('orders.refunded') ? 'active' : '' }}">
                <i data-lucide="rotate-ccw" class="w-4 h-4 mr-3 text-blue-500"></i>
                <span>Refunded</span>
                <span class="ml-auto text-xs text-[var(--muted-foreground)]">
                    {{ \App\Models\Order::where('status', 'refunded')->count() }}
                </span>
            </a>

            <a href="{{ route('orders.returned') }}"
                class="nav-link flex items-center {{ request()->routeIs('orders.returned') ? 'active' : '' }}">
                <i data-lucide="undo-2" class="w-4 h-4 mr-3 text-purple-500"></i>
                <span>Returned</span>
                <span class="ml-auto text-xs text-[var(--muted-foreground)]">
                    {{ \App\Models\Order::where('status', 'returned')->count() }}
                </span>
            </a>

            <a href="{{ route('orders.cancelled') }}"
                class="nav-link flex items-center {{ request()->routeIs('orders.cancelled') ? 'active' : '' }}">
                <i data-lucide="circle-x" class="w-4 h-4 mr-3 text-red-500"></i>
                <span>Cancelled</span>
                <span class="ml-auto text-xs text-[var(--muted-foreground)]">
                    {{ \App\Models\Order::where('status', 'cancelled')->count() }}
                </span>
            </a>

           <a href="{{ route('orders.trash') }}"
                class="nav-link flex items-center {{ request()->routeIs('orders.trash*') ? 'active' : '' }}">
                <i data-lucide="trash-2" class="w-4 h-4 mr-3 text-red-500"></i>
                <span>Trash</span>
                @php
                    $count = \App\Models\Order::onlyTrashed()->count();
                @endphp
                @if($count > 0)
                    <span class="ml-auto text-xs bg-red-500 text-white px-2 py-0.5 rounded-full">
                        {{ $count }}
                    </span>
                @endif
            </a>

        </div>

    </div>


    <a href="{{ route('expenses.index') }}"
        class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}" data-tooltip="Expenses">
        <i data-lucide="wallet" class="w-5 h-5"></i>
        <span class="nav-text ml-3">Expenses</span>
        <span class="nav-badge ml-auto bg-red-500/20 text-red-500 text-xs px-2 py-0.5 rounded-full">32</span>
    </a>

    <a href="{{ route('customers.index') }}"
        class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" data-tooltip="Customers">
        <i data-lucide="users" class="w-5 h-5"></i>
        <span class="nav-text ml-3">Customers</span>
    </a>

    <a href="{{ route('cart.index') }}" class="nav-link {{ request()->routeIs('cart.*') ? 'active' : '' }}"
        data-tooltip="Cart">
        <i data-lucide="shopping-cart" class="w-5 h-5"></i>
        <span class="nav-text ml-3">Cart</span>
    </a>

    <a href="{{ route('ai_assistant.index') }}" class="nav-link {{ request()->routeIs('ai_assistant.*') ? 'active' : '' }}"
        data-tooltip="AI Assistant">
        <i data-lucide="bot" class="w-5 h-5"></i>
        <span class="nav-text ml-3">AI Assistant</span>
    </a>

    <!-- Inventory Operations -->
    <div class="dropdown">
        <div class="nav-link dropdown-toggle" role="button" tabindex="0" aria-expanded="false"
            data-tooltip="Inventory Ops">
            <i data-lucide="repeat" class="w-5 h-5"></i>
            <span class="nav-text ml-3">Inventory Ops</span>
            <i data-lucide="chevron-down" class="dropdown-chevron w-4 h-4 ml-auto"></i>
        </div>

        <div class="dropdown-content">
            <a href="{{ route('returns.wizard') }}"
                class="nav-link flex items-center {{ request()->routeIs('returns.*') ? 'active' : '' }}">
                <i data-lucide="undo-2" class="w-4 h-4 mr-3"></i>
                <span>Return</span>
                <span
                    class="ml-auto text-xs text-[var(--muted-foreground)]">{{ \App\Models\ProductReturn::count() }}</span>
            </a>

            <a href="{{ route('exchanges.create') }}"
                class="nav-link flex items-center {{ request()->routeIs('exchanges.*') ? 'active' : '' }}">
                <i data-lucide="refresh-cw" class="w-4 h-4 mr-3"></i>
                <span>Exchange</span>
                <span
                    class="ml-auto text-xs text-[var(--muted-foreground)]">{{ \App\Models\Exchange::count() }}</span>
            </a>

            <a href="{{ route('transfers.create') }}"
                class="nav-link flex items-center {{ request()->routeIs('transfers.*') ? 'active' : '' }}">
                <i data-lucide="truck" class="w-4 h-4 mr-3"></i>
                <span>Stock Transfer</span>
                <span
                    class="ml-auto text-xs text-[var(--muted-foreground)]">{{ \App\Models\StockTransaction::where('type', 'TRANSFER')->count() }}</span>
            </a>

            <a href="{{ route('stock-ledger.index') }}"
                class="nav-link flex items-center {{ request()->routeIs('stock-ledger.*') ? 'active' : '' }}">
                <i data-lucide="book-open" class="w-4 h-4 mr-3"></i>
                <span>Stock Ledger</span>
                <span
                    class="ml-auto text-xs text-[var(--muted-foreground)]">{{ \App\Models\StockLedger::count() }}</span>
            </a>

            <a href="{{ route('product.gift-audit') }}"
                class="nav-link flex items-center {{ request()->routeIs('product.gift-audit*') ? 'active' : '' }}">
                <i data-lucide="gift" class="w-4 h-4 mr-3"></i>
                <span>Gift Audit</span>
            </a>
        </div>
    </div>

    <!-- Categories -->
    <div class="dropdown">
        <div class="nav-link dropdown-toggle" role="button" tabindex="0" aria-expanded="false"
            data-tooltip="Categories">
            <i data-lucide="tag" class="w-5 h-5"></i>
            <span class="nav-text ml-3">Categories</span>
            <i data-lucide="chevron-down" class="dropdown-chevron w-4 h-4 ml-auto"></i>
        </div>

        <div class="dropdown-content">
            <a href="{{ route('categories.index') }}"
                class="nav-link flex items-center {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i data-lucide="shirt" class="w-4 h-4 mr-3"></i>
                <span>Categories</span>
                <span
                    class="ml-auto text-xs text-[var(--muted-foreground)]">{{ \App\Models\Category::count() }}</span>
            </a>
        </div>
    </div>

    <a href="{{ route('brands.index') }}"
        class="nav-link {{ request()->routeIs('brands.*') ? 'active' : '' }}" data-tooltip="Brands">
        <i data-lucide="trending-up" class="w-5 h-5"></i>
        <span class="nav-text ml-3">Brands</span>
    </a>

    <!-- Products -->
    <div class="dropdown">
        <div class="nav-link dropdown-toggle" role="button" tabindex="0" aria-expanded="false"
            data-tooltip="Products">
            <i data-lucide="tag" class="w-5 h-5"></i>
            <span class="nav-text ml-3">Products</span>
            <i data-lucide="chevron-down" class="dropdown-chevron w-4 h-4 ml-auto"></i>
        </div>

        <div class="dropdown-content">
            <a href="{{ route('products.index') }}"
                class="nav-link flex items-center {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i data-lucide="package" class="w-4 h-4 mr-3"></i>
                <span>Products</span>
                <span class="ml-auto text-xs text-[var(--muted-foreground)]">{{ $productCount }}</span>
            </a>

            <a href="{{ route('product.images.all') }}"
                class="nav-link flex items-center {{ request()->routeIs('product.images.*') ? 'active' : '' }}">
                <i data-lucide="image" class="w-4 h-4 mr-3"></i>
                <span>Product Images</span>
                <span class="ml-auto text-xs text-[var(--muted-foreground)]">{{ $productImageCount }}</span>
            </a>

            <a href="{{ route('product.batches.all') }}"
                class="nav-link flex items-center {{ request()->routeIs('product.batches.*') ? 'active' : '' }}">
                <i data-lucide="layers" class="w-4 h-4 mr-3"></i>
                <span>Product Batches</span>
                <span class="ml-auto text-xs text-[var(--muted-foreground)]">{{ $productBatchCount }}</span>
            </a>

            <a href="{{ route('product.status.index') }}"
                class="nav-link flex items-center {{ request()->routeIs('product.status.*') ? 'active' : '' }}">
                <i data-lucide="layers" class="w-4 h-4 mr-3"></i>
                <span>Product Statuses</span>
                <span class="ml-auto text-xs text-[var(--muted-foreground)]">{{ $productStatusCount }}</span>
            </a>
        </div>
    </div>

    <!-- Access Control -->
    <div class="dropdown">
        <div class="nav-link dropdown-toggle" role="button" tabindex="0" aria-expanded="false"
            data-tooltip="Access Control">
            <i data-lucide="shield" class="w-5 h-5"></i>
            <span class="nav-text ml-3">Access Control</span>
            <i data-lucide="chevron-down" class="dropdown-chevron w-4 h-4 ml-auto"></i>
        </div>

        <div class="dropdown-content">
            <a href="{{ route('roles.index') }}"
                class="nav-link flex items-center {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                <i data-lucide="users" class="w-4 h-4 mr-3"></i>
                <span>Roles</span>
                @isset($roleCount)
                    <span class="ml-auto text-xs text-[var(--muted-foreground)]">{{ $roleCount }}</span>
                @endisset
            </a>

            <a href="{{ route('privileges.index') }}"
                class="nav-link flex items-center {{ request()->routeIs('privileges.*') ? 'active' : '' }}">
                <i data-lucide="key" class="w-4 h-4 mr-3"></i>
                <span>Privileges</span>
                @isset($privilegeCount)
                    <span class="ml-auto text-xs text-[var(--muted-foreground)]">{{ $privilegeCount }}</span>
                @endisset
            </a>

            <a href="{{ route('user.roles.index') }}"
                class="nav-link flex items-center {{ request()->routeIs('user.roles.*') ? 'active' : '' }}">
                <i data-lucide="users" class="w-4 h-4 mr-3"></i>
                <span>User Roles</span>
                @isset($roleCount)
                    <span class="ml-auto text-xs text-[var(--muted-foreground)]">{{ $roleCount }}</span>
                @endisset
            </a>

            <a href="{{ route('access_keys.index') }}"
                class="nav-link flex items-center {{ request()->routeIs('access_keys.*') ? 'active' : '' }}">
                <i data-lucide="shield-check" class="w-4 h-4 mr-3"></i>
                <span>Access Routes</span>
            </a>

            <a href="{{ route('rbac.audit_log') }}"
                class="nav-link flex items-center {{ request()->routeIs('rbac.audit_log') ? 'active' : '' }}">
                <i data-lucide="history" class="w-4 h-4 mr-3"></i>
                <span>Audit Log</span>
            </a>
        </div>
    </div>


    <!-- Settings -->
    <div class="dropdown">
        <div class="nav-link dropdown-toggle" role="button" tabindex="0" aria-expanded="false"
            data-tooltip="Settings">
            <i data-lucide="settings" class="w-5 h-5"></i>
            <span class="nav-text ml-3">Settings</span>
            <i data-lucide="chevron-down" class="dropdown-chevron w-4 h-4 ml-auto"></i>
        </div>

        <div class="dropdown-content">
            <!-- Profile Settings Link -->
            <a href="{{ route('profile.edit') }}"
                class="nav-link flex items-center {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                <i data-lucide="user" class="w-4 h-4 mr-3"></i>
                <span>Profile Settings</span>
            </a>

            <!-- Change Password (if needed) -->
            <a href="{{ route('password.change.form') }}"
                class="nav-link flex items-center {{ request()->routeIs('password.change.form') ? 'active' : '' }}">
                <i data-lucide="lock" class="w-4 h-4 mr-3"></i>
                <span>Change Password</span>
            </a>

            <!-- View Current User Profile (me route) -->
            <a href="{{ route('me') }}"
                class="nav-link flex items-center {{ request()->routeIs('me') ? 'active' : '' }}">
                <i data-lucide="user" class="w-4 h-4 mr-3"></i>
                <span>Current Profile</span>
            </a>

            <!-- Store Settings -->
            <a href="{{ route('settings.edit') }}"
                class="nav-link flex items-center {{ request()->routeIs('settings.edit') ? 'active' : '' }}">
                <i data-lucide="store" class="w-4 h-4 mr-3"></i>
                <span>Store Settings</span>
            </a>
        </div>
    </div>


    <a href="{{ route('help.index') }}" class="nav-link {{ request()->routeIs('help.index') ? 'active' : '' }}" data-tooltip="Help & Support">
        <i data-lucide="help-circle" class="w-5 h-5"></i>
        <span class="nav-text ml-3">Help &amp; Support</span>
    </a>

    <a href="{{ route('terms.index') }}" class="nav-link {{ request()->routeIs('terms.index') ? 'active' : '' }}" data-tooltip="Terms & Conditions">
        <i data-lucide="file-text" class="w-5 h-5"></i>
        <span class="nav-text ml-3">Terms &amp; Conditions</span>
    </a>

</nav>
