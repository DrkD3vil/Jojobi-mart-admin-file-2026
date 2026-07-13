@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

<style>
/* =========================
   FIXED COLOR SYSTEM (YOURS)
   ========================= */
:root{
  --radius: 0.625rem;

  --transition-fast: 150ms;
  --transition-normal: 250ms;
  --transition-slow: 350ms;

  --background: oklch(0.145 0 0);
  --foreground: oklch(0.985 0 0);
  --card: oklch(0.205 0 0);
  --card-foreground: oklch(0.985 0 0);
  --popover: oklch(0.205 0 0);
  --popover-foreground: oklch(0.985 0 0);
  --primary: oklch(0.922 0 0);
  --primary-foreground: oklch(0.205 0 0);
  --secondary: oklch(0.269 0 0);
  --secondary-foreground: oklch(0.985 0 0);
  --muted: oklch(0.269 0 0);
  --muted-foreground: oklch(0.708 0 0);
  --accent: oklch(0.269 0 0);
  --accent-foreground: oklch(0.985 0 0);
  --destructive: oklch(0.704 0.191 22.216);
  --border: oklch(1 0 0 / 15%);
  --input: oklch(1 0 0 / 15%);
  --ring: oklch(0.556 0 0);

  --sidebar-primary: oklch(0.488 0.243 264.376);

  --success: oklch(0.696 0.17 162.48);
  --warning: oklch(0.769 0.188 70.08);
  --info: oklch(0.488 0.243 264.376);
  --danger: oklch(0.704 0.191 22.216);

  --card-shadow: 0 2px 4px 0 rgb(0 0 0 / 0.25);
  --card-shadow-hover: 0 6px 12px -1px rgb(0 0 0 / 0.35), 0 3px 6px -2px rgb(0 0 0 / 0.25);
  --dropdown-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.4), 0 8px 10px -6px rgb(0 0 0 / 0.3);

  --accent-color: var(--sidebar-primary);
  --accent-hover: oklch(0.488 0.243 264.376 / 0.8);
  --accent-glow: oklch(0.488 0.243 264.376 / 0.2);

  --bg-primary: var(--background);
  --bg-secondary: var(--card);
  --bg-tertiary: var(--secondary);
  --text-primary: var(--foreground);
  --text-secondary: var(--muted-foreground);
  --text-muted: oklch(0.708 0 0 / 0.7);
  --border-color: var(--border);
}

/* LIGHT MODE */
html[data-theme='light']{
  --background: oklch(0.99 0 0);
  --foreground: oklch(0.12 0 0);
  --card: oklch(1 0 0);
  --card-foreground: oklch(0.12 0 0);
  --popover: oklch(1 0 0);
  --popover-foreground: oklch(0.12 0 0);
  --primary: oklch(0.15 0 0);
  --primary-foreground: oklch(0.99 0 0);
  --secondary: oklch(0.97 0 0);
  --secondary-foreground: oklch(0.15 0 0);
  --muted: oklch(0.96 0 0);
  --muted-foreground: oklch(0.5 0 0);
  --accent: oklch(0.96 0 0);
  --accent-foreground: oklch(0.15 0 0);
  --destructive: oklch(0.577 0.245 27.325);
  --border: oklch(0.9 0 0);
  --input: oklch(0.96 0 0);
  --ring: oklch(0.65 0 0);

  --sidebar-primary: oklch(0.646 0.222 41.116);

  --success: oklch(0.627 0.194 149.214);
  --warning: oklch(0.769 0.188 70.08);
  --info: oklch(0.623 0.214 259.815);
  --danger: oklch(0.577 0.245 27.325);

  --card-shadow: 0 2px 4px 0 rgb(0 0 0 / 0.08);
  --card-shadow-hover: 0 6px 12px -1px rgb(0 0 0 / 0.12), 0 3px 6px -2px rgb(0 0 0 / 0.08);
  --dropdown-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.15), 0 8px 10px -6px rgb(0 0 0 / 0.1);

  --accent-color: var(--sidebar-primary);
  --accent-hover: oklch(0.646 0.222 41.116 / 0.8);
  --accent-glow: oklch(0.646 0.222 41.116 / 0.1);

  --bg-primary: var(--background);
  --bg-secondary: var(--card);
  --bg-tertiary: var(--secondary);
  --text-primary: var(--foreground);
  --text-secondary: var(--muted-foreground);
  --text-muted: oklch(0.5 0 0 / 0.7);
  --border-color: var(--border);
}

