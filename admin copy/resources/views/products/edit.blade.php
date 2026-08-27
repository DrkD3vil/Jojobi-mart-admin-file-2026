{{-- ======================================================================
    FULL SINGLE BLADE: Product Edit + Form + Existing Images (Responsive Table)
    + New Upload UI (Preview Grid) + Separated Comments + Advanced JS
    - Put this in: resources/views/products/edit.blade.php
    - Assumes: @include('products._form') exists (your big form/tabs file)
    - Uses existing fields:
        deleted_images[]   (checkboxes)
        primary_image_id   (radio, existing images)
        images[]           (new uploads)
        new_primary_index  (hidden index for new uploads primary)
====================================================================== --}}

@extends('layouts.app')

@section('content')

    {{-- =======================
  PAGE: HTML
======================= --}}
    <div class="container">
        <div class="page-header animate-slide-up">
            <div class="header-content">
                <h1 class="page-title">Edit Product</h1>
                <p class="page-subtitle">{{ $product->name }}</p>



                <span class="product-id-label">ID:</span>
                <span class="product-id-value">{{ $product->id }}</span>
                @if ($product->uuid)
                    <span style="margin: 0 0.25rem; opacity: 0.5">•</span>
                    <span class="product-id-label">UUID:</span>
                    <span class="product-id-value">{{ $product->uuid }}</span>
                @endif

                <!-- Optional breadcrumb -->
                <nav class="breadcrumb" aria-label="Breadcrumb">
                    <div class="breadcrumb-item">
                        <a href="{{ route('dashboard.financial.today') }}" class="breadcrumb-link">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor">
                                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" />
                            </svg>
                            Dashboard
                        </a>
                    </div>
                    <div class="breadcrumb-item">
                        <a href="{{ route('products.index') }}" class="breadcrumb-link">
                            Products
                        </a>
                    </div>
                    <div class="breadcrumb-item">
                        <span class="breadcrumb-current">Edit {{ $product->name }}</span>
                    </div>
                </nav>

                <!-- Optional status badges -->
                <div class="page-status">
                    @if ($product->is_active)
                        <span class="status-badge active">
                            <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor">
                                <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z" />
                            </svg>
                            Active
                        </span>
                    @else
                        <span class="status-badge inactive">
                            <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor">
                                <path
                                    d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                            </svg>
                            Inactive
                        </span>
                    @endif

                    @if ($product->batches->count() > 0)
                        <span class="status-badge info">
                            <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor">
                                <path
                                    d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4z" />
                            </svg>
                            {{ $product->batches->count() }} batch(es)
                        </span>
                    @endif

                    @if ($product->images->count() > 0)
                        <span class="status-badge info">
                            <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor">
                                <path
                                    d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z" />
                            </svg>
                            {{ $product->images->count() }} image(s)
                        </span>
                    @endif
                </div>
            </div>

            <div class="header-actions">
                <a href="{{ route('products.show', $product) }}" class="header-btn secondary" title="View product">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                        <path
                            d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" />
                    </svg>
                    View
                </a>

                <a href="{{ route('products.index') }}" class="header-btn secondary" title="Back to products">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" />
                    </svg>
                    Back to List
                </a>

                <button type="submit" form="product-form" class="header-btn primary" title="Save changes">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                        <path
                            d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z" />
                    </svg>
                    Save Changes
                </button>
            </div>
        </div>
        <style>
            /* Enhanced Page Header with Product Context */
            .page-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 2.5rem;
                flex-wrap: wrap;
                gap: 1.5rem;
                padding: 1.5rem;
                border-radius: var(--radius, 16px);
                background: linear-gradient(135deg,
                        color-mix(in srgb, var(--card, oklch(0.205 0 0)) 90%, transparent),
                        var(--card, oklch(0.205 0 0)));
                border: 1px solid var(--border-color, oklch(0.9 0 0));
                box-shadow: var(--card-shadow, 0 10px 30px rgba(0, 0, 0, .06));
                backdrop-filter: blur(10px);
            }

            .header-content {
                flex: 1;
                min-width: 300px;
            }

            .page-title {
                font-size: 2.25rem;
                font-weight: 1000;
                color: var(--text-primary, oklch(0.985 0 0));
                margin-bottom: 0.5rem;
                background: linear-gradient(135deg,
                        var(--text-primary, oklch(0.985 0 0)),
                        var(--accent-color, oklch(0.488 0.243 264.376)));
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                letter-spacing: -0.025em;
            }

            .page-subtitle {
                color: var(--text-secondary, oklch(0.708 0 0));
                font-size: 1.1rem;
                font-weight: 600;
                margin-bottom: 1.25rem;
                line-height: 1.5;
            }

            /* Breadcrumb Navigation */
            .breadcrumb {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                margin-bottom: 1rem;
                font-size: 0.9rem;
                color: var(--text-muted, oklch(0.708 0 0 / 0.7));
                flex-wrap: wrap;
            }

            .breadcrumb-item {
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .breadcrumb-item:not(:last-child)::after {
                content: "/";
                margin-left: 0.5rem;
                color: var(--text-muted, oklch(0.708 0 0 / 0.7));
                opacity: 0.6;
            }

            .breadcrumb-link {
                color: var(--text-secondary, oklch(0.708 0 0));
                text-decoration: none;
                font-weight: 600;
                transition: all var(--transition-fast, 150ms) ease;
                display: inline-flex;
                align-items: center;
                gap: 0.375rem;
            }

            .breadcrumb-link:hover {
                color: var(--accent-color, oklch(0.488 0.243 264.376));
                transform: translateY(-1px);
            }

            .breadcrumb-link svg {
                width: 14px;
                height: 14px;
            }

            .breadcrumb-current {
                color: var(--text-primary, oklch(0.985 0 0));
                font-weight: 800;
                opacity: 0.9;
            }

            /* Status Badges */
            .page-status {
                display: flex;
                gap: 0.75rem;
                margin-top: 1.25rem;
                flex-wrap: wrap;
            }

            .status-badge {
                display: inline-flex;
                align-items: center;
                gap: 0.375rem;
                padding: 0.5rem 0.875rem;
                border-radius: 999px;
                font-size: 0.85rem;
                font-weight: 800;
                border: 1px solid;
                backdrop-filter: blur(4px);
                transition: all var(--transition-fast, 150ms) ease;
            }

            .status-badge:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            .status-badge svg {
                width: 12px;
                height: 12px;
            }

            .status-badge.active {
                background: rgba(34, 197, 94, 0.15);
                color: var(--success, #16a34a);
                border-color: rgba(34, 197, 94, 0.3);
            }

            .status-badge.inactive {
                background: rgba(107, 114, 128, 0.15);
                color: var(--text-muted, #6b7280);
                border-color: rgba(107, 114, 128, 0.3);
            }

            .status-badge.warning {
                background: rgba(245, 158, 11, 0.15);
                color: var(--warning, #b45309);
                border-color: rgba(245, 158, 11, 0.3);
            }

            .status-badge.info {
                background: rgba(37, 99, 235, 0.15);
                color: var(--accent-color, #2563eb);
                border-color: rgba(37, 99, 235, 0.3);
            }

            /* Header Actions */
            .header-actions {
                display: flex;
                gap: 0.75rem;
                flex-wrap: wrap;
                align-items: center;
                justify-content: flex-end;
            }

            .header-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                padding: 0.75rem 1.25rem;
                border-radius: var(--radius, 12px);
                font-weight: 800;
                text-decoration: none;
                transition: all var(--transition-fast, 150ms) ease;
                border: 1px solid transparent;
                font-size: 0.95rem;
                white-space: nowrap;
            }

            .header-btn svg {
                width: 18px;
                height: 18px;
                flex-shrink: 0;
            }

            .header-btn.primary {
                background: var(--accent-color, oklch(0.488 0.243 264.376));
                color: var(--sidebar-primary-foreground, #fff);
                box-shadow: 0 4px 12px var(--accent-glow, rgba(37, 99, 235, 0.2));
            }

            .header-btn.primary:hover {
                background: var(--accent-hover, oklch(0.488 0.243 264.376 / 0.8));
                transform: translateY(-2px);
                box-shadow: 0 8px 20px var(--accent-glow, rgba(37, 99, 235, 0.3));
            }

            .header-btn.secondary {
                background: var(--glass-base, rgba(255, 255, 255, 0.1));
                border-color: var(--border-color, oklch(0.9 0 0));
                color: var(--text-primary, oklch(0.985 0 0));
            }

            .header-btn.secondary:hover {
                background: var(--accent, oklch(0.269 0 0));
                border-color: var(--accent-color, oklch(0.488 0.243 264.376));
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            /* Form Actions (for bottom of form) */
            .form-actions {
                display: flex;
                gap: 1rem;
                justify-content: flex-end;
                margin-top: 3rem;
                padding-top: 1.5rem;
                border-top: 1px solid var(--border-color, oklch(0.9 0 0));
                flex-wrap: wrap;
            }

            .form-actions .header-btn {
                min-width: 140px;
            }

            /* Responsive Design */
            @media (max-width: 768px) {
                .page-header {
                    flex-direction: column;
                    align-items: stretch;
                    text-align: center;
                    padding: 1.25rem;
                    gap: 1.25rem;
                }

                .header-content {
                    margin-bottom: 1rem;
                    min-width: auto;
                }

                .page-title {
                    font-size: 1.75rem;
                    text-align: center;
                }

                .page-subtitle {
                    text-align: center;
                    font-size: 1rem;
                }

                .breadcrumb {
                    justify-content: center;
                }

                .page-status {
                    justify-content: center;
                }

                .header-actions {
                    justify-content: center;
                    width: 100%;
                }

                .header-btn {
                    flex: 1;
                    min-width: 140px;
                    justify-content: center;
                }

                .form-actions {
                    flex-direction: column;
                }

                .form-actions .header-btn {
                    width: 100%;
                }
            }

            @media (max-width: 480px) {
                .page-title {
                    font-size: 1.5rem;
                }

                .page-subtitle {
                    font-size: 0.95rem;
                }

                .breadcrumb {
                    font-size: 0.85rem;
                }

                .status-badge {
                    padding: 0.375rem 0.75rem;
                    font-size: 0.8rem;
                }

                .header-btn {
                    padding: 0.65rem 1rem;
                    font-size: 0.9rem;
                }
            }

            /* Animations */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateX(-10px);
                }

                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            .animate-fade-in {
                animation: fadeIn var(--transition-normal, 250ms) ease-out;
            }

            .animate-slide-up {
                animation: slideUp var(--transition-normal, 250ms) ease-out;
            }

            .animate-slide-in {
                animation: slideIn var(--transition-normal, 250ms) ease-out;
            }

            .animate-fade-in-delay {
                animation: fadeIn var(--transition-normal, 250ms) ease-out 0.2s both;
            }

            .animate-slide-up-delay {
                animation: slideUp var(--transition-normal, 250ms) ease-out 0.2s both;
            }

            /* Focus styles for accessibility */
            .header-btn:focus,
            .breadcrumb-link:focus,
            .status-badge:focus {
                outline: 2px solid var(--ring, oklch(0.556 0 0));
                outline-offset: 2px;
            }

            /* Product ID Display (optional) */
            .product-id-display {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.375rem 0.75rem;
                background: var(--glass-base, rgba(255, 255, 255, 0.1));
                border: 1px solid var(--border-color, oklch(0.9 0 0));
                border-radius: 999px;
                font-size: 0.85rem;
                color: var(--text-muted, oklch(0.708 0 0 / 0.7));
                margin-top: 0.5rem;
            }

            .product-id-label {
                font-weight: 700;
                color: var(--text-secondary, oklch(0.708 0 0));
            }

            .product-id-value {
                font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
                font-weight: 800;
                color: var(--text-primary, oklch(0.985 0 0));
            }
        </style>





        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data"
            id="product-form">
            @csrf
            @method('PUT')

            {{-- =======================
            PRODUCT FORM: HTML
          (Your tabs/form inside products/_form.blade.php)
            ======================= --}}

            @include('products._form')

            <hr class="my-5">

            {{-- peast hear  --}}

            {{-- =======================
            SUBMIT: HTML
            ======================= --}}
            <div class="pf-submit-wrap">
                <button type="submit" class="btn btn-primary btn-lg px-5" id="submit-btn">
                    Update Product
                </button>
            </div>
        </form>
    </div>

    {{-- =======================
  STYLES: CSS
  - Section A: Image Management (Existing)
  - Section B: Upload UI (New uploads)
======================= --}}
<div class="">
 <hr class="my-5">


    <form action="{{ route('products.images.store', $product) }}" method="POST" enctype="multipart/form-data">

        @csrf

        <!-- your upload UI -->
        {{-- =======================
             IMAGE MANAGEMENT: HTML (Existing Images -> Responsive Table + Mobile Cards)
            ======================= --}}
        <div class="pf-imgmgr" data-pf-imgmgr>
            <div class="pf-imgmgr-card">
                <div class="pf-imgmgr-head">
                    <div>
                        <h5 class="pf-imgmgr-title">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z" />
                            </svg>
                            Existing Product Images
                        </h5>
                        <p class="pf-imgmgr-sub">Set primary, mark for removal, search & preview quickly.</p>
                    </div>

                    <div class="pf-imgmgr-badges">
                        <span class="pf-imgmgr-badge" data-pf-img-count>{{ $product->images->count() }}</span>
                        <span class="pf-imgmgr-badge muted">images</span>
                    </div>
                </div>

                <div class="pf-imgmgr-toolbar">
                    <div class="pf-imgmgr-left">
                        <div class="pf-imgmgr-search">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
                            </svg>
                            <input type="text" placeholder="Search by filename..." data-pf-img-search>
                        </div>

                        <div class="pf-imgmgr-filters">
                            <label class="pf-imgmgr-chip">
                                <input type="checkbox" data-pf-filter-primary>
                                <span>Primary only</span>
                            </label>

                            <label class="pf-imgmgr-chip">
                                <input type="checkbox" data-pf-filter-marked>
                                <span>Marked for removal</span>
                            </label>
                        </div>
                    </div>

                    <div class="pf-imgmgr-right">
                        <button type="button" class="pf-imgmgr-btn" data-pf-select-all> Select all </button>
                        <button type="button" class="pf-imgmgr-btn danger" data-pf-bulk-remove disabled> Mark selected
                            for removal </button>
                        <button type="button" class="pf-imgmgr-btn" data-pf-clear-selection disabled> Clear selection
                        </button>
                    </div>
                </div>

                <div class="pf-imgmgr-body">
                    @if ($product->images->isEmpty())
                        <div class="pf-imgmgr-empty">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z" />
                            </svg>
                            <div>
                                <div class="pf-imgmgr-empty-title">No images found</div>
                                <div class="pf-imgmgr-empty-sub">Upload new images below.</div>
                            </div>
                        </div>
                    @else
                        <div class="pf-imgmgr-tablewrap" role="region" aria-label="Image management table"
                            tabindex="0">
                            <table class="pf-imgmgr-table">
                                <thead>
                                    <tr>
                                        <th style="width:48px;">
                                            <input type="checkbox" data-pf-master-check aria-label="Select all rows">
                                        </th>
                                        <th style="width:92px;">Preview</th>
                                        <th>Filename</th>
                                        <th style="width:140px;">Primary</th>
                                        <th style="width:170px;">Remove</th>
                                        <th style="width:110px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody data-pf-img-tbody>
                                    @foreach ($product->images as $image)
                                        @php
                                            $path = asset('storage/' . $image->image_path);
                                            $filename = basename($image->image_path);
                                        @endphp
                                        <tr class="pf-imgmgr-row" data-name="{{ strtolower($filename) }}"
                                            data-primary="{{ $image->is_primary ? '1' : '0' }}"
                                            data-id="{{ $image->id }}">
                                            <td>
                                                <input type="checkbox" class="pf-imgmgr-rowcheck" data-pf-row-check
                                                    aria-label="Select image {{ $filename }}">
                                            </td>

                                            <td>
                                                <button type="button" class="pf-imgmgr-thumbbtn" data-pf-preview-btn
                                                    data-src="{{ $path }}" data-title="{{ $filename }}"
                                                    aria-label="Preview {{ $filename }}">
                                                    <img src="{{ $path }}" alt="{{ $filename }}"
                                                        loading="lazy">
                                                </button>
                                            </td>

                                            <td class="pf-imgmgr-name">
                                                <div class="pf-imgmgr-filename" title="{{ $filename }}">
                                                    {{ $filename }}</div>
                                                <div class="pf-imgmgr-meta">
                                                    ID: <span class="muted">{{ $image->id }}</span>
                                                    @if ($image->is_primary)
                                                        <span class="pf-imgmgr-pill ok">Primary</span>
                                                    @endif
                                                </div>
                                            </td>

                                            <td>
                                                <div class="locf-switch">
                                                    <!-- Checkbox for toggle -->
                                                    <input type="checkbox" class="locf-check"
                                                        id="primary-image-toggle-{{ $image->id }}"
                                                        data-image-id="{{ $image->id }}"
                                                        data-product-id="{{ $product->id }}"
                                                        {{ $image->is_primary ? 'checked' : '' }}
                                                        onchange="togglePrimaryImage({{ $image->id }})">
                                                    <label class="locf-toggle"
                                                        for="primary-image-toggle-{{ $image->id }}"
                                                        title="Toggle primary image"></label>

                                                    <div>
                                                        <div style="font-weight:800;">
                                                            <span id="locfActiveText">
                                                                {{ $image->is_primary ? 'Primary' : 'Set as Primary' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>




                                            <td>
                                                <label class="pf-imgmgr-check danger">
                                                    <input type="checkbox" name="deleted_images[]"
                                                        value="{{ $image->id }}" id="delete_{{ $image->id }}"
                                                        data-pf-delete-check>
                                                    <span>Mark remove</span>
                                                </label>
                                            </td>

                                            <td>
                                                <div class="pf-imgmgr-actions">
                                                    <button type="button" class="pf-imgmgr-icon" data-pf-open-newtab
                                                        data-src="{{ $path }}" aria-label="Open in new tab">
                                                        <svg viewBox="0 0 24 24">
                                                            <path
                                                                d="M14 3h7v7h-2V6.41l-9.29 9.3-1.42-1.42 9.3-9.29H14V3z" />
                                                            <path
                                                                d="M5 5h6V3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-6h-2v6H5V5z" />
                                                        </svg>
                                                    </button>

                                                    <button type="button" class="pf-imgmgr-icon danger"
                                                        data-pf-toggle-remove aria-label="Toggle remove">
                                                        <svg viewBox="0 0 24 24">
                                                            <path
                                                                d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="pf-imgmgr-foot">
                            <div>Tip: use <span class="pf-imgmgr-kbd">Shift</span> to multi-select rows faster.</div>
                            <div class="pf-imgmgr-foot-right">
                                <span class="pf-imgmgr-pill warn" data-pf-marked-count style="display:none;">0
                                    marked</span>
                                <span class="pf-imgmgr-pill info" data-pf-selected-count style="display:none;">0
                                    selected</span>
                            </div>
                        </div>
                    @endif
                </div>

            </div>

            {{-- PREVIEW MODAL --}}
            <div class="pf-imgmgr-modal" data-pf-modal aria-hidden="true">
                <div class="pf-imgmgr-modalbox" role="dialog" aria-modal="true" aria-label="Image preview">
                    <div class="pf-imgmgr-modalhead">
                        <div class="pf-imgmgr-modaltitle" data-pf-modal-title>Preview</div>
                        <button type="button" class="pf-imgmgr-icon" data-pf-modal-close aria-label="Close preview">
                            <svg viewBox="0 0 24 24">
                                <path
                                    d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L12 13.41l-6.89 6.3-1.41-1.42L10.59 12 3.7 5.71 5.11 4.29 12 10.59l6.89-6.3z" />
                            </svg>
                        </button>
                    </div>
                    <div class="pf-imgmgr-modalbody">
                        <img src="" alt="Preview" data-pf-modal-img>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-5">

        {{-- =======================
            NEW UPLOADS: HTML (Your advanced upload UI)
            ======================= --}}
        <div class="pf-upload" data-pf-upload>
            <div class="pf-upload-card">
                <div class="pf-upload-head">
                    <div>
                        <h4 class="pf-upload-title">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M19 15v4H5v-4H3v6h18v-6h-2zM11 16h2V7h3L12 3 8 7h3v9z" />
                            </svg>
                            Add New Images
                        </h4>
                        <p class="pf-upload-sub">Drag & drop or click to choose files. Maximum 10 files per upload.</p>
                    </div>

                    <div class="pf-upload-actions">
                        <button type="button" class="pf-btn" data-pf-clear-new disabled>Clear All</button>
                        <button type="button" class="pf-btn pf-btn-primary" data-pf-open-picker>Choose Files</button>
                    </div>
                </div>

                <div class="pf-upload-body">
                    <div class="pf-dropzone" data-pf-dropzone>
                        <div class="pf-drop-inner">
                            <div class="pf-drop-icon">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z" />
                                </svg>
                            </div>
                            <div style="font-weight:600;color:var(--text-primary);font-size:1.125rem;">
                                Drop images here or click to browse
                            </div>
                            <p class="pf-drop-hint">Supports PNG, JPG, WEBP, GIF up to 10MB each.</p>
                        </div>

                        <input type="file" name="images[]" id="new_images" class="pf-hidden-input" multiple
                            accept="image/*" data-pf-file-input>
                    </div>

                    <input type="hidden" name="new_primary_index" value="" id="pf_new_primary_index">

                    <div class="pf-preview-wrap" data-pf-preview>
                        <div class="pf-preview-head">
                            <div>
                                <h5 class="pf-preview-title">
                                    Selected Images
                                    <span class="pf-badge" data-pf-count>0</span>
                                </h5>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="pf-status pf-status-success" style="display:none;" data-pf-size-info>
                                    <span data-pf-total-size>0 MB</span>
                                </span>
                                <span class="text-muted" style="font-size:.75rem;">Click Set Primary on one
                                    image</span>
                            </div>
                        </div>

                        <div class="pf-grid" data-pf-grid></div>

                        <div class="pf-upload-foot">
                            <div>Primary affects <strong>new uploads</strong> only</div>
                            <div>Shortcuts: <span class="pf-kbd">Esc</span> Clear</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="pf-submit-wrap">
            <button type="submit" class="btn btn-primary btn-lg px-5" id="submit-btn">
                Upload image
            </button>
        </div>
    </form>
</div>


    <style>
        /* =======================
                                          IMAGE MANAGEMENT: CSS - Updated with Theme Variables
                                        ======================= */
        .pf-imgmgr {
            --r: var(--radius, 14px);
            --b: var(--border-color, oklch(0.9 0 0));
            --bg: var(--card, oklch(0.205 0 0));
            --bg2: var(--accent, oklch(0.269 0 0));
            --txt: var(--text-primary, oklch(0.985 0 0));
            --muted: var(--text-muted, oklch(0.708 0 0 / 0.7));
            --accent: var(--accent-color, oklch(0.488 0.243 264.376));
            --danger: var(--danger, #ef4444);
            --ok: var(--success, #22c55e);
            --warn: var(--warning, #f59e0b);
            --shadow: var(--card-shadow, 0 10px 30px rgba(0, 0, 0, .08));
            margin-bottom: 1.25rem;
        }

        .pf-imgmgr-card {
            border: 1px solid var(--b);
            background: var(--bg);
            border-radius: calc(var(--r) + 2px);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .pf-imgmgr-head {
            padding: 1rem 1.25rem;
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: center;
            background: linear-gradient(135deg, var(--bg2), var(--glass-base, rgba(255, 255, 255, .45)));
            border-bottom: 1px solid var(--b);
        }

        .pf-imgmgr-title {
            margin: 0;
            display: flex;
            align-items: center;
            gap: .6rem;
            font-weight: 900;
            color: var(--txt);
        }

        .pf-imgmgr-title svg {
            width: 20px;
            height: 20px;
            fill: currentColor;
            color: var(--accent);
        }

        .pf-imgmgr-sub {
            margin: .25rem 0 0;
            color: var(--muted);
            font-size: .9rem;
        }

        .pf-imgmgr-badges {
            display: flex;
            gap: .35rem;
            align-items: center;
        }

        .pf-imgmgr-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .25rem .55rem;
            border: 1px solid var(--b);
            border-radius: 999px;
            font-weight: 800;
            font-size: .8rem;
            color: var(--txt);
            background: var(--glass-base, rgba(255, 255, 255, .75));
        }

        .pf-imgmgr-badge.muted {
            color: var(--muted);
            font-weight: 700;
        }

        .pf-imgmgr-toolbar {
            padding: .85rem 1.25rem;
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            border-bottom: 1px solid var(--b);
        }

        .pf-imgmgr-left {
            display: flex;
            gap: .75rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .pf-imgmgr-right {
            display: flex;
            gap: .5rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .pf-imgmgr-search {
            display: flex;
            align-items: center;
            gap: .5rem;
            border: 1px solid var(--b);
            border-radius: 999px;
            padding: .4rem .7rem;
            background: var(--glass-base, rgba(255, 255, 255, .75));
            min-width: 260px;
        }

        .pf-imgmgr-search svg {
            width: 18px;
            height: 18px;
            fill: currentColor;
            color: var(--muted);
        }

        .pf-imgmgr-search input {
            border: none;
            outline: none;
            background: transparent;
            color: var(--txt);
            width: 100%;
            font-weight: 600;
        }

        .pf-imgmgr-filters {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
        }

        .pf-imgmgr-chip {
            display: inline-flex;
            gap: .45rem;
            align-items: center;
            border: 1px solid var(--b);
            background: var(--glass-base, rgba(255, 255, 255, .65));
            border-radius: 999px;
            padding: .35rem .6rem;
            font-weight: 800;
            font-size: .82rem;
            color: var(--txt);
            user-select: none;
            transition: all var(--transition-fast, 150ms) ease;
        }

        .pf-imgmgr-chip input {
            margin: 0;
        }

        .pf-imgmgr-chip:hover {
            border-color: var(--accent);
            transform: translateY(-1px);
        }

        .pf-imgmgr-btn {
            border: 1px solid var(--b);
            background: var(--glass-base, rgba(255, 255, 255, .75));
            color: var(--txt);
            border-radius: calc(var(--r) - 2px);
            padding: .5rem .75rem;
            font-weight: 900;
            cursor: pointer;
            transition: transform var(--transition-fast, 150ms) ease,
                box-shadow var(--transition-fast, 150ms) ease,
                border-color var(--transition-fast, 150ms) ease;
        }

        .pf-imgmgr-btn:hover {
            border-color: var(--accent);
            box-shadow: 0 10px 22px rgba(0, 0, 0, .08);
            transform: translateY(-1px);
        }

        .pf-imgmgr-btn:disabled {
            opacity: .5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .pf-imgmgr-btn.danger {
            border-color: color-mix(in srgb, var(--danger) 35%, var(--b));
            color: var(--danger);
        }

        .pf-imgmgr-btn.danger:hover {
            border-color: var(--danger);
            background: rgba(239, 68, 68, .1);
        }

        .pf-imgmgr-body {
            padding: 1rem 1.25rem;
        }

        .pf-imgmgr-empty {
            display: flex;
            gap: 1rem;
            align-items: center;
            padding: 1rem;
            border: 1px dashed var(--b);
            border-radius: calc(var(--r) - 2px);
            background: var(--bg2);
            color: var(--muted);
        }

        .pf-imgmgr-empty svg {
            width: 40px;
            height: 40px;
            fill: currentColor;
            opacity: .7;
        }

        .pf-imgmgr-empty-title {
            font-weight: 900;
            color: var(--txt);
        }

        .pf-imgmgr-empty-sub {
            font-size: .9rem;
        }

        .pf-imgmgr-tablewrap {
            overflow: auto;
            border: 1px solid var(--b);
            border-radius: calc(var(--r) - 2px);
        }

        .pf-imgmgr-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 820px;
            background: var(--bg);
        }

        .pf-imgmgr-table th,
        .pf-imgmgr-table td {
            padding: .85rem;
            border-bottom: 1px solid var(--b);
            text-align: left;
            vertical-align: middle;
            color: var(--txt);
        }

        .pf-imgmgr-table th {
            background: var(--bg2);
            font-weight: 900;
            color: var(--txt);
        }

        .pf-imgmgr-row.is-hidden {
            display: none;
        }

        .pf-imgmgr-row.is-marked {
            background: color-mix(in srgb, var(--danger) 7%, transparent);
        }

        .pf-imgmgr-row.is-selected {
            outline: 2px solid color-mix(in srgb, var(--accent) 35%, transparent);
            outline-offset: -2px;
        }

        .pf-imgmgr-thumbbtn {
            border: none;
            background: transparent;
            padding: 0;
            cursor: pointer;
            width: 72px;
            height: 54px;
            border-radius: calc(var(--r) - 2px);
            overflow: hidden;
            border: 1px solid var(--b);
        }

        .pf-imgmgr-thumbbtn img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .pf-imgmgr-filename {
            font-weight: 900;
            color: var(--txt);
            max-width: 420px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .pf-imgmgr-meta {
            margin-top: .2rem;
            font-size: .82rem;
            color: var(--muted);
            display: flex;
            gap: .5rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .pf-imgmgr-meta .muted {
            opacity: .9;
        }

        .pf-imgmgr-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .15rem .55rem;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 900;
            border: 1px solid var(--b);
            background: var(--glass-base, rgba(255, 255, 255, .75));
        }

        .pf-imgmgr-pill.ok {
            border-color: color-mix(in srgb, var(--ok) 35%, var(--b));
            color: var(--ok);
            background: color-mix(in srgb, var(--ok) 10%, transparent);
        }

        .pf-imgmgr-pill.warn {
            border-color: color-mix(in srgb, var(--warn) 35%, var(--b));
            color: var(--warn);
            background: color-mix(in srgb, var(--warn) 10%, transparent);
        }

        .pf-imgmgr-pill.info {
            border-color: color-mix(in srgb, var(--accent) 35%, var(--b));
            color: var(--accent);
            background: color-mix(in srgb, var(--accent) 10%, transparent);
        }

        .pf-imgmgr-radio,
        .pf-imgmgr-check {
            display: inline-flex;
            gap: .5rem;
            align-items: center;
            font-weight: 900;
            color: var(--txt);
            user-select: none;
        }

        .pf-imgmgr-check.danger {
            color: var(--danger);
        }

        .pf-imgmgr-actions {
            display: flex;
            gap: .45rem;
            align-items: center;
        }

        .pf-imgmgr-icon {
            width: 38px;
            height: 38px;
            border-radius: calc(var(--r) - 2px);
            border: 1px solid var(--b);
            background: var(--glass-base, rgba(255, 255, 255, .65));
            cursor: pointer;
            display: grid;
            place-items: center;
            transition: transform var(--transition-fast, 150ms) ease,
                box-shadow var(--transition-fast, 150ms) ease,
                border-color var(--transition-fast, 150ms) ease;
        }

        .pf-imgmgr-icon svg {
            width: 18px;
            height: 18px;
            fill: currentColor;
            color: var(--txt);
        }

        .pf-imgmgr-icon:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 18px rgba(0, 0, 0, .08);
            border-color: var(--accent);
        }

        .pf-imgmgr-icon.danger svg {
            color: var(--danger);
        }

        .pf-imgmgr-icon.danger:hover {
            border-color: var(--danger);
            background: rgba(239, 68, 68, .1);
        }

        .pf-imgmgr-foot {
            margin-top: .85rem;
            padding-top: .75rem;
            border-top: 1px solid var(--b);
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            color: var(--muted);
            font-weight: 800;
            font-size: .85rem;
        }

        .pf-imgmgr-kbd {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: .75rem;
            padding: .2rem .45rem;
            border-radius: calc(var(--r) - 4px);
            border: 1px solid var(--b);
            background: var(--glass-base, rgba(255, 255, 255, .65));
            color: var(--txt);
        }

        .pf-imgmgr-foot-right {
            display: flex;
            gap: .5rem;
            align-items: center;
        }

        .pf-imgmgr-modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .55);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            z-index: 99999;
        }

        .pf-imgmgr-modal.show {
            display: flex;
        }

        .pf-imgmgr-modalbox {
            width: min(900px, calc(100vw - 2rem));
            background: var(--bg);
            border: 1px solid var(--b);
            border-radius: calc(var(--r) + 2px);
            overflow: hidden;
            box-shadow: var(--dropdown-shadow, 0 24px 70px rgba(0, 0, 0, .25));
        }

        .pf-imgmgr-modalhead {
            padding: .9rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--b);
            background: var(--bg2);
        }

        .pf-imgmgr-modaltitle {
            font-weight: 900;
            color: var(--txt);
        }

        .pf-imgmgr-modalbody {
            padding: 1rem;
            background: #111;
            display: grid;
            place-items: center;
        }

        .pf-imgmgr-modalbody img {
            max-width: 100%;
            max-height: 70vh;
            object-fit: contain;
            display: block;
            border-radius: calc(var(--r) - 2px);
            background: #111;
        }

        /* Responsive: table to cards */
        @media (max-width: 860px) {
            .pf-imgmgr-table {
                min-width: 0;
            }

            .pf-imgmgr-table thead {
                display: none;
            }

            .pf-imgmgr-table,
            .pf-imgmgr-table tbody,
            .pf-imgmgr-table tr,
            .pf-imgmgr-table td {
                display: block;
                width: 100%;
            }

            .pf-imgmgr-table tr {
                border-bottom: 1px solid var(--b);
                padding: .75rem;
                background: var(--bg);
            }

            .pf-imgmgr-table td {
                border: none;
                padding: .35rem 0;
            }

            .pf-imgmgr-thumbbtn {
                width: 100%;
                height: 180px;
            }

            .pf-imgmgr-filename {
                max-width: 100%;
            }

            .pf-imgmgr-actions {
                justify-content: flex-start;
            }
        }

        /* =======================
                                          UPLOAD UI: CSS (Updated with Theme Variables)
                                        ======================= */
        .pf-upload {
            margin-top: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .pf-upload-card {
            border: 1px solid var(--border-color, oklch(0.9 0 0));
            border-radius: calc(var(--radius, 12px) + 4px);
            background: var(--card, oklch(0.205 0 0));
            box-shadow: var(--card-shadow, 0 10px 30px rgba(0, 0, 0, .06));
            overflow: hidden;
        }

        .pf-upload-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.25rem 1.5rem;
            background: linear-gradient(135deg, var(--accent, oklch(0.269 0 0)), var(--glass-base, rgba(255, 255, 255, .45)));
            border-bottom: 1px solid var(--border-color, oklch(0.9 0 0));
        }

        .pf-upload-title {
            margin: 0;
            font-weight: 900;
            display: flex;
            align-items: center;
            gap: .75rem;
            color: var(--text-primary, oklch(0.985 0 0));
        }

        .pf-upload-title svg {
            width: 22px;
            height: 22px;
            fill: currentColor;
            color: var(--accent-color, oklch(0.488 0.243 264.376));
        }

        .pf-upload-sub {
            margin: .35rem 0 0;
            color: var(--text-muted, oklch(0.708 0 0 / 0.7));
            font-size: .9rem;
        }

        .pf-upload-actions {
            display: flex;
            gap: .75rem;
            flex-wrap: wrap;
        }

        .pf-btn {
            border: 1px solid var(--border-color, oklch(0.9 0 0));
            background: var(--glass-base, rgba(255, 255, 255, .75));
            border-radius: var(--radius, 12px);
            padding: .65rem 1rem;
            font-weight: 900;
            cursor: pointer;
            color: var(--text-primary, oklch(0.985 0 0));
            transition: all var(--transition-fast, 150ms) ease;
        }

        .pf-btn:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .pf-btn-primary {
            background: var(--accent-color, oklch(0.488 0.243 264.376));
            border-color: var(--accent-color, oklch(0.488 0.243 264.376));
            color: var(--sidebar-primary-foreground, #fff);
        }

        .pf-btn-primary:hover:not(:disabled) {
            background: var(--accent-hover, oklch(0.488 0.243 264.376 / 0.8));
            transform: translateY(-1px);
            box-shadow: 0 4px 12px var(--accent-glow, rgba(37, 99, 235, .2));
        }

        .pf-btn:hover:not(:disabled) {
            border-color: var(--accent-color, oklch(0.488 0.243 264.376));
            transform: translateY(-1px);
        }

        .pf-upload-body {
            padding: 1.5rem;
        }

        .pf-dropzone {
            border: 2px dashed var(--border-color, oklch(0.9 0 0));
            border-radius: calc(var(--radius, 12px) + 4px);
            padding: 2.2rem 1.5rem;
            background: linear-gradient(180deg, var(--glass-base, rgba(255, 255, 255, .6)), var(--card, oklch(0.205 0 0)));
            position: relative;
        }

        .pf-dropzone.is-dragover {
            border-color: var(--accent-color, oklch(0.488 0.243 264.376));
            background: var(--accent-glow, rgba(37, 99, 235, .08));
        }

        .pf-drop-inner {
            display: grid;
            gap: .75rem;
            place-items: center;
            text-align: center;
        }

        .pf-drop-icon {
            width: 64px;
            height: 64px;
            border-radius: calc(var(--radius, 12px) + 4px);
            display: grid;
            place-items: center;
            background: var(--accent-glow, rgba(37, 99, 235, .12));
            color: var(--accent-color, oklch(0.488 0.243 264.376));
        }

        .pf-drop-icon svg {
            width: 28px;
            height: 28px;
            fill: currentColor;
        }

        .pf-drop-hint {
            color: var(--text-muted, oklch(0.708 0 0 / 0.7));
            margin: 0;
        }

        .pf-hidden-input {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
        }

        .pf-preview-wrap {
            margin-top: 1.25rem;
            display: none;
        }

        .pf-preview-wrap.show {
            display: block;
        }

        .pf-preview-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
            padding-bottom: .75rem;
            border-bottom: 1px solid var(--border-color, oklch(0.9 0 0));
        }

        .pf-preview-title {
            margin: 0;
            font-weight: 900;
            color: var(--text-primary, oklch(0.985 0 0));
        }

        .pf-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .25rem .6rem;
            border-radius: 999px;
            border: 1px solid var(--border-color, oklch(0.9 0 0));
            font-weight: 900;
            color: var(--text-primary, oklch(0.985 0 0));
            background: var(--glass-base, rgba(255, 255, 255, .75));
        }

        .pf-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1rem;
        }

        @media (max-width:1200px) {
            .pf-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width:992px) {
            .pf-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width:768px) {
            .pf-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width:480px) {
            .pf-grid {
                grid-template-columns: 1fr;
            }
        }

        .pf-thumb {
            border: 1px solid var(--border-color, oklch(0.9 0 0));
            border-radius: calc(var(--radius, 12px) + 2px);
            overflow: hidden;
            background: var(--card, oklch(0.205 0 0));
        }

        .pf-thumb img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            display: block;
        }

        .pf-thumb-bar {
            padding: .75rem;
            border-top: 1px solid var(--border-color, oklch(0.9 0 0));
            display: flex;
            justify-content: space-between;
            gap: .75rem;
            align-items: center;
        }

        .pf-file-name {
            font-weight: 900;
            font-size: .82rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 100%;
            color: var(--text-primary, oklch(0.985 0 0));
        }

        .pf-file-size {
            font-size: .75rem;
            color: var(--text-muted, oklch(0.708 0 0 / 0.7));
        }

        .pf-icon-btn {
            width: 38px;
            height: 38px;
            border-radius: var(--radius, 12px);
            border: 1px solid var(--border-color, oklch(0.9 0 0));
            background: var(--glass-base, rgba(255, 255, 255, .65));
            display: grid;
            place-items: center;
            cursor: pointer;
            transition: all var(--transition-fast, 150ms) ease;
        }

        .pf-icon-btn:hover {
            border-color: var(--accent-color, oklch(0.488 0.243 264.376));
            transform: translateY(-1px);
        }

        .pf-primary-pick {
            display: inline-flex;
            gap: .5rem;
            align-items: center;
            border: 1px solid rgba(34, 197, 94, .35);
            background: rgba(34, 197, 94, .12);
            color: var(--success, #16a34a);
            border-radius: 999px;
            padding: .45rem .7rem;
            font-weight: 900;
            font-size: .75rem;
            cursor: pointer;
        }

        .pf-primary-radio {
            appearance: none;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 2px solid var(--success, #16a34a);
            display: inline-grid;
            place-items: center;
        }

        .pf-primary-radio:after {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--success, #16a34a);
            transform: scale(0);
            transition: transform var(--transition-fast, 150ms) ease;
        }

        .pf-primary-radio:checked:after {
            transform: scale(1);
        }

        .pf-upload-foot {
            margin-top: 1rem;
            padding-top: .75rem;
            border-top: 1px solid var(--border-color, oklch(0.9 0 0));
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            color: var(--text-muted, oklch(0.708 0 0 / 0.7));
            font-weight: 800;
            font-size: .85rem;
        }

        .pf-kbd {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: .75rem;
            padding: .2rem .45rem;
            border-radius: calc(var(--radius, 12px) - 2px);
            border: 1px solid var(--border-color, oklch(0.9 0 0));
            background: var(--glass-base, rgba(255, 255, 255, .65));
            color: var(--text-primary, oklch(0.985 0 0));
        }

        /* Submit wrapper */
        .pf-submit-wrap {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color, oklch(0.9 0 0));
        }

        /* Focus styles for accessibility */
        .pf-imgmgr-btn:focus,
        .pf-imgmgr-icon:focus,
        .pf-imgmgr-search input:focus,
        .pf-imgmgr-chip:focus,
        .pf-btn:focus,
        .pf-icon-btn:focus,
        .pf-primary-pick:focus {
            outline: 2px solid var(--ring, oklch(0.556 0 0));
            outline-offset: 2px;
        }
    </style>

    {{-- =======================
  SCRIPTS: JS
  - Section A: Existing image manager
  - Section B: New uploads UI
======================= --}}


    <script>
        function togglePrimaryImage(imageId) {
            const checkbox = document.getElementById('primary-image-toggle-' + imageId);
            const statusText = document.getElementById('locfActiveText');
            const productId = checkbox.dataset.productId; // Get the productId from the dataset

            // Log the request URL for debugging purposes
            console.log("Sending request to set primary image:", `/products/${productId}/images/${imageId}/primary-toggle`);

            fetch(`/products/${productId}/images/${imageId}/primary-toggle`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}', // CSRF token
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        image_id: imageId,
                        is_primary: checkbox.checked
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log("Response Data:", data); // Log the response for debugging

                    if (data.ok) {
                        // Update the status text based on the new primary image value
                        statusText.textContent = data.is_primary ? 'Primary' : 'Set as Primary';
                        // Uncheck other images if this image is set to primary
                        const checkboxes = document.querySelectorAll('[data-product-id="' + productId + '"]');
                        checkboxes.forEach(cb => {
                            if (cb.id !== 'primary-image-toggle-' + imageId) {
                                cb.checked = false;
                                const text = cb.closest('td').querySelector('#locfActiveText');
                                if (text) text.textContent = 'Set as Primary';
                            }
                        });
                    } else {
                        alert('Failed to update primary image');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to update primary image');
                });
        }
    </script>



    <script>
        /* =======================
                                          IMAGE MANAGEMENT: JS (Existing Images)
                                          - Search + filters
                                          - Select all + shift select
                                          - Bulk mark remove
                                          - Toggle remove per row
                                          - Preview modal
                                        ======================= */
        (function() {
            "use strict";

            const mgr = document.querySelector("[data-pf-imgmgr]");
            if (!mgr) return;

            const tbody = mgr.querySelector("[data-pf-img-tbody]");
            const rows = () => Array.from(mgr.querySelectorAll(".pf-imgmgr-row"));

            const search = mgr.querySelector("[data-pf-img-search]");
            const filterPrimary = mgr.querySelector("[data-pf-filter-primary]");
            const filterMarked = mgr.querySelector("[data-pf-filter-marked]");

            const master = mgr.querySelector("[data-pf-master-check]");
            const selectAllBtn = mgr.querySelector("[data-pf-select-all]");
            const bulkRemoveBtn = mgr.querySelector("[data-pf-bulk-remove]");
            const clearSelBtn = mgr.querySelector("[data-pf-clear-selection]");

            const selectedCountPill = mgr.querySelector("[data-pf-selected-count]");
            const markedCountPill = mgr.querySelector("[data-pf-marked-count]");

            const modal = mgr.querySelector("[data-pf-modal]");
            const modalImg = mgr.querySelector("[data-pf-modal-img]");
            const modalTitle = mgr.querySelector("[data-pf-modal-title]");
            const modalClose = mgr.querySelector("[data-pf-modal-close]");

            let lastCheckedIndex = null;

            const getRowCheck = (row) => row.querySelector("[data-pf-row-check]");
            const getDeleteCheck = (row) => row.querySelector("[data-pf-delete-check]");
            const isMarked = (row) => !!getDeleteCheck(row)?.checked;
            const setMarked = (row, v) => {
                const c = getDeleteCheck(row);
                if (c) c.checked = !!v;
            };
            const isSelected = (row) => !!getRowCheck(row)?.checked;
            const setSelected = (row, v) => {
                const c = getRowCheck(row);
                if (c) c.checked = !!v;
            };

            const updateRowStates = () => {
                let selected = 0;
                let marked = 0;

                rows().forEach(r => {
                    r.classList.toggle("is-selected", isSelected(r));
                    r.classList.toggle("is-marked", isMarked(r));
                    if (isSelected(r)) selected++;
                    if (isMarked(r)) marked++;
                });

                // master checkbox reflects visible selected state
                const visible = rows().filter(r => !r.classList.contains("is-hidden"));
                const visibleSelected = visible.filter(r => isSelected(r)).length;
                if (master) {
                    master.checked = visible.length > 0 && visibleSelected === visible.length;
                    master.indeterminate = visibleSelected > 0 && visibleSelected < visible.length;
                }

                if (selectedCountPill) {
                    selectedCountPill.style.display = selected > 0 ? "inline-flex" : "none";
                    selectedCountPill.textContent = `${selected} selected`;
                }
                if (markedCountPill) {
                    markedCountPill.style.display = marked > 0 ? "inline-flex" : "none";
                    markedCountPill.textContent = `${marked} marked`;
                }

                if (bulkRemoveBtn) bulkRemoveBtn.disabled = selected === 0;
                if (clearSelBtn) clearSelBtn.disabled = selected === 0;
            };

            const applyFilters = () => {
                const q = (search?.value || "").trim().toLowerCase();
                const onlyPrimary = !!filterPrimary?.checked;
                const onlyMarked = !!filterMarked?.checked;

                rows().forEach(r => {
                    const name = (r.dataset.name || "");
                    const primary = r.dataset.primary === "1";
                    const marked = isMarked(r);

                    let show = true;
                    if (q && !name.includes(q)) show = false;
                    if (onlyPrimary && !primary) show = false;
                    if (onlyMarked && !marked) show = false;

                    r.classList.toggle("is-hidden", !show);
                });

                updateRowStates();
            };

            // Preview modal
            const openModal = (src, title) => {
                if (!modal || !modalImg) return;
                modalImg.src = src || "";
                if (modalTitle) modalTitle.textContent = title || "Preview";
                modal.classList.add("show");
                modal.setAttribute("aria-hidden", "false");
            };

            const closeModal = () => {
                if (!modal || !modalImg) return;
                modal.classList.remove("show");
                modal.setAttribute("aria-hidden", "true");
                modalImg.src = "";
            };

            modalClose?.addEventListener("click", closeModal);
            modal?.addEventListener("click", (e) => {
                if (e.target === modal) closeModal();
            });
            document.addEventListener("keydown", (e) => {
                if (e.key === "Escape") closeModal();
            });

            // Row events
            rows().forEach((row, idx) => {
                // click preview
                row.querySelectorAll("[data-pf-preview-btn]").forEach(btn => {
                    btn.addEventListener("click", () => openModal(btn.dataset.src, btn.dataset.title));
                });

                // open new tab
                row.querySelectorAll("[data-pf-open-newtab]").forEach(btn => {
                    btn.addEventListener("click", () => window.open(btn.dataset.src, "_blank"));
                });

                // toggle remove
                row.querySelectorAll("[data-pf-toggle-remove]").forEach(btn => {
                    btn.addEventListener("click", () => {
                        setMarked(row, !isMarked(row));
                        applyFilters();
                    });
                });

                // selecting with shift
                const checkbox = getRowCheck(row);
                checkbox?.addEventListener("click", (e) => {
                    const visible = rows().filter(r => !r.classList.contains("is-hidden"));
                    const visibleIndexes = visible.map(r => rows().indexOf(r));
                    const currentIndex = idx;

                    if (e.shiftKey && lastCheckedIndex !== null) {
                        const a = Math.min(lastCheckedIndex, currentIndex);
                        const b = Math.max(lastCheckedIndex, currentIndex);
                        for (let i = a; i <= b; i++) {
                            const r = rows()[i];
                            if (r && !r.classList.contains("is-hidden")) setSelected(r, checkbox
                                .checked);
                        }
                    }
                    lastCheckedIndex = currentIndex;
                    updateRowStates();
                });

                // if delete checkbox changes, re-filter
                getDeleteCheck(row)?.addEventListener("change", applyFilters);
            });

            // master select visible
            master?.addEventListener("change", () => {
                const v = !!master.checked;
                rows().forEach(r => {
                    if (!r.classList.contains("is-hidden")) setSelected(r, v);
                });
                updateRowStates();
            });

            selectAllBtn?.addEventListener("click", () => {
                rows().forEach(r => {
                    if (!r.classList.contains("is-hidden")) setSelected(r, true);
                });
                updateRowStates();
            });

            clearSelBtn?.addEventListener("click", () => {
                rows().forEach(r => setSelected(r, false));
                updateRowStates();
            });

            bulkRemoveBtn?.addEventListener("click", () => {
                const selectedRows = rows().filter(r => isSelected(r) && !r.classList.contains("is-hidden"));
                if (!selectedRows.length) return;

                if (!confirm(`Mark ${selectedRows.length} selected image(s) for removal?`)) return;

                selectedRows.forEach(r => setMarked(r, true));
                selectedRows.forEach(r => setSelected(r, false));
                applyFilters();
            });

            // Search + filters
            search?.addEventListener("input", applyFilters);
            filterPrimary?.addEventListener("change", applyFilters);
            filterMarked?.addEventListener("change", applyFilters);

            // When primary changes, update data-primary markers (optional visual correctness)
            mgr.querySelectorAll("[data-pf-primary-radio]").forEach(radio => {
                radio.addEventListener("change", () => {
                    // set all to 0 then current row to 1
                    rows().forEach(r => r.dataset.primary = "0");
                    const row = radio.closest(".pf-imgmgr-row");
                    if (row) row.dataset.primary = "1";
                    applyFilters();
                });
            });

            applyFilters();
        })();
    </script>

    <script>
        /* =======================
                                          NEW UPLOADS: JS
                                          - Preview grid
                                          - Validate image + size
                                          - Primary index for new uploads
                                          - Drag & drop
                                        ======================= */
        (function() {
            "use strict";

            const root = document.querySelector("[data-pf-upload]");
            if (!root) return;

            const dropzone = root.querySelector("[data-pf-dropzone]");
            const input = root.querySelector("[data-pf-file-input]");
            const previewWrap = root.querySelector("[data-pf-preview]");
            const grid = root.querySelector("[data-pf-grid]");
            const countEl = root.querySelector("[data-pf-count]");
            const openBtn = root.querySelector("[data-pf-open-picker]");
            const clearBtn = root.querySelector("[data-pf-clear-new]");
            const newPrimaryHidden = document.getElementById("pf_new_primary_index");
            const sizeInfo = root.querySelector("[data-pf-size-info]");
            const totalSizeEl = root.querySelector("[data-pf-total-size]");

            const urls = new Map();
            const MAX_FILES = 10;
            const MAX_SIZE = 10 * 1024 * 1024; // 10MB

            const fmtSize = (bytes) => {
                if (!Number.isFinite(bytes)) return "";
                const units = ["B", "KB", "MB", "GB"];
                let i = 0,
                    v = bytes;
                while (v >= 1024 && i < units.length - 1) {
                    v /= 1024;
                    i++;
                }
                return `${v.toFixed(i === 0 ? 0 : 1)} ${units[i]}`;
            };

            const getFiles = () => Array.from(input.files || []);

            const setFiles = (files) => {
                const dt = new DataTransfer();
                files.slice(0, MAX_FILES).forEach(f => dt.items.add(f));
                input.files = dt.files;
                input.dispatchEvent(new Event("change", {
                    bubbles: true
                }));
            };

            const revokeAll = () => {
                urls.forEach((u) => {
                    try {
                        URL.revokeObjectURL(u);
                    } catch (_) {}
                });
                urls.clear();
            };

            const ensureURL = (file) => {
                if (urls.has(file)) return urls.get(file);
                const u = URL.createObjectURL(file);
                urls.set(file, u);
                return u;
            };

            const syncClearState = (count) => {
                if (!clearBtn) return;
                clearBtn.disabled = count <= 0;
                clearBtn.setAttribute("aria-disabled", String(count <= 0));
            };

            const updateSizeInfo = (files) => {
                if (!sizeInfo || !totalSizeEl) return;
                const totalBytes = files.reduce((sum, f) => sum + f.size, 0);
                totalSizeEl.textContent = fmtSize(totalBytes);
                sizeInfo.style.display = files.length > 0 ? 'inline-flex' : 'none';
            };

            const setPrimary = (index) => {
                newPrimaryHidden.value = String(index);
                render();
            };

            const clearAll = () => {
                if (getFiles().length === 0) return;
                revokeAll();
                newPrimaryHidden.value = "";
                setFiles([]);
                render();
            };

            const removeAt = (index) => {
                const files = getFiles();
                if (!files[index]) return;

                const f = files[index];
                const u = urls.get(f);
                if (u) {
                    try {
                        URL.revokeObjectURL(u);
                    } catch (_) {}
                }
                urls.delete(f);

                files.splice(index, 1);

                const p = newPrimaryHidden.value === "" ? null : Number(newPrimaryHidden.value);
                if (p !== null) {
                    if (p === index) newPrimaryHidden.value = "";
                    else if (p > index) newPrimaryHidden.value = String(p - 1);
                }

                setFiles(files);
                if (files.length && newPrimaryHidden.value === "") newPrimaryHidden.value = "0";
                render();
            };

            const validateFiles = (files) => {
                const errors = [];
                const valid = [];

                files.forEach(file => {
                    if (!file.type.startsWith('image/')) {
                        errors.push(`${file.name} is not an image`);
                        return;
                    }
                    if (file.size > MAX_SIZE) {
                        errors.push(`${file.name} exceeds 10MB`);
                        return;
                    }
                    valid.push(file);
                });

                if (errors.length) alert('Some files were rejected:\n' + errors.join('\n'));
                return valid;
            };

            const render = () => {
                const files = getFiles();
                const primaryIndex = newPrimaryHidden.value === "" ? null : Number(newPrimaryHidden.value);

                countEl.textContent = String(files.length);
                previewWrap.classList.toggle("show", files.length > 0);
                syncClearState(files.length);
                updateSizeInfo(files);

                grid.innerHTML = "";

                if (files.length === 0) return;

                files.forEach((file, idx) => {
                    const url = ensureURL(file);
                    const isPrimary = primaryIndex === idx;

                    const card = document.createElement("div");
                    card.className = "pf-thumb" + (isPrimary ? " is-primary" : "");

                    card.innerHTML = `
        <img src="${url}" alt="Preview" loading="lazy">
        <div class="pf-thumb-bar">
          <div style="min-width:0;flex:1;">
            <div class="pf-file-name" title="${file.name}">${file.name}</div>
            <div class="pf-file-size">${fmtSize(file.size)}</div>
          </div>
          <div style="display:flex;gap:.5rem;align-items:center;">
            <label class="pf-primary-pick" title="Set as primary for new uploads">
              <input class="pf-primary-radio" type="radio" name="pf_new_primary_radio" ${isPrimary ? "checked" : ""}>
              Set Primary
            </label>
            <button type="button" class="pf-icon-btn" title="Remove" aria-label="Remove">
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
              </svg>
            </button>
          </div>
        </div>
      `;

                    card.querySelector(".pf-primary-pick")?.addEventListener("click", (e) => {
                        e.preventDefault();
                        setPrimary(idx);
                    });

                    card.querySelector(".pf-icon-btn")?.addEventListener("click", (e) => {
                        e.preventDefault();
                        removeAt(idx);
                    });

                    grid.appendChild(card);
                });
            };

            openBtn?.addEventListener("click", () => input.click());
            clearBtn?.addEventListener("click", clearAll);

            input.addEventListener("change", () => {
                const files = getFiles();
                const valid = validateFiles(files);

                if (valid.length !== files.length) setFiles(valid);

                if (valid.length && newPrimaryHidden.value === "") newPrimaryHidden.value = "0";
                if (!valid.length) {
                    newPrimaryHidden.value = "";
                    revokeAll();
                }

                render();
            });

            const prevent = (e) => {
                e.preventDefault();
                e.stopPropagation();
            };

            ["dragenter", "dragover"].forEach(evt => {
                dropzone?.addEventListener(evt, (e) => {
                    prevent(e);
                    dropzone.classList.add("is-dragover");
                });
            });

            ["dragleave", "drop"].forEach(evt => {
                dropzone?.addEventListener(evt, (e) => {
                    prevent(e);
                    dropzone.classList.remove("is-dragover");
                });
            });

            dropzone?.addEventListener("drop", (e) => {
                const dropped = Array.from(e.dataTransfer?.files || []);
                const images = validateFiles(dropped);
                if (!images.length) return;

                const current = getFiles();
                const total = current.length + images.length;

                if (total > MAX_FILES) {
                    alert(
                        `Maximum ${MAX_FILES} files allowed. You have ${current.length} and tried to add ${images.length}.`
                    );
                    images.splice(MAX_FILES - current.length); // keep allowed
                }

                const merged = current.concat(images);
                setFiles(merged);
                if (merged.length && newPrimaryHidden.value === "") newPrimaryHidden.value = "0";
                render();
            });

            window.addEventListener("unload", revokeAll);
            render();
        })();
    </script>

@endsection
