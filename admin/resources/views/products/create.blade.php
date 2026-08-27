@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header animate-fade-in" data-reveal>
        <div class="header-content">
            <h2 class="page-title">Create New Product</h2>
            <p class="page-subtitle">Add a new product to your inventory</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn-secondary">
            <svg viewBox="0 0 24 24">
                <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
            </svg>
            Back to Products
        </a>
    </div>




        {{-- <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="animate-slide-up">
            @csrf

            @include('products._form')

            {{-- Action Buttons --}}
            {{-- <div class="form-actions animate-fade-in-delay">
                <a href="{{ route('products.index') }}" class="btn-secondary">
                    <svg viewBox="0 0 24 24">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                    </svg>
                    Cancel
                </a>
                <button type="submit" class="btn-primary">
                    <svg viewBox="0 0 24 24">
                        <path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/>
                    </svg>
                    Create Product
                </button>
            </div>
        </form> --}}



        {{-- @if(auth()->user()->privilegeAccessKeys()->where('access_key', 'products')->exists())
         --}}
         {{-- @if(auth()->user()->hasPrivilege('add-product')) --}}
         @if(auth()->user()->hasPrivilegeAccessKey('products'))
    <h2>Access Key</h2>
    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="animate-slide-up" data-reveal>
        @csrf

        @include('products._form')

        {{-- Action Buttons --}}
        <div class="form-actions animate-fade-in-delay">
            <a href="{{ route('products.index') }}" class="btn-secondary">
                <svg viewBox="0 0 24 24">
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                </svg>
                Cancel
            </a>
            <button type="submit" class="btn-primary">
                <svg viewBox="0 0 24 24">
                    <path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/>
                </svg>
                Create Product
            </button>
        </div>
    </form>
@else
    <p>You do not have permission to create a product.</p>
@endif



</div>

<style>
/* Page Header */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.header-content {
    flex: 1;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--foreground);
    margin-bottom: 0.5rem;
    background: linear-gradient(135deg,
        var(--foreground),
        var(--muted-foreground));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.page-subtitle {
    color: var(--muted-foreground);
    font-size: 1rem;
    font-weight: 500;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border);
}

/* Responsive Design */
@media (max-width: 576px) {
    .page-header {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
    }

    .header-content {
        margin-bottom: 1rem;
    }

    .page-title {
        font-size: 1.75rem;
        text-align: center;
    }

    .page-subtitle {
        text-align: center;
    }

    .form-actions {
        flex-direction: column;
    }

    .form-actions a,
    .form-actions button {
        width: 100%;
        justify-content: center;
    }
}

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
    animation: fadeIn var(--transition-normal) ease-out;
}

.animate-slide-up {
    animation: slideUp var(--transition-normal) ease-out;
}

.animate-slide-in {
    animation: slideIn var(--transition-normal) ease-out;
}

.animate-fade-in-delay {
    animation: fadeIn var(--transition-normal) ease-out 0.2s both;
}

.animate-slide-up-delay {
    animation: slideUp var(--transition-normal) ease-out 0.2s both;
}

/* Optional: Enhanced page header with theme colors */
.page-header.enhanced {
    padding: 1.5rem;
    border-radius: var(--radius);
    background: var(--card);
    border: 1px solid var(--border);
    box-shadow: var(--card-shadow);
}

.page-header.enhanced .page-title {
    background: linear-gradient(135deg,
        var(--foreground),
        var(--accent-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.page-header.enhanced .page-subtitle {
    color: var(--muted-foreground);
}

.page-header.glass {
    backdrop-filter: blur(10px);
    background: var(--glass-base);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.5rem;
}

/* Header actions styling */
.header-actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    flex-wrap: wrap;
}

.header-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    border-radius: var(--radius);
    font-weight: 600;
    text-decoration: none;
    transition: all var(--transition-fast) ease;
    border: 1px solid transparent;
}

.header-btn.primary {
    background: var(--accent-color);
    color: var(--sidebar-primary-foreground);
}

.header-btn.primary:hover {
    background: color-mix(in oklch, var(--accent-color) 85%, transparent);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px var(--accent-glow);
}

.header-btn.secondary {
    background: var(--secondary);
    border-color: var(--border);
    color: var(--foreground);
}

.header-btn.secondary:hover {
    background: var(--accent);
    border-color: var(--accent-color);
    transform: translateY(-1px);
}

.header-btn svg {
    width: 18px;
    height: 18px;
    fill: currentColor;
}

/* Breadcrumb styling (optional) */
.breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
    font-size: 0.9rem;
    color: var(--muted-foreground);
}

.breadcrumb-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.breadcrumb-item:not(:last-child)::after {
    content: "/";
    margin-left: 0.5rem;
    color: var(--muted-foreground);
}

.breadcrumb-link {
    color: var(--muted-foreground);
    text-decoration: none;
    transition: color var(--transition-fast) ease;
}

.breadcrumb-link:hover {
    color: var(--accent-color);
    text-decoration: underline;
}

.breadcrumb-current {
    color: var(--foreground);
    font-weight: 600;
}

/* Page status indicators */
.page-status {
    display: flex;
    gap: 0.75rem;
    margin-top: 1rem;
    flex-wrap: wrap;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    border-radius: 999px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-badge.active {
    background: color-mix(in oklch, var(--success) 12%, var(--card));
    color: var(--success);
    border: 1px solid color-mix(in oklch, var(--success) 25%, var(--border));
}

.status-badge.inactive {
    background: color-mix(in oklch, var(--muted-foreground) 12%, var(--card));
    color: var(--muted-foreground);
    border: 1px solid color-mix(in oklch, var(--muted-foreground) 25%, var(--border));
}

.status-badge.warning {
    background: color-mix(in oklch, var(--warning) 12%, var(--card));
    color: var(--warning);
    border: 1px solid color-mix(in oklch, var(--warning) 25%, var(--border));
}

/* Focus styles for accessibility */
.page-header button:focus,
.page-header a:focus,
.form-actions button:focus,
.form-actions a:focus {
    outline: 2px solid var(--ring);
    outline-offset: 2px;
}
</style>
@endsection
