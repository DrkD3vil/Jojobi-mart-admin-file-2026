



@extends('layouts.app')

@section('content')
<div class="container animate-fade-in">

    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="header-content">
            <h1 class="page-title">Product Images</h1>
            <p class="page-subtitle">{{ $product->name }}</p>

            {{-- Breadcrumb --}}
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <div class="breadcrumb-item">
                    <a href="" class="breadcrumb-link">
                        <svg class="input-svg" viewBox="0 0 24 24">
                            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                        </svg>
                        Dashboard
                    </a>
                </div>
                <div class="breadcrumb-item">
                    <a href="{{ route('products.index') }}" class="breadcrumb-link">
                        <svg class="input-svg" viewBox="0 0 24 24">
                            <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                        </svg>
                        Products
                    </a>
                </div>
                <div class="breadcrumb-item">
                    <a href="{{ route('products.edit', $product) }}" class="breadcrumb-link">
                        <svg class="input-svg" viewBox="0 0 24 24">
                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                        </svg>
                        {{ Str::limit($product->name, 20) }}
                    </a>
                </div>
                <div class="breadcrumb-item">
                    <span class="breadcrumb-current">Images</span>
                </div>
            </nav>

            {{-- Status badges --}}
            <div class="page-status">
                <span class="status-badge {{ $product->is_active ? 'active' : 'inactive' }}">
                    <svg class="input-svg" viewBox="0 0 24 24">
                        @if($product->is_active)
                        <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                        @else
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                        @endif
                    </svg>
                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                </span>

                <span class="status-badge info">
                    <svg class="input-svg" viewBox="0 0 24 24">
                        <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                    </svg>
                    {{ $product->images->count() }} image(s)
                </span>
            </div>
        </div>

        <div class="header-actions">
            <a href="{{ route('products.edit', $product) }}" class="header-btn secondary">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                </svg>
                Back to Product
            </a>
        </div>
    </div>

    {{-- Upload Card --}}
    <div class="cardx mb-5 animate-slide-up">
        <div class="cardx-hd">
            <div>
                <h2 class="title">Upload New Images</h2>
                <p class="subtle">Upload images for your product. First image will be set as primary.</p>
            </div>
            <span class="pill success">
                {{ $product->images->count() }} / 10
            </span>
        </div>

        <div class="p-4">
            <form method="POST"
                  action="{{ route('products.images.store', $product) }}"
                  enctype="multipart/form-data"
                  id="productImageUploadForm"
                  class="upload-container">
                @csrf

                <div class="upload-area" id="productImageUploadArea">
                    <input type="file"
                           name="images[]"
                           multiple
                           required
                           accept="image/*"
                           id="productFileInput"
                           class="upload-input">

                    <div class="upload-content">
                        <div class="upload-icon">
                            <svg viewBox="0 0 24 24" width="64" height="64" fill="currentColor">
                                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                            </svg>
                        </div>

                        <div class="upload-text">
                            <div class="upload-title">Drop images here or click to upload</div>
                            <div class="upload-subtitle">Supports JPG, PNG, WebP up to 5MB each</div>
                            <div class="upload-requirements">Maximum 10 images per product</div>
                        </div>
                    </div>
                </div>

                {{-- Selected files preview --}}
                <div class="preview-container" id="productPreviewContainer"></div>

                <div class="form-actions">
                    <button type="button"
                            class="btnx-ghost"
                            onclick="productImageClearSelection()"
                            id="productClearBtn"
                            style="display: none;">
                        Clear Selection
                    </button>

                    <button type="submit"
                            class="btnx"
                            id="productUploadBtn"
                            disabled>
                        <span id="productUploadBtnText">Upload Images</span>
                        <span class="spin" id="productUploadSpinner" style="display: none;"></span>
                    </button>
                </div>
            </form>

            <div class="hintbar">
                <span class="subtle">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" style="vertical-align: -3px; margin-right: 4px;">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                    </svg>
                    Tip: Use high-quality images (minimum 800×800px) for best results
                </span>

                <span class="subtle">
                    Remaining: {{ 10 - $product->images->count() }} images
                </span>
            </div>
        </div>
    </div>

    {{-- Image Gallery --}}
    <div class="cardx animate-slide-up-delay">
        <div class="cardx-hd">
            <div>
                <h2 class="title">Product Gallery</h2>
                <p class="subtle">Manage and organize your product images</p>
            </div>

            @if($product->images->count() > 0)
            <div class="actions">
                <span class="pill info">
                    {{ $product->images->where('is_primary', true)->count() }} primary
                </span>
            </div>
            @endif
        </div>

        <div class="p-4">
            @if($product->images->count() > 0)
                <div class="pf-grid">
                    @foreach($product->images as $image)
                        <div class="pf-thumb animate-pop-in" style="animation-delay: {{ $loop->index * 0.05 }}s;">
                            <div class="image-preview">
                                <img src="{{ asset('storage/'.$image->image_path) }}"
                                     alt="Product Image"
                                     loading="lazy">

                                @if($image->is_primary)
                                    <div class="preview-remove primary-badge">
                                        Primary
                                    </div>
                                @endif

                                <div class="preview-remove delete-btn"
                                     onclick="productImageConfirmDelete('{{ $image->id }}')">
                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                    </svg>
                                </div>
                            </div>

                            <div class="pf-thumb-bar">
                                <div class="pf-thumb-info">
                                    <div class="pf-file-name">
                                        Image #{{ $loop->iteration }}
                                    </div>
                                    <div class="pf-file-size">
                                        {{ $image->created_at->diffForHumans() }}
                                    </div>
                                </div>

                                <div class="pf-thumb-actions">
                                    @if(!$image->is_primary)
                                        <form method="POST"
                                              action="{{ route('products.images.primary', ['product' => $product, 'image' => $image]) }}"
                                              class="inline-form">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="pf-primary-pick"
                                                    onclick="return productImageConfirmPrimary()">
                                                <input type="radio" class="pf-primary-radio" checked disabled>
                                                Set Primary
                                            </button>
                                        </form>
                                    @endif

                                    <button class="pf-icon-btn view-btn" onclick="productImageViewImage('{{ asset('storage/'.$image->image_path) }}')">
                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                                            <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg viewBox="0 0 24 24" width="80" height="80" fill="currentColor">
                            <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                        </svg>
                    </div>
                    <div class="empty-title">No images yet</div>
                    <div class="empty-description">
                        Upload images to showcase your product from different angles.
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Image Modal --}}
<div class="modalwrap" id="productImageModal">
    <div class="overlay" onclick="productImageCloseModal()"></div>
    <div class="modalx cardx">
        <div class="cardx-hd">
            <h3 class="title">Image Preview</h3>
            <button class="btnx-ghost icon" onclick="productImageCloseModal()">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                    <path d="M18.3 5.71L12 12l6.3 6.29-1.41 1.42L12 13.41l-6.89 6.3-1.41-1.42L10.59 12 3.7 5.71 5.11 4.29 12 10.59l6.89-6.3z"/>
                </svg>
            </button>
        </div>
        <div class="p-4" style="text-align: center;">
            <img id="productModalImage" src="" alt="Preview" class="modal-image">
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modalwrap" id="productDeleteModal">
    <div class="overlay" onclick="productImageCloseDeleteModal()"></div>
    <div class="modalx cardx modal-small">
        <div class="cardx-hd">
            <h3 class="title">Confirm Delete</h3>
        </div>
        <div class="p-4">
            <p class="modal-text">
                Are you sure you want to delete this image? This action cannot be undone.
            </p>

            <form method="POST" action="" id="productDeleteForm">
                @csrf
                @method('DELETE')
                <div class="modal-actions">
                    <button type="button" class="btnx-ghost" onclick="productImageCloseDeleteModal()">
                        Cancel
                    </button>
                    <button type="submit" class="btnx danger">
                        Delete Image
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Toast Container --}}
<div class="toast-stack" id="productToastStack"></div>

