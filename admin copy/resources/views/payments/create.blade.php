@extends('layouts.app')

@section('content')
<div class="container py-3">
  <div class="mb-3">
    <div class="text-muted">Payment</div>
    <h4 class="m-0">{{ $order->order_no }}</h4>

    <div class="mt-2 d-flex gap-2 flex-wrap">
      <span class="badge bg-secondary">Payable: {{ number_format($order->payable_total, 2) }}</span>
      <span class="badge bg-info">Paid: {{ number_format($order->paid_total ?? 0, 2) }}</span>
      <span class="badge bg-warning text-dark">Due: {{ number_format($order->due_total ?? $order->payable_total, 2) }}</span>
      <span class="badge bg-success">Status: {{ $order->payment_status ?? 'unpaid' }}</span>
    </div>
  </div>

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <strong>Split Payments</strong>
      <button class="btn btn-sm btn-outline-primary" id="addRowBtn" type="button">+ Add</button>
    </div>

    <div class="card-body">
      <div id="rows"></div>

      <div class="row mt-3">
        <div class="col-md-6">
          <label class="form-label">Note</label>
          <textarea class="form-control" id="note" rows="2"></textarea>
        </div>
        <div class="col-md-6">
          <div class="d-flex justify-content-end gap-4">
            <div>
              <div class="text-muted small">Total Paying</div>
              <div class="h5" id="sumPaying">0.00</div>
            </div>
            <div>
              <div class="text-muted small">Due/Change</div>
              <div class="h5" id="dueOrChange">0.00</div>
            </div>
          </div>
        </div>
      </div>

      <button class="btn btn-primary mt-3" id="saveBtn" type="button">Save Payment</button>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const payable = Number(@json((float)$order->payable_total));
  const methods = @json($methods);

  const rowsEl = document.getElementById('rows');
  const addRowBtn = document.getElementById('addRowBtn');
  const saveBtn = document.getElementById('saveBtn');

  const sumPayingEl = document.getElementById('sumPaying');
  const dueOrChangeEl = document.getElementById('dueOrChange');
  const noteEl = document.getElementById('note');

  function money(n){ return (Number(n||0)).toFixed(2); }

  function methodOptions(channel){
    return (methods[channel] || []).map(m => `<option value="${m}">${m.toUpperCase()}</option>`).join('');
  }

  function addRow(){
    const box = document.createElement('div');
    box.className = 'border rounded p-2 mb-2';

    box.innerHTML = `
      <div class="row g-2 align-items-end">
        <div class="col-md-2">
          <label class="form-label">Channel</label>
          <select class="form-select channel">
            <option value="offline" selected>Offline</option>
            <option value="online">Online</option>
          </select>
        </div>

        <div class="col-md-2">
          <label class="form-label">Method</label>
          <select class="form-select method">
            ${methodOptions('offline')}
          </select>
        </div>

        <div class="col-md-3 trxWrap" style="display:none;">
          <label class="form-label">Trx ID</label>
          <input class="form-control trx" placeholder="bKash/Nagad Trx ID">
        </div>

        <div class="col-md-3">
          <label class="form-label">Account/Ref</label>
          <input class="form-control account" placeholder="Cash / last4 / bank name">
        </div>

        <div class="col-md-2">
          <label class="form-label">Amount</label>
          <input class="form-control amount" type="number" min="0.01" step="0.01" value="0.00">
        </div>

        <div class="col-md-12 d-flex justify-content-end">
          <button class="btn btn-sm btn-outline-danger removeBtn" type="button">Remove</button>
        </div>
      </div>
    `;

    const channelEl = box.querySelector('.channel');
    const methodEl = box.querySelector('.method');
    const trxWrap = box.querySelector('.trxWrap');
    const trxEl = box.querySelector('.trx');
    const amountEl = box.querySelector('.amount');
    const removeBtn = box.querySelector('.removeBtn');

    channelEl.addEventListener('change', () => {
      const ch = channelEl.value;
      methodEl.innerHTML = methodOptions(ch);

      if (ch === 'online') trxWrap.style.display = '';
      else { trxWrap.style.display = 'none'; trxEl.value = ''; }

      calc();
    });

    amountEl.addEventListener('input', calc);
    removeBtn.addEventListener('click', () => { box.remove(); calc(); });

    rowsEl.appendChild(box);
    calc();
  }

  function calc(){
    let sum = 0;
    rowsEl.querySelectorAll('.amount').forEach(a => sum += Number(a.value || 0));

    sumPayingEl.textContent = money(sum);

    const diff = sum - payable;
    dueOrChangeEl.textContent = money(Math.abs(diff)); // show number only
  }

  function collect(){
    const payments = [];
    rowsEl.querySelectorAll('.border').forEach(box => {
      payments.push({
        channel: box.querySelector('.channel').value,
        method: box.querySelector('.method').value,
        trx_id: (box.querySelector('.trx')?.value || '').trim() || null,
        account_label: (box.querySelector('.account').value || '').trim() || null,
        amount: Number(box.querySelector('.amount').value || 0),
      });
    });
    return payments.filter(p => p.amount > 0);
  }

  addRowBtn.addEventListener('click', addRow);
  addRow(); // first

  saveBtn.addEventListener('click', async () => {
    const payments = collect();
    if (!payments.length) return alert('Add at least one payment amount');

    for (const p of payments) {
      if (p.channel === 'online' && !p.trx_id) {
        return alert(`Trx ID required for online (${p.method})`);
      }
    }

    const res = await fetch(`{{ route('payments.store', $order->id) }}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        payments,
        payment_note: noteEl.value
      })
    });

    const data = await res.json().catch(() => ({}));
    if (!res.ok || !data.success) return alert(data.message || 'Failed');

    alert(`Saved! Status: ${data.order.payment_status}, Due: ${money(data.order.due_total)}, Change: ${money(data.order.change_total)}`);
  });
});
</script>
@endsection