/* =========================
   ORDERS UI (unique: ords-)
   ========================= */
.ords-wrap{max-width:1200px;margin:0 auto;padding:16px;color:var(--foreground);}
.ords-top{display:flex;justify-content:space-between;gap:12px;align-items:flex-start;margin-bottom:16px;flex-wrap:wrap;}
.ords-title{font-size:1.5rem;font-weight:800;display:flex;align-items:center;gap:10px}
.ords-sub{color:var(--text-secondary);font-size:.95rem;margin-top:6px;}
.ords-row{display:flex;gap:10px;align-items:center;flex-wrap:wrap;}
.ords-card{background:var(--card);border:1px solid var(--border-color);border-radius:var(--radius);padding:16px;box-shadow:var(--card-shadow);margin-bottom:16px;}
.ords-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;}
@media(max-width: 900px){.ords-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width: 520px){.ords-grid{grid-template-columns:1fr}}
.ords-kv{background:color-mix(in oklch, var(--card) 70%, var(--bg-tertiary) 30%);border:1px solid var(--border-color);border-radius:calc(var(--radius) - 2px);padding:12px;}
.ords-k{color:var(--text-secondary);font-size:.85rem;margin-bottom:6px;}
.ords-v{font-weight:800;}
.ords-mini{color:var(--text-secondary);font-size:.85rem;margin-top:4px;}
.ords-pill{display:inline-flex;align-items:center;gap:6px;border:1px solid var(--border-color);background:var(--bg-tertiary);padding:6px 10px;border-radius:999px;font-size:.85rem;}
.ords-btn{border:1px solid var(--border-color);padding:10px 12px;border-radius:calc(var(--radius) - 2px);cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all var(--transition-fast);}
.ords-btn-primary{background:var(--accent-color);border-color:transparent;color:white;}
.ords-btn-primary:hover{background:var(--accent-hover);box-shadow:0 8px 20px -8px var(--accent-glow);}
.ords-btn-ghost{background:transparent;color:var(--foreground);}
.ords-btn-ghost:hover{background:var(--accent);border-color:var(--accent-color);}
.ords-input{width:100%;padding:10px 12px;border-radius:calc(var(--radius) - 2px);border:1px solid var(--border-color);background:var(--input);color:var(--foreground);outline:none;}
.ords-input:focus{border-color:var(--accent-color);box-shadow:0 0 0 3px var(--accent-glow);}
.ords-tableWrap{overflow:auto;border:1px solid var(--border-color);border-radius:var(--radius);}
.ords-table{width:100%;border-collapse:collapse;min-width:1200px;}
.ords-table thead{background:var(--bg-tertiary);}
.ords-table th,.ords-table td{padding:12px;border-bottom:1px solid var(--border-color);text-align:left;vertical-align:middle;}
.ords-table tbody tr:hover{background:var(--accent);}
.ords-link{color:var(--foreground);text-decoration:none;border-bottom:1px dashed color-mix(in oklch, var(--foreground) 25%, transparent 75%);}
.ords-link:hover{border-bottom-style:solid;}
.ords-badge{width:34px;height:34px;border-radius:12px;display:flex;align-items:center;justify-content:center;border:1px solid var(--border-color);}
.ords-badge.ok{background:color-mix(in oklch, var(--success) 35%, var(--card) 65%);}
.ords-badge.warn{background:color-mix(in oklch, var(--warning) 35%, var(--card) 65%);}
.ords-badge.bad{background:color-mix(in oklch, var(--danger) 35%, var(--card) 65%);}
.ords-tag{display:inline-flex;align-items:center;gap:6px;border:1px solid var(--border-color);background:color-mix(in oklch, var(--info) 22%, var(--card) 78%);padding:4px 10px;border-radius:999px;font-size:.8rem;}
</style>

