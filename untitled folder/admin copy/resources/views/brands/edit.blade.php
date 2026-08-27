@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header animate-fade-in">
        <h2 class="page-title">Edit Brand</h2>
        <p class="page-subtitle">Update brand information</p>
    </div>

    <form id="brand-form"
          method="POST"
          action="{{ route('brands.update', $brand) }}"
          enctype="multipart/form-data"
          class="brand-form animate-slide-up">
        @csrf
        @method('PUT')

        {{-- Form Card --}}
        <div class="form-card glass-effect animate-pop-in">
            {{-- Name Section --}}
            <div class="form-section">
                <div class="form-label-group">
                    <label class="form-label required">Brand Name</label>
                    <span class="form-label-hint">Required</span>
                </div>
                <div class="input-wrapper">
                    <div class="input-icon">
                        <svg class="input-svg" viewBox="0 0 24 24">
                            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-5 14H4v-4h11v4zm0-5H4V9h11v4zm5 5h-4V9h4v9z"/>
                        </svg>
                    </div>
                    <input type="text"
                           class="form-input"
                           name="name"
                           required
                           placeholder="Enter brand name"
                           value="{{ old('name', $brand->name) }}">
                    <div class="input-focus-line"></div>
                </div>
                @error('name')
                <span class="error-message show">{{ $message }}</span>
                @enderror
            </div>

            {{-- Description Section --}}
            <div class="form-section">
                <div class="form-label-group">
                    <label class="form-label">Description</label>
                    <span class="form-label-hint">Optional</span>
                </div>
                <div class="textarea-wrapper">
                    <div class="textarea-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                        </svg>
                    </div>
                    <textarea class="form-textarea"
                              name="description"
                              placeholder="Brief description about the brand..."
                              rows="3">{{ old('description', $brand->description) }}</textarea>
                    <div class="textarea-focus-line"></div>
                </div>
                @error('description')
                <span class="error-message show">{{ $message }}</span>
                @enderror
            </div>

            {{-- Current Logo --}}
            @if($brand->logo)
            <div class="form-section">
                <div class="form-label-group">
                    <label class="form-label">Current Logo</label>
                </div>
                <div class="current-image">
                    <div class="image-preview">
                        <img src="{{ asset('storage/' . $brand->logo) }}"
                             alt="{{ $brand->name }}"
                             class="brand-image">
                        <button type="button" class="preview-remove" onclick="removeLogo()" title="Remove logo">
                            <svg viewBox="0 0 24 24">
                                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                            </svg>
                        </button>
                    </div>
                    <p class="image-caption">Current logo</p>
                </div>
                <input type="hidden" name="remove_logo" id="removeLogo" value="0">
            </div>
            @endif

            {{-- Logo Upload --}}
            <div class="form-section">
                <div class="form-label-group">
                    <label class="form-label">
                        @if($brand->logo)
                            Change Logo
                        @else
                            Upload Logo
                        @endif
                    </label>
                    <span class="form-label-hint">Optional</span>
                </div>
                <div class="upload-container">
                    <div class="upload-area" id="uploadArea">
                        <input type="file"
                               class="upload-input"
                               name="logo"
                               id="logoUpload"
                               accept="image/*">
                        <div class="upload-content">
                            <div class="upload-icon">
                                <svg viewBox="0 0 24 24">
                                    <path d="M19 19H5V5h7V3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"/>
                                </svg>
                            </div>
                            <div class="upload-text">
                                <h4 class="upload-title">
                                    @if($brand->logo)
                                        Upload New Logo
                                    @else
                                        Upload Logo
                                    @endif
                                </h4>
                                <p class="upload-subtitle">Click or drag image here</p>
                                <p class="upload-requirements">PNG, JPG, SVG • Max 5MB</p>
                            </div>
                        </div>
                    </div>

                    <div class="preview-container" id="logoPreview"></div>
                </div>
                @error('logo')
                <span class="error-message show">{{ $message }}</span>
                @enderror
                <p class="input-help">Leave empty to keep current logo</p>
            </div>

            {{-- Active Status --}}
            <div class="form-section">
                <div class="form-label-group">
                    <label class="form-label">Status</label>
                </div>
                <div class="toggle-switch">
                    <input type="checkbox"
                           class="toggle-input"
                           id="is_active"
                           name="is_active"
                           value="1"
                           {{ old('is_active', $brand->is_active) ? 'checked' : '' }}>
                    <label class="toggle-label" for="is_active">
                        <span class="toggle-track"></span>
                        <span class="toggle-thumb"></span>
                        <span class="toggle-text">Active</span>
                    </label>
                </div>
                <p class="toggle-help">Inactive brands won't appear in product listings</p>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="form-actions animate-fade-in-delay">
            <a href="{{ route('brands.index') }}" class="btn-secondary">
                <svg viewBox="0 0 24 24">
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                </svg>
                Cancel
            </a>
            <button type="submit" class="btn-primary">
                <svg viewBox="0 0 24 24">
                    <path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/>
                </svg>
                Update Brand
            </button>
        </div>
    </form>
