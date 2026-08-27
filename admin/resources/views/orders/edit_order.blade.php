@extends('layouts.app')

@section('content')
<div class="container py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3>Edit Order #{{ $order->order_no ?? $order->id }}</h3>
            <span class="badge bg-{{ $order->status === 'pending' ? 'warning' : ($order->status === 'processing' ? 'info' : 'danger') }}">
                {{ ucfirst($order->status) }}
            </span>
        </div>
        <div>
            <a href="{{ route('orders.edit.cancel', $order->id) }}"
               class="btn btn-secondary"
               onclick="return confirm('Cancel editing? Changes will be lost.')">
                Cancel
            </a>
            <button class="btn btn-primary" id="saveOrderBtn">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
    </div>

    <!-- Customer Info -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>Customer:</strong> {{ $order->customer->name ?? 'Guest' }}
                </div>
                <div class="col-md-6">
                    <strong>Phone:</strong> {{ $order->customer->phone ?? '-' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Items -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Cart Items</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Batch</th>
                            <th>Unit</th>
                            <th class="text-right">Price</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="cartItems">
                        @forelse($cart->items as $index => $item)
                            <tr data-item-id="{{ $item->id }}" data-batch-id="{{ $item->batch_id }}">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{ $item->product->name ?? 'Unknown' }}
                                    @if($item->is_gift)
                                        <span class="badge bg-success">GIFT</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $item->batch->batch_sku ?? 'N/A' }}
                                    <small class="d-block text-muted">{{ $item->batch_unit ?? 'pcs' }}</small>
                                </td>
                                <td>
                                    <select class="form-select form-select-sm unit-select" data-item-id="{{ $item->id }}">
                                        <option value="pcs" {{ $item->unit === 'pcs' ? 'selected' : '' }}>pcs</option>
                                        <option value="dozen" {{ $item->unit === 'dozen' ? 'selected' : '' }}>dozen</option>
                                        <option value="box" {{ $item->unit === 'box' ? 'selected' : '' }}>box</option>
                                        <option value="kg" {{ $item->unit === 'kg' ? 'selected' : '' }}>kg</option>
                                        <option value="g" {{ $item->unit === 'g' ? 'selected' : '' }}>g</option>
                                    </select>
                                </td>
                                <td class="unit-price text-right">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-right">
                                    <input type="number"
                                           class="form-control form-control-sm qty-input"
                                           value="{{ $item->quantity }}"
                                           min="0.0001"
                                           step="0.0001"
                                           data-item-id="{{ $item->id }}"
                                           style="width: 80px; text-align: right;">
                                </td>
                                <td class="item-total text-right">{{ number_format($item->total_price, 2) }}</td>
                                <td>
                                    <button class="btn btn-sm btn-danger remove-item" data-item-id="{{ $item->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-3">No items in cart</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6" class="text-end text-right">Total:</th>
                            <th id="cartTotal" class="text-right">{{ number_format($cart->total ?? 0, 2) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Order Details -->
    <div class="card mt-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Discount Amount</label>
                    <input type="number" class="form-control" id="orderDiscount"
                           value="{{ $order->discount_amount ?? 0 }}" step="0.01" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Reward Points Used</label>
                    <input type="number" class="form-control" id="rewardPoints"
                           value="{{ $order->rewards_points_used ?? 0 }}" step="0.01" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Net Total</label>
                    <h4 id="netTotal">{{ number_format($order->total ?? 0, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const orderId = {{ $order->id }};
    const cartItems = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');
    const netTotal = document.getElementById('netTotal');

    // Update cart item
    async function updateItem(itemId, quantity, unit) {
        try {
            const response = await fetch('{{ route("cart.item.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    item_id: itemId,
                    quantity: quantity,
                    unit: unit
                })
            });

            const data = await response.json();
            if (data.success) {
                updateUI(data.cart);
            }
        } catch (error) {
            console.error('Update failed:', error);
        }
    }

    // Remove item
    async function removeItem(itemId) {
        if (!confirm('Remove this item?')) return;

        try {
            const response = await fetch(`/cart/item/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            if (data.success) {
                updateUI(data.cart);
            }
        } catch (error) {
            console.error('Remove failed:', error);
        }
    }

    // Update UI
    function updateUI(cart) {
        // Update totals
        cartTotal.textContent = Number(cart.total).toFixed(2);
        updateNetTotal();

        // Reload items
        if (cart.items && cart.items.length > 0) {
            // Simple refresh - reload page to keep it simple
            window.location.reload();
        } else {
            cartItems.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-3">Cart is empty</td>
                </tr>
            `;
        }
    }

    // Update net total
    function updateNetTotal() {
        const total = parseFloat(cartTotal.textContent) || 0;
        const discount = parseFloat(document.getElementById('orderDiscount').value) || 0;
        const points = parseFloat(document.getElementById('rewardPoints').value) || 0;
        const net = total - discount - points;
        netTotal.textContent = net.toFixed(2);
    }

    // Event listeners for qty
    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('change', function() {
            const itemId = this.dataset.itemId;
            const quantity = parseFloat(this.value) || 0.0001;
            const unitSelect = document.querySelector(`.unit-select[data-item-id="${itemId}"]`);
            const unit = unitSelect ? unitSelect.value : 'pcs';
            updateItem(itemId, quantity, unit);
        });
    });

    // Event listeners for unit
    document.querySelectorAll('.unit-select').forEach(select => {
        select.addEventListener('change', function() {
            const itemId = this.dataset.itemId;
            const qtyInput = document.querySelector(`.qty-input[data-item-id="${itemId}"]`);
            const quantity = parseFloat(qtyInput?.value) || 1;
            updateItem(itemId, quantity, this.value);
        });
    });

    // Remove item
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', function() {
            removeItem(this.dataset.itemId);
        });
    });

    // Update net total on discount/points change
    document.getElementById('orderDiscount').addEventListener('input', updateNetTotal);
    document.getElementById('rewardPoints').addEventListener('input', updateNetTotal);

    // Save order
    document.getElementById('saveOrderBtn').addEventListener('click', async function() {
        if (!confirm('Save changes to this order?')) return;

        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';

        try {
            const response = await fetch(`/orders/${orderId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    discount: document.getElementById('orderDiscount').value,
                    rewards_points: document.getElementById('rewardPoints').value
                })
            });

            const data = await response.json();

            if (data.success) {
                alert('Order updated successfully!');
                window.location.href = data.redirect;
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            alert('Failed to save order.');
        } finally {
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-save"></i> Save Changes';
        }
    });
});
</script>
@endsection
