@extends('layouts.app')

@section('content')
<div class="container-fluid py-3 ui-page" data-ui-page>

    {{-- Page Header --}}
    <div class="ui-page-header mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('products.show', $product) }}" class="ui-back-btn" data-animate="scale-hover">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h1 class="ui-page-title mb-1">{{ $product->name }}</h1>
                    <div class="ui-page-subtitle">
                        <span class="ui-badge">Batches</span>
                        <span class="ui-sep">•</span>
                        <span>Barcode: <code>{{ $product->barcode ?? 'N/A' }}</code></span>
                        <span class="ui-sep">•</span>
                        <span>Total: <strong>{{ $totalBatches }}</strong> batches</span>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button class="ui-btn ui-btn-primary" type="button" data-ui-open="#drawerAdd">
                    <i class="bi bi-plus-lg"></i> Quick Add
                </button>
                <a href="{{ route('product.batches.create', $product) }}" class="ui-btn ui-btn-outline">
                    <i class="bi bi-plus-square"></i> Full Create
                </a>
                <div class="ui-dropdown">
                    <button class="ui-btn ui-btn-ghost" type="button" data-dropdown-toggle>
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <div class="ui-dropdown-menu">
                        <a href="{{ route('products.edit', $product) }}" class="ui-dropdown-item">
                            <i class="bi bi-pencil-square"></i> Edit Product
                        </a>
                        <a href="{{ route('products.show', $product) }}" class="ui-dropdown-item">
                            <i class="bi bi-eye"></i> View Product
                        </a>
                        <div class="ui-dropdown-divider"></div>
                        <button class="ui-dropdown-item" type="button" data-export="csv">
                            <i class="bi bi-download"></i> Export CSV
                        </button>
                        <button class="ui-dropdown-item" type="button" data-export="pdf">
                            <i class="bi bi-file-pdf"></i> Export PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="ui-stat-card" data-stat="active" data-animate="slide-up">
                <div class="ui-stat-icon success">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <div class="ui-stat-content">
                    <div class="ui-stat-value" data-counter="{{ $metrics['activeCount'] }}">0</div>
                    <div class="ui-stat-label">Active Batches</div>
                </div>
                <button class="ui-stat-action" data-filter="active" title="Filter active batches">
                    <i class="bi bi-filter"></i>
                </button>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="ui-stat-card" data-stat="expiring" data-animate="slide-up" data-animate-delay="50">
                <div class="ui-stat-icon warning">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div class="ui-stat-content">
                    <div class="ui-stat-value" data-counter="{{ $metrics['expiringSoon'] }}">0</div>
                    <div class="ui-stat-label">Expiring Soon</div>
                </div>
                <button class="ui-stat-action" data-filter="expSoon" title="Filter expiring batches">
                    <i class="bi bi-filter"></i>
                </button>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="ui-stat-card" data-stat="expired" data-animate="slide-up" data-animate-delay="100">
                <div class="ui-stat-icon danger">
                    <i class="bi bi-x-octagon"></i>
                </div>
                <div class="ui-stat-content">
                    <div class="ui-stat-value" data-counter="{{ $metrics['expired'] }}">0</div>
                    <div class="ui-stat-label">Expired</div>
                </div>
                <button class="ui-stat-action" data-filter="expired" title="Filter expired batches">
                    <i class="bi bi-filter"></i>
                </button>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="ui-stat-card" data-stat="gifts" data-animate="slide-up" data-animate-delay="150">
                <div class="ui-stat-icon info">
                    <i class="bi bi-gift"></i>
                </div>
                <div class="ui-stat-content">
                    <div class="ui-stat-value" data-counter="{{ $metrics['giftOffers'] }}">0</div>
                    <div class="ui-stat-label">Gift Offers</div>
                </div>
                <button class="ui-stat-action" data-filter="gift" title="Filter gift batches">
                    <i class="bi bi-filter"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Action Bar --}}
    <div class="ui-action-bar mb-4">
        <div class="d-flex align-items-center flex-wrap gap-3">
            {{-- Search --}}
            <div class="ui-search-box flex-grow-1" style="max-width: 400px;">
                <i class="bi bi-search"></i>
                <input type="text"
                       id="searchInput"
                       class="ui-search-input"
                       placeholder="Search batches, SKU, notes..."
                       autocomplete="off"
                       data-search>
                <button class="ui-search-clear" id="clearSearch" type="button">
                    <i class="bi bi-x"></i>
                </button>
            </div>

            {{-- Quick Actions --}}
            <div class="d-flex gap-2">
                <button class="ui-btn ui-btn-outline" type="button" data-bulk-action="export">
                    <i class="bi bi-download"></i> Export
                </button>
                <button class="ui-btn ui-btn-outline" type="button" data-bulk-action="print">
                    <i class="bi bi-printer"></i> Print
                </button>
                <div class="ui-dropdown">
                    <button class="ui-btn ui-btn-outline" type="button" data-dropdown-toggle>
                        <i class="bi bi-sliders"></i> View Options
                    </button>
                    <div class="ui-dropdown-menu">
                        <button class="ui-dropdown-item" type="button" data-view="table">
                            <i class="bi bi-table"></i> Table View
                        </button>
                        <button class="ui-dropdown-item" type="button" data-view="grid">
                            <i class="bi bi-grid-3x3-gap"></i> Grid View
                        </button>
                        <div class="ui-dropdown-divider"></div>
                        <button class="ui-dropdown-item" type="button" data-columns-toggle>
                            <i class="bi bi-layout-sidebar-inset"></i> Columns
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters Bar --}}
        <div class="ui-filters-bar mt-3">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <span class="ui-filters-label">Filters:</span>
                <div class="ui-filter-chips">
                    <button class="ui-filter-chip" data-filter="inStock">
                        <i class="bi bi-box-seam"></i> In Stock
                    </button>
                    <button class="ui-filter-chip" data-filter="outStock">
                        <i class="bi bi-slash-circle"></i> Out of Stock
                    </button>
                    <button class="ui-filter-chip" data-filter="active">
                        <i class="bi bi-check2-circle"></i> Active
                    </button>
                    <button class="ui-filter-chip" data-filter="gift">
                        <i class="bi bi-gift"></i> With Gift
                    </button>
                    <button class="ui-filter-chip" data-filter="expSoon">
                        <i class="bi bi-hourglass-split"></i> Expiring Soon
                    </button>
                    <button class="ui-filter-chip" data-filter="expired">
                        <i class="bi bi-x-octagon"></i> Expired
                    </button>
                </div>
                <button class="ui-btn ui-btn-text" id="clearFilters" type="button">
                    <i class="bi bi-x-circle"></i> Clear All
                </button>
            </div>
        </div>
    </div>

    {{-- Main Content Area --}}
    <div class="ui-content-area">
        {{-- Table View (Default) --}}
        <div class="ui-table-view" id="tableView" data-view="active">
            <div class="ui-table-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h3 class="ui-table-title">Batches List</h3>
                    <div class="d-flex align-items-center gap-2">
                        <span class="ui-table-count" data-table-count>{{ $batches->count() }} items</span>
                        <select class="ui-select-sm" id="sortSelect">
                            <option value="latest">Latest First</option>
                            <option value="expirySoon">Expiry Soonest</option>
                            <option value="qtyLow">Lowest Quantity</option>
                            <option value="qtyHigh">Highest Quantity</option>
                            <option value="sellHigh">Highest Sell Price</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="ui-table-container">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">
                                <input type="checkbox" class="ui-checkbox" id="selectAll">
                            </th>
                            <th style="width: 80px;">#</th>
                            <th>Batch Details</th>
                            <th style="width: 120px;">Quantity</th>
                            <th style="width: 120px;">Prices</th>
                            <th style="width: 120px;">Expiry</th>
                            <th style="width: 120px;">Status</th>
                            <th style="width: 80px;" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @foreach($batches as $batch)
                            @php
                                $qty = (float)$batch->quantity;
                                $expiryISO = $batch->expiry_date ? $batch->expiry_date->format('Y-m-d') : '';
                                $expired = $batch->expiry_date ? $batch->expiry_date->isPast() : false;
                                $expSoon = $batch->expiry_date ? ($batch->expiry_date->isFuture() && $batch->expiry_date->lte(now()->addDays(30))) : false;
                                $giftActive = (bool)($batch->is_free_offer_active ?? false);
                                $gift = $batch->freeProduct;

                                $daysLeft = $batch->expiry_date ? now()->startOfDay()->diffInDays($batch->expiry_date->startOfDay(), false) : null;
                                $progress = $daysLeft && $daysLeft <= 60 ? max(0, min(100, (int)(100 - ($daysLeft/60)*100))) : 0;
                            @endphp

                            <tr class="ui-table-row {{ $expired ? 'is-expired' : ($expSoon ? 'is-warning' : '') }}"
                                data-batch-id="{{ $batch->id }}"
                                data-row
                                data-text="{{ strtolower(($batch->batch_no ?? '').' '.($batch->batch_sku ?? '').' '.($batch->notes ?? '').' '.($gift?->name ?? '')) }}"
                                data-qty="{{ $qty }}"
                                data-expired="{{ $expired ? 1 : 0 }}"
                                data-exps="{{ $expSoon ? 1 : 0 }}"
                                data-gift="{{ $giftActive ? 1 : 0 }}"
                                data-active="{{ $batch->is_active ? 1 : 0 }}"
                                data-sell="{{ (float)$batch->sell_price }}"
                                data-expiry="{{ $expiryISO }}"
                                data-animate="slide-up"
                                data-animate-delay="{{ $loop->index * 30 }}"
                            >
                                <td>
                                    <input type="checkbox" class="ui-checkbox batch-checkbox" value="{{ $batch->id }}">
                                </td>
                                <td class="ui-table-index">
                                    {{ $loop->iteration + ($batches->currentPage()-1)*$batches->perPage() }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="ui-status-indicator {{ $expired ? 'danger' : ($expSoon ? 'warning' : ($batch->is_active ? 'success' : 'muted')) }}"></div>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <a href="{{ route('product.batches.show', $batch) }}"
                                                   class="ui-table-link"
                                                   title="View batch details">
                                                    <strong>{{ $batch->batch_no ?? 'N/A' }}</strong>
                                                </a>
                                                <span class="ui-badge ui-badge-sm">{{ $batch->batch_sku ?? '—' }}</span>
                                                @if($giftActive)
                                                    <span class="ui-badge ui-badge-sm info" title="Has gift offer">
                                                        <i class="bi bi-gift"></i>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="ui-table-meta">
                                                <span class="ui-mono">{{ $batch->unit ?? '—' }}</span>
                                                @if($batch->notes)
                                                    <span class="ui-sep">•</span>
                                                    <span class="text-truncate">{{ Str::limit($batch->notes, 60) }}</span>
                                                @endif
                                            </div>
                                            @if($giftActive && $gift)
                                                <div class="ui-table-subtext mt-1">
                                                    <i class="bi bi-gift text-info"></i>
                                                    Gift:
                                                    <a href="{{ route('product.batches.by-product', $gift) }}"
                                                       class="ui-link">
                                                        {{ $gift->name }}
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="ui-table-value {{ $qty <= 0 ? 'text-danger' : ($qty <= 10 ? 'text-warning' : '') }}">
                                        {{ number_format($qty, 3) }}
                                    </div>
                                    <div class="ui-table-subtext">
                                        @if($qty <= 0)
                                            <span class="text-danger">Out of stock</span>
                                        @elseif($qty <= 10)
                                            <span class="text-warning">Low stock</span>
                                        @else
                                            <span class="text-muted">In stock</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="ui-table-stack">
                                        <div class="ui-table-value">
                                            Buy: <span class="ui-mono">{{ number_format((float)$batch->buy_price, 2) }}</span>
                                        </div>
                                        <div class="ui-table-value">
                                            Sell: <span class="ui-mono">{{ number_format((float)$batch->sell_price, 2) }}</span>
                                        </div>
                                        @if($batch->discounted_price || $batch->discount_percentage)
                                            <div class="ui-table-subtext text-success">
                                                <i class="bi bi-tag"></i>
                                                @if($batch->discounted_price)
                                                    -{{ number_format((float)$batch->discounted_price, 2) }}
                                                @else
                                                    -{{ number_format((float)$batch->discount_percentage, 2) }}%
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($batch->expiry_date)
                                        <div class="ui-table-value">
                                            {{ $batch->expiry_date->format('d M Y') }}
                                        </div>
                                        <div class="ui-table-subtext">
                                            @if($daysLeft < 0)
                                                <span class="text-danger">
                                                    Expired {{ abs($daysLeft) }} days ago
                                                </span>
                                            @else
                                                <span class="{{ $daysLeft <= 30 ? 'text-warning' : 'text-muted' }}">
                                                    {{ $daysLeft }} days left
                                                </span>
                                            @endif
                                        </div>
                                        @if($daysLeft <= 60)
                                            <div class="ui-progress mt-1">
                                                <div class="ui-progress-bar" style="width: {{ $progress }}%"></div>
                                            </div>
                                        @endif
                                    @else
                                        <span class="ui-table-muted">No expiry</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        @if($expired)
                                            <span class="ui-status-badge danger">Expired</span>
                                        @elseif($qty > 0)
                                            <span class="ui-status-badge success">In Stock</span>
                                        @else
                                            <span class="ui-status-badge muted">Out of Stock</span>
                                        @endif
                                        <span class="ui-status-badge {{ $batch->is_active ? 'success' : 'muted' }}">
                                            {{ $batch->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="ui-table-actions">
                                        <div class="ui-dropdown">
                                            <button class="ui-btn ui-btn-icon" type="button" data-dropdown-toggle>
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <div class="ui-dropdown-menu ui-dropdown-menu-right">
                                                <a href="{{ route('product.batches.show', $batch) }}" class="ui-dropdown-item">
                                                    <i class="bi bi-eye"></i> View Details
                                                </a>
                                                <button class="ui-dropdown-item" type="button"
                                                        data-ui-open="#drawerView"
                                                        data-batch-view="{{ $batch->id }}">
                                                    <i class="bi bi-lightning"></i> Quick View
                                                </button>
                                                <a href="{{ route('product.batches.edit', $batch) }}" class="ui-dropdown-item">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </a>
                                                <div class="ui-dropdown-divider"></div>
                                                <form method="POST" action="{{ route('product.batches.destroy', $batch) }}"
                                                      onsubmit="return confirm('Delete this batch?')" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button class="ui-dropdown-item danger" type="submit">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($batches->isEmpty())
                    <div class="ui-empty-state">
                        <div class="ui-empty-icon">
                            <i class="bi bi-inbox"></i>
                        </div>
                        <h4>No batches found</h4>
                        <p class="text-muted">No batches have been added for this product yet.</p>
                        <button class="ui-btn ui-btn-primary" data-ui-open="#drawerAdd">
                            <i class="bi bi-plus-lg"></i> Add First Batch
                        </button>
                    </div>
                @endif
            </div>

            {{-- Bulk Actions Bar --}}
            <div class="ui-bulk-actions" id="bulkActions" style="display: none;">
                <div class="d-flex align-items-center justify-content-between p-3">
                    <div class="d-flex align-items-center gap-3">
                        <span id="selectedCount">0 items selected</span>
                        <div class="ui-dropdown">
                            <button class="ui-btn ui-btn-outline" type="button" data-dropdown-toggle>
                                Bulk Actions
                            </button>
                            <div class="ui-dropdown-menu">
                                <button class="ui-dropdown-item" type="button" data-bulk-action="activate">
                                    <i class="bi bi-check2-circle"></i> Activate
                                </button>
                                <button class="ui-dropdown-item" type="button" data-bulk-action="deactivate">
                                    <i class="bi bi-slash-circle"></i> Deactivate
                                </button>
                                <div class="ui-dropdown-divider"></div>
                                <button class="ui-dropdown-item danger" type="button" data-bulk-action="delete">
                                    <i class="bi bi-trash"></i> Delete Selected
                                </button>
                            </div>
                        </div>
                    </div>
                    <button class="ui-btn ui-btn-text" type="button" id="clearSelection">
                        <i class="bi bi-x"></i> Clear Selection
                    </button>
                </div>
            </div>

            {{-- Pagination --}}
            @if($batches->hasPages())
                <div class="ui-table-footer">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="ui-table-pagination-info">
                            Showing {{ $batches->firstItem() }} to {{ $batches->lastItem() }} of {{ $batches->total() }} entries
                        </div>
                        <div class="ui-pagination">
                            {{ $batches->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Grid View (Hidden by default) --}}
        <div class="ui-grid-view" id="gridView" data-view="hidden" style="display: none;">
            <div class="ui-grid-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h3 class="ui-grid-title">Grid View</h3>
                    <div class="d-flex align-items-center gap-2">
                        <span class="ui-grid-count" data-grid-count>{{ $batches->count() }} items</span>
                        <select class="ui-select-sm" id="gridSort">
                            <option value="latest">Latest First</option>
                            <option value="expirySoon">Expiry Soonest</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="ui-grid-container">
                <div class="row g-3" id="gridBody">
                    @foreach($batches as $batch)
                        @php
                            $qty = (float)$batch->quantity;
                            $expired = $batch->expiry_date ? $batch->expiry_date->isPast() : false;
                            $expSoon = $batch->expiry_date ? ($batch->expiry_date->isFuture() && $batch->expiry_date->lte(now()->addDays(30))) : false;
                            $giftActive = (bool)($batch->is_free_offer_active ?? false);
                            $gift = $batch->freeProduct;
                            $daysLeft = $batch->expiry_date ? now()->startOfDay()->diffInDays($batch->expiry_date->startOfDay(), false) : null;
                        @endphp

                        <div class="col-12 col-md-6 col-xl-4"
                             data-batch-id="{{ $batch->id }}"
                             data-row
                             data-text="{{ strtolower(($batch->batch_no ?? '').' '.($batch->batch_sku ?? '').' '.($batch->notes ?? '').' '.($gift?->name ?? '')) }}"
                             data-qty="{{ $qty }}"
                             data-expired="{{ $expired ? 1 : 0 }}"
                             data-exps="{{ $expSoon ? 1 : 0 }}"
                             data-gift="{{ $giftActive ? 1 : 0 }}"
                             data-active="{{ $batch->is_active ? 1 : 0 }}"
                             data-sell="{{ (float)$batch->sell_price }}"
                             data-expiry="{{ $batch->expiry_date ? $batch->expiry_date->format('Y-m-d') : '' }}"
                             data-animate="pop"
                             data-animate-delay="{{ $loop->index * 50 }}"
                        >
                            <div class="ui-grid-card {{ $expired ? 'is-expired' : ($expSoon ? 'is-warning' : '') }}">
                                <div class="ui-grid-card-header">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="ui-status-indicator {{ $expired ? 'danger' : ($expSoon ? 'warning' : ($batch->is_active ? 'success' : 'muted')) }}"></div>
                                            <a href="{{ route('product.batches.show', $batch) }}"
                                               class="ui-grid-card-title">
                                                {{ $batch->batch_no ?? 'N/A' }}
                                            </a>
                                        </div>
                                        <div class="ui-grid-card-actions">
                                            <input type="checkbox" class="ui-checkbox grid-checkbox" value="{{ $batch->id }}">
                                            <div class="ui-dropdown">
                                                <button class="ui-btn ui-btn-icon" type="button" data-dropdown-toggle>
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <div class="ui-dropdown-menu ui-dropdown-menu-right">
                                                    <a href="{{ route('product.batches.show', $batch) }}" class="ui-dropdown-item">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                    <a href="{{ route('product.batches.edit', $batch) }}" class="ui-dropdown-item">
                                                        <i class="bi bi-pencil-square"></i> Edit
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ui-grid-card-meta mt-2">
                                        <span class="ui-badge ui-badge-sm">{{ $batch->batch_sku ?? '—' }}</span>
                                        <span class="ui-badge ui-badge-sm">{{ $batch->unit ?? '—' }}</span>
                                        @if($giftActive)
                                            <span class="ui-badge ui-badge-sm info">
                                                <i class="bi bi-gift"></i> Gift
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="ui-grid-card-body">
                                    <div class="ui-grid-card-section">
                                        <div class="ui-grid-card-label">Quantity</div>
                                        <div class="ui-grid-card-value {{ $qty <= 0 ? 'text-danger' : ($qty <= 10 ? 'text-warning' : '') }}">
                                            {{ number_format($qty, 3) }}
                                            @if($qty <= 0)
                                                <span class="ui-grid-card-subtext">Out of stock</span>
                                            @elseif($qty <= 10)
                                                <span class="ui-grid-card-subtext">Low stock</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="ui-grid-card-section">
                                        <div class="ui-grid-card-label">Prices</div>
                                        <div class="ui-grid-card-stack">
                                            <div class="ui-grid-card-value">
                                                Buy: <span class="ui-mono">{{ number_format((float)$batch->buy_price, 2) }}</span>
                                            </div>
                                            <div class="ui-grid-card-value">
                                                Sell: <span class="ui-mono">{{ number_format((float)$batch->sell_price, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    @if($batch->expiry_date)
                                        <div class="ui-grid-card-section">
                                            <div class="ui-grid-card-label">Expiry</div>
                                            <div class="ui-grid-card-value">
                                                {{ $batch->expiry_date->format('d M Y') }}
                                            </div>
                                            <div class="ui-grid-card-subtext {{ $daysLeft <= 30 ? 'text-warning' : 'text-muted' }}">
                                                @if($daysLeft < 0)
                                                    Expired {{ abs($daysLeft) }} days ago
                                                @else
                                                    {{ $daysLeft }} days left
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    @if($batch->notes)
                                        <div class="ui-grid-card-section">
                                            <div class="ui-grid-card-label">Notes</div>
                                            <div class="ui-grid-card-text">
                                                {{ Str::limit($batch->notes, 100) }}
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="ui-grid-card-footer">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="ui-status-badges">
                                            @if($expired)
                                                <span class="ui-status-badge danger">Expired</span>
                                            @elseif($qty > 0)
                                                <span class="ui-status-badge success">In Stock</span>
                                            @else
                                                <span class="ui-status-badge muted">Out</span>
                                            @endif
                                            <span class="ui-status-badge {{ $batch->is_active ? 'success' : 'muted' }}">
                                                {{ $batch->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                        <button class="ui-btn ui-btn-sm ui-btn-outline"
                                                type="button"
                                                data-ui-open="#drawerView"
                                                data-batch-view="{{ $batch->id }}">
                                            Quick View
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Drawer: Quick View --}}
<div class="ui-drawer" id="drawerView" aria-hidden="true">
    <div class="ui-drawer-backdrop" data-ui-close></div>
    <div class="ui-drawer-panel" data-drawer-content>
        <div class="ui-drawer-header">
            <div>
                <div class="fw-semibold">Batch Quick View</div>
                <div class="small ui-muted" id="viewSub">Loading…</div>
            </div>
            <button class="btn ui-btn ui-btn-ghost btn-sm" type="button" data-ui-close>
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="ui-drawer-body" id="viewBody">
            <div class="ui-skeleton">
                <div class="bar w60"></div>
                <div class="bar w90"></div>
                <div class="bar w80"></div>
                <div class="bar w70"></div>
            </div>
        </div>
        <div class="ui-drawer-footer">
            <button class="btn ui-btn ui-btn-ghost" type="button" data-ui-close>Close</button>
        </div>
    </div>
</div>

{{-- Drawer: Quick Add --}}
<div class="ui-drawer" id="drawerAdd" aria-hidden="true">
    <div class="ui-drawer-backdrop" data-ui-close></div>
    <div class="ui-drawer-panel" data-drawer-content>
        <div class="ui-drawer-header">
            <div>
                <div class="fw-semibold">Quick Add Batch</div>
                <div class="small ui-muted">{{ $product->name }}</div>
            </div>
            <button class="btn ui-btn ui-btn-ghost btn-sm" type="button" data-ui-close>
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('product.batches.store') }}" class="ui-drawer-body" id="quickAddForm">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">

            <div class="ui-form-grid">
                <div>
                    <label class="ui-label">Batch No</label>
                    <input name="batch_no" class="form-control ui-input" placeholder="e.g. A-102">
                </div>
                <div>
                    <label class="ui-label">Unit *</label>
                    <input name="unit" class="form-control ui-input" value="pcs" required>
                </div>
                <div>
                    <label class="ui-label">Quantity *</label>
                    <input type="number" step="0.001" min="0.001" name="quantity" class="form-control ui-input" required>
                </div>
                <div>
                    <label class="ui-label">Buy Price *</label>
                    <input type="number" step="0.0001" min="0" name="buy_price" class="form-control ui-input" required>
                </div>
                <div>
                    <label class="ui-label">Original Sell *</label>
                    <input type="number" step="0.0001" min="0" name="original_sell_price" class="form-control ui-input" required>
                </div>
                <div>
                    <label class="ui-label">Expiry</label>
                    <input type="date" name="expiry_date" class="form-control ui-input">
                </div>
            </div>

            <div class="ui-split mt-3">
                <label class="ui-check"><input type="checkbox" name="is_active" value="1" checked> <span>Active</span></label>
                <label class="ui-check"><input type="checkbox" name="is_online" value="1" checked> <span>Online</span></label>
                <label class="ui-check"><input type="checkbox" name="is_offline" value="1" checked> <span>Offline</span></label>
                <label class="ui-check"><input type="checkbox" name="is_pos" value="1" checked> <span>POS</span></label>
            </div>

            <div class="ui-divider"></div>

            <div class="d-flex align-items-center justify-content-between">
                <div class="fw-semibold">Free Gift Offer</div>
                <label class="ui-switch">
                    <input type="checkbox" name="is_free_offer_active" value="1" id="giftToggle">
                    <span class="track"></span>
                </label>
            </div>

            <div id="giftFields" class="ui-form-grid mt-2" style="display:none;">
                <div class="ui-form-full">
                    <label class="ui-label">Gift Product ID</label>
                    <input type="number" name="free_product_id" class="form-control ui-input" placeholder="Gift product id">
                </div>
                <div>
                    <label class="ui-label">Buy Qty</label>
                    <input type="number" step="0.0001" min="0.0001" name="free_buy_qty" class="form-control ui-input">
                </div>
                <div>
                    <label class="ui-label">Free Qty</label>
                    <input type="number" step="0.0001" min="0.0001" name="free_qty" class="form-control ui-input">
                </div>
            </div>

            <div class="ui-drawer-footer">
                <button class="btn ui-btn ui-btn-ghost" type="button" data-ui-close>Cancel</button>
                <button class="btn ui-btn ui-btn-primary" type="submit"><i class="bi bi-check2"></i> Save</button>
            </div>
        </form>
    </div>
</div>

{{-- Columns Toggle Modal --}}
<div class="ui-modal" id="columnsModal" aria-hidden="true">
    <div class="ui-modal-backdrop" data-ui-close></div>
    <div class="ui-modal-dialog">
        <div class="ui-modal-header">
            <h5 class="ui-modal-title">Visible Columns</h5>
            <button type="button" class="ui-modal-close" data-ui-close>
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="ui-modal-body">
            <div class="ui-checkbox-list">
                <label class="ui-checkbox-item">
                    <input type="checkbox" name="column" value="batch" checked> Batch Details
                </label>
                <label class="ui-checkbox-item">
                    <input type="checkbox" name="column" value="quantity" checked> Quantity
                </label>
                <label class="ui-checkbox-item">
                    <input type="checkbox" name="column" value="prices" checked> Prices
                </label>
                <label class="ui-checkbox-item">
                    <input type="checkbox" name="column" value="expiry" checked> Expiry
                </label>
                <label class="ui-checkbox-item">
                    <input type="checkbox" name="column" value="status" checked> Status
                </label>
                <label class="ui-checkbox-item">
                    <input type="checkbox" name="column" value="sku" checked> SKU
                </label>
                <label class="ui-checkbox-item">
                    <input type="checkbox" name="column" value="unit"> Unit
                </label>
                <label class="ui-checkbox-item">
                    <input type="checkbox" name="column" value="notes"> Notes
                </label>
            </div>
        </div>
        <div class="ui-modal-footer">
            <button class="ui-btn ui-btn-ghost" type="button" data-ui-close>Cancel</button>
            <button class="ui-btn ui-btn-primary" type="button" id="applyColumns">Apply</button>
        </div>
    </div>
</div>

{{-- ===================== MODERN STYLES ===================== --}}
<style>
/* Base & Layout */
.ui-page {
    color: var(--foreground);
    background: var(--background);
    min-height: 100vh;
}

/* Page Header */
.ui-page-header {
    padding: 1.5rem 0;
    border-bottom: 1px solid var(--border);
    margin-bottom: 2rem;
}

.ui-back-btn {
    width: 40px;
    height: 40px;
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--card);
    border: 1px solid var(--border);
    color: var(--foreground);
    text-decoration: none;
    transition: all var(--transition-fast) ease;
}

.ui-back-btn:hover {
    background: var(--accent);
    border-color: var(--accent-color);
    transform: translateX(-2px);
}

.ui-page-title {
    font-size: 1.75rem;
    font-weight: 800;
    margin: 0;
    color: var(--foreground);
}

.ui-page-subtitle {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: var(--muted-foreground);
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

.ui-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.25rem 0.75rem;
    background: var(--accent);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    font-size: 0.75rem;
    font-weight: 600;
}

.ui-badge-sm {
    padding: 0.125rem 0.5rem;
    font-size: 0.6875rem;
}

.ui-badge.info {
    background: color-mix(in oklch, var(--info) 15%, transparent);
    border-color: color-mix(in oklch, var(--info) 30%, transparent);
    color: var(--info);
}

/* Stats Cards */
.ui-stat-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    position: relative;
    overflow: hidden;
    transition: all var(--transition-normal) ease;
}

.ui-stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--card-shadow-hover);
    border-color: var(--accent-color);
}