</div>

<style>
/* Animations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
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
        transform: scale(0.95);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

/* Animation Classes */
.animate-fade-in {
    animation: fadeIn var(--transition-normal, 250ms) ease-out;
}

.animate-slide-up {
    animation: slideUp var(--transition-normal, 250ms) ease-out;
}

.animate-pop-in {
    animation: popIn var(--transition-normal, 250ms) cubic-bezier(0.34, 1.56, 0.64, 1);
}

.animate-fade-in-delay {
    animation: fadeIn var(--transition-normal, 250ms) ease-out 0.2s both;
}

/* Page Header */
.page-header {
    margin-bottom: 2rem;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary, oklch(0.985 0 0));
    margin-bottom: 0.5rem;
    background: linear-gradient(135deg, 
        var(--text-primary, oklch(0.985 0 0)), 
        var(--text-secondary, oklch(0.708 0 0)));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.page-subtitle {
    color: var(--text-secondary, oklch(0.708 0 0));
    font-size: 1rem;
}

/* Form Card */
.form-card {
    background: var(--card, oklch(0.205 0 0));
    border: 1px solid var(--border-color, oklch(0.9 0 0));
    border-radius: var(--radius, 0.625rem);
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--card-shadow, 0 2px 4px 0 rgb(0 0 0 / 0.25));
}

.glass-effect {
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    background: var(--glass-base, oklch(0.205 0 0 / 0.7));
}

/* Form Sections */
.form-section {
    margin-bottom: 1.5rem;
}

.form-label-group {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.form-label {
    font-weight: 600;
    color: var(--text-primary, oklch(0.985 0 0));
    font-size: 0.95rem;
}

.form-label.required::after {
    content: '*';
    color: var(--danger, oklch(0.704 0.191 22.216));
    margin-left: 4px;
}

.form-label-hint {
    font-size: 0.85rem;
    color: var(--text-muted, oklch(0.708 0 0 / 0.7));
    background: var(--bg-tertiary, var(--secondary, oklch(0.269 0 0)));
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    border: 1px solid var(--border-color, oklch(0.9 0 0));
}

/* Input Styles */
.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    background: var(--input, oklch(1 0 0 / 15%));
    border: 2px solid var(--border-color, oklch(0.9 0 0));
    border-radius: var(--radius, 0.625rem);
    transition: all var(--transition-normal, 250ms) ease;
}

.input-wrapper:focus-within {
    border-color: var(--accent-color, oklch(0.488 0.243 264.376));
    box-shadow: 0 0 0 3px var(--accent-glow, oklch(0.488 0.243 264.376 / 0.2));
}

.form-input {
    flex: 1;
    padding: 0.875rem 1rem 0.875rem 3rem;
    background: transparent;
    border: none;
    color: var(--text-primary, oklch(0.985 0 0));
    font-size: 1rem;
    outline: none;
    width: 100%;
    font-weight: 500;
}

.form-input::placeholder {
    color: var(--text-muted, oklch(0.708 0 0 / 0.7));
}

.input-icon {
    position: absolute;
    left: 1rem;
    display: flex;
    align-items: center;
    color: var(--text-secondary, oklch(0.708 0 0));
}

.input-svg {
    width: 1.25rem;
    height: 1.25rem;
    fill: currentColor;
}

.input-focus-line {
    position: absolute;
    bottom: 0;
    left: 50%;
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, 
        var(--accent-color, oklch(0.488 0.243 264.376)), 
        var(--info, oklch(0.488 0.243 264.376)));
    transition: all var(--transition-normal, 250ms) ease;
    transform: translateX(-50%);
}

.form-input:focus ~ .input-focus-line {
    width: 100%;
}

