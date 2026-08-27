{{-- resources/views/locations/index.blade.php --}}
@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

<style>

/* ===== Locations UI ===== */
.loc-wrap{max-width:100%;margin:0 auto;padding:16px;color:var(--foreground);}
.loc-top{display:flex;justify-content:space-between;gap:12px;align-items:flex-start;flex-wrap:wrap;margin-bottom:14px;}
.loc-title{font-size:1.55rem;font-weight:900;display:flex;align-items:center;gap:10px;}
.loc-sub{color:var(--text-secondary);font-size:.95rem;margin-top:6px;}
.loc-actions{display:flex;gap:10px;align-items:center;flex-wrap:wrap;}
.loc-btn{border:1px solid var(--border-color);padding:10px 12px;border-radius:calc(var(--radius) - 2px);cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all var(--transition-fast);background:transparent;color:var(--foreground);}
.loc-btn:hover{background:var(--bg-tertiary);border-color:var(--accent-color);}
.loc-btn-primary{background:var(--accent-color);border-color:transparent;color:#fff;}
.loc-btn-primary:hover{background:var(--accent-hover);box-shadow:0 8px 18px -10px var(--accent-glow);}
.loc-pill{display:inline-flex;align-items:center;gap:8px;border:1px solid var(--border-color);background:var(--bg-tertiary);padding:8px 10px;border-radius:999px;font-size:.85rem;}
.loc-card{background:var(--card);border:1px solid var(--border-color);border-radius:var(--radius);padding:14px;box-shadow:var(--card-shadow);margin-bottom:14px;}
.loc-grid{display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1fr;gap:10px;align-items:end;}
@media(max-width: 980px){.loc-grid{grid-template-columns:1fr 1fr;}}
@media(max-width: 520px){.loc-grid{grid-template-columns:1fr;}}
.loc-field label{display:block;font-size:.82rem;color:var(--text-secondary);margin-bottom:6px;}
.loc-input,.loc-select{
  width:100%;padding:10px 12px;border-radius:calc(var(--radius) - 2px);
  border:1px solid var(--border-color);background:var(--input);color:var(--foreground);outline:none;
}
.loc-input:focus,.loc-select:focus{border-color:var(--accent-color);box-shadow:0 0 0 3px var(--accent-glow);}
.loc-tableWrap{overflow:auto;border:1px solid var(--border-color);border-radius:var(--radius);}
.loc-table{width:100%;border-collapse:collapse;min-width:900px;}
.loc-table thead{background:var(--bg-tertiary);}
.loc-table th,.loc-table td{padding:12px;border-bottom:1px solid var(--border-color);text-align:left;vertical-align:middle;}
.loc-table tbody tr:hover{background:var(--bg-tertiary);}
.loc-mini{color:var(--text-secondary);font-size:.85rem;}
.loc-mono{font-family: ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;}
.loc-badge{display:inline-flex;align-items:center;gap:8px;border:1px solid var(--border-color);padding:6px 10px;border-radius:999px;font-size:.85rem;font-weight:800;}
.loc-on{background:color-mix(in oklch, var(--success) 30%, var(--card) 70%);}
.loc-off{background:color-mix(in oklch, var(--danger) 25%, var(--card) 75%);}
.loc-type{background:color-mix(in oklch, var(--accent-color) 18%, var(--card) 82%);}
.loc-foot{display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-top:12px;}
.loc-muted{color:var(--text-secondary);font-size:.9rem;}
.loc-toggle{display:inline-flex;align-items:center;gap:10px;}
.loc-switch{position:relative;width:46px;height:26px;border-radius:999px;border:1px solid var(--border-color);background:var(--bg-tertiary);cursor:pointer;transition:all var(--transition-fast);}
.loc-switch::after{content:"";position:absolute;top:3px;left:3px;width:20px;height:20px;border-radius:999px;background:var(--foreground);transition:all var(--transition-fast);opacity:.9;}
.loc-switch.on{background:color-mix(in oklch, var(--success) 25%, var(--bg-tertiary) 75%);border-color:color-mix(in oklch, var(--success) 45%, var(--border-color) 55%);}
.loc-switch.on::after{left:23px;}
.loc-iconBtn{border:1px solid var(--border-color);background:transparent;color:var(--foreground);border-radius:10px;padding:8px 10px;cursor:pointer;}
.loc-iconBtn:hover{background:var(--bg-tertiary);border-color:var(--accent-color);}
.loc-skel{height:14px;border-radius:999px;background:color-mix(in oklch, var(--bg-tertiary) 65%, var(--card) 35%);}
</style>

@php
  $ajaxUrl = route('locations.ajax');
@endphp

<div class="loc-wrap">
  <div class="loc-top">
    <div>
      <div class="loc-title"><i class="fas fa-map-location-dot"></i> Locations</div>
      <div class="loc-sub">AJAX search + filters + toggle status + delete without reload.</div>
    </div>
    <div class="loc-actions">
      <span class="loc-pill" id="locPill"><i class="fas fa-bolt"></i> Ready</span>
      <button type="button" class="loc-btn" id="locClear"><i class="fas fa-eraser"></i> Clear</button>
      <a href="{{ route('locations.create') }}" class="loc-btn loc-btn-primary"><i class="fas fa-plus"></i> Add Location</a>
    </div>
  </div>

  @if(session('success'))
    <div class="loc-card">
      <b><i class="fas fa-circle-check"></i> {{ session('success') }}</b>
    </div>
  @endif

  <div class="loc-card">
    <div class="loc-grid">
      <div class="loc-field">
        <label><i class="fas fa-magnifying-glass"></i> Search</label>
        <input class="loc-input" id="q" placeholder="name / code / id..." value="{{ $q ?? '' }}">
        <div class="loc-mini" style="margin-top:6px;">Live search (server-side).</div>
      </div>

      <div class="loc-field">
        <label><i class="fas fa-layer-group"></i> Type</label>
        <select class="loc-select" id="type">
          <option value="">All</option>
          @foreach(($types ?? []) as $k=>$lbl)
            <option value="{{ $k }}" @selected(($type ?? '') === $k)>{{ $lbl }}</option>
          @endforeach
        </select>
      </div>

      <div class="loc-field">
        <label><i class="fas fa-toggle-on"></i> Status</label>
        <select class="loc-select" id="status">
          <option value="">All</option>
          <option value="active" @selected(($status ?? '')==='active')>Active</option>
          <option value="inactive" @selected(($status ?? '')==='inactive')>Inactive</option>
        </select>
      </div>

      <div class="loc-field">
        <label><i class="fas fa-list"></i> Per Page</label>
        <select class="loc-select" id="per_page">
          <option value="10">10</option>
          <option value="15" selected>15</option>
          <option value="25">25</option>
          <option value="50">50</option>
        </select>
      </div>

      <div class="loc-field">
        <label>&nbsp;</label>
        <button class="loc-btn" type="button" id="locRefresh"><i class="fas fa-rotate"></i> Refresh</button>
      </div>
    </div>

    <div class="loc-foot">
      <div class="loc-muted" id="locStatusLine">Tip: toggle status instantly.</div>
      <span class="loc-pill" id="locShown">—</span>
    </div>
  </div>

  <div class="loc-card">
    <div class="loc-tableWrap">
      <table class="loc-table">
        <thead>
          <tr>
            <th style="width:90px;">#</th>
            <th>Name</th>
            <th style="width:160px;">Code</th>
            <th style="width:170px;">Type</th>
            <th style="width:220px;">Status</th>
            <th style="width:200px;">Actions</th>
          </tr>
        </thead>
        <tbody id="tbody">
          {{-- SSR fallback (will be replaced by AJAX) --}}
          @foreach($locations as $loc)
            <tr>
              <td class="loc-mono"><b>{{ $loc->id }}</b></td>
              <td>
                <div><b>{{ $loc->name }}</b></div>
                <div class="loc-mini">Created: {{ $loc->created_at }}</div>
              </td>
              <td class="loc-mono">{{ $loc->code ?? '—' }}</td>
              <td><span class="loc-badge loc-type"><i class="fas fa-tag"></i> {{ ucfirst(str_replace('_',' ', $loc->type)) }}</span></td>
              <td>
                <span class="loc-badge {{ $loc->is_active ? 'loc-on' : 'loc-off' }}">
                  <i class="fas fa-circle"></i> {{ $loc->is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td>
                <a class="loc-iconBtn" href="{{ route('locations.edit', $loc) }}"><i class="fas fa-pen"></i> Edit</a>
                <form action="{{ route('locations.destroy', $loc) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this location?')">
                  @csrf @method('DELETE')
                  <button class="loc-iconBtn" type="submit"><i class="fas fa-trash"></i> Delete</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="loc-foot">
      <div class="loc-muted" id="locInfo">Showing initial page…</div>
      <div id="paginationBox">
        {{ $locations->links() }}
      </div>
    </div>
  </div>
</div>

<script>
(() => {
  const AJAX_URL = @json($ajaxUrl);
  const csrf = @json(csrf_token());

  const el = (id) => document.getElementById(id);

  const q = el('q');
  const type = el('type');
  const status = el('status');
  const perPage = el('per_page');

  const tbody = el('tbody');
  const paginationBox = el('paginationBox');

  const pill = el('locPill');
  const shown = el('locShown');
  const info = el('locInfo');
  const statusLine = el('locStatusLine');

  const clearBtn = el('locClear');
  const refreshBtn = el('locRefresh');

  let lastMeta = null;

  const debounce = (fn, d=250) => { let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a), d);} };

  function escapeHtml(s){
    return String(s ?? '')
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  function buildParams(page=1){
    const p = new URLSearchParams();
    if(q.value.trim()) p.set('q', q.value.trim());
    if(type.value) p.set('type', type.value);
    if(status.value) p.set('status', status.value);
    if(perPage.value) p.set('per_page', perPage.value);
    p.set('page', String(page));
    return p;
  }

  function skelRows(n=8){
    return Array.from({length:n}).map(()=>`
      <tr>
        <td><div class="loc-skel" style="width:50px"></div></td>
        <td><div class="loc-skel" style="width:220px"></div><div class="loc-skel" style="width:120px;margin-top:8px"></div></td>
        <td><div class="loc-skel" style="width:90px"></div></td>
        <td><div class="loc-skel" style="width:120px"></div></td>
        <td><div class="loc-skel" style="width:140px"></div></td>
        <td><div class="loc-skel" style="width:160px"></div></td>
      </tr>
    `).join('');
  }

  function typeLabel(t){
    const map = @json($types ?? []);
    if(map[t]) return map[t];
    return t ? t.replaceAll('_',' ') : '-';
  }

  async function load(page=1){
    const params = buildParams(page);
    const url = `${AJAX_URL}?${params.toString()}`;

    pill.innerHTML = `<i class="fas fa-rotate fa-spin"></i> Loading`;
    statusLine.textContent = 'Loading...';
    tbody.innerHTML = skelRows(8);

    try{
      const res = await fetch(url, { headers: { 'Accept': 'application/json' }});
      if(!res.ok) throw new Error(await res.text());
      const json = await res.json();

      lastMeta = json.meta || null;

      tbody.innerHTML = (json.rows || []).map(r => {
        const active = !!r.is_active;
        const statusBadge = active
          ? `<span class="loc-badge loc-on"><i class="fas fa-circle"></i> Active</span>`
          : `<span class="loc-badge loc-off"><i class="fas fa-circle"></i> Inactive</span>`;

        return `
          <tr data-id="${escapeHtml(r.id)}">
            <td class="loc-mono"><b>${escapeHtml(r.id)}</b></td>
            <td>
              <div><b>${escapeHtml(r.name)}</b></div>
              <div class="loc-mini">ID: <span class="loc-mono">${escapeHtml(r.id)}</span></div>
            </td>
            <td class="loc-mono">${escapeHtml(r.code || '—')}</td>
            <td>
              <span class="loc-badge loc-type"><i class="fas fa-tag"></i> ${escapeHtml(typeLabel(r.type))}</span>
            </td>
            <td>
              <div class="loc-toggle">
                <div class="loc-switch ${active ? 'on' : ''}" data-action="toggle" data-url="${escapeHtml(r.toggle_url)}" title="Toggle active"></div>
                ${statusBadge}
              </div>
              <div class="loc-mini">Type: ${escapeHtml(r.type)}</div>
            </td>
            <td>
              <a class="loc-iconBtn" href="${escapeHtml(r.edit_url)}"><i class="fas fa-pen"></i> Edit</a>
              <button class="loc-iconBtn" type="button" data-action="delete" data-url="${escapeHtml(r.delete_url)}">
                <i class="fas fa-trash"></i> Delete
              </button>
            </td>
          </tr>
        `;
      }).join('');

      paginationBox.innerHTML = json.pagination_html || '';

      const m = json.meta || {};
      const shownCount = m.count_on_page ?? 0;

      pill.innerHTML = `<i class="fas fa-bolt"></i> Ready`;
      shown.textContent = `${shownCount} shown`;
      info.textContent = `Showing ${shownCount} of ${m.total ?? '?'} (page ${m.current_page ?? '?'} / ${m.last_page ?? '?'})`;
      statusLine.textContent = 'Ready';

      // update browser querystring (nice UX)
      const newQs = buildParams(m.current_page || page);
      history.replaceState({}, '', `${location.pathname}?${newQs.toString()}`);

    }catch(err){
      console.error(err);
      pill.innerHTML = `<i class="fas fa-triangle-exclamation"></i> Error`;
      statusLine.textContent = 'Failed to load';
      tbody.innerHTML = `<tr><td colspan="6"><b>Error:</b> ${escapeHtml(err.message)}</td></tr>`;
    }
  }

  // paginate via ajax
  paginationBox.addEventListener('click', (e) => {
    const a = e.target.closest('a');
    if(!a) return;
    e.preventDefault();
    try{
      const u = new URL(a.getAttribute('href'), window.location.origin);
      const page = Number(u.searchParams.get('page') || 1);
      load(page);
    }catch(_){}
  });

  // toggle + delete actions
  document.addEventListener('click', async (e) => {
    const toggle = e.target.closest('[data-action="toggle"]');
    if(toggle){
      const url = toggle.getAttribute('data-url');
      if(!url) return;

      toggle.style.pointerEvents = 'none';
      statusLine.textContent = 'Updating...';

      try{
        const res = await fetch(url, {
          method: 'PATCH',
          headers: {
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json',
          }
        });
        if(!res.ok) throw new Error(await res.text());
        const json = await res.json();

        // update this row without reload
        const tr = toggle.closest('tr');
        const badgeCell = tr.querySelector('td:nth-child(5)');
        const isActive = !!json.is_active;
        toggle.classList.toggle('on', isActive);

        // rebuild status badge
        const statusBadge = isActive
          ? `<span class="loc-badge loc-on"><i class="fas fa-circle"></i> Active</span>`
          : `<span class="loc-badge loc-off"><i class="fas fa-circle"></i> Inactive</span>`;

        // keep switch + badge inside
        const toggleWrap = badgeCell.querySelector('.loc-toggle');
        if(toggleWrap){
          toggleWrap.innerHTML = `
            <div class="loc-switch ${isActive ? 'on' : ''}" data-action="toggle" data-url="${escapeHtml(url)}" title="Toggle active"></div>
            ${statusBadge}
          `;
        }

        statusLine.textContent = 'Updated';
      }catch(err){
        console.error(err);
        alert('Toggle failed');
        statusLine.textContent = 'Toggle failed';
      }finally{
        toggle.style.pointerEvents = '';
        setTimeout(()=>statusLine.textContent='Ready', 700);
      }

      return;
    }

    const del = e.target.closest('[data-action="delete"]');
    if(del){
      const url = del.getAttribute('data-url');
      if(!url) return;
      if(!confirm('Delete this location?')) return;

      del.disabled = true;
      statusLine.textContent = 'Deleting...';

      try{
        const res = await fetch(url, {
          method: 'DELETE',
          headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
        });
        if(!res.ok) throw new Error(await res.text());

        // remove row
        const tr = del.closest('tr');
        tr.remove();

        // optionally reload to refresh pagination counts
        await load(lastMeta?.current_page || 1);

      }catch(err){
        console.error(err);
        alert('Delete failed');
        statusLine.textContent = 'Delete failed';
        del.disabled = false;
      }

      return;
    }
  });

  // inputs -> ajax
  const run = debounce(() => load(1), 260);
  q.addEventListener('input', run);
  type.addEventListener('change', () => load(1));
  status.addEventListener('change', () => load(1));
  perPage.addEventListener('change', () => load(1));

  refreshBtn.addEventListener('click', () => load(1));
  clearBtn.addEventListener('click', () => {
    q.value=''; type.value=''; status.value='';
    perPage.value='15';
    load(1);
  });

  // initial load respects querystring
  const qs = new URLSearchParams(window.location.search);
  if(qs.get('q')) q.value = qs.get('q');
  if(qs.get('type')) type.value = qs.get('type');
  if(qs.get('status')) status.value = qs.get('status');
  if(qs.get('per_page')) perPage.value = qs.get('per_page');

  const page = Number(qs.get('page') || 1);
  load(page);
})();
</script>
@endsection
