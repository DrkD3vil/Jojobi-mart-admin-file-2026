@extends('layouts.app')

@section('content')
<div class="container fade-in">
    <div class="page-header slide-up">
        <div class="breadcrumb-nav">
            <a href="{{ route('products.index') }}" class="breadcrumb-link">
                <svg class="breadcrumb-icon" viewBox="0 0 24 24">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
                Products
            </a>
            <span class="breadcrumb-separator">/</span>
            <a href="{{ route('products.edit', $status->product_id) }}" class="breadcrumb-link">
                {{ $product->name ?? 'Product' }}
            </a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">Edit Status</span>
        </div>
        <h1 class="page-title">Edit Product Status</h1>
        <p class="page-subtitle">Update status details for "{{ $product->name ?? 'Product' }}"</p>
    </div>

    <div class="form-container pop-in">
        <form action="{{ route('product.status.update', $status->uuid) }}" method="POST" id="statusForm" class="status-form">
            @csrf
            @method('PUT')

            <div class="form-card glass-effect">
                <div class="form-header">
                    <div class="form-header-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="form-title">Status Details</h2>
                        <p class="form-subtitle">Update the status information below</p>
                    </div>
                </div>

                <div class="form-body">

                    {{-- Select Existing Template --}}
<div class="mb-3">
    <label class="form-label">Use Existing Status Template</label>
    <select name="template_id" id="templateSelect" class="form-control">
        <option value="">-- Create New Status --</option>
        @foreach($templates as $template)
            <option
                value="{{ $template->id }}"
                data-name="{{ $template->name }}"
                data-badge-text="{{ $template->badge_text }}"
                data-badge-color="{{ $template->badge_color }}"
                data-description="{{ $template->description }}"
                {{ old('template_id', $status->is_template && $status->template_id == $template->id ? 'selected' : '') }}
            >
                {{ $template->name }}
            </option>
        @endforeach
    </select>
    <small class="text-muted">
        Selecting a template will auto-fill status for the product
    </small>
</div>


                    <!-- Status Name -->
                    <div class="form-group">
                        <label for="name" class="form-label">
                            Status Name
                            <span class="required-indicator">*</span>
                        </label>
                        <div class="input-wrapper">
                            <div class="input-icon">
                                <svg viewBox="0 0 24 24">
                                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-5 14H4v-4h11v4zm0-5H4V9h11v4zm5 5h-4V9h4v9z" />
                                </svg>
                            </div>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $status->name) }}"
                                   class="form-input"
                                   placeholder="e.g., On Sale, Featured, New Arrival"
                                   required
                                   autofocus>
                            <div class="input-focus-line"></div>
                        </div>
                        <div class="form-helper">
                            Unique per product. Slug will be auto-generated.
                        </div>
                        @error('name')
                            <div class="error-message show">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Badge Section -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="badge_text" class="form-label">Badge Text</label>
                            <div class="input-wrapper">
                                <div class="input-icon">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                    </svg>
                                </div>
                                <input type="text"
                                       id="badge_text"
                                       name="badge_text"
                                       value="{{ old('badge_text', $status->badge_text) }}"
                                       class="form-input"
                                       placeholder="e.g., SALE, HOT, NEW">
                                <div class="input-focus-line"></div>
                            </div>
                            <div class="form-helper">
                                Optional. Shows as a badge on product.
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="badge_color" class="form-label">Badge Color</label>
                            <div class="color-picker-container">
                                <input type="color"
                                       id="badge_color"
                                       name="badge_color"
                                       value="{{ old('badge_color', $status->badge_color ?: '#3B82F6') }}"
                                       class="color-picker">
                                <input type="text"
                                       id="badge_color_hex"
                                       class="form-input"
                                       placeholder="#3B82F6"
                                       value="{{ old('badge_color', $status->badge_color ?: '#3B82F6') }}">
                            </div>
                            <div class="form-helper">
                                Color for the badge
                            </div>
                            @error('badge_color')
                                <div class="error-message show">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Badge Preview -->
                    @if($status->badge_text || old('badge_text'))
                    <div class="form-group">
                        <label class="form-label">Badge Preview</label>
                        <div class="badge-preview-container">
                            <span class="badge-preview" id="badgePreview">
                                {{ old('badge_text', $status->badge_text) }}
                            </span>
                        </div>
                    </div>
                    @endif

                    <!-- Description -->
                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <div class="textarea-wrapper">
                            <div class="textarea-icon">
                                <svg viewBox="0 0 24 24">
                                    <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" />
                                </svg>
                            </div>
                            <textarea id="description"
                                      name="description"
                                      class="form-textarea"
                                      placeholder="Optional description for this status..."
                                      rows="3">{{ old('description', $status->description) }}</textarea>
                            <div class="textarea-focus-line"></div>
                        </div>
                    </div>

                    <!-- Active Status -->
                    <!-- Active Status -->
