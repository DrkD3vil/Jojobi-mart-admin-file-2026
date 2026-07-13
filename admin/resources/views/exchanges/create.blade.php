@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

<style>
/* ===========================
   FIXED THEME COLORS (OKLCH)
   =========================== */
:root{
  --radius: 0.625rem;
  --transition-fast: 150ms;
  --transition-normal: 250ms;

  --background: oklch(0.145 0 0);
  --foreground: oklch(0.985 0 0);
  --card: oklch(0.205 0 0);
  --muted: oklch(0.269 0 0);
  --muted-foreground: oklch(0.708 0 0);
  --border: oklch(1 0 0 / 15%);
  --input: oklch(1 0 0 / 15%);

  --sidebar-primary: oklch(0.488 0.243 264.376);
  --sidebar-primary-foreground: oklch(0.985 0 0);

  --success: oklch(0.696 0.17 162.48);
  --warning: oklch(0.769 0.188 70.08);
  --danger: oklch(0.704 0.191 22.216);

  --card-shadow: 0 2px 4px 0 rgb(0 0 0 / 0.25);
  --card-shadow-hover: 0 6px 12px -1px rgb(0 0 0 / 0.35), 0 3px 6px -2px rgb(0 0 0 / 0.25);
  --dropdown-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.4), 0 8px 10px -6px rgb(0 0 0 / 0.3);

  --accent-color: var(--sidebar-primary);
  --accent-hover: oklch(0.488 0.243 264.376 / 0.85);
  --accent-glow: oklch(0.488 0.243 264.376 / 0.22);
}

html[data-theme='light']{
  --background: oklch(0.99 0 0);
  --foreground: oklch(0.12 0 0);
  --card: oklch(1 0 0);
  --muted: oklch(0.96 0 0);
  --muted-foreground: oklch(0.5 0 0);
  --border: oklch(0.9 0 0);
  --input: oklch(0.96 0 0);

  --sidebar-primary: oklch(0.646 0.222 41.116);
  --sidebar-primary-foreground: oklch(1 0 0);

  --success: oklch(0.627 0.194 149.214);
  --warning: oklch(0.769 0.188 70.08);
  --danger: oklch(0.577 0.245 27.325);

  --card-shadow: 0 2px 4px 0 rgb(0 0 0 / 0.08);
  --card-shadow-hover: 0 6px 12px -1px rgb(0 0 0 / 0.12), 0 3px 6px -2px rgb(0 0 0 / 0.08);
  --dropdown-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.15), 0 8px 10px -6px rgb(0 0 0 / 0.1);

  --accent-color: var(--sidebar-primary);
  --accent-hover: oklch(0.646 0.222 41.116 / 0.85);
  --accent-glow: oklch(0.646 0.222 41.116 / 0.12);
}

/* ===========================
   UNIQUE UI: exc-*
   =========================== */
.exc-wrap{max-width:1150px; margin:0 auto; padding:18px; color:var(--foreground);}
.exc-top{display:flex; justify-content:space-between; gap:12px; align-items:center; margin-bottom:12px;}
.exc-h{font-size:1.3rem; font-weight:900; display:flex; gap:10px; align-items:center;}
.exc-sub{color:var(--muted-foreground);}

.exc-theme{
  width:56px; height:28px; border-radius:14px;
  background:var(--muted); border:1px solid var(--border); position:relative; cursor:pointer;
}
.exc-theme::before{
  content:''; width:22px; height:22px; border-radius:50%;
  background:var(--accent-color); position:absolute; top:2px; left:2px;
  transition: transform var(--transition-normal);
}
html[data-theme='light'] .exc-theme::before{transform: translateX(28px);}

.exc-card{
  background:var(--card);
  border:1px solid var(--border);
  border-radius:var(--radius);
  box-shadow:var(--card-shadow);
  padding:18px;
  margin:14px 0;
}

.exc-row{display:flex; gap:12px; flex-wrap:wrap;}
.exc-col{flex:1; min-width:260px;}

.exc-label{display:block; margin:8px 0 6px; font-weight:800;}
.exc-input,.exc-select,.exc-textarea{
  width:100%;
  background:var(--input);
  color:var(--foreground);
  border:1px solid var(--border);
  border-radius:var(--radius);
  padding:11px 12px;
  outline:none;
}
.exc-textarea{min-height:70px; resize:vertical;}
.exc-mini{color:var(--muted-foreground); font-size:.9rem;}