/* Textarea Styles */
.textarea-wrapper {
    position: relative;
    background: var(--input, oklch(1 0 0 / 15%));
    border: 2px solid var(--border-color, oklch(0.9 0 0));
    border-radius: var(--radius, 0.625rem);
    transition: all var(--transition-normal, 250ms) ease;
}

.textarea-wrapper:focus-within {
    border-color: var(--accent-color, oklch(0.488 0.243 264.376));
    box-shadow: 0 0 0 3px var(--accent-glow, oklch(0.488 0.243 264.376 / 0.2));
}

.textarea-icon {
    position: absolute;
    top: 1rem;
    left: 1rem;
    color: var(--text-secondary, oklch(0.708 0 0));
}

.textarea-icon svg {
    width: 1.25rem;
    height: 1.25rem;
    fill: currentColor;
}

.form-textarea {
    width: 100%;
    padding: 1rem 1rem 1rem 3rem;
    background: transparent;
    border: none;
    color: var(--text-primary, oklch(0.985 0 0));
    font-size: 1rem;
    outline: none;
    resize: vertical;
    min-height: 100px;
    font-family: inherit;
    font-weight: 500;
}

.form-textarea::placeholder {
    color: var(--text-muted, oklch(0.708 0 0 / 0.7));
}

.textarea-focus-line {
    position: absolute;
    bottom: 0;
    left: 50%;
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, 
        var(--accent-color, oklch(0.488 0.243 264.376)), 
        var(--info, oklch(0.488 0.243 264.376)));
    transition: all var(--transition-normal, 250ms) ease;
    transform: translateX(-50%);
}

.form-textarea:focus ~ .textarea-focus-line {
    width: 100%;
}

/* Current Image */
.current-image {
    animation: slideUp 0.3s ease-out;
}

.image-preview {
    position: relative;
    width: 120px;
    height: 120px;
    border-radius: var(--radius, 0.625rem);
    overflow: hidden;
    border: 2px solid var(--border-color, oklch(0.9 0 0));
    transition: all var(--transition-normal, 250ms) ease;
    background: var(--glass-base, rgba(255, 255, 255, 0.85));
}

.image-preview:hover {
    transform: scale(1.02);
    box-shadow: 0 4px 12px var(--accent-glow, oklch(0.488 0.243 264.376 / 0.2));
    border-color: var(--accent-color, oklch(0.488 0.243 264.376));
}

.brand-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 0.5rem;
    background: var(--glass-base, rgba(255, 255, 255, 0.85));
}