<div class="form-group">
    <div class="toggle-container">
        <div class="toggle-switch">
            <input type="checkbox"
                   class="toggle-input"
                   id="is_active"
                   name="is_active"
                   value="1"
                   {{ old('is_active', $status->is_active) ? 'checked' : '' }}>
            <label class="toggle-label" for="is_active">
                <span class="toggle-track"></span>
                <span class="toggle-thumb"></span>
                <span class="toggle-text">Active Status</span>
            </label>
        </div>
        <div class="form-helper">
            Inactive statuses won't be displayed
        </div>
    </div>
</div>


</div>
                </div>

                <div class="form-footer">
                    <a href="{{ route('products.edit', $status->product_id) }}" class="btn-secondary">
                        <svg viewBox="0 0 24 24">
                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                        </svg>
                        Cancel
                    </a>
                    <button type="submit" class="btn-primary" id="submitBtn">
                        <span id="submitText">Update Status</span>
                        <span id="loadingSpinner" class="loading-spinner" style="display: none;"></span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('templateSelect').addEventListener('change', function () {
    const option = this.options[this.selectedIndex];
    document.querySelector('[name="name"]').value = option.dataset.name || '';
    document.querySelector('[name="badge_text"]').value = option.dataset.badgeText || '';
    document.querySelector('[name="badge_color"]').value = option.dataset.badgeColor || '';
    document.querySelector('[name="description"]').value = option.dataset.description || '';
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Color picker sync
        const badgeColorInput = document.getElementById('badge_color');
        const badgeColorHex = document.getElementById('badge_color_hex');
        const badgePreview = document.getElementById('badgePreview');
        const badgeTextInput = document.getElementById('badge_text');

        function updateBadgePreview() {
            if (badgePreview && badgeTextInput && badgeTextInput.value) {
                const color = badgeColorInput ? badgeColorInput.value : '#3B82F6';
                badgePreview.style.backgroundColor = color;
                badgePreview.style.color = getContrastColor(color);
                badgePreview.textContent = badgeTextInput.value;
            }
        }

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

        if (badgeTextInput) {
            badgeTextInput.addEventListener('input', updateBadgePreview);
        }

        // Form submission loading state
        const form = document.getElementById('statusForm');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const loadingSpinner = document.getElementById('loadingSpinner');

        if (form) {
            form.addEventListener('submit', function() {
                if (submitBtn && submitText && loadingSpinner) {
                    submitBtn.disabled = true;
                    submitText.style.display = 'none';
                    loadingSpinner.style.display = 'block';
                }
            });
        }

        // Helper function to get contrast color
        function getContrastColor(hexColor) {
            if (!hexColor) return '#FFFFFF';

            // Remove the # if present
            const hex = hexColor.replace('#', '');

            if (hex.length !== 6) return '#FFFFFF';

            // Convert to RGB
            const r = parseInt(hex.substr(0, 2), 16);
            const g = parseInt(hex.substr(2, 2), 16);
            const b = parseInt(hex.substr(4, 2), 16);

            // Calculate luminance
            const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;

            // Return black or white based on luminance
            return luminance > 0.5 ? '#000000' : '#FFFFFF';
        }

        // Initialize badge preview
        updateBadgePreview();
    });
