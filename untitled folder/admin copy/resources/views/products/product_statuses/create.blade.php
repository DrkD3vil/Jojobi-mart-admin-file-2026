@extends('layouts.app')

@section('content')

{{-- ✅ Context Line --}}
<div style="margin:10px 0 16px; padding:10px 14px; border-radius:14px; border:1px solid var(--border-color); background:var(--accent); display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
    <span style="display:inline-flex; width:10px; height:10px; border-radius:999px; background: {{ $product ? 'var(--success)' : 'var(--text-muted)' }};"></span>

    @if($product)
        <strong style="color:var(--text-primary);">
            Adding Status For:
        </strong>
        <span style="font-weight:700; color:var(--accent-color);">
            {{ $product->name }}
        </span>
        <span style="color:var(--text-secondary); font-size:.9rem;">
            (ID: {{ $product->id }})
        </span>
    @else
        <strong style="color:var(--text-primary);">
            Template Mode:
        </strong>
        <span style="color:var(--text-secondary); font-weight:700;">
            No product selected — this will be saved as a reusable template.
        </span>
    @endif
</div>



<div class="container fade-in">
    <div class="page-header slide-up">
        <div class="breadcrumb-nav">
            @if($product)
                <a href="{{ route('products.index') }}" class="breadcrumb-link">
                    <svg class="breadcrumb-icon" viewBox="0 0 24 24">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                    </svg>
                    Products
                </a>
                <span class="breadcrumb-separator">/</span>
                <a href="{{ route('products.edit', $product->id) }}" class="breadcrumb-link">
                    {{ $product->name }}
                </a>
                <span class="breadcrumb-separator">/</span>
            @endif
            <span class="breadcrumb-current">{{ $product ? 'Add Status' : 'Create Template' }}</span>
        </div>
        <h1 class="page-title">
            {{ $product ? 'Add Status to: ' . $product->name : 'Create Status Template' }}
        </h1>
        <p class="page-subtitle">
            {{ $product ? 'Add a new status badge to highlight your product' : 'Create a reusable status template for products' }}
        </p>
    </div>

    <div class="form-container pop-in">
        <form method="POST" action="{{ route('product.status.store') }}" id="statusForm" class="status-form">
            @csrf

            <div class="form-card glass-effect">
                <!-- Hidden product binding -->
                @if($product)
                    <input type="hidden" name="product_uuid" value="{{ $product->uuid }}">
                @endif

                <!-- Template Selection Section -->
                <div class="form-section template-section slide-up">
                    <div class="form-header">
                        <div class="form-header-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="form-section-title">Start with a Template</h3>
                            <p class="form-section-subtitle">Select an existing template or create a new one</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="templateSelect" class="form-label">
                            <svg viewBox="0 0 24 24" class="label-icon">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-5 14H4v-4h11v4zm0-5H4V9h11v4zm5 5h-4V9h4v9z"/>
                            </svg>
                            Use Existing Template
                        </label>
                        <div class="select-wrapper">
                            <select name="template_id" id="templateSelect" class="form-select">
                                <option value="">-- Create New Status --</option>
                                @foreach($templates as $template)
                                    <option
                                        value="{{ $template->id }}"
                                        data-name="{{ $template->name }}"
                                        data-badge-text="{{ $template->badge_text }}"
                                        data-badge-color="{{ $template->badge_color ?: '#3B82F6' }}"
                                        data-description="{{ $template->description }}"
                                    >
                                        <div class="template-option">
                                            <span class="template-name">{{ $template->name }}</span>
                                            @if($template->badge_text)
                                                <span class="template-badge" style="background-color: {{ $template->badge_color ?: '#3B82F6' }}">
                                                    {{ $template->badge_text }}
                                                </span>
                                            @endif
                                        </div>
                                    </option>
                                @endforeach
                            </select>
                            <div class="select-arrow">
                                <svg viewBox="0 0 24 24">
                                    <path d="M7 10l5 5 5-5z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="form-helper">
                            Selecting a template will auto-fill the form below
                        </div>
                    </div>
                </div>

                <!-- Divider with animation -->
                <div class="section-divider slide-up">
                    <span>OR</span>
                </div>

                <!-- Status Details Section -->
                <div class="form-section status-details-section slide-up" style="animation-delay: 0.1s;">
                    <div class="form-header">
                        <div class="form-header-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="form-section-title">Status Details</h3>
                            <p class="form-section-subtitle">Customize your status information</p>
                        </div>
                    </div>

                    <!-- Status Name -->
                    <div class="form-group">
                        <label for="name" class="form-label">
                            <svg viewBox="0 0 24 24" class="label-icon">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-5 14H4v-4h11v4zm0-5H4V9h11v4zm5 5h-4V9h4v9z"/>
                            </svg>
                            Status Name
                            <span class="required-indicator">*</span>
                        </label>
                        <div class="input-wrapper">
                            <div class="input-icon">
                                <svg viewBox="0 0 24 24">
                                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-5 14H4v-4h11v4zm0-5H4V9h11v4zm5 5h-4V9h4v9z"/>
                                </svg>
                            </div>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-input"
                                placeholder="e.g., Hot, Sale, Featured, New Arrival"
                                value="{{ old('name') }}"
                                required
                            >
                            <div class="input-focus-line"></div>
                        </div>
                        <div class="form-helper">
                            {{ $product ? 'Unique per product. Slug will be auto-generated.' : 'Name for your reusable template' }}
                        </div>
                        @error('name')
                            <div class="error-message show">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Badge Section -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="badge_text" class="form-label">
                                <svg viewBox="0 0 24 24" class="label-icon">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                                Badge Text
                            </label>
                            <div class="input-wrapper">
                                <div class="input-icon">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    id="badge_text"
                                    name="badge_text"
                                    class="form-input"
                                    placeholder="e.g., HOT, SALE, NEW, FEATURED"
                                    value="{{ old('badge_text') }}"
                                >
                                <div class="input-focus-line"></div>
                            </div>
                            <div class="form-helper">
                                Text displayed on the badge (optional)
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="badge_color" class="form-label">
                                <svg viewBox="0 0 24 24" class="label-icon">
                                    <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/>
                                </svg>
                                Badge Color
                            </label>
                            <div class="color-picker-container">
                                <input
                                    type="color"
                                    id="badge_color"
                                    name="badge_color"
                                    class="color-picker"
                                    value="{{ old('badge_color', '#3B82F6') }}"
                                >
                                <input
                                    type="text"
                                    id="badge_color_hex"
                                    class="form-input"
                                    placeholder="#3B82F6"
                                    value="{{ old('badge_color', '#3B82F6') }}"
                                >
                            </div>
                            <div class="form-helper">
                                Color for the badge display
                            </div>
                            @error('badge_color')
                                <div class="error-message show">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Badge Preview -->
                    <div class="form-group">
                        <label class="form-label">Badge Preview</label>
                        <div class="badge-preview-container">
                            <span class="badge-preview" id="badgePreview">
                                {{ old('badge_text', 'PREVIEW') }}
                            </span>
                        </div>
                        <div class="form-helper">
                            How the badge will appear on products
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label for="description" class="form-label">
                            <svg viewBox="0 0 24 24" class="label-icon">
                                <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                            </svg>
                            Description
                        </label>
                        <div class="textarea-wrapper">
                            <div class="textarea-icon">
                                <svg viewBox="0 0 24 24">
                                    <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                                </svg>
                            </div>
                            <textarea
                                id="description"
                                name="description"
                                class="form-textarea"
                                placeholder="Optional description for this status..."
                                rows="3"
                            >{{ old('description') }}</textarea>
                            <div class="textarea-focus-line"></div>
                        </div>
                        <div class="form-helper">
                            Internal notes about this status (optional)
                        </div>
                    </div>
                </div>

                <!-- Form Footer -->
                <div class="form-footer slide-up" style="animation-delay: 0.2s;">
                    <a href="{{ $product ? route('products.edit', $product->id) : route('products.index') }}" class="btn-secondary">
                        <svg viewBox="0 0 24 24">
                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                        </svg>
                        Cancel
                    </a>
                    <button type="submit" class="btn-primary" id="submitBtn">
                        <svg viewBox="0 0 24 24">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                        </svg>
                        <span id="submitText">
                            {{ $product ? 'Add Status to Product' : 'Create Template' }}
                        </span>
                        <span id="loadingSpinner" class="loading-spinner" style="display: none;"></span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    /* Animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
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

    @keyframes popIn {
        0% {
            opacity: 0;
            transform: scale(0.95) translateY(20px);
        }
        100% {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .fade-in {
        animation: fadeIn 0.5s ease-out;
    }

    .slide-up {
        animation: slideUp 0.4s ease-out forwards;
        opacity: 0;
    }

    .pop-in {
        animation: popIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    /* Layout */
    .container {
        max-width: 800px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    /* Page Header */
    .page-header {
        margin-bottom: 2rem;
    }

    .breadcrumb-nav {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    .breadcrumb-link {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-secondary);
        text-decoration: none;
        transition: color var(--transition-normal);
        padding: 0.25rem 0.5rem;
        border-radius: var(--radius);
    }

    .breadcrumb-link:hover {
        color: var(--accent-color);
        background: var(--accent-glow);
    }

    .breadcrumb-icon {
        width: 16px;
        height: 16px;
        fill: currentColor;
    }

    .breadcrumb-separator {
        color: var(--text-muted);
    }

    .breadcrumb-current {
        color: var(--text-primary);
        font-weight: 500;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 0.5rem 0;
        background: linear-gradient(135deg, var(--accent-color), var(--chart-2));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .page-subtitle {
        color: var(--text-secondary);
        font-size: 1.125rem;
        margin: 0;
    }

    /* Form Container */
    .form-container {
        margin-bottom: 3rem;
    }

    .form-card {
        background: var(--card);
        border: 1px solid var(--border-color);
        border-radius: calc(var(--radius) * 1.5);
        overflow: hidden;
        box-shadow: var(--card-shadow);
        transition: box-shadow var(--transition-normal), transform var(--transition-normal);
    }

    .form-card:hover {
        box-shadow: var(--card-shadow-hover);
        transform: translateY(-2px);
    }

    .glass-effect {
        background: var(--glass-base);
        backdrop-filter: blur(10px);
    }

    /* Form Sections */
    .form-section {
        padding: 2rem;
        border-bottom: 1px solid var(--border-color);
    }

    .form-section:last-child {
        border-bottom: none;
    }

    .form-header {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .form-header-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius);
        background: linear-gradient(135deg, var(--chart-1), var(--chart-4));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        flex-shrink: 0;
    }

    .form-header-icon svg {
        width: 24px;
        height: 24px;
        fill: currentColor;
    }

    .form-section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0 0 0.25rem 0;
    }

    .form-section-subtitle {
        color: var(--text-secondary);
        font-size: 0.875rem;
        margin: 0;
    }

    /* Section Divider */
    .section-divider {
        display: flex;
        align-items: center;
        text-align: center;
        padding: 1rem 2rem;
        color: var(--text-muted);
        font-size: 0.875rem;
        font-weight: 500;
    }

    .section-divider::before,
    .section-divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid var(--border-color);
    }

    .section-divider span {
        padding: 0 1rem;
    }

    /* Form Groups */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
    }

    /* Labels */
    .form-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }

    .label-icon {
        width: 16px;
        height: 16px;
        fill: var(--text-secondary);
    }

    .required-indicator {
        color: var(--danger);
        margin-left: 4px;
    }

    /* Select Wrapper */
    .select-wrapper {
        position: relative;
        background: var(--input);
        border: 2px solid var(--border-color);
        border-radius: var(--radius);
        transition: all var(--transition-normal);
    }

    .select-wrapper:focus-within {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px var(--accent-glow);
    }

    .form-select {
        width: 100%;
        padding: 0.875rem 1rem;
        background: transparent;
        border: none;
        color: var(--text-primary);
        font-size: 1rem;
        outline: none;
        appearance: none;
        cursor: pointer;
        font-family: inherit;
    }

    .select-arrow {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
        pointer-events: none;
    }

    .select-arrow svg {
        width: 20px;
        height: 20px;
        fill: currentColor;
    }

    /* Template Options */
    .template-option {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.5rem 0;
    }

    .template-name {
        font-weight: 500;
    }

    .template-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: white;
        min-width: 60px;
        text-align: center;
    }

    /* Input & Textarea Styles */
    .input-wrapper, .textarea-wrapper {
        position: relative;
        background: var(--input);
        border: 2px solid var(--border-color);
        border-radius: var(--radius);
        transition: all var(--transition-normal);
    }

    .input-wrapper:focus-within, .textarea-wrapper:focus-within {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px var(--accent-glow);
    }

    .input-icon, .textarea-icon {
        position: absolute;
        left: 1rem;
        display: flex;
        align-items: center;
        color: var(--text-secondary);
        z-index: 1;
    }

    .input-icon {
        top: 50%;
        transform: translateY(-50%);
    }

    .textarea-icon {
        top: 1rem;
    }

    .input-icon svg, .textarea-icon svg {
        width: 20px;
        height: 20px;
        fill: currentColor;
    }

    .form-input, .form-textarea {
        width: 100%;
        padding: 0.875rem 1rem 0.875rem 3rem;
        background: transparent;
        border: none;
        color: var(--text-primary);
        font-size: 1rem;
        outline: none;
        font-family: inherit;
    }

    .form-textarea {
        padding: 1rem 1rem 1rem 3rem;
        resize: vertical;
        min-height: 100px;
        line-height: 1.5;
    }

    .form-input::placeholder, .form-textarea::placeholder {
        color: var(--text-muted);
    }

    .input-focus-line, .textarea-focus-line {
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 0;
        height: 2px;
        background: linear-gradient(90deg, var(--accent-color), var(--chart-2));
        transition: all var(--transition-normal);
        transform: translateX(-50%);
    }

    .input-wrapper:focus-within .input-focus-line,
    .textarea-wrapper:focus-within .textarea-focus-line {
        width: 100%;
    }

    /* Color Picker */
    .color-picker-container {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }

    .color-picker {
        width: 48px;
        height: 48px;
        border: 2px solid var(--border-color);
        border-radius: var(--radius);
        cursor: pointer;
        background: none;
        padding: 0;
        transition: border-color var(--transition-normal);
    }

    .color-picker:hover {
        border-color: var(--accent-color);
    }

    .color-picker::-webkit-color-swatch-wrapper {
        padding: 0;
    }

    .color-picker::-webkit-color-swatch {
        border: none;
        border-radius: calc(var(--radius) - 2px);
    }

    .color-picker-container .form-input {
        flex: 1;
        padding: 0.75rem 1rem;
    }

    /* Badge Preview */
    .badge-preview-container {
        padding: 1rem;
        background: var(--accent);
        border-radius: var(--radius);
        border: 1px solid var(--border-color);
        text-align: center;
    }

    .badge-preview {
        display: inline-block;
        padding: 0.5rem 1.5rem;
        border-radius: 2rem;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background-color: {{ old('badge_color', '#3B82F6') }};
        color: {{ getContrastColor(old('badge_color', '#3B82F6')) }};
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        transition: all var(--transition-normal);
        min-width: 120px;
    }

    /* Form Helper Text */
    .form-helper {
        display: block;
        margin-top: 0.5rem;
        font-size: 0.75rem;
        color: var(--text-muted);
        line-height: 1.4;
    }

    /* Error Messages */
    .error-message {
        display: block;
        margin-top: 0.5rem;
        font-size: 0.875rem;
        color: var(--danger);
        min-height: 1.25rem;
        opacity: 0;
        transform: translateY(-5px);
        transition: all 0.2s ease;
    }

    .error-message.show {
        opacity: 1;
        transform: translateY(0);
    }

    /* Form Footer */
    .form-footer {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        padding: 1.5rem 2rem;
        border-top: 1px solid var(--border-color);
        background: var(--accent);
    }

    /* Buttons */
    .btn-primary, .btn-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: var(--radius);
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all var(--transition-normal);
        text-decoration: none;
        min-height: 44px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--accent-color), var(--chart-2));
        color: white;
        box-shadow: 0 2px 8px var(--accent-glow);
    }

    .btn-primary:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px var(--accent-glow);
    }

    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .btn-secondary {
        background: var(--accent);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }

    .btn-secondary:hover {
        background: var(--bg-tertiary);
        transform: translateY(-2px);
    }

    .btn-primary svg, .btn-secondary svg {
        width: 20px;
        height: 20px;
        fill: currentColor;
    }

    /* Loading Spinner */
    .loading-spinner {
        width: 20px;
        height: 20px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .container {
            padding: 0 1rem;
            margin: 1rem auto;
        }

        .form-section {
            padding: 1.5rem;
        }

        .form-footer {
            padding: 1.5rem;
            flex-direction: column;
        }

        .btn-primary, .btn-secondary {
            width: 100%;
        }

        .page-title {
            font-size: 1.75rem;
        }
    }

    @media (max-width: 480px) {
        .form-header {
            flex-direction: column;
            text-align: center;
            gap: 0.75rem;
        }

        .form-header-icon {
            margin: 0 auto;
        }

        .form-section-title {
            font-size: 1.125rem;
            text-align: center;
        }

        .form-section-subtitle {
            text-align: center;
        }

        .section-divider {
            padding: 1rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Template selection auto-fill
        const templateSelect = document.getElementById('templateSelect');
        const nameInput = document.getElementById('name');
        const badgeTextInput = document.getElementById('badge_text');
        const badgeColorInput = document.getElementById('badge_color');
        const badgeColorHex = document.getElementById('badge_color_hex');
        const descriptionInput = document.getElementById('description');
        const badgePreview = document.getElementById('badgePreview');

        // Color picker sync
        if (badgeColorInput && badgeColorHex) {
            badgeColorInput.addEventListener('input', function(e) {
                badgeColorHex.value = e.target.value;
                updateBadgePreview();
            });

            badgeColorHex.addEventListener('input', function(e) {
                const color = e.target.value;
                if (/^#[0-9A-F]{6}$/i.test(color)) {
                    badgeColorInput.value = color;
                    updateBadgePreview();
                }
            });
        }

        // Template selection handler
        if (templateSelect) {
            templateSelect.addEventListener('change', function() {
                const option = this.options[this.selectedIndex];

                if (option.value) {
                    // Auto-fill from template
                    if (nameInput) nameInput.value = option.dataset.name || '';
                    if (badgeTextInput) badgeTextInput.value = option.dataset.badgeText || '';
                    if (badgeColorInput) {
                        badgeColorInput.value = option.dataset.badgeColor || '#3B82F6';
                        if (badgeColorHex) badgeColorHex.value = option.dataset.badgeColor || '#3B82F6';
                    }
                    if (descriptionInput) descriptionInput.value = option.dataset.description || '';

                    // Update preview
                    updateBadgePreview();

                    // Add visual feedback
                    nameInput.parentElement.classList.add('template-filled');
                    setTimeout(() => {
                        nameInput.parentElement.classList.remove('template-filled');
                    }, 1000);
                }
            });
        }

        // Real-time badge preview updates
        if (badgeTextInput) {
            badgeTextInput.addEventListener('input', updateBadgePreview);
        }

        if (badgeColorInput) {
            badgeColorInput.addEventListener('input', updateBadgePreview);
        }

        if (badgeColorHex) {
            badgeColorHex.addEventListener('input', updateBadgePreview);
        }

        // Update badge preview function
        function updateBadgePreview() {
            if (badgePreview) {
                const badgeText = badgeTextInput ? badgeTextInput.value : 'PREVIEW';
                const color = badgeColorInput ? badgeColorInput.value : '#3B82F6';

                badgePreview.textContent = badgeText || 'PREVIEW';
                badgePreview.style.backgroundColor = color;
                badgePreview.style.color = getContrastColor(color);

                // Add animation
                badgePreview.classList.add('preview-updated');
                setTimeout(() => {
                    badgePreview.classList.remove('preview-updated');
                }, 300);
            }
        }

        // Form submission loading state
        const form = document.getElementById('statusForm');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const loadingSpinner = document.getElementById('loadingSpinner');

        if (form) {
            form.addEventListener('submit', function(e) {
                if (submitBtn && submitText && loadingSpinner) {
                    submitBtn.disabled = true;
                    submitText.style.display = 'none';
                    loadingSpinner.style.display = 'inline-block';
                }
            });
        }

        // Helper function to get contrast color
        function getContrastColor(hexColor) {
            if (!hexColor || !/^#[0-9A-F]{6}$/i.test(hexColor)) return '#FFFFFF';

            const hex = hexColor.replace('#', '');
            const r = parseInt(hex.substr(0, 2), 16);
            const g = parseInt(hex.substr(2, 2), 16);
            const b = parseInt(hex.substr(4, 2), 16);

            const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
            return luminance > 0.5 ? '#000000' : '#FFFFFF';
        }

        // Initialize badge preview
        updateBadgePreview();

        // Add CSS for template-filled state
        const style = document.createElement('style');
        style.textContent = `
            .template-filled {
                position: relative;
            }

            .template-filled::after {
                content: '✓ Template Applied';
                position: absolute;
                right: 10px;
                top: 50%;
                transform: translateY(-50%);
                background: var(--success);
                color: white;
                padding: 0.25rem 0.5rem;
                border-radius: var(--radius);
                font-size: 0.75rem;
                animation: fadeInOut 1s ease;
            }

            .preview-updated {
                animation: badgePulse 0.3s ease;
            }

            @keyframes fadeInOut {
                0%, 100% { opacity: 0; }
                50% { opacity: 1; }
            }

            @keyframes badgePulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.05); }
                100% { transform: scale(1); }
            }
        `;
        document.head.appendChild(style);
    });
</script>
@endsection