@php
  // ✅ Main rule: NET uses returned_qty only (exchange updates returned_qty)
  $itemsCount = $order->items->count();
  $totalQty = (float)$order->items->sum(fn($it)=>(float)$it->quantity);
  $totalReturned = (float)$order->items->sum(fn($it)=>(float)($it->returned_qty ?? 0));
@endphp

<div class="ords-wrap">
  <div class="ords-top">
    <div>
      <div class="ords-title"><i class="fas fa-receipt"></i> Order {{ $order->order_no ?? ('ORD-'.$order->id) }}</div>
      <div class="ords-sub">Order details + items (net qty uses returned_qty only to avoid double-counting exchanges).</div>
    </div>

    <div class="ords-row">
      <a class="ords-btn ords-btn-ghost" href="{{ route('orders.index') }}">
        <i class="fas fa-arrow-left"></i> Back
      </a>

      <a class="ords-btn ords-btn-primary" href="{{ route('returns.wizard', ['order_id' => $order->id]) }}">
        <i class="fas fa-rotate-left"></i> Return / Exchange
      </a>
    </div>
  </div>

  <div class="ords-card">
    <div class="ords-grid">
      <div class="ords-kv">
        <div class="ords-k">Customer</div>
        <div class="ords-v">{{ $order->customer?->name ?? 'Guest' }}</div>
        <div class="ords-mini">{{ $order->customer?->phone ?? '' }}</div>
      </div>

      <div class="ords-kv">
        <div class="ords-k">Status</div>
        <div class="ords-v"><span class="ords-pill">{{ $order->status ?? '—' }}</span></div>
        <div class="ords-mini">Created: {{ $order->created_at }}</div>
      </div>

      <div class="ords-kv">
        <div class="ords-k">Totals</div>
        <div class="ords-v">Payable: {{ number_format((float)$order->payable_total, 2) }}</div>
        <div class="ords-mini">
          Subtotal: {{ number_format((float)$order->subtotal, 2) }}
          | Discount: {{ number_format((float)$order->discount_total, 2) }}
        </div>
      </div>

      <div class="ords-kv">
        <div class="ords-k">Items Summary</div>
        <div class="ords-v">{{ $itemsCount }} items</div>
        <div class="ords-mini">
          Qty: {{ number_format($totalQty, 4) }}
          | Returned (includes exchange): {{ number_format($totalReturned, 4) }}
        </div>
      </div>
    </div>
  </div>

  <div class="ords-card">
    <div class="ords-row" style="margin-bottom:10px;">
      <div style="flex:1;min-width:240px;">
        <input class="ords-input" id="ordsItemSearch" placeholder="Search item: name, barcode, product id, batch id, price type..." />
        <div class="ords-mini" style="margin-top:6px;">Filters only current page (fast).</div>
      </div>
      <div style="width:200px;">
        <input class="ords-input" id="ordsMinQty" type="number" step="0.0001" placeholder="Min qty" />
      </div>
      <div style="width:200px;">
        <input class="ords-input" id="ordsMaxQty" type="number" step="0.0001" placeholder="Max qty" />
      </div>
      <button type="button" class="ords-btn ords-btn-ghost" id="ordsClear">
        <i class="fas fa-eraser"></i> Clear
      </button>
      <span class="ords-pill" id="ordsCountPill">{{ $order->items->count() }} items</span>
    </div>

    <div class="ords-tableWrap">
      <table class="ords-table" id="ordsItemsTable">
        <thead>
          <tr>
            <th>Product</th>
            <th style="width:120px;">Product ID</th>
            <th style="width:120px;">Batch ID</th>
            <th style="width:160px;">Barcode</th>
            <th style="width:120px;">Price Type</th>

            <th style="width:120px;">Sold</th>
            <th style="width:120px;">Returned</th>
            <th style="width:130px;">Net Qty</th>

            <th style="width:120px;">Unit</th>
            <th style="width:120px;">Discount</th>
            <th style="width:140px;">Net Total</th>
          </tr>
        </thead>

        <tbody id="ordsItemsBody">
        @foreach($order->items as $item)
          @php
            $sold = (float)($item->quantity ?? 0);
            $returned = (float)($item->returned_qty ?? 0);

            // ✅ FIX: DO NOT subtract exchangeReturn here
            $net = max(0, $sold - $returned);

            $disc = (float)($item->discount_amount ?? 0);
            $netTotal = $net * (float)($item->unit_price ?? 0);

            $rowBadge = 'ok';
            if($returned > 0 && $net > 0) $rowBadge = 'warn';
            if($net <= 0 && $sold > 0) $rowBadge = 'bad';

            $isExchangeItem = strtolower((string)($item->price_type ?? '')) === 'exchange';
          @endphp

          <tr class="ords-tr"
            data-product="{{ strtolower($item->product_name ?? '') }}"
            data-barcode="{{ strtolower($item->barcode ?? '') }}"
            data-productid="{{ $item->product_id }}"
            data-batchid="{{ $item->product_batch_id }}"
            data-pricetype="{{ strtolower($item->price_type ?? '') }}"
            data-qty="{{ $sold }}"
          >
            <td>
              <div style="display:flex;align-items:center;gap:10px;">
                <span class="ords-badge {{ $rowBadge }}"><i class="fas fa-box"></i></span>
                <div>
                  @if(Route::has('products.show'))
                    <a class="ords-link" href="{{ route('products.show', $item->product_id) }}">
                      {{ $item->product_name ?? ('Product #'.$item->product_id) }}
                    </a>
                  @else
                    <b>{{ $item->product_name ?? ('Product #'.$item->product_id) }}</b>
                  @endif

                  <div class="ords-mini">
                    Order Item ID: {{ $item->id }}
                    @if($isExchangeItem)
                      &nbsp;•&nbsp; <span class="ords-tag"><i class="fas fa-shuffle"></i> Exchange Issue</span>
                    @endif
                  </div>
                </div>
              </div>
            </td>

            <td><b>{{ $item->product_id }}</b></td>
            <td><b>{{ $item->product_batch_id }}</b></td>
            <td class="ords-mini">{{ $item->barcode ?? '—' }}</td>
            <td>{{ ucfirst($item->price_type ?? '-') }}</td>

            <td><b>{{ number_format($sold, 4) }}</b></td>
            <td class="ords-mini">{{ number_format($returned, 4) }}</td>
            <td><b>{{ number_format($net, 4) }}</b></td>

            <td>{{ number_format((float)($item->unit_price ?? 0), 2) }}</td>
            <td class="ords-mini">{{ number_format($disc, 2) }}</td>
            <td><b>{{ number_format($netTotal, 2) }}</b></td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
