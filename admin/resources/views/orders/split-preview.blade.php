{{-- resources/views/orders/split-preview.blade.php --}}
@php
    if (!function_exists('currency_bdt')) {
        function currency_bdt($amount) {
            return '৳ ' . number_format((float) $amount, 2);
        }
    }
@endphp

<div class="split-preview" data-reveal>
    <div class="alertx alertx-warning" data-reveal>
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Confirm Split:</strong> You are about to create a child order with the selected items.
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="summary-item">
                <div class="label">Original Order</div>
                <div class="value" style="font-size:16px;">
                    #{{ $order->order_no }}
                    <small class="text-muted" style="font-size:12px;">
                        {{ currency_bdt($order->payable_total) }}
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-item">
                <div class="label">Amount to Split</div>
                <div class="value text-warning" style="font-size:18px;">
                    {{ currency_bdt($totalAmount) }}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-item">
                <div class="label">Remaining Amount</div>
                <div class="value text-danger" style="font-size:18px;">
                    {{ currency_bdt($remainingTotal) }}
                </div>
            </div>
        </div>
    </div>

    <h6 class="mb-3">Items to Move to Child Order</h6>
    <div style="overflow-x: auto; max-height: 400px; overflow-y: auto;">
        <table class="tablex">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Barcode</th>
                    <th class="text-right">Qty to Move</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-end text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($splitItems as $item)
                    <tr>
                        <td>
                            <strong>{{ $item['product_name'] }}</strong>
                            <div class="text-muted small">{{ $item['price_type'] ?? 'retail' }}</div>
                        </td>
                        <td>
                            <span class="badgex badgex-info" style="font-size:11px;">
                                {{ $item['barcode'] ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="text-right">
                            <span class="fw-bold" style="font-weight: 600;">{{ number_format($item['quantity'], 3) }}</span>
                            <div class="text-muted small">Available: {{ number_format($item['available_quantity'], 3) }}</div>
                        </td>
                        <td class="text-right">{{ currency_bdt($item['unit_price']) }}</td>
                        <td class="text-end text-right fw-bold" style="font-weight: 600;">{{ currency_bdt($item['total_price']) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="border-top: 2px solid var(--border);">
                    <th colspan="4" class="text-end text-right">Total to Split:</th>
                    <th class="text-end text-right" style="font-size:18px; color: var(--warning);">
                        {{ currency_bdt($totalAmount) }}
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="d-flex flex-wrap gap-3 mt-4">
        <button type="button" class="btnx btnx-success" id="executeSplitBtn">
            <i class="fas fa-check"></i> Confirm Split
        </button>
        <button type="button" class="btnx btnx-secondary" id="closePreviewBtn">
            <i class="fas fa-edit"></i> Edit Selection
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const closePreviewBtn = document.querySelector('#closePreviewBtn');
    if (closePreviewBtn) {
        closePreviewBtn.addEventListener('click', function() {
            document.getElementById('previewModal').style.display = 'none';
        });
    }
});
</script>
