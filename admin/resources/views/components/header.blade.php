@php
    $routeName = request()->route()?->getName() ?? '';

    // Default header
    $header = [
        'title' => 'Dashboard',
        'action_text' => null,
        'action_icon' => null,
        'action_href' => null,
    ];

    // Map by route patterns
    $map = [
        // Dashboards
        'dashboard.financial.today*' => [
            'title' => 'Today Dashboard',
            'action_text' => null,
            'action_icon' => null,
            'action_href' => null,
        ],
        'dashboard.financial*' => [
            'title' => 'Financial Analysis',
            'action_text' => 'Export',
            'action_icon' => 'download',
            'action_href' => route('dashboard.financial.export', ['format' => 'csv']),
        ],
        'dashboard.reports*' => [
            'title' => 'Reports & Analytics',
            'action_text' => 'Export',
            'action_icon' => 'download',
            'action_href' => route('dashboard.reports.export', ['format' => 'csv', 'type' => 'products']),
        ],

        // Products
        'products.index' => [
            'title' => 'Products',
            'action_text' => 'Add Product',
            'action_icon' => 'plus',
            'action_href' => route('products.create'),
        ],
        'products.create' => [
            'title' => 'Create Product',
            'action_text' => 'All Products',
            'action_icon' => 'list',
            'action_href' => route('products.index'),
        ],
        'products.edit' => [
            'title' => 'Edit Product',
            'action_text' => 'All Products',
            'action_icon' => 'list',
            'action_href' => route('products.index'),
        ],

        // Categories
        'categories.*' => [
            'title' => 'Categories',
            'action_text' => 'Add Category',
            'action_icon' => 'plus',
            'action_href' => route('categories.create'),
        ],

        // Brands (resource)
        'brands.index' => [
            'title' => 'Brands',
            'action_text' => 'Add Brand',
            'action_icon' => 'plus',
            'action_href' => route('brands.create'),
        ],
        'brands.create' => [
            'title' => 'Create Brand',
            'action_text' => 'All Brands',
            'action_icon' => 'list',
            'action_href' => route('brands.index'),
        ],
        'brands.edit' => [
            'title' => 'Edit Brand',
            'action_text' => 'All Brands',
            'action_icon' => 'list',
            'action_href' => route('brands.index'),
        ],

        // Orders
        'orders.index' => [
            'title' => 'Orders',
            'action_text' => null,
            'action_icon' => null,
            'action_href' => null,
        ],
        'orders.show' => [
            'title' => 'Order Details',
            'action_text' => 'Back to Orders',
            'action_icon' => 'arrow-left',
            'action_href' => route('orders.index'),
        ],

        // Customers
        'customers.index' => [
            'title' => 'Customers',
            'action_text' => null,
            'action_icon' => null,
            'action_href' => null,
        ],
        'customers.show' => [
            'title' => 'Customer Details',
            'action_text' => 'All Customers',
            'action_icon' => 'list',
            'action_href' => route('customers.index'),
        ],

        // Cart / POS
        'cart.*' => [
            'title' => 'POS Cart',
            'action_text' => null,
            'action_icon' => null,
            'action_href' => null,
        ],

        // RBAC: roles, privileges, user roles, access keys
        'roles.*' => [
            'title' => 'Roles',
            'action_text' => null,
            'action_icon' => null,
            'action_href' => null,
        ],
        'privileges.*' => [
            'title' => 'Privileges',
            'action_text' => null,
            'action_icon' => null,
            'action_href' => null,
        ],
        'user.roles.*' => [
            'title' => 'User Roles',
            'action_text' => null,
            'action_icon' => null,
            'action_href' => null,
        ],
        'access_keys.index' => [
            'title' => 'Access Key Management',
            'action_text' => null,
            'action_icon' => null,
            'action_href' => null,
        ],
        'rbac.audit_log' => [
            'title' => 'Audit Log',
            'action_text' => 'Access Keys',
            'action_icon' => 'arrow-left',
            'action_href' => route('access_keys.index'),
        ],

        // Profile / account
        'profile.edit' => [
            'title' => 'Profile Settings',
            'action_text' => null,
            'action_icon' => null,
            'action_href' => null,
        ],
        'password.change.form' => [
            'title' => 'Change Password',
            'action_text' => 'Back to Profile',
            'action_icon' => 'arrow-left',
            'action_href' => route('profile.edit'),
        ],

        // Expenses
        'expenses.index' => [
            'title' => 'Expenses',
            'action_text' => 'Add Expense',
            'action_icon' => 'plus',
            'action_href' => route('expenses.create'),
        ],
        'expenses.create' => [
            'title' => 'Create Expense',
            'action_text' => 'All Expenses',
            'action_icon' => 'list',
            'action_href' => route('expenses.index'),
        ],
        'expenses.edit' => [
            'title' => 'Edit Expense',
            'action_text' => 'All Expenses',
            'action_icon' => 'list',
            'action_href' => route('expenses.index'),
        ],

        // Locations (resource)
        'locations.index' => [
            'title' => 'Locations',
            'action_text' => 'Add Location',
            'action_icon' => 'plus',
            'action_href' => route('locations.create'),
        ],
        'locations.create' => [
            'title' => 'Create Location',
            'action_text' => 'All Locations',
            'action_icon' => 'list',
            'action_href' => route('locations.index'),
        ],
        'locations.edit' => [
            'title' => 'Edit Location',
            'action_text' => 'All Locations',
            'action_icon' => 'list',
            'action_href' => route('locations.index'),
        ],

        // Inventory Ops
        'returns.wizard' => [
            'title' => 'Return Wizard',
            'action_text' => null,
            'action_icon' => null,
            'action_href' => null,
        ],
        'exchanges.create' => [
            'title' => 'Create Exchange',
            'action_text' => null,
            'action_icon' => null,
            'action_href' => null,
        ],
        'transfers.create' => [
            'title' => 'Stock Transfer',
            'action_text' => null,
            'action_icon' => null,
            'action_href' => null,
        ],
        'stock-ledger.*' => [
            'title' => 'Stock Ledger',
            'action_text' => null,
            'action_icon' => null,
            'action_href' => null,
        ],

        // Product images / batches / statuses
        'product.images.*' => [
            'title' => 'Product Images',
            'action_text' => 'All Products',
            'action_icon' => 'list',
            'action_href' => route('products.index'),
        ],
        'product.batches.*' => [
            'title' => 'Product Batches',
            'action_text' => 'Create Batch',
            'action_icon' => 'plus',
            'action_href' => route('product.batches.create'),
        ],
        'product.status.*' => [
            'title' => 'Product Status',
            'action_text' => 'Add Status',
            'action_icon' => 'plus',
            'action_href' => route('product.status.create'),
        ],
    ];

    foreach ($map as $pattern => $conf) {
        if (request()->routeIs($pattern)) {
            $header = array_merge($header, $conf);
            break;
        }
    }
