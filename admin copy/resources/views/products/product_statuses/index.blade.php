@extends('layouts.app')

@section('content')
<div class="container fade-in">
    <!-- Page Header -->
    <div class="page-header slide-up">
        <div class="breadcrumb-nav">
            <a href="" class="breadcrumb-link">
                <svg class="breadcrumb-icon" viewBox="0 0 24 24">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
                Dashboard
            </a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">Product Statuses</span>
        </div>
        <div class="header-content">
            <div class="header-text">
                <h1 class="page-title">Product Statuses</h1>
                <p class="page-subtitle">Manage all product status badges and templates</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('product.status.create') }}" class="btn-primary">
                    <svg viewBox="0 0 24 24">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    Create Status
                </a>
                <button class="btn-secondary" id="filterToggle">
                    <svg viewBox="0 0 24 24">
                        <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z"/>
                    </svg>
                    Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Filter Panel -->
    <div class="filter-panel slide-up" id="filterPanel" style="display: none;">
        <div class="filter-content">
            <div class="filter-row">
                <div class="filter-group">
                    <label class="filter-label">Search</label>
                    <div class="search-wrapper">
                        <div class="search-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5z"/>
                            </svg>
                        </div>
                        <input type="text" id="searchInput" class="search-input" placeholder="Search statuses...">
                    </div>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Status</label>
                    <select id="statusFilter" class="filter-select">
                        <option value="all">All Statuses</option>
                        <option value="active">Active Only</option>
                        <option value="inactive">Inactive Only</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Sort By</label>
                    <select id="sortFilter" class="filter-select">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="name_asc">Name A-Z</option>
                        <option value="name_desc">Name Z-A</option>
                    </select>
                </div>
            </div>
            <div class="filter-actions">
                <button class="btn-secondary" id="clearFilters">Clear Filters</button>
                <button class="btn-primary" id="applyFilters">Apply Filters</button>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="stats-grid slide-up" style="animation-delay: 0.1s;">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--chart-1), var(--chart-4));">
                <svg viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $statuses->count() }}</div>
                <div class="stat-label">Total Statuses</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--chart-2), var(--chart-5));">
                <svg viewBox="0 0 24 24">
                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $statuses->where('is_active', true)->count() }}</div>
                <div class="stat-label">Active Statuses</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--chart-3), var(--chart-1));">
                <svg viewBox="0 0 24 24">
                    <path d="M17 3H7c-1.1 0-1.99.9-1.99 2L5 21l7-3 7 3V5c0-1.1-.9-2-2-2z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $statuses->where('product_id', null)->count() }}</div>
                <div class="stat-label">Templates</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--chart-4), var(--chart-2));">
                <svg viewBox="0 0 24 24">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $products->count() }}</div>
                <div class="stat-label">Products</div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content-card pop-in">
        @if($statuses->isEmpty())
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zM7 12h10v2H7zm0-4h10v2H7z"/>
                    </svg>
                </div>
                <h3>No Statuses Found</h3>
                <p>Get started by creating your first product status or template</p>
                <a href="{{ route('product.status.create') }}" class="btn-primary">
                    <svg viewBox="0 0 24 24">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    Create First Status
                </a>
            </div>
        @else
            <!-- Status Table -->
            <div class="table-container">
                <div class="table-responsive">
                    <table class="data-table" id="statusTable">
                        <thead>
                            <tr>
                                <th>
                                    <div class="table-header">
                                        <span>Status Name</span>
                                        <button class="sort-btn" data-sort="name">
                                            <svg viewBox="0 0 24 24">
                                                <path d="M3 18h6v-2H3v2zM3 6v2h18V6H3zm0 7h12v-2H3v2z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </th>
                                <th>
                                    <div class="table-header">
                                        <span>Product</span>
                                    </div>
                                </th>
                                <th>
                                    <div class="table-header">
                                        <span>Badge Preview</span>
                                    </div>
                                </th>
                                <th>
                                    <div class="table-header">
                                        <span>Description</span>
                                    </div>
                                </th>
                                <th>
                                    <div class="table-header">
                                        <span>Status</span>
                                        <button class="sort-btn" data-sort="status">
                                            <svg viewBox="0 0 24 24">
                                                <path d="M3 18h6v-2H3v2zM3 6v2h18V6H3zm0 7h12v-2H3v2z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </th>
                                <th>
                                    <div class="table-header">
                                        <span>Created</span>
                                        <button class="sort-btn" data-sort="created">
                                            <svg viewBox="0 0 24 24">
                                                <path d="M3 18h6v-2H3v2zM3 6v2h18V6H3zm0 7h12v-2H3v2z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </th>
                                <th>
                                    <div class="table-header">
                                        <span>Actions</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($statuses as $status)
                                @php
                                    $product = $status->product;
                                    $badgeColor = $status->badge_color ?: '#3B82F6';
                                    $textColor = getContrastColor($badgeColor);
                                @endphp
                                <tr class="table-row" data-status="{{ $status->is_active ? 'active' : 'inactive' }}">
                                    <td>
                                        <div class="cell-content">
                                            <div class="status-name">{{ $status->name }}</div>
                                            <div class="status-slug">{{ $status->slug }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($product)
                                            <a href="{{ route('products.edit', $product->id) }}" class="product-link">
                                                <div class="product-info">
                                                    <div class="product-name">{{ $product->name }}</div>
                                                    <div class="product-sku">{{ $product->sku ?: 'No SKU' }}</div>
                                                </div>
                                            </a>
                                        @else
                                            <span class="template-badge">Template</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($status->badge_text)
                                            <span class="badge-preview" style="background-color: {{ $badgeColor }}; color: {{ $textColor }};">
                                                {{ $status->badge_text }}
                                            </span>
                                        @else
                                            <span class="no-badge">No Badge</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="description-cell">
                                            {{ $status->description ?: 'No description' }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-indicator {{ $status->is_active ? 'active' : 'inactive' }}">
                                            {{ $status->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="date-cell">
                                            <div class="date">{{ $status->created_at->format('M d, Y') }}</div>
                                            <div class="time">{{ $status->created_at->format('h:i A') }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            @if($product)
                                                <a href="{{ route('products.edit', $product->id) }}?tab=product-status" class="btn-action view" title="View Product">
                                                    <svg viewBox="0 0 24 24">
                                                        <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                                    </svg>
                                                </a>
                                            @endif
                                            @if($status->uuid)
                                                <a href="{{ route('product.status.edit', $status->uuid) }}" class="btn-action edit" title="Edit Status">
                                                    <svg viewBox="0 0 24 24">
                                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                                    </svg>
                                                </a>
                                            @endif
                                            <form action="{{ route('product.status.destroy', $status->uuid) }}" method="POST" class="delete-form" onsubmit="return confirmDelete(event)">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-action delete" title="Delete Status">
                                                    <svg viewBox="0 0 24 24">
                                                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($statuses->hasPages())
            <div class="pagination-container">
                <div class="pagination-info">
                    Showing {{ $statuses->firstItem() }} to {{ $statuses->lastItem() }} of {{ $statuses->total() }} entries
                </div>
                <div class="pagination-links">
                    {{ $statuses->links() }}
                </div>
            </div>
            @endif
        @endif
    </div>
</div>

<style>
    /* Animations */
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

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes popIn {
        0% {
            opacity: 0;
            transform: scale(0.95) translateY(20px);
        }
        100% {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .fade-in {
        animation: fadeIn 0.5s ease-out;
    }

    .slide-up {
        animation: slideUp 0.4s ease-out forwards;
        opacity: 0;
    }

    .pop-in {
        animation: popIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    /* Layout */
    .container {
        max-width: 1400px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    /* Page Header */
    .page-header {
        margin-bottom: 2rem;
    }

    .breadcrumb-nav {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    .breadcrumb-link {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-secondary);
        text-decoration: none;
        transition: color var(--transition-normal);
        padding: 0.25rem 0.5rem;
        border-radius: var(--radius);
    }

    .breadcrumb-link:hover {
        color: var(--accent-color);
        background: var(--accent-glow);
    }

    .breadcrumb-icon {
        width: 16px;
        height: 16px;
        fill: currentColor;
    }

    .breadcrumb-separator {
        color: var(--text-muted);
    }

    .breadcrumb-current {
        color: var(--text-primary);
        font-weight: 500;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 2rem;
        flex-wrap: wrap;
    }

    .header-text {
        flex: 1;
    }

    .page-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--text-primary);
        margin: 0 0 0.5rem 0;
        background: linear-gradient(135deg, var(--accent-color), var(--chart-2));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1.2;
    }

    .page-subtitle {
        color: var(--text-secondary);
        font-size: 1.125rem;
        margin: 0;
        max-width: 600px;
    }

    .header-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    /* Filter Panel */
    .filter-panel {
        background: var(--card);
        border: 1px solid var(--border-color);
        border-radius: calc(var(--radius) * 1.5);
        margin-bottom: 2rem;
        overflow: hidden;
        box-shadow: var(--card-shadow);
    }

    .filter-content {
        padding: 1.5rem;
    }

    .filter-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .filter-label {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.875rem;
    }

    .search-wrapper {
        position: relative;
        background: var(--input);
        border: 2px solid var(--border-color);
        border-radius: var(--radius);
        transition: all var(--transition-normal);
    }

    .search-wrapper:focus-within {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px var(--accent-glow);
    }

    .search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
        z-index: 1;
    }

    .search-icon svg {
        width: 20px;
        height: 20px;
        fill: currentColor;
    }

    .search-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 3rem;
        background: transparent;
        border: none;
        color: var(--text-primary);
        font-size: 0.875rem;
        outline: none;
        font-family: inherit;
    }

    .filter-select {
        width: 100%;
        padding: 0.75rem 1rem;
        background: var(--input);
        border: 2px solid var(--border-color);
        border-radius: var(--radius);
        color: var(--text-primary);
        font-size: 0.875rem;
        outline: none;
        cursor: pointer;
        transition: all var(--transition-normal);
    }

    .filter-select:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px var(--accent-glow);
    }

    .filter-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--card);
        border: 1px solid var(--border-color);
        border-radius: calc(var(--radius) * 1.5);
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all var(--transition-normal);
        box-shadow: var(--card-shadow);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--card-shadow-hover);
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        flex-shrink: 0;
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
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    /* Content Card */
    .content-card {
        background: var(--card);
        border: 1px solid var(--border-color);
        border-radius: calc(var(--radius) * 1.5);
        overflow: hidden;
        box-shadow: var(--card-shadow);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-state-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 1.5rem;
        color: var(--text-muted);
    }

    .empty-state-icon svg {
        width: 100%;
        height: 100%;
        fill: currentColor;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0 0 0.5rem 0;
    }

    .empty-state p {
        color: var(--text-secondary);
        margin: 0 0 1.5rem 0;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
    }

    /* Table Container */
    .table-container {
        overflow-x: auto;
    }

    .table-responsive {
        min-width: 1000px;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table thead {
        background: var(--accent);
        border-bottom: 2px solid var(--border-color);
    }

    .data-table th {
        padding: 1rem 1.5rem;
        text-align: left;
        font-weight: 600;
        color: var(--text-secondary);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
    }

    .data-table td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--border-color);
    }

    .data-table tbody tr {
        transition: all var(--transition-normal);
    }

    .data-table tbody tr:hover {
        background: var(--accent);
    }

    /* Table Header */
    .table-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .sort-btn {
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 4px;
        transition: all var(--transition-normal);
    }

    .sort-btn:hover {
        color: var(--accent-color);
        background: var(--accent-glow);
    }

    .sort-btn svg {
        width: 16px;
        height: 16px;
        fill: currentColor;
    }

    /* Table Cells */
    .cell-content {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .status-name {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.875rem;
    }

    .status-slug {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-family: monospace;
    }

    .product-link {
        text-decoration: none;
        color: inherit;
        transition: color var(--transition-normal);
    }

    .product-link:hover {
        color: var(--accent-color);
    }

    .product-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .product-name {
        font-weight: 500;
        color: var(--text-primary);
        font-size: 0.875rem;
    }

    .product-sku {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .template-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        background: linear-gradient(135deg, var(--chart-1), var(--chart-4));
        color: white;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .badge-preview {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        min-width: 80px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .no-badge {
        color: var(--text-muted);
        font-size: 0.875rem;
        font-style: italic;
    }

    .description-cell {
        font-size: 0.875rem;
        color: var(--text-secondary);
        max-width: 300px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .status-indicator {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .status-indicator.active {
        background: rgba(25, 135, 84, 0.1);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.2);
    }

    .status-indicator.inactive {
        background: rgba(108, 117, 125, 0.1);
        color: #6c757d;
        border: 1px solid rgba(108, 117, 125, 0.2);
    }

    .date-cell {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .date {
        font-weight: 500;
        color: var(--text-primary);
        font-size: 0.875rem;
    }

    .time {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .btn-action {
        width: 36px;
        height: 36px;
        border-radius: var(--radius);
        border: 1px solid var(--border-color);
        background: var(--accent);
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all var(--transition-normal);
        text-decoration: none;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .btn-action.view:hover {
        background: var(--info);
        color: white;
        border-color: var(--info);
    }

    .btn-action.edit:hover {
        background: var(--accent-color);
        color: white;
        border-color: var(--accent-color);
    }

    .btn-action.delete:hover {
        background: var(--danger);
        color: white;
        border-color: var(--danger);
    }

    .btn-action svg {
        width: 18px;
        height: 18px;
        fill: currentColor;
    }

    .delete-form {
        display: inline;
    }

    /* Pagination */
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-top: 1px solid var(--border-color);
        background: var(--accent);
    }

    .pagination-info {
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    .pagination-links {
        display: flex;
        gap: 0.5rem;
    }

    .pagination-links .pagination {
        margin: 0;
        display: flex;
        gap: 0.25rem;
    }

    .pagination-links .page-item.active .page-link {
        background: var(--accent-color);
        border-color: var(--accent-color);
        color: white;
    }

    .pagination-links .page-link {
        padding: 0.5rem 0.75rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        color: var(--text-primary);
        text-decoration: none;
        transition: all var(--transition-normal);
    }

    .pagination-links .page-link:hover {
        background: var(--accent-glow);
        border-color: var(--accent-color);
    }

    /* Buttons */
    .btn-primary, .btn-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: var(--radius);
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all var(--transition-normal);
        text-decoration: none;
        min-height: 44px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--accent-color), var(--chart-2));
        color: white;
        box-shadow: 0 2px 8px var(--accent-glow);
    }

    .btn-primary:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px var(--accent-glow);
    }

    .btn-secondary {
        background: var(--accent);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }

    .btn-secondary:hover {
        background: var(--bg-tertiary);
        transform: translateY(-2px);
    }

    .btn-primary svg, .btn-secondary svg {
        width: 20px;
        height: 20px;
        fill: currentColor;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .header-content {
            flex-direction: column;
            gap: 1rem;
        }

        .header-actions {
            width: 100%;
        }

        .btn-primary, .btn-secondary {
            flex: 1;
            min-width: 0;
        }
    }

    @media (max-width: 768px) {
        .container {
            padding: 0 0.75rem;
            margin: 1rem auto;
        }

        .page-title {
            font-size: 2rem;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .filter-row {
            grid-template-columns: 1fr;
        }

        .pagination-container {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }

        .data-table th, .data-table td {
            padding: 0.75rem 1rem;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .header-actions {
            flex-direction: column;
        }

        .filter-actions {
            flex-direction: column;
        }

        .btn-primary, .btn-secondary {
            width: 100%;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn-action {
            width: 32px;
            height: 32px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filter Panel Toggle
        const filterToggle = document.getElementById('filterToggle');
        const filterPanel = document.getElementById('filterPanel');

        if (filterToggle && filterPanel) {
            filterToggle.addEventListener('click', function() {
                const isVisible = filterPanel.style.display === 'block';
                filterPanel.style.display = isVisible ? 'none' : 'block';
                filterToggle.classList.toggle('active', !isVisible);

                // Animate the toggle icon
                const icon = filterToggle.querySelector('svg');
                if (isVisible) {
                    icon.style.transform = 'rotate(0deg)';
                } else {
                    icon.style.transform = 'rotate(180deg)';
                }
            });
        }

        // Filter Functionality
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const sortFilter = document.getElementById('sortFilter');
        const applyFiltersBtn = document.getElementById('applyFilters');
        const clearFiltersBtn = document.getElementById('clearFilters');
        const tableRows = document.querySelectorAll('.table-row');

        function applyFilters() {
            const searchTerm = searchInput.value.toLowerCase();
            const statusValue = statusFilter.value;
            const sortValue = sortFilter.value;

            tableRows.forEach(row => {
                const statusName = row.querySelector('.status-name').textContent.toLowerCase();
                const statusSlug = row.querySelector('.status-slug').textContent.toLowerCase();
                const description = row.querySelector('.description-cell').textContent.toLowerCase();
                const rowStatus = row.getAttribute('data-status');

                let matchesSearch = !searchTerm ||
                    statusName.includes(searchTerm) ||
                    statusSlug.includes(searchTerm) ||
                    description.includes(searchTerm);

                let matchesStatus = statusValue === 'all' || rowStatus === statusValue;

                row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
            });

            // Apply sorting
            const visibleRows = Array.from(tableRows).filter(row => row.style.display !== 'none');

            if (visibleRows.length > 0) {
                visibleRows.sort((a, b) => {
                    const aName = a.querySelector('.status-name').textContent.toLowerCase();
                    const bName = b.querySelector('.status-name').textContent.toLowerCase();
                    const aStatus = a.getAttribute('data-status');
                    const bStatus = b.getAttribute('data-status');
                    const aDate = new Date(a.querySelector('.date').textContent + ' ' + a.querySelector('.time').textContent);
                    const bDate = new Date(b.querySelector('.date').textContent + ' ' + b.querySelector('.time').textContent);

                    switch (sortValue) {
                        case 'name_asc':
                            return aName.localeCompare(bName);
                        case 'name_desc':
                            return bName.localeCompare(aName);
                        case 'newest':
                            return bDate - aDate;
                        case 'oldest':
                            return aDate - bDate;
                        default:
                            return 0;
                    }
                });

                // Reorder rows in DOM
                const tbody = document.querySelector('tbody');
                visibleRows.forEach(row => tbody.appendChild(row));
            }
        }

        // Event Listeners for Filters
        if (applyFiltersBtn) {
            applyFiltersBtn.addEventListener('click', applyFilters);
        }

        if (searchInput) {
            searchInput.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    applyFilters();
                }
            });
        }

        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', function() {
                searchInput.value = '';
                statusFilter.value = 'all';
                sortFilter.value = 'newest';
                applyFilters();
            });
        }

        // Sort buttons in table headers
        document.querySelectorAll('.sort-btn').forEach(button => {
            button.addEventListener('click', function() {
                const sortType = this.getAttribute('data-sort');
                const currentSort = sortFilter.value;

                // Toggle between ascending and descending
                let newSortValue;
                switch (sortType) {
                    case 'name':
                        newSortValue = currentSort === 'name_asc' ? 'name_desc' : 'name_asc';
                        break;
                    case 'created':
                        newSortValue = currentSort === 'newest' ? 'oldest' : 'newest';
                        break;
                    case 'status':
                        newSortValue = currentSort === 'status' ? 'status_desc' : 'status';
                        break;
                    default:
                        newSortValue = 'newest';
                }

                sortFilter.value = newSortValue;
                applyFilters();
            });
        });

        // Delete confirmation
        window.confirmDelete = function(event) {
            event.preventDefault();
            const form = event.target.closest('form');

            if (confirm('Are you sure you want to delete this status? This action cannot be undone.')) {
                form.submit();
            }
            return false;
        };

        // Initialize tooltips
        document.querySelectorAll('.btn-action').forEach(button => {
            button.addEventListener('mouseenter', function() {
                const title = this.getAttribute('title');
                if (title) {
                    const tooltip = document.createElement('div');
                    tooltip.className = 'tooltip';
                    tooltip.textContent = title;
                    tooltip.style.position = 'absolute';
                    tooltip.style.background = 'var(--card)';
                    tooltip.style.color = 'var(--text-primary)';
                    tooltip.style.padding = '0.5rem 0.75rem';
                    tooltip.style.borderRadius = 'var(--radius)';
                    tooltip.style.fontSize = '0.75rem';
                    tooltip.style.boxShadow = 'var(--card-shadow)';
                    tooltip.style.zIndex = '1000';
                    tooltip.style.whiteSpace = 'nowrap';

                    const rect = this.getBoundingClientRect();
                    tooltip.style.top = (rect.top - 40) + 'px';
                    tooltip.style.left = (rect.left + rect.width / 2) + 'px';
                    tooltip.style.transform = 'translateX(-50%)';

                    document.body.appendChild(tooltip);

                    this.tooltip = tooltip;
                }
            });

            button.addEventListener('mouseleave', function() {
                if (this.tooltip) {
                    document.body.removeChild(this.tooltip);
                    delete this.tooltip;
                }
            });
        });

        // Apply initial filters
        applyFilters();
    });
</script>
@endsection
