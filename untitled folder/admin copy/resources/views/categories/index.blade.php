@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="page-header animate-fade-in">
            <h2 class="page-title">Categories</h2>
            <p class="page-subtitle">Manage your product categories hierarchy</p>
        </div>

        <div class="header-actions animate-fade-in-delay">
            <a href="{{ route('categories.create') }}" class="btn-primary btn-icon">
                <svg viewBox="0 0 24 24">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
                Add Category
            </a>

            <div class="search-container">
                <div class="input-wrapper">
                    <div class="input-icon">
                        <svg viewBox="0 0 24 24">
                            <path
                                d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
                        </svg>
                    </div>
                    <input type="text" id="searchInput" class="form-input" placeholder="Search categories...">
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="success-message animate-slide-up" id="successAlert">
                <div class="success-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                    </svg>
                </div>
                <p>{{ session('success') }}</p>
                <button class="close-alert" onclick="closeAlert()">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                    </svg>
                </button>
            </div>
        @endif

        <div class="categories-table-container animate-slide-up">
            <div class="table-responsive">
                <table class="categories-table">
                    <thead>
                        <tr>
                            <th class="table-header">
                                <span>Name</span>
                                <button class="sort-btn" data-sort="name">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M3 18h6v-2H3v2zM3 6v2h18V6H3zm0 7h12v-2H3v2z" />
                                    </svg>
                                </button>
                            </th>
                            <th class="table-header">
                                <span>Parent</span>
                                <button class="sort-btn" data-sort="parent">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M3 18h6v-2H3v2zM3 6v2h18V6H3zm0 7h12v-2H3v2z" />
                                    </svg>
                                </button>
                            </th>
                            <th class="table-header">
                                <span>Barcode</span>
                            </th>
                            <th class="table-header">
                                <span>Image</span>
                            </th>
                            <th class="table-header actions-header">
                                <span>Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="categoriesTableBody">
                        @foreach ($categories as $cat)
                            <tr class="category-row animate-fade-in" style="animation-delay: {{ $loop->index * 0.05 }}s">
                                <td class="category-name">
                                    <div class="name-content">
                                        <div class="category-icon">
                                            <svg viewBox="0 0 24 24">
                                                <path
                                                    d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <strong>{{ $cat->name }}</strong>
                                            <small class="category-meta">{{ $cat->created_at->format('M d, Y') }}</small>
                                        </div>
                                    </div>
                                </td>

                                <td class="parent-category">
                                    @if ($cat->parent)
                                        <span class="parent-tag">
                                            <svg viewBox="0 0 24 24">
                                                <path
                                                    d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z" />
                                            </svg>
                                            {{ $cat->parent->name }}
                                        </span>
                                    @else
                                        <span class="root-tag">Root Category</span>
                                    @endif
                                </td>

                                <td class="barcode-cell">
                                    @if ($cat->barcode_svg)
                                        <div class="barcode-preview" onclick="zoomBarcode(this)">
                                            <img src="{{ asset('storage/' . $cat->barcode_svg) }}" alt="Barcode"
                                                loading="lazy" class="barcode-image">
                                            <div class="zoom-hint">
                                                <svg viewBox="0 0 24 24">
                                                    <path
                                                        d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
                                                </svg>
                                            </div>
                                        </div>
                                    @else
                                        <span class="no-barcode">No barcode</span>
                                    @endif
                                </td>

                                <td class="image-cell">
                                    @if ($cat->image)
                                        <div class="image-preview" onclick="zoomImage(this)">
                                            <img src="{{ asset('storage/' . $cat->image) }}" alt="{{ $cat->name }}"
                                                loading="lazy" class="category-image">
                                            <div class="zoom-hint">
                                                <svg viewBox="0 0 24 24">
                                                    <path
                                                        d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
                                                </svg>
                                            </div>
                                        </div>
                                    @else
                                        <div class="no-image">
                                            <svg viewBox="0 0 24 24">
                                                <path
                                                    d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z" />
                                            </svg>
                                        </div>
                                    @endif
                                </td>

                                <td class="actions-cell">
                                    <div class="action-buttons">
                                        <a href="{{ route('categories.edit', $cat) }}" class="btn-action btn-edit"
                                            title="Edit">
                                            <svg viewBox="0 0 24 24">
                                                <path
                                                    d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                                            </svg>
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('categories.destroy', $cat) }}"
                                            class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn-action btn-delete"
                                                onclick="confirmDelete(this)" title="Delete">
                                                <svg viewBox="0 0 24 24">
                                                    <path
                                                        d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($categories->isEmpty())
                <div class="empty-state animate-fade-in">
                    <div class="empty-icon">
                        <svg viewBox="0 0 24 24">
                            <path
                                d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z" />
                        </svg>
                    </div>
                    <h3>No Categories Found</h3>
                    <p>Get started by creating your first category</p>
                    <a href="{{ route('categories.create') }}" class="btn-primary">
                        <svg viewBox="0 0 24 24">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                        </svg>
                        Create Category
                    </a>
                </div>
            @endif
        </div>

        {{-- Custom Pagination --}}
        @if ($categories->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{-- Use custom pagination --}}
                {{ $categories->links('vendor.pagination.custom') }}

                {{-- OR if you want to show simple info --}}
                {{--
                <div class="flex items-center justify-between text-sm text-gray-700">
                    <div>
                        Showing {{ $categories->firstItem() }} to {{ $categories->lastItem() }} of {{ $categories->total() }} results
                    </div>
                    {{ $categories->links() }}
                </div>
                --}}
            </div>
        @endif

        <!-- Modal for Image Zoom -->
        <div class="modal" id="imageModal">
            <div class="modal-content animate-pop-in">
                <button class="modal-close" onclick="closeModal()">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                    </svg>
                </button>
                <img id="modalImage" src="" alt="Zoomed Image">
                <div class="modal-caption" id="modalCaption"></div>
            </div>
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

            @keyframes slideInRight {
                from {
                    opacity: 0;
                    transform: translateX(20px);
                }

                to {
                    opacity: 1;
                    transform: translateX(0);
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

            @keyframes shimmer {
                0% {
                    background-position: -200px 0;
                }

                100% {
                    background-position: calc(200px + 100%) 0;
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
                animation: fadeIn var(--transition-normal) ease-out 0.2s both;
            }

            .animate-slide-in-right {
                animation: slideInRight var(--transition-normal) ease-out;
            }

            .animate-pulse {
                animation: pulse 2s infinite;
            }

            /* Layout */


            /* Page Header */
            .page-header {
                margin-bottom: 2rem;
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

            /* Header Actions */
            .header-actions {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 2rem;
                gap: 1rem;
                flex-wrap: wrap;
            }

            @media (max-width: 768px) {
                .header-actions {
                    flex-direction: column;
                    align-items: stretch;
                }

                .search-container {
                    width: 100%;
                }
            }

            .btn-primary {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.75rem;
                padding: 0.875rem 1.75rem;
                background: linear-gradient(135deg, var(--accent-color), var(--info));
                color: var(--primary-foreground);
                border: none;
                border-radius: var(--radius);
                font-weight: 600;
                font-size: 1rem;
                cursor: pointer;
                box-shadow: 0 4px 12px var(--accent-glow);
                transition: all var(--transition-normal);
                text-decoration: none;
                position: relative;
                overflow: hidden;
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px var(--accent-glow);
            }

            .btn-primary:active {
                transform: translateY(0);
            }

            .btn-primary svg {
                width: 1.25rem;
                height: 1.25rem;
                fill: currentColor;
            }

            .btn-icon {
                padding: 0.875rem 1.5rem;
            }

            /* Search Container */
            .search-container {
                flex: 1;
                max-width: 400px;
            }

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

            .input-icon {
                position: absolute;
                left: 1rem;
                display: flex;
                align-items: center;
                color: var(--text-secondary);
            }

            /* Success Message */
            .success-message {
                display: flex;
                align-items: center;
                gap: 1rem;
                padding: 1rem 1.5rem;
                background: var(--success);
                color: white;
                border-radius: var(--radius);
                margin-bottom: 2rem;
                position: relative;
            }

            .success-icon {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .success-icon svg {
                width: 1.5rem;
                height: 1.5rem;
                fill: currentColor;
            }

            .close-alert {
                background: none;
                border: none;
                color: white;
                cursor: pointer;
                padding: 0.25rem;
                margin-left: auto;
                opacity: 0.7;
                transition: opacity var(--transition-fast);
            }

            .close-alert:hover {
                opacity: 1;
            }

            .close-alert svg {
                width: 1.25rem;
                height: 1.25rem;
                fill: currentColor;
            }

            /* Table Container */
            .categories-table-container {
                background: var(--card);
                border: 1px solid var(--border-color);
                border-radius: var(--radius);
                overflow: hidden;
                box-shadow: var(--card-shadow);
                transition: box-shadow var(--transition-normal);
            }

            .categories-table-container:hover {
                box-shadow: var(--card-shadow-hover);
            }

            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            /* Table Styles */
            .categories-table {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0;
                min-width: 800px;
            }

            .categories-table thead {
                background: var(--sidebar);
            }

            .table-header {
                padding: 1.25rem 1.5rem;
                text-align: left;
                font-weight: 600;
                color: var(--text-primary);
                border-bottom: 2px solid var(--border-color);
                position: sticky;
                top: 0;
                background: var(--sidebar);
                z-index: 10;
            }

            .table-header span {
                margin-right: 0.5rem;
            }

            .sort-btn {
                background: none;
                border: none;
                color: var(--text-secondary);
                cursor: pointer;
                padding: 0.25rem;
                border-radius: 4px;
                transition: all var(--transition-fast);
            }

            .sort-btn:hover {
                color: var(--accent-color);
                background: var(--accent);
            }

            .sort-btn svg {
                width: 1.25rem;
                height: 1.25rem;
                fill: currentColor;
            }

            .actions-header {
                text-align: center;
            }

            /* Table Rows */
            .category-row {
                transition: all var(--transition-fast);
                border-bottom: 1px solid var(--border-color);
            }

            .category-row:hover {
                background: var(--accent);
                transform: translateX(4px);
            }

            .category-row:last-child {
                border-bottom: none;
            }

            /* Table Cells */
            .category-name,
            .parent-category,
            .barcode-cell,
            .image-cell,
            .actions-cell {
                padding: 1.25rem 1.5rem;
                vertical-align: middle;
            }

            /* Category Name */
            .name-content {
                display: flex;
                align-items: center;
                gap: 1rem;
            }

            .category-icon {
                color: var(--accent-color);
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .category-icon svg {
                width: 2rem;
                height: 2rem;
                fill: currentColor;
            }

            .category-meta {
                display: block;
                font-size: 0.875rem;
                color: var(--text-muted);
                margin-top: 0.25rem;
            }

            /* Parent Category */
            .parent-tag {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.5rem 1rem;
                background: var(--bg-tertiary);
                border-radius: var(--radius);
                color: var(--text-secondary);
                font-size: 0.875rem;
            }

            .parent-tag svg {
                width: 1rem;
                height: 1rem;
                fill: currentColor;
                opacity: 0.7;
            }

            .root-tag {
                padding: 0.5rem 1rem;
                background: var(--info);
                color: white;
                border-radius: var(--radius);
                font-size: 0.875rem;
                display: inline-block;
            }

            /* Barcode Cell */
            .barcode-preview {
                position: relative;
                cursor: pointer;
                border-radius: var(--radius);
                overflow: hidden;
                border: 2px solid transparent;
                transition: all var(--transition-normal);
            }

            .barcode-preview:hover {
                border-color: var(--accent-color);
                transform: translateY(-2px);
            }

            .barcode-image {
                width: 120px;
                height: 60px;
                object-fit: contain;
                background: white;
                padding: 0.5rem;
                border-radius: 4px;
            }

            .zoom-hint {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                transition: opacity var(--transition-fast);
            }

            .barcode-preview:hover .zoom-hint {
                opacity: 1;
            }

            .zoom-hint svg {
                width: 2rem;
                height: 2rem;
                fill: white;
            }

            .no-barcode {
                color: var(--text-muted);
                font-style: italic;
                font-size: 0.875rem;
            }

            /* Image Cell */
            .image-preview {
                position: relative;
                width: 60px;
                height: 60px;
                cursor: pointer;
                border-radius: var(--radius);
                overflow: hidden;
                border: 2px solid transparent;
                transition: all var(--transition-normal);
            }

            .image-preview:hover {
                border-color: var(--accent-color);
                transform: scale(1.05);
            }

            .category-image {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .image-preview .zoom-hint {
                background: rgba(0, 0, 0, 0.7);
            }

            .no-image {
                width: 60px;
                height: 60px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: var(--bg-tertiary);
                border-radius: var(--radius);
                color: var(--text-muted);
            }

            .no-image svg {
                width: 2rem;
                height: 2rem;
                fill: currentColor;
                opacity: 0.5;
            }

            /* Actions Cell */
            .action-buttons {
                display: flex;
                gap: 0.5rem;
                justify-content: center;
            }

            .btn-action {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                padding: 0.5rem 1rem;
                border: none;
                border-radius: var(--radius);
                font-size: 0.875rem;
                cursor: pointer;
                transition: all var(--transition-fast);
                text-decoration: none;
            }

            .btn-action svg {
                width: 1rem;
                height: 1rem;
                fill: currentColor;
            }

            .btn-edit {
                background: var(--info);
                color: white;
            }

            .btn-edit:hover {
                background: var(--info);
                opacity: 0.9;
                transform: translateY(-1px);
            }

            .btn-delete {
                background: var(--danger);
                color: white;
            }

            .btn-delete:hover {
                background: var(--danger);
                opacity: 0.9;
                transform: translateY(-1px);
            }

            .delete-form {
                margin: 0;
            }

            /* Empty State */
            .empty-state {
                padding: 4rem 2rem;
                text-align: center;
                color: var(--text-muted);
            }

            .empty-icon {
                width: 4rem;
                height: 4rem;
                margin: 0 auto 1.5rem;
                color: var(--text-muted);
                opacity: 0.5;
            }

            .empty-icon svg {
                width: 100%;
                height: 100%;
                fill: currentColor;
            }

            .empty-state h3 {
                font-size: 1.5rem;
                margin-bottom: 0.5rem;
                color: var(--text-primary);
            }

            .empty-state p {
                margin-bottom: 1.5rem;
                font-size: 1rem;
            }

            /* Pagination */
            .pagination-container {
                margin-top: 2rem;
                padding-top: 1rem;
                border-top: 1px solid var(--border-color);
            }

            .pagination {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 1rem;
                flex-wrap: wrap;
            }

            .pagination-btn {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.75rem 1.25rem;
                background: var(--secondary);
                color: var(--secondary-foreground);
                border: 1px solid var(--border-color);
                border-radius: var(--radius);
                text-decoration: none;
                transition: all var(--transition-normal);
            }

            .pagination-btn:hover:not(.disabled) {
                background: var(--accent);
                transform: translateY(-1px);
            }

            .pagination-btn.disabled {
                opacity: 0.5;
                cursor: not-allowed;
            }

            .pagination-btn svg {
                width: 1.25rem;
                height: 1.25rem;
                fill: currentColor;
            }

            .page-numbers {
                display: flex;
                gap: 0.5rem;
            }

            .page-number {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 2.5rem;
                height: 2.5rem;
                border-radius: var(--radius);
                background: var(--secondary);
                color: var(--secondary-foreground);
                text-decoration: none;
                transition: all var(--transition-normal);
            }

            .page-number:hover:not(.active) {
                background: var(--accent);
            }

            .page-number.active {
                background: var(--accent-color);
                color: var(--primary-foreground);
                font-weight: 600;
            }

            /* Modal */
            .modal {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.8);
                z-index: 1000;
                align-items: center;
                justify-content: center;
                animation: fadeIn var(--transition-normal);
            }

            .modal.show {
                display: flex;
            }

            .modal-content {
                background: var(--card);
                border-radius: var(--radius);
                max-width: 90%;
                max-height: 90%;
                position: relative;
                box-shadow: var(--dropdown-shadow);
            }

            .modal-close {
                position: absolute;
                top: 1rem;
                right: 1rem;
                background: var(--danger);
                color: white;
                border: none;
                width: 2.5rem;
                height: 2.5rem;
                border-radius: 50%;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 1;
                transition: transform var(--transition-fast);
            }

            .modal-close:hover {
                transform: rotate(90deg);
            }

            .modal-close svg {
                width: 1.5rem;
                height: 1.5rem;
                fill: currentColor;
            }

            #modalImage {
                max-width: 100%;
                max-height: 80vh;
                object-fit: contain;
                display: block;
            }

            .modal-caption {
                padding: 1rem;
                text-align: center;
                color: var(--text-primary);
                background: var(--card);
                border-top: 1px solid var(--border-color);
            }

            /* Responsive Design */
            @media (max-width: 768px) {
                .categories-table {
                    min-width: 600px;
                }

                .category-name,
                .parent-category,
                .barcode-cell,
                .image-cell,
                .actions-cell {
                    padding: 1rem;
                }

                .action-buttons {
                    flex-direction: column;
                    gap: 0.5rem;
                }

                .btn-action {
                    width: 100%;
                    justify-content: center;
                }

                .pagination {
                    flex-direction: column;
                }

                .page-numbers {
                    flex-wrap: wrap;
                    justify-content: center;
                }
            }

            @media (max-width: 480px) {
                .page-title {
                    font-size: 1.75rem;
                }

                .header-actions {
                    gap: 1rem;
                }

                .btn-primary {
                    width: 100%;
                    justify-content: center;
                }

                .search-container {
                    max-width: 100%;
                }

                .name-content {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 0.5rem;
                }

                .category-icon {
                    align-self: flex-start;
                }
            }

            /* Dark/Light Mode Specific */
            html[data-theme='dark'] .categories-table-container {
                background: var(--glass-base);
            }

            html[data-theme='light'] .categories-table-container {
                background: var(--card);
            }

            /* Loading Animation for Images */
            .barcode-image,
            .category-image {
                background: linear-gradient(90deg,
                        var(--bg-tertiary) 0%,
                        var(--accent) 50%,
                        var(--bg-tertiary) 100%);
                background-size: 200% 100%;
                animation: shimmer 1.5s infinite;
            }
        </style>

        <script>
            // Wait for DOM to be fully loaded

            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('searchInput');
                const tableBody = document.getElementById('categoriesTableBody');
                let debounceTimer = null;

                searchInput.addEventListener('input', function() {
                    const query = this.value.trim();

                    clearTimeout(debounceTimer);

                    debounceTimer = setTimeout(() => {
                        if (query.length === 0) {
                            tableBody.innerHTML = '';
                            return;
                        }

                        fetch(`{{ route('categories.ajax.search') }}?q=${encodeURIComponent(query)}`)
                            .then(res => res.json())
                            .then(data => {
                                tableBody.innerHTML = '';

                                if (data.length === 0) {
                                    tableBody.innerHTML = `
                            <tr>
                                <td colspan="3" class="text-center text-muted">
                                    No categories found
                                </td>
                            </tr>`;
                                    return;
                                }

                                data.forEach(cat => {
                                    tableBody.innerHTML += `
                            <tr class="category-row">
                                <td class="category-name">${cat.name}</td>
                                <td class="parent-category">
                                    ${cat.parent ? cat.parent.name : '—'}
                                </td>
                                <td>
                                    <span class="badge bg-success">Active</span>
                                </td>
                            </tr>
                        `;
                                });
                            });
                    }, 300); // ⚡ debounce delay
                });
            });


            // Alert functions
            function closeAlert() {
                const alert = document.getElementById('successAlert');
                if (alert) {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 300);
                }
            }

            // Modal functions
            let currentModalType = '';

            function zoomBarcode(element) {
                const img = element.querySelector('img');
                const modal = document.getElementById('imageModal');
                const modalImage = document.getElementById('modalImage');
                const modalCaption = document.getElementById('modalCaption');

                modalImage.src = img.src;
                modalCaption.textContent = 'Barcode';
                currentModalType = 'barcode';

                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            }

            function zoomImage(element) {
                const img = element.querySelector('img');
                const row = element.closest('.category-row');
                const name = row.querySelector('.category-name strong').textContent;
                const modal = document.getElementById('imageModal');
                const modalImage = document.getElementById('modalImage');
                const modalCaption = document.getElementById('modalCaption');

                modalImage.src = img.src;
                modalCaption.textContent = name;
                currentModalType = 'image';

                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                const modal = document.getElementById('imageModal');
                modal.classList.remove('show');
                document.body.style.overflow = '';

                // Clear image src to prevent ghost image
                const modalImage = document.getElementById('modalImage');
                setTimeout(() => {
                    modalImage.src = '';
                }, 300);
            }

            // Close modal on ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeModal();
                }
            });

            // Close modal on outside click
            document.getElementById('imageModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });

            // Delete confirmation
            function confirmDelete(button) {
                if (confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
                    button.closest('form').submit();
                }
            }

            // Row click animation
            const rows = document.querySelectorAll('.category-row');
            rows.forEach(row => {
                row.addEventListener('click', function(e) {
                    // Don't trigger for action buttons
                    if (!e.target.closest('.action-buttons')) {
                        this.classList.add('animate-pulse');
                        setTimeout(() => {
                            this.classList.remove('animate-pulse');
                        }, 500);
                    }
                });
            });
        </script>
    @endsection