.ui-stat-icon {
    width: 56px;
    height: 56px;
    border-radius: calc(var(--radius) * 1.5);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.ui-stat-icon.success {
    background: color-mix(in oklch, var(--success) 15%, transparent);
    color: var(--success);
    border: 1px solid color-mix(in oklch, var(--success) 30%, transparent);
}

.ui-stat-icon.warning {
    background: color-mix(in oklch, var(--warning) 15%, transparent);
    color: var(--warning);
    border: 1px solid color-mix(in oklch, var(--warning) 30%, transparent);
}

.ui-stat-icon.danger {
    background: color-mix(in oklch, var(--danger) 15%, transparent);
    color: var(--danger);
    border: 1px solid color-mix(in oklch, var(--danger) 30%, transparent);
}

.ui-stat-icon.info {
    background: color-mix(in oklch, var(--info) 15%, transparent);
    color: var(--info);
    border: 1px solid color-mix(in oklch, var(--info) 30%, transparent);
}

.ui-stat-content {
    flex: 1;
    min-width: 0;
}

.ui-stat-value {
    font-size: 1.75rem;
    font-weight: 800;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.ui-stat-label {
    color: var(--muted-foreground);
    font-size: 0.875rem;
    font-weight: 600;
}

.ui-stat-action {
    width: 36px;
    height: 36px;
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: 1px solid var(--border);
    color: var(--muted-foreground);
    transition: all var(--transition-fast) ease;
    opacity: 0;
}

.ui-stat-card:hover .ui-stat-action {
    opacity: 1;
}

.ui-stat-action:hover {
    background: var(--accent);
    border-color: var(--accent-color);
    color: var(--accent-color);
}

/* Buttons */
.ui-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    border-radius: var(--radius);
    border: 1px solid transparent;
    background: var(--accent);
    color: var(--foreground);
    font-weight: 600;
    font-size: 0.875rem;
    line-height: 1;
    cursor: pointer;
    transition: all var(--transition-fast) ease;
    text-decoration: none;
    user-select: none;
}

.ui-btn:hover {
    background: color-mix(in oklch, var(--accent) 80%, transparent);
    border-color: var(--border);
    transform: translateY(-1px);
}

.ui-btn-primary {
    background: var(--accent-color);
    color: var(--sidebar-primary-foreground);
    border-color: var(--accent-color);
}

.ui-btn-primary:hover {
    background: color-mix(in oklch, var(--accent-color) 85%, transparent);
    box-shadow: 0 4px 12px color-mix(in oklch, var(--accent-color) 25%, transparent);
}

.ui-btn-outline {
    background: transparent;
    border-color: var(--border);
    color: var(--foreground);
}

.ui-btn-outline:hover {
    background: var(--accent);
    border-color: var(--accent-color);
}

.ui-btn-ghost {
    background: transparent;
    border-color: transparent;
    color: var(--foreground);
}

.ui-btn-ghost:hover {
    background: var(--accent);
}

.ui-btn-text {
    background: transparent;
    border-color: transparent;
    color: var(--foreground);
    padding: 0.5rem;
}

.ui-btn-text:hover {
    color: var(--accent-color);
    background: transparent;
}

.ui-btn-icon {
    width: 36px;
    height: 36px;
    padding: 0;
    border-radius: var(--radius);
}

.ui-btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.8125rem;
}

