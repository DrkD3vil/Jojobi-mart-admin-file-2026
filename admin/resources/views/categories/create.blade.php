@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="page-header animate-fade-in">
            <h2 class="page-title">Create Category</h2>
            <p class="page-subtitle">Add a new product category to your inventory</p>
        </div>

        <form id="category-form" method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data"
            class="category-form animate-slide-up">
            @csrf

            {{-- Form Card --}}
            <div class="form-card glass-effect animate-pop-in">
                {{-- Barcode Section --}}
                <div class="form-section">
                    <div class="form-label-group">
                        <label class="form-label">Barcode</label>
                        <span class="form-label-hint">Optional</span>
                    </div>
                    <div class="input-wrapper">
                        <div class="input-icon">
                            <svg class="input-svg" viewBox="0 0 24 24">
                                <path d="M2 6h6v4H2V6zm0 6h6v4H2v-4zm8-6h12v4H10V6zm0 6h12v4H10v-4z" />
                            </svg>
                        </div>
                        <input type="text" class="form-input" name="barcode" placeholder="Scan or enter barcode">
                        <div class="input-trailing">
                            <button type="button" class="btn-icon scan-btn" title="Scan barcode">
                                <svg viewBox="0 0 24 24">
                                    <path
                                        d="M4 6h2v12H4zm4 0h1v12H8zm2 0h3v12h-3zm4 0h2v12h-2zm3 0h1v12h-1zm2 0h1v12h-1z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <p class="input-help">Leave empty to auto-generate unique barcode</p>
                    <span class="error-message" id="error-barcode"></span>
                </div>

                {{-- Name Section --}}
                <div class="form-section">
                    <div class="form-label-group">
                        <label class="form-label required">Category Name</label>
                        <span class="form-label-hint">Required</span>
                    </div>
                    <div class="input-wrapper">
                        <div class="input-icon">
                            <svg class="input-svg" viewBox="0 0 24 24">
                                <path d="M3 3h18v4H3V3zm0 6h18v4H3V9zm0 6h18v4H3v-4z" />
                            </svg>
                        </div>
                        <input type="text" class="form-input" name="name" required placeholder="Enter category name">
                    </div>
                    <span class="error-message" id="error-name"></span>
                </div>

                {{-- Parent Category Search --}}
                <div class="form-section">
                    <div class="form-label-group">
                        <label class="form-label">Parent Category</label>
                        <span class="form-label-hint">Optional</span>
                    </div>
                    <div class="search-container">
                        <div class="input-wrapper">
                            <div class="input-icon">
                                <svg class="input-svg" viewBox="0 0 24 24">
                                    <path
                                        d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
                                </svg>
                            </div>
                            <input type="text" id="categorySearch" class="form-input"
                                placeholder="Search parent / child / sub-category...">
                            <div class="search-clear" id="searchClear">
                                <svg viewBox="0 0 24 24">
                                    <path
                                        d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                                </svg>
                            </div>
                        </div>

                        <div id="categoryDropdown" class="dropdowns-card">
                            <div class="dropdowns-header">
                                <span class="dropdowns-title">Select Category</span>
                                <span class="dropdowns-count" id="resultCount">0 results</span>
                            </div>
                            <div class="dropdowns-content" id="dropdownContent">
                                {{-- JS will populate here --}}
                            </div>
                            <div class="dropdowns-footer">
                                <span class="dropdowns-hint">Click to select</span>
                            </div>
                        </div>

                        <input type="hidden" name="parent_id" id="parent_id">
                        <div class="selected-preview" id="selectedPreview"></div>
                    </div>
                    <span class="error-message" id="error-parent_id"></span>
                </div>

                {{-- Image Upload --}}
                <div class="form-section">
                    <div class="form-label-group">
                        <label class="form-label">Category Image</label>
                        <span class="form-label-hint">Optional</span>
                    </div>
                    <div class="upload-container">
                        <input type="file" class="upload-input" name="image" id="imageUpload" accept="image/*">
                        <label for="imageUpload" class="upload-area">
                            <div class="upload-icon">
                                <svg viewBox="0 0 24 24">
                                    <path
                                        d="M19 12v7H5v-7H3v7a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7h-2zm-6 .67l2.59-2.58L17 11.5l-5 5-5-5 1.41-1.41L11 12.67V3h2v9.67z" />
                                </svg>
                            </div>
                            <div class="upload-text">
                                <span class="upload-title">Drop image or click to browse</span>
                                <span class="upload-subtitle">PNG, JPG up to 5MB</span>
                            </div>
                        </label>
                        <div class="preview-container" id="imagePreview"></div>
                    </div>
                    <span class="error-message" id="error-image"></span>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="form-actions animate-fade-in-delay">
                <button type="submit" class="btn-primary submit-btn" id="submitBtn">
                    <span class="btn-spinner"></span>
                    <span class="btn-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                        </svg>
                    </span>
                    Create Category
                </button>
            </div>

            {{-- Success Message --}}
            <div id="success-message" class="success-card">
                <div class="success-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                    </svg>
                </div>
                <div class="success-content">
                    <h4 class="success-title">Success!</h4>
                    <p class="success-message"></p>
                </div>
            </div>
        </form>
    </div>

    <style>
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

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 var(--accent-glow);
            }

            50% {
                box-shadow: 0 0 0 4px var(--accent-glow);
            }
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        /* Animation Classes */
        .animate-fade-in {
            animation: fadeIn var(--transition-normal) ease-out;
        }

        .animate-slide-up {
            animation: slideUp var(--transition-normal) ease-out;
        }

        .animate-pop-in {
            animation: popIn var(--transition-normal) cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .animate-fade-in-delay {
            animation: fadeIn var(--transition-normal) ease-out 0.3s both;
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        .animate-pulse {
            animation: pulse 2s infinite;
        }

        .animate-shake {
            animation: shake 0.5s ease-in-out;
        }

        /* Layout */
        

        /* Page Header */
        .page-header {
            margin-bottom: 2.5rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--text-primary), var(--text-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-subtitle {
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        /* Form Card */
        .form-card {
            background: var(--card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--card-shadow);
            transition: box-shadow var(--transition-normal),
                transform var(--transition-normal);
        }

        .form-card:hover {
            box-shadow: var(--card-shadow-hover);
            transform: translateY(-2px);
        }

        .glass-effect {
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        /* Form Sections */
        .form-section {
            margin-bottom: 2rem;
            position: relative;
        }

        .form-section:last-child {
            margin-bottom: 0;
        }

        .form-label-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 0.95rem;
        }

        .form-label.required::after {
            content: '*';
            color: var(--danger);
            margin-left: 4px;
        }

        .form-label-hint {
            font-size: 0.85rem;
            color: var(--text-muted);
            background: var(--bg-tertiary);
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
        }

        /* Input Styles */
        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            background: var(--input);
            border: 2px solid var(--border-color);
            border-radius: var(--radius);
            transition: all var(--transition-normal);
        }

        .input-wrapper:focus-within {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px var(--accent-glow);
            transform: translateY(-1px);
        }

        .form-input {
            flex: 1;
            padding: 0.875rem 1rem 0.875rem 3rem;
            background: transparent;
            border: none;
            color: var(--text-primary);
            font-size: 1rem;
            outline: none;
            width: 100%;
        }

        .form-input::placeholder {
            color: var(--text-muted);
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            display: flex;
            align-items: center;
            color: var(--text-secondary);
        }

        .input-svg {
            width: 1.25rem;
            height: 1.25rem;
            fill: currentColor;
        }

        .input-trailing {
            position: absolute;
            right: 1rem;
            display: flex;
            gap: 0.5rem;
        }

        .btn-icon {
            background: none;
            border: none;
            padding: 0.5rem;
            border-radius: 6px;
            cursor: pointer;
            color: var(--text-secondary);
            transition: all var(--transition-fast);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-icon:hover {
            background: var(--accent);
            color: var(--accent-foreground);
            transform: scale(1.1);
        }

        .btn-icon svg {
            width: 1.25rem;
            height: 1.25rem;
            fill: currentColor;
        }

        .input-help {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        /* Search Container */
        .search-container {
            position: relative;
            max-width: 400px;
        }

        .search-clear {
            position: absolute;
            right: 3.5rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            padding: 0.5rem;
            opacity: 0;
            transition: opacity var(--transition-fast);
            z-index: 2;
            background: transparent;
            border: none;
            color: var(--text-secondary);
        }

        .search-clear.visible {
            opacity: 0.5;
        }

        .search-clear:hover {
            opacity: 1 !important;
        }

        .search-clear svg {
            width: 1.25rem;
            height: 1.25rem;
            fill: currentColor;
        }

        /* Dropdowns Card */
        .dropdowns-card {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            margin-top: 0.5rem;
            background: var(--popover);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            box-shadow: var(--dropdown-shadow);
            z-index: 1000;
            opacity: 0;
            transform: translateY(-10px);
            visibility: hidden;
            transition: all var(--transition-normal);
        }

        .dropdowns-card.show {
            opacity: 1;
            transform: translateY(0);
            visibility: visible;
        }

        .dropdowns-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dropdowns-title {
            font-weight: 600;
            color: var(--text-primary);
        }

        .dropdowns-count {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .dropdowns-content {
            max-height: 300px;
            overflow-y: auto;
        }

        .dropdowns-item {
            padding: 0.75rem 1.25rem;
            cursor: pointer;
            transition: all var(--transition-fast);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-primary);
            border-bottom: 1px solid var(--border-color);
        }

        .dropdowns-item:last-child {
            border-bottom: none;
        }

        .dropdowns-item:hover {
            background: var(--accent);
            color: var(--accent-foreground);
        }

        .dropdowns-item.selected {
            background: var(--sidebar-primary);
            color: var(--sidebar-primary-foreground);
        }

        .dropdowns-item-icon {
            width: 1.25rem;
            height: 1.25rem;
            fill: currentColor;
            opacity: 0.6;
        }

        .dropdowns-item-level-0 {
            margin-left: 0;
        }

        .dropdowns-item-level-1 {
            margin-left: 1.5rem;
        }

        .dropdowns-item-level-2 {
            margin-left: 3rem;
        }

        .dropdowns-item-level-3 {
            margin-left: 4.5rem;
        }

        .dropdowns-footer {
            padding: 0.75rem 1.25rem;
            border-top: 1px solid var(--border-color);
            font-size: 0.875rem;
            color: var(--text-muted);
            text-align: center;
        }

        /* Selected Preview */
        .selected-preview {
            margin-top: 1rem;
            padding: 1rem;
            background: var(--accent);
            border-radius: var(--radius);
            border-left: 4px solid var(--accent-color);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            opacity: 0;
            transform: translateY(-10px);
            transition: all var(--transition-normal);
            color: var(--accent-foreground);
        }

        .selected-preview.show {
            opacity: 1;
            transform: translateY(0);
        }

        .selected-preview svg {
            width: 1.25rem;
            height: 1.25rem;
            fill: var(--accent-color);
        }

        /* Upload Container */
        .upload-container {
            position: relative;
        }

        .upload-input {
            display: none;
        }

        .upload-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
            border: 2px dashed var(--border-color);
            border-radius: var(--radius);
            background: var(--input);
            cursor: pointer;
            transition: all var(--transition-normal);
        }

        .upload-area:hover,
        .upload-area.dragover {
            border-color: var(--accent-color);
            background: var(--accent);
            transform: translateY(-2px);
        }

        .upload-icon {
            margin-bottom: 1rem;
            color: var(--text-secondary);
            transition: all var(--transition-normal);
        }

        .upload-area:hover .upload-icon {
            color: var(--accent-color);
            transform: scale(1.1);
        }

        .upload-icon svg {
            width: 3rem;
            height: 3rem;
            fill: currentColor;
        }

        .upload-text {
            text-align: center;
        }

        .upload-title {
            display: block;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .upload-subtitle {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .preview-container {
            margin-top: 1rem;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 1rem;
        }

        .preview-image {
            position: relative;
            aspect-ratio: 1;
            border-radius: var(--radius);
            overflow: hidden;
            border: 2px solid var(--border-color);
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
            background: var(--danger);
            color: white;
            border: none;
            border-radius: 50%;
            width: 1.5rem;
            height: 1.5rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity var(--transition-fast);
            z-index: 1;
        }

        .preview-image:hover .preview-remove {
            opacity: 1;
        }

        /* Form Actions */
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }

        @media (max-width: 576px) {
            .form-actions {
                flex-direction: column;
            }

            .form-actions button {
                width: 100%;
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
            border-radius: var(--radius);
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all var(--transition-normal);
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-color), var(--info));
            color: var(--primary-foreground);
            box-shadow: 0 4px 12px var(--accent-glow);
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px var(--accent-glow);
        }

        .btn-primary:active:not(:disabled) {
            transform: translateY(0);
        }

        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: var(--secondary);
            color: var(--secondary-foreground);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: var(--accent);
            transform: translateY(-2px);
        }

        .btn-spinner {
            position: absolute;
            width: 1.5rem;
            height: 1.5rem;
            border: 2px solid transparent;
            border-top-color: currentColor;
            border-radius: 50%;
            animation: spin 0.75s linear infinite;
            opacity: 0;
        }

        .btn-primary.loading .btn-spinner {
            opacity: 1;
        }

        .btn-primary.loading .btn-icon {
            opacity: 0;
        }

        /* Success Card */
        .success-card {
            display: none;
            margin-top: 2rem;
            padding: 1.5rem;
            background: var(--success);
            color: white;
            border-radius: var(--radius);
            transform-origin: top;
        }

        .success-card.show {
            display: flex;
            align-items: center;
            gap: 1rem;
            animation: slideUp var(--transition-normal);
        }

        .success-icon {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            padding: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-icon svg {
            width: 1.5rem;
            height: 1.5rem;
            fill: currentColor;
        }

        .success-content {
            flex: 1;
        }

        .success-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .success-message {
            opacity: 0.9;
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
            transition: all var(--transition-fast);
        }

        .error-message.show {
            opacity: 1;
            transform: translateY(0);
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
                padding: 2rem 1rem;
            }

            .dropdowns-item-level-3 {
                margin-left: 3rem;
            }

            .dropdowns-item-level-2 {
                margin-left: 2rem;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 0.75rem;
            }

            .form-card {
                padding: 1rem;
            }

            .input-wrapper {
                flex-direction: row !important;
                align-items: center !important;
            }

            .form-input {
                padding: 0.875rem 1rem 0.875rem 3rem !important;
            }

            .input-icon {
                position: absolute !important;
                margin-bottom: 0 !important;
            }

            .input-trailing {
                position: absolute !important;
                margin-top: 0 !important;
            }

            .search-container {
                max-width: 100%;
            }
        }

        /* Dark/Light mode specific adjustments */
        html[data-theme='dark'] .form-card {
            background: var(--glass-base);
        }

        html[data-theme='light'] .form-card {
            background: var(--card);
        }

        /* Fix for search clear button position */
        .input-wrapper {
            position: relative;
        }

        .search-clear {
            right: 1rem !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
        }

        .form-input {
            padding-right: 3.5rem !important;
        }
    </style>

    <script>
        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            const data = @json($flat) || [];
            const searchInput = document.getElementById('categorySearch');
            const searchClear = document.getElementById('searchClear');
            const dropdowns = document.getElementById('categoryDropdown');
            const dropdownContent = document.getElementById('dropdownContent');
            const resultCount = document.getElementById('resultCount');
            const hiddenInput = document.getElementById('parent_id');
            const selectedPreview = document.getElementById('selectedPreview');
            const submitBtn = document.getElementById('submitBtn');
            const successCard = document.getElementById('success-message');
            const successMessageElement = successCard.querySelector('.success-message');
            const imageUpload = document.getElementById('imageUpload');
            const imagePreview = document.getElementById('imagePreview');
            const form = document.getElementById('category-form');

            let debounceTimer;
            let selectedCategory = null;

            // Format category path with icons based on depth
            function getCategoryIcon(level) {
                const icons = ['📁', '📂', '📄', '🗂️'];
                return icons[Math.min(level || 0, icons.length - 1)];
            }

            // Populate dropdown with filtered results
            function populateDropdown(filter = '') {
                dropdownContent.innerHTML = '';

                if (!Array.isArray(data)) {
                    console.error('Data is not an array:', data);
                    return;
                }

                const filtered = data
                    .filter(item => item && item.search && item.search.toLowerCase().includes(filter.toLowerCase()))
                    .slice(0, 20); // Limit results for performance

                resultCount.textContent = `${filtered.length} result${filtered.length !== 1 ? 's' : ''}`;

                if (filtered.length === 0) {
                    const empty = document.createElement('div');
                    empty.className = 'dropdowns-item';
                    empty.innerHTML = `
                        <svg class="dropdowns-item-icon" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                        </svg>
                        <span>No categories found</span>
                    `;
                    dropdownContent.appendChild(empty);
                } else {
                    filtered.forEach(item => {
                        const div = document.createElement('div');
                        div.className = `dropdowns-item dropdowns-item-level-${item.level || 0}`;
                        div.innerHTML = `
                            <span>${getCategoryIcon(item.level || 0)}</span>
                            <span>${item.path || ''}</span>
                        `;
                        div.dataset.id = item.id;
                        div.dataset.name = item.name;
                        div.dataset.path = item.path;

                        div.addEventListener('click', () => selectCategory(item.id, item.name, item.path));
                        dropdownContent.appendChild(div);
                    });
                }

                dropdowns.classList.add('show');
            }

            // Select a category
            function selectCategory(id, name, path) {
                selectedCategory = { id, name, path };
                searchInput.value = path;
                hiddenInput.value = id;
                dropdowns.classList.remove('show');

                // Update search clear visibility
                updateSearchClearVisibility();

                // Update preview
                selectedPreview.innerHTML = `
                    <svg viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                    </svg>
                    <div>
                        <strong>Selected:</strong> ${path}
                    </div>
                `;
                selectedPreview.classList.add('show');
            }

            // Clear selection
            function clearSelection() {
                selectedCategory = null;
                searchInput.value = '';
                hiddenInput.value = '';
                selectedPreview.classList.remove('show');
                selectedPreview.innerHTML = '';
                dropdowns.classList.remove('show');
                updateSearchClearVisibility();
            }

            // Update search clear button visibility
            function updateSearchClearVisibility() {
                if (searchInput.value.trim()) {
                    searchClear.classList.add('visible');
                } else {
                    searchClear.classList.remove('visible');
                }
            }

            // Debounced search with animation
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                updateSearchClearVisibility();

                debounceTimer = setTimeout(() => {
                    const value = this.value.trim();
                    if (value) {
                        // Add search animation
                        this.parentElement.classList.add('animate-pulse');
                        setTimeout(() => {
                            this.parentElement.classList.remove('animate-pulse');
                        }, 500);

                        populateDropdown(value);
                    } else {
                        dropdowns.classList.remove('show');
                    }
                }, 150);
            });

            // Clear search
            searchClear.addEventListener('click', function(e) {
                e.stopPropagation();
                clearSelection();
                searchInput.focus();
            });

            // Show dropdown on focus
            searchInput.addEventListener('focus', function() {
                const value = this.value.trim();
                if (value) {
                    populateDropdown(value);
                }
            });

            // Handle keyboard navigation
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    dropdowns.classList.remove('show');
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (dropdowns.classList.contains('show')) {
                        // Select first item if dropdown is open
                        const firstItem = dropdownContent.querySelector('.dropdowns-item');
                        if (firstItem && firstItem.dataset.id) {
                            selectCategory(firstItem.dataset.id, firstItem.dataset.name, firstItem.dataset.path);
                        }
                    }
                }
            });

            // Hide dropdown on click outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !dropdowns.contains(e.target)) {
                    dropdowns.classList.remove('show');
                }
            });

            // Image upload preview
            imageUpload.addEventListener('change', function(e) {
                imagePreview.innerHTML = '';

                Array.from(this.files).forEach(file => {
                    if (!file.type.startsWith('image/')) {
                        alert('Please select an image file');
                        return;
                    }

                    // Check file size (5MB limit)
                    if (file.size > 5 * 1024 * 1024) {
                        alert('File size should be less than 5MB');
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.createElement('div');
                        preview.className = 'preview-image';
                        preview.innerHTML = `
                            <img src="${e.target.result}" alt="${file.name}">
                            <button type="button" class="preview-remove" title="Remove image">
                                <svg viewBox="0 0 24 24" width="16" height="16">
                                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                </svg>
                            </button>
                        `;

                        preview.querySelector('.preview-remove').addEventListener('click', function() {
                            preview.classList.add('animate-shake');
                            setTimeout(() => {
                                preview.remove();
                                imageUpload.value = '';
                            }, 500);
                        });

                        imagePreview.appendChild(preview);
                    };
                    reader.readAsDataURL(file);
                });
            });

            // Drag and drop for image upload
            const uploadArea = document.querySelector('.upload-area');

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadArea.addEventListener(eventName, () => {
                    uploadArea.classList.add('dragover');
                    uploadArea.classList.add('animate-pulse');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, () => {
                    uploadArea.classList.remove('dragover');
                    uploadArea.classList.remove('animate-pulse');
                }, false);
            });

            uploadArea.addEventListener('drop', function(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                imageUpload.files = files;
                const changeEvent = new Event('change');
                imageUpload.dispatchEvent(changeEvent);
            });

            // Form submission with animation


            // Initialize search clear button
            updateSearchClearVisibility();
        });
    </script>
@endsection