</script>

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
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes popIn {
        0% {
            opacity: 0;
            transform: scale(0.9) translateY(20px);
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
        animation: slideUp 0.6s ease-out;
    }

    .pop-in {
        animation: popIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
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

    /* Form Header */
    .form-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 2rem 2rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
    }

    .form-header-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius);
        background: linear-gradient(135deg, var(--accent-color), var(--chart-2));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .form-header-icon svg {
        width: 24px;
        height: 24px;
        fill: currentColor;
    }

    .form-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .form-subtitle {
        color: var(--text-secondary);
        font-size: 0.875rem;
        margin: 0.25rem 0 0 0;
    }

    /* Form Body */
    .form-body {
        padding: 2rem;
    }

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

    /* Form Labels */
    .form-label {
        display: block;
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }

    .required-indicator {
        color: var(--danger);
        margin-left: 4px;
    }

    /* Input Styles */
    .input-wrapper {
        position: relative;
        background: var(--input);
        border: 2px solid var(--border-color);
        border-radius: var(--radius);
        transition: all var(--transition-normal);
    }

    .input-wrapper:focus-within {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px var(--accent-glow);
    }

    .input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        display: flex;
        align-items: center;
        color: var(--text-secondary);
        z-index: 1;
    }

    .input-icon svg {
        width: 20px;
        height: 20px;
        fill: currentColor;
    }

    .form-input {
        width: 100%;
        padding: 0.875rem 1rem 0.875rem 3rem;
        background: transparent;
        border: none;
        color: var(--text-primary);
        font-size: 1rem;
        outline: none;
        font-family: inherit;
    }

    .form-input::placeholder {
        color: var(--text-muted);
    }

    .input-focus-line {
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 0;
        height: 2px;
        background: linear-gradient(90deg, var(--accent-color), var(--chart-2));
        transition: all var(--transition-normal);
        transform: translateX(-50%);
    }

    .input-wrapper:focus-within .input-focus-line {
        width: 100%;
    }

    /* Textarea */
    .textarea-wrapper {
        position: relative;
        background: var(--input);
        border: 2px solid var(--border-color);
        border-radius: var(--radius);
        transition: all var(--transition-normal);
    }

    .textarea-wrapper:focus-within {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px var(--accent-glow);
    }

    .textarea-icon {
        position: absolute;
        left: 1rem;
        top: 1rem;
        display: flex;
        align-items: center;
        color: var(--text-secondary);
        z-index: 1;
    }

    .textarea-icon svg {
        width: 20px;
        height: 20px;
        fill: currentColor;
    }

    .form-textarea {
        width: 100%;
        padding: 1rem 1rem 1rem 3rem;
        background: transparent;
        border: none;
        color: var(--text-primary);
        font-size: 1rem;
        outline: none;
        resize: vertical;
        min-height: 100px;
        font-family: inherit;
        line-height: 1.5;
    }

    .form-textarea::placeholder {
        color: var(--text-muted);
    }

    .textarea-focus-line {
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 0;
        height: 2px;
        background: linear-gradient(90deg, var(--accent-color), var(--chart-2));
        transition: all var(--transition-normal);
        transform: translateX(-50%);
    }

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
        padding: 0.75rem 1rem 0.75rem 1rem;
    }

    /* Badge Preview */
    .badge-preview-container {
        padding: 1rem;
        background: var(--accent);
        border-radius: var(--radius);
        border: 1px solid var(--border-color);
    }

    .badge-preview {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background-color: {{ $status->badge_color ?: '#3B82F6' }};
        color: {{ getContrastColor($status->badge_color) }};
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all var(--transition-normal);
    }

    /* Toggle Switch */
    .toggle-container {
        background: var(--accent);
        padding: 1rem;
        border-radius: var(--radius);
        border: 1px solid var(--border-color);
    }

    .toggle-switch {
        display: inline-flex;
        align-items: center;
        gap: 1rem;
    }

    .toggle-input {
        display: none;
    }

    .toggle-label {
        display: inline-flex;
        align-items: center;
        cursor: pointer;
        gap: 0.75rem;
        user-select: none;
    }

    .toggle-track {
        position: relative;
        width: 3rem;
        height: 1.5rem;
        background: var(--border-color);
        border-radius: 0.75rem;
        transition: all var(--transition-normal);
    }

    .toggle-thumb {
        position: absolute;
        top: 0.125rem;
        left: 0.125rem;
        width: 1.25rem;
        height: 1.25rem;
        background: white;
        border-radius: 50%;
        transition: transform var(--transition-normal);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .toggle-input:checked + .toggle-label .toggle-track {
        background: var(--success);
    }

    .toggle-input:checked + .toggle-label .toggle-thumb {
        transform: translateX(1.5rem);
    }

    .toggle-text {
        font-weight: 500;
        color: var(--text-primary);
        font-size: 0.875rem;
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
    .btn-primary,
    .btn-secondary {
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

    .btn-primary svg,
    .btn-secondary svg {
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

        .form-header {
            padding: 1.5rem 1.5rem 1rem;
        }

        .form-body {
            padding: 1.5rem;
        }

        .form-footer {
            padding: 1.5rem;
            flex-direction: column;
        }

        .btn-primary,
        .btn-secondary {
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

        .form-title {
            font-size: 1.25rem;
        }

        .form-subtitle {
            font-size: 0.75rem;
        }
    }
</style>


@endsection
