{{-- resources/views/stock-ledger/index.blade.php --}}
@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

<style>
/* ===== Your fixed color system (same as before, kept short) ===== */
:root{
  --radius:.625rem;
  --transition-fast:150ms;
  --transition-normal:250ms;

  --background: oklch(0.145 0 0);
  --foreground: oklch(0.985 0 0);
  --card: oklch(0.205 0 0);
  --secondary: oklch(0.269 0 0);
  --muted-foreground: oklch(0.708 0 0);
  --border: oklch(1 0 0 / 15%);
  --input: oklch(1 0 0 / 15%);
  --sidebar-primary: oklch(0.488 0.243 264.376);

  --success: oklch(0.696 0.17 162.48);
  --warning: oklch(0.769 0.188 70.08);
  --info: oklch(0.488 0.243 264.376);
  --danger: oklch(0.704 0.191 22.216);

  --accent-color: var(--sidebar-primary);
  --accent-hover: oklch(0.488 0.243 264.376 / .85);
  --accent-glow: oklch(0.488 0.243 264.376 / .2);

  --border-color: var(--border);
  --bg-tertiary: var(--secondary);
  --text-secondary: var(--muted-foreground);

  --card-shadow: 0 2px 4px 0 rgb(0 0 0 / .25);
  --dropdown-shadow: 0 10px 25px -5px rgb(0 0 0 / .4), 0 8px 10px -6px rgb(0 0 0 / .3);
}
html[data-theme='light']{
  --background: oklch(0.99 0 0);
  --foreground: oklch(0.12 0 0);
  --card: oklch(1 0 0);
  --secondary: oklch(0.97 0 0);
  --muted-foreground: oklch(0.5 0 0);
  --border: oklch(0.9 0 0);
  --input: oklch(0.96 0 0);
  --sidebar-primary: oklch(0.646 0.222 41.116);

  --success: oklch(0.627 0.194 149.214);
  --warning: oklch(0.769 0.188 70.08);
  --info: oklch(0.623 0.214 259.815);
  --danger: oklch(0.577 0.245 27.325);

  --accent-color: var(--sidebar-primary);
  --accent-hover: oklch(0.646 0.222 41.116 / .85);
  --accent-glow: oklch(0.646 0.222 41.116 / .12);

  --border-color: var(--border);
  --bg-tertiary: var(--secondary);

  --card-shadow: 0 2px 4px 0 rgb(0 0 0 / .08);
  --dropdown-shadow: 0 10px 25px -5px rgb(0 0 0 / .15), 0 8px 10px -6px rgb(0 0 0 / .1);
}

