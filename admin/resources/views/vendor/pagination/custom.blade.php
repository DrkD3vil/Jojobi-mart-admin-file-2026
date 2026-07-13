{{-- resources/views/vendor/pagination/simple.blade.php --}}
@if ($paginator->hasPages())
    <div class="differ">

                <div class="pagination-info">
            <div class="pagination-results">
                <span class="results-text">Showing</span>
                <span class="results-highlight">{{ $paginator->firstItem() ?: 0 }}</span>
                <span class="results-text">to</span>
                <span class="results-highlight">{{ $paginator->lastItem() ?: 0 }}</span>
                <span class="results-text">of</span>
                <span class="results-highlight">{{ $paginator->total() }}</span>
                <span class="results-text">results</span>
            </div>
        </div>



        <div class="custom-pagination">
        {{-- Mobile Navigation --}}
        <div class="pagination-mobile">
            @if (!$paginator->onFirstPage())
                <a href="{{ $paginator->previousPageUrl() }}" class="pagination-mobile-btn" aria-label="Previous page">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </a>
            @endif

            <div class="pagination-mobile-info">
                <span class="pagination-current">{{ $paginator->currentPage() }}</span>
                <span class="pagination-divider">/</span>
                <span class="pagination-total">{{ $paginator->lastPage() }}</span>
            </div>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="pagination-mobile-btn" aria-label="Next page">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </a>
            @endif
        </div>

        {{-- Desktop Navigation --}}
        <div class="pagination-desktop">
            {{-- Previous Button --}}
            @if ($paginator->onFirstPage())
                <button class="pagination-btn pagination-prev disabled" disabled aria-label="Previous page">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    <span>Previous</span>
                </button>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="pagination-btn pagination-prev"
                    aria-label="Previous page">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    <span>Previous</span>
                </a>
            @endif

            {{-- Page Numbers --}}
            <div class="pagination-pages">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="pagination-ellipsis" aria-hidden="true">...</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <button class="pagination-page active" aria-current="page"
                                    aria-label="Page {{ $page }}">
                                    {{ $page }}
                                </button>
                            @else
                                <a href="{{ $url }}" class="pagination-page"
                                    aria-label="Go to page {{ $page }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Next Button --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="pagination-btn pagination-next"
                    aria-label="Next page">
                    <span>Next</span>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </a>
            @else
                <button class="pagination-btn pagination-next disabled" disabled aria-label="Next page">
                    <span>Next</span>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </button>
            @endif
        </div>
    </div>
    {{-- Results Info --}}

    </div>
@endif


<style>
    .differ{
        display: flex;
        justify-content: space-between;
    }
    /* Custom Pagination Styles */
    .custom-pagination {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color);
    }

    /* Mobile Pagination */
    .pagination-mobile {
        display: none;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem;
        background: var(--card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        margin-bottom: 1rem;
    }

    .pagination-mobile-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        background: var(--secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        color: var(--foreground);
        text-decoration: none;
        transition: all var(--transition-fast) ease;
    }

    .pagination-mobile-btn:hover:not(:disabled) {
        background: var(--accent);
        border-color: var(--ring);
        transform: translateY(-1px);
    }

    .pagination-mobile-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
    }

    .pagination-current {
        font-weight: 600;
        color: var(--primary);
        min-width: 1.5rem;
        text-align: center;
    }

    .pagination-divider {
        color: var(--muted-foreground);
    }

    .pagination-total {
        color: var(--muted-foreground);
        min-width: 1.5rem;
        text-align: center;
    }

    /* Desktop Pagination */
    .pagination-desktop {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .pagination-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        background: var(--card);
        color: var(--foreground);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
        transition: all var(--transition-fast) ease;
        min-height: 2.5rem;
    }

    .pagination-btn:hover:not(.disabled) {
        background: var(--secondary);
        border-color: var(--ring);
        transform: translateY(-1px);
        box-shadow: var(--card-shadow);
    }

    .pagination-btn:focus-visible {
        outline: 2px solid var(--ring);
        outline-offset: 2px;
    }

    .pagination-btn.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background: var(--muted);
        color: var(--muted-foreground);
    }

    .pagination-prev span,
    .pagination-next span {
        display: inline-block;
    }

    /* Page Numbers */
    .pagination-pages {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        margin: 0 0.75rem;
    }

    .pagination-page {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 2.5rem;
        height: 2.5rem;
        padding: 0 0.5rem;
        background: var(--card);
        color: var(--foreground);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
        transition: all var(--transition-fast) ease;
    }

    .pagination-page:hover:not(.active) {
        background: var(--secondary);
        border-color: var(--ring);
        transform: translateY(-1px);
    }

    .pagination-page.active {
        background: var(--primary);
        color: var(--primary-foreground);
        border-color: var(--primary);
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .pagination-ellipsis {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        color: var(--muted-foreground);
        font-size: 0.875rem;
    }

    /* Pagination Info */
    .pagination-info {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border-color);
        font-size: 0.875rem;
    }

    .pagination-results {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        flex-wrap: wrap;
    }

    .results-text {
        color: var(--muted-foreground);
    }

    .results-highlight {
        font-weight: 600;
        color: var(--foreground);
        background: var(--secondary);
        padding: 0.125rem 0.5rem;
        border-radius: calc(var(--radius) / 2);
    }

    .pagination-size {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .size-label {
        color: var(--muted-foreground);
    }

    .size-select {
        padding: 0.375rem 0.75rem;
        background: var(--card);
        color: var(--foreground);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        font-size: 0.875rem;
        cursor: pointer;
        transition: all var(--transition-fast) ease;
    }

    .size-select:hover {
        border-color: var(--ring);
    }

    .size-select:focus {
        outline: 2px solid var(--ring);
        outline-offset: 2px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .pagination-mobile {
            display: flex;
        }

        .pagination-desktop {
            display: none;
        }

        .pagination-info {
            flex-direction: column;
            align-items: stretch;
            gap: 0.75rem;
        }

        .pagination-results,
        .pagination-size {
            justify-content: center;
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .custom-pagination {
            padding-top: 1rem;
        }

        .pagination-mobile {
            padding: 0.5rem;
        }

        .pagination-mobile-btn {
            width: 2rem;
            height: 2rem;
        }

        .pagination-info {
            font-size: 0.8125rem;
        }

        .size-select {
            padding: 0.25rem 0.5rem;
        }
    }

    /* Dark/Light mode adjustments */
    html[data-theme='light'] .pagination-page.active {
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }

    html[data-theme='dark'] .pagination-page.active {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
    }

    /* Accessibility */
    .pagination-btn:focus,
    .pagination-page:focus,
    .size-select:focus {
        outline: 2px solid var(--ring);
        outline-offset: 2px;
    }

    .pagination-btn:focus:not(:focus-visible),
    .pagination-page:focus:not(:focus-visible),
    .size-select:focus:not(:focus-visible) {
        outline: none;
    }

    /* Print styles */
    @media print {
        .custom-pagination {
            display: none;
        }
    }
</style>
