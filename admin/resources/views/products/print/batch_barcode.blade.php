@extends('layouts.app')

@section('title', 'Barcode Print')

@section('content')
@php
    $product = $batch->product;

    $productName = $product->name ?? 'Product';
    $barcodeValue = $product->barcode ?? '';
    $batchNo = $batch->batch_no ?? $batch->id;

    $exp = $batch->expiry_date?->format('Y-m-d');
    $mfg = $batch->manufacture_date?->format('Y-m-d');

    $brand = $product->brand->name ?? null;
    $category = $product->category->name ?? null;
@endphp

<div class="barcode-page">
    {{-- Header --}}
    <div class="barcode-header">
        <div class="barcode-header-left">
            <div class="barcode-title">Barcode Print</div>
            <div class="barcode-subtitle">
                <span class="pill">Product: <strong>{{ $productName }}</strong></span>
                <span class="pill">Batch: <strong>{{ $batchNo }}</strong></span>
                @if($exp) <span class="pill">Exp: <strong>{{ $exp }}</strong></span> @endif
            </div>
        </div>

        <div class="barcode-header-actions">
            <a href="{{ route('products.index') }}" class="btnx btnx-ghost">← Back to Products</a>

            @if($product)
                <a href="{{ route('products.edit', $product) }}" class="btnx btnx-ghost">Edit Product</a>
            @endif

            <button type="button" class="btnx btnx-primary" onclick="barcode_label_print_print_page()">
                Print Page
            </button>

            <button type="button" class="btnx btnx-success" onclick="barcode_label_print_print_label_only()">
                Print Label Only
            </button>
        </div>
    </div>

    {{-- Controls --}}
    <div class="barcode-controls">
        <div class="control">
            <label class="control-label">Page Type</label>
            <select class="control-input" id="barcode_label_print_page_type" onchange="barcode_label_print_apply_layout()">
                <option value="label" selected>Label (Single)</option>
                <option value="a4">A4 Sheet (Grid)</option>
            </select>
        </div>

        <div class="control">
            <label class="control-label">Barcode Type</label>
            <select class="control-input" id="barcode_label_print_barcode_type" onchange="barcode_label_print_render_barcode()">
                <option value="C128" selected>Code 128 (C128)</option>
                <option value="EAN13">EAN-13 (numeric 12/13)</option>
            </select>
        </div>

        <div class="control">
            <label class="control-label">Quantity</label>
            <input class="control-input" id="barcode_label_print_qty" type="number" min="1" value="1" oninput="barcode_label_print_apply_layout()">
        </div>

        <div class="control control-toggle">
            <label class="toggle">
                <input type="checkbox" id="barcode_label_print_show_details" checked onchange="barcode_label_print_toggle_details()">
                <span class="toggle-ui"></span>
                <span class="toggle-text">Show Details</span>
            </label>
        </div>
    </div>

    <div class="barcode-grid">
        {{-- Left: Preview / Print Area --}}
        <div class="cardx">
            <div class="cardx-head">
                <div>
                    <div class="cardx-title">Preview</div>
                    <div class="cardx-subtitle">This is what will be printed</div>
                </div>

                <div class="cardx-badges">
                    <span class="badge">50×30mm label</span>
                </div>
            </div>

            <div class="cardx-body">
                {{-- Print Area (Label or A4) --}}
                <div id="barcode_label_print_area">
                    {{-- Default single label container --}}
                    <div class="label-wrap" data-layout="label">
                        <div class="label">
                            <div class="label-name" title="{{ $productName }}">{{ $productName }}</div>

                            <div class="label-barcode" id="barcode_label_print_svg">
                                {!! \DNS1D::getBarcodeSVG($barcodeValue, 'C128', 2, 60) !!}
                            </div>

                            <div class="label-meta">
                                <div>Barcode: <strong>{{ $barcodeValue }}</strong></div>
                                <div>Batch: <strong>{{ $batchNo }}</strong></div>
                                @if($mfg) <div>MFG: <strong>{{ $mfg }}</strong></div> @endif
                                @if($exp) <div>EXP: <strong>{{ $exp }}</strong></div> @endif
                            </div>
                        </div>
                    </div>

                    {{-- A4 grid container (hidden by default) --}}
                    <div class="a4-wrap hidden" data-layout="a4" id="barcode_label_print_a4_wrap">
                        {{-- JS will fill labels here --}}
                    </div>
                </div>

                <div class="hint">
                    Tip: For sticker printer, use <strong>Print Label Only</strong>. For A4 sheet, use <strong>Print Page</strong>.
                </div>
            </div>
        </div>

        {{-- Right: Details --}}
        <div class="cardx" id="barcode_label_print_details_card">
            <div class="cardx-head">
                <div>
                    <div class="cardx-title">Information</div>
                    <div class="cardx-subtitle">Verify details before printing</div>
                </div>
            </div>

            <div class="cardx-body">
                <div class="infobox">
                    <div class="infotitle">Product</div>
                    <div class="inforow"><span>Name</span><strong>{{ $productName }}</strong></div>
                    <div class="inforow"><span>Barcode</span><strong>{{ $barcodeValue }}</strong></div>
                    <div class="inforow"><span>Brand</span><strong>{{ $brand ?? '—' }}</strong></div>
                    <div class="inforow"><span>Category</span><strong>{{ $category ?? '—' }}</strong></div>
                    <div class="inforow"><span>Status</span><strong>{{ ($product?->is_active ?? false) ? 'Active' : 'Inactive' }}</strong></div>
                </div>

                <div class="infobox">
                    <div class="infotitle">Batch</div>
                    <div class="inforow"><span>Batch No</span><strong>{{ $batchNo }}</strong></div>
                    <div class="inforow"><span>SKU</span><strong>{{ $batch->batch_sku ?? '—' }}</strong></div>
                    <div class="inforow"><span>Unit</span><strong>{{ $batch->unit ?? '—' }}</strong></div>
                    <div class="inforow"><span>Sell</span><strong>{{ $batch->sell_price ?? '—' }}</strong></div>
                    <div class="inforow"><span>Original</span><strong>{{ $batch->original_sell_price ?? '—' }}</strong></div>
                    <div class="inforow"><span>Discounted</span><strong>{{ $batch->discounted_price ?? '—' }}</strong></div>
                    <div class="inforow"><span>Disc%</span><strong>{{ $batch->discount_percentage ?? '—' }}</strong></div>
                    <div class="inforow"><span>MFG</span><strong>{{ $mfg ?? '—' }}</strong></div>
                    <div class="inforow"><span>EXP</span><strong>{{ $exp ?? '—' }}</strong></div>
                </div>

                <div class="infobox">
                    <div class="infotitle">Channels</div>
                    <div class="chips">
                        <span class="chip {{ $batch->is_online ? 'chip-on' : '' }}">Online</span>
                        <span class="chip {{ $batch->is_offline ? 'chip-on' : '' }}">Offline</span>
                        <span class="chip {{ $batch->is_pos ? 'chip-on' : '' }}">POS</span>
                        <span class="chip {{ $batch->is_active ? 'chip-on' : '' }}">Active</span>
                    </div>
                </div>

                @if(!empty($batch->notes))
                    <div class="infobox">
                        <div class="infotitle">Notes</div>
                        <div class="smallmuted">{{ $batch->notes }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Hidden values for JS --}}