.preview-remove {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    background: var(--danger, oklch(0.704 0.191 22.216));
    color: var(--sidebar-primary-foreground, #fff);
    border: none;
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all var(--transition-fast, 150ms) ease;
}

.image-preview:hover .preview-remove {
    opacity: 1;
}

.preview-remove:hover {
    transform: scale(1.1) rotate(90deg);
    background: color-mix(in srgb, var(--danger, oklch(0.704 0.191 22.216)) 90%, #000);
}

.preview-remove svg {
    width: 0.875rem;
    height: 0.875rem;
    fill: currentColor;
}

.image-caption {
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: var(--text-muted, oklch(0.708 0 0 / 0.7));
    font-weight: 500;
}

/* Upload Container */
.upload-container {
    position: relative;
}

.upload-area {
    border: 2px dashed var(--border-color, oklch(0.9 0 0));
    border-radius: var(--radius, 0.625rem);
    padding: 2rem;
    background: var(--input, oklch(1 0 0 / 15%));
    cursor: pointer;
    transition: all var(--transition-normal, 250ms) ease;
    position: relative;
    overflow: hidden;
}

.upload-area:hover {
    border-color: var(--accent-color, oklch(0.488 0.243 264.376));
    background: var(--accent, oklch(0.269 0 0));
}

.upload-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.upload-content {
    text-align: center;
}

.upload-icon {
    margin-bottom: 1rem;
    color: var(--text-secondary, oklch(0.708 0 0));
    transition: all var(--transition-normal, 250ms) ease;
}

.upload-area:hover .upload-icon {
    color: var(--accent-color, oklch(0.488 0.243 264.376));
}

.upload-icon svg {
    width: 3rem;
    height: 3rem;
    fill: currentColor;
}

.upload-text {
    color: var(--text-primary, oklch(0.985 0 0));
}

.upload-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.upload-subtitle {
    font-size: 0.9rem;
    color: var(--text-secondary, oklch(0.708 0 0));
    margin-bottom: 0.25rem;
}

.upload-requirements {
    font-size: 0.8rem;
    color: var(--text-muted, oklch(0.708 0 0 / 0.7));
}

/* Preview Container */
.preview-container {
    margin-top: 1rem;
}

.preview-image {
    position: relative;
    width: 120px;
    height: 120px;
    border-radius: var(--radius, 0.625rem);
    overflow: hidden;
    border: 2px solid var(--border-color, oklch(0.9 0 0));
    animation: popIn 0.3s ease-out;
    background: var(--glass-base, rgba(255, 255, 255, 0.85));
}

.preview-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 0.5rem;
    background: var(--glass-base, rgba(255, 255, 255, 0.85));
}

.preview-remove {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    background: var(--danger, oklch(0.704 0.191 22.216));
    color: var(--sidebar-primary-foreground, #fff);
    border: none;
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity var(--transition-fast, 150ms) ease;
}

.preview-image:hover .preview-remove {
    opacity: 1;
}

.preview-remove svg {
    width: 0.875rem;
    height: 0.875rem;
    fill: currentColor;
}

/* Toggle Switch */
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
    background: var(--border-color, oklch(0.9 0 0));
    border-radius: 0.75rem;
    transition: all var(--transition-normal, 250ms) ease;
}

.toggle-thumb {
    position: absolute;
    top: 0.125rem;
    left: 0.125rem;
    width: 1.25rem;
    height: 1.25rem;
    background: var(--sidebar-primary-foreground, #fff);
    border-radius: 50%;
    transition: transform var(--transition-normal, 250ms) ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.toggle-input:checked + .toggle-label .toggle-track {
    background: var(--success, oklch(0.696 0.17 162.48));
}

.toggle-input:checked + .toggle-label .toggle-thumb {
    transform: translateX(1.5rem);
}

.toggle-text {
    font-weight: 500;
    color: var(--text-primary, oklch(0.985 0 0));
}

.toggle-help {
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: var(--text-muted, oklch(0.708 0 0 / 0.7));
    font-weight: 500;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color, oklch(0.9 0 0));
}

@media (max-width: 576px) {
    .form-actions {
        flex-direction: column;
    }

    .form-actions a,
    .form-actions button {
        width: 100%;
        justify-content: center;
    }
}

.btn-primary,
.btn-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 0.875rem 1.75rem;
    border: none;
    border-radius: var(--radius, 0.625rem);
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all var(--transition-normal, 250ms) ease;
    text-decoration: none;
}

.btn-primary {
    background: linear-gradient(135deg, 
        var(--accent-color, oklch(0.488 0.243 264.376)), 
        var(--info, oklch(0.488 0.243 264.376)));
    color: var(--sidebar-primary-foreground, #fff);
    box-shadow: 0 4px 12px var(--accent-glow, oklch(0.488 0.243 264.376 / 0.2));
}

.btn-primary:hover {
    transform: translateY(-2px);
    background: var(--accent-hover, oklch(0.488 0.243 264.376 / 0.8));
    box-shadow: 0 6px 20px var(--accent-glow, oklch(0.488 0.243 264.376 / 0.3));
}

.btn-secondary {
    background: var(--secondary, oklch(0.269 0 0));
    color: var(--secondary-foreground, oklch(0.985 0 0));
    border: 1px solid var(--border-color, oklch(0.9 0 0));
}

.btn-secondary:hover {
    background: var(--accent, oklch(0.269 0 0));
    transform: translateY(-2px);
    border-color: var(--accent-color, oklch(0.488 0.243 264.376));
}

/* Error Messages */
.error-message {
    display: block;
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: var(--danger, oklch(0.704 0.191 22.216));
    min-height: 1.25rem;
    opacity: 0;
    transform: translateY(-5px);
    transition: all var(--transition-fast, 150ms) ease;
    font-weight: 500;
}

.error-message.show {
    opacity: 1;
    transform: translateY(0);
}

/* Success Messages */
.success-message {
    display: block;
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: var(--success, oklch(0.696 0.17 162.48));
    min-height: 1.25rem;
    opacity: 0;
    transform: translateY(-5px);
    transition: all var(--transition-fast, 150ms) ease;
    font-weight: 500;
}

.success-message.show {
    opacity: 1;
    transform: translateY(0);
}

/* Input Help Text */
.input-help {
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: var(--text-muted, oklch(0.708 0 0 / 0.7));
    font-style: italic;
    font-weight: 500;
}

/* Responsive Design */
@media (max-width: 768px) {
    .form-card {
        padding: 1.5rem;
    }

    .page-title {
        font-size: 1.75rem;
    }

    .upload-area {
        padding: 1.5rem;
    }

    .upload-icon svg {
        width: 2.5rem;
        height: 2.5rem;
    }
}

@media (max-width: 480px) {
    .container {
        padding: 0.75rem;
    }

    .form-card {
        padding: 1rem;
    }

    .form-input {
        padding: 0.75rem 1rem 0.75rem 3rem !important;
    }

    .form-textarea {
        padding: 0.75rem 1rem 0.75rem 3rem !important;
    }

    .image-preview,
    .preview-image {
        width: 100px;
        height: 100px;
    }
}

/* Focus styles for accessibility */
.form-input:focus,
.form-textarea:focus,
.btn-primary:focus,
.btn-secondary:focus,
.toggle-label:focus,
.upload-area:focus-within,
.image-preview:focus,
.preview-image:focus {
    outline: 2px solid var(--ring, oklch(0.556 0 0));
    outline-offset: 2px;
}

/* Form layout improvements */
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.form-full-width {
    grid-column: 1 / -1;
}

/* Loading states */
.loading {
    opacity: 0.7;
    pointer-events: none;
    position: relative;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 1.5rem;
    height: 1.5rem;
    border: 2px solid var(--border-color, oklch(0.9 0 0));
    border-top-color: var(--accent-color, oklch(0.488 0.243 264.376));
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    transform: translate(-50%, -50%);
}

@keyframes spin {
    to {
        transform: translate(-50%, -50%) rotate(360deg);
    }
}

/* Dark/Light Mode Specific */
html[data-theme='dark'] .form-card {
    background: var(--glass-base, oklch(0.205 0 0 / 0.7));
}

html[data-theme='light'] .form-card {
    background: var(--card, oklch(1 0 0));
}

html[data-theme='dark'] .brand-image,
html[data-theme='dark'] .preview-image img {
    background: var(--glass-base, rgba(255, 255, 255, 0.1));
}

html[data-theme='light'] .brand-image,
html[data-theme='light'] .preview-image img {
    background: var(--glass-base, rgba(255, 255, 255, 0.85));
}
</style>

<script>
// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    const logoUpload = document.getElementById('logoUpload');
    const uploadArea = document.getElementById('uploadArea');
    const logoPreview = document.getElementById('logoPreview');
    const removeLogoInput = document.getElementById('removeLogo');

    // Remove current logo function
    window.removeLogo = function() {
        const currentImage = document.querySelector('.current-image');
        if (currentImage) {
            currentImage.classList.add('animate-shake');
            setTimeout(() => {
                currentImage.remove();
                if (removeLogoInput) {
                    removeLogoInput.value = '1';
                }
            }, 500);
        }
    };

    // Logo upload functionality
    if (logoUpload && uploadArea && logoPreview) {
        // Handle file selection
        logoUpload.addEventListener('change', function(e) {
            handleFiles(this.files);
        });

        // Click upload area to trigger file input
        uploadArea.addEventListener('click', function(e) {
            if (e.target !== logoUpload) {
                logoUpload.click();
            }
        });

        // Handle file validation and preview
        function handleFiles(files) {
            const file = files[0];
            if (!file) return;

            // Validate file type
            if (!file.type.match('image.*')) {
                showError('Please select an image file (PNG, JPG, SVG)');
                return;
            }

            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                showError('File size should be less than 5MB');
                return;
            }

            // Create preview
            const reader = new FileReader();
            reader.onload = function(e) {
                showPreview(e.target.result, file.name);
            };
            reader.readAsDataURL(file);
        }

        // Show image preview
        function showPreview(src, filename) {
            logoPreview.innerHTML = '';

            const preview = document.createElement('div');
            preview.className = 'preview-image';
            preview.innerHTML = `
                <img src="${src}" alt="${filename}">
                <button type="button" class="preview-remove" title="Remove new logo">
                    <svg viewBox="0 0 24 24">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                    </svg>
                </button>
            `;

            preview.querySelector('.preview-remove').addEventListener('click', function() {
                preview.remove();
                logoUpload.value = '';
            });

            logoPreview.appendChild(preview);
        }

        // Show error message
        function showError(message) {
            // You can add error display logic here if needed
            alert(message);
            logoUpload.value = '';
        }
    }
});
</script>
@endsection