/* Action Bar */
.ui-action-bar {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.25rem;
    margin-bottom: 1.5rem;
}

/* Search Box */
.ui-search-box {
    position: relative;
}

.ui-search-box i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--muted-foreground);
    pointer-events: none;
}

.ui-search-input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.75rem;
    background: var(--background);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    color: var(--foreground);
    font-size: 0.875rem;
    transition: all var(--transition-fast) ease;
}

.ui-search-input:focus {
    outline: none;
    border-color: var(--accent-color);
    box-shadow: 0 0 0 3px color-mix(in oklch, var(--accent-color) 20%, transparent);
}

.ui-search-clear {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: none;
    color: var(--muted-foreground);
    cursor: pointer;
    opacity: 0;
    transition: all var(--transition-fast) ease;
}

.ui-search-input:not(:placeholder-shown) + .ui-search-clear {
    opacity: 1;
}

.ui-search-clear:hover {
    background: var(--accent);
    color: var(--foreground);
}

/* Filters Bar */
.ui-filters-bar {
    padding-top: 1rem;
    border-top: 1px solid var(--border);
}

.ui-filters-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--muted-foreground);
}

.ui-filter-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.ui-filter-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    background: var(--accent);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    color: var(--foreground);
    font-size: 0.8125rem;
    font-weight: 500;
    cursor: pointer;
    transition: all var(--transition-fast) ease;
}

