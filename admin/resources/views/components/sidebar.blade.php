<!-- Desktop Sidebar -->
<div id="sidebar-desktop" class="sidebar sidebar-expanded flex-shrink-0 z-50">

    <!-- Header -->
    <div class="p-6 pb-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div
                class="w-10 h-10 rounded-xl bg-gradient-to-r from-[var(--chart-1)] to-[var(--chart-4)] flex items-center justify-center">
                <i data-lucide="shopping-bag" class="w-5 h-5 text-white"></i>
            </div>

            <span
                class="logo-full text-xl font-bold bg-gradient-to-r from-[var(--chart-1)] to-[var(--chart-4)] bg-clip-text text-transparent">
                ShopSphere
            </span>
            <span
                class="logo-icon text-xl font-bold bg-gradient-to-r from-[var(--chart-1)] to-[var(--chart-4)] bg-clip-text text-transparent hidden">
                SS
            </span>
        </div>
    </div>

    <!-- ✅ Scroll Area -->
    <div class="sidebar-body custom-scrollbar">
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

            <a href="{{ route('locations.index') }}"
                class="nav-link {{ request()->routeIs('locations.*') ? 'active' : '' }}" data-tooltip="Locations">
                <i data-lucide="map-pin" class="w-5 h-5"></i>
                <span class="nav-text ml-3">Locations</span>
            </a>

            <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}"
                data-tooltip="Orders">
                <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                <span class="nav-text ml-3">Orders</span>
                <span class="nav-badge ml-auto bg-red-500/20 text-red-500 text-xs px-2 py-0.5 rounded-full">32</span>
            </a>

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
                </div>
            </div>


            <a href="#" class="nav-link" data-tooltip="Help & Support">
                <i data-lucide="help-circle" class="w-5 h-5"></i>
                <span class="nav-text ml-3">Help &amp; Support</span>
            </a>

        </nav>
    </div>

    <!-- ✅ Fixed Footer -->
    <div class="sidebar-footer p-4 border-t" style="border-color: var(--sidebar-border);">
        <div class="flex items-center space-x-3 p-3 rounded-xl" style="background-color: var(--sidebar-accent);">
            <!-- Display user's profile image from the KycUser model -->
            <img class="h-10 w-10 rounded-full object-cover border-2 border-[var(--sidebar-primary)]/50"
                src="{{ asset('storage/' . (Auth::user()->kycDetail ? Auth::user()->kycDetail->profile_image : 'default-profile.jpg')) }}"
                alt="{{ Auth::user()->name }} Profile Image">

            <div class="user-info flex-1 min-w-0">
                <p class="text-sm font-medium truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs truncate" style="color: var(--muted-foreground);">
                    @foreach (Auth::user()->roles as $role)
                        <p>{{ $role->name }}</p>
                    @endforeach


                </p>
            </div>

            <button onclick="event.preventDefault(); document.getElementById('tyro-logout-form').submit();"
                class="text-[var(--muted-foreground)] hover:text-[var(--sidebar-primary)] transition-colors duration-200"
                title="Logout">
                <i data-lucide="log-out" class="w-4 h-4"></i>
            </button>

            <!-- Logout form -->
            <form id="tyro-logout-form" method="POST" action="/logout" class="hidden">
                @csrf
            </form>
        </div>
    </div>

</div>



<!-- Mobile Sidebar -->
<div id="sidebar-mobile" class="sidebar flex-shrink-0 z-50">

    <!-- Header -->
    <div class="p-6 pb-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div
                class="w-10 h-10 rounded-xl bg-gradient-to-r from-[var(--chart-1)] to-[var(--chart-4)] flex items-center justify-center">
                <i data-lucide="shopping-bag" class="w-5 h-5 text-white"></i>
            </div>
            <span
                class="text-xl font-bold bg-gradient-to-r from-[var(--chart-1)] to-[var(--chart-4)] bg-clip-text text-transparent">
                ShopSphere
            </span>
        </div>

        <button id="sidebar-close-mobile"
            class="text-[var(--muted-foreground)] hover:text-[var(--sidebar-primary)] transition-colors duration-200">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>
    </div>

    <!-- ✅ Scroll Area -->
    <div class="sidebar-body custom-scrollbar">
        <nav class="space-y-1 px-4 mt-4">

            <a href="{{ route('dashboard.financial.today') }}"
                class="nav-link {{ request()->routeIs('dashboard.financial.today*') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span class="nav-text ml-3">Dashboard</span>
            </a>

            <a href="{{ route('dashboard.financial') }}"
                class="nav-link {{ request()->routeIs('dashboard.financial') ? 'active' : '' }}">
                <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                <span class="nav-text ml-3">Analysis</span>
            </a>

            <a href="{{ route('locations.index') }}"
                class="nav-link {{ request()->routeIs('locations.*') ? 'active' : '' }}">
                <i data-lucide="map-pin" class="w-5 h-5"></i>
                <span class="nav-text ml-3">Locations</span>
            </a>

            <a href="{{ route('orders.index') }}"
                class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                <span class="nav-text ml-3">Orders</span>
                <span class="nav-badge ml-auto bg-red-500/20 text-red-500 text-xs px-2 py-0.5 rounded-full">32</span>
            </a>

            <a href="{{ route('expenses.index') }}"
                class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                <i data-lucide="wallet" class="w-5 h-5"></i>
                <span class="nav-text ml-3">Expenses</span>
                <span class="nav-badge ml-auto bg-red-500/20 text-red-500 text-xs px-2 py-0.5 rounded-full">32</span>
            </a>

            <a href="{{ route('customers.index') }}"
                class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                <i data-lucide="users" class="w-5 h-5"></i>
                <span class="nav-text ml-3">Customers</span>
            </a>

            <a href="{{ route('cart.index') }}" class="nav-link {{ request()->routeIs('cart.*') ? 'active' : '' }}">
                <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                <span class="nav-text ml-3">Cart</span>
            </a>

            <!-- keep the rest of your dropdowns same as desktop (copy/paste) -->

        </nav>
    </div>

    <!-- ✅ Fixed Footer -->
    <div class="sidebar-footer p-4 border-t" style="border-color: var(--sidebar-border);">
        <div class="flex items-center space-x-3 p-3 rounded-xl" style="background-color: var(--sidebar-accent);">
            <img class="h-10 w-10 rounded-full object-cover border-2 border-[var(--sidebar-primary)]/50"
                src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                alt="Admin Profile">

            <div class="user-info flex-1 min-w-0">
                <p class="text-sm font-medium truncate">Alex Johnson</p>
                <p class="text-xs truncate" style="color: var(--muted-foreground);">Store Manager</p>
            </div>

            <button onclick="event.preventDefault(); document.getElementById('tyro-logout-form').submit();"
                class="text-[var(--muted-foreground)] hover:text-[var(--sidebar-primary)] transition-colors duration-200"
                title="Logout">
                <i data-lucide="log-out" class="w-4 h-4"></i>
            </button>

            <form id="tyro-logout-form" method="POST" action="/logout" class="hidden">
                @csrf
            </form>
        </div>
    </div>

</div>