<input type="hidden" id="barcode_label_print_barcode_value" value="{{ $barcodeValue }}">
<input type="hidden" id="barcode_label_print_product_name_value" value="{{ $productName }}">
<input type="hidden" id="barcode_label_print_batch_no_value" value="{{ $batchNo }}">
<input type="hidden" id="barcode_label_print_mfg_value" value="{{ $mfg ?? '' }}">
<input type="hidden" id="barcode_label_print_exp_value" value="{{ $exp ?? '' }}">


<style>
/* =======================
   Your Theme Variables
   ======================= */
:root {
    --radius: 0.625rem;

    --background: oklch(0.145 0 0);
    --foreground: oklch(0.985 0 0);
    --card: oklch(0.205 0 0);
    --card-foreground: oklch(0.985 0 0);
    --secondary: oklch(0.269 0 0);
    --muted-foreground: oklch(0.708 0 0);
    --border: oklch(1 0 0 / 15%);

    --sidebar-primary: oklch(0.488 0.243 264.376);

    --success: oklch(0.696 0.17 162.48);
    --warning: oklch(0.769 0.188 70.08);
    --danger: oklch(0.704 0.191 22.216);

    --card-shadow: 0 2px 4px 0 rgb(0 0 0 / 0.25);
    --card-shadow-hover: 0 6px 12px -1px rgb(0 0 0 / 0.35), 0 3px 6px -2px rgb(0 0 0 / 0.25);

    --accent-color: var(--sidebar-primary);
    --accent-hover: oklch(0.488 0.243 264.376 / 0.8);
    --bg-primary: var(--background);
    --bg-secondary: var(--card);
    --text-primary: var(--foreground);
    --text-secondary: var(--muted-foreground);
    --border-color: var(--border);
}

