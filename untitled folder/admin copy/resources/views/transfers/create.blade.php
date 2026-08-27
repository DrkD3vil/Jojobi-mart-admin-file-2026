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

/* layout */
.stx-wrap{max-width:1100px; margin:0 auto; padding:18px; color:var(--foreground);}
.stx-top{display:flex; justify-content:space-between; gap:12px; align-items:center; margin-bottom:12px;}
.stx-h{font-size:1.3rem; font-weight:800; display:flex; gap:10px; align-items:center;}
.stx-sub{color:var(--muted-foreground);}

.stx-theme{
  width:56px; height:28px; border-radius:14px;
  background:var(--muted); border:1px solid var(--border); position:relative; cursor:pointer;
}
.stx-theme::before{
  content:''; width:22px; height:22px; border-radius:50%;
  background:var(--accent-color); position:absolute; top:2px; left:2px;
  transition: transform var(--transition-normal);
}
html[data-theme='light'] .stx-theme::before{transform: translateX(28px);}

.stx-card{
  background:var(--card);
  border:1px solid var(--border);
  border-radius:var(--radius);
  box-shadow:var(--card-shadow);
  padding:18px;
  margin:14px 0;
}

.stx-row{display:flex; gap:12px; flex-wrap:wrap;}
.stx-col{flex:1; min-width:260px;}

.stx-label{display:block; margin:8px 0 6px; font-weight:700;}
.stx-input,.stx-select,.stx-textarea{
  width:100%;
  background:var(--input);
  color:var(--foreground);
  border:1px solid var(--border);
  border-radius:var(--radius);
  padding:11px 12px;
  outline:none;
}
.stx-textarea{min-height:70px; resize:vertical;}
.stx-mini{color:var(--muted-foreground); font-size:.9rem;}

.stx-btn{
  border:0; border-radius:var(--radius);
  padding:11px 14px; cursor:pointer;
  display:inline-flex; gap:8px; align-items:center;
  font-weight:700;
}
.stx-btn-primary{background:var(--accent-color); color:var(--sidebar-primary-foreground);}
.stx-btn-primary:hover{background:var(--accent-hover); box-shadow:0 6px 16px var(--accent-glow);}
.stx-btn-ghost{background:var(--muted); color:var(--foreground); border:1px solid var(--border);}
.stx-btn-ghost:hover{border-color:var(--accent-color);}

.stx-pill{
  display:inline-block; padding:4px 10px; border-radius:999px;
  background:var(--muted); border:1px solid var(--border);
  font-size:.85rem;
}

.stx-lines{display:flex; flex-direction:column; gap:12px; margin-top:12px;}
.stx-lineTop{display:flex; justify-content:space-between; gap:10px; align-items:center;}

.stx-dd{position:relative;}
.stx-ddbox{
  position:absolute; left:0; right:0; top:56px;
  background:var(--card); border:1px solid var(--border);
  border-radius:var(--radius);
  box-shadow:var(--dropdown-shadow);
  max-height:320px; overflow:auto;
  padding:8px; display:none; z-index:999;
}
.stx-ddbox.show{display:block;}
.stx-dditem{
  padding:10px; border-radius:calc(var(--radius) - 2px);
  cursor:pointer; display:flex; justify-content:space-between; gap:10px;
}
.stx-dditem:hover{background:var(--muted);}
.stx-ddL b{display:block; margin-bottom:2px;}
.stx-ddL small{color:var(--muted-foreground);}
.stx-ddR{font-weight:900; color:var(--accent-color);}

.stx-danger{color:var(--danger); font-weight:800;}
</style>

