@csrf

<style>
/* =========================
   LOC FORM UI (unique: locf-)
   Works with your theme variables (light/dark)
   ========================= */
.locf-wrap{max-width:980px;margin:0 auto;}
.locf-card{
  background:var(--card);
  color:var(--foreground);
  border:1px solid var(--border-color);
  border-radius:var(--radius, .75rem);
  box-shadow:var(--card-shadow, 0 2px 6px rgba(0,0,0,.12));
  padding:16px;
}
.locf-head{display:flex;gap:12px;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;margin-bottom:12px;}
.locf-title{font-weight:800;font-size:1.1rem;display:flex;gap:10px;align-items:center;}
.locf-sub{color:var(--text-secondary);font-size:.92rem;margin-top:4px;}
.locf-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
@media (max-width: 820px){.locf-grid{grid-template-columns:1fr}}
.locf-field{display:flex;flex-direction:column;gap:6px;}
.locf-label{font-weight:700;font-size:.9rem;}
.locf-help{color:var(--text-secondary);font-size:.85rem;}
.locf-input,.locf-select,.locf-textarea{
  width:100%;
  padding:10px 12px;
  border-radius:calc(var(--radius, .75rem) - 2px);
  border:1px solid var(--border-color);
  background:var(--input);
  color:var(--foreground);
  outline:none;
  transition: border-color 150ms, box-shadow 150ms;
}
.locf-textarea{min-height:86px;resize:vertical;}
.locf-input:focus,.locf-select:focus,.locf-textarea:focus{
  border-color:var(--accent-color);
  box-shadow:0 0 0 3px var(--accent-glow);
}
.locf-invalid{border-color:var(--danger)!important; box-shadow:0 0 0 3px color-mix(in oklch, var(--danger) 20%, transparent 80%);}
.locf-error{color:var(--danger);font-size:.86rem;}
.locf-row{display:flex;gap:12px;align-items:center;flex-wrap:wrap;}
.locf-actions{display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;margin-top:14px;}
.locf-btn{
  border:1px solid var(--border-color);
  padding:10px 12px;
  border-radius:calc(var(--radius, .75rem) - 2px);
  cursor:pointer;
  text-decoration:none;
  display:inline-flex;
  align-items:center;
  gap:8px;
  transition: all 150ms;
}
.locf-btn-primary{
  background:var(--accent-color);
  color:#fff;
  border-color:transparent;
}
.locf-btn-primary:hover{background:var(--accent-hover); box-shadow:0 10px 22px -12px var(--accent-glow);}
.locf-btn-ghost{background:transparent;color:var(--foreground);}
.locf-btn-ghost:hover{background:var(--accent);border-color:var(--accent-color);}

.locf-pill{
  display:inline-flex;align-items:center;gap:8px;
  padding:6px 10px;border-radius:999px;
  border:1px solid var(--border-color);
  background:var(--bg-tertiary);
  font-size:.85rem;color:var(--foreground);
}

/* Toggle switch */
.locf-switch{display:flex;align-items:center;gap:10px;margin-top:6px;}
.locf-toggle{
  width:46px;height:26px;border-radius:999px;
  background:color-mix(in oklch, var(--muted) 80%, var(--card) 20%);
  border:1px solid var(--border-color);
  position:relative; cursor:pointer;
  transition: background 150ms;
}
.locf-toggle::after{
  content:"";
  width:22px;height:22px;border-radius:50%;
  background:var(--card);
  border:1px solid var(--border-color);
  position:absolute; top:1px; left:1px;
  transition: transform 150ms;
}
.locf-check{display:none;}
.locf-check:checked + .locf-toggle{
  background:color-mix(in oklch, var(--success) 55%, var(--card) 45%);
  border-color:color-mix(in oklch, var(--success) 55%, var(--border-color) 45%);
}
.locf-check:checked + .locf-toggle::after{transform:translateX(20px);}
</style>

