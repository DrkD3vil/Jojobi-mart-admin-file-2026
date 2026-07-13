@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

<style>

/* ===== UNIQUE PREFIX: ordx-* (no collision) ===== */
.ordx-wrap{max-width:100%;margin:0 auto;padding:18px;color:var(--foreground);}
.ordx-top{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:12px;}
.ordx-title{font-size:1.35rem;font-weight:900;display:flex;gap:10px;align-items:center;}
.ordx-sub{color:var(--muted-foreground);margin-top:4px;}
.ordx-theme{
  width:56px;height:28px;border-radius:14px;background:var(--muted);
  border:1px solid var(--border);position:relative;cursor:pointer;
}
.ordx-theme::before{
  content:'';width:22px;height:22px;border-radius:50%;
  background:var(--sidebar-primary);position:absolute;top:2px;left:2px;
  transition:transform var(--transition-normal);
}
html[data-theme='light'] .ordx-theme::before{transform:translateX(28px);}

.ordx-card{
  background:var(--card);border:1px solid var(--border);border-radius:var(--radius);
  box-shadow:var(--card-shadow);padding:16px;margin:14px 0;
}
.ordx-row{display:flex;gap:12px;flex-wrap:wrap;align-items:center;}
.ordx-col{flex:1;min-width:220px;}
.ordx-input{
  width:100%;background:var(--input);color:var(--foreground);
  border:1px solid var(--border);border-radius:var(--radius);
  padding:11px 12px;outline:none;
}
.ordx-input:focus{border-color:var(--sidebar-primary);box-shadow:0 0 0 4px rgb(0 0 0 / 0.1);}
.ordx-btn{
  border:0;border-radius:var(--radius);padding:11px 14px;cursor:pointer;
  display:inline-flex;gap:8px;align-items:center;font-weight:900;
}
.ordx-btn-primary{background:var(--sidebar-primary);color:var(--sidebar-primary-foreground);}
.ordx-btn-ghost{background:var(--muted);color:var(--foreground);border:1px solid var(--border);}
.ordx-btn-ghost:hover{border-color:var(--sidebar-primary);}
.ordx-pill{display:inline-block;padding:4px 10px;border-radius:999px;background:var(--muted);border:1px solid var(--border);color:var(--muted-foreground);font-size:.85rem;}

.ordx-tableWrap{overflow:auto;border-radius:var(--radius);border:1px solid var(--border);}
.ordx-table{width:100%;border-collapse:collapse;min-width:900px;}
.ordx-table th,.ordx-table td{padding:12px 12px;border-bottom:1px solid var(--border);text-align:left;vertical-align:middle;}
.ordx-table th{font-size:.85rem;text-transform:uppercase;letter-spacing:.04em;color:var(--muted-foreground);
  background:color-mix(in oklch, var(--card) 90%, var(--muted) 10%);
}
.ordx-tr{cursor:pointer;}
.ordx-tr:hover td{background:color-mix(in oklch, var(--card) 85%, var(--muted) 15%);}
.ordx-link{color:var(--foreground);font-weight:900;text-decoration:none;}
.ordx-link:hover{color:var(--sidebar-primary);text-decoration:underline;}
.ordx-mini{color:var(--muted-foreground);font-size:.9rem;}
.ordx-right{margin-left:auto;}

.ordx-loading{display:inline-block;width:18px;height:18px;border:2px solid var(--border);
  border-top-color:var(--sidebar-primary);border-radius:50%;animation:ordxSpin 1s linear infinite;
}
@keyframes ordxSpin{to{transform:rotate(360deg)}}
.ordx-empty{padding:16px;border:1px dashed var(--border);border-radius:var(--radius);color:var(--muted-foreground);text-align:center;}
</style>