/* LIGHT MODE */
html[data-theme='light'] {
    --background: oklch(0.99 0 0);
    --foreground: oklch(0.12 0 0);
    --card: oklch(1 0 0);
    --card-foreground: oklch(0.12 0 0);
    --secondary: oklch(0.97 0 0);
    --muted-foreground: oklch(0.5 0 0);
    --border: oklch(0.9 0 0);
    --sidebar-primary: oklch(0.646 0.222 41.116);

    --success: oklch(0.627 0.194 149.214);
    --warning: oklch(0.769 0.188 70.08);
    --danger: oklch(0.577 0.245 27.325);

    --card-shadow: 0 2px 4px 0 rgb(0 0 0 / 0.08);
    --card-shadow-hover: 0 6px 12px -1px rgb(0 0 0 / 0.12), 0 3px 6px -2px rgb(0 0 0 / 0.08);

    --accent-color: var(--sidebar-primary);
    --accent-hover: oklch(0.646 0.222 41.116 / 0.8);
    --bg-primary: var(--background);
    --bg-secondary: var(--card);
    --text-primary: var(--foreground);
    --text-secondary: var(--muted-foreground);
    --border-color: var(--border);
}

/* =======================
   Page layout
   ======================= */
.barcode-page{
    color: var(--text-primary);
    background: var(--bg-primary);
    padding: 16px;
    border-radius: calc(var(--radius) + 6px);
}

.barcode-header{
    display:flex;
    gap:12px;
    align-items:flex-start;
    justify-content:space-between;
    flex-wrap:wrap;
    margin-bottom: 14px;
}
.barcode-title{
    font-size: 18px;
    font-weight: 800;
}
.barcode-subtitle{
    margin-top: 6px;
    display:flex;
    gap:8px;
    flex-wrap:wrap;
}
.pill{
    font-size: 12px;
    color: var(--text-secondary);
    background: color-mix(in oklch, var(--bg-secondary) 70%, transparent);
    border: 1px solid var(--border-color);
    padding: 6px 10px;
    border-radius: 999px;
}

.barcode-header-actions{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
}

/* Buttons */
.btnx{
    border: 1px solid var(--border-color);
    background: var(--bg-secondary);
    color: var(--text-primary);
    padding: 9px 12px;
    border-radius: 12px;
    text-decoration:none;
    cursor:pointer;
    transition: transform 150ms ease, box-shadow 150ms ease, background 150ms ease;
    box-shadow: var(--card-shadow);
    display:inline-flex;
    align-items:center;
    gap:8px;
    font-weight: 600;
    font-size: 13px;
}
.btnx:hover{
    transform: translateY(-1px);
    box-shadow: var(--card-shadow-hover);
}
.btnx-primary{
    background: color-mix(in oklch, var(--accent-color) 30%, var(--bg-secondary));
    border-color: color-mix(in oklch, var(--accent-color) 30%, var(--border-color));
}
.btnx-success{
    background: color-mix(in oklch, var(--success) 25%, var(--bg-secondary));
    border-color: color-mix(in oklch, var(--success) 30%, var(--border-color));
}
.btnx-ghost{
    background: transparent;
    box-shadow: none;
}

/* Controls */
.barcode-controls{
    display:flex;
    gap:12px;
    flex-wrap:wrap;
    padding: 12px;
    border: 1px solid var(--border-color);
    background: color-mix(in oklch, var(--bg-secondary) 85%, transparent);
    border-radius: 16px;
    margin-bottom: 14px;
}
.control{
    min-width: 190px;
    flex: 1;
}
.control-label{
    display:block;
    font-size: 12px;
    color: var(--text-secondary);
    margin-bottom: 6px;
}
.control-input{
    width:100%;
    padding: 10px 10px;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    background: var(--bg-secondary);
    color: var(--text-primary);
    outline: none;
}
.control-input:focus{
    border-color: color-mix(in oklch, var(--accent-color) 40%, var(--border-color));
    box-shadow: 0 0 0 4px color-mix(in oklch, var(--accent-color) 18%, transparent);
}
.control-toggle{ display:flex; align-items:flex-end; }
.toggle{ display:flex; align-items:center; gap:10px; user-select:none; }
.toggle input{ display:none; }
.toggle-ui{
    width: 44px; height: 26px;
    background: color-mix(in oklch, var(--secondary) 80%, transparent);
    border: 1px solid var(--border-color);
    border-radius: 999px;
    position: relative;
    transition: background 150ms ease;
}
.toggle-ui::after{
    content:"";
    width: 20px; height: 20px;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 999px;
    position:absolute;
    top: 50%;
    left: 3px;
    transform: translateY(-50%);
    transition: left 150ms ease;
}
.toggle input:checked + .toggle-ui{
    background: color-mix(in oklch, var(--accent-color) 30%, transparent);
}
.toggle input:checked + .toggle-ui::after{ left: 21px; }
.toggle-text{ color: var(--text-secondary); font-size: 13px; }

