{{-- resources/views/orders/split-select.blade.php --}}
@extends('layouts.app')

@php
    use Illuminate\Support\Str;

    if (!function_exists('currency_bdt')) {
        function currency_bdt($amount) {
            return '৳ ' . number_format((float) $amount, 2);
        }
    }
@endphp

@section('content')
<div class="container py-4">
    <style>
        :root {
            --background: oklch(0.145 0 0);
            --foreground: oklch(0.985 0 0);
            --card: oklch(0.205 0 0);
            --card-foreground: oklch(0.985 0 0);
            --primary: oklch(0.488 0.243 264.376);
            --primary-foreground: oklch(0.985 0 0);
            --secondary: oklch(0.269 0 0);
            --secondary-foreground: oklch(0.985 0 0);
            --muted: oklch(0.269 0 0);
            --muted-foreground: oklch(0.708 0 0);
            --border: oklch(0.269 0 0);
            --input: oklch(0.269 0 0);
            --success: oklch(0.696 0.17 162.48);
            --warning: oklch(0.769 0.188 70.08);
            --danger: oklch(0.704 0.191 22.216);
            --info: oklch(0.488 0.243 264.376);
            --radius: 0.625rem;
        }

        .split-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .cardx {
            background: var(--card);
            color: var(--card-foreground);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: 0 2px 4px 0 rgb(0 0 0 / 0.25);
            overflow: hidden;
            transition: box-shadow 250ms ease, transform 250ms ease;
        }

        .cardx:hover {
            box-shadow: 0 6px 12px -1px rgb(0 0 0 / 0.35);
        }

        .cardx-hd {
            padding: 14px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--secondary);
        }

        .cardx-body {
            padding: 20px;
        }

        .badgex {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 12px;
        }

        .badgex-success {
            background: color-mix(in oklch, var(--success) 20%, transparent 80%);
            color: var(--success);
            border: 1px solid var(--success);
        }

        .badgex-warning {
            background: color-mix(in oklch, var(--warning) 20%, transparent 80%);
            color: var(--warning);
            border: 1px solid var(--warning);
        }

        .badgex-danger {
            background: color-mix(in oklch, var(--danger) 20%, transparent 80%);
            color: var(--danger);
            border: 1px solid var(--danger);
        }

        .badgex-info {
            background: color-mix(in oklch, var(--info) 20%, transparent 80%);
            color: var(--info);
            border: 1px solid var(--info);
        }

        .tablex {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .tablex th {
            background: var(--secondary);
            color: var(--muted-foreground);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            font-weight: 900;
            padding: 10px 12px;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 2;
        }

        .tablex td {
            padding: 10px 12px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .tablex tr:hover {
            background: color-mix(in oklch, var(--primary) 10%, transparent 90%);
        }

        .inputx {
            width: 100%;
            background: var(--input);
            border: 1px solid var(--border);
            color: var(--foreground);
            border-radius: calc(var(--radius) - 4px);
            padding: 8px 12px;
            outline: none;
            transition: box-shadow 150ms ease, border-color 150ms ease;
        }

        .inputx:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px color-mix(in oklch, var(--primary) 20%, transparent 80%);
        }

        .inputx-sm {
            width: 80px;
            padding: 4px 8px;
            font-size: 13px;
        }

        .btnx {
            border: 1px solid transparent;
            padding: 8px 16px;
            border-radius: calc(var(--radius) - 4px);
            font-weight: 700;
            cursor: pointer;
            transition: all 150ms ease;
            font-size: 13px;
        }

        .btnx-primary {
            background: var(--primary);
            color: #fff;
        }

        .btnx-primary:hover {
            background: color-mix(in oklch, var(--primary) 80%, transparent 20%);
            transform: translateY(-1px);
        }

        .btnx-success {
            background: var(--success);
            color: #fff;
        }

        .btnx-success:hover {
            background: color-mix(in oklch, var(--success) 80%, transparent 20%);
            transform: translateY(-1px);
        }

        .btnx-danger {
            background: var(--danger);
            color: #fff;
        }

        .btnx-danger:hover {
            background: color-mix(in oklch, var(--danger) 80%, transparent 20%);
            transform: translateY(-1px);
        }

        .btnx-secondary {
            background: var(--secondary);
            color: var(--foreground);
            border: 1px solid var(--border);
        }

        .btnx-secondary:hover {
            background: color-mix(in oklch, var(--secondary) 80%, transparent 20%);
        }

        .btnx:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-top: 16px;
        }

        .summary-item {
            padding: 12px 16px;
            background: var(--input);
            border-radius: var(--radius);
            border: 1px solid var(--border);
        }

        .summary-item .label {
            font-size: 11px;
            color: var(--muted-foreground);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            font-weight: 700;
        }

        .summary-item .value {
            font-size: 20px;
            font-weight: 900;
            margin-top: 4px;
        }

        .summary-item .value.text-success {
            color: var(--success);
        }

        .summary-item .value.text-danger {
            color: var(--danger);
        }

        .summary-item .value.text-warning {
            color: var(--warning);
        }

        .spin {
            display: inline-block;
            width: 14px;
            height: 14px;
            border-radius: 999px;
            border: 2px solid var(--border);
            border-top-color: var(--primary);
            animation: sp 800ms linear infinite;
            vertical-align: -2px;
            margin-right: 6px;
        }

        @keyframes sp {
            to { transform: rotate(360deg); }
        }

        .alertx {
            padding: 12px 16px;
            border-radius: var(--radius);
            margin-bottom: 16px;
            border: 1px solid var(--border);
        }

        .alertx-info {
            background: color-mix(in oklch, var(--info) 15%, transparent 85%);
            border-color: var(--info);
            color: var(--info);
        }

        .alertx-warning {
            background: color-mix(in oklch, var(--warning) 15%, transparent 85%);
            border-color: var(--warning);
            color: var(--warning);
        }

        .alertx-success {
            background: color-mix(in oklch, var(--success) 15%, transparent 85%);
            border-color: var(--success);
            color: var(--success);
        }

        .toast-stack {
            position: fixed;
            right: 16px;
            top: 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 12000;
            width: min(420px, calc(100vw - 32px));
        }

        .toastx {
            border: 1px solid var(--border);
            background: var(--card);
            color: var(--foreground);
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.4);
            overflow: hidden;
            transform: translateY(-10px);
            opacity: 0;
            animation: toastIn 180ms ease forwards;
        }

        @keyframes toastIn {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .toastx[data-type="success"] {
            border-color: var(--success);
        }
        .toastx[data-type="error"] {
            border-color: var(--danger);
        }
        .toastx[data-type="warning"] {
            border-color: var(--warning);
        }

        .toastx-body {
            padding: 12px 16px;
            font-size: 13px;
        }

        .checkbox-custom {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--primary);
        }

        .selected-row {
            background: color-mix(in oklch, var(--primary) 15%, transparent 85%) !important;
        }

        .item-count {
            font-size: 12px;
            color: var(--muted-foreground);
            margin-left: 8px;
        }

        @media (max-width: 768px) {
            .tablex {
                font-size: 12px;
            }
            .tablex th,
            .tablex td {
                padding: 6px 8px;
            }
            .summary-grid {
                grid-template-columns: 1fr 1fr;
            }
            .inputx-sm {
                width: 60px;
            }
        }
    </style>

    <div class="split-container">
        <div class="toast-stack" id="toastStack"></div>

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div>
                <h3 class="mb-0" style="color: var(--foreground);">
                    <i class="fas fa-code-branch" style="color: var(--primary);"></i>
                    Split Order
                </h3>
                <small class="text-muted">Create a child order from selected items</small>
            </div>
            <div>
                <span class="badgex badgex-info">
                    Order #{{ $order->order_no }}
                </span>
                <span class="badgex {{ $order->status === 'completed' ? 'badgex-success' : 'badgex-warning' }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="cardx mb-4">
            <div class="cardx-hd">
                <span class="fw-bold">Order Summary</span>
                <small class="text-muted">{{ $order->created_at->format('Y-m-d H:i') }}</small>
            </div>
            <div class="cardx-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <small class="text-muted d-block">Customer</small>
                        <strong>{{ $order->customer?->name ?? 'Guest' }}</strong>
                        <small class="text-muted d-block">{{ $order->customer?->phone ?? '-' }}</small>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Location</small>
                        <strong>{{ $order->location?->name ?? 'N/A' }}</strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Total Amount</small>
                        <strong style="font-size: 18px; color: var(--primary);">
                            {{ currency_bdt($order->payable_total) }}
                        </strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Items Available</small>
                        <strong>{{ $availableItems->count() }} items</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Split Form -->
        <form id="splitForm" action="{{ route('orders.split.execute', $order) }}" method="POST">
            @csrf

            <div class="cardx mb-4">
                <div class="cardx-hd">
                    <span class="fw-bold">Select Items to Split</span>
                    <div class="d-flex gap-2 align-items-center">
                        <button type="button" class="btnx btnx-secondary btnx-sm" id="selectAllBtn">
                            <i class="fas fa-check-double"></i> Select All
                        </button>
                        <button type="button" class="btnx btnx-secondary btnx-sm" id="deselectAllBtn">
                            <i class="fas fa-times"></i> Deselect All
                        </button>
                        <span class="item-count" id="selectedCount">0 selected</span>
                    </div>
                </div>
                <div class="cardx-body p-0">
                    <div style="overflow-x: auto;">
                        <table class="tablex">
                            <thead>
                                <tr>
                                    <th style="width:40px;">
                                        <input type="checkbox" id="masterCheckbox" class="checkbox-custom">
                                    </th>
                                    <th style="min-width:180px;">Product</th>
                                    <th style="min-width:120px;">Barcode</th>
                                    <th style="min-width:100px;" class="text-right">Unit Price</th>
                                    <th style="min-width:80px;" class="text-right">Available</th>
                                    <th style="min-width:120px;" class="text-right">Qty to Split</th>
                                    <th style="min-width:120px;" class="text-end text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                @forelse($availableItems as $item)
                                    @php
                                        $available = $item->quantity - ($item->returned_qty ?? 0);
                                        $itemTotal = $item->unit_price * $available;
                                    @endphp
                                    <tr data-item-id="{{ $item->id }}" class="item-row">
                                        <td>
                                            <input type="checkbox" class="checkbox-custom item-checkbox"
                                                   data-id="{{ $item->id }}"
                                                   data-price="{{ $item->unit_price }}"
                                                   data-max="{{ $available }}"
                                                   data-name="{{ $item->product_name }}">
                                        </td>
                                        <td>
                                            <strong>{{ $item->product_name }}</strong>
                                            <div class="text-muted small">
                                                {{ $item->price_type ?? 'retail' }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badgex badgex-info" style="font-size:11px;">
                                                {{ $item->barcode ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <strong>{{ currency_bdt($item->unit_price) }}</strong>
                                            <div class="text-muted small">per {{ $item->unit ?? 'pcs' }}</div>
                                        </td>
                                        <td class="text-right">
                                            <span class="badgex {{ $available > 5 ? 'badgex-success' : 'badgex-warning' }}">
                                                {{ number_format($available, 3) }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <input type="number"
                                                   name="items[{{ $item->id }}][quantity]"
                                                   class="inputx inputx-sm qty-input"
                                                   min="0.0001"
                                                   step="0.0001"
                                                   value="{{ $available }}"
                                                   data-id="{{ $item->id }}"
                                                   data-max="{{ $available }}"
                                                   style="text-align: right;"
                                                   disabled>
                                            <input type="hidden" name="items[{{ $item->id }}][id]"
                                                   value="{{ $item->id }}"
                                                   class="item-id-input" data-id="{{ $item->id }}"
                                                   disabled>
                                            <div class="text-muted small mt-1" id="qtyMsg-{{ $item->id }}"></div>
                                        </td>
                                        <td class="text-end text-right">
                                            <span class="item-subtotal" id="subtotal-{{ $item->id }}">
                                                {{ currency_bdt($itemTotal) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="fas fa-box-open fa-2x d-block mb-2"></i>
                                            No items available to split
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Split Details -->
            <div class="cardx mb-4">
                <div class="cardx-hd">
                    <span class="fw-bold">Split Details</span>
                </div>
                <div class="cardx-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="text-muted small fw-bold d-block mb-1">Split Reason</label>
                            <input type="text" name="split_reason" class="inputx"
                                   placeholder="e.g., Partial shipment, Payment split"
                                   value="{{ old('split_reason') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small fw-bold d-block mb-1">Split Type</label>
                            <select name="split_type" class="inputx">
                                <option value="manual">Manual Split</option>
                                <option value="partial_payment">Partial Payment</option>
                                <option value="partial_fulfillment">Partial Fulfillment</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small fw-bold d-block mb-1">Split Notes</label>
                            <input type="text" name="split_notes" class="inputx"
                                   placeholder="Additional notes" value="{{ old('split_notes') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div class="cardx mb-4">
                <div class="cardx-hd">
                    <span class="fw-bold">Split Summary</span>
                </div>
                <div class="cardx-body">
                    <div class="summary-grid">
                        <div class="summary-item">
                            <div class="label">Original Order Total</div>
                            <div class="value text-success" id="originalTotal">
                                {{ currency_bdt($order->payable_total) }}
                            </div>
                        </div>
                        <div class="summary-item">
                            <div class="label">Amount to Split</div>
                            <div class="value text-warning" id="splitTotal">
                                {{ currency_bdt(0) }}
                            </div>
                        </div>
                        <div class="summary-item">
                            <div class="label">Remaining Amount</div>
                            <div class="value text-danger" id="remainingTotal">
                                {{ currency_bdt($order->payable_total) }}
                            </div>
                        </div>
                        <div class="summary-item">
                            <div class="label">Items Selected</div>
                            <div class="value" id="selectedItemsCount">0</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex gap-3 flex-wrap">
                <button type="button" class="btnx btnx-primary" id="previewBtn" disabled>
                    <i class="fas fa-eye"></i> Preview Split
                </button>
                <button type="submit" class="btnx btnx-success" id="executeBtn" disabled>
                    <i class="fas fa-check"></i> Execute Split
                </button>
                <a href="{{ route('orders.show', $order) }}" class="btnx btnx-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Preview Modal -->
    <div class="modalwrap" id="previewModal" style="display:none;">
        <div class="overlay"></div>
        <div class="modalx">
            <div class="cardx">
                <div class="cardx-hd">
                    <span class="fw-bold">Split Preview</span>
                    <button type="button" class="btnx btnx-secondary btnx-sm" id="closePreviewBtn">✕ Close</button>
                </div>
                <div class="cardx-body" id="previewContent">
                    <div class="text-center py-4">
                        <div class="spin"></div> Loading preview...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const masterCheckbox = document.getElementById('masterCheckbox');
    const checkboxes = document.querySelectorAll('.item-checkbox');
    const qtyInputs = document.querySelectorAll('.qty-input');
    const previewBtn = document.getElementById('previewBtn');
    const executeBtn = document.getElementById('executeBtn');
    const splitTotal = document.getElementById('splitTotal');
    const remainingTotal = document.getElementById('remainingTotal');
    const selectedItemsCount = document.getElementById('selectedItemsCount');
    const originalTotal = parseFloat('{{ $order->payable_total }}');
    const previewModal = document.getElementById('previewModal');
    const closePreviewBtn = document.getElementById('closePreviewBtn');
    const previewContent = document.getElementById('previewContent');

    function toast(message, type = 'info') {
        const stack = document.getElementById('toastStack');
        const toast = document.createElement('div');
        toast.className = 'toastx';
        toast.dataset.type = type;
        toast.innerHTML = `
            <div class="toastx-body">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                ${message}
            </div>
        `;
        stack.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }

    function updateSummary() {
        let total = 0;
        let count = 0;

        checkboxes.forEach(cb => {
            if (cb.checked) {
                const price = parseFloat(cb.dataset.price);
                const qtyInput = document.querySelector(`.qty-input[data-id="${cb.dataset.id}"]`);
                const qty = parseFloat(qtyInput?.value) || 0;
                const subtotal = price * qty;
                total += subtotal;
                count++;

                // Update subtotal display
                const subtotalEl = document.getElementById(`subtotal-${cb.dataset.id}`);
                if (subtotalEl) {
                    subtotalEl.textContent = '৳ ' + subtotal.toFixed(2);
                }
            } else {
                const subtotalEl = document.getElementById(`subtotal-${cb.dataset.id}`);
                const qtyInput = document.querySelector(`.qty-input[data-id="${cb.dataset.id}"]`);
                const max = parseFloat(cb.dataset.max);
                if (subtotalEl) {
                    const price = parseFloat(cb.dataset.price);
                    subtotalEl.textContent = '৳ ' + (price * max).toFixed(2);
                }
            }
        });

        const remaining = originalTotal - total;
        splitTotal.textContent = '৳ ' + total.toFixed(2);
        remainingTotal.textContent = '৳ ' + remaining.toFixed(2);
        selectedItemsCount.textContent = count;

        // Enable/disable buttons
        previewBtn.disabled = total <= 0;
        executeBtn.disabled = total <= 0;

        // Update row styling
        document.querySelectorAll('.item-row').forEach(row => {
            const cb = row.querySelector('.item-checkbox');
            row.classList.toggle('selected-row', cb.checked);
        });
    }

    // Master checkbox
    masterCheckbox.addEventListener('change', function() {
        checkboxes.forEach(cb => {
            cb.checked = this.checked;
            const qtyInput = document.querySelector(`.qty-input[data-id="${cb.dataset.id}"]`);
            const idInput = document.querySelector(`.item-id-input[data-id="${cb.dataset.id}"]`);
            qtyInput.disabled = !this.checked;
            // Keep the hidden "id" field's disabled state in lockstep with the
            // qty input: unchecked items must not submit *any* items[id][...]
            // entry, otherwise the backend validator sees an item with an id
            // but no quantity and rejects the whole split (this is what broke
            // every partial split -- selecting some but not all items).
            if (idInput) idInput.disabled = !this.checked;
            if (this.checked) {
                qtyInput.value = cb.dataset.max;
            } else {
                qtyInput.value = 0;
            }
        });
        updateSummary();
    });

    // Individual checkbox
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const qtyInput = document.querySelector(`.qty-input[data-id="${this.dataset.id}"]`);
            const idInput = document.querySelector(`.item-id-input[data-id="${this.dataset.id}"]`);
            qtyInput.disabled = !this.checked;
            if (idInput) idInput.disabled = !this.checked;
            if (this.checked) {
                qtyInput.value = this.dataset.max;
            } else {
                qtyInput.value = 0;
                document.getElementById(`qtyMsg-${this.dataset.id}`).textContent = '';
            }
            updateSummary();
            updateMasterCheckbox();
        });
    });

    // Quantity input changes
    qtyInputs.forEach(input => {
        input.addEventListener('input', function() {
            const max = parseFloat(this.dataset.max);
            const val = parseFloat(this.value) || 0;
            const id = this.dataset.id;
            const msgEl = document.getElementById(`qtyMsg-${id}`);

            if (val > max) {
                this.value = max;
                msgEl.textContent = '⚠️ Max: ' + max;
                msgEl.style.color = 'var(--danger)';
            } else if (val <= 0) {
                this.value = 0.0001;
                msgEl.textContent = '⚠️ Min: 0.0001';
                msgEl.style.color = 'var(--warning)';
            } else {
                msgEl.textContent = '';
            }

            updateSummary();
        });

        input.addEventListener('blur', function() {
            if (parseFloat(this.value) <= 0) {
                this.value = 0.0001;
            }
            updateSummary();
        });
    });

    function updateMasterCheckbox() {
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        const someChecked = Array.from(checkboxes).some(cb => cb.checked);
        masterCheckbox.checked = allChecked;
        masterCheckbox.indeterminate = !allChecked && someChecked;
    }

    // Select All / Deselect All
    document.getElementById('selectAllBtn').addEventListener('click', function() {
        masterCheckbox.checked = true;
        masterCheckbox.dispatchEvent(new Event('change'));
    });

    document.getElementById('deselectAllBtn').addEventListener('click', function() {
        masterCheckbox.checked = false;
        masterCheckbox.dispatchEvent(new Event('change'));
    });

    // Preview Split
    previewBtn.addEventListener('click', async function() {
        const form = document.getElementById('splitForm');
        const formData = new FormData(form);

        previewModal.style.display = 'flex';
        previewContent.innerHTML = `
            <div class="text-center py-4">
                <div class="spin"></div> Loading preview...
            </div>
        `;

        try {
            const response = await fetch('{{ route('orders.split.preview', $order) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (!data.success) {
                toast(data.message || 'Error loading preview', 'error');
                previewModal.style.display = 'none';
                return;
            }

            previewContent.innerHTML = data.html;

            // Show execute button in preview
            const executeInPreview = previewContent.querySelector('#executeSplitBtn');
            if (executeInPreview) {
                executeInPreview.addEventListener('click', function() {
                    document.getElementById('splitForm').submit();
                });
            }

        } catch (error) {
            toast('Error loading preview', 'error');
            previewModal.style.display = 'none';
        }
    });

    // Close preview
    closePreviewBtn.addEventListener('click', function() {
        previewModal.style.display = 'none';
    });

    previewModal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
        }
    });

    // Guard against a double-click/double-submit executing the split twice.
    let splitSubmitting = false;
    document.getElementById('splitForm').addEventListener('submit', function(e) {
        if (splitSubmitting) {
            e.preventDefault();
            return;
        }
        splitSubmitting = true;
        executeBtn.disabled = true;
    });

    // Initial update
    updateSummary();

    // Toast on load if any items
    const totalItems = {{ $availableItems->count() }};
    if (totalItems > 0) {
        toast(`Found ${totalItems} items available to split. Select items to continue.`, 'info');
    }
});
</script>
@endsection