<div class="ordx-wrap">
  

  <div class="ordx-card">
    <div class="ordx-row">
      <div class="ordx-col">
        <input class="ordx-input" id="ordxQ" placeholder="Search order no / customer name / customer phone / id..." />
        <div class="ordx-mini" style="margin-top:6px;">Type 2+ characters. Results update instantly.</div>
      </div>

      <div class="ordx-col" style="max-width:220px;">
        <input class="ordx-input" id="ordxMin" type="number" step="0.01" placeholder="Min total" />
      </div>
      <div class="ordx-col" style="max-width:220px;">
        <input class="ordx-input" id="ordxMax" type="number" step="0.01" placeholder="Max total" />
      </div>

      <button type="button" class="ordx-btn ordx-btn-ghost ordx-right" id="ordxClear">
        <i class="fas fa-eraser"></i> Clear
      </button>

      <span class="ordx-pill" id="ordxCount">
        {{ $orders->count() }} on this page
      </span>
    </div>
  </div>

  <div class="ordx-card">
    <div class="ordx-tableWrap">
      <table class="ordx-table">
        <thead>
          <tr>
            <th style="width:90px;">ID</th>
            <th>Order No</th>
            <th>Customer</th>
            <th style="width:140px;">Total</th>
            <th style="width:220px;">Date</th>
            <th style="width:120px;">Status</th>
          </tr>
        </thead>
        <tbody id="ordxBody">
          @foreach($orders as $order)
            <tr class="ordx-tr" data-href="{{ route('orders.show', $order) }}">
              <td><b>{{ $order->id }}</b></td>
              <td>
                <a class="ordx-link" href="{{ route('orders.show', $order) }}" onclick="event.stopPropagation()">
                  {{ $order->order_no ?? ('ORD-'.$order->id) }}
                </a>
              </td>
              <td>
                <div><b>{{ $order->customer?->name ?? 'Guest' }}</b></div>
                <div class="ordx-mini">{{ $order->customer?->phone ?? '' }}</div>
              </td>
              <td><b>{{ number_format($order->payable_total, 2) }}</b></td>
              <td class="ordx-mini">{{ $order->created_at }}</td>
              <td class="ordx-mini">{{ $order->status ?? '-' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="ordx-mini" style="margin-top:10px;" id="ordxMeta"></div>
  </div>




  {{-- Pagination --}}
            <div class="table-footer" id="paginationSection">
                @if ($orders->hasPages())
                    <div class="pagination-wrapper">
                        {{ $orders->onEachSide(1)->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>


</div>

<script>
(() => {
  

  // click row redirect
  function bindRowClicks(){
    document.querySelectorAll('.ordx-tr[data-href]').forEach(tr => {
      tr.addEventListener('click', () => window.location.href = tr.dataset.href);
    });
  }
  bindRowClicks();

  // ajax search
  const URL_AJAX = @json(route('orders.ajax.index'));
  const qEl = document.getElementById('ordxQ');
  const minEl = document.getElementById('ordxMin');
  const maxEl = document.getElementById('ordxMax');
  const bodyEl = document.getElementById('ordxBody');
  const countEl = document.getElementById('ordxCount');
  const metaEl = document.getElementById('ordxMeta');
  const clearBtn = document.getElementById('ordxClear');
  const paginateWrap = document.getElementById('ordxPaginateWrap');

  let lastController = null;

  const debounce = (fn, d=250) => { let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a), d);} };

  function esc(s){ return String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }
  function money(n){ return Number(n||0).toFixed(2); }

  async function getJSON(url){
    if(lastController) lastController.abort();
    lastController = new AbortController();

    const res = await fetch(url, {
      headers: {'Accept':'application/json'},
      signal: lastController.signal
    });
    if(!res.ok) throw new Error(await res.text());
    return res.json();
  }

  async function runSearch(pageUrl=null){
    const q = (qEl.value || '').trim();
    const min = (minEl.value || '').trim();
    const max = (maxEl.value || '').trim();

    // keep it fast: if query too short and no min/max, don't hit server
    if(q.length > 0 && q.length < 2 && !min && !max){
      metaEl.innerHTML = `<span class="ordx-mini">Type at least 2 characters...</span>`;
      return;
    }

    metaEl.innerHTML = `<span class="ordx-loading"></span> loading...`;

    const base = pageUrl ? new URL(pageUrl, window.location.origin) : new URL(URL_AJAX, window.location.origin);
    if(q) base.searchParams.set('q', q);
    if(min) base.searchParams.set('min_total', min);
    if(max) base.searchParams.set('max_total', max);

    try{
      const data = await getJSON(base.toString());

      // table rows
      bodyEl.innerHTML = (data.rows || []).map(o => `
        <tr class="ordx-tr" data-href="${esc(o.show_url)}">
          <td><b>${esc(o.id)}</b></td>
          <td>
            <a class="ordx-link" href="${esc(o.show_url)}" onclick="event.stopPropagation()">${esc(o.order_no || ('ORD-'+o.id))}</a>
          </td>
          <td>
            <div><b>${esc(o.customer_name || 'Guest')}</b></div>
            <div class="ordx-mini">${esc(o.customer_phone || '')}</div>
          </td>
          <td><b>${money(o.payable_total)}</b></td>
          <td class="ordx-mini">${esc(o.created_at)}</td>
          <td class="ordx-mini">${esc(o.status || '-')}</td>
        </tr>
      `).join('') || `
        <tr><td colspan="6"><div class="ordx-empty"><i class="fas fa-box-open"></i> No orders found.</div></td></tr>
      `;

      // pagination html (server sends rendered pagination)
      paginateWrap.innerHTML = data.pagination_html || '';

      // meta
      countEl.textContent = `${data.count_on_page || 0} on this page`;
      metaEl.textContent = data.meta || '';

      bindRowClicks();
      bindPaginationClicks();

    }catch(e){
      if(String(e).includes('AbortError')) return;
      console.error(e);
      metaEl.innerHTML = `<span style="color:var(--danger)">Search failed.</span>`;
    }
  }

  function bindPaginationClicks(){
    paginateWrap.querySelectorAll('a').forEach(a => {
      a.addEventListener('click', (e) => {
        e.preventDefault();
        runSearch(a.getAttribute('href'));
      });
    });
  }
  bindPaginationClicks();

  const run = debounce(() => runSearch(), 260);
  qEl.addEventListener('input', run);
  minEl.addEventListener('input', run);
  maxEl.addEventListener('input', run);

  clearBtn.addEventListener('click', () => {
    qEl.value = ''; minEl.value = ''; maxEl.value = '';
    runSearch();
  });
})();
</script>
@endsection