<style>
    /* Container */
    .container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    /* Page Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 2rem;
        margin-bottom: 2rem;
        padding: 1.5rem;
        background: var(--bg-secondary);
        border-radius: var(--radius);
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
    }

    .header-content {
        flex: 1;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 0.25rem;
    }

    .page-subtitle {
        font-size: 0.95rem;
        color: var(--text-secondary);
        margin: 0 0 1rem;
    }

    /* Breadcrumb */
    .breadcrumb {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
        font-size: 0.875rem;
    }

    .breadcrumb-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .breadcrumb-link {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        color: var(--text-secondary);
        text-decoration: none;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        transition: all var(--transition-fast);
    }

    .breadcrumb-link:hover {
        color: var(--text-primary);
        background: var(--bg-tertiary);
    }

    .breadcrumb-current {
        color: var(--text-primary);
        font-weight: 500;
        padding: 0.25rem 0.5rem;
    }

    .input-svg {
        width: 16px;
        height: 16px;
        fill: currentColor;
    }

    /* Page Status */
    .page-status {
        display: flex;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 500;
        line-height: 1;
    }

    .status-badge.active {
        background: var(--success);
        color: white;
    }

    .status-badge.inactive {
        background: var(--muted);
        color: var(--text-secondary);
    }

    .status-badge.info {
        background: var(--info);
        color: white;
    }

    /* Header Actions */
    .header-actions {
        display: flex;
        gap: 0.75rem;
    }

    .header-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        border-radius: var(--radius);
        font-weight: 500;
        font-size: 0.875rem;
        text-decoration: none;
        transition: all var(--transition-fast);
        border: 1px solid;
        cursor: pointer;
    }

    .header-btn.secondary {
        background: var(--bg-secondary);
        border-color: var(--border-color);
        color: var(--text-primary);
    }

    .header-btn.secondary:hover {
        background: var(--bg-tertiary);
        border-color: var(--accent-color);
    }

    /* Card */
    .cardx {
        background: var(--bg-secondary);
        border-radius: var(--radius);
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        transition: all var(--transition-normal);
    }

    .cardx:hover {
        box-shadow: var(--card-shadow-hover);
    }

    .cardx-hd {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
    }

    .title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .subtle {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin: 0.25rem 0 0;
    }

    .pill {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 500;
        line-height: 1;
    }

    .pill.success {
        background: var(--success);
        color: white;
    }

    .pill.info {
        background: var(--info);
        color: white;
    }

    /* Upload Area */
    .upload-container {
        padding: 0.5rem;
    }

    .upload-area {
        position: relative;
        border: 2px dashed var(--border-color);
        border-radius: var(--radius);
        padding: 3rem 2rem;
        text-align: center;
        cursor: pointer;
        transition: all var(--transition-normal);
        background: var(--bg-tertiary);
    }

    .upload-area:hover,
    .upload-area.is-dragover {
        border-color: var(--accent-color);
        background: var(--bg-tertiary);
    }

    .upload-input {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }

    .upload-content {
        pointer-events: none;
    }

    .upload-icon {
        margin: 0 auto 1rem;
        color: var(--text-secondary);
    }

    .upload-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .upload-subtitle {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin-bottom: 0.25rem;
    }

    .upload-requirements {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    /* Preview Container */
    .preview-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 1rem;
        margin-top: 1.5rem;
        display: none;
    }

    .preview-image {
        position: relative;
        border-radius: var(--radius);
        overflow: hidden;
        aspect-ratio: 1;
        background: var(--bg-tertiary);
    }

    .preview-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .preview-remove {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--danger);
        color: white;
        display: grid;
        place-items: center;
        cursor: pointer;
        transition: all var(--transition-fast);
        border: none;
        padding: 0;
    }

    .preview-remove:hover {
        transform: scale(1.1);
    }

    .primary-badge {
        position: absolute;
        top: 0.5rem;
        left: 0.5rem;
        background: var(--success);
        color: white;
        width: auto;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.6875rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color);
    }

    .btnx {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: var(--radius);
        font-weight: 500;
        font-size: 0.875rem;
        border: 1px solid transparent;
        background: var(--accent-color);
        color: white;
        cursor: pointer;
        transition: all var(--transition-fast);
    }

    .btnx:hover:not(:disabled) {
        background: var(--accent-hover);
        transform: translateY(-1px);
    }

    .btnx:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .btnx-ghost {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: var(--radius);
        font-weight: 500;
        font-size: 0.875rem;
        border: 1px solid var(--border-color);
        background: transparent;
        color: var(--text-primary);
        cursor: pointer;
        transition: all var(--transition-fast);
    }

    .btnx-ghost:hover {
        background: var(--bg-tertiary);
        border-color: var(--accent-color);
    }

    .btnx.danger {
        background: var(--danger);
        border-color: var(--danger);
    }

    .btnx.danger:hover {
        background: var(--danger);
        opacity: 0.9;
    }

    .btnx.icon {
        padding: 0.5rem;
        width: 36px;
        height: 36px;
        display: grid;
        place-items: center;
    }

    .spin {
        width: 16px;
        height: 16px;
        border: 2px solid transparent;
        border-top-color: white;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Hint Bar */
    .hintbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1rem;
        padding: 0.75rem 1rem;
        background: var(--bg-tertiary);
        border-radius: var(--radius);
        font-size: 0.8125rem;
    }

    /* Product Gallery Grid */
    .pf-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    .pf-thumb {
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        overflow: hidden;
        background: var(--bg-secondary);
        transition: all var(--transition-normal);
    }

    .pf-thumb:hover {
        transform: translateY(-4px);
        box-shadow: var(--card-shadow-hover);
        border-color: var(--accent-color);
    }

    .image-preview {
        position: relative;
        aspect-ratio: 16/9;
        background: var(--bg-tertiary);
    }

    .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .pf-thumb-bar {
        padding: 1rem;
        background: var(--glass-base);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
        backdrop-filter: blur(10px);
    }

    .pf-thumb-info {
        flex: 1;
        min-width: 0;
    }

    .pf-file-name {
        font-weight: 600;
        font-size: 0.875rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: var(--text-primary);
    }

    .pf-file-size {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-top: 0.125rem;
    }

    .pf-thumb-actions {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .pf-icon-btn {
        width: 38px;
        height: 38px;
        border-radius: var(--radius);
        border: 1px solid var(--border-color);
        background: var(--glass-base);
        display: grid;
        place-items: center;
        cursor: pointer;
        transition: all var(--transition-fast);
        flex-shrink: 0;
    }

    .pf-icon-btn:hover {
        border-color: var(--accent-color);
        transform: translateY(-1px);
        background: var(--bg-tertiary);
    }

    .pf-primary-pick {
        display: inline-flex;
        gap: 0.5rem;
        align-items: center;
        border: 1px solid rgba(34,197,94,.35);
        background: rgba(34,197,94,.12);
        color: var(--success);
        border-radius: 999px;
        padding: 0.45rem 0.7rem;
        font-weight: 600;
        font-size: 0.75rem;
        cursor: pointer;
        transition: all var(--transition-fast);
        text-decoration: none;
        border: none;
    }

    .pf-primary-pick:hover {
        background: rgba(34,197,94,.2);
        transform: translateY(-1px);
    }

    .pf-primary-radio {
        appearance: none;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 2px solid var(--success);
        display: inline-grid;
        place-items: center;
    }

    .pf-primary-radio:after {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--success);
        transform: scale(0);
        transition: transform var(--transition-fast);
    }

    .pf-primary-radio:checked:after {
        transform: scale(1);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
    }

    .empty-icon {
        margin: 0 auto 1.5rem;
        color: var(--text-secondary);
        opacity: 0.5;
    }

    .empty-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .empty-description {
        font-size: 0.875rem;
        color: var(--text-secondary);
        max-width: 400px;
        margin: 0 auto;
    }

    /* Modal */
    .modalwrap {
        position: fixed;
        inset: 0;
        z-index: 50;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .modalwrap.show {
        display: flex;
        animation: fadeIn var(--transition-fast);
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
    }

    .modalx {
        position: relative;
        z-index: 10;
        max-width: 90vw;
        max-height: 90vh;
        overflow: auto;
        animation: slideUp var(--transition-normal);
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(1rem);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-small {
        max-width: 400px;
    }

    .modal-image {
        max-width: 100%;
        max-height: 70vh;
        border-radius: var(--radius);
        object-fit: contain;
    }

    .modal-text {
        color: var(--text-primary);
        margin-bottom: 1.5rem;
        line-height: 1.5;
    }

    .modal-actions {
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
    }

    /* Toast */
    .toast-stack {
        position: fixed;
        bottom: 1rem;
        right: 1rem;
        z-index: 100;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        max-width: 400px;
    }

    .toastx {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        box-shadow: var(--dropdown-shadow);
        overflow: hidden;
        animation: slideInRight var(--transition-fast);
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100%);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .toastx-progress {
        height: 3px;
        background: var(--border-color);
        overflow: hidden;
    }

    .toastx-progress div {
        height: 100%;
        background: currentColor;
        animation: progress linear;
    }

    .toastx.success .toastx-progress div {
        color: var(--success);
    }

    .toastx.danger .toastx-progress div,
    .toastx.error .toastx-progress div {
        color: var(--danger);
    }

    @keyframes progress {
        from { width: 100%; }
        to { width: 0; }
    }

    .toastx-hd {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 1rem 0.5rem;
    }

    .toastx-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        font-size: 0.875rem;
        color: var(--text-primary);
    }

    .toastx-title .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .toastx-close {
        background: none;
        border: none;
        color: var(--text-secondary);
        font-size: 1.25rem;
        cursor: pointer;
        padding: 0;
        width: 24px;
        height: 24px;
        display: grid;
        place-items: center;
        border-radius: 0.25rem;
    }

    .toastx-close:hover {
        background: var(--bg-tertiary);
    }

    .toastx-body {
        padding: 0 1rem 0.75rem 1rem;
        font-size: 0.8125rem;
        color: var(--text-secondary);
        line-height: 1.4;
    }

    /* Animations */
    .animate-fade-in {
        animation: fadeIn var(--transition-normal);
    }

    .animate-slide-up {
        animation: slideUp var(--transition-normal);
    }

    .animate-slide-up-delay {
        animation: slideUp var(--transition-normal) 0.1s both;
    }

    .animate-pop-in {
        animation: popIn var(--transition-fast) both;
    }

    @keyframes popIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* Utilities */
    .p-4 {
        padding: 1.5rem;
    }

    .mb-4 {
        margin-bottom: 1.5rem;
    }

    .mb-5 {
        margin-bottom: 2rem;
    }

    .inline-form {
        display: inline;
    }

    /* Delete Button */
    .delete-btn {
        background: var(--danger) !important;
    }

    .delete-btn:hover {
        background: var(--danger) !important;
        opacity: 0.9;
    }
</style>

<script>
    // File upload handling - unique function names
    const productImageUploadArea = document.getElementById('productImageUploadArea');
    const productFileInput = document.getElementById('productFileInput');
    const productPreviewContainer = document.getElementById('productPreviewContainer');
    const productUploadBtn = document.getElementById('productUploadBtn');
    const productClearBtn = document.getElementById('productClearBtn');
    const productUploadBtnText = document.getElementById('productUploadBtnText');
    const productUploadSpinner = document.getElementById('productUploadSpinner');

    let productSelectedFiles = [];

    productImageUploadArea.addEventListener('click', () => productFileInput.click());

    productImageUploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        productImageUploadArea.classList.add('is-dragover');
    });

    productImageUploadArea.addEventListener('dragleave', () => {
        productImageUploadArea.classList.remove('is-dragover');
    });

    productImageUploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        productImageUploadArea.classList.remove('is-dragover');
        productImageHandleFiles(e.dataTransfer.files);
    });

    productFileInput.addEventListener('change', (e) => {
        productImageHandleFiles(e.target.files);
    });

    function productImageHandleFiles(files) {
        productSelectedFiles = Array.from(files);
        productImageUpdatePreview();
        productImageUpdateButtons();
    }

    function productImageUpdatePreview() {
        productPreviewContainer.innerHTML = '';

        if (productSelectedFiles.length === 0) {
            productPreviewContainer.style.display = 'none';
            return;
        }

        productPreviewContainer.style.display = 'grid';

        productSelectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const preview = document.createElement('div');
                preview.className = 'preview-image';
                preview.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <button type="button" class="preview-remove" onclick="productImageRemoveFile(${index})">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                        </svg>
                    </button>
                `;
                productPreviewContainer.appendChild(preview);
            };
            reader.readAsDataURL(file);
        });
    }

    function productImageRemoveFile(index) {
        productSelectedFiles.splice(index, 1);
        productImageUpdatePreview();
        productImageUpdateButtons();

        // Update file input
        const dataTransfer = new DataTransfer();
        productSelectedFiles.forEach(file => dataTransfer.items.add(file));
        productFileInput.files = dataTransfer.files;
    }

    function productImageClearSelection() {
        productSelectedFiles = [];
        productFileInput.value = '';
        productImageUpdatePreview();
        productImageUpdateButtons();
    }

    function productImageUpdateButtons() {
        const hasFiles = productSelectedFiles.length > 0;
        productUploadBtn.disabled = !hasFiles;
        productClearBtn.style.display = hasFiles ? 'inline-flex' : 'none';
        productUploadBtnText.textContent = hasFiles ?
            `Upload ${productSelectedFiles.length} image${productSelectedFiles.length > 1 ? 's' : ''}` :
            'Upload Images';
    }

    // Form submission
    document.getElementById('productImageUploadForm').addEventListener('submit', function(e) {
        const maxImages = 10;
        const currentImages = {{ $product->images->count() }};
        const totalImages = currentImages + productSelectedFiles.length;

        if (totalImages > maxImages) {
            e.preventDefault();
            productImageShowToast('error', 'Too many images', `You can only upload ${maxImages - currentImages} more image${maxImages - currentImages > 1 ? 's' : ''}.`);
            return;
        }

        productUploadBtnText.textContent = 'Uploading...';
        productUploadSpinner.style.display = 'inline-block';
        productUploadBtn.disabled = true;
    });

    // Image viewing
    function productImageViewImage(src) {
        document.getElementById('productModalImage').src = src;
        document.getElementById('productImageModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function productImageCloseModal() {
        document.getElementById('productImageModal').classList.remove('show');
        document.body.style.overflow = '';
    }

    // Delete confirmation
    let productImageIdToDelete = null;

    function productImageConfirmDelete(imageId) {
        productImageIdToDelete = imageId;
        const deleteForm = document.getElementById('productDeleteForm');
        deleteForm.action = `{{ route('products.images.destroy', ['product' => $product, 'image' => '__ID__']) }}`.replace('__ID__', imageId);
        document.getElementById('productDeleteModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function productImageCloseDeleteModal() {
        document.getElementById('productDeleteModal').classList.remove('show');
        document.body.style.overflow = '';
        productImageIdToDelete = null;
    }

    // Set primary confirmation
    function productImageConfirmPrimary() {
        return confirm('Set this as primary image?');
    }

    // Toast notifications
    function productImageShowToast(type, title, message) {
        const toastStack = document.getElementById('productToastStack');
        const toast = document.createElement('div');
        toast.className = `toastx ${type}`;
        toast.setAttribute('data-type', type);

        const duration = type === 'success' ? 5000 : 7000;

        toast.innerHTML = `
            <div class="toastx-progress">
                <div style="animation-duration: ${duration}ms;"></div>
            </div>
            <div class="toastx-hd">
                <div class="toastx-title">
                    <span class="dot"></span>
                    ${title}
                </div>
                <button class="toastx-close" onclick="this.parentElement.parentElement.remove()">
                    &times;
                </button>
            </div>
            <div class="toastx-body">
                ${message}
            </div>
        `;

        toastStack.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-10px)';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }

    // Handle flash messages from server
    @if(session('success'))
        productImageShowToast('success', 'Success', @json(session('success')));
    @endif

    @if(session('error'))
        productImageShowToast('danger', 'Error', @json(session('error')));
    @endif

    // Keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            productImageCloseModal();
            productImageCloseDeleteModal();
        }
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        productImageUpdateButtons();

        @if($errors->any())
            productImageShowToast('danger', 'Validation Error', 'Please check the form for errors.');
        @endif
    });
</script>
@endsection
