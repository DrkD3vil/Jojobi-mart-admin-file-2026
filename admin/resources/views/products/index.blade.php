@extends('layouts.app')

@section('content')
    @php
        // Unique prefix so nothing conflicts with other components
        $barcodeNs = 'blp'; // barcode label print namespace
    @endphp
    <div class="container-fluid px-4">
        {{-- Stats Cards --}}
        <div class="stats-grid mb-6 animate-slide-up">
            <!-- Total Products Section -->
            <div id="total-products-section" class="stat-card group hover:scale-[1.02] transition-all duration-300">
                <div
                    class="stat-icon total bg-gradient-to-br from-blue-100 to-blue-50 group-hover:from-blue-200 group-hover:to-blue-100">
                    <svg viewBox="0 0 24 24" class="text-blue-600">
                        <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h10v2H4z" fill="currentColor" />
                    </svg>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value" id="total-products-count">{{ $products->total() }}</h3>
                    <p class="stat-label">Total Products</p>
                    <span class="text-xs text-gray-500 mt-1">{{ $products->where('is_active', false)->count() }}
                        inactive</span>
                </div>
                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>

            <!-- Active Products Section -->
            <div id="active-products-section" class="stat-card group hover:scale-[1.02] transition-all duration-300">
                <div
                    class="stat-icon active bg-gradient-to-br from-green-100 to-green-50 group-hover:from-green-200 group-hover:to-green-100">
                    <svg viewBox="0 0 24 24" class="text-green-600">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"
                            fill="currentColor" />
                    </svg>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value" id="active-products-count">{{ $products->where('is_active', true)->count() }}
                    </h3>
                    <p class="stat-label">Active Products</p>
                    <span class="text-xs text-green-600 mt-1 font-medium">
                        {{ $products->total() > 0 ? round(($products->where('is_active', true)->count() / $products->total()) * 100, 1) : 0 }}%
                    </span>
                </div>
                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>

            <!-- Free Products Section -->
            <a href="{{ route('product.gift-audit') }}" id="free-products-section"
                class="stat-card group hover:scale-[1.02] transition-all duration-300 text-decoration-none">
                <div
                    class="stat-icon free bg-gradient-to-br from-purple-100 to-purple-50 group-hover:from-purple-200 group-hover:to-purple-100">
                    <svg viewBox="0 0 24 24" class="text-purple-600">
                        <path
                            d="M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35L12 4l-.5-.65C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"
                            fill="currentColor" />
                    </svg>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value" id="free-products-count">
                        {{ $products_batchs->where('is_free_offer_active', true)->count() }}
                    </h3>
                    <p class="stat-label">Free Products</p>
                    <span class="text-xs text-purple-600 mt-1 font-medium">Gift Audit</span>
                </div>
                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>

            <!-- Categories Section -->
            <div id="categories-section" class="stat-card group hover:scale-[1.02] transition-all duration-300">
                <div
                    class="stat-icon categories bg-gradient-to-br from-amber-100 to-amber-50 group-hover:from-amber-200 group-hover:to-amber-100">
                    <svg viewBox="0 0 24 24" class="text-amber-600">
                        <path d="M12 2l-5.5 9h11z" fill="currentColor" />
                        <circle cx="17.5" cy="17.5" r="4.5" fill="currentColor" />
                        <path d="M3 13.5h8v8H3z" fill="currentColor" />
                    </svg>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value" id="categories-count">
                        {{ $categoriesCount ?? $products->unique('category_id')->count() }}</h3>
                    <p class="stat-label">Categories</p>
                    <span class="text-xs text-gray-500 mt-1">Product types</span>
                </div>
                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>

            <!-- Total Images Section -->
            <div id="total-images-section" class="stat-card group hover:scale-[1.02] transition-all duration-300">
                <div
                    class="stat-icon images bg-gradient-to-br from-rose-100 to-rose-50 group-hover:from-rose-200 group-hover:to-rose-100">
                    <svg viewBox="0 0 24 24" class="text-rose-600">
                        <path
                            d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"
                            fill="currentColor" />
                    </svg>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value" id="total-images-count">
                        {{ $totalImages ?? $products->sum(fn($p) => $p->images->count()) }}</h3>
                    <p class="stat-label">Total Images</p>
                    <span class="text-xs text-gray-500 mt-1">
                        {{ $products->count() > 0 ? round(($totalImages ?? $products->sum(fn($p) => $p->images->count())) / $products->count(), 1) : 0 }}
                        avg per product
                    </span>
                </div>
                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>

            <!-- Trash Section -->
            <a href="{{ route('products.trash') }}" id="trash-section"
                class="stat-card group hover:scale-[1.02] transition-all duration-300 text-decoration-none">
                <div
                    class="stat-icon danger bg-gradient-to-br from-gray-100 to-gray-50 group-hover:from-gray-200 group-hover:to-gray-100">
                    <svg viewBox="0 0 24 24" class="text-gray-600">
                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"
                            fill="currentColor" />
                    </svg>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value" id="trash-count">{{ $trashedCount }}</h3>
                    <p class="stat-label">Trash (Deleted)</p>
                    <span class="text-xs text-gray-600 mt-1 font-medium">Restore or delete permanently</span>
                </div>
                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>
        </div>

        {{-- Products Table Container --}}
        <div class="products-card glass-effect animate-slide-up-delay">
            {{-- Table Header with Search --}}
            <div class="table-header">
                <div class="table-search">
                    <div class="search-wrapper">
                        <svg viewBox="0 0 24 24" class="search-icon">
                            <path
                                d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
                        </svg>
                        <input type="text" placeholder="Search products by name or barcode..." class="search-input"
                            id="liveSearchInput" data-search-url="{{ route('products.live-search') }}">
                        <div class="search-loader" id="searchLoader" style="display: none;">
                            <div class="loader-spinner"></div>
                        </div>
                        <button class="clear-search-btn" id="clearSearchBtn" style="display: none;"
                            title="Clear search">
                            <svg viewBox="0 0 24 24">
                                <path
                                    d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="table-actions">
                    <div class="table-info" id="tableInfo">
                        Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of
                        {{ $products->total() }} products
                    </div>
                    <div class="view-toggle">
                        <button class="view-btn active" data-view="table" title="Table View">
                            <svg viewBox="0 0 24 24">
                                <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z" />
                            </svg>
                        </button>
                        <button class="view-btn" data-view="grid" title="Grid View">
                            <svg viewBox="0 0 24 24">
                                <path d="M4 4h7v7H4zm0 9h7v7H4zm9-9h7v7h-7zm0 9h7v7h-7z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Table View --}}
            <div class="products-table-view" id="tableView">
                <div class="table-responsive">
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th class="col-barcode">Barcode</th>
                                <th class="col-name">Product Name</th>
                                <th class="col-category">Category</th>
                                <th class="col-brand">Brand</th>
                                <th class="col-batches">Batches</th>
                                <th class="col-status">Status</th>
                                <th class="col-images">Images</th>
                                <th class="col-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="productsTableBody">
                            @forelse($products as $product)
                                <tr class="product-row" data-product-id="{{ $product->id }}">
                                    <td class="cell-barcode">
                                        <div class="barcode-cell">
                                            <svg viewBox="0 0 24 24" class="barcode-icon">
                                                <path
                                                    d="M2 6h2v12H2zm3 0h1v12H5zm2 0h3v12H7zm4 0h1v12h-1zm3 0h2v12h-2zm4 0h1v12h-1zm3 0h1v12h-1zm2 0h1v12h-1z" />
                                            </svg>
                                            <span class="barcode-value">{{ $product->barcode }}</span>
                                        </div>
                                    </td>
                                    <td class="cell-name">
                                        <div class="product-name-cell">
                                            <div class="product-name">{{ $product->name }}</div>
                                            @if ($product->description)
                                                <div class="product-description">
                                                    {{ Str::limit($product->description, 50) }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="cell-category">
                                        @if ($product->category)
                                            <span class="category-badge">
                                                <a href="{{ route('categories.edit', $product->category->id) }}"
                                                    style="color: inherit; text-decoration: none;">
                                                    {{ $product->category->name }}
                                                </a>
                                            </span>
                                        @else
                                            <span class="text-muted">Uncategorized</span>
                                        @endif
                                    </td>
                                    <td class="cell-brand">
                                        @if ($product->brand)
                                            <span class="brand-badge">
                                                <a href="{{ route('brands.edit', $product->brand->id) }}"
                                                    style="color: inherit; text-decoration: none;">
                                                    {{ $product->brand->name }}
                                                </a>
                                            </span>
                                        @else
                                            <span class="text-muted">No Brand</span>
                                        @endif
                                    </td>
                                    <td class="cell-batches">
                                        @if ($product->batches->count() > 0)
                                            <span class="batch-badge">
                                                <a href="{{ route('products.edit', $product->id) }}"
                                                    style="color: inherit; text-decoration: none;">
                                                    {{ $product->batches->count() }}
                                                </a>
                                            </span>
                                        @else
                                            <span class="text-muted">No Batch</span>
                                        @endif
                                    </td>
                                    <td class="cell-status">
                                        <div class="status-indicator {{ $product->is_active ? 'active' : 'inactive' }}">
                                            <span class="status-dot"></span>
                                            <span
                                                class="status-text">{{ $product->is_active ? 'Active' : 'Inactive' }}</span>
                                        </div>
                                    </td>
                                    <td class="cell-images">
                                        <div class="image-gallery">
                                            @php
                                                $previewImages = $product->images->take(3);
                                                $remainingCount = $product->images->count() - $previewImages->count();
                                            @endphp

                                            @forelse($previewImages as $image)
                                                <div class="image-thumbnail"
                                                    style="background-image: url('{{ asset('storage/' . $image->image_path) }}')"
                                                    title="{{ $product->name }}"
                                                    onclick="showImageModal('{{ asset('storage/' . $image->image_path) }}')">
                                                    <div class="image-overlay"></div>
                                                </div>
                                            @empty
                                                <div class="no-images">
                                                    <svg viewBox="0 0 24 24">
                                                        <path
                                                            d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z" />
                                                    </svg>
                                                </div>
                                            @endforelse

                                            @if ($remainingCount > 0)
                                                <a href="{{ route('products.images.index', $product) }}"
                                                    class="image-more" title="View all images">
                                                    +{{ $remainingCount }}
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="cell-actions">
                                        <div class="action-buttons">
                                            <a href="{{ route('products.edit', $product) }}" class="action-btn edit"
                                                title="Edit Product">
                                                <svg viewBox="0 0 24 24">
                                                    <path
                                                        d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('product.batches.create', ['product' => $product->id]) }}"
                                                class="action-btn add-batch" title="Add Batch">
                                                <svg viewBox="0 0 24 24">
                                                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                                                </svg>
                                            </a>

                                            @php
                                                $batchesForPrint = $product->batches
                                                    ->map(function ($b) {
                                                        return [
                                                            'id' => $b->id,
                                                            'text' =>
                                                                'Batch ' .
                                                                ($b->batch_no ?? $b->id) .
                                                                ($b->expiry_date
                                                                    ? ' | Exp ' . $b->expiry_date->format('Y-m-d')
                                                                    : ''),
                                                        ];
                                                    })
                                                    ->values();
                                            @endphp

                                            <button type="button"
                                                class="action-btn {{ $barcodeNs }}-btn {{ $barcodeNs }}-trigger"
                                                title="Print Barcode"
                                                data-{{ $barcodeNs }}-product-id="{{ $product->id }}"
                                                data-{{ $barcodeNs }}-product-name="{{ e($product->name) }}"
                                                aria-label="Print barcode for {{ e($product->name) }}">
                                                <span class="{{ $barcodeNs }}-icon" aria-hidden="true">
                                                    <svg viewBox="0 0 24 24">
                                                        <path d="M19 8H5a3 3 0 0 0-3 3v4h4v4h12v-4h4v-4a3 3 0 0 0-3-3z" />
                                                    </svg>
                                                </span>
                                            </button>

                                            <script type="application/json" id="{{ $barcodeNs }}_batches_{{ $product->id }}">