/* ===== Ledger UI ===== */
.ledg-wrap{max-width:1280px;margin:0 auto;padding:16px;color:var(--foreground);}
.ledg-top{display:flex;gap:12px;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;margin-bottom:14px;}
.ledg-title{font-size:1.55rem;font-weight:900;display:flex;align-items:center;gap:10px;}
.ledg-sub{color:var(--text-secondary);font-size:.95rem;margin-top:6px;max-width:760px;}
.ledg-actions{display:flex;gap:10px;align-items:center;flex-wrap:wrap;}
.ledg-pill{display:inline-flex;align-items:center;gap:8px;border:1px solid var(--border-color);background:var(--bg-tertiary);padding:8px 10px;border-radius:999px;font-size:.85rem;}
.ledg-btn{border:1px solid var(--border-color);padding:10px 12px;border-radius:calc(var(--radius) - 2px);cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all var(--transition-fast);background:transparent;color:var(--foreground);}
.ledg-btn:hover{background:var(--bg-tertiary);border-color:var(--accent-color);}
.ledg-btn-primary{background:var(--accent-color);border-color:transparent;color:#fff;}
.ledg-btn-primary:hover{background:var(--accent-hover);box-shadow:0 8px 18px -10px var(--accent-glow);}
.ledg-card{background:var(--card);border:1px solid var(--border-color);border-radius:var(--radius);padding:14px;box-shadow:var(--card-shadow);margin-bottom:14px;}
.ledg-grid{display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1fr;gap:10px;align-items:end;}
@media(max-width: 980px){.ledg-grid{grid-template-columns:1fr 1fr;}}
@media(max-width: 520px){.ledg-grid{grid-template-columns:1fr;}}
.ledg-field label{display:block;font-size:.82rem;color:var(--text-secondary);margin-bottom:6px;}
.ledg-input,.ledg-select{
  width:100%;padding:10px 12px;border-radius:calc(var(--radius) - 2px);
  border:1px solid var(--border-color);background:var(--input);color:var(--foreground);outline:none;
}
.ledg-input:focus,.ledg-select:focus{border-color:var(--accent-color);box-shadow:0 0 0 3px var(--accent-glow);}
.ledg-tableWrap{overflow:auto;border:1px solid var(--border-color);border-radius:var(--radius);}
.ledg-table{width:100%;border-collapse:collapse;min-width:1150px;}
.ledg-table thead{background:var(--bg-tertiary);}
.ledg-table th,.ledg-table td{padding:12px;border-bottom:1px solid var(--border-color);text-align:left;vertical-align:middle;}
.ledg-table tbody tr:hover{background:var(--bg-tertiary);}
.ledg-mini{color:var(--text-secondary);font-size:.85rem;}
.ledg-mono{font-family: ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;}
.ledg-badge{display:inline-flex;align-items:center;gap:8px;border:1px solid var(--border-color);padding:6px 10px;border-radius:999px;font-size:.85rem;font-weight:800;}
.ledg-in{background:color-mix(in oklch, var(--success) 30%, var(--card) 70%);}
.ledg-out{background:color-mix(in oklch, var(--danger) 30%, var(--card) 70%);}
.ledg-warn{background:color-mix(in oklch, var(--warning) 30%, var(--card) 70%);}
.ledg-metaBtn{border:1px solid var(--border-color);background:transparent;color:var(--foreground);border-radius:10px;padding:8px 10px;cursor:pointer;}
.ledg-metaBtn:hover{background:var(--bg-tertiary);border-color:var(--accent-color);}
.ledg-foot{display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-top:12px;}
.ledg-muted{color:var(--text-secondary);font-size:.9rem;}
.ledg-skel{height:14px;border-radius:999px;background:color-mix(in oklch, var(--bg-tertiary) 65%, var(--card) 35%);}
.ledg-modal{position:fixed;inset:0;display:none;align-items:center;justify-content:center;padding:16px;z-index:9999;background:rgba(0,0,0,.55);}
.ledg-modal.show{display:flex;}
.ledg-modalCard{width:min(920px,100%);background:var(--card);border:1px solid var(--border-color);border-radius:var(--radius);box-shadow:var(--dropdown-shadow);overflow:hidden;}
.ledg-modalTop{display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-bottom:1px solid var(--border-color);}
.ledg-modalTitle{font-weight:900;display:flex;gap:10px;align-items:center;}
.ledg-modalBody{padding:14px;}
.ledg-pre{margin:0;background:color-mix(in oklch, var(--card) 70%, var(--bg-tertiary) 30%);border:1px solid var(--border-color);border-radius:12px;padding:12px;max-height:65vh;overflow:auto;white-space:pre-wrap;word-break:break-word;}
</style>

@php
  $ajaxUrl = route('stock-ledger.ajax');
@endphp

<div class="ledg-wrap">
  <div class="ledg-top">
    <div>
      <div class="ledg-title"><i class="fas fa-book"></i> Stock Ledger</div>
      <div class="ledg-sub">Server-side AJAX search + filters + pagination. Click Meta for JSON, export current page.</div>
    </div>

    <div class="ledg-actions">
      <span class="ledg-pill" id="ledgCountPill">
        <i class="fas fa-list"></i> Ready
      </span>
      <button type="button" class="ledg-btn" id="ledgClear"><i class="fas fa-eraser"></i> Clear</button>
      <button type="button" class="ledg-btn ledg-btn-primary" id="ledgExportCsv"><i class="fas fa-file-csv"></i> Export (page)</button>
    </div>
  </div>

  <div class="ledg-card">
    <div class="ledg-grid">
      <div class="ledg-field">
        <label><i class="fas fa-magnifying-glass"></i> Search</label>
        <input class="ledg-input" id="q" placeholder="ref_type, ref_id, batch, location, direction, meta..." />
        <div class="ledg-mini" style="margin-top:6px;">Debounced, hits server.</div>
      </div>

      <div class="ledg-field">
        <label><i class="fas fa-boxes-stacked"></i> Batch ID</label>
        <input class="ledg-input" id="batch_id" inputmode="numeric" placeholder="e.g. 25" />
      </div>

      <div class="ledg-field">
        <label><i class="fas fa-location-dot"></i> Location ID</label>
        <input class="ledg-input" id="location_id" inputmode="numeric" placeholder="e.g. 1" />
      </div>

      <div class="ledg-field">
        <label><i class="fas fa-right-left"></i> Direction</label>
        <select class="ledg-select" id="direction">
          <option value="">All</option>
          <option value="IN">IN</option>
          <option value="OUT">OUT</option>
        </select>
      </div>

      <div class="ledg-field">
        <label><i class="fas fa-layer-group"></i> Per Page</label>
        <select class="ledg-select" id="per_page">
          <option value="25">25</option>
          <option value="50" selected>50</option>
          <option value="100">100</option>
          <option value="200">200</option>
        </select>
      </div>

      <div class="ledg-field">
        <label><i class="fas fa-calendar"></i> Date From</label>
        <input class="ledg-input" id="date_from" type="date" />
      </div>

      <div class="ledg-field">
        <label><i class="fas fa-calendar"></i> Date To</label>
        <input class="ledg-input" id="date_to" type="date" />
      </div>

      <div class="ledg-field">
        <label><i class="fas fa-scale-balanced"></i> Min Qty</label>
        <input class="ledg-input" id="min_qty" type="number" step="0.0001" placeholder="0" />
      </div>

      <div class="ledg-field">
        <label><i class="fas fa-scale-balanced"></i> Max Qty</label>
        <input class="ledg-input" id="max_qty" type="number" step="0.0001" placeholder="999" />
      </div>

      <div class="ledg-field">
        <label><i class="fas fa-tag"></i> Ref Type</label>
        <input class="ledg-input" id="ref_type" placeholder="sale, exchange, return..." />
      </div>
    </div>

    <div class="ledg-foot">
      <div class="ledg-muted" id="statusLine">Tip: paginate from below without full page reload.</div>
      <span class="ledg-pill" id="metaPill"><i class="fas fa-bolt"></i> AJAX enabled</span>
    </div>
  </div>

  <div class="ledg-card">
    <div class="ledg-tableWrap">
      <table class="ledg-table">
        <thead>
          <tr>
            <th style="width:210px;">Date</th>
            <th style="width:120px;">Batch</th>
            <th style="width:120px;">Location</th>
            <th style="width:130px;">Direction</th>
            <th style="width:140px;">Qty</th>
            <th style="width:220px;">Ref</th>
            <th style="width:120px;">Meta</th>
          </tr>
        </thead>
        <tbody id="tbody">
          {{-- initial SSR rows (optional), will be replaced on first AJAX load --}}
          @foreach($rows as $r)
            @php
              $dir = strtoupper((string)($r->direction ?? ''));
              $badgeClass = $dir === 'IN' ? 'ledg-in' : ($dir === 'OUT' ? 'ledg-out' : 'ledg-warn');
              $metaJson = json_encode($r->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}';
            @endphp
            <tr>
              <td>
                <div class="ledg-mini">{{ $r->created_at }}</div>
                <div class="ledg-mini ledg-mono">#{{ $r->id }}</div>
              </td>
              <td><b class="ledg-mono">{{ $r->product_batch_id }}</b></td>
              <td><b class="ledg-mono">{{ $r->location_id }}</b></td>
              <td><span class="ledg-badge {{ $badgeClass }}"><i class="fas fa-right-left"></i> {{ $dir }}</span></td>
              <td><b class="ledg-mono">{{ number_format((float)$r->qty, 4) }}</b></td>
              <td>
                <div class="ledg-mini">Ref</div>
                <div class="ledg-mono">{{ $r->ref_type ?? '-' }}#{{ $r->ref_id ?? '-' }}</div>
                @if($r->line_id)<div class="ledg-mini">Line ID: <span class="ledg-mono">{{ $r->line_id }}</span></div>@endif
              </td>
              <td>
                <button type="button" class="ledg-metaBtn" data-action="meta"
                  data-title="Batch {{ $r->product_batch_id }} • Location {{ $r->location_id }} • {{ $dir }}"
                  data-meta="{{ e($metaJson) }}">
                  <i class="fas fa-code"></i> Meta
                </button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="ledg-foot">
      <div class="ledg-muted" id="shownInfo">Showing initial page…</div>
      <div id="paginationBox">
        {{ $rows->links() }}
      </div>
    </div>
  </div>
</div>

{{-- Meta Modal --}}
<div class="ledg-modal" id="modal" aria-hidden="true">
  <div class="ledg-modalCard" role="dialog" aria-modal="true">
    <div class="ledg-modalTop">
      <div class="ledg-modalTitle"><i class="fas fa-brackets-curly"></i> <span id="modalTitle">Meta</span></div>
      <div class="ledg-actions">
        <button type="button" class="ledg-btn" id="copyBtn"><i class="fas fa-copy"></i> Copy</button>
        <button type="button" class="ledg-btn" id="closeBtn"><i class="fas fa-xmark"></i> Close</button>
      </div>
    </div>
    <div class="ledg-modalBody">
      <pre class="ledg-pre ledg-mono" id="modalPre">{}</pre>
      <div class="ledg-mini" style="margin-top:10px;">Tip: ESC to close</div>
    </div>
  </div>
</div>

<script>
(() => {
  const AJAX_URL = @json($ajaxUrl);

  // inputs
  const el = (id) => document.getElementById(id);
  const q = el('q');
  const batch_id = el('batch_id');
  const location_id = el('location_id');
  const direction = el('direction');
  const per_page = el('per_page');
  const date_from = el('date_from');
  const date_to = el('date_to');
  const min_qty = el('min_qty');
  const max_qty = el('max_qty');
  const ref_type = el('ref_type');

  // ui
  const tbody = el('tbody');
  const paginationBox = el('paginationBox');
  const countPill = el('ledgCountPill');
  const shownInfo = el('shownInfo');
  const statusLine = el('statusLine');
  const clearBtn = el('ledgClear');
  const exportBtn = el('ledgExportCsv');

  // modal
  const modal = el('modal');
  const modalTitle = el('modalTitle');
  const modalPre = el('modalPre');
  const closeBtn = el('closeBtn');
  const copyBtn = el('copyBtn');

  let lastRows = []; // keep last AJAX rows for export

  const debounce = (fn, d=250) => { let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a), d);} };

  function buildParams(page=1){
    const p = new URLSearchParams();
    if(q.value.trim()) p.set('q', q.value.trim());
    if(batch_id.value.trim()) p.set('batch_id', batch_id.value.trim());
    if(location_id.value.trim()) p.set('location_id', location_id.value.trim());
    if(direction.value) p.set('direction', direction.value);
    if(ref_type.value.trim()) p.set('ref_type', ref_type.value.trim());
    if(date_from.value) p.set('date_from', date_from.value);
    if(date_to.value) p.set('date_to', date_to.value);
    if(min_qty.value.trim()) p.set('min_qty', min_qty.value.trim());
    if(max_qty.value.trim()) p.set('max_qty', max_qty.value.trim());
    if(per_page.value) p.set('per_page', per_page.value);
    p.set('page', String(page));
    return p;
  }

  function skelRows(n=10){
    return Array.from({length:n}).map(()=>`
      <tr>
        <td><div class="ledg-skel" style="width:180px"></div><div class="ledg-skel" style="width:70px;margin-top:8px"></div></td>
        <td><div class="ledg-skel" style="width:70px"></div></td>
        <td><div class="ledg-skel" style="width:70px"></div></td>
        <td><div class="ledg-skel" style="width:90px"></div></td>
        <td><div class="ledg-skel" style="width:90px"></div></td>
        <td><div class="ledg-skel" style="width:160px"></div><div class="ledg-skel" style="width:120px;margin-top:8px"></div></td>
        <td><div class="ledg-skel" style="width:80px"></div></td>
      </tr>
    `).join('');
  }

  function badgeClass(dir){
    dir = String(dir||'').toUpperCase();
    if(dir === 'IN') return 'ledg-in';
    if(dir === 'OUT') return 'ledg-out';
    return 'ledg-warn';
  }

  function escapeHtml(s){
    return String(s ?? '')
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  async function load(page=1){
    const params = buildParams(page);
    const url = `${AJAX_URL}?${params.toString()}`;

    statusLine.textContent = 'Loading...';
    tbody.innerHTML = skelRows(10);

    try{
      const res = await fetch(url, { headers: { 'Accept':'application/json' }});
      if(!res.ok) throw new Error(await res.text());
      const json = await res.json();

      lastRows = json.rows || [];

      tbody.innerHTML = lastRows.map(r => {
        const dir = String(r.direction || '').toUpperCase();
        const ref = `${r.ref_type || '-'}#${(r.ref_id ?? '-')}`;
        return `
          <tr>
            <td>
              <div class="ledg-mini">${escapeHtml(r.created_at)}</div>
              <div class="ledg-mini ledg-mono">#${escapeHtml(r.id)}</div>
            </td>
            <td><b class="ledg-mono">${escapeHtml(r.product_batch_id)}</b></td>
            <td><b class="ledg-mono">${escapeHtml(r.location_id)}</b></td>
            <td><span class="ledg-badge ${badgeClass(dir)}"><i class="fas fa-right-left"></i> ${escapeHtml(dir)}</span></td>
            <td><b class="ledg-mono">${Number(r.qty || 0).toFixed(4)}</b></td>
            <td>
              <div class="ledg-mini">Ref</div>
              <div class="ledg-mono">${escapeHtml(ref)}</div>
              ${r.line_id ? `<div class="ledg-mini">Line ID: <span class="ledg-mono">${escapeHtml(r.line_id)}</span></div>` : ``}
            </td>
            <td>
              <button type="button" class="ledg-metaBtn" data-action="meta"
                data-title="Batch ${escapeHtml(r.product_batch_id)} • Location ${escapeHtml(r.location_id)} • ${escapeHtml(dir)}"
                data-meta="${escapeHtml(r.meta_json || '{}')}">
                <i class="fas fa-code"></i> Meta
              </button>
            </td>
          </tr>
        `;
      }).join('');

      paginationBox.innerHTML = json.pagination_html || '';
      const m = json.meta || {};
      const shown = m.count_on_page ?? lastRows.length;
      countPill.innerHTML = `<i class="fas fa-list"></i> ${shown} shown`;
      shownInfo.textContent = `Showing ${shown} of ${m.total ?? '?'} (page ${m.current_page ?? '?'} / ${m.last_page ?? '?'})`;
      statusLine.textContent = 'Ready';

      // Update URL query (nice UX) WITHOUT reload
      const newQs = buildParams(m.current_page || page);
      history.replaceState({}, '', `${location.pathname}?${newQs.toString()}`);

    }catch(err){
      console.error(err);
      statusLine.textContent = 'Failed to load';
      tbody.innerHTML = `<tr><td colspan="7"><b>Error:</b> ${escapeHtml(err.message)}</td></tr>`;
    }
  }

  // Pagination click interception
  paginationBox.addEventListener('click', (e) => {
    const a = e.target.closest('a');
    if(!a) return;
    const href = a.getAttribute('href') || '';
    if(!href) return;

    // prevent full reload
    e.preventDefault();

    try{
      const u = new URL(href, window.location.origin);
      const page = Number(u.searchParams.get('page') || 1);
      load(page);
    }catch(_){
      // ignore
    }
  });

  // debounced search
  const run = debounce(() => load(1), 260);
  [q,batch_id,location_id,ref_type,min_qty,max_qty].forEach(x => x.addEventListener('input', run));
  [direction,per_page,date_from,date_to].forEach(x => x.addEventListener('change', () => load(1)));

  // clear
  clearBtn.addEventListener('click', () => {
    q.value=''; batch_id.value=''; location_id.value=''; direction.value='';
    per_page.value='50'; date_from.value=''; date_to.value='';
    min_qty.value=''; max_qty.value=''; ref_type.value='';
    load(1);
  });

  // meta modal
  function openModal(title, jsonText){
    modalTitle.textContent = title || 'Meta';
    modalPre.textContent = jsonText || '{}';
    modal.classList.add('show');
    modal.setAttribute('aria-hidden','false');
  }
  function closeModal(){
    modal.classList.remove('show');
    modal.setAttribute('aria-hidden','true');
  }
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-action="meta"]');
    if(!btn) return;
    openModal(btn.getAttribute('data-title'), btn.getAttribute('data-meta'));
  });
  closeBtn.addEventListener('click', closeModal);
  modal.addEventListener('click', (e) => { if(e.target === modal) closeModal(); });
  document.addEventListener('keydown', (e) => { if(e.key === 'Escape') closeModal(); });

  copyBtn.addEventListener('click', async () => {
    try{
      await navigator.clipboard.writeText(modalPre.textContent || '');
      copyBtn.innerHTML = '<i class="fas fa-check"></i> Copied';
      setTimeout(()=>copyBtn.innerHTML='<i class="fas fa-copy"></i> Copy', 900);
    }catch(err){
      console.error(err);
      alert('Copy failed');
    }
  });

  // export current page (AJAX rows only)
  function csvVal(v){
    const s = String(v ?? '');
    if(/[",\n]/.test(s)) return `"${s.replaceAll('"','""')}"`;
    return s;
  }
  exportBtn.addEventListener('click', () => {
    const header = ['created_at','product_batch_id','location_id','direction','qty','ref_type','ref_id','line_id','meta_json'];
    const lines = [
      header.join(','),
      ...lastRows.map(r => ([
        r.created_at, r.product_batch_id, r.location_id, r.direction, r.qty,
        r.ref_type, r.ref_id, r.line_id, r.meta_json
      ]).map(csvVal).join(','))
    ];
    const blob = new Blob([lines.join('\n')], {type:'text/csv;charset=utf-8'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'stock_ledger_page.csv';
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);
  });

  // initial load: respect querystring if user refreshed
  const qs = new URLSearchParams(window.location.search);
  if(qs.get('q')) q.value = qs.get('q');
  if(qs.get('batch_id')) batch_id.value = qs.get('batch_id');
  if(qs.get('location_id')) location_id.value = qs.get('location_id');
  if(qs.get('direction')) direction.value = qs.get('direction');
  if(qs.get('ref_type')) ref_type.value = qs.get('ref_type');
  if(qs.get('date_from')) date_from.value = qs.get('date_from');
  if(qs.get('date_to')) date_to.value = qs.get('date_to');
  if(qs.get('min_qty')) min_qty.value = qs.get('min_qty');
  if(qs.get('max_qty')) max_qty.value = qs.get('max_qty');
  if(qs.get('per_page')) per_page.value = qs.get('per_page');

  const page = Number(qs.get('page') || 1);
  load(page);
})();
</script>
@endsection