.ui-filter-chip:hover {
    background: color-mix(in oklch, var(--accent) 80%, transparent);
    border-color: var(--accent-color);
}

.ui-filter-chip.active {
    background: color-mix(in oklch, var(--accent-color) 15%, transparent);
    border-color: var(--accent-color);
    color: var(--accent-color);
}

/* Dropdowns */
.ui-dropdown {
    position: relative;
    display: inline-block;
}

.ui-dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    min-width: 200px;
    background: var(--popover);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--dropdown-shadow);
    padding: 0.5rem;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all var(--transition-fast) ease;
}

.ui-dropdown-menu.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.ui-dropdown-menu-right {
    right: 0;
    left: auto;
}

.ui-dropdown-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    width: 100%;
    padding: 0.625rem 0.75rem;
    background: transparent;
    border: none;
    border-radius: calc(var(--radius) * 0.8);
    color: var(--popover-foreground);
    font-size: 0.875rem;
    font-weight: 500;
    text-align: left;
    cursor: pointer;
    transition: all var(--transition-fast) ease;
    text-decoration: none;
}

.ui-dropdown-item:hover {
    background: var(--accent);
    color: var(--foreground);
}

.ui-dropdown-item.danger {
    color: var(--danger);
}

.ui-dropdown-item.danger:hover {
    background: color-mix(in oklch, var(--danger) 15%, transparent);
}