@json($batchesForPrint)
</script>

                                            <a href="{{ route('products.images.index', $product) }}"
                                                class="action-btn images" title="Manage Images">
                                                <svg viewBox="0 0 24 24">
                                                    <path
                                                        d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('products.show', $product) }}" class="action-btn view"
                                                title="View Product">
                                                <svg viewBox="0 0 24 24">
                                                    <path d="M12 5c-7.633 0-11 6.5-11 7s3.367 7 11 7 11-6.5 11-7-3.367-7-11-7zm0 12c-2.757 0-5-2.243-5-5s2.243-5 5-5
                                                                                    5 2.243 5 5-2.243 5-5 5zm0-8
                                                                                    c-1.654 0-3 1.346-3 3
                                                                                    s1.346 3 3 3
                                                                                    3-1.346 3-3
                                                                                    -1.346-3-3-3z" />
                                                </svg>
                                            </a>

                                            <form action="{{ route('products.destroy', $product) }}" method="POST"
                                                class="delete-form" data-product-id="{{ $product->id }}"
                                                style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="action-btn delete delete-product-btn"
                                                    data-product-id="{{ $product->id }}"
                                                    data-product-name="{{ addslashes($product->name) }}"
                                                    title="Delete Product">
                                                    <svg viewBox="0 0 24 24">
                                                        <path
                                                            d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr class="no-products">
                                    <td colspan="8">
                                        <div class="empty-state">
                                            <svg viewBox="0 0 24 24" class="empty-icon">
                                                <path
                                                    d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z" />
                                            </svg>
                                            <h3>No Products Found</h3>
                                            <p>Get started by adding your first product</p>
                                            <a href="{{ route('products.create') }}" class="btn-primary">
                                                <svg viewBox="0 0 24 24" class="btn-icon">
                                                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                                                </svg>
                                                Add Product
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ========== GLOBAL MODAL (render ONLY ONCE, OUTSIDE the loop) ========== --}}
            <div class="modal fade" id="{{ $barcodeNs }}_modal" tabindex="-1" aria-hidden="true"
                data-bs-backdrop="true" data-bs-keyboard="true">
                <div class="modal-dialog modal-md modal-dialog-centered">
                    <div class="modal-content {{ $barcodeNs }}-modal">
                        <div class="modal-header {{ $barcodeNs }}-modal-header">
                            <div>
                                <h5 class="mb-0 {{ $barcodeNs }}-title">Print Barcode</h5>
                                <small class="{{ $barcodeNs }}-sub" id="{{ $barcodeNs }}_product_name">—</small>
                            </div>
                            <button type="button" class="btn-close {{ $barcodeNs }}-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body {{ $barcodeNs }}-modal-body">
                            <label class="form-label {{ $barcodeNs }}-label" for="{{ $barcodeNs }}_select">Select
                                Batch</label>
                            <select class="form-select {{ $barcodeNs }}-select" id="{{ $barcodeNs }}_select">
                                <option value="">Select batch</option>
                            </select>
                            <div class="{{ $barcodeNs }}-preview mt-3">
                                <div class="{{ $barcodeNs }}-preview-top">
                                    <strong>Preview</strong>
                                    <span class="{{ $barcodeNs }}-chip"
                                        id="{{ $barcodeNs }}_status_chip">Ready</span>
                                </div>
                                <div class="small mt-1">
                                    <span id="{{ $barcodeNs }}_preview_text">—</span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer {{ $barcodeNs }}-modal-footer">
                            <button type="button" class="btn btn-secondary {{ $barcodeNs }}-btn-secondary"
                                data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary {{ $barcodeNs }}-btn-primary"
                                id="{{ $barcodeNs }}_print_btn" disabled>Print</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Delete Confirmation Modal --}}
            <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true" style="display: none">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Delete</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete <strong id="deleteProductName"></strong>?</p>
                            <p class="text-danger mb-0">This action cannot be undone. All associated data will be
                                permanently deleted.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Success Toast Notification --}}
            <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
                <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert"
                    aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="3000">
                    <div class="d-flex">
                        <div class="toast-body">
                            Product deleted successfully!
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="table-footer" id="paginationSection">
                @if ($products->hasPages())
                    <div class="pagination-info">
                        Page {{ $products->currentPage() }} of {{ $products->lastPage() }}
                    </div>
                    <div class="pagination-wrapper">
                        {{ $products->onEachSide(1)->links('vendor.pagination.custom') }}
                    </div>
                    <div class="pagination-actions">
                        <select class="per-page-select" onchange="window.location.href = this.value">
                            <option value="{{ request()->fullUrlWithQuery(['per_page' => 10]) }}"
                                {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 per page</option>
                            <option value="{{ request()->fullUrlWithQuery(['per_page' => 25]) }}"
                                {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25 per page</option>
                            <option value="{{ request()->fullUrlWithQuery(['per_page' => 50]) }}"
                                {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50 per page</option>
                            <option value="{{ request()->fullUrlWithQuery(['per_page' => 100]) }}"
                                {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100 per page</option>
                        </select>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        /* =========== Animations =========== */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes popIn {
            0% {
                opacity: 0;
                transform: scale(0.95);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes shimmer {
            0% {
                background-position: -200px 0;
            }

            100% {
                background-position: 200px 0;
            }
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.4s ease-out;
        }

        .animate-slide-up {
            animation: slideUp 0.4s ease-out;
        }

        .animate-slide-up-delay {
            animation: slideUp 0.4s ease-out 0.1s both;
        }

        .animate-slide-down {
            animation: slideDown 0.3s ease-out;
        }

        .animate-pop-in {
            animation: popIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        /* =========== Page Header =========== */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .header-content {
            flex: 1;
            min-width: 300px;
        }

        .page-title {
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--text-primary), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
        }

        .page-subtitle {
            color: var(--text-secondary);
            font-size: 1.1rem;
            max-width: 600px;
            line-height: 1.6;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            padding: 0.875rem 1.75rem;
            background: linear-gradient(135deg, var(--accent-color), var(--info));
            color: var(--primary-foreground);
            border: none;
            border-radius: var(--radius);
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all var(--transition-normal);
            text-decoration: none;
            box-shadow: 0 4px 12px var(--accent-glow);
            white-space: nowrap;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px var(--accent-glow);
        }

        .btn-primary .btn-icon {
            width: 20px;
            height: 20px;
            fill: currentColor;
        }

        .btn-with-icon {
            padding: 0.875rem 1.5rem;
        }

        /* =========== Stats Grid =========== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: var(--card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            transition: all var(--transition-normal);
            box-shadow: var(--card-shadow);
            text-decoration: none;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--card-shadow-hover);
            border-color: var(--accent-color);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon.total {
            background: linear-gradient(135deg, var(--accent-color), var(--info));
            color: var(--primary-foreground);
        }

        .stat-icon.active {
            background: linear-gradient(135deg, var(--success), #34d399);
            color: white;
        }

        .stat-icon.categories {
            background: linear-gradient(135deg, var(--warning), #fbbf24);
            color: white;
        }

        .stat-icon.images {
            background: linear-gradient(135deg, #8b5cf6, #a78bfa);
            color: white;
        }

        .stat-icon svg {
            width: 24px;
            height: 24px;
            fill: currentColor;
        }

        .stat-content {
            flex: 1;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
            line-height: 1;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 0.95rem;
            font-weight: 500;
        }

        /* =========== Products Card =========== */
        .products-card {
            background: var(--card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .glass-effect {
            background: var(--glass-base);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        /* =========== Table Header =========== */
        .table-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 1.5rem;
        }

        .table-search {
            flex: 1;
            min-width: 300px;
            position: relative;
        }

        .search-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            width: 20px;
            height: 20px;
            color: var(--text-secondary);
            pointer-events: none;
            z-index: 2;
        }

        .search-input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3rem;
            background: var(--input);
            border: 2px solid var(--border-color);
            border-radius: var(--radius);
            color: var(--text-primary);
            font-size: 1rem;
            transition: all var(--transition-normal);
            outline: none;
            padding-right: 3rem;
        }

        .search-input:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .search-input::placeholder {
            color: var(--text-muted);
        }

        .search-loader {
            position: absolute;
            right: 3rem;
            top: 50%;
            transform: translateY(-50%);
        }

        .loader-spinner {
            width: 20px;
            height: 20px;
            border: 2px solid var(--border-color);
            border-top-color: var(--accent-color);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        .clear-search-btn {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            background: transparent;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color var(--transition-fast);
            padding: 0;
        }

        .clear-search-btn:hover {
            color: var(--danger);
        }

        .clear-search-btn svg {
            width: 16px;
            height: 16px;
            fill: currentColor;
        }

        /* Search highlighting */
        .search-highlight {
            background-color: rgba(255, 235, 59, 0.3);
            color: inherit;
            padding: 0 2px;
            border-radius: 2px;
            font-weight: 600;
        }

        /* =========== Table Actions =========== */
        .table-actions {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .table-info {
            color: var(--text-secondary);
            font-size: 0.95rem;
            font-weight: 500;
            min-width: 200px;
        }

        .view-toggle {
            display: flex;
            gap: 0.5rem;
            background: var(--muted);
            padding: 0.25rem;
            border-radius: var(--radius);
        }

        .view-btn {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            border-radius: calc(var(--radius) - 0.25rem);
            color: var(--text-secondary);
            cursor: pointer;
            transition: all var(--transition-fast);
        }

        .view-btn:hover {
            color: var(--text-primary);
            background: var(--accent);
        }

        .view-btn.active {
            background: var(--card);
            color: var(--text-primary);
            box-shadow: var(--shadow-sm);
        }

        .view-btn svg {
            width: 18px;
            height: 18px;
            fill: currentColor;
        }

        /* =========== Products Table =========== */
        .products-table-view {
            overflow-x: auto;
        }

        .products-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .products-table thead {
            background: var(--muted);
        }

        .products-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-secondary);
            border-bottom: 2px solid var(--border-color);
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .products-table tbody tr {
            border-bottom: 1px solid var(--border-color);
            transition: all var(--transition-fast);
        }

        .products-table tbody tr:hover {
            background-color: var(--accent);
        }

        .products-table td {
            padding: 1rem;
            vertical-align: middle;
        }

        /* Barcode Cell */
        .barcode-cell {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .barcode-icon {
            width: 20px;
            height: 20px;
            color: var(--text-secondary);
            flex-shrink: 0;
        }

        .barcode-value {
            font-family: monospace;
            font-size: 0.95rem;
            color: var(--text-primary);
            font-weight: 500;
        }

        /* Product Name Cell */
        .product-name-cell {
            min-width: 200px;
        }

        .product-name {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
            line-height: 1.4;
        }

        .product-description {
            font-size: 0.875rem;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        /* Category & Brand Badges */
        .category-badge,
        .brand-badge {
            display: inline-block;
            padding: 0.375rem 0.75rem;
            background: var(--muted);
            color: var(--text-primary);
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            white-space: nowrap;
        }

        .category-badge {
            background: linear-gradient(135deg, var(--accent-color), var(--accent-color) / 0.1);
            color: var(--accent-color);
        }

        .brand-badge {
            background: linear-gradient(135deg, var(--info), var(--info) / 0.1);
            color: var(--info);
        }

        /* Status Indicator */
        .status-indicator {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-indicator.active .status-dot {
            background: var(--success);
        }

        .status-indicator.inactive .status-dot {
            background: var(--danger);
        }

        .status-text {
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Image Gallery */
        .image-gallery {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .image-thumbnail {
            width: 40px;
            height: 40px;
            border-radius: 6px;
            background-size: cover;
            background-position: center;
            position: relative;
            border: 1px solid var(--border-color);
            overflow: hidden;
            cursor: pointer;
            transition: transform var(--transition-fast);
        }

        .image-thumbnail:hover {
            transform: scale(1.05);
            z-index: 1;
        }

        .image-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.1);
        }

        .no-images {
            width: 40px;
            height: 40px;
            border-radius: 6px;
            background: var(--muted);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .no-images svg {
            width: 20px;
            height: 20px;
            color: var(--text-secondary);
        }

        .image-more {
            width: 40px;
            height: 40px;
            border-radius: 6px;
            background: var(--accent);
            color: var(--text-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            transition: all var(--transition-fast);
        }

        .image-more:hover {
            background: var(--accent-color);
            color: var(--primary-foreground);
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all var(--transition-fast);
            text-decoration: none;
        }

        .action-btn:hover {
            background: var(--accent);
            color: var(--text-primary);
        }

        .action-btn.edit:hover {
            background: var(--warning);
            color: white;
        }

        .action-btn.add-batch:hover {
            background: var(--success);
            color: white;
        }

        .action-btn.images:hover {
            background: var(--info);
            color: white;
        }

        .action-btn.delete:hover {
            background: var(--danger);
            color: white;
        }

        .action-btn svg {
            width: 18px;
            height: 18px;
            fill: currentColor;
        }

        /* Empty State */
        .no-products td {
            padding: 0 !important;
        }

        .empty-state {
            padding: 4rem 2rem;
            text-align: center;
        }

        .empty-icon {
            width: 64px;
            height: 64px;
            margin-bottom: 1.5rem;
            color: var(--text-muted);
        }

        .empty-state h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Table Footer */
        .table-footer {
            padding: 1.5rem;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .pagination-info {
            color: var(--text-secondary);
            font-size: 0.95rem;
            font-weight: 500;
        }

        .pagination-wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
        }

        .per-page-select {
            padding: 0.5rem 1rem;
            background: var(--input);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            color: var(--text-primary);
            font-size: 0.95rem;
            cursor: pointer;
            outline: none;
            transition: border-color var(--transition-fast);
        }

        .per-page-select:focus {
            border-color: var(--accent-color);
        }

        /* =========== Skeleton Loading =========== */
        .skeleton-row {
            display: table-row;
        }

        .skeleton-cell {
            padding: 1rem;
        }

        .skeleton-line {
            height: 16px;
            background: linear-gradient(90deg, var(--muted) 25%, var(--accent) 50%, var(--muted) 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 4px;
            margin: 8px 0;
        }

        .skeleton-line.short {
            width: 60%;
        }

        .skeleton-line.medium {
            width: 80%;
        }

        /* =========== Responsive Design =========== */
        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .products-table {
                min-width: 1000px;
            }
        }

        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: stretch;
            }

            .header-content {
                text-align: center;
            }

            .btn-primary {
                width: 100%;
                justify-content: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .table-header {
                flex-direction: column;
                align-items: stretch;
            }

            .table-search,
            .table-actions {
                width: 100%;
            }

            .table-actions {
                justify-content: space-between;
            }

            .table-footer {
                flex-direction: column;
                text-align: center;
                gap: 1.5rem;
            }

            .pagination-wrapper {
                order: -1;
            }
        }

        @media (max-width: 576px) {
            .container-fluid {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .page-title {
                font-size: 1.75rem;
            }

            .stat-card {
                padding: 1.25rem;
            }

            .stat-icon {
                width: 48px;
                height: 48px;
            }

            .stat-value {
                font-size: 1.75rem;
            }

            .products-table td,
            .products-table th {
                padding: 0.75rem;
            }

            .action-buttons {
                flex-wrap: wrap;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .page-title {
                font-size: 1.5rem;
            }

            .page-subtitle {
                font-size: 1rem;
            }

            .search-input {
                font-size: 0.95rem;
                padding: 0.75rem 1rem 0.75rem 2.75rem;
            }

            .search-icon {
                left: 0.75rem;
            }

            .view-toggle {
                align-self: center;
            }
        }

        /* =========== Image Modal =========== */
        .image-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 9999;
            display: none;
        }

        .modal-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(4px);
        }

        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            max-width: 90vw;
            max-height: 90vh;
            background: var(--card);
            border-radius: var(--radius);
            overflow: hidden;
            animation: popIn 0.3s ease-out;
        }

        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--danger);
            color: white;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform var(--transition-fast);
        }

        .modal-close:hover {
            transform: scale(1.1);
        }

        .modal-image {
            max-width: 100%;
            max-height: 90vh;
            display: block;
        }

        /* Search active state */
        .search-active .table-footer {
            display: none !important;
        }

        /* Batch badge styling */
        .batch-badge {
            display: inline-block;
            padding: 0.375rem 0.75rem;
            background: linear-gradient(135deg, var(--success), var(--success) / 0.1);
            color: var(--success);
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            white-space: nowrap;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        (function() {
            const NS = 'blp';
            const $ = (id) => document.getElementById(id);
            const ids = (suffix) => `${NS}_${suffix}`;

            function hardHideModal(modalEl) {
                if (!modalEl) return;

                // Remove visible state from markup
                modalEl.classList.remove('show');
                modalEl.style.display = 'none';
                modalEl.setAttribute('aria-hidden', 'true');

                // Remove body modal state/backdrops if any leaked
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('padding-right');

                document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
            }

            document.addEventListener('DOMContentLoaded', function() {
                const modalEl = $(ids('modal'));

                // FORCE CLOSE on page load (prevents auto open)
                hardHideModal(modalEl);

                // Then create instance normally (won't auto open)
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl, {
                    backdrop: true,
                    keyboard: true,
                    focus: true
                });

                // Also enforce hide after full load (extra safety for slow scripts)
                window.addEventListener('load', () => hardHideModal(modalEl));

                // Barcode print logic
                let selectedBatchId = null;

                function setChip(text, mode) {
                    const chip = $(ids('status_chip'));
                    if (!chip) return;
                    chip.textContent = text;
                }

                function resetModalUI() {
                    $(ids('product_name')).textContent = '—';
                    $(ids('preview_text')).textContent = '—';
                    const select = $(ids('select'));
                    select.innerHTML = '<option value="">Select batch</option>';
                    $(ids('print_btn')).disabled = true;
                    selectedBatchId = null;
                    setChip('Ready', 'info');
                }

                function setPreviewFromSelect() {
                    const select = $(ids('select'));
                    const preview = $(ids('preview_text'));
                    const printBtn = $(ids('print_btn'));

                    const val = select.value ? String(select.value) : '';
                    if (!val) {
                        preview.textContent = '—';
                        printBtn.disabled = true;
                        selectedBatchId = null;
                        setChip('Select a batch', 'warn');
                        return;
                    }

                    selectedBatchId = val;
                    preview.textContent = select.options[select.selectedIndex]?.text || '—';
                    printBtn.disabled = false;
                    setChip('Ready to print', 'ok');
                }

                function fillForProduct(productId, productName) {
                    $(ids('product_name')).textContent = productName || '—';

                    const jsonEl = document.getElementById(`${NS}_batches_${productId}`);
                    let batches = [];
                    try {
                        batches = JSON.parse(jsonEl?.textContent || '[]');
                    } catch (e) {
                        batches = [];
                    }

                    const select = $(ids('select'));
                    select.innerHTML = '';

                    const ph = document.createElement('option');
                    ph.value = '';
                    ph.textContent = 'Select batch';
                    select.appendChild(ph);

                    if (!batches.length) {
                        const opt = document.createElement('option');
                        opt.value = '';
                        opt.textContent = 'No batch found';
                        select.appendChild(opt);
                        $(ids('preview_text')).textContent = '—';
                        $(ids('print_btn')).disabled = true;
                        selectedBatchId = null;
                        setChip('No batches', 'warn');
                        return;
                    }

                    batches.forEach(b => {
                        const opt = document.createElement('option');
                        opt.value = String(b.id);
                        opt.textContent = b.text;
                        select.appendChild(opt);
                    });

                    select.value = String(batches[0].id);
                    setPreviewFromSelect();
                }

                document.addEventListener('click', function(e) {
                    const btn = e.target.closest(`.${NS}-trigger`);
                    if (!btn) return;

                    e.preventDefault();

                    const productId = btn.getAttribute(`data-${NS}-product-id`);
                    const productName = btn.getAttribute(`data-${NS}-product-name`) || '—';

                    fillForProduct(productId, productName);
                    modal.show();
                });

                $(ids('select'))?.addEventListener('change', setPreviewFromSelect);

                $(ids('print_btn'))?.addEventListener('click', function() {
                    if (!selectedBatchId) return;
                    window.open(`{{ url('/batches') }}/${selectedBatchId}/barcode/print`, '_blank');
                    modal.hide();
                });

                modalEl?.addEventListener('hidden.bs.modal', function() {
                    resetModalUI();
                });
            });

            // Delete confirmation logic
            let currentDeleteForm = null;

            window.showDeleteModal = function(productId, productName, form) {
                currentDeleteForm = form;
                document.getElementById('deleteProductName').textContent = productName;
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
                deleteModal.show();
            };

            document.addEventListener('DOMContentLoaded', function() {
                // Setup delete buttons
                const deleteButtons = document.querySelectorAll('.delete-product-btn');
                deleteButtons.forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        const productId = this.getAttribute('data-product-id');
                        const productName = this.getAttribute('data-product-name');
                        const form = this.closest('.delete-form');
                        showDeleteModal(productId, productName, form);
                    });
                });

                // Confirm delete button
                const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
                if (confirmDeleteBtn) {
                    confirmDeleteBtn.addEventListener('click', function() {
                        if (currentDeleteForm) {
                            // Show loading state on button
                            this.disabled = true;
                            this.innerHTML = 'Deleting...';

                            // Submit the form
                            currentDeleteForm.submit();
                        }
                    });
                }
            });
        })();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // =========== Global Variables ===========
            const searchInput = document.getElementById('liveSearchInput');
            const searchLoader = document.getElementById('searchLoader');
            const clearSearchBtn = document.getElementById('clearSearchBtn');
            const productsTableBody = document.getElementById('productsTableBody');
            const tableInfo = document.getElementById('tableInfo');
            const paginationSection = document.getElementById('paginationSection');
            const totalProductsElement = document.getElementById('total-products-count');
            const activeProductsElement = document.getElementById('active-products-count');

            // Store original data
            let originalTableHTML = productsTableBody.innerHTML;
            let originalTableInfo = tableInfo.innerHTML;
            let originalTotalProducts = totalProductsElement ? parseInt(totalProductsElement.textContent) || 0 : 0;
            let originalActiveProducts = activeProductsElement ? parseInt(activeProductsElement.textContent) || 0 :
                0;
            let isSearching = false;
            let searchTimeout = null;
            let searchAbortController = null;

            // =========== Initialize Search ===========
            if (searchInput) {
                const searchUrl = searchInput.dataset.searchUrl;

                if (!searchUrl) {
                    console.error('Search URL not found');
                    searchInput.disabled = true;
                    searchInput.placeholder = 'Search disabled';
                    return;
                }

                // Input event with debouncing
                searchInput.addEventListener('input', handleSearchInput);

                // Clear search button
                clearSearchBtn.addEventListener('click', clearSearch);

                // Keyboard shortcuts
                searchInput.addEventListener('keydown', handleKeyboardShortcuts);

                // Show/hide clear button based on input
                searchInput.addEventListener('input', function() {
                    clearSearchBtn.style.display = this.value.trim() ? 'flex' : 'none';
                });
            }

            // =========== Event Handlers ===========
            function handleSearchInput(e) {
                const query = e.target.value.trim();

                // Clear previous timeout and abort request
                clearTimeout(searchTimeout);
                if (searchAbortController) {
                    searchAbortController.abort();
                }

                // Show/hide clear button
                clearSearchBtn.style.display = query ? 'flex' : 'none';

                // If query is empty, restore original table
                if (query.length === 0) {
                    restoreOriginalTable();
                    return;
                }

                // Show loader and set searching state
                searchLoader.style.display = 'block';
                isSearching = true;

                // Debounce search to prevent too many requests
                searchTimeout = setTimeout(() => {
                    performLiveSearch(query);
                }, 350);
            }

            function handleKeyboardShortcuts(e) {
                // Clear search with Escape
                if (e.key === 'Escape' && searchInput.value.trim()) {
                    clearSearch();
                }

                // Focus search with Ctrl+K or Cmd+K
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    searchInput.focus();
                }
            }

            // =========== Search Functions ===========
            async function performLiveSearch(query) {
                if (query.length < 2) {
                    filterLocalProducts(query);
                    return;
                }

                searchAbortController = new AbortController();

                try {
                    const response = await fetch(
                        `${searchInput.dataset.searchUrl}?q=${encodeURIComponent(query)}`, {
                            signal: searchAbortController.signal,
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    ?.content || ''
                            }
                        }
                    );

                    if (!response.ok) throw new Error(`Search failed: ${response.status}`);

                    const products = await response.json();
                    updateTableWithSearchResults(products, query);

                } catch (error) {
                    if (error.name !== 'AbortError') {
                        console.error('Search error:', error);
                        showSearchError();
                    }
                } finally {
                    searchLoader.style.display = 'none';
                }
            }

            function filterLocalProducts(query) {
                showLoadingSkeleton();
                setTimeout(() => {
                    restoreOriginalTable();
                    searchLoader.style.display = 'none';
                }, 150);
            }

            function updateTableWithSearchResults(products, query) {
                if (!products || products.length === 0) {
                    showNoResults(query);
                    return;
                }

                if (paginationSection) paginationSection.style.display = 'none';

                const rowsHTML = products.map(product => {
                    const previewImages = product.images ? product.images.slice(0, 3) : [];
                    const remainingCount = product.images ? product.images.length - previewImages.length :
                    0;

                    const highlightedName = highlightSearchTerm(product.name, query);
                    const highlightedBarcode = highlightSearchTerm(product.barcode, query);

                    return `
                <tr class="product-row" data-product-id="${product.id}">
                    <td class="cell-barcode">
                        <div class="barcode-cell">
                            <svg viewBox="0 0 24 24" class="barcode-icon">
                                <path d="M2 6h2v12H2zm3 0h1v12H5zm2 0h3v12H7zm4 0h1v12h-1zm3 0h2v12h-2zm4 0h1v12h-1zm3 0h1v12h-1zm2 0h1v12h-1z"/>
                            </svg>
                            <span class="barcode-value">${highlightedBarcode}</span>
                        </div>
                    </td>
                    <td class="cell-name">
                        <div class="product-name-cell">
                            <div class="product-name">${highlightedName}</div>
                        </div>
                    </td>
                    <td class="cell-category">
                        ${product.category ? `<span class="category-badge">${escapeHtml(product.category.name)}</span>` : '<span class="text-muted">Uncategorized</span>'}
                    </td>
                    <td class="cell-brand">
                        ${product.brand ? `<span class="brand-badge">${escapeHtml(product.brand.name)}</span>` : '<span class="text-muted">No Brand</span>'}
                    </td>
                    <td class="cell-batches">
                        ${product.batches && product.batches.length > 0 ? `<span class="batch-badge">${product.batches.length}</span>` : '<span class="text-muted">No Batch</span>'}
                    </td>
                    <td class="cell-status">
                        <div class="status-indicator ${product.is_active ? 'active' : 'inactive'}">
                            <span class="status-dot"></span>
                            <span class="status-text">${product.is_active ? 'Active' : 'Inactive'}</span>
                        </div>
                    </td>
                    <td class="cell-images">
                        <div class="image-gallery">
                            ${previewImages.length > 0 ? previewImages.map(image => `
                                    <div class="image-thumbnail"
                                         style="background-image: url('{{ asset('storage/') }}/${image.image_path}')"
                                         title="${escapeHtml(product.name)}"
                                         onclick="showImageModal('{{ asset('storage/') }}/${image.image_path}')">
                                        <div class="image-overlay"></div>
                                    </div>
                                `).join('') : `<div class="no-images"><svg viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg></div>`}
                            ${remainingCount > 0 ? `<a href="{{ url('products/${product.id}/images') }}" class="image-more">+${remainingCount}</a>` : ''}
                        </div>
                    </td>
                    <td class="cell-actions">
                        <div class="action-buttons">
                            <a href="{{ url('products/${product.id}/edit') }}" class="action-btn edit" title="Edit Product">
                                <svg viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                            </a>
                            <a href="{{ url('product-batches/create?product=${product.id}') }}" class="action-btn add-batch" title="Add Batch">
                                <svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                            </a>
                            <a href="{{ url('products/${product.id}/images') }}" class="action-btn images" title="Manage Images">
                                <svg viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                            </a>
                            <a href="{{ url('products/${product.id}') }}" class="action-btn view" title="View Product">
                                <svg viewBox="0 0 24 24"><path d="M12 5c-7.633 0-11 6.5-11 7s3.367 7 11 7 11-6.5 11-7-3.367-7-11-7zm0 12c-2.757 0-5-2.243-5-5s2.243-5 5-5 5 2.243 5 5-2.243 5-5 5z"/></svg>
                            </a>
                            <form action="{{ url('products/${product.id}') }}" method="POST" class="delete-form" style="display: inline-block;">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="button" class="action-btn delete delete-product-btn" data-product-id="${product.id}" data-product-name="${escapeHtml(product.name)}" title="Delete Product">
                                    <svg viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            `;
                }).join('');

                productsTableBody.style.opacity = '0';
                productsTableBody.innerHTML = rowsHTML;
                updateTableInfo(products.length, 1, products.length);

                setTimeout(() => {
                    productsTableBody.style.transition = 'opacity 0.3s ease';
                    productsTableBody.style.opacity = '1';
                }, 10);

                attachEventListeners();
            }

            function showNoResults(query) {
                productsTableBody.innerHTML = `
            <tr class="no-products">
                <td colspan="8">
                    <div class="empty-state">
                        <svg viewBox="0 0 24 24" class="empty-icon">
                            <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5z"/>
                        </svg>
                        <h3>No Products Found</h3>
                        <p>No products found for "${escapeHtml(query)}"</p>
                        <button onclick="clearSearch()" class="btn-primary">Clear Search</button>
                    </div>
                </td>
            </tr>
        `;
                updateTableInfo(0, 0, 0);
                if (paginationSection) paginationSection.style.display = 'none';
            }

            function showLoadingSkeleton() {
                const skeletonRows = Array.from({
                    length: 5
                }, () => `
            <tr class="skeleton-row">
                <td class="skeleton-cell" colspan="8">
                    <div class="skeleton-line medium"></div>
                    <div class="skeleton-line short"></div>
                </td>
             </tr>
        `).join('');
                productsTableBody.innerHTML = skeletonRows;
                if (tableInfo) tableInfo.textContent = 'Searching...';
            }

            function showSearchError() {
                productsTableBody.innerHTML = `
            <tr class="no-products">
                <td colspan="8">
                    <div class="empty-state">
                        <svg viewBox="0 0 24 24" class="empty-icon">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                        </svg>
                        <h3>Search Error</h3>
                        <p>Failed to load search results. Please try again.</p>
                        <button onclick="clearSearch()" class="btn-primary">Clear Search</button>
                    </div>
                </td>
            </tr>
        `;
            }

            // =========== Utility Functions ===========
            function restoreOriginalTable() {
                if (!isSearching) return;

                isSearching = false;
                if (searchLoader) searchLoader.style.display = 'none';
                if (clearSearchBtn) clearSearchBtn.style.display = 'none';
                if (paginationSection) paginationSection.style.display = '';

                productsTableBody.style.opacity = '0';
                setTimeout(() => {
                    productsTableBody.innerHTML = originalTableHTML;
                    if (tableInfo) tableInfo.innerHTML = originalTableInfo;
                    if (totalProductsElement) totalProductsElement.textContent = originalTotalProducts;
                    if (activeProductsElement) activeProductsElement.textContent = originalActiveProducts;

                    productsTableBody.style.transition = 'opacity 0.3s ease';
                    productsTableBody.style.opacity = '1';

                    attachEventListeners();
                }, 150);
            }

            window.clearSearch = function() {
                if (searchInput) {
                    searchInput.value = '';
                    if (clearSearchBtn) clearSearchBtn.style.display = 'none';
                    restoreOriginalTable();
                }
            };

            function updateTableInfo(total, start, end) {
                if (tableInfo) {
                    tableInfo.textContent = `Showing ${start}-${end} of ${total} products`;
                }
                if (totalProductsElement) totalProductsElement.textContent = total;
            }

            function highlightSearchTerm(text, query) {
                if (!text || !query) return escapeHtml(text);
                const escapedQuery = escapeRegex(query);
                const regex = new RegExp(`(${escapedQuery})`, 'gi');
                return escapeHtml(text).replace(regex, '<mark class="search-highlight">$1</mark>');
            }

            function escapeRegex(string) {
                return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            }

            function escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // =========== Event Listener Attachment ===========
            function attachEventListeners() {
                // Image thumbnail click handlers
                document.querySelectorAll('.image-thumbnail').forEach(thumbnail => {
                    thumbnail.addEventListener('click', function(e) {
                        e.preventDefault();
                        const imageUrl = this.style.backgroundImage
                            .replace('url("', '')
                            .replace('")', '');
                        showImageModal(imageUrl);
                    });
                });

                // Delete button handlers
                const deleteButtons = document.querySelectorAll('.delete-product-btn');
                deleteButtons.forEach(button => {
                    button.removeEventListener('click', handleDeleteClick);
                    button.addEventListener('click', handleDeleteClick);
                });
            }

            function handleDeleteClick(e) {
                e.preventDefault();
                const productId = this.getAttribute('data-product-id');
                const productName = this.getAttribute('data-product-name');
                const form = this.closest('.delete-form');

                if (confirm(`Are you sure you want to delete "${productName}"? This action cannot be undone.`)) {
                    form.submit();
                }
            }

            // =========== Global Functions ===========
            window.showImageModal = function(imageUrl) {
                const modal = document.createElement('div');
                modal.className = 'image-modal';
                modal.innerHTML = `
            <div class="modal-overlay" onclick="closeImageModal(this)"></div>
            <div class="modal-content">
                <button class="modal-close" onclick="closeImageModal(this)">&times;</button>
                <img src="${imageUrl}" alt="Product Image" class="modal-image">
            </div>
        `;
                document.body.appendChild(modal);
                modal.style.display = 'block';
            };

            window.closeImageModal = function(element) {
                const modal = element.closest('.image-modal');
                if (modal) {
                    document.body.removeChild(modal);
                }
            };

            // =========== View Toggle ===========
            const viewButtons = document.querySelectorAll('.view-btn');
            const tableView = document.getElementById('tableView');
            const gridView = document.getElementById('gridView');

            if (viewButtons.length > 0) {
                viewButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const view = this.dataset.view;
                        viewButtons.forEach(btn => btn.classList.remove('active'));
                        this.classList.add('active');

                        if (view === 'table') {
                            if (tableView) tableView.style.display = 'block';
                            if (gridView) gridView.style.display = 'none';
                        } else {
                            if (tableView) tableView.style.display = 'none';
                            if (gridView) gridView.style.display = 'block';
                        }
                    });
                });
            }

            // Initial event listener attachment
            attachEventListeners();
        });
    </script>
@endsection
