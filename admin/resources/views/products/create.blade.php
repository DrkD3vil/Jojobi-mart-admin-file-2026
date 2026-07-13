@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header animate-fade-in">
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
    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="animate-slide-up">
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
    font-weight: 500;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border-color, oklch(0.9 0 0));
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

/* Optional: Enhanced page header with theme colors */
.page-header.enhanced {
    padding: 1.5rem;
    border-radius: var(--radius, 12px);
    background: var(--card, oklch(0.205 0 0));
    border: 1px solid var(--border-color, oklch(0.9 0 0));
    box-shadow: var(--card-shadow, 0 10px 30px rgba(0, 0, 0, .06));
}

.page-header.enhanced .page-title {
    background: linear-gradient(135deg,
        var(--text-primary, oklch(0.985 0 0)),
        var(--accent-color, oklch(0.488 0.243 264.376)));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.page-header.enhanced .page-subtitle {
    color: var(--text-muted, oklch(0.708 0 0 / 0.7));
}

.page-header.glass {
    backdrop-filter: blur(10px);
    background: var(--glass-base, rgba(255, 255, 255, 0.85));
    border: 1px solid var(--border-color, oklch(0.9 0 0));
    border-radius: var(--radius, 12px);
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
    border-radius: var(--radius, 12px);
    font-weight: 600;
    text-decoration: none;
    transition: all var(--transition-fast, 150ms) ease;
    border: 1px solid transparent;
}

.header-btn.primary {
    background: var(--accent-color, oklch(0.488 0.243 264.376));
    color: var(--sidebar-primary-foreground, #fff);
}

.header-btn.primary:hover {
    background: var(--accent-hover, oklch(0.488 0.243 264.376 / 0.8));
    transform: translateY(-1px);
    box-shadow: 0 4px 12px var(--accent-glow, rgba(37, 99, 235, .2));
}

.header-btn.secondary {
    background: var(--accent, oklch(0.269 0 0));
    border-color: var(--border-color, oklch(0.9 0 0));
    color: var(--text-primary, oklch(0.985 0 0));
}

.header-btn.secondary:hover {
    background: var(--bg-tertiary, oklch(0.269 0 0));
    border-color: var(--accent-color, oklch(0.488 0.243 264.376));
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
    color: var(--text-muted, oklch(0.708 0 0 / 0.7));
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
}

.breadcrumb-link {
    color: var(--text-secondary, oklch(0.708 0 0));
    text-decoration: none;
    transition: color var(--transition-fast, 150ms) ease;
}

.breadcrumb-link:hover {
    color: var(--accent-color, oklch(0.488 0.243 264.376));
    text-decoration: underline;
}

.breadcrumb-current {
    color: var(--text-primary, oklch(0.985 0 0));
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
    background: rgba(34, 197, 94, 0.1);
    color: var(--success, #16a34a);
    border: 1px solid rgba(34, 197, 94, 0.2);
}

.status-badge.inactive {
    background: rgba(107, 114, 128, 0.1);
    color: var(--text-muted, #6b7280);
    border: 1px solid rgba(107, 114, 128, 0.2);
}

.status-badge.warning {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning, #b45309);
    border: 1px solid rgba(245, 158, 11, 0.2);
}

/* Focus styles for accessibility */
.page-header button:focus,
.page-header a:focus,
.form-actions button:focus,
.form-actions a:focus {
    outline: 2px solid var(--ring, oklch(0.556 0 0));
    outline-offset: 2px;
}
</style>
@endsection