/* Cards */
.barcode-grid{
    display:grid;
    grid-template-columns: 1fr;
    gap: 12px;
}
@media (min-width: 992px){
    .barcode-grid{ grid-template-columns: 1.05fr .95fr; }
}
.cardx{
    border: 1px solid var(--border-color);
    background: var(--bg-secondary);
    border-radius: 18px;
    box-shadow: var(--card-shadow);
    overflow:hidden;
}
.cardx-head{
    padding: 12px 14px;
    border-bottom: 1px solid var(--border-color);
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap: 10px;
}
.cardx-title{ font-weight: 800; }
.cardx-subtitle{ color: var(--text-secondary); font-size: 12px; margin-top: 2px; }
.cardx-body{ padding: 14px; }
.badge{
    font-size: 12px;
    padding: 6px 10px;
    border-radius: 999px;
    border: 1px solid var(--border-color);
    color: var(--text-secondary);
    background: color-mix(in oklch, var(--secondary) 80%, transparent);
}

/* Label */
.label{
    width: 50mm;
    height: 30mm;
    padding: 6px;
    box-sizing: border-box;
    border-radius: 14px;
    border: 1px dashed color-mix(in oklch, var(--border-color) 65%, transparent);
    background: #fff; /* keeps label readable on dark theme */
    color: #000;
}
.label-name{
    font-size: 11px;
    font-weight: 800;
    text-align: center;
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.label-barcode{ text-align:center; margin: 2px 0; }
.label-barcode svg{ width:100%; height:auto; }
.label-meta{
    font-size: 9px;
    text-align:center;
    line-height: 1.2;
}
.hint{
    margin-top: 10px;
    color: var(--text-secondary);
    font-size: 12px;
}

/* A4 grid */
.a4-wrap{
    background: #fff;
    color:#000;
    border: 1px dashed rgba(0,0,0,.2);
    border-radius: 14px;
    padding: 10mm;
}
.a4-grid{
    display:grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 6mm;
}
.a4-grid .label{
    width: auto;
    height: 30mm;
}

/* Details */
.infobox{
    border: 1px solid var(--border-color);
    background: color-mix(in oklch, var(--secondary) 70%, transparent);
    border-radius: 16px;
    padding: 12px;
    margin-bottom: 10px;
}
.infotitle{
    font-weight: 800;
    margin-bottom: 8px;
}
.inforow{
    display:flex;
    justify-content:space-between;
    gap:10px;
    padding: 4px 0;
    border-bottom: 1px dashed color-mix(in oklch, var(--border-color) 70%, transparent);
    font-size: 13px;
}
.inforow:last-child{ border-bottom:0; }
.inforow span{ color: var(--text-secondary); }
.chips{ display:flex; gap:8px; flex-wrap:wrap; margin-top: 6px; }
.chip{
    font-size: 12px;
    padding: 6px 10px;
    border-radius: 999px;
    border: 1px solid var(--border-color);
    background: transparent;
    color: var(--text-secondary);
}
.chip-on{
    background: color-mix(in oklch, var(--accent-color) 22%, transparent);
    color: var(--text-primary);
}
.smallmuted{ color: var(--text-secondary); font-size: 13px; }

.hidden{ display:none !important; }

/* Print rules */
@media print{
    /* print the current layout only; page uses white */
    body{ background:#fff !important; }
    .barcode-header-actions,
    .barcode-controls,
    #barcode_label_print_details_card,
    .hint{
        display:none !important;
    }
    .barcode-page{
        padding: 0 !important;
        background:#fff !important;
        border-radius: 0 !important;
    }
    .cardx{ box-shadow:none !important; border:0 !important; }
    .cardx-head{ display:none !important; }
    .cardx-body{ padding: 0 !important; }
}
</style>

<script>
(function(){
  const valueEl = document.getElementById('barcode_label_print_barcode_value');
  const productNameEl = document.getElementById('barcode_label_print_product_name_value');
  const batchNoEl = document.getElementById('barcode_label_print_batch_no_value');
  const mfgEl = document.getElementById('barcode_label_print_mfg_value');
  const expEl = document.getElementById('barcode_label_print_exp_value');

  const pageTypeEl = document.getElementById('barcode_label_print_page_type');
  const qtyEl = document.getElementById('barcode_label_print_qty');
  const showDetailsEl = document.getElementById('barcode_label_print_show_details');
  const detailsCard = document.getElementById('barcode_label_print_details_card');

  const a4Wrap = document.getElementById('barcode_label_print_a4_wrap');
  const svgWrap = document.getElementById('barcode_label_print_svg');

  // Server-rendered SVG for C128 is already present.
  // If user switches barcode type, we just reload the page with a query param (simple & reliable).
  // (No dependency on JS barcode libs)
  window.barcode_label_print_render_barcode = function(){
      // You can also generate server-side by adding ?type=EAN13 etc.
      // For now, just show a warning if EAN13 chosen but barcode not numeric.
      const type = document.getElementById('barcode_label_print_barcode_type').value;
      const value = valueEl.value || '';

      if (type === 'EAN13' && !/^\d{12,13}$/.test(value)) {
          alert('EAN13 supports only 12/13 digit numeric barcode. Please use C128 for this barcode.');
          document.getElementById('barcode_label_print_barcode_type').value = 'C128';
      }
      // Note: The SVG shown is generated server-side (C128). If you want EAN13 SVG too,
      // I can update your controller to pass $type and render appropriate barcode.
  }

  window.barcode_label_print_toggle_details = function(){
      detailsCard.style.display = showDetailsEl.checked ? '' : 'none';
  }

  window.barcode_label_print_apply_layout = function(){
      const type = pageTypeEl.value;
      const qty = Math.max(1, parseInt(qtyEl.value || '1', 10));

      const labelWrap = document.querySelector('.label-wrap[data-layout="label"]');

      if (type === 'label') {
          labelWrap.classList.remove('hidden');
          a4Wrap.classList.add('hidden');
          return;
      }

      // A4 layout
      labelWrap.classList.add('hidden');
      a4Wrap.classList.remove('hidden');

      const value = valueEl.value || '';
      const productName = productNameEl.value || 'Product';
      const batchNo = batchNoEl.value || '';
      const mfg = mfgEl.value || '';
      const exp = expEl.value || '';

      // Build A4 grid
      const items = [];
      for (let i=0; i<qty; i++){
          items.push(`
            <div class="label">
              <div class="label-name" title="${escapeHtml(productName)}">${escapeHtml(productName)}</div>
              <div class="label-barcode">
                ${svgWrap.innerHTML}
              </div>
              <div class="label-meta">
                <div>Barcode: <strong>${escapeHtml(value)}</strong></div>
                <div>Batch: <strong>${escapeHtml(batchNo)}</strong></div>
                ${mfg ? `<div>MFG: <strong>${escapeHtml(mfg)}</strong></div>` : ``}
                ${exp ? `<div>EXP: <strong>${escapeHtml(exp)}</strong></div>` : ``}
              </div>
            </div>
          `);
      }

      a4Wrap.innerHTML = `<div class="a4-grid">${items.join('')}</div>`;
  }

  window.barcode_label_print_print_page = function(){
      window.print();
  }

  window.barcode_label_print_print_label_only = function(){
      // Print only the preview area by opening a clean print window
      const area = document.getElementById('barcode_label_print_area');
      if (!area) return;

      const html = area.innerHTML;

      const w = window.open('', '_blank', 'width=900,height=650');
      w.document.open();
      w.document.write(`
        <!doctype html>
        <html>
          <head>
            <meta charset="utf-8" />
            <title>Print Label</title>
            <style>
              @media print { body{ margin:0; padding:0; } }
              body { font-family: Arial, Helvetica, sans-serif; background:#fff; }
              .label{
                width: 50mm; height: 30mm; padding: 6px; box-sizing: border-box;
                border-radius: 10px;
              }
              .label-name{
                font-size: 11px; font-weight: 800; text-align:center; margin-bottom:4px;
                white-space: nowrap; overflow:hidden; text-overflow: ellipsis;
              }
              .label-barcode{ text-align:center; margin:2px 0; }
              .label-barcode svg{ width:100%; height:auto; }
              .label-meta{ font-size: 9px; text-align:center; line-height:1.2; }
              .a4-wrap{ padding: 10mm; }
              .a4-grid{ display:grid; grid-template-columns: repeat(3, 1fr); gap: 6mm; }
            </style>
          </head>
          <body onload="window.print(); window.close();">
            ${html}
          </body>
        </html>
      `);
      w.document.close();
  }

  function escapeHtml(s){
      return String(s).replace(/[&<>"']/g, function(m){
          return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]);
      });
  }

  // init
  barcode_label_print_toggle_details();
  barcode_label_print_apply_layout();
})();
</script>
@endsection