.exc-btn{
  border:0; border-radius:var(--radius);
  padding:11px 14px; cursor:pointer;
  display:inline-flex; gap:8px; align-items:center;
  font-weight:800;
}
.exc-btn-primary{background:var(--accent-color); color:var(--sidebar-primary-foreground);}
.exc-btn-primary:hover{background:var(--accent-hover); box-shadow:0 6px 16px var(--accent-glow);}
.exc-btn-ghost{background:var(--muted); color:var(--foreground); border:1px solid var(--border);}
.exc-btn-ghost:hover{border-color:var(--accent-color);}

.exc-pill{
  display:inline-block; padding:4px 10px; border-radius:999px;
  background:var(--muted); border:1px solid var(--border);
  font-size:.85rem;
}

.exc-danger{color:var(--danger); font-weight:900;}
.exc-ok{background:var(--success); color:white;}
.exc-err{background:var(--danger); color:white;}
.exc-warn{background:var(--warning); color:white;}

.exc-dd{position:relative;}
.exc-ddbox{
  position:absolute; left:0; right:0; top:56px;
  background:var(--card); border:1px solid var(--border);
  border-radius:var(--radius);
  box-shadow:var(--dropdown-shadow);
  max-height:320px; overflow:auto;
  padding:8px; display:none; z-index:999;
}
.exc-ddbox.show{display:block;}
.exc-dditem{
  padding:10px; border-radius:calc(var(--radius) - 2px);
  cursor:pointer; display:flex; justify-content:space-between; gap:10px;
}
.exc-dditem:hover{background:var(--muted);}
.exc-ddL b{display:block; margin-bottom:2px;}
.exc-ddL small{color:var(--muted-foreground);}
.exc-ddR{font-weight:900; color:var(--accent-color);}

.exc-secTitle{
  display:flex; justify-content:space-between; gap:10px; align-items:center;
  margin-bottom:8px;
}
.exc-lines{display:flex; flex-direction:column; gap:12px;}
</style>

<div class="exc-wrap">
  <div class="exc-top">
    <div>
      <div class="exc-h"><i class="fas fa-rotate"></i> Exchange</div>
      <div class="exc-sub">Return IN + Issue OUT + Auto Price Difference</div>
    </div>
    <div class="exc-theme" id="excThemeToggle" title="Toggle theme"></div>
  </div>

  @if(session('ok'))
    <div class="exc-card exc-ok"><i class="fas fa-check-circle"></i> {{ session('ok') }}</div>
  @endif

  @if($errors->any())
    <div class="exc-card exc-err">
      <i class="fas fa-exclamation-triangle"></i>
      <div style="margin-top:6px;">
        <b>Errors:</b>
        <ul style="margin:8px 0 0 18px;">
          @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
      </div>
    </div>
  @endif

  <form method="POST" action="{{ route('exchanges.store') }}" id="excForm">
    @csrf

    {{-- Header / Config --}}
    <div class="exc-card">
      <div class="exc-row">
        <div class="exc-col exc-dd">
          <label class="exc-label">Order Search (order no / phone / name)</label>
          <input type="text" class="exc-input" id="excOrderSearch" placeholder="Type at least 2 chars...">
          <div class="exc-ddbox" id="excOrderDD"></div>
          <div class="exc-mini">Select an order to auto-load order items for return.</div>

          <input type="hidden" name="order_id" id="excOrderId" required>
          <div class="exc-mini" id="excOrderMeta" style="margin-top:6px;"></div>
        </div>

        <div class="exc-col">
          <label class="exc-label">Location (Return IN & Issue OUT)</label>
          <select name="location_id" id="excLoc" class="exc-select" required>
            <option value="">Select location</option>
            @foreach($locations as $l)
              <option value="{{ $l->id }}">{{ $l->name }}</option>
            @endforeach
          </select>
          <div class="exc-mini">Stock availability for ISSUE uses this location.</div>
        </div>
      </div>

      <label class="exc-label">Note</label>
      <textarea name="note" class="exc-textarea" placeholder="Optional note..."></textarea>

      <div class="exc-mini" id="excHint"></div>
    </div>

    {{-- RETURN LINES --}}
    <div class="exc-card">
      <div class="exc-secTitle">
        <div>
          <b><i class="fas fa-arrow-down"></i> Return Lines (from selected order)</b>
          <div class="exc-mini">Pick an order item and set qty (max returnable shown).</div>
        </div>
        <span class="exc-pill" id="excReturnCount">0 lines</span>
      </div>

      <div class="exc-lines" id="excReturnLines"></div>

      <div class="exc-row" style="margin-top:12px;">
        <button type="button" class="exc-btn exc-btn-ghost" id="excAddReturn"><i class="fas fa-plus"></i> Add return line</button>
        <button type="button" class="exc-btn exc-btn-ghost" id="excClearReturn"><i class="fas fa-eraser"></i> Clear</button>
      </div>
    </div>

    {{-- ISSUE LINES --}}
    <div class="exc-card">
      <div class="exc-secTitle">
        <div>
          <b><i class="fas fa-arrow-up"></i> Issue Lines (new items)</b>
          <div class="exc-mini">Search batch by name / product barcode / batch_no / batch_sku / batch id.</div>
        </div>
        <span class="exc-pill" id="excIssueCount">1 line</span>
      </div>

      <div class="exc-lines" id="excIssueLines"></div>

      <div class="exc-row" style="margin-top:12px;">
        <button type="button" class="exc-btn exc-btn-ghost" id="excAddIssue"><i class="fas fa-plus"></i> Add issue line</button>
        <button type="button" class="exc-btn exc-btn-ghost" id="excClearIssue"><i class="fas fa-eraser"></i> Clear</button>
      </div>
    </div>

    {{-- SUMMARY --}}
    <div class="exc-card">
      <div class="exc-secTitle">
        <div>
          <b><i class="fas fa-calculator"></i> Summary</b>
          <div class="exc-mini">Auto-calculated totals + price difference.</div>
        </div>
        <span class="exc-pill" id="excDiffPill">Diff: 0</span>
      </div>

      <div class="exc-row">
        <div class="exc-col">
          <label class="exc-label">Return Total</label>
          <input type="text" class="exc-input" id="excReturnTotal" readonly value="0">
        </div>
        <div class="exc-col">
          <label class="exc-label">Issue Total</label>
          <input type="text" class="exc-input" id="excIssueTotal" readonly value="0">
        </div>
      </div>

      <div class="exc-row" style="margin-top:12px;">
        <button type="submit" class="exc-btn exc-btn-primary" id="excSubmit" style="margin-left:auto;">
          <i class="fas fa-paper-plane"></i> Submit Exchange
        </button>
      </div>
    </div>
  </form>