.ui-dropdown-divider {
    height: 1px;
    background: var(--border);
    margin: 0.5rem 0;
}

/* Select */
.ui-select-sm {
    padding: 0.375rem 2rem 0.375rem 0.75rem;
    background: var(--background);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    color: var(--foreground);
    font-size: 0.875rem;
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 16px;
}

.ui-select-sm:focus {
    outline: none;
    border-color: var(--accent-color);
}

/* Content Area */
.ui-content-area {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
}

/* Table View */
.ui-table-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border);
    background: var(--card);
}

.ui-table-title {
    font-size: 1.125rem;
    font-weight: 700;
    margin: 0;
    color: var(--foreground);
}

.ui-table-count {
    font-size: 0.875rem;
    color: var(--muted-foreground);
}

.ui-table-container {
    overflow-x: auto;
}

.ui-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.ui-table thead th {
    position: sticky;
    top: 0;
    background: var(--card);
    border-bottom: 2px solid var(--border);
    padding: 1rem 1.25rem;
    font-weight: 700;
    font-size: 0.8125rem;
    color: var(--muted-foreground);
    text-align: left;
    white-space: nowrap;
    z-index: 10;
}

.ui-table tbody td {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--border);
    vertical-align: top;
}

.ui-table-row {
    background: var(--card);
    transition: all var(--transition-fast) ease;
}

.ui-table-row:hover {
    background: var(--accent);
}

.ui-table-row.is-expired {
    background: color-mix(in oklch, var(--danger) 8%, transparent);
}

.ui-table-row.is-warning {
    background: color-mix(in oklch, var(--warning) 8%, transparent);
}

/* Table Components */
.ui-status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 0.5rem;
}

.ui-status-indicator.success {
    background: var(--success);
    box-shadow: 0 0 0 4px color-mix(in oklch, var(--success) 15%, transparent);
}

.ui-status-indicator.warning {
    background: var(--warning);
    box-shadow: 0 0 0 4px color-mix(in oklch, var(--warning) 15%, transparent);
}

.ui-status-indicator.danger {
    background: var(--danger);
    box-shadow: 0 0 0 4px color-mix(in oklch, var(--danger) 15%, transparent);
}

.ui-status-indicator.muted {
    background: var(--muted-foreground);
    box-shadow: 0 0 0 4px var(--accent);
}

.ui-table-link {
    color: var(--foreground);
    text-decoration: none;
    font-weight: 600;
    transition: color var(--transition-fast) ease;
}

.ui-table-link:hover {
    color: var(--accent-color);
    text-decoration: underline;
}

.ui-table-meta {
    font-size: 0.8125rem;
    color: var(--muted-foreground);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.25rem;
}

.ui-table-subtext {
    font-size: 0.75rem;
    color: var(--muted-foreground);
}

.ui-table-value {
    font-weight: 600;
    font-size: 0.9375rem;
    margin-bottom: 0.25rem;
}

.ui-mono {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}

.ui-table-stack {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.ui-progress {
    height: 6px;
    background: var(--accent);
    border-radius: 3px;
    overflow: hidden;
}

.ui-progress-bar {
    height: 100%;
    background: var(--accent-color);
    border-radius: 3px;
    transition: width 0.6s ease;
}

.ui-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    border-radius: calc(var(--radius) * 0.8);
    font-size: 0.75rem;
    font-weight: 600;
    line-height: 1;
}

.ui-status-badge.success {
    background: color-mix(in oklch, var(--success) 15%, transparent);
    color: var(--success);
}

.ui-status-badge.warning {
    background: color-mix(in oklch, var(--warning) 15%, transparent);
    color: var(--warning);
}

.ui-status-badge.danger {
    background: color-mix(in oklch, var(--danger) 15%, transparent);
    color: var(--danger);
}

.ui-status-badge.muted {
    background: var(--accent);
    color: var(--muted-foreground);
}

.ui-table-actions {
    display: flex;
    justify-content: flex-end;
}

/* Checkboxes */
.ui-checkbox {
    width: 18px;
    height: 18px;
    border-radius: 4px;
    border: 2px solid var(--border);
    background: var(--background);
    cursor: pointer;
    appearance: none;
    position: relative;
    transition: all var(--transition-fast) ease;
}

.ui-checkbox:checked {
    background: var(--accent-color);
    border-color: var(--accent-color);
}

.ui-checkbox:checked::after {
    content: '';
    position: absolute;
    top: 2px;
    left: 5px;
    width: 4px;
    height: 8px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

/* Bulk Actions */
.ui-bulk-actions {
    background: var(--accent);
    border-top: 1px solid var(--border);
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from {
        transform: translateY(100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Table Footer */
.ui-table-footer {
    padding: 1.25rem 1.5rem;
    border-top: 1px solid var(--border);
    background: var(--card);
}

.ui-table-pagination-info {
    font-size: 0.875rem;
    color: var(--muted-foreground);
}

.ui-pagination {
    display: flex;
    gap: 0.5rem;
}

.ui-pagination .pagination {
    margin: 0;
}

.ui-pagination .page-link {
    border: 1px solid var(--border);
    background: var(--background);
    color: var(--foreground);
    padding: 0.5rem 0.75rem;
    border-radius: var(--radius);
}

.ui-pagination .page-item.active .page-link {
    background: var(--accent-color);
    border-color: var(--accent-color);
    color: white;
}

/* Grid View */
.ui-grid-view {
    display: none;
}

.ui-grid-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border);
    background: var(--card);
}

.ui-grid-title {
    font-size: 1.125rem;
    font-weight: 700;
    margin: 0;
    color: var(--foreground);
}

.ui-grid-count {
    font-size: 0.875rem;
    color: var(--muted-foreground);
}

.ui-grid-container {
    padding: 1.5rem;
}

.ui-grid-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    transition: all var(--transition-normal) ease;
    height: 100%;
}

.ui-grid-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--card-shadow-hover);
    border-color: var(--accent-color);
}

.ui-grid-card.is-expired {
    border-color: var(--danger);
    background: color-mix(in oklch, var(--danger) 5%, transparent);
}

.ui-grid-card.is-warning {
    border-color: var(--warning);
    background: color-mix(in oklch, var(--warning) 5%, transparent);
}

.ui-grid-card-header {
    padding: 1.25rem 1.25rem 0.75rem;
    border-bottom: 1px solid var(--border);
}

.ui-grid-card-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--foreground);
    text-decoration: none;
}

.ui-grid-card-title:hover {
    color: var(--accent-color);
    text-decoration: underline;
}

.ui-grid-card-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.ui-grid-card-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.ui-grid-card-body {
    padding: 1.25rem;
}

.ui-grid-card-section {
    margin-bottom: 1rem;
}

.ui-grid-card-section:last-child {
    margin-bottom: 0;
}