@endphp
<header class="sticky top-0 z-30 px-6 py-4 flex items-center justify-between border-b"
        style="border-color: var(--border); background-color: color-mix(in srgb, var(--background) 95%, transparent); backdrop-filter: blur(10px);">

    <div class="flex items-center">
        <button id="sidebar-toggle-desktop"
                class="text-[var(--muted-foreground)] hover:text-[var(--sidebar-primary)] transition-colors duration-200 mr-4 hidden lg:block">
            <i data-lucide="menu" class="w-6 h-6"></i>
        </button>

        <button id="sidebar-toggle-mobile"
                class="text-[var(--muted-foreground)] hover:text-[var(--sidebar-primary)] transition-colors duration-200 mr-4 lg:hidden">
            <i data-lucide="menu" class="w-6 h-6"></i>
        </button>

        <div class="text-xl lg:hidden" style="font-family: var(--font-display); font-weight: 600; color: var(--foreground);">
            JOJOBI MART
        </div>

        <!-- ✅ Dynamic title -->
        <h1 class="text-xl hidden lg:block">
            {{ $header['title'] }}
        </h1>
    </div>

    <div class="flex items-center space-x-4">
        <div class="relative hidden md:block">
            <i data-lucide="search"
               class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4"
               style="color: var(--muted-foreground);"></i>

            <input type="text" placeholder="Search products, orders..."
                   class="rounded-full py-2 pl-10 pr-4 text-sm focus:ring-2 focus:ring-[var(--ring)] focus:border-transparent border transition-all duration-200 w-40 lg:w-64"
                   style="background-color: var(--input); border-color: var(--border); color: var(--foreground);">
        </div>

        <button id="mobile-search-trigger" class="icon-btn-anim relative p-2 rounded-full hover:bg-[var(--accent)] transition-colors duration-200 md:hidden">
            <i data-lucide="search" class="w-5 h-5" style="color: var(--muted-foreground);"></i>
        </button>

        <button class="icon-btn-anim relative p-2 rounded-full hover:bg-[var(--accent)] transition-colors duration-200">
            <i data-lucide="bell" class="w-5 h-5" style="color: var(--muted-foreground);"></i>
            <span class="pulse-animation absolute top-1.5 right-1.5 block h-2 w-2 rounded-full" style="background-color: var(--danger);"></span>
        </button>

        <button id="theme-toggle" class="icon-btn-anim p-2 rounded-full hover:bg-[var(--accent)] transition-colors duration-200">
            <i data-lucide="sun" id="theme-icon" class="w-5 h-5" style="color: var(--muted-foreground);"></i>
        </button>

        <!-- ✅ Dynamic action button -->
        @if(!empty($header['action_href']) && !empty($header['action_text']))
            <span class="hidden md:inline-flex">
                <a href="{{ $header['action_href'] }}" class="btn-primary">
                    <i data-lucide="{{ $header['action_icon'] ?? 'plus' }}" class="w-4 h-4"></i>
                    {{ $header['action_text'] }}
                </a>
            </span>
        @endif
    </div>
</header>

<!-- Mobile search overlay -->
<div id="mobile-search-overlay">
    <div class="mobile-search-box">
        <div class="relative">
            <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4" style="color: var(--muted-foreground);"></i>
            <input id="mobile-search-input" type="text" placeholder="Search products, orders..."
                   class="w-full rounded-full py-3 pl-11 pr-11 text-sm border focus:ring-2 focus:ring-[var(--ring)] focus:border-transparent transition-all duration-200"
                   style="background-color: var(--input); border-color: var(--border); color: var(--foreground);">
            <button id="mobile-search-close" class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 rounded-full hover:bg-[var(--accent)]">
                <i data-lucide="x" class="w-4 h-4" style="color: var(--muted-foreground);"></i>
            </button>
        </div>
    </div>
</div>