(() => {
  const searchEl = document.getElementById('ordsItemSearch');
  const minQtyEl = document.getElementById('ordsMinQty');
  const maxQtyEl = document.getElementById('ordsMaxQty');
  const clearBtn = document.getElementById('ordsClear');
  const pill = document.getElementById('ordsCountPill');
  const rows = Array.from(document.querySelectorAll('#ordsItemsBody tr'));

  const debounce = (fn, d=160) => { let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a), d);} };

  function apply(){
    const q = (searchEl.value || '').trim().toLowerCase();
    const minQty = minQtyEl.value === '' ? null : Number(minQtyEl.value);
    const maxQty = maxQtyEl.value === '' ? null : Number(maxQtyEl.value);

    let shown = 0;

    rows.forEach(tr => {
      const txt = [
        tr.dataset.product, tr.dataset.barcode, tr.dataset.pricetype,
        tr.dataset.productid, tr.dataset.batchid
      ].join(' ');
      const qty = Number(tr.dataset.qty || 0);

      const matchQ = !q || txt.includes(q);
      const matchMin = (minQty === null) || qty >= minQty;
      const matchMax = (maxQty === null) || qty <= maxQty;

      const ok = matchQ && matchMin && matchMax;
      tr.style.display = ok ? '' : 'none';
      if(ok) shown++;
    });

    pill.textContent = `${shown} items shown`;
  }

  const run = debounce(apply, 140);
  searchEl.addEventListener('input', run);
  minQtyEl.addEventListener('input', run);
  maxQtyEl.addEventListener('input', run);

  clearBtn.addEventListener('click', () => {
    searchEl.value = '';
    minQtyEl.value = '';
    maxQtyEl.value = '';
    apply();
  });

  apply();
})();
</script>

@endsection