<div class="stx-wrap">
  <div class="stx-top">
    <div>
      <div class="stx-h"><i class="fas fa-right-left"></i> Stock Transfer</div>
      <div class="stx-sub">Search by Product Name / Product Barcode / Batch No / Batch SKU / Batch ID</div>
    </div>
    <div class="stx-theme" id="stxThemeToggle" title="Toggle theme"></div>
  </div>

  @if(session('ok'))
    <div class="stx-card" style="background:var(--success); color:white;">
      <i class="fas fa-check-circle"></i> {{ session('ok') }}
    </div>
  @endif

  @if($errors->any())
    <div class="stx-card" style="background:var(--danger); color:white;">
      <i class="fas fa-exclamation-triangle"></i>
      <div style="margin-top:6px;">
        <b>Errors:</b>
        <ul style="margin:8px 0 0 18px;">
          @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
      </div>
    </div>
  @endif

  <form method="POST" action="{{ route('transfers.store') }}" id="stxForm">
    @csrf

    <div class="stx-card">
      <div class="stx-row">
        <div class="stx-col">
          <label class="stx-label">From Location</label>
          <select name="from_location_id" id="stxFromLoc" class="stx-select" required>
            <option value="">Select</option>
            @foreach($locations as $l)
              <option value="{{ $l->id }}">{{ $l->name }}</option>
            @endforeach
          </select>
          <div class="stx-mini">Availability uses (on_hand - reserved).</div>
        </div>

        <div class="stx-col">
          <label class="stx-label">To Location</label>
          <select name="to_location_id" id="stxToLoc" class="stx-select" required>
            <option value="">Select</option>
            @foreach($locations as $l)
              <option value="{{ $l->id }}">{{ $l->name }}</option>
            @endforeach
          </select>
          <div class="stx-mini">Must be different from From Location.</div>
        </div>
      </div>

      <label class="stx-label">Note</label>
      <textarea name="note" class="stx-textarea" placeholder="Optional note..."></textarea>
      <div class="stx-mini" id="stxHint"></div>
    </div>

    <div class="stx-card">
      <div class="stx-lineTop">
        <div>
          <b style="font-size:1.05rem;"><i class="fas fa-list"></i> Transfer Lines</b>
          <div class="stx-mini">Tip: type at least 2 characters to search.</div>
        </div>
        <span class="stx-pill" id="stxLineCount">1 line</span>
      </div>

      <div class="stx-lines" id="stxLines"></div>

      <div class="stx-row" style="margin-top:12px;">
        <button type="button" class="stx-btn stx-btn-ghost" id="stxAddLine"><i class="fas fa-plus"></i> Add line</button>
        <button type="button" class="stx-btn stx-btn-ghost" id="stxClearAll"><i class="fas fa-eraser"></i> Clear</button>
        <button type="submit" class="stx-btn stx-btn-primary" id="stxSubmit" style="margin-left:auto;">
          <i class="fas fa-paper-plane"></i> Submit Transfer
        </button>
      </div>
    </div>
  </form>
</div>