.ui-grid-card-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--muted-foreground);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.375rem;
}

.ui-grid-card-value {
    font-size: 1rem;
    font-weight: 600;
    color: var(--foreground);
}

.ui-grid-card-subtext {
    font-size: 0.8125rem;
    color: var(--muted-foreground);
    margin-top: 0.25rem;
}

.ui-grid-card-stack {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.ui-grid-card-text {
    font-size: 0.875rem;
    color: var(--foreground);
    line-height: 1.5;
}

.ui-grid-card-footer {
    padding: 1rem 1.25rem;
    border-top: 1px solid var(--border);
    background: var(--accent);
}

.ui-status-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 0.375rem;
}

/* Empty State */
.ui-empty-state {
    padding: 4rem 2rem;
    text-align: center;
}

.ui-empty-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 1.5rem;
    background: var(--accent);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--muted-foreground);
}

.ui-empty-state h4 {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: var(--foreground);
}

/* Modal */
.ui-modal {
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 1050;
}

.ui-modal[aria-hidden="false"] {
    pointer-events: auto;
}

.ui-modal-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    opacity: 0;
    transition: opacity var(--transition-normal) ease;
}

.ui-modal[aria-hidden="false"] .ui-modal-backdrop {
    opacity: 1;
}

.ui-modal-dialog {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.9);
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--dropdown-shadow);
    width: 90%;
    max-width: 500px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    opacity: 0;
    transition: all var(--transition-normal) ease;
}

.ui-modal[aria-hidden="false"] .ui-modal-dialog {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
}

.ui-modal-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.ui-modal-title {
    font-size: 1.125rem;
    font-weight: 700;
    margin: 0;
    color: var(--foreground);
}

.ui-modal-close {
    width: 32px;
    height: 32px;
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: 1px solid var(--border);
    color: var(--foreground);
    cursor: pointer;
    transition: all var(--transition-fast) ease;
}

.ui-modal-close:hover {
    background: var(--accent);
    border-color: var(--accent-color);
}

.ui-modal-body {
    padding: 1.5rem;
    flex: 1;
    overflow-y: auto;
}

.ui-modal-footer {
    padding: 1.25rem 1.5rem;
    border-top: 1px solid var(--border);
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}

/* Checkbox List */
.ui-checkbox-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.ui-checkbox-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: var(--accent);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    cursor: pointer;
    transition: all var(--transition-fast) ease;
}

.ui-checkbox-item:hover {
    background: color-mix(in oklch, var(--accent) 80%, transparent);
    border-color: var(--accent-color);
}

.ui-checkbox-item input[type="checkbox"] {
    width: 18px;
    height: 18px;
}

/* Form Grid */
.ui-form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.ui-form-full {
    grid-column: 1 / -1;
}

/* Split */
.ui-split {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

/* Check */
.ui-check {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: var(--accent);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    cursor: pointer;
}

.ui-check input[type="checkbox"] {
    width: 18px;
    height: 18px;
}

/* Switch */
.ui-switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 26px;
}

.ui-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.ui-switch .track {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: var(--border);
    transition: .4s;
    border-radius: 34px;
}

.ui-switch .track:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

.ui-switch input:checked + .track {
    background-color: var(--accent-color);
}

.ui-switch input:checked + .track:before {
    transform: translateX(24px);
}

/* Divider */
.ui-divider {
    height: 1px;
    background: var(--border);
    margin: 1.5rem 0;
}

/* Drawer (from previous code, adjusted for consistency) */
.ui-drawer {
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 1055;
}

.ui-drawer[aria-hidden="false"] {
    pointer-events: auto;
}

.ui-drawer-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    opacity: 0;
    transition: opacity var(--transition-normal) ease;
}

.ui-drawer[aria-hidden="false"] .ui-drawer-backdrop {
    opacity: 1;
}

.ui-drawer-panel {
    position: absolute;
    top: 0;
    right: 0;
    height: 100%;
    width: min(500px, 95vw);
    background: var(--card);
    border-left: 1px solid var(--border);
    box-shadow: var(--dropdown-shadow);
    transform: translateX(100%);
    transition: transform var(--transition-normal) cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
}

.ui-drawer[aria-hidden="false"] .ui-drawer-panel {
    transform: translateX(0);
}

.ui-drawer-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.ui-drawer-body {
    padding: 1.5rem;
    overflow-y: auto;
    flex: 1;
}

.ui-drawer-footer {
    padding: 1.25rem 1.5rem;
    border-top: 1px solid var(--border);
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}

/* Link */
.ui-link {
    color: var(--accent-color);
    text-decoration: none;
    font-weight: 500;
}

.ui-link:hover {
    text-decoration: underline;
}

/* Table Muted */
.ui-table-muted {
    color: var(--muted-foreground);
    font-style: italic;
}

/* Responsive */
@media (max-width: 768px) {
    .ui-form-grid {
        grid-template-columns: 1fr;
    }

    .ui-page-subtitle {
        flex-wrap: wrap;
    }

    .ui-table-header,
    .ui-grid-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .ui-table-footer {
        flex-direction: column;
        gap: 1rem;
    }

    .ui-pagination {
        flex-wrap: wrap;
    }
}

/* Animation Keyframes */
@keyframes uiFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes uiSlideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes uiPop {
    0% { opacity: 0; transform: scale(0.9); }
    70% { opacity: 1; transform: scale(1.05); }
    100% { opacity: 1; transform: scale(1); }
}

/* Animation Classes */
[data-animate] {
    opacity: 0;
}

.animate-in {
    opacity: 1;
}

.animate-fade-in {
    animation: uiFadeIn 0.5s ease forwards;
}

.animate-slide-up {
    animation: uiSlideUp 0.6s ease forwards;
}

.animate-pop {
    animation: uiPop 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}

/* Utility Classes */
.min-w-0 {
    min-width: 0;
}

.flex-grow-1 {
    flex-grow: 1;
}

.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.text-muted {
    color: var(--muted-foreground);
}

.text-danger {
    color: var(--danger);
}

.text-warning {
    color: var(--warning);
}

.text-success {
    color: var(--success);
}

.text-info {
    color: var(--info);
}

.text-end {
    text-align: right;
}

.mt-1 { margin-top: 0.25rem; }
.mt-2 { margin-top: 0.5rem; }
.mt-3 { margin-top: 1rem; }
.mb-1 { margin-bottom: 0.25rem; }
.mb-2 { margin-bottom: 0.5rem; }
.mb-3 { margin-bottom: 1rem; }
.mb-4 { margin-bottom: 1.5rem; }
.ms-1 { margin-left: 0.25rem; }

.gap-1 { gap: 0.25rem; }
.gap-2 { gap: 0.5rem; }
.gap-3 { gap: 1rem; }

.d-flex { display: flex; }
.d-inline { display: inline; }
.d-inline-block { display: inline-block; }
.d-block { display: block; }
.d-none { display: none; }

.align-items-center { align-items: center; }
.justify-content-between { justify-content: space-between; }
.justify-content-end { justify-content: flex-end; }
.flex-wrap { flex-wrap: wrap; }

.row { display: flex; flex-wrap: wrap; margin: -0.75rem; }
.row > * { padding: 0.75rem; }

.col-12 { width: 100%; }
.col-6 { width: 50%; }

@media (min-width: 768px) {
    .col-md-6 { width: 50%; }
    .col-md-3 { width: 25%; }
}

@media (min-width: 1200px) {
    .col-xl-4 { width: 33.333%; }
}
</style>


