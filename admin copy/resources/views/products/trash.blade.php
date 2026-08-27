@extends('layouts.app')

@section('title', 'Deleted Products')
@section('page_title', 'Deleted Products')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-lg bg-danger/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-foreground">Deleted Products</h1>
                    <p class="text-sm text-muted-foreground mt-1">Manage and restore products from the recycle bin</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <!-- Search Input -->
            <div class="relative">
                <input type="text"
                       id="searchDeletedProducts"
                       class="form-input pl-9 pr-4 py-2 w-full sm:w-64"
                       placeholder="Search deleted products...">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <a href="{{ route('products.index') }}" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Products
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    @if($products->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="stat-card">
                <div class="stat-icon bg-danger/10">
                    <svg class="w-6 h-6 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <div class="stat-content">
                    <h3 class="stat-label">Total Deleted</h3>
                    <p class="stat-value">{{ number_format($products->total()) }}</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-warning/10">
                    <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="stat-content">
                    <h3 class="stat-label">Days to Auto-Delete</h3>
                    <p class="stat-value">30</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-success/10">
                    <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                <div class="stat-content">
                    <h3 class="stat-label">Restorable Items</h3>
                    <p class="stat-value">{{ number_format($products->count()) }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Deleted Products Table -->
    @if($products->count() === 0)
        <div class="empty-state">
            <div class="empty-state-content">
                <div class="empty-state-icon">
                    <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-foreground mt-4">No Deleted Products Found</h3>
                <p class="text-muted-foreground mt-2">The recycle bin is empty. Deleted products will appear here.</p>
                <div class="mt-6">
                    <a href="{{ route('products.index') }}" class="btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h2m0 0h10m-10 0H4m10 0h4m-6 0v4m0-4v-4m3 3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Browse Products
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-header">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="selectAllDeleted" class="checkbox">
                        <label for="selectAllDeleted" class="text-sm font-medium">Select All</label>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" id="bulkRestoreBtn" class="btn-success btn-sm hidden" disabled>
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Restore Selected
                        </button>
                        <button type="button" id="bulkForceDeleteBtn" class="btn-danger btn-sm hidden" disabled>
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Permanently Delete
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table" id="deletedProductsTable">
                    <thead>
                        <tr>
                            <th class="w-10">Select</th>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Barcode / SKU</th>
                            <th>Category</th>
                            <th>Deleted At</th>
                            <th>Days in Bin</th>
                            <th class="w-48">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr data-id="{{ $product->id }}">
                                <td>
                                    <input type="checkbox" class="row-checkbox checkbox" value="{{ $product->id }}">
                                </td>
                                <td class="font-mono text-sm">{{ $product->id }}</td>
                                <td>
                                    <div>
                                        <div class="font-medium">{{ $product->name }}</div>
                                        @if($product->sku)
                                            <div class="text-xs text-muted-foreground mt-1">SKU: {{ $product->sku }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    {!! $product->barcode ?
                                        '<div class="flex items-center gap-2">
                                            <span class="font-mono text-sm">' . e($product->barcode) . '</span>
                                            <button onclick="copyToClipboard(\'' . e($product->barcode) . '\')" class="btn-icon btn-sm">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                        </div>' :
                                        '<span class="text-muted-foreground">—</span>'
                                    !!}
                                </td>
                                <td>
                                    <span class="badge badge-secondary">{{ $product->category->name ?? 'Uncategorized' }}</span>
                                </td>
                                <td>
                                    <div class="flex flex-col">
                                        <span class="text-sm">{{ $product->deleted_at->format('M d, Y') }}</span>
                                        <span class="text-xs text-muted-foreground">{{ $product->deleted_at->format('h:i A') }}</span>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $daysDeleted = $product->deleted_at->diffInDays(now());
                                        $daysWarning = $daysDeleted >= 25;
                                        $daysCritical = $daysDeleted >= 28;
                                    @endphp
                                    <span class="badge {{ $daysCritical ? 'badge-danger' : ($daysWarning ? 'badge-warning' : 'badge-info') }}">
                                        {{ $daysDeleted }} day(s)
                                    </span>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="{{ route('products.restore', $product->id) }}" class="restore-form">
                                            @csrf
                                            <button type="button"
                                                    class="btn-success btn-sm restore-btn"
                                                    data-id="{{ $product->id }}"
                                                    data-name="{{ $product->name }}">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                                Restore
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('products.forceDelete', $product->id) }}" class="force-delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                    class="btn-danger btn-sm force-delete-btn"
                                                    data-id="{{ $product->id }}"
                                                    data-name="{{ $product->name }}">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Force Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(method_exists($products, 'links') && $products->hasPages())
                <div class="card-footer">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-sm text-muted-foreground">
                            Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} results
                        </div>
                        <div>
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>