<script>
(() => {
  // theme
  const themeToggle = document.getElementById('stxThemeToggle');
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
  const URL_BATCHES = @json(route('transfers.ajax.batches'));
  const URL_AVAIL   = @json(route('transfers.ajax.availability'));

  // dom
  const elLines = document.getElementById('stxLines');
  const elCount = document.getElementById('stxLineCount');
  const elHint  = document.getElementById('stxHint');
  const fromLoc = document.getElementById('stxFromLoc');
  const toLoc   = document.getElementById('stxToLoc');
  const btnAdd  = document.getElementById('stxAddLine');
  const btnClr  = document.getElementById('stxClearAll');
  const btnSub  = document.getElementById('stxSubmit');

  const debounce = (fn, d=250) => { let t; return (...a) => { clearTimeout(t); t=setTimeout(()=>fn(...a), d); } };
  async function getJSON(url){ const r = await fetch(url, {headers:{'Accept':'application/json'}}); if(!r.ok) throw new Error(await r.text()); return r.json(); }
  function setHint(msg, danger=false){ elHint.innerHTML = msg ? `<span class="${danger ? 'stx-danger':''}">${msg}</span>` : ''; }

  function ensureLocValid(){
    if(fromLoc.value && toLoc.value && fromLoc.value === toLoc.value){
      btnSub.disabled = true;
      setHint('From Location and To Location cannot be same.', true);
      return false;
    }
    btnSub.disabled = false;
    setHint('');
    return true;
  }

  const state = { lines: [], availCache: new Map() };
  const uid = () => Math.random().toString(16).slice(2) + Date.now().toString(16);

  function updateCount(){
    const n = state.lines.length;
    elCount.textContent = `${n} line${n>1?'s':''}`;
  }

  function lineTpl(line, idx){
    return `
      <div class="stx-card" data-line="${line._id}">
        <div class="stx-lineTop">
          <div>
            <b>Line #${idx+1}</b>
            <div class="stx-mini">
              Product: <span data-role="pname">${line.product_name || '-'}</span>
              &nbsp;|&nbsp;
              Batch: <span data-role="bmeta">${line.batch_no || line.batch_sku || line.product_batch_id || '-'}</span>
            </div>
          </div>
          <button type="button" class="stx-btn stx-btn-ghost" data-action="remove"><i class="fas fa-trash"></i></button>
        </div>

        <div class="stx-row">
          <div class="stx-col stx-dd">
            <label class="stx-label">Search</label>
            <input class="stx-input" type="text" data-role="search" placeholder="name / barcode / batch_no / batch_sku / batch id">
            <div class="stx-ddbox" data-role="dd"></div>
            <div class="stx-mini">Click result to fill IDs.</div>
          </div>

          <div class="stx-col">
            <label class="stx-label">Available (From Location)</label>
            <input class="stx-input" type="text" data-role="avail" readonly value="${line.available ?? ''}">
            <div class="stx-mini" data-role="qtyHint"></div>
          </div>
        </div>

        <div class="stx-row">
          <div class="stx-col">
            <label class="stx-label">Product ID</label>
            <input class="stx-input" name="lines[${idx}][product_id]" data-role="product_id" type="number" required value="${line.product_id || ''}">
          </div>
          <div class="stx-col">
            <label class="stx-label">Batch ID</label>
            <input class="stx-input" name="lines[${idx}][product_batch_id]" data-role="product_batch_id" type="number" required value="${line.product_batch_id || ''}">
          </div>
        </div>

        <div class="stx-row">
          <div class="stx-col">
            <label class="stx-label">Qty</label>
            <input class="stx-input" name="lines[${idx}][qty]" data-role="qty" type="number" step="0.0001" required value="${line.qty || ''}">
          </div>
          <div class="stx-col">
            <label class="stx-label">Unit</label>
            <input class="stx-input" name="lines[${idx}][unit]" data-role="unit" type="text" value="${line.unit || 'pcs'}">
          </div>
        </div>
      </div>
    `;
  }

  function render(){
    elLines.innerHTML = state.lines.map(lineTpl).join('');
    updateCount();
  }

  function reindexNames(){
    [...elLines.querySelectorAll('[data-line]')].forEach((box, idx) => {
      box.querySelector('[data-role="product_id"]').name = `lines[${idx}][product_id]`;
      box.querySelector('[data-role="product_batch_id"]').name = `lines[${idx}][product_batch_id]`;
      box.querySelector('[data-role="qty"]').name = `lines[${idx}][qty]`;
      box.querySelector('[data-role="unit"]').name = `lines[${idx}][unit]`;
    });
  }

  function addLine(){
    state.lines.push({_id:uid(), product_id:'', product_batch_id:'', qty:'', unit:'pcs', product_name:'', batch_no:'', batch_sku:'', available:''});
    render();
  }

  function removeLine(id){
    state.lines = state.lines.filter(x => x._id !== id);
    if(state.lines.length === 0) addLine();
    render();
    reindexNames();
  }

  async function refreshAvail(lineId){
    const line = state.lines.find(x => x._id === lineId);
    if(!line || !line.product_batch_id || !fromLoc.value) return;

    const key = `${fromLoc.value}:${line.product_batch_id}`;
    if(state.availCache.has(key)){
      line.available = state.availCache.get(key);
      updateLineUI(lineId);
      return;
    }

    try{
      const res = await getJSON(`${URL_AVAIL}?location_id=${encodeURIComponent(fromLoc.value)}&product_batch_id=${encodeURIComponent(line.product_batch_id)}`);
      const available = parseFloat(res.available || 0);
      state.availCache.set(key, available);
      line.available = available;
      updateLineUI(lineId);
    }catch(e){ console.error(e); }
  }

  function updateLineUI(lineId){
    const line = state.lines.find(x => x._id === lineId);
    const box = elLines.querySelector(`[data-line="${lineId}"]`);
    if(!line || !box) return;

    box.querySelector('[data-role="pname"]').textContent = line.product_name || '-';
    box.querySelector('[data-role="bmeta"]').textContent = line.batch_no || line.batch_sku || line.product_batch_id || '-';
    box.querySelector('[data-role="avail"]').value = (line.available ?? '') + '';

    const qtyHint = box.querySelector('[data-role="qtyHint"]');
    const qty = parseFloat(line.qty || 0);
    const av  = parseFloat(line.available || 0);

    if(line.product_batch_id && fromLoc.value){
      if(qty > av) qtyHint.innerHTML = `<span class="stx-danger">Qty exceeds available (${av}).</span>`;
      else qtyHint.textContent = av ? `Max: ${av}` : 'No stock';
    } else qtyHint.textContent = '';
  }

  const doSearch = debounce(async (lineId, q) => {
    const box = elLines.querySelector(`[data-line="${lineId}"]`);
    const line = state.lines.find(x => x._id === lineId);
    if(!box || !line) return;

    const dd = box.querySelector('[data-role="dd"]');
    const query = (q || '').trim();
    if(query.length < 2){ dd.classList.remove('show'); dd.innerHTML=''; return; }

    try{
      const res = await getJSON(`${URL_BATCHES}?q=${encodeURIComponent(query)}&from_location_id=${encodeURIComponent(fromLoc.value || '')}`);
      const rows = res.data || [];
      if(!rows.length){
        dd.innerHTML = `<div class="stx-mini" style="padding:10px;">No results</div>`;
        dd.classList.add('show'); return;
      }

      dd.innerHTML = rows.map(r => {
        const meta = [
          r.product_barcode ? `Barcode: ${r.product_barcode}` : '',
          r.batch_no ? `BatchNo: ${r.batch_no}` : '',
          r.batch_sku ? `BatchSKU: ${r.batch_sku}` : '',
        ].filter(Boolean).join(' • ');

        return `
          <div class="stx-dditem"
            data-pid="${r.product_id}"
            data-bid="${r.product_batch_id}"
            data-pname="${encodeURIComponent(r.product_name || '')}"
            data-bno="${encodeURIComponent(r.batch_no || '')}"
            data-bsku="${encodeURIComponent(r.batch_sku || '')}"
            data-av="${r.available ?? 0}">
            <div class="stx-ddL">
              <b>${r.product_name || ('Product #' + r.product_id)}</b>
              <small>Batch #${r.product_batch_id}${meta ? ' • ' + meta : ''}</small>
            </div>
            <div class="stx-ddR">${r.available ?? 0}</div>
          </div>
        `;
      }).join('');
      dd.classList.add('show');
    }catch(e){ console.error(e); }
  }, 250);

  fromLoc.addEventListener('change', () => {
    state.availCache.clear();
    ensureLocValid();
    state.lines.forEach(l => { if(l.product_batch_id) refreshAvail(l._id); });
  });
  toLoc.addEventListener('change', ensureLocValid);

  elLines.addEventListener('input', (e) => {
    const box = e.target.closest('[data-line]');
    if(!box) return;
    const id = box.getAttribute('data-line');
    const line = state.lines.find(x => x._id === id);
    if(!line) return;

    if(e.target.matches('[data-role="search"]')) doSearch(id, e.target.value);
    if(e.target.matches('[data-role="product_id"]')) line.product_id = e.target.value;
    if(e.target.matches('[data-role="product_batch_id"]')) { line.product_batch_id = e.target.value; refreshAvail(id); }
    if(e.target.matches('[data-role="qty"]')) { line.qty = e.target.value; updateLineUI(id); }
    if(e.target.matches('[data-role="unit"]')) line.unit = e.target.value || 'pcs';
  });

  elLines.addEventListener('click', (e) => {
    const box = e.target.closest('[data-line]');
    if(!box) return;
    const id = box.getAttribute('data-line');

    if(e.target.closest('[data-action="remove"]')) { removeLine(id); return; }

    const item = e.target.closest('.stx-dditem');
    if(item){
      const line = state.lines.find(x => x._id === id);
      if(!line) return;

      line.product_id = item.getAttribute('data-pid');
      line.product_batch_id = item.getAttribute('data-bid');
      line.product_name = decodeURIComponent(item.getAttribute('data-pname') || '');
      line.batch_no = decodeURIComponent(item.getAttribute('data-bno') || '');
      line.batch_sku = decodeURIComponent(item.getAttribute('data-bsku') || '');
      line.available = parseFloat(item.getAttribute('data-av') || 0);

      box.querySelector('[data-role="product_id"]').value = line.product_id;
      box.querySelector('[data-role="product_batch_id"]').value = line.product_batch_id;

      const dd = box.querySelector('[data-role="dd"]');
      dd.classList.remove('show'); dd.innerHTML = '';

      updateLineUI(id);
    }
  });

  document.addEventListener('click', (e) => {
    if(e.target.closest('.stx-dd')) return;
    document.querySelectorAll('.stx-ddbox.show').forEach(dd => { dd.classList.remove('show'); dd.innerHTML=''; });
  });

  btnAdd.addEventListener('click', () => { addLine(); reindexNames(); });
  btnClr.addEventListener('click', () => { state.lines=[]; addLine(); render(); reindexNames(); setHint(''); });

  document.getElementById('stxForm').addEventListener('submit', (e) => {
    if(!ensureLocValid()){ e.preventDefault(); return; }
    for(const l of state.lines){
      const qty = parseFloat(l.qty || 0);
      const av  = parseFloat(l.available || 0);
      if(l.product_batch_id && fromLoc.value && qty > av){
        e.preventDefault();
        setHint(`Qty exceeds available stock for batch #${l.product_batch_id} (available ${av}).`, true);
        return;
      }
    }
  });

  addLine();
  render();
  ensureLocValid();
})();
</script>
@endsection
