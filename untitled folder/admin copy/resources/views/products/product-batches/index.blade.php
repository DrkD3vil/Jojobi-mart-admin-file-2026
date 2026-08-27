@extends('layouts.app')

@section('content')
    <div class="container-fluid py-3">

        {{-- Header --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div class="min-w-0">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge ui-badge">Inventory</span>
                    <h4 class="mb-0 text-truncate">Batches for: {{ $product->name }}</h4>
                </div>
                <div class="ui-muted small mt-1">
                    Barcode: <span class="ui-mono">{{ $product->barcode ?? '—' }}</span>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('product.batches.all') }}" class="btn ui-btn ui-btn-secondary">
                    <i class="bi bi-arrow-left"></i> All Batches
                </a>

                <button class="btn ui-btn ui-btn-primary" type="button" data-ui-open-drawer="#drawerAddBatch">
                    <i class="bi bi-plus-lg"></i> Add Batch
                </button>
            </div>
        </div>

        {{-- Flash / Errors --}}
        @if (session('success'))
            <div class="ui-alert ui-alert-success mb-3">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="ui-alert ui-alert-danger mb-3">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="ui-alert ui-alert-danger mb-3">
                <div class="fw-semibold mb-1">Please fix the errors:</div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Top Stats --}}
        <div class="row g-3 mb-3">
            <div class="col-12 col-md-6 col-xl-3">
                <div class="ui-card p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="ui-muted small">Total Stock</div>
                            <div class="fs-4 fw-semibold">{{ number_format($totalStock, 3) }}</div>
                        </div>
                        <div class="ui-icon ui-icon-info">
                            <i class="bi bi-box-seam"></i>
                        </div>
                    </div>
                    <div class="ui-muted small mt-2">Across {{ $totalBatches }} batches</div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="ui-card p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="ui-muted small">Expiring Soon</div>
                            <div class="fs-4 fw-semibold" id="statExpiringSoon">—</div>
                        </div>
                        <div class="ui-icon ui-icon-warning">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                    <div class="ui-muted small mt-2">Next 30 days</div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="ui-card p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="ui-muted small">Low Stock</div>
                            <div class="fs-4 fw-semibold" id="statLowStock">—</div>
                        </div>
                        <div class="ui-icon ui-icon-danger">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                    </div>
                    <div class="ui-muted small mt-2">Qty ≤ 10</div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <a href="{{ route('product-batches.trash') }}" class="ui-card p-3 text-decoration-none d-block">

                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="ui-muted small">Batch Trash</div>
                            <div class="fs-4 fw-semibold text-danger">
                                {{ $trashedBatchCount }}
                            </div>
                        </div>

                        <div class="ui-icon ui-icon-danger">
                            <i class="bi bi-trash"></i>
                        </div>
                    </div>

                    <div class="ui-muted small mt-2">
                        Archived batches
                    </div>
                </a>
            </div>


            <div class="col-12 col-md-6 col-xl-3">
                <div class="ui-card p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="ui-muted small">Active Batches</div>
                            <div class="fs-4 fw-semibold" id="statActive">—</div>
                        </div>
                        <div class="ui-icon ui-icon-success">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                    </div>
                    <div class="ui-muted small mt-2">Is Active = true</div>
                </div>
            </div>
        </div>

        {{-- Toolbar --}}
        <div class="ui-card p-3 mb-3">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-lg-5">
                    <div class="ui-input-wrap">
                        <i class="bi bi-search ui-input-icon"></i>
                        <input id="searchInput" class="form-control ui-input" placeholder="Search batch no / sku / notes…"
                            autocomplete="off">
                    </div>
                </div>

                <div class="col-12 col-lg-7">
                    <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                        <button class="btn ui-btn ui-btn-secondary" type="button" id="btnClearFilters">
                            <i class="bi bi-x-circle"></i> Clear
                        </button>

                        <button class="btn ui-btn ui-btn-secondary" type="button" id="btnOnlyActive">
                            <i class="bi bi-funnel"></i> Only Active
                        </button>

                        <button class="btn ui-btn ui-btn-secondary" type="button" id="btnExpiringSoon">
                            <i class="bi bi-hourglass"></i> Expiring Soon
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="ui-card p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table ui-table align-middle mb-0" id="batchesTable">
                    <thead>
                        <tr>
                            <th>Batch</th>
                            <th class="text-end">Buy</th>
                            <th class="text-end">Sell</th>
                            <th class="text-end">Qty</th>
                            <th>Expiry</th>
                            <th>Status</th>
                            <th class="text-end" style="width: 120px;">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($batches as $batch)
                            @php
                                $expiry = $batch->expiry_date ? $batch->expiry_date->format('Y-m-d') : null;
                            @endphp
                            <tr data-batch-row
                                data-batch="{{ strtolower(($batch->batch_no ?? '') . ' ' . ($batch->batch_sku ?? '') . ' ' . ($batch->notes ?? '')) }}"
                                data-active="{{ $batch->is_active ? 1 : 0 }}" data-expiry="{{ $expiry ?? '' }}"
                                data-qty="{{ (float) $batch->quantity }}">
                                <td class="min-w-0">
                                    <div class="d-flex flex-column">
                                        <div class="fw-semibold text-truncate">
                                            {{ $batch->batch_no ?? '—' }}
                                            <span class="ui-muted fw-normal">({{ $batch->batch_sku ?? '—' }})</span>
                                        </div>
                                        <div class="small ui-muted text-truncate">
                                            Unit: <span class="ui-mono">{{ $batch->unit ?? '—' }}</span>
                                            @if ($batch->is_free_offer_active)
                                                <span class="badge ui-badge ms-2">Gift Offer</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="text-end ui-mono">
                                    {{ number_format((float) $batch->buy_price, 2) }}
                                </td>

                                <td class="text-end ui-mono">
                                    {{ number_format((float) $batch->sell_price, 2) }}
                                    @if ($batch->discounted_price || $batch->discount_percentage)
                                        <div class="small ui-muted">
                                            @if ($batch->discounted_price)
                                                -{{ number_format((float) $batch->discounted_price, 2) }}
                                            @else
                                                -{{ number_format((float) $batch->discount_percentage, 2) }}%
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                <td class="text-end ui-mono">
                                    {{ number_format((float) $batch->quantity, 3) }}
                                    @if ((float) $batch->quantity <= 0)
                                        <div class="small text-danger">Out of stock</div>
                                    @elseif((float) $batch->quantity <= 10)
                                        <div class="small text-warning">Low stock</div>
                                    @endif
                                </td>

                                <td>
                                    @if ($expiry)
                                        <span class="ui-mono">{{ $expiry }}</span>
                                    @else
                                        <span class="ui-muted">No expiry</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($batch->is_active)
                                        <span class="ui-pill ui-pill-success">Active</span>
                                    @else
                                        <span class="ui-pill ui-pill-muted">Inactive</span>
                                    @endif

                                    <div class="small ui-muted mt-1">
                                        @php
                                            $channels = [];
                                            if ($batch->is_online) {
                                                $channels[] = 'Online';
                                            }
                                            if ($batch->is_offline) {
                                                $channels[] = 'Offline';
                                            }
                                            if ($batch->is_pos) {
                                                $channels[] = 'POS';
                                            }
                                        @endphp
                                        {{ $channels ? implode(' • ', $channels) : 'No channel' }}
                                    </div>
                                </td>

                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a href="{{ route('product.batches.edit', $batch) }}"
                                            class="btn ui-btn ui-btn-secondary btn-sm">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <button class="btn ui-btn ui-btn-danger btn-sm" type="button"
                                            data-ui-confirm="Delete this batch?"
                                            data-ui-confirm-text="This action cannot be undone."
                                            data-ui-confirm-action="{{ route('product.batches.destroy', $batch) }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="ui-muted">No batches found for this product.</div>
                                    <button class="btn ui-btn ui-btn-primary mt-3" type="button"
                                        data-ui-open-drawer="#drawerAddBatch">
                                        <i class="bi bi-plus-lg"></i> Add First Batch
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if (method_exists($batches, 'links'))
                <div class="p-3 border-top ui-border">
                    {{ $batches->links() }}
                </div>
            @endif
        </div>

    </div>

    {{-- Drawer: Add Batch --}}
    <div class="ui-drawer" id="drawerAddBatch" aria-hidden="true">
        <div class="ui-drawer-backdrop" data-ui-close-drawer></div>

        <div class="ui-drawer-panel">
            <div class="ui-drawer-header">
                <div class="min-w-0">
                    <div class="fw-semibold">Add Batch</div>
                    <div class="small ui-muted text-truncate">{{ $product->name }}</div>
                </div>
                <button class="btn ui-btn ui-btn-secondary btn-sm" type="button" data-ui-close-drawer>
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('product.batches.store') }}" class="ui-drawer-body">
                @csrf

                {{-- IMPORTANT: your store() needs product_id --}}
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <div class="ui-section">
                    <div class="ui-section-title">Basic</div>

                    <div class="row g-2">
                        <div class="col-12 col-md-6">
                            <label class="ui-label">Batch No</label>
                            <input name="batch_no" class="form-control ui-input" placeholder="e.g. A-102"
                                value="{{ old('batch_no') }}">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="ui-label">Unit <span class="text-danger">*</span></label>
                            <input name="unit" class="form-control ui-input" placeholder="pcs / kg / box"
                                value="{{ old('unit', 'pcs') }}" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="ui-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" step="0.001" min="0.001" name="quantity"
                                class="form-control ui-input" value="{{ old('quantity') }}" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="ui-label">Buy Price <span class="text-danger">*</span></label>
                            <input type="number" step="0.0001" min="0" name="buy_price"
                                class="form-control ui-input" value="{{ old('buy_price') }}" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="ui-label">Original Sell Price <span class="text-danger">*</span></label>
                            <input type="number" step="0.0001" min="0" name="original_sell_price"
                                class="form-control ui-input" value="{{ old('original_sell_price') }}" required
                                data-ui-price="original">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="ui-label">Discount (Fixed)</label>
                            <input type="number" step="0.0001" min="0" name="discounted_price"
                                class="form-control ui-input" value="{{ old('discounted_price') }}"
                                placeholder="e.g. 10" data-ui-price="fixed">
                            <div class="small ui-muted mt-1">If set, percentage will be ignored.</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="ui-label">Discount (%)</label>
                            <input type="number" step="0.01" min="0" max="100"
                                name="discount_percentage" class="form-control ui-input"
                                value="{{ old('discount_percentage') }}" placeholder="e.g. 5" data-ui-price="percent">
                            <div class="small ui-muted mt-1">If fixed discount is set, this is ignored.</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="ui-card p-3">
                                <div class="ui-muted small">Preview Sell Price</div>
                                <div class="fs-4 fw-semibold ui-mono" id="sellPreview">—</div>
                                <div class="small ui-muted">Auto-calculated in controller on submit ✅</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ui-section">
                    <div class="ui-section-title">Dates</div>

                    <div class="row g-2">
                        <div class="col-12 col-md-6">
                            <label class="ui-label">Manufacture Date</label>
                            <input type="date" name="manufacture_date" class="form-control ui-input"
                                value="{{ old('manufacture_date') }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="ui-label">Expiry Date</label>
                            <input type="date" name="expiry_date" class="form-control ui-input"
                                value="{{ old('expiry_date') }}">
                        </div>
                    </div>
                </div>

                <div class="ui-section">
                    <div class="ui-section-title">Wholesale (Optional)</div>

                    <div class="row g-2">
                        <div class="col-12 col-md-4">
                            <label class="ui-label">Wholesale Price</label>
                            <input type="number" step="0.0001" min="0" name="whole_sell_price"
                                class="form-control ui-input" value="{{ old('whole_sell_price') }}">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="ui-label">Min Qty</label>
                            <input type="number" step="0.001" min="0" name="whole_sell_min_qty"
                                class="form-control ui-input" value="{{ old('whole_sell_min_qty') }}">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="ui-label">Max Qty</label>
                            <input type="number" step="0.001" min="0" name="whole_sell_max_qty"
                                class="form-control ui-input" value="{{ old('whole_sell_max_qty') }}">
                        </div>
                    </div>

                    <div class="row g-2 mt-1">
                        <div class="col-12 col-md-4">
                            <label class="ui-label">Customer Wholesale Price</label>
                            <input type="number" step="0.0001" min="0" name="customer_whole_price"
                                class="form-control ui-input" value="{{ old('customer_whole_price') }}">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="ui-label">Customer Min Qty</label>
                            <input type="number" step="0.001" min="0" name="customer_whole_min_qty"
                                class="form-control ui-input" value="{{ old('customer_whole_min_qty') }}">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="ui-label">Customer Max Qty</label>
                            <input type="number" step="0.001" min="0" name="customer_whole_max_qty"
                                class="form-control ui-input" value="{{ old('customer_whole_max_qty') }}">
                        </div>
                    </div>
                </div>

                <div class="ui-section">
                    <div class="ui-section-title">Channels & Status</div>

                    <div class="row g-2">
                        <div class="col-12">
                            <div class="ui-check-grid">
                                <label class="ui-check">
                                    <input type="checkbox" name="is_online" value="1" checked>
                                    <span>Online</span>
                                </label>
                                <label class="ui-check">
                                    <input type="checkbox" name="is_offline" value="1" checked>
                                    <span>Offline</span>
                                </label>
                                <label class="ui-check">
                                    <input type="checkbox" name="is_pos" value="1" checked>
                                    <span>POS</span>
                                </label>
                                <label class="ui-check">
                                    <input type="checkbox" name="is_active" value="1" checked>
                                    <span>Active</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="ui-label">Notes</label>
                            <textarea name="notes" class="form-control ui-input" rows="3" placeholder="Internal notes...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="ui-section">
                    <div class="ui-section-title d-flex align-items-center justify-content-between">
                        <span>Free Gift Offer</span>
                        <label class="ui-switch">
                            <input type="checkbox" name="is_free_offer_active" value="1" id="giftToggle">
                            <span class="ui-switch-track"></span>
                        </label>
                    </div>

                    <div id="giftFields" class="row g-2" style="display:none;">
                        <div class="col-12">
                            <label class="ui-label">Gift Product ID <span class="ui-muted">(use your quick search screen
                                    for now)</span></label>
                            <input type="number" name="free_product_id" class="form-control ui-input"
                                placeholder="Gift product id">
                            <div class="small ui-muted mt-1">
                                You already have API: <span class="ui-mono">product-batches/api/gifts/search</span> (can be
                                wired to Select2 later).
                            </div>
                        </div>

                        <div class="col-6">
                            <label class="ui-label">Buy Qty</label>
                            <input type="number" step="0.0001" min="0.0001" name="free_buy_qty"
                                class="form-control ui-input" placeholder="e.g. 2">
                        </div>
                        <div class="col-6">
                            <label class="ui-label">Free Qty</label>
                            <input type="number" step="0.0001" min="0.0001" name="free_qty"
                                class="form-control ui-input" placeholder="e.g. 1">
                        </div>
                    </div>
                </div>

                <div class="ui-drawer-footer">
                    <button class="btn ui-btn ui-btn-secondary" type="button" data-ui-close-drawer>Cancel</button>
                    <button class="btn ui-btn ui-btn-primary" type="submit">
                        <i class="bi bi-check2"></i> Save Batch
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Confirm Modal (single, reusable) --}}
    <form method="POST" id="confirmDeleteForm" class="d-none">
        @csrf
        @method('DELETE')
    </form>

    {{-- Toast --}}
    <div class="ui-toast" id="uiToast" role="status" aria-live="polite" aria-atomic="true">
        <div class="ui-toast-inner" id="uiToastText">Done</div>
    </div>


    <style>
        /* =========================================================
           Uses YOUR CSS variables (oklch theme). Bootstrap-friendly.
           ========================================================= */

        /* Card / borders */
        .ui-card {
            background: var(--card);
            color: var(--card-foreground);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--card-shadow);
        }

        .ui-card:hover {
            box-shadow: var(--card-shadow-hover);
        }

        .ui-border {
            border-color: var(--border) !important;
        }

        .ui-muted {
            color: var(--muted-foreground) !important;
        }

        .ui-mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        }

        .ui-badge {
            background: color-mix(in oklch, var(--accent-color) 20%, transparent);
            color: var(--foreground);
            border: 1px solid color-mix(in oklch, var(--accent-color) 35%, transparent);
            border-radius: 999px;
            padding: .35rem .6rem;
        }

        /* Buttons */
        .ui-btn {
            border-radius: calc(var(--radius) - 2px);
            border: 1px solid var(--border);
            transition: transform var(--transition-fast) ease, background var(--transition-fast) ease, border-color var(--transition-fast) ease;
        }

        .ui-btn:active {
            transform: translateY(1px);
        }

        .ui-btn-primary {
            background: var(--sidebar-primary);
            color: var(--sidebar-primary-foreground);
            border-color: color-mix(in oklch, var(--sidebar-primary) 70%, var(--border));
        }

        .ui-btn-primary:hover {
            background: color-mix(in oklch, var(--sidebar-primary) 85%, white 0%);
        }

        .ui-btn-secondary {
            background: color-mix(in oklch, var(--secondary) 75%, transparent);
            color: var(--secondary-foreground);
        }

        .ui-btn-secondary:hover {
            background: color-mix(in oklch, var(--secondary) 90%, transparent);
        }

        .ui-btn-danger {
            background: color-mix(in oklch, var(--destructive) 18%, transparent);
            color: var(--foreground);
            border-color: color-mix(in oklch, var(--destructive) 40%, var(--border));
        }

        .ui-btn-danger:hover {
            background: color-mix(in oklch, var(--destructive) 28%, transparent);
        }

        /* Alerts */
        .ui-alert {
            border-radius: var(--radius);
            padding: .85rem 1rem;
            border: 1px solid var(--border);
            background: color-mix(in oklch, var(--card) 85%, transparent);
        }

        .ui-alert-success {
            border-color: color-mix(in oklch, var(--success) 35%, var(--border));
            background: color-mix(in oklch, var(--success) 12%, var(--card));
        }

        .ui-alert-danger {
            border-color: color-mix(in oklch, var(--destructive) 35%, var(--border));
            background: color-mix(in oklch, var(--destructive) 12%, var(--card));
        }

        /* Inputs */
        .ui-input {
            background: color-mix(in oklch, var(--background) 30%, var(--card));
            color: var(--foreground);
            border: 1px solid var(--input);
            border-radius: calc(var(--radius) - 2px);
        }

        .ui-input:focus {
            border-color: color-mix(in oklch, var(--ring) 60%, var(--border));
            box-shadow: 0 0 0 .2rem color-mix(in oklch, var(--accent-color) 20%, transparent);
        }

        .ui-label {
            font-size: .85rem;
            color: var(--muted-foreground);
            margin-bottom: .35rem;
        }

        .ui-input-wrap {
            position: relative;
        }

        .ui-input-icon {
            position: absolute;
            left: .8rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted-foreground);
        }

        .ui-input-wrap .ui-input {
            padding-left: 2.3rem;
        }

        /* Table */
        .ui-table {
            color: var(--foreground);
        }

        .ui-table thead th {
            position: sticky;
            top: 0;
            background: color-mix(in oklch, var(--card) 85%, var(--background));
            border-bottom: 1px solid var(--border);
            color: var(--muted-foreground);
            font-weight: 600;
            z-index: 1;
        }

        .ui-table td,
        .ui-table th {
            border-color: var(--border) !important;
        }

        .ui-table tbody tr:hover {
            background: color-mix(in oklch, var(--accent-color) 8%, transparent);
        }

        /* Pills */
        .ui-pill {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .25rem .55rem;
            border-radius: 999px;
            font-size: .78rem;
            border: 1px solid var(--border);
        }

        .ui-pill-success {
            background: color-mix(in oklch, var(--success) 14%, transparent);
            border-color: color-mix(in oklch, var(--success) 35%, var(--border));
        }

        .ui-pill-muted {
            background: color-mix(in oklch, var(--muted) 50%, transparent);
        }

        /* Icon bubbles */
        .ui-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border);
            box-shadow: var(--card-shadow);
        }

        .ui-icon i {
            font-size: 1.2rem;
        }

        .ui-icon-info {
            background: color-mix(in oklch, var(--info) 15%, transparent);
        }

        .ui-icon-warning {
            background: color-mix(in oklch, var(--warning) 15%, transparent);
        }

        .ui-icon-danger {
            background: color-mix(in oklch, var(--danger) 15%, transparent);
        }

        .ui-icon-success {
            background: color-mix(in oklch, var(--success) 15%, transparent);
        }

        /* Sections inside drawer */
        .ui-section {
            padding: .75rem 0;
            border-top: 1px solid var(--border);
        }

        .ui-section:first-child {
            border-top: 0;
            padding-top: 0;
        }

        .ui-section-title {
            font-weight: 700;
            margin-bottom: .5rem;
        }

        /* Check grid */
        .ui-check-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .5rem;
        }

        @media (min-width: 768px) {
            .ui-check-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        .ui-check {
            display: flex;
            gap: .5rem;
            align-items: center;
            padding: .55rem .65rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: color-mix(in oklch, var(--card) 90%, transparent);
        }

        .ui-check input {
            accent-color: var(--accent-color);
        }

        /* Switch */
        .ui-switch {
            display: inline-flex;
            align-items: center;
            cursor: pointer;
        }

        .ui-switch input {
            display: none;
        }

        .ui-switch-track {
            width: 46px;
            height: 26px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: color-mix(in oklch, var(--muted) 60%, transparent);
            position: relative;
            transition: background var(--transition-fast) ease;
        }

        .ui-switch-track::after {
            content: "";
            width: 20px;
            height: 20px;
            border-radius: 999px;
            background: var(--foreground);
            position: absolute;
            top: 50%;
            left: 3px;
            transform: translateY(-50%);
            transition: left var(--transition-fast) ease;
        }

        .ui-switch input:checked+.ui-switch-track {
            background: color-mix(in oklch, var(--accent-color) 45%, transparent);
        }

        .ui-switch input:checked+.ui-switch-track::after {
            left: 23px;
        }

        /* Drawer */
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
            background: rgba(0, 0, 0, .55);
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
            width: min(520px, 92vw);
            background: var(--card);
            color: var(--card-foreground);
            border-left: 1px solid var(--border);
            box-shadow: var(--dropdown-shadow);
            transform: translateX(100%);
            transition: transform var(--transition-normal) ease;
            display: flex;
            flex-direction: column;
        }

        .ui-drawer[aria-hidden="false"] .ui-drawer-panel {
            transform: translateX(0);
        }

        .ui-drawer-header {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .ui-drawer-body {
            padding: 1rem;
            overflow: auto;
            flex: 1;
        }

        .ui-drawer-footer {
            padding: 1rem;
            border-top: 1px solid var(--border);
            display: flex;
            gap: .75rem;
            justify-content: flex-end;
        }

        /* Toast */
        .ui-toast {
            position: fixed;
            bottom: 18px;
            right: 18px;
            z-index: 2000;
            opacity: 0;
            transform: translateY(10px);
            pointer-events: none;
            transition: opacity var(--transition-fast) ease, transform var(--transition-fast) ease;
        }

        .ui-toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .ui-toast-inner {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: .7rem .9rem;
            box-shadow: var(--dropdown-shadow);
        }
    </style>

    <script>
        (function() {
            // Drawer open/close
            document.addEventListener('click', function(e) {
                const openBtn = e.target.closest('[data-ui-open-drawer]');
                const closeBtn = e.target.closest('[data-ui-close-drawer]');
                const backdrop = e.target.closest('.ui-drawer-backdrop');

                if (openBtn) {
                    const sel = openBtn.getAttribute('data-ui-open-drawer');
                    const drawer = document.querySelector(sel);
                    if (drawer) drawer.setAttribute('aria-hidden', 'false');
                }
                if (closeBtn || backdrop) {
                    const drawer = e.target.closest('.ui-drawer') || document.querySelector(
                        '.ui-drawer[aria-hidden="false"]');
                    if (drawer) drawer.setAttribute('aria-hidden', 'true');
                }
            });

            // Escape to close drawer
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const open = document.querySelector('.ui-drawer[aria-hidden="false"]');
                    if (open) open.setAttribute('aria-hidden', 'true');
                }
            });

            // Confirm delete (modern confirm modal behavior without Bootstrap JS dependency)
            const confirmForm = document.getElementById('confirmDeleteForm');
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('[data-ui-confirm]');
                if (!btn) return;

                const title = btn.getAttribute('data-ui-confirm') || 'Are you sure?';
                const text = btn.getAttribute('data-ui-confirm-text') || '';
                const action = btn.getAttribute('data-ui-confirm-action');

                const ok = window.confirm(title + (text ? "\n\n" + text : ""));
                if (ok && confirmForm && action) {
                    confirmForm.setAttribute('action', action);
                    confirmForm.submit();
                }
            });

            // Search/filter
            const searchInput = document.getElementById('searchInput');
            const btnClear = document.getElementById('btnClearFilters');
            const btnOnlyActive = document.getElementById('btnOnlyActive');
            const btnExpiringSoon = document.getElementById('btnExpiringSoon');
            const rows = Array.from(document.querySelectorAll('[data-batch-row]'));

            let onlyActive = false;
            let onlyExpiring = false;

            function isExpiringSoon(expiryStr) {
                if (!expiryStr) return false;
                const expiry = new Date(expiryStr + "T00:00:00");
                const now = new Date();
                const limit = new Date();
                limit.setDate(limit.getDate() + 30);
                return expiry > now && expiry <= limit;
            }

            function applyFilters() {
                const q = (searchInput?.value || '').trim().toLowerCase();

                let expiringCount = 0,
                    lowCount = 0,
                    activeCount = 0;
                rows.forEach(r => {
                    const text = r.getAttribute('data-batch') || '';
                    const active = r.getAttribute('data-active') === '1';
                    const expiry = r.getAttribute('data-expiry') || '';
                    const qty = parseFloat(r.getAttribute('data-qty') || '0');

                    const matchQ = !q || text.includes(q);
                    const matchActive = !onlyActive || active;
                    const matchExp = !onlyExpiring || isExpiringSoon(expiry);

                    const show = matchQ && matchActive && matchExp;
                    r.style.display = show ? '' : 'none';

                    // stats on visible rows only (feels better UX)
                    if (show) {
                        if (isExpiringSoon(expiry)) expiringCount++;
                        if (qty > 0 && qty <= 10) lowCount++;
                        if (active) activeCount++;
                    }
                });

                const elExp = document.getElementById('statExpiringSoon');
                const elLow = document.getElementById('statLowStock');
                const elAct = document.getElementById('statActive');
                if (elExp) elExp.textContent = expiringCount;
                if (elLow) elLow.textContent = lowCount;
                if (elAct) elAct.textContent = activeCount;
            }

            searchInput?.addEventListener('input', applyFilters);
            btnClear?.addEventListener('click', function() {
                onlyActive = false;
                onlyExpiring = false;
                if (searchInput) searchInput.value = '';
                btnOnlyActive?.classList.remove('ui-btn-primary');
                btnExpiringSoon?.classList.remove('ui-btn-primary');
                applyFilters();
                toast("Filters cleared");
            });

            btnOnlyActive?.addEventListener('click', function() {
                onlyActive = !onlyActive;
                btnOnlyActive.classList.toggle('ui-btn-primary', onlyActive);
                applyFilters();
                toast(onlyActive ? "Showing only active batches" : "Showing all batches");
            });

            btnExpiringSoon?.addEventListener('click', function() {
                onlyExpiring = !onlyExpiring;
                btnExpiringSoon.classList.toggle('ui-btn-primary', onlyExpiring);
                applyFilters();
                toast(onlyExpiring ? "Showing expiring soon" : "Showing all expiries");
            });

            // Gift toggle fields
            const giftToggle = document.getElementById('giftToggle');
            const giftFields = document.getElementById('giftFields');

            function syncGiftUI() {
                if (!giftToggle || !giftFields) return;
                giftFields.style.display = giftToggle.checked ? '' : 'none';
            }
            giftToggle?.addEventListener('change', syncGiftUI);
            syncGiftUI();

            // Sell price preview (UX only — controller is source of truth)
            const original = document.querySelector('[data-ui-price="original"]');
            const fixed = document.querySelector('[data-ui-price="fixed"]');
            const percent = document.querySelector('[data-ui-price="percent"]');
            const sellPreview = document.getElementById('sellPreview');

            function calcPreview() {
                const o = parseFloat(original?.value || '0') || 0;
                const f = parseFloat(fixed?.value || '0') || 0;
                const p = parseFloat(percent?.value || '0') || 0;

                let sell = o;
                if (f > 0) {
                    sell = o - f;
                } else if (p > 0) {
                    sell = o - (o * (p / 100));
                }
                sell = Math.max(0, sell);
                if (sellPreview) sellPreview.textContent = sell.toFixed(4);
            }
            [original, fixed, percent].forEach(el => el?.addEventListener('input', calcPreview));
            calcPreview();

            // Toast
            const toastEl = document.getElementById('uiToast');
            const toastText = document.getElementById('uiToastText');
            let toastTimer = null;

            function toast(msg) {
                if (!toastEl || !toastText) return;
                toastText.textContent = msg;
                toastEl.classList.add('show');
                clearTimeout(toastTimer);
                toastTimer = setTimeout(() => toastEl.classList.remove('show'), 1800);
            }

            // Init stats
            applyFilters();
        })();
    </script>
@endsection