<script>
(function(){
    'use strict';

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        initAnimations();
        initDropdowns();
        initTable();
        initFilters();
        initViewToggle();
        initBulkActions();
        initSearch();
        initStats();
        initDrawers();
        initColumnsModal();
        initExport();
    });

    // Animation Observer
    function initAnimations() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const delay = el.dataset.animateDelay || 0;

                    setTimeout(() => {
                        el.classList.add('animate-in');

                        switch(el.dataset.animate) {
                            case 'slide-up':
                                el.classList.add('animate-slide-up');
                                break;
                            case 'pop':
                                el.classList.add('animate-pop');
                                break;
                            case 'fade-in':
                                el.classList.add('animate-fade-in');
                                break;
                        }

                        // Initialize progress bars
                        if (el.dataset.progress !== undefined) {
                            const progress = el.querySelector('.ui-progress-bar');
                            if (progress) {
                                progress.style.width = el.dataset.progress + '%';
                            }
                        }
                    }, parseInt(delay));

                    observer.unobserve(el);
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('[data-animate]').forEach(el => observer.observe(el));
    }

    // Dropdowns
    function initDropdowns() {
        document.addEventListener('click', function(e) {
            // Close all dropdowns
            if (!e.target.closest('[data-dropdown-toggle]')) {
                document.querySelectorAll('.ui-dropdown-menu.show').forEach(menu => {
                    menu.classList.remove('show');
                });
            }

            // Toggle dropdown
            const toggle = e.target.closest('[data-dropdown-toggle]');
            if (toggle) {
                e.stopPropagation();
                const dropdown = toggle.closest('.ui-dropdown');
                const menu = dropdown.querySelector('.ui-dropdown-menu');
                menu.classList.toggle('show');
            }
        });

        // Close on escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.ui-dropdown-menu.show').forEach(menu => {
                    menu.classList.remove('show');
                });
            }
        });
    }

    // Table functionality
    function initTable() {
        const table = document.querySelector('.ui-table');
        if (!table) return;

        // Row click for quick view
        table.addEventListener('click', function(e) {
            const row = e.target.closest('.ui-table-row');
            if (!row || e.target.closest('a, button, input, .ui-dropdown')) return;

            const batchId = row.dataset.batchId;
            if (batchId) {
                openDrawer('#drawerView');
                loadQuickView(batchId);
            }
        });

        // Initialize progress bars
        document.querySelectorAll('.ui-progress-bar').forEach(bar => {
            const progress = bar.closest('[data-progress]');
            if (progress) {
                setTimeout(() => {
                    bar.style.width = progress.dataset.progress + '%';
                }, 300);
            }
        });
    }

    // Filters
    function initFilters() {
        const state = {
            q: '',
            filters: new Set(),
            sort: 'latest'
        };

        const rows = Array.from(document.querySelectorAll('[data-row]'));
        const tableCount = document.querySelector('[data-table-count]');
        const gridCount = document.querySelector('[data-grid-count]');
        const filterChips = document.querySelectorAll('.ui-filter-chip');
        const statActions = document.querySelectorAll('.ui-stat-action');
        const clearFiltersBtn = document.getElementById('clearFilters');
        const sortSelect = document.getElementById('sortSelect');
        const gridSort = document.getElementById('gridSort');

        // Filter matches
        function matchesFilters(row) {
            const qty = parseFloat(row.dataset.qty || '0');
            const expired = row.dataset.expired === '1';
            const expSoon = row.dataset.exps === '1';
            const gift = row.dataset.gift === '1';
            const active = row.dataset.active === '1';

            for (const filter of state.filters) {
                switch(filter) {
                    case 'inStock':
                        if (qty <= 0) return false;
                        break;
                    case 'outStock':
                        if (qty > 0) return false;
                        break;
                    case 'expired':
                        if (!expired) return false;
                        break;
                    case 'expSoon':
                        if (!expSoon) return false;
                        break;
                    case 'gift':
                        if (!gift) return false;
                        break;
                    case 'active':
                        if (!active) return false;
                        break;
                }
            }
            return true;
        }

        // Apply filters
        function applyFilters() {
            let visibleCount = 0;

            rows.forEach(row => {
                const text = row.dataset.text || '';
                const matchesText = !state.q || text.includes(state.q.toLowerCase());
                const matchesFilter = matchesFilters(row);
                const isVisible = matchesText && matchesFilter;

                row.style.display = isVisible ? '' : 'none';
                if (isVisible) visibleCount++;
            });

            // Update counts
            if (tableCount) tableCount.textContent = `${visibleCount} items`;
            if (gridCount) gridCount.textContent = `${visibleCount} items`;

            // Sort table rows
            sortTableRows();
        }

        // Sort table rows
        function sortTableRows() {
            const tbody = document.querySelector('#tableBody');
            if (!tbody) return;

            const visibleRows = rows.filter(r => r.style.display !== 'none');

            visibleRows.sort((a, b) => {
                switch(state.sort) {
                    case 'latest':
                        return parseInt(b.dataset.batchId) - parseInt(a.dataset.batchId);
                    case 'expirySoon':
                        const expiryA = a.dataset.expiry || '9999-12-31';
                        const expiryB = b.dataset.expiry || '9999-12-31';
                        return expiryA.localeCompare(expiryB);
                    case 'qtyLow':
                        return parseFloat(a.dataset.qty) - parseFloat(b.dataset.qty);
                    case 'qtyHigh':
                        return parseFloat(b.dataset.qty) - parseFloat(a.dataset.qty);
                    case 'sellHigh':
                        return parseFloat(b.dataset.sell) - parseFloat(a.dataset.sell);
                    default:
                        return 0;
                }
            });

            // Reorder in DOM
            visibleRows.forEach(row => tbody.appendChild(row));
        }

        // Filter chip clicks
        filterChips.forEach(chip => {
            chip.addEventListener('click', function() {
                const filter = this.dataset.filter;
                if (state.filters.has(filter)) {
                    state.filters.delete(filter);
                    this.classList.remove('active');
                } else {
                    state.filters.add(filter);
                    this.classList.add('active');
                }
                applyFilters();
            });
        });

        // Stat action clicks
        statActions.forEach(action => {
            action.addEventListener('click', function() {
                const filter = this.dataset.filter;
                state.filters.clear();
                state.filters.add(filter);

                // Update UI
                filterChips.forEach(chip => {
                    chip.classList.toggle('active', chip.dataset.filter === filter);
                });

                applyFilters();
                scrollToTable();
            });
        });

        // Clear filters
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', function() {
                state.filters.clear();
                filterChips.forEach(chip => chip.classList.remove('active'));
                applyFilters();
            });
        }

        // Sort change
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                state.sort = this.value;
                applyFilters();
            });
        }

        if (gridSort) {
            gridSort.addEventListener('change', function() {
                state.sort = this.value;
                applyFilters();
            });
        }

        // Initial apply
        applyFilters();

        function scrollToTable() {
            const table = document.querySelector('.ui-table-view[data-view="active"]');
            if (table) {
                table.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    }

    // View toggle (Table/Grid)
    function initViewToggle() {
        const tableView = document.getElementById('tableView');
        const gridView = document.getElementById('gridView');
        const viewOptions = document.querySelectorAll('[data-view]');

        viewOptions.forEach(option => {
            option.addEventListener('click', function() {
                const view = this.dataset.view;

                if (view === 'table') {
                    tableView.style.display = '';
                    tableView.setAttribute('data-view', 'active');
                    gridView.style.display = 'none';
                    gridView.setAttribute('data-view', 'hidden');
                } else if (view === 'grid') {
                    tableView.style.display = 'none';
                    tableView.setAttribute('data-view', 'hidden');
                    gridView.style.display = '';
                    gridView.setAttribute('data-view', 'active');
                }
            });
        });
    }

    // Bulk actions
    function initBulkActions() {
        const selectAll = document.getElementById('selectAll');
        const batchCheckboxes = document.querySelectorAll('.batch-checkbox, .grid-checkbox');
        const bulkActions = document.getElementById('bulkActions');
        const selectedCount = document.getElementById('selectedCount');
        const clearSelectionBtn = document.getElementById('clearSelection');
        const bulkActionButtons = document.querySelectorAll('[data-bulk-action]');

        // Select all
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                const isChecked = this.checked;
                batchCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                updateBulkActions();
            });
        }

        // Individual checkbox change
        batchCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkActions);
        });

        // Update bulk actions UI
        function updateBulkActions() {
            const checked = document.querySelectorAll('.batch-checkbox:checked, .grid-checkbox:checked');
            const count = checked.length;

            if (selectedCount) {
                selectedCount.textContent = `${count} item${count !== 1 ? 's' : ''} selected`;
            }

            if (bulkActions) {
                bulkActions.style.display = count > 0 ? '' : 'none';
            }

            if (selectAll) {
                selectAll.indeterminate = count > 0 && count < batchCheckboxes.length;
                selectAll.checked = count === batchCheckboxes.length && batchCheckboxes.length > 0;
            }
        }

        // Clear selection
        if (clearSelectionBtn) {
            clearSelectionBtn.addEventListener('click', function() {
                batchCheckboxes.forEach(checkbox => checkbox.checked = false);
                updateBulkActions();
            });
        }

        // Bulk actions
        bulkActionButtons.forEach(button => {
            button.addEventListener('click', function() {
                const action = this.dataset.bulkAction;
                const checked = document.querySelectorAll('.batch-checkbox:checked, .grid-checkbox:checked');
                const ids = Array.from(checked).map(cb => cb.value);

                if (ids.length === 0) {
                    alert('Please select at least one batch.');
                    return;
                }

                switch(action) {
                    case 'export':
                        exportBatches(ids);
                        break;
                    case 'print':
                        printBatches(ids);
                        break;
                    case 'activate':
                        updateBatchStatus(ids, true);
                        break;
                    case 'deactivate':
                        updateBatchStatus(ids, false);
                        break;
                    case 'delete':
                        if (confirm(`Delete ${ids.length} selected batch${ids.length !== 1 ? 'es' : ''}?`)) {
                            deleteBatches(ids);
                        }
                        break;
                }
            });
        });

        function exportBatches(ids) {
            console.log('Exporting batches:', ids);
            alert(`Exporting ${ids.length} batches...`);
        }

        function printBatches(ids) {
            console.log('Printing batches:', ids);
            alert(`Printing ${ids.length} batches...`);
        }

        function updateBatchStatus(ids, active) {
            console.log(`Setting ${ids.length} batches to ${active ? 'active' : 'inactive'}`);
            alert(`Updating ${ids.length} batches...`);
        }

        function deleteBatches(ids) {
            console.log('Deleting batches:', ids);
            alert(`Deleting ${ids.length} batches...`);
        }

        // Initial update
        updateBulkActions();
    }

    // Search
    function initSearch() {
        const searchInput = document.getElementById('searchInput');
        const clearSearch = document.getElementById('clearSearch');

        if (searchInput) {
            let searchTimeout;

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const state = { q: this.value.trim().toLowerCase() };
                    applySearch(state.q);
                }, 300);
            });

            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    this.value = '';
                    applySearch('');
                }
            });
        }

        if (clearSearch) {
            clearSearch.addEventListener('click', function() {
                if (searchInput) {
                    searchInput.value = '';
                    searchInput.focus();
                    applySearch('');
                }
            });
        }

        function applySearch(query) {
            const rows = document.querySelectorAll('[data-row]');
            let visibleCount = 0;

            rows.forEach(row => {
                const text = row.dataset.text || '';
                const isVisible = !query || text.includes(query);
                row.style.display = isVisible ? '' : 'none';
                if (isVisible) visibleCount++;
            });

            // Update counts
            const tableCount = document.querySelector('[data-table-count]');
            const gridCount = document.querySelector('[data-grid-count]');
            if (tableCount) tableCount.textContent = `${visibleCount} items`;
            if (gridCount) gridCount.textContent = `${visibleCount} items`;
        }
    }

    // Animated counters
    function initStats() {
        document.querySelectorAll('[data-counter]').forEach(counter => {
            const target = parseInt(counter.dataset.counter);
            const duration = 1000;
            const step = 20;
            const increment = target / (duration / step);
            let current = 0;

            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    counter.textContent = target;
                    clearInterval(timer);
                } else {
                    counter.textContent = Math.floor(current);
                }
            }, step);
        });
    }

    // Drawers
    function initDrawers() {
        document.addEventListener('click', function(e) {
            // Open drawer
            if (e.target.closest('[data-ui-open]')) {
                const selector = e.target.closest('[data-ui-open]').getAttribute('data-ui-open');
                openDrawer(selector);

                const batchId = e.target.closest('[data-ui-open]').getAttribute('data-batch-view');
                if (batchId) {
                    loadQuickView(batchId);
                }
            }

            // Close drawer
            if (e.target.closest('[data-ui-close]') || e.target.classList.contains('ui-drawer-backdrop')) {
                const drawer = e.target.closest('.ui-drawer');
                if (drawer) closeDrawer(drawer);
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const openDrawer = document.querySelector('.ui-drawer[aria-hidden="false"]');
                if (openDrawer) closeDrawer(openDrawer);
            }
        });

        // Gift toggle
        const giftToggle = document.getElementById('giftToggle');
        const giftFields = document.getElementById('giftFields');

        if (giftToggle && giftFields) {
            giftToggle.addEventListener('change', function() {
                giftFields.style.display = this.checked ? '' : 'none';
            });
        }
    }

    function openDrawer(selector) {
        const drawer = document.querySelector(selector);
        if (drawer) {
            drawer.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeDrawer(drawer) {
        if (drawer) {
            drawer.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }
    }

    async function loadQuickView(id) {
        const body = document.getElementById('viewBody');
        const sub = document.getElementById('viewSub');

        if (body) body.innerHTML = `<div class="ui-skeleton"><div class="bar w60"></div><div class="bar w90"></div><div class="bar w80"></div><div class="bar w70"></div></div>`;
        if (sub) sub.textContent = 'Loading…';

        try {
            const url = `{{ url('/product-batches/api/batch') }}/${id}`;
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error('Failed');

            const b = await res.json();
            if (sub) sub.textContent = `${b.batch_no || 'Batch'} • ${b.batch_sku || ''}`;

            const gift = b.is_free_offer_active ? `
                <div class="ui-badge info mb-2"><i class="bi bi-gift"></i> Gift Offer</div>
                <div class="ui-table-subtext mb-2">
                    Gift Product ID: <span class="ui-mono">${b.free_product_id || '—'}</span><br>
                    Buy <span class="ui-mono">${b.free_buy_qty || '—'}</span> → Free <span class="ui-mono">${b.free_qty || '—'}</span>
                </div>` : '';

            if (body) {
                body.innerHTML = `
                    <div class="mb-3">
                        <div class="ui-badge">${b.is_active ? 'Active' : 'Inactive'}</div>
                    </div>

                    <div class="mb-3">
                        <div class="ui-table-label">Quantity</div>
                        <div class="ui-table-value">${b.quantity || '—'} (${b.unit || '—'})</div>
                    </div>

                    <div class="mb-3">
                        <div class="ui-table-label">Prices</div>
                        <div class="ui-table-stack">
                            <div>Buy: <span class="ui-mono">${Number(b.buy_price || 0).toFixed(2)}</span></div>
                            <div>Sell: <span class="ui-mono">${Number(b.sell_price || 0).toFixed(2)}</span></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="ui-table-label">Dates</div>
                        <div class="ui-table-stack">
                            <div>MFG: <span class="ui-mono">${b.manufacture_date || '—'}</span></div>
                            <div>EXP: <span class="ui-mono">${b.expiry_date || '—'}</span></div>
                        </div>
                    </div>

                    ${gift}

                    <div class="mt-3">
                        <div class="ui-table-label">Notes</div>
                        <div class="ui-table-text">${b.notes || '—'}</div>
                    </div>
                `;
            }
        } catch (err) {
            if (sub) sub.textContent = 'Error';
            if (body) body.innerHTML = `<div class="ui-alert danger">Failed to load batch details.</div>`;
        }
    }

    // Columns modal
    function initColumnsModal() {
        const columnsModal = document.getElementById('columnsModal');
        const columnsToggle = document.querySelector('[data-columns-toggle]');
        const applyColumnsBtn = document.getElementById('applyColumns');

        if (columnsToggle) {
            columnsToggle.addEventListener('click', function() {
                columnsModal.setAttribute('aria-hidden', 'false');
            });
        }

        // Close modal
        columnsModal.addEventListener('click', function(e) {
            if (e.target.closest('[data-ui-close]') || e.target.classList.contains('ui-modal-backdrop')) {
                columnsModal.setAttribute('aria-hidden', 'true');
            }
        });

        // Apply columns
        if (applyColumnsBtn) {
            applyColumnsBtn.addEventListener('click', function() {
                const selectedColumns = Array.from(document.querySelectorAll('input[name="column"]:checked'))
                    .map(cb => cb.value);
                console.log('Selected columns:', selectedColumns);
                alert(`Showing columns: ${selectedColumns.join(', ')}`);
                columnsModal.setAttribute('aria-hidden', 'true');
            });
        }
    }

    // Export functionality
    function initExport() {
        const exportButtons = document.querySelectorAll('[data-export]');

        exportButtons.forEach(button => {
            button.addEventListener('click', function() {
                const format = this.dataset.export;
                exportData(format);
            });
        });

        function exportData(format) {
            const rows = Array.from(document.querySelectorAll('[data-row]'));
            const data = rows.map(row => ({
                id: row.dataset.batchId,
                batch: row.querySelector('.ui-table-link')?.textContent.trim() || '',
                sku: row.dataset.sku || '',
                quantity: row.dataset.qty,
                buy: row.dataset.buy,
                sell: row.dataset.sell,
                expiry: row.dataset.expiry,
                status: row.dataset.status
            }));

            console.log(`Exporting ${data.length} rows as ${format}`);
            alert(`Exporting ${data.length} batches as ${format.toUpperCase()}...`);

            // In a real app, you would make an API call here
            // window.location.href = `/export/batches/${format}?ids=${data.map(d => d.id).join(',')}`;
        }
    }
})();
</script>

@endsection
