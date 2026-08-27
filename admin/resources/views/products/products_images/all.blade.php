@extends('layouts.app')

@section('title', 'All Product Images')

@section('content')

    <style>
        .pi-page { max-width: 1400px; margin: 0 auto; }

        .pi-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
        .pi-head .eyebrow { font-family: var(--font-mono); font-size: .72rem; text-transform: uppercase; letter-spacing: .08em; color: var(--primary); font-weight: 600; margin-bottom: .35rem; }
        .pi-head h1 { font-size: clamp(1.4rem, 2.2vw, 1.85rem); margin-bottom: .3rem; }
        .pi-head p { color: var(--muted-foreground); font-size: .88rem; }

        .pi-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.25rem; }
        @media (max-width: 720px) { .pi-stats { grid-template-columns: repeat(2, 1fr); } }

        .pi-toolbar {
            display: flex; align-items: center; gap: .65rem; flex-wrap: wrap;
            background: var(--card); border: 1px solid var(--border); border-radius: var(--radius);
            padding: .85rem 1rem; margin-bottom: 1.25rem; box-shadow: var(--card-shadow);
        }
        .pi-search { position: relative; flex: 1 1 240px; min-width: 200px; }
        .pi-search svg { position: absolute; left: .75rem; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--muted-foreground); pointer-events: none; }
        .pi-search input { padding-left: 2.25rem; }
        .pi-toolbar select { width: auto; min-width: 130px; }
        .pi-view-toggle { display: inline-flex; border: 1px solid var(--border); border-radius: calc(var(--radius) - 2px); overflow: hidden; flex-shrink: 0; margin-left: auto; }
        .pi-view-toggle button {
            background: var(--secondary); color: var(--muted-foreground); border: none; padding: .55rem .75rem;
            cursor: pointer; display: flex; align-items: center; transition: background-color .15s ease, color .15s ease;
        }
        .pi-view-toggle button.is-active { background: var(--primary); color: var(--primary-foreground); }
        .pi-view-toggle button svg { width: 16px; height: 16px; }

        /* Grid view */
        .pi-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 1rem; }
        .pi-card { border: 1px solid var(--border); border-radius: var(--radius); background: var(--card); overflow: hidden; box-shadow: var(--card-shadow); transition: transform .2s var(--ease), box-shadow .2s var(--ease); position: relative; }
        .pi-card:hover { transform: translateY(-3px); box-shadow: var(--card-shadow-hover); }
        .pi-card-thumb { position: relative; aspect-ratio: 1 / 1; background: var(--secondary); cursor: zoom-in; }
        .pi-card-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .pi-card-check { position: absolute; top: .5rem; left: .5rem; width: 20px; height: 20px; z-index: 2; accent-color: var(--primary); cursor: pointer; }
        .pi-card-primary { position: absolute; top: .5rem; right: .5rem; z-index: 2; }
        .pi-card-body { padding: .65rem .75rem .75rem; }
        .pi-card-product { font-size: .82rem; font-weight: 600; color: var(--foreground); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .pi-card-id { font-size: .72rem; font-family: var(--font-mono); color: var(--muted-foreground); margin-bottom: .55rem; }
        .pi-card-actions { display: flex; gap: .35rem; }
        .pi-icon-btn {
            flex: 1; display: flex; align-items: center; justify-content: center; padding: .4rem; border-radius: calc(var(--radius) - 3px);
            background: var(--secondary); color: var(--muted-foreground); border: none; cursor: pointer; transition: background-color .15s ease, color .15s ease;
        }
        .pi-icon-btn svg { width: 14px; height: 14px; }
        .pi-icon-btn:hover { background: var(--accent); color: var(--foreground); }
        .pi-icon-btn.danger:hover { background: color-mix(in oklch, var(--danger) 18%, var(--card)); color: var(--danger); }
        .pi-icon-btn.success:hover { background: color-mix(in oklch, var(--success) 18%, var(--card)); color: var(--success); }
        .pi-icon-btn:disabled { opacity: .4; cursor: not-allowed; }

        /* Table view */
        .pi-table-check { width: 34px; }
        .pi-table-thumb { width: 60px; height: 60px; object-fit: cover; border-radius: calc(var(--radius) - 3px); cursor: zoom-in; }
        .pi-table-actions { display: flex; flex-wrap: wrap; gap: .4rem; }

        .pi-empty { padding: 3rem 1rem; text-align: center; color: var(--muted-foreground); }
        .pi-empty svg { width: 40px; height: 40px; margin-bottom: .75rem; opacity: .5; }

        /* Bulk action bar */
        .pi-bulkbar {
            position: sticky; bottom: 1rem; z-index: 20; display: none; align-items: center; justify-content: space-between; gap: 1rem;
            background: var(--foreground); color: var(--background); border-radius: var(--radius); padding: .85rem 1.25rem;
            box-shadow: var(--dropdown-shadow); margin-top: 1.25rem;
        }
        .pi-bulkbar.is-visible { display: flex; }
        .pi-bulkbar span { font-size: .85rem; font-weight: 600; }
        .pi-bulkbar .btn-group { display: flex; gap: .6rem; }

        /* Lightbox */
        .pi-lightbox {
            position: fixed; inset: 0; z-index: 200; display: none; align-items: center; justify-content: center;
            background: rgba(0,0,0,.75); backdrop-filter: blur(4px); padding: 2rem;
        }
        .pi-lightbox.is-open { display: flex; }
        .pi-lightbox-inner { background: var(--card); border-radius: var(--radius); overflow: hidden; max-width: 900px; width: 100%; max-height: 90vh; display: flex; flex-wrap: wrap; }
        .pi-lightbox-img { flex: 1 1 400px; background: #000; display: flex; align-items: center; justify-content: center; max-height: 90vh; }
        .pi-lightbox-img img { max-width: 100%; max-height: 90vh; object-fit: contain; }
        .pi-lightbox-info { flex: 1 1 260px; padding: 1.5rem; display: flex; flex-direction: column; gap: .5rem; }
        .pi-lightbox-close { position: absolute; top: 1.25rem; right: 1.5rem; color: #fff; background: rgba(255,255,255,.15); border: none; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; }
        .pi-lightbox-close svg { width: 18px; height: 18px; }
    </style>

    <div class="pi-page" data-reveal-group>

        <div class="pi-head" data-reveal>
            <div>
                <p class="eyebrow">Media Library</p>
                <h1>All Product Images</h1>
                <p>Search, filter, and manage images across every product from one place.</p>
            </div>
            <a href="{{ route('product-images.trash') }}" class="btn-outline">
                <i data-lucide="trash-2" class="w-4 h-4"></i> Trash ({{ $trashedImageCount ?? 0 }})
            </a>
        </div>

        @if (session('success'))
            <div class="ui-alert ui-alert-success" style="margin-bottom: 1.25rem;">
                <i data-lucide="check-circle" class="w-5 h-5"></i><div>{{ session('success') }}</div>
            </div>
        @endif
        @if (session('info'))
            <div class="ui-alert ui-alert-info" style="margin-bottom: 1.25rem;">
                <i data-lucide="info" class="w-5 h-5"></i><div>{{ session('info') }}</div>
            </div>
        @endif
        @if (session('error'))
            <div class="ui-alert ui-alert-danger" style="margin-bottom: 1.25rem;">
                <i data-lucide="alert-circle" class="w-5 h-5"></i><div>{{ session('error') }}</div>
            </div>
        @endif

        <div class="pi-stats" data-reveal>
            <x-stat title="Total Images" :value="$totalImageCount ?? $images->total()" />
            <x-stat title="Primary Set" :value="$primaryCount ?? 0" />
            <x-stat title="Orphaned" :value="$orphanCount ?? 0" :negative="($orphanCount ?? 0) > 0" />
            <x-stat title="In Trash" :value="$trashedImageCount ?? 0" />
        </div>

        <form method="GET" action="{{ route('product.images.all') }}" id="pi-filters" class="pi-toolbar" data-reveal>
            <div class="pi-search">
                <i data-lucide="search"></i>
                <input type="text" name="q" class="ui-input" placeholder="Search by product name…" value="{{ $search ?? '' }}">
            </div>

            <select name="filter" class="ui-select" onchange="document.getElementById('pi-filters').submit()">
                <option value="all" @selected(($filter ?? 'all') === 'all')>All Images</option>
                <option value="primary" @selected(($filter ?? '') === 'primary')>Primary Only</option>
                <option value="no_primary" @selected(($filter ?? '') === 'no_primary')>Non-Primary</option>
                <option value="orphan" @selected(($filter ?? '') === 'orphan')>Orphaned</option>
            </select>

            <select name="sort" class="ui-select" onchange="document.getElementById('pi-filters').submit()">
                <option value="newest" @selected(($sort ?? 'newest') === 'newest')>Newest First</option>
                <option value="oldest" @selected(($sort ?? '') === 'oldest')>Oldest First</option>
            </select>

            <select name="per_page" class="ui-select" onchange="document.getElementById('pi-filters').submit()">
                @foreach ([12, 24, 48, 96] as $pp)
                    <option value="{{ $pp }}" @selected(($perPage ?? 24) == $pp)>{{ $pp }} / page</option>
                @endforeach
            </select>

            <button type="submit" class="btn-secondary">
                <i data-lucide="filter" class="w-4 h-4"></i> Apply
            </button>

            @if (($search ?? '') !== '' || ($filter ?? 'all') !== 'all' || ($sort ?? 'newest') !== 'newest')
                <a href="{{ route('product.images.all') }}" class="btn-ghost">
                    <i data-lucide="x" class="w-4 h-4"></i> Clear
                </a>
            @endif

            <div class="pi-view-toggle" id="pi-view-toggle">
                <button type="button" data-view="grid" title="Grid view"><i data-lucide="layout-grid"></i></button>
                <button type="button" data-view="table" title="Table view"><i data-lucide="list"></i></button>
            </div>
        </form>

        {{-- ============ Bulk selection form (hidden ids injected by JS on submit) ============ --}}
        <form method="POST" action="{{ route('product-images.bulkTrash') }}" id="pi-bulk-form">
            @csrf
        </form>

        @if ($images->isEmpty())
            <div class="ui-card pi-empty" data-reveal>
                <i data-lucide="image-off"></i>
                <p>No images match your current search &amp; filters.</p>
            </div>
        @else

            {{-- ============ Grid view ============ --}}
            <div class="pi-grid" id="pi-grid-view" data-reveal>
                @foreach ($images as $img)
                    @php $p = $img->product; @endphp
                    <div class="pi-card" data-img-id="{{ $img->id }}">
                        <div class="pi-card-thumb" data-lightbox-trigger
                             data-src="{{ asset('storage/' . $img->image_path) }}"
                             data-product="{{ $p->name ?? 'Orphaned image' }}"
                             data-pid="{{ $p->id ?? '' }}"
                             data-primary="{{ $img->is_primary ? '1' : '0' }}">
                            <input type="checkbox" class="pi-card-check pi-select" value="{{ $img->id }}">
                            @if ($img->is_primary)
                                <span class="badge badge-success pi-card-primary"><span class="badge-dot"></span> Primary</span>
                            @elseif (!$p)
                                <span class="badge badge-warning pi-card-primary">Orphan</span>
                            @endif
                            <img src="{{ asset('storage/' . $img->image_path) }}" alt="{{ $p->name ?? 'Orphaned image' }}" loading="lazy">
                        </div>
                        <div class="pi-card-body">
                            @if ($p)
                                <div class="pi-card-product" title="{{ $p->name }}">{{ $p->name }}</div>
                                <div class="pi-card-id">#{{ $p->id }}</div>
                            @else
                                <div class="pi-card-product" style="color: var(--warning);">Orphaned</div>
                                <div class="pi-card-id">product deleted</div>
                            @endif

                            <div class="pi-card-actions">
                                @if ($p)
                                    <a class="pi-icon-btn" href="{{ route('products.images.index', $p->id) }}" title="Manage">
                                        <i data-lucide="image"></i>
                                    </a>
                                    <form method="POST" action="{{ route('products.images.primary', [$p->id, $img->id]) }}">
                                        @csrf
                                        <button type="submit" class="pi-icon-btn success" title="Set primary" onclick="return confirm('Set as primary image?')" {{ $img->is_primary ? 'disabled' : '' }}>
                                            <i data-lucide="star"></i>
                                        </button>
                                    </form>
                                @else
                                    <button class="pi-icon-btn" disabled title="Manage"><i data-lucide="image"></i></button>
                                    <button class="pi-icon-btn" disabled title="Set primary"><i data-lucide="star"></i></button>
                                @endif
                                <form method="POST" action="{{ route('product-images.deleteById', $img->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="pi-icon-btn danger" title="Trash" onclick="return confirm('Move to trash?')">
                                        <i data-lucide="trash-2"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ============ Table view ============ --}}
            <div class="ui-table-wrap" id="pi-table-view" style="display:none;" data-reveal>
                <table>
                    <thead>
                        <tr>
                            <th class="pi-table-check"><input type="checkbox" id="pi-select-all"></th>
                            <th>Preview</th>
                            <th>Product</th>
                            <th>Primary</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($images as $img)
                            @php $p = $img->product; @endphp
                            <tr data-img-id="{{ $img->id }}">
                                <td><input type="checkbox" class="pi-select" value="{{ $img->id }}"></td>
                                <td>
                                    <img src="{{ asset('storage/' . $img->image_path) }}" alt="{{ $p->name ?? 'Orphaned image' }}"
                                         class="pi-table-thumb" loading="lazy"
                                         data-lightbox-trigger
                                         data-src="{{ asset('storage/' . $img->image_path) }}"
                                         data-product="{{ $p->name ?? 'Orphaned image' }}"
                                         data-pid="{{ $p->id ?? '' }}"
                                         data-primary="{{ $img->is_primary ? '1' : '0' }}">
                                </td>
                                <td>
                                    @if ($p)
                                        <div style="font-weight:600;">{{ $p->name }}</div>
                                        <div style="font-size:.78rem; color:var(--muted-foreground);">#{{ $p->id }}</div>
                                    @else
                                        <span class="badge badge-warning">Orphan (product deleted)</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($img->is_primary)
                                        <span class="badge badge-success"><span class="badge-dot"></span> Yes</span>
                                    @else
                                        <span class="badge badge-outline">No</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="pi-table-actions">
                                        @if ($p)
                                            <a class="btn-outline" style="padding:.4rem .7rem; font-size:.76rem;" href="{{ route('products.images.index', $p->id) }}">
                                                <i data-lucide="image" class="w-3.5 h-3.5"></i> Manage
                                            </a>
                                            <form method="POST" action="{{ route('products.images.primary', [$p->id, $img->id]) }}">
                                                @csrf
                                                <button type="submit" class="btn-secondary" style="padding:.4rem .7rem; font-size:.76rem;" onclick="return confirm('Set as primary?')" {{ $img->is_primary ? 'disabled' : '' }}>
                                                    <i data-lucide="star" class="w-3.5 h-3.5"></i> Primary
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn-outline" style="padding:.4rem .7rem; font-size:.76rem;" disabled>Manage</button>
                                            <button class="btn-secondary" style="padding:.4rem .7rem; font-size:.76rem;" disabled>Primary</button>
                                        @endif
                                        <form method="POST" action="{{ route('product-images.deleteById', $img->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-destructive" style="padding:.4rem .7rem; font-size:.76rem;" onclick="return confirm('Move to trash?')">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Trash
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 1.5rem;">
                {{ $images->onEachSide(1)->links('vendor.pagination.custom') }}
            </div>
        @endif

        {{-- ============ Bulk action bar ============ --}}
        <div class="pi-bulkbar" id="pi-bulkbar">
            <span id="pi-bulk-count">0 selected</span>
            <div class="btn-group">
                <button type="button" class="btn-secondary" id="pi-bulk-clear">Clear</button>
                <button type="button" class="btn-destructive" id="pi-bulk-trash">
                    <i data-lucide="trash-2" class="w-4 h-4"></i> Trash Selected
                </button>
            </div>
        </div>
    </div>

    {{-- ============ Lightbox ============ --}}
    <div class="pi-lightbox" id="pi-lightbox">
        <button type="button" class="pi-lightbox-close" id="pi-lightbox-close"><i data-lucide="x"></i></button>
        <div class="pi-lightbox-inner">
            <div class="pi-lightbox-img"><img id="pi-lightbox-img" src="" alt=""></div>
            <div class="pi-lightbox-info">
                <p class="eyebrow" style="font-family: var(--font-mono); font-size: .72rem; text-transform: uppercase; letter-spacing: .08em; color: var(--primary); font-weight: 600;">Preview</p>
                <h3 id="pi-lightbox-product" style="font-size: 1.15rem;"></h3>
                <div id="pi-lightbox-badge"></div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const selected = new Set();
            const bulkbar = document.getElementById('pi-bulkbar');
            const bulkCount = document.getElementById('pi-bulk-count');

            function syncCheckboxes() {
                document.querySelectorAll('.pi-select').forEach((cb) => {
                    cb.checked = selected.has(cb.value);
                });
                bulkCount.textContent = selected.size + ' selected';
                bulkbar.classList.toggle('is-visible', selected.size > 0);
            }

            document.addEventListener('change', (e) => {
                if (!e.target.classList.contains('pi-select')) return;
                if (e.target.checked) selected.add(e.target.value);
                else selected.delete(e.target.value);
                syncCheckboxes();
            });

            const selectAll = document.getElementById('pi-select-all');
            if (selectAll) {
                selectAll.addEventListener('change', () => {
                    document.querySelectorAll('#pi-table-view .pi-select').forEach((cb) => {
                        if (selectAll.checked) selected.add(cb.value); else selected.delete(cb.value);
                    });
                    syncCheckboxes();
                });
            }

            document.getElementById('pi-bulk-clear')?.addEventListener('click', () => {
                selected.clear();
                if (selectAll) selectAll.checked = false;
                syncCheckboxes();
            });

            document.getElementById('pi-bulk-trash')?.addEventListener('click', () => {
                if (!selected.size) return;
                if (!confirm(`Move ${selected.size} image(s) to trash?`)) return;
                const form = document.getElementById('pi-bulk-form');
                selected.forEach((id) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    form.appendChild(input);
                });
                form.submit();
            });

            // View toggle (persisted per-browser)
            const gridView = document.getElementById('pi-grid-view');
            const tableView = document.getElementById('pi-table-view');
            const toggleBtns = document.querySelectorAll('#pi-view-toggle button');
            const STORAGE_KEY = 'pi_view_mode';

            function setView(mode) {
                if (!gridView || !tableView) return;
                gridView.style.display = mode === 'grid' ? 'grid' : 'none';
                tableView.style.display = mode === 'table' ? 'block' : 'none';
                toggleBtns.forEach((b) => b.classList.toggle('is-active', b.dataset.view === mode));
                try { localStorage.setItem(STORAGE_KEY, mode); } catch (e) {}
            }

            toggleBtns.forEach((btn) => btn.addEventListener('click', () => setView(btn.dataset.view)));

            let savedMode = 'grid';
            try { savedMode = localStorage.getItem(STORAGE_KEY) || 'grid'; } catch (e) {}
            setView(savedMode);

            // Lightbox
            const lightbox = document.getElementById('pi-lightbox');
            const lbImg = document.getElementById('pi-lightbox-img');
            const lbProduct = document.getElementById('pi-lightbox-product');
            const lbBadge = document.getElementById('pi-lightbox-badge');

            function openLightbox(trigger) {
                lbImg.src = trigger.dataset.src;
                lbProduct.textContent = trigger.dataset.product || 'Orphaned image';
                lbBadge.innerHTML = trigger.dataset.primary === '1'
                    ? '<span class="badge badge-success"><span class="badge-dot"></span> Primary image</span>'
                    : (trigger.dataset.pid ? '' : '<span class="badge badge-warning">Orphaned — product deleted</span>');
                lightbox.classList.add('is-open');
            }

            document.querySelectorAll('[data-lightbox-trigger]').forEach((el) => {
                el.addEventListener('click', (e) => {
                    // Don't open the lightbox when clicking the selection checkbox on top of the thumb
                    if (e.target.classList.contains('pi-select')) return;
                    openLightbox(el);
                });
            });

            function closeLightbox() { lightbox.classList.remove('is-open'); }
            document.getElementById('pi-lightbox-close').addEventListener('click', closeLightbox);
            lightbox.addEventListener('click', (e) => { if (e.target === lightbox) closeLightbox(); });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeLightbox(); });

            if (window.lucide) lucide.createIcons();
        })();
    </script>

@endsection