</div>

<script>
(() => {
  // theme
  const themeToggle = document.getElementById('excThemeToggle');
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  if (!localStorage.getItem('theme')) localStorage.setItem('theme', prefersDark ? 'dark' : 'light');
  document.documentElement.setAttribute('data-theme', localStorage.getItem('theme'));
  themeToggle.addEventListener('click', () => {
    const cur = document.documentElement.getAttribute('data-theme');
    const nxt = cur === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', nxt);
    localStorage.setItem('theme', nxt);
  });

  // endpoints
  const URL_ORDERS     = @json(route('exchanges.ajax.orders'));
  const URL_ORDERITEMS = @json(route('exchanges.ajax.orderItems'));
  const URL_BATCHES    = @json(route('exchanges.ajax.batches'));
  const URL_AVAIL      = @json(route('exchanges.ajax.availability'));

  // dom
  const elOrderSearch = document.getElementById('excOrderSearch');
  const elOrderDD     = document.getElementById('excOrderDD');
  const elOrderId     = document.getElementById('excOrderId');
  const elOrderMeta   = document.getElementById('excOrderMeta');
  const elLoc         = document.getElementById('excLoc');
  const elHint        = document.getElementById('excHint');
  const btnSubmit     = document.getElementById('excSubmit');

  const elReturnLines = document.getElementById('excReturnLines');
  const elIssueLines  = document.getElementById('excIssueLines');
  const elReturnCount = document.getElementById('excReturnCount');
  const elIssueCount  = document.getElementById('excIssueCount');

  const elReturnTotal = document.getElementById('excReturnTotal');
  const elIssueTotal  = document.getElementById('excIssueTotal');
  const elDiffPill    = document.getElementById('excDiffPill');

  const debounce = (fn, d=250) => { let t; return (...a) => { clearTimeout(t); t=setTimeout(()=>fn(...a), d); } };
  async function getJSON(url){ const r=await fetch(url,{headers:{'Accept':'application/json'}}); if(!r.ok) throw new Error(await r.text()); return r.json(); }

  function setHint(msg, danger=false){
    elHint.innerHTML = msg ? `<span class="${danger?'exc-danger':''}">${msg}</span>` : '';
  }

  const state = {
    orderItems: [],
    returnLines: [],
    issueLines: [],
    availCache: new Map(), // key: `${locId}:${batchId}` -> available
  };

  const uid = () => Math.random().toString(16).slice(2) + Date.now().toString(16);

  function money(n){ n = Number(n||0); return n.toFixed(4); }
  function num(n){ n = Number(n||0); return isFinite(n) ? n : 0; }

  function calcSummary(){
    let rt = 0;
    let it = 0;

    state.returnLines.forEach(l => rt += num(l.qty) * num(l.unit_price));
    state.issueLines.forEach(l => it += num(l.qty) * num(l.unit_price));

    const diff = it - rt;

    elReturnTotal.value = money(rt);
    elIssueTotal.value = money(it);
    elDiffPill.textContent = `Diff: ${money(diff)} ${diff>0 ? '(Customer Pays)' : diff<0 ? '(Refund)' : ''}`;
  }

  // -----------------------
  // ORDER SEARCH DROPDOWN
  // -----------------------
  const doOrderSearch = debounce(async (q) => {
    const query = (q||'').trim();
    if(query.length < 2){ elOrderDD.classList.remove('show'); elOrderDD.innerHTML=''; return; }

    try{
      const res = await getJSON(`${URL_ORDERS}?q=${encodeURIComponent(query)}`);
      const rows = res.data || [];

      if(!rows.length){
        elOrderDD.innerHTML = `<div class="exc-mini" style="padding:10px;">No results</div>`;
        elOrderDD.classList.add('show');
        return;
      }

      elOrderDD.innerHTML = rows.map(r => {
        const meta = [
          r.customer_name ? r.customer_name : '',
          r.customer_phone ? r.customer_phone : '',
          r.status ? r.status : '',
        ].filter(Boolean).join(' • ');

        return `
          <div class="exc-dditem"
            data-id="${r.id}"
            data-no="${encodeURIComponent(r.order_no || '')}"
            data-meta="${encodeURIComponent(meta)}">
            <div class="exc-ddL">
              <b>${r.order_no || ('Order#' + r.id)}</b>
              <small>${meta || ''}</small>
            </div>
            <div class="exc-ddR">#${r.id}</div>
          </div>
        `;
      }).join('');

      elOrderDD.classList.add('show');
    }catch(e){
      console.error(e);
    }
  }, 250);

  elOrderSearch.addEventListener('input', () => doOrderSearch(elOrderSearch.value));

  elOrderDD.addEventListener('click', async (e) => {
    const item = e.target.closest('.exc-dditem');
    if(!item) return;

    const orderId = item.getAttribute('data-id');
    const orderNo = decodeURIComponent(item.getAttribute('data-no') || '');
    const meta    = decodeURIComponent(item.getAttribute('data-meta') || '');

    elOrderId.value = orderId;
    elOrderMeta.textContent = `Selected: ${orderNo || ('Order#' + orderId)} ${meta ? ' • ' + meta : ''}`;

    elOrderDD.classList.remove('show');
    elOrderDD.innerHTML = '';
    elOrderSearch.value = orderNo || '';

    // load order items
    await loadOrderItems(orderId);

    // ensure at least 1 return line if items exist
    if(state.orderItems.length){
      if(state.returnLines.length === 0) addReturnLine();
      renderReturnLines();
    }else{
      state.returnLines = [];
      renderReturnLines();
    }

    calcSummary();
  });

  document.addEventListener('click', (e) => {
    if(e.target.closest('.exc-dd')) return;
    if(e.target.closest('#excOrderDD') || e.target.closest('#excOrderSearch')) return;
    elOrderDD.classList.remove('show'); elOrderDD.innerHTML = '';
    document.querySelectorAll('.exc-ddbox.show').forEach(dd => { dd.classList.remove('show'); dd.innerHTML=''; });
  });

  async function loadOrderItems(orderId){
    state.orderItems = [];
    state.returnLines = [];
    renderReturnLines();

    if(!orderId) return;

    try{
      const res = await getJSON(`${URL_ORDERITEMS}?order_id=${encodeURIComponent(orderId)}`);
      state.orderItems = res.data || [];
    }catch(e){
      console.error(e);
      setHint('Failed to load order items.', true);
    }
  }

  // -----------------------
  // RETURN LINES
  // -----------------------
  function addReturnLine(){
    state.returnLines.push({
      _id: uid(),
      order_item_id: '',
      product_id: '',
      product_batch_id: '',
      qty: '',
      unit_price: '',
      max_returnable: 0,
      product_name: '',
      barcode: '',
    });
    renderReturnLines();
  }

  function removeReturnLine(id){
    state.returnLines = state.returnLines.filter(x => x._id !== id);
    renderReturnLines();
    calcSummary();
  }

  function returnLineTpl(line, idx){
    const options = state.orderItems.map(it => {
      const label = `${it.product_name || ('Product#'+it.product_id)} | Item#${it.id} | Batch#${it.product_batch_id} | Returnable:${it.qty_returnable}`;
      return `<option value="${it.id}">${label}</option>`;
    }).join('');

    return `
      <div class="exc-card" data-rline="${line._id}">
        <div class="exc-secTitle">
          <div>
            <b>Return Line #${idx+1}</b>
            <div class="exc-mini">
              <span data-role="rmeta">${line.product_name ? (line.product_name + (line.barcode ? ' • '+line.barcode : '')) : 'Select an order item'}</span>
            </div>
          </div>
          <button type="button" class="exc-btn exc-btn-ghost" data-action="rremove"><i class="fas fa-trash"></i></button>
        </div>

        <div class="exc-row">
          <div class="exc-col">
            <label class="exc-label">Order Item</label>
            <select class="exc-select" data-role="order_item_id" name="return_lines[${idx}][order_item_id]" required>
              <option value="">Select order item</option>
              ${options}
            </select>
            <div class="exc-mini">Max returnable: <b data-role="maxret">${line.max_returnable || 0}</b></div>
          </div>

          <div class="exc-col">
            <label class="exc-label">Qty</label>
            <input class="exc-input" data-role="qty" name="return_lines[${idx}][qty]" type="number" step="0.0001" required value="${line.qty || ''}">
            <div class="exc-mini" data-role="qtyHint"></div>
          </div>
        </div>

        <div class="exc-row">
          <div class="exc-col">
            <label class="exc-label">Unit Price</label>
            <input class="exc-input" data-role="unit_price" name="return_lines[${idx}][unit_price]" type="number" step="0.0001" required value="${line.unit_price || ''}">
            <div class="exc-mini">Return valuation (usually from order item unit price).</div>
          </div>

          <div class="exc-col">
            <label class="exc-label">Hidden IDs</label>
            <input class="exc-input" data-role="product_id" name="return_lines[${idx}][product_id]" type="number" required readonly value="${line.product_id || ''}">
            <input class="exc-input" data-role="product_batch_id" name="return_lines[${idx}][product_batch_id]" type="number" required readonly value="${line.product_batch_id || ''}" style="margin-top:8px;">
          </div>
        </div>
      </div>
    `;
  }

  function renderReturnLines(){
    elReturnLines.innerHTML = state.returnLines.map(returnLineTpl).join('');
    elReturnCount.textContent = `${state.returnLines.length} line${state.returnLines.length===1?'':'s'}`;

    // set selected values after render
    state.returnLines.forEach((l) => {
      const box = elReturnLines.querySelector(`[data-rline="${l._id}"]`);
      if(!box) return;
      const sel = box.querySelector('[data-role="order_item_id"]');
      if(sel && l.order_item_id) sel.value = l.order_item_id;
      updateReturnLineUI(l._id);
    });
  }

  function updateReturnLineUI(id){
    const l = state.returnLines.find(x => x._id === id);
    const box = elReturnLines.querySelector(`[data-rline="${id}"]`);
    if(!l || !box) return;

    box.querySelector('[data-role="maxret"]').textContent = l.max_returnable || 0;
    const hint = box.querySelector('[data-role="qtyHint"]');

    const qty = num(l.qty);
    const max = num(l.max_returnable);

    if(l.order_item_id){
      if(qty > max){
        hint.innerHTML = `<span class="exc-danger">Qty exceeds max (${max}).</span>`;
      }else{
        hint.textContent = max ? `Max: ${max}` : 'No returnable qty';
      }
    }else{
      hint.textContent = '';
    }

    const meta = box.querySelector('[data-role="rmeta"]');
    meta.textContent = l.product_name ? (l.product_name + (l.barcode ? ' • '+l.barcode : '')) : 'Select an order item';
  }

  // Return line events (delegation)
  elReturnLines.addEventListener('change', (e) => {
    const box = e.target.closest('[data-rline]');
    if(!box) return;
    const id = box.getAttribute('data-rline');
    const l = state.returnLines.find(x => x._id === id);
    if(!l) return;

    if(e.target.matches('[data-role="order_item_id"]')){
      const orderItemId = e.target.value;
      l.order_item_id = orderItemId;

      const it = state.orderItems.find(x => String(x.id) === String(orderItemId));
      if(it){
        l.product_id = it.product_id;
        l.product_batch_id = it.product_batch_id;
        l.max_returnable = it.qty_returnable;
        l.product_name = it.product_name || '';
        l.barcode = it.barcode || '';
        // default unit_price from order item
        l.unit_price = l.unit_price !== '' ? l.unit_price : it.unit_price;

        // fill read-only fields
        box.querySelector('[data-role="product_id"]').value = l.product_id;
        box.querySelector('[data-role="product_batch_id"]').value = l.product_batch_id;
        box.querySelector('[data-role="unit_price"]').value = l.unit_price;
      }

      updateReturnLineUI(id);
      calcSummary();
    }
  });

  elReturnLines.addEventListener('input', (e) => {
    const box = e.target.closest('[data-rline]');
    if(!box) return;
    const id = box.getAttribute('data-rline');
    const l = state.returnLines.find(x => x._id === id);
    if(!l) return;

    if(e.target.matches('[data-role="qty"]')){
      l.qty = e.target.value;
      updateReturnLineUI(id);
      calcSummary();
    }
    if(e.target.matches('[data-role="unit_price"]')){
      l.unit_price = e.target.value;
      calcSummary();
    }
  });

  elReturnLines.addEventListener('click', (e) => {
    const box = e.target.closest('[data-rline]');
    if(!box) return;
    const id = box.getAttribute('data-rline');
    if(e.target.closest('[data-action="rremove"]')) removeReturnLine(id);
  });

  document.getElementById('excAddReturn').addEventListener('click', () => {
    if(!elOrderId.value){
      setHint('Select an order first.', true);
      return;
    }
    if(!state.orderItems.length){
      setHint('This order has no items.', true);
      return;
    }
    addReturnLine();
  });

  document.getElementById('excClearReturn').addEventListener('click', () => {
    state.returnLines = [];
    renderReturnLines();
    calcSummary();
    setHint('');
  });

  // -----------------------
  // ISSUE LINES (batch search)
  // -----------------------
  function addIssueLine(){
    state.issueLines.push({
      _id: uid(),
      product_id: '',
      product_batch_id: '',
      qty: '',
      unit_price: '',
      product_name: '',
      product_barcode: '',
      batch_no: '',
      batch_sku: '',
      available: '',
    });
    renderIssueLines();
  }

  function removeIssueLine(id){
    state.issueLines = state.issueLines.filter(x => x._id !== id);
    if(state.issueLines.length === 0) addIssueLine();
    renderIssueLines();
    calcSummary();
  }

  function issueLineTpl(line, idx){
    return `
      <div class="exc-card exc-dd" data-iline="${line._id}">
        <div class="exc-secTitle">
          <div>
            <b>Issue Line #${idx+1}</b>
            <div class="exc-mini">
              <span data-role="imeta">${line.product_name ? line.product_name : 'Search & select a batch'}</span>
            </div>
          </div>
          <button type="button" class="exc-btn exc-btn-ghost" data-action="iremove"><i class="fas fa-trash"></i></button>
        </div>

        <div class="exc-row">
          <div class="exc-col">
            <label class="exc-label">Search Batch</label>
            <input class="exc-input" type="text" data-role="search" placeholder="name / product barcode / batch_no / batch_sku / batch id">
            <div class="exc-ddbox" data-role="dd"></div>
            <div class="exc-mini">Select result to autofill IDs + available.</div>
          </div>

          <div class="exc-col">
            <label class="exc-label">Available (Location)</label>
            <input class="exc-input" type="text" data-role="avail" readonly value="${line.available ?? ''}">
            <div class="exc-mini" data-role="qtyHint"></div>
          </div>
        </div>

        <div class="exc-row">
          <div class="exc-col">
            <label class="exc-label">Product ID</label>
            <input class="exc-input" name="issue_lines[${idx}][product_id]" data-role="product_id" type="number" required value="${line.product_id || ''}">
          </div>
          <div class="exc-col">
            <label class="exc-label">Batch ID</label>
            <input class="exc-input" name="issue_lines[${idx}][product_batch_id]" data-role="product_batch_id" type="number" required value="${line.product_batch_id || ''}">
          </div>
        </div>

        <div class="exc-row">
          <div class="exc-col">
            <label class="exc-label">Qty</label>
            <input class="exc-input" name="issue_lines[${idx}][qty]" data-role="qty" type="number" step="0.0001" required value="${line.qty || ''}">
          </div>
          <div class="exc-col">
            <label class="exc-label">Unit Price</label>
            <input class="exc-input" name="issue_lines[${idx}][unit_price]" data-role="unit_price" type="number" step="0.0001" required value="${line.unit_price || ''}">
          </div>
        </div>
      </div>
    `;
  }

  function renderIssueLines(){
    elIssueLines.innerHTML = state.issueLines.map(issueLineTpl).join('');
    elIssueCount.textContent = `${state.issueLines.length} line${state.issueLines.length===1?'':'s'}`;
    state.issueLines.forEach(l => updateIssueLineUI(l._id));
  }

  function updateIssueLineUI(id){
    const l = state.issueLines.find(x => x._id === id);
    const box = elIssueLines.querySelector(`[data-iline="${id}"]`);
    if(!l || !box) return;

    box.querySelector('[data-role="imeta"]').textContent = l.product_name || 'Search & select a batch';
    box.querySelector('[data-role="avail"]').value = (l.available ?? '') + '';

    const qtyHint = box.querySelector('[data-role="qtyHint"]');
    const qty = num(l.qty);
    const av  = num(l.available);

    if(l.product_batch_id && elLoc.value){
      if(qty > av) qtyHint.innerHTML = `<span class="exc-danger">Qty exceeds available (${av}).</span>`;
      else qtyHint.textContent = av ? `Max: ${av}` : 'No stock';
    } else qtyHint.textContent = '';
  }

  const doBatchSearch = debounce(async (lineId, q) => {
    const box = elIssueLines.querySelector(`[data-iline="${lineId}"]`);
    const l = state.issueLines.find(x => x._id === lineId);
    if(!box || !l) return;

    const dd = box.querySelector('[data-role="dd"]');
    const query = (q||'').trim();
    if(query.length < 2){ dd.classList.remove('show'); dd.innerHTML=''; return; }

    try{
      const res = await getJSON(`${URL_BATCHES}?q=${encodeURIComponent(query)}&location_id=${encodeURIComponent(elLoc.value || '')}`);
      const rows = res.data || [];

      if(!rows.length){
        dd.innerHTML = `<div class="exc-mini" style="padding:10px;">No results</div>`;
        dd.classList.add('show');
        return;
      }

      dd.innerHTML = rows.map(r => {
        const meta = [
          r.product_barcode ? `Barcode:${r.product_barcode}` : '',
          r.batch_no ? `BatchNo:${r.batch_no}` : '',
          r.batch_sku ? `BatchSKU:${r.batch_sku}` : '',
        ].filter(Boolean).join(' • ');

        return `
          <div class="exc-dditem"
            data-pid="${r.product_id}"
            data-bid="${r.product_batch_id}"
            data-pname="${encodeURIComponent(r.product_name || '')}"
            data-pbar="${encodeURIComponent(r.product_barcode || '')}"
            data-bno="${encodeURIComponent(r.batch_no || '')}"
            data-bsku="${encodeURIComponent(r.batch_sku || '')}"
            data-av="${r.available ?? 0}">
            <div class="exc-ddL">
              <b>${r.product_name || ('Product#'+r.product_id)}</b>
              <small>Batch #${r.product_batch_id}${meta ? ' • '+meta : ''}</small>
            </div>
            <div class="exc-ddR">${r.available ?? 0}</div>
          </div>
        `;
      }).join('');

      dd.classList.add('show');
    }catch(e){
      console.error(e);
    }
  }, 250);

  elIssueLines.addEventListener('input', (e) => {
    const box = e.target.closest('[data-iline]');
    if(!box) return;
    const id = box.getAttribute('data-iline');
    const l = state.issueLines.find(x => x._id === id);
    if(!l) return;

    if(e.target.matches('[data-role="search"]')) doBatchSearch(id, e.target.value);
    if(e.target.matches('[data-role="product_id"]')) l.product_id = e.target.value;
    if(e.target.matches('[data-role="product_batch_id"]')) { l.product_batch_id = e.target.value; refreshAvail(id); }
    if(e.target.matches('[data-role="qty"]')) { l.qty = e.target.value; updateIssueLineUI(id); calcSummary(); }
    if(e.target.matches('[data-role="unit_price"]')) { l.unit_price = e.target.value; calcSummary(); }
  });

  elIssueLines.addEventListener('click', (e) => {
    const box = e.target.closest('[data-iline]');
    if(!box) return;
    const id = box.getAttribute('data-iline');

    if(e.target.closest('[data-action="iremove"]')) { removeIssueLine(id); return; }

    const item = e.target.closest('.exc-dditem');
    if(item){
      const l = state.issueLines.find(x => x._id === id);
      if(!l) return;

      l.product_id = item.getAttribute('data-pid');
      l.product_batch_id = item.getAttribute('data-bid');
      l.product_name = decodeURIComponent(item.getAttribute('data-pname') || '');
      l.product_barcode = decodeURIComponent(item.getAttribute('data-pbar') || '');
      l.batch_no = decodeURIComponent(item.getAttribute('data-bno') || '');
      l.batch_sku = decodeURIComponent(item.getAttribute('data-bsku') || '');
      l.available = num(item.getAttribute('data-av'));

      box.querySelector('[data-role="product_id"]').value = l.product_id;
      box.querySelector('[data-role="product_batch_id"]').value = l.product_batch_id;

      const dd = box.querySelector('[data-role="dd"]');
      dd.classList.remove('show'); dd.innerHTML='';

      updateIssueLineUI(id);
      calcSummary();
    }
  });

  async function refreshAvail(lineId){
    const l = state.issueLines.find(x => x._id === lineId);
    if(!l || !l.product_batch_id || !elLoc.value) return;

    const key = `${elLoc.value}:${l.product_batch_id}`;
    if(state.availCache.has(key)){
      l.available = state.availCache.get(key);
      updateIssueLineUI(lineId);
      return;
    }

    try{
      const res = await getJSON(`${URL_AVAIL}?location_id=${encodeURIComponent(elLoc.value)}&product_batch_id=${encodeURIComponent(l.product_batch_id)}`);
      const av = num(res.available);
      state.availCache.set(key, av);
      l.available = av;
      updateIssueLineUI(lineId);
    }catch(e){ console.error(e); }
  }

  // location change -> clear caches + refresh issue availability
  elLoc.addEventListener('change', () => {
    state.availCache.clear();
    state.issueLines.forEach(l => { if(l.product_batch_id) refreshAvail(l._id); });
  });

  document.getElementById('excAddIssue').addEventListener('click', () => addIssueLine());
  document.getElementById('excClearIssue').addEventListener('click', () => {
    state.issueLines = [];
    addIssueLine();
    renderIssueLines();
    calcSummary();
  });

  // submit validations
  document.getElementById('excForm').addEventListener('submit', (e) => {
    setHint('');

    if(!elOrderId.value){
      e.preventDefault();
      setHint('Select an order before submitting.', true);
      return;
    }
    if(!elLoc.value){
      e.preventDefault();
      setHint('Select location before submitting.', true);
      return;
    }

    // return qty validation
    for(const l of state.returnLines){
      const qty = num(l.qty);
      const max = num(l.max_returnable);
      if(!l.order_item_id){
        e.preventDefault();
        setHint('Each return line must select an order item.', true);
        return;
      }
      if(qty <= 0 || qty > max){
        e.preventDefault();
        setHint(`Return qty invalid for order_item_id=${l.order_item_id}. Max ${max}.`, true);
        return;
      }
    }

    // issue qty validation (if availability known)
    for(const l of state.issueLines){
      const qty = num(l.qty);
      const av  = num(l.available);
      if(!l.product_batch_id || !l.product_id){
        e.preventDefault();
        setHint('Each issue line must have product_id and product_batch_id.', true);
        return;
      }
      if(qty <= 0){
        e.preventDefault();
        setHint('Issue qty must be > 0.', true);
        return;
      }
      if(elLoc.value && l.product_batch_id && av >= 0 && qty > av){
        e.preventDefault();
        setHint(`Issue qty exceeds available for batch #${l.product_batch_id} (available ${av}).`, true);
        return;
      }
    }
  });

  // init
  addIssueLine();
  renderIssueLines();
  renderReturnLines();
  calcSummary();
})();
</script>
@endsection