<!-- Confirm Modal -->
<div id="confirmModal" class="modal hidden">
    <div class="modal-overlay"></div>
    <div class="modal-container max-w-md">
        <div class="modal-header">
            <h3 class="text-lg font-semibold" id="modalTitle">Confirm Action</h3>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body" id="modalMessage">
            Are you sure you want to perform this action?
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary modal-close">Cancel</button>
            <button type="button" id="modalConfirmBtn" class="btn-primary">Confirm</button>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="toast hidden"></div>

<style>
    /* Alert Styles */
    .alert {
        padding: 1rem;
        border-radius: var(--radius);
        margin-bottom: 1rem;
        animation: slideDown var(--transition-normal);
    }

    .alert-success {
        background: var(--success);
        color: white;
        border-left: 4px solid oklch(0.5 0.2 150);
    }

    .alert-danger {
        background: var(--danger);
        color: white;
        border-left: 4px solid oklch(0.5 0.25 25);
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Empty State */
    .empty-state {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        padding: 4rem 2rem;
        text-align: center;
    }

    .empty-state-icon {
        color: var(--muted-foreground);
        opacity: 0.5;
    }

    /* Badge Variants */
    .badge-info {
        background: var(--info);
        color: white;
    }

    /* Modal Styles (reused from previous) */
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal.hidden {
        display: none;
    }

    .modal-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
    }

    .modal-container {
        position: relative;
        background: var(--bg-secondary);
        border-radius: var(--radius);
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: var(--dropdown-shadow);
        animation: modalSlideIn var(--transition-normal);
    }

    .modal-container.max-w-md {
        max-width: 28rem;
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .modal-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: var(--text-secondary);
        transition: color var(--transition-fast);
    }

    .modal-close:hover {
        color: var(--text-primary);
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    /* Toast Notification */
    .toast {
        position: fixed;
        bottom: 1.5rem;
        right: 1.5rem;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        padding: 1rem 1.5rem;
        box-shadow: var(--dropdown-shadow);
        z-index: 1100;
        animation: toastSlideIn var(--transition-normal);
    }

    .toast.hidden {
        display: none;
    }

    @keyframes toastSlideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .toast.success {
        border-left: 4px solid var(--success);
    }

    .toast.error {
        border-left: 4px solid var(--danger);
    }

    /* Copy tooltip */
    .tooltip {
        position: relative;
        display: inline-block;
    }

    .tooltip .tooltip-text {
        visibility: hidden;
        background-color: var(--bg-primary);
        color: var(--text-primary);
        text-align: center;
        padding: 4px 8px;
        border-radius: 4px;
        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        margin-left: -60px;
        opacity: 0;
        transition: opacity 0.3s;
        font-size: 12px;
        white-space: nowrap;
        border: 1px solid var(--border-color);
    }

    .tooltip:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .modal-container {
            width: 95%;
            margin: 1rem;
        }

        .toast {
            left: 1rem;
            right: 1rem;
            bottom: 1rem;
        }

        .data-table th,
        .data-table td {
            padding: 0.75rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeSearch();
        initializeBulkActions();
        initializeModals();
        initializeRestoreAndDelete();
    });

    // Search functionality
    function initializeSearch() {
        const searchInput = document.getElementById('searchDeletedProducts');
        const table = document.getElementById('deletedProductsTable');
        if (!searchInput || !table) return;

        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchTerm = this.value.toLowerCase();
                const rows = table.querySelectorAll('tbody tr');

                rows.forEach(row => {
                    const name = row.querySelector('td:nth-child(3) .font-medium')?.textContent.toLowerCase() || '';
                    const barcode = row.querySelector('td:nth-child(4) .font-mono')?.textContent.toLowerCase() || '';
                    const id = row.querySelector('td:nth-child(2)')?.textContent || '';

                    if (name.includes(searchTerm) || barcode.includes(searchTerm) || id.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }, 300);
        });
    }

    // Bulk actions
    function initializeBulkActions() {
        const selectAllCheckbox = document.getElementById('selectAllDeleted');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const bulkRestoreBtn = document.getElementById('bulkRestoreBtn');
        const bulkForceDeleteBtn = document.getElementById('bulkForceDeleteBtn');

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkButtons();
            });
        }

        rowCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkButtons);
        });

        function updateBulkButtons() {
            const selectedCount = document.querySelectorAll('.row-checkbox:checked').length;

            if (selectedCount > 0) {
                bulkRestoreBtn?.classList.remove('hidden');
                bulkForceDeleteBtn?.classList.remove('hidden');
                bulkRestoreBtn.disabled = false;
                bulkForceDeleteBtn.disabled = false;
            } else {
                bulkRestoreBtn?.classList.add('hidden');
                bulkForceDeleteBtn?.classList.add('hidden');
            }
        }

        // Bulk Restore
        if (bulkRestoreBtn) {
            bulkRestoreBtn.addEventListener('click', function() {
                const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                    .map(cb => cb.value);

                if (selectedIds.length === 0) return;

                showConfirmModal(
                    'Restore Products',
                    `Are you sure you want to restore ${selectedIds.length} product(s)?`,
                    () => performBulkAction(selectedIds, 'restore')
                );
            });
        }

        // Bulk Force Delete
        if (bulkForceDeleteBtn) {
            bulkForceDeleteBtn.addEventListener('click', function() {
                const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                    .map(cb => cb.value);

                if (selectedIds.length === 0) return;

                showConfirmModal(
                    'Permanently Delete Products',
                    `⚠️ Warning: This action cannot be undone! Are you sure you want to permanently delete ${selectedIds.length} product(s)?`,
                    () => performBulkAction(selectedIds, 'forceDelete')
                );
            });
        }
    }

    function performBulkAction(ids, action) {
        const url = action === 'restore'
            ? '{{ route("products.bulk-restore") }}'
            : '{{ route("products.bulk-force-delete") }}';

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ids: ids })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            showToast('An error occurred. Please try again.', 'error');
        });
    }

    // Modal handling
    let modalConfirmCallback = null;

    function initializeModals() {
        const modal = document.getElementById('confirmModal');
        const closeButtons = document.querySelectorAll('.modal-close');
        const confirmBtn = document.getElementById('modalConfirmBtn');

        closeButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                modal.classList.add('hidden');
                modalConfirmCallback = null;
            });
        });

        if (confirmBtn) {
            confirmBtn.addEventListener('click', () => {
                if (modalConfirmCallback) {
                    modalConfirmCallback();
                    modalConfirmCallback = null;
                }
                modal.classList.add('hidden');
            });
        }

        // Close on overlay click
        const overlay = document.querySelector('#confirmModal .modal-overlay');
        if (overlay) {
            overlay.addEventListener('click', () => {
                modal.classList.add('hidden');
                modalConfirmCallback = null;
            });
        }
    }

    function showConfirmModal(title, message, callback) {
        const modal = document.getElementById('confirmModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalMessage = document.getElementById('modalMessage');

        if (modalTitle) modalTitle.textContent = title;
        if (modalMessage) modalMessage.innerHTML = message;

        modalConfirmCallback = callback;
        modal.classList.remove('hidden');
    }

    // Individual restore and delete handlers
    function initializeRestoreAndDelete() {
        // Restore buttons
        document.querySelectorAll('.restore-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const productName = this.getAttribute('data-name');
                showConfirmModal(
                    'Restore Product',
                    `Are you sure you want to restore "${productName}"?`,
                    () => this.closest('form').submit()
                );
            });
        });

        // Force delete buttons
        document.querySelectorAll('.force-delete-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const productName = this.getAttribute('data-name');
                showConfirmModal(
                    'Permanently Delete Product',
                    `⚠️ Warning: This action cannot be undone! Are you sure you want to permanently delete "${productName}"?`,
                    () => this.closest('form').submit()
                );
            });
        });
    }

    // Copy to clipboard
    window.copyToClipboard = function(text) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('Copied to clipboard!', 'success');
        }).catch(() => {
            showToast('Failed to copy', 'error');
        });
    }

    // Toast notification
    function showToast(message, type = 'info') {
        const toast = document.getElementById('toast');
        if (!toast) return;

        toast.textContent = message;
        toast.className = `toast ${type}`;
        toast.classList.remove('hidden');

        setTimeout(() => {
            toast.classList.add('hidden');
        }, 3000);
    }
</script>
@endsection