<div class="locf-wrap">
  <div class="locf-card">
    <div class="locf-head">
      <div>
        <div class="locf-title">
          <i class="fas fa-location-dot"></i>
          {{ isset($location) ? 'Update Location' : 'Create Location' }}
        </div>
        <div class="locf-sub">Fill the details. Fields marked * are required.</div>
      </div>

      <div class="locf-row">
        <span class="locf-pill">
          <i class="fas fa-circle-info"></i>
          Type: <b id="locfTypePreview">{{ old('type', $location->type ?? 'store') }}</b>
        </span>
      </div>
    </div>

    <div class="locf-grid">

      {{-- Name --}}
      <div class="locf-field">
        <label class="locf-label">Name *</label>
        <input
          type="text"
          name="name"
          value="{{ old('name', $location->name ?? '') }}"
          class="locf-input @error('name') locf-invalid @enderror"
          placeholder="Warehouse Main / Store Gulshan"
          required
          autofocus
        >
        @error('name') <div class="locf-error">{{ $message }}</div> @enderror
        <div class="locf-help">Shown in transfers, stock, orders, and reports.</div>
      </div>

      {{-- Code --}}
      <div class="locf-field">
        <label class="locf-label">Code (optional)</label>
        <input
          type="text"
          name="code"
          id="locfCode"
          value="{{ old('code', $location->code ?? '') }}"
          class="locf-input @error('code') locf-invalid @enderror"
          placeholder="WH-01 / SHOP-01"
        >
        @error('code') <div class="locf-error">{{ $message }}</div> @enderror
        <div class="locf-help">Unique short code. Keep it uppercase (auto format).</div>
      </div>

      {{-- Type --}}
      <div class="locf-field">
        <label class="locf-label">Type *</label>
        <select
          name="type"
          id="locfType"
          class="locf-select @error('type') locf-invalid @enderror"
          required
        >
          @foreach($types as $key => $label)
            <option value="{{ $key }}" @selected(old('type', $location->type ?? 'store') === $key)>
              {{ $label }}
            </option>
          @endforeach
        </select>
        @error('type') <div class="locf-error">{{ $message }}</div> @enderror
        <div class="locf-help">Helps rules: POS / warehouse / return holding, etc.</div>
      </div>

      {{-- Active --}}
      <div class="locf-field">
        <label class="locf-label">Status</label>

        <div class="locf-switch">
          <input
            type="checkbox"
            class="locf-check"
            id="is_active"
            name="is_active"
            value="1"
            @checked(old('is_active', $location->is_active ?? true))
          >
          <label class="locf-toggle" for="is_active" title="Toggle active/inactive"></label>

          <div>
            <div style="font-weight:800;">
              <span id="locfActiveText">
                {{ old('is_active', $location->is_active ?? true) ? 'Active' : 'Inactive' }}
              </span>
            </div>
            <div class="locf-help">Inactive locations can be hidden from selection.</div>
          </div>
        </div>

        @error('is_active') <div class="locf-error">{{ $message }}</div> @enderror
      </div>

      {{-- Address (full row) --}}
      <div class="locf-field" style="grid-column:1/-1;">
        <label class="locf-label">Address</label>
        <textarea
          name="address"
          class="locf-textarea @error('address') locf-invalid @enderror"
          placeholder="Optional address..."
        >{{ old('address', $location->address ?? '') }}</textarea>
        @error('address') <div class="locf-error">{{ $message }}</div> @enderror
      </div>

      {{-- Notes (full row) --}}
      <div class="locf-field" style="grid-column:1/-1;">
        <label class="locf-label">Notes</label>
        <textarea
          name="notes"
          class="locf-textarea @error('notes') locf-invalid @enderror"
          placeholder="Internal notes..."
        >{{ old('notes', $location->notes ?? '') }}</textarea>
        @error('notes') <div class="locf-error">{{ $message }}</div> @enderror
        <div class="locf-help">Visible to staff only. Good for operating hours, contact person, etc.</div>
      </div>

    </div>

    <div class="locf-actions">
      <a href="{{ route('locations.index') }}" class="locf-btn locf-btn-ghost">
        <i class="fas fa-arrow-left"></i> Back
      </a>

      <button class="locf-btn locf-btn-primary" type="submit">
        <i class="fas fa-save"></i> {{ $buttonText ?? 'Save' }}
      </button>
    </div>
  </div>
</div>

<script>
(() => {
  const code = document.getElementById('locfCode');
  const type = document.getElementById('locfType');
  const typePreview = document.getElementById('locfTypePreview');
  const active = document.getElementById('is_active');
  const activeText = document.getElementById('locfActiveText');

  // Uppercase + strip spaces for code (optional)
  if(code){
    code.addEventListener('input', () => {
      code.value = (code.value || '').toUpperCase().replace(/\s+/g,'');
    });
  }

  // Type preview pill
  if(type && typePreview){
    const syncType = () => typePreview.textContent = type.value || '';
    type.addEventListener('change', syncType);
    syncType();
  }

  // Active text label
  if(active && activeText){
    const syncActive = () => activeText.textContent = active.checked ? 'Active' : 'Inactive';
    active.addEventListener('change', syncActive);
    syncActive();
  }
})();
</script>
