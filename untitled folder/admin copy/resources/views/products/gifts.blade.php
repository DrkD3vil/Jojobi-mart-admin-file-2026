
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-foreground">Free Gift Offer Audit</h1>
            <p class="text-muted-foreground mt-1">Manage and monitor free product offers across all locations</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-3">
            <a href="{{ route('products.gift-audit.export', request()->query()) }}"
               class="px-4 py-2 text-sm font-medium rounded-lg border border-border bg-card text-card-foreground hover:bg-accent transition-colors duration-200"
               id="exportBtn">
                <i class="fas fa-download mr-2"></i> Export CSV
            </a>
            <button class="px-4 py-2 text-sm font-medium rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 transition-colors duration-200"
                    onclick="showCreateModal()">
                <i class="fas fa-plus mr-2"></i> New Offer
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-card rounded-xl p-5 shadow-card border border-border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-muted-foreground">Total Offers</p>
                    <p class="text-2xl font-bold text-foreground mt-1">{{ $stats['total_offers'] }}</p>
                </div>
                <div class="p-3 rounded-lg bg-blue-500/10">
                    <i class="fas fa-gift text-blue-500 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-card rounded-xl p-5 shadow-card border border-border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-muted-foreground">Active Locations</p>
                    <p class="text-2xl font-bold text-foreground mt-1">{{ $stats['active_locations'] }}</p>
                </div>
                <div class="p-3 rounded-lg bg-green-500/10">
                    <i class="fas fa-store text-green-500 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-card rounded-xl p-5 shadow-card border border-border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-muted-foreground">Total Free Qty</p>
                    <p class="text-2xl font-bold text-foreground mt-1">{{ $stats['total_free_qty'] }}</p>
                </div>
                <div class="p-3 rounded-lg bg-purple-500/10">
                    <i class="fas fa-boxes text-purple-500 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-card rounded-xl p-5 shadow-card border border-border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-muted-foreground">Avg Free Per Offer</p>
                    <p class="text-2xl font-bold text-foreground mt-1">{{ $stats['avg_free_per_offer'] }}</p>
                </div>
                <div class="p-3 rounded-lg bg-amber-500/10">
                    <i class="fas fa-chart-bar text-amber-500 text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-card rounded-xl p-4 mb-6 shadow-card border border-border">
        <form method="GET" action="{{ route('products.gift-audit') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search Input -->
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground"></i>
                    <input
                        type="text"
                        name="search"
                        placeholder="Search offers..."
                        value="{{ $searchTerm }}"
                        class="w-full pl-10 pr-4 py-2 rounded-lg border border-border bg-input text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent"
                    >
                </div>

                <!-- Location Filter -->
                <select name="location_id" class="px-4 py-2 rounded-lg border border-border bg-input text-foreground focus:outline-none focus:ring-2 focus:ring-ring">
                    <option value="">All Locations</option>
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}" {{ $selectedLocation == $location->id ? 'selected' : '' }}>
                            {{ $location->name }}
                        </option>
                    @endforeach
                </select>

                <!-- Product Filter -->
                <select name="product_id" class="px-4 py-2 rounded-lg border border-border bg-input text-foreground focus:outline-none focus:ring-2 focus:ring-ring">
                    <option value="">All Products</option>
                    @foreach($allProducts as $product)
                        <option value="{{ $product->id }}" {{ $selectedProduct == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>

                <!-- Status Filter -->
                <select name="status" class="px-4 py-2 rounded-lg border border-border bg-input text-foreground focus:outline-none focus:ring-2 focus:ring-ring">
                    <option value="">All Status</option>
                    <option value="high" {{ $selectedStatus == 'high' ? 'selected' : '' }}>High Stock</option>
                    <option value="medium" {{ $selectedStatus == 'medium' ? 'selected' : '' }}>Medium Stock</option>
                    <option value="low" {{ $selectedStatus == 'low' ? 'selected' : '' }}>Low Stock</option>
                </select>
            </div>

            <!-- Second row of filters -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <!-- Results per page -->
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-muted-foreground">Show:</span>
                        <select name="per_page" class="px-3 py-1 rounded border border-border bg-input text-foreground text-sm focus:outline-none focus:ring-2 focus:ring-ring">
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>

                    <!-- Active Filter -->
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="active_only" value="1" class="rounded border-border text-primary focus:ring-primary"
                               onchange="this.form.submit()">
                        <span class="text-sm text-foreground">Active Offers Only</span>
                    </label>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 transition-colors duration-200">
                        <i class="fas fa-filter mr-2"></i> Apply Filters
                    </button>
                    <a href="{{ route('products.gift-audit') }}" class="px-4 py-2 text-sm font-medium rounded-lg border border-border bg-card text-card-foreground hover:bg-accent transition-colors duration-200">
                        <i class="fas fa-redo mr-2"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="bg-card rounded-xl shadow-card border border-border overflow-hidden">
        @if(count($productGiftDetails) > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border bg-secondary/30">
                        <th class="px-6 py-4 text-left text-sm font-semibold text-foreground uppercase tracking-wider">Product</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-foreground uppercase tracking-wider">Batch SKU</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-foreground uppercase tracking-wider">Free Product</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-foreground uppercase tracking-wider">Free Qty</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-foreground uppercase tracking-wider">Location</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-foreground uppercase tracking-wider">Available Free Qty</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-foreground uppercase tracking-wider">Stock Status</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-foreground uppercase tracking-wider">Offer Status</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-foreground uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($productGiftDetails as $detail)
                    <tr class="hover:bg-accent/30 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center">
                                    <i class="fas fa-box text-primary"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-foreground">{{ $detail['product_name'] }}</div>
                                    <div class="text-xs text-muted-foreground">{{ $detail['product_sku'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-500/10 text-blue-500">
                                {{ $detail['batch_sku'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 rounded-lg bg-green-500/10 flex items-center justify-center mr-3">
                                    <i class="fas fa-gift text-green-500 text-sm"></i>
                                </div>
                                <div>
                                    <span class="text-sm text-foreground">{{ $detail['free_product_name'] }}</span>
                                    <div class="text-xs text-muted-foreground">{{ $detail['free_product_sku'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-lg font-bold text-foreground">{{ $detail['free_qty_per_offer'] }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt text-muted-foreground mr-2"></i>
                                <span class="text-sm text-foreground">{{ $detail['location_name'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-24 bg-secondary rounded-full h-2 mr-3">
                                    <div class="{{ $detail['status_class'] == 'high' ? 'bg-green-500' : ($detail['status_class'] == 'medium' ? 'bg-amber-500' : 'bg-red-500') }} h-2 rounded-full"
                                         style="width: {{ min(100, $detail['stock_ratio'] * 100) }}%">
                                    </div>
                                </div>
                                <span class="text-sm font-medium text-foreground">{{ $detail['available_free_qty'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusClass = $detail['status_class'] == 'high' ? 'bg-green-500/10 text-green-500' :
                                             ($detail['status_class'] == 'medium' ? 'bg-amber-500/10 text-amber-500' : 'bg-red-500/10 text-red-500');
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                {{ $detail['status_text'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="relative inline-block w-10 mr-2 align-middle select-none">
                                    <input type="checkbox"
                                           name="toggle"
                                           id="toggle-{{ $detail['batch_id'] }}"
                                           class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"
                                           data-batch-id="{{ $detail['batch_id'] }}"
                                           {{ $detail['is_active'] ? 'checked' : '' }}
                                           onchange="toggleOfferStatus(this)">
                                    <label for="toggle-{{ $detail['batch_id'] }}" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                                </div>
                                <span class="text-sm {{ $detail['is_active'] ? 'text-green-500' : 'text-red-500' }}">
                                    {{ $detail['is_active'] ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <button class="p-2 rounded-lg border border-border hover:bg-accent text-foreground"
                                        onclick="showEditModal({{ json_encode($detail) }})">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                <button class="p-2 rounded-lg border border-border hover:bg-accent text-foreground"
                                        onclick="viewDetails({{ json_encode($detail) }})">
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                                <a href="{{ route('products.batches.edit', $detail['batch_id']) }}"
                                   class="p-2 rounded-lg border border-border hover:bg-accent text-foreground">
                                    <i class="fas fa-external-link-alt text-sm"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-border bg-secondary/30 flex flex-col md:flex-row md:items-center justify-between">
            <div class="text-sm text-muted-foreground mb-4 md:mb-0">
                Showing {{ $productBatches->firstItem() }} to {{ $productBatches->lastItem() }} of {{ $productBatches->total() }} offers
            </div>
            <div>
                {{ $productBatches->appends(request()->query())->links('vendor.pagination.tailwind') }}
            </div>
        </div>
        @else
        <!-- Empty State -->
        <div class="py-12 text-center">
            <div class="mx-auto w-24 h-24 rounded-full bg-secondary flex items-center justify-center mb-4">
                <i class="fas fa-gift text-3xl text-muted-foreground"></i>
            </div>
            <h3 class="text-lg font-medium text-foreground mb-2">No Free Gift Offers Found</h3>
            <p class="text-muted-foreground mb-6">Try adjusting your filters or create a new offer.</p>
            <button class="px-4 py-2 text-sm font-medium rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 transition-colors duration-200"
                    onclick="showCreateModal()">
                <i class="fas fa-plus mr-2"></i> Create First Offer
            </button>
        </div>
        @endif
    </div>

    <!-- Stock Distribution Chart -->
    @if(count($productGiftDetails) > 0)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
        <div class="bg-card rounded-xl p-6 shadow-card border border-border">
            <h3 class="text-lg font-semibold text-foreground mb-4">Stock Distribution by Status</h3>
            <div class="space-y-4">
                @php
                    $highStock = collect($productGiftDetails)->where('status_class', 'high')->count();
                    $mediumStock = collect($productGiftDetails)->where('status_class', 'medium')->count();
                    $lowStock = collect($productGiftDetails)->where('status_class', 'low')->count();
                    $total = count($productGiftDetails);
                @endphp

                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-green-500">High Stock</span>
                        <span class="text-muted-foreground">{{ $highStock }} offers</span>
                    </div>
                    <div class="w-full bg-secondary rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full"
                             style="width: {{ $total > 0 ? ($highStock / $total) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-amber-500">Medium Stock</span>
                        <span class="text-muted-foreground">{{ $mediumStock }} offers</span>
                    </div>
                    <div class="w-full bg-secondary rounded-full h-2">
                        <div class="bg-amber-500 h-2 rounded-full"
                             style="width: {{ $total > 0 ? ($mediumStock / $total) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-red-500">Low Stock</span>
                        <span class="text-muted-foreground">{{ $lowStock }} offers</span>
                    </div>
                    <div class="w-full bg-secondary rounded-full h-2">
                        <div class="bg-red-500 h-2 rounded-full"
                             style="width: {{ $total > 0 ? ($lowStock / $total) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-card rounded-xl p-6 shadow-card border border-border">
            <h3 class="text-lg font-semibold text-foreground mb-4">Available Quantity by Location</h3>
            <div class="space-y-4">
                @foreach($locations as $location)
                @php
                    $locationQty = collect($productGiftDetails)
                        ->filter(function($item) use ($location) {
                            return str_contains($item['location_name'], $location->name) ||
                                   (!$selectedLocation && $item['location_name'] == 'Multiple Locations');
                        })
                        ->sum('available_free_qty');
                @endphp
                @if($locationQty > 0)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-foreground">{{ $location->name }}</span>
                        <span class="text-muted-foreground">{{ $locationQty }} available</span>
                    </div>
                    <div class="w-full bg-secondary rounded-full h-2">
                        <div class="bg-primary h-2 rounded-full"
                             style="width: {{ min(100, ($locationQty / max(1, $stats['total_free_qty'])) * 100) }}%">
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-black/50" onclick="hideModal()"></div>
        <div class="inline-block align-bottom bg-card rounded-xl text-left overflow-hidden shadow-card transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-6 py-4 border-b border-border">
                <h3 class="text-lg font-medium text-foreground">Edit Free Gift Offer</h3>
            </div>
            <div class="px-6 py-4">
                <form id="editForm">
                    @csrf
                    <input type="hidden" name="batch_id" id="editBatchId">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Free Quantity Per Offer</label>
                            <input type="number" name="free_qty" id="editFreeQty" min="1" step="1"
                                   class="w-full px-3 py-2 rounded-lg border border-border bg-input text-foreground focus:outline-none focus:ring-2 focus:ring-ring">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-2">Start Date</label>
                                <input type="date" name="start_date" id="editStartDate"
                                       class="w-full px-3 py-2 rounded-lg border border-border bg-input text-foreground focus:outline-none focus:ring-2 focus:ring-ring">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-2">End Date</label>
                                <input type="date" name="end_date" id="editEndDate"
                                       class="w-full px-3 py-2 rounded-lg border border-border bg-input text-foreground focus:outline-none focus:ring-2 focus:ring-ring">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 border-t border-border flex justify-end gap-3">
                <button type="button" onclick="hideModal()"
                        class="px-4 py-2 text-sm font-medium rounded-lg border border-border bg-card text-card-foreground hover:bg-accent transition-colors duration-200">
                    Cancel
                </button>
                <button type="button" onclick="updateOffer()"
                        class="px-4 py-2 text-sm font-medium rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 transition-colors duration-200">
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

    .container {
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Toggle Switch Styles */
    .toggle-checkbox:checked {
        right: 0;
        border-color: var(--success);
    }

    .toggle-checkbox:checked + .toggle-label {
        background-color: var(--success);
    }

    .toggle-checkbox {
        transition: all 0.3s;
        transform: translateX(0);
    }

    .toggle-checkbox:checked {
        transform: translateX(1rem);
    }

    .toggle-label {
        transition: background-color 0.3s;
    }

    /* Table Styles */
    table {
        border-collapse: separate;
        border-spacing: 0;
    }

    th:first-child {
        border-top-left-radius: var(--radius);
    }

    th:last-child {
        border-top-right-radius: var(--radius);
    }

    tr:last-child td:first-child {
        border-bottom-left-radius: var(--radius);
    }

    tr:last-child td:last-child {
        border-bottom-right-radius: var(--radius);
    }

    /* Input and Select Styles */
    input, select {
        transition: all var(--transition-fast);
    }

    input:focus, select:focus {
        box-shadow: 0 0 0 2px var(--accent-glow);
    }

    /* Button Styles */
    button {
        transition: all var(--transition-fast);
    }

    button:hover {
        transform: translateY(-1px);
        box-shadow: var(--card-shadow-hover);
    }

    /* Modal Styles */
    #editModal {
        opacity: 0;
        visibility: hidden;
        transition: opacity var(--transition-normal), visibility var(--transition-normal);
    }

    #editModal.hidden {
        opacity: 0;
        visibility: hidden;
    }

    #editModal:not(.hidden) {
        opacity: 1;
        visibility: visible;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .table-container {
            margin: 0 -1rem;
            width: calc(100% + 2rem);
        }

        table {
            font-size: 0.875rem;
        }

        th, td {
            padding: 0.75rem 0.5rem;
        }

        .grid-cols-4 {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .grid-cols-4 {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const theme = document.documentElement.getAttribute('data-theme') || 'dark';

        // Export button loading state
        const exportBtn = document.getElementById('exportBtn');
        if (exportBtn) {
            exportBtn.addEventListener('click', function(e) {
                const originalHTML = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Exporting...';
                this.classList.add('opacity-50', 'cursor-not-allowed');

                // Allow time for download to start
                setTimeout(() => {
                    this.innerHTML = originalHTML;
                    this.classList.remove('opacity-50', 'cursor-not-allowed');
                }, 2000);
            });
        }

        // Table row click for details
        const tableRows = document.querySelectorAll('tbody tr');
        tableRows.forEach(row => {
            row.addEventListener('click', function(e) {
                // Don't trigger if clicking on action buttons
                if (e.target.closest('button') || e.target.closest('a') || e.target.type === 'checkbox') {
                    return;
                }

                // Add visual feedback
                this.style.backgroundColor = theme === 'dark'
                    ? 'rgba(255, 255, 255, 0.05)'
                    : 'rgba(0, 0, 0, 0.02)';

                setTimeout(() => {
                    this.style.backgroundColor = '';
                }, 300);

                // Get data from row and show details
                const productName = this.querySelector('td:first-child .font-medium').textContent;
                const batchSku = this.querySelector('td:nth-child(2) span').textContent;
                const freeProduct = this.querySelector('td:nth-child(3) span.text-sm').textContent;

                showQuickView({
                    product_name: productName,
                    batch_sku: batchSku,
                    free_product_name: freeProduct
                });
            });
        });
    });

    function toggleOfferStatus(checkbox) {
        const batchId = checkbox.getAttribute('data-batch-id');
        const isActive = checkbox.checked;

        fetch('/products/gift-audit/toggle-status/' + batchId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                is_active: isActive
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Offer status updated successfully', 'success');
            } else {
                checkbox.checked = !isActive;
                showNotification('Failed to update status', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            checkbox.checked = !isActive;
            showNotification('Error updating status', 'error');
        });
    }

    function showEditModal(detail) {
        document.getElementById('editBatchId').value = detail.batch_id;
        document.getElementById('editFreeQty').value = detail.free_qty_per_offer;
        document.getElementById('editStartDate').value = detail.start_date ? detail.start_date.split(' ')[0] : '';
        document.getElementById('editEndDate').value = detail.end_date ? detail.end_date.split(' ')[0] : '';

        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.style.opacity = '1';
        }, 10);
    }

    function hideModal() {
        const modal = document.getElementById('editModal');
        modal.style.opacity = '0';
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function updateOffer() {
        const form = document.getElementById('editForm');
        const formData = new FormData(form);

        fetch('/products/batches/update-free-offer', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Offer updated successfully', 'success');
                hideModal();
                // Reload page to reflect changes
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showNotification(data.message || 'Failed to update offer', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error updating offer', 'error');
        });
    }

    function showQuickView(detail) {
        // Create a simple alert with details - you can enhance this with a proper modal
        alert(`Product: ${detail.product_name}\nBatch SKU: ${detail.batch_sku}\nFree Product: ${detail.free_product_name}`);
    }

    function showCreateModal() {
        // Redirect to batch creation page or show create modal
        window.location.href = '/products/batches/create?free_offer=true';
    }

    function viewDetails(detail) {
        // Open detail view in new tab or modal
        window.open(`/products/batches/${detail.batch_id}`, '_blank');
    }

    function showNotification(message, type = 'success') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-card border ${
            type === 'success' ? 'bg-green-500/10 border-green-500/20 text-green-500' :
            'bg-red-500/10 border-red-500/20 text-red-500'
        }`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(notification);

        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
</script>
@endsection
