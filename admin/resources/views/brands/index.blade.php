@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="page-header animate-fade-in">
            <div class="header-content">
                <h2 class="page-title">Brand Management</h2>
                <p class="page-subtitle">Manage your product brands and categories</p>
            </div>
            <a href="{{ route('brands.create') }}" class="btn-primary">
                <svg viewBox="0 0 24 24">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
                Add New Brand
            </a>
        </div>

        {{-- Stats Cards --}}
        <div class="stats-grid animate-slide-up">
            <div class="stat-card glass-effect">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z" />
                    </svg>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $brands->total() }}</h3>
                    <p class="stat-label">Total Brands</p>
                </div>
            </div>

            <div class="stat-card glass-effect">
                <div class="stat-icon active">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                    </svg>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value" id="activeCount">{{ $brands->where('is_active', true)->count() }}</h3>
                    <p class="stat-label">Active Brands</p>
                </div>
            </div>

            <div class="stat-card glass-effect">
                <div class="stat-icon inactive">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z" />
                    </svg>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value" id="inactiveCount">{{ $brands->where('is_active', false)->count() }}</h3>
                    <p class="stat-label">Inactive Brands</p>
                </div>
            </div>
        </div>

        {{-- Brands Table --}}
        <div class="card glass-effect animate-slide-up-delay">
            <div class="card-header">
                <h3 class="card-title">All Brands</h3>
                <div class="card-actions">
                    <button class="btn-icon" id="refreshBtn" title="Refresh">
                        <svg viewBox="0 0 24 24">
                            <path
                                d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="card-body">
                @if ($brands->isEmpty())
                    <div class="empty-state">
                        <div class="empty-icon">
                            <svg viewBox="0 0 24 24">
                                <path
                                    d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-5 14H4v-4h11v4zm0-5H4V9h11v4zm5 5h-4V9h4v9z" />
                            </svg>
                        </div>
                        <h3 class="empty-title">No Brands Found</h3>
                        <p class="empty-description">Get started by creating your first brand</p>
                        <a href="{{ route('brands.create') }}" class="btn-primary">
                            <svg viewBox="0 0 24 24">
                                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                            </svg>
                            Create Brand
                        </a>
                    </div>
                @else
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="text-left">Brand</th>
                                    <th class="text-left">Status</th>
                                    <th class="text-left">Created</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($brands as $brand)
                                    <tr class="table-row animate-fade-in-row"
                                        style="animation-delay: {{ $loop->index * 0.05 }}s"
                                        data-brand-id="{{ $brand->id }}">
                                        <td>
                                            <div class="brand-info">
                                                @if ($brand->logo)
                                                    <div class="brand-logo">
                                                        <img src="{{ asset('storage/' . $brand->logo) }}"
                                                            alt="{{ $brand->name }}">
                                                    </div>
                                                @else
                                                    <div class="brand-logo placeholder">
                                                        <span>{{ substr($brand->name, 0, 2) }}</span>
                                                    </div>
                                                @endif
                                                <div class="brand-details">
                                                    <h4 class="brand-name">{{ $brand->name }}</h4>
                                                    @if ($brand->description)
                                                        <p class="brand-description">
                                                            {{ Str::limit($brand->description, 50) }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>



                                        <td>
                                            <input type="checkbox" class="brand-toggle" data-id="{{ $brand->id }}"
                                                {{ $brand->is_active ? 'checked' : '' }}>
                                        </td>

                                        <td>
                                            <span class="date-text">{{ $brand->created_at->format('M d, Y') }}</span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="{{ route('brands.edit', $brand) }}" class="btn-action edit"
                                                    title="Edit">
                                                    <svg viewBox="0 0 24 24">
                                                        <path
                                                            d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                                                    </svg>
                                                </a>
                                                <form action="{{ route('brands.destroy', $brand) }}" method="POST"
                                                    class="inline-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn-action delete" title="Delete"
                                                        onclick="confirmDelete(this)">
                                                        <svg viewBox="0 0 24 24">
                                                            <path
                                                                d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($brands->hasPages())
                        <div class="pagination-container">
                            <div class="pagination-info">
                                Showing {{ $brands->firstItem() }} to {{ $brands->lastItem() }} of {{ $brands->total() }}
                                results
                            </div>
                            {{ $brands->links('vendor.pagination.custom') }}
                        </div>
                    @endif
                @endif
            </div>
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

        @keyframes fadeInRow {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .animate-fade-in {
            animation: fadeIn var(--transition-normal, 250ms) ease-out;
        }

        .animate-slide-up {
            animation: slideUp var(--transition-normal, 250ms) ease-out;
        }

        .animate-slide-up-delay {
            animation: slideUp var(--transition-normal, 250ms) ease-out 0.1s both;
        }

        .animate-fade-in-row {
            animation: fadeInRow 0.3s ease-out;
        }

        /* Layout */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

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
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--card, oklch(0.205 0 0));
            border: 1px solid var(--border-color, oklch(0.9 0 0));
            border-radius: var(--radius, 0.625rem);
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: var(--card-shadow, 0 2px 4px 0 rgb(0 0 0 / 0.25));
            transition: all var(--transition-normal, 250ms) ease;
            backdrop-filter: blur(4px);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--card-shadow-hover, 0 8px 24px rgba(0, 0, 0, 0.35));
            border-color: var(--accent-color, oklch(0.488 0.243 264.376));
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius, 0.625rem);
            background: var(--accent, oklch(0.269 0 0));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-color, oklch(0.488 0.243 264.376));
            transition: all var(--transition-normal, 250ms) ease;
        }

        .stat-card:hover .stat-icon {
            background: var(--accent-color, oklch(0.488 0.243 264.376));
            color: var(--sidebar-primary-foreground, #fff);
        }

        .stat-icon svg {
            width: 24px;
            height: 24px;
            fill: currentColor;
        }

        .stat-icon.active {
            background: var(--accent-glow, rgba(34, 197, 94, 0.1));
            color: var(--success, rgb(34, 197, 94));
        }

        .stat-icon.inactive {
            background: var(--accent-glow, rgba(239, 68, 68, 0.1));
            color: var(--danger, rgb(239, 68, 68));
        }

        .stat-content {
            flex: 1;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary, oklch(0.985 0 0));
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--text-secondary, oklch(0.708 0 0));
        }

        /* Card Styles */
        .card {
            background: var(--card, oklch(0.205 0 0));
            border: 1px solid var(--border-color, oklch(0.9 0 0));
            border-radius: var(--radius, 0.625rem);
            overflow: hidden;
            box-shadow: var(--card-shadow, 0 2px 4px 0 rgb(0 0 0 / 0.25));
            backdrop-filter: blur(4px);
        }

        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color, oklch(0.9 0 0));
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--accent, oklch(0.269 0 0));
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary, oklch(0.985 0 0));
        }

        .card-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-icon {
            width: 40px;
            height: 40px;
            border-radius: var(--radius, 0.625rem);
            background: var(--glass-base, rgba(255, 255, 255, 0.85));
            border: 1px solid var(--border-color, oklch(0.9 0 0));
            color: var(--text-secondary, oklch(0.708 0 0));
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all var(--transition-normal, 250ms) ease;
        }

        .btn-icon:hover {
            background: var(--accent-color, oklch(0.488 0.243 264.376));
            color: var(--sidebar-primary-foreground, #fff);
            border-color: var(--accent-color, oklch(0.488 0.243 264.376));
            transform: translateY(-1px);
        }

        .btn-icon svg {
            width: 20px;
            height: 20px;
            fill: currentColor;
        }

        .card-body {
            padding: 1.5rem;
            background: var(--glass-base, rgba(255, 255, 255, 0.05));
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            color: var(--text-muted, oklch(0.708 0 0 / 0.7));
        }

        .empty-icon svg {
            width: 100%;
            height: 100%;
            fill: currentColor;
        }

        .empty-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary, oklch(0.985 0 0));
            margin-bottom: 0.5rem;
        }

        .empty-description {
            color: var(--text-secondary, oklch(0.708 0 0));
            margin-bottom: 1.5rem;
        }

        /* Table Styles */
        .table-container {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead {
            background: var(--accent, oklch(0.269 0 0));
        }

        .data-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-secondary, oklch(0.708 0 0));
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid var(--border-color, oklch(0.9 0 0));
        }

        .data-table tbody tr {
            border-bottom: 1px solid var(--border-color, oklch(0.9 0 0));
            transition: all var(--transition-normal, 250ms) ease;
        }

        .data-table tbody tr:hover {
            background: var(--accent-glow, rgba(37, 99, 235, 0.1));
        }

        .data-table td {
            padding: 1rem;
            vertical-align: middle;
            color: var(--text-primary, oklch(0.985 0 0));
        }

        /* Brand Info */
        .brand-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .brand-logo {
            width: 48px;
            height: 48px;
            border-radius: var(--radius, 0.625rem);
            overflow: hidden;
            border: 1px solid var(--border-color, oklch(0.9 0 0));
            background: var(--glass-base, rgba(255, 255, 255, 0.85));
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-logo.placeholder {
            background: linear-gradient(135deg,
                    var(--accent-color, oklch(0.488 0.243 264.376)),
                    var(--info, oklch(0.488 0.243 264.376)));
            color: var(--sidebar-primary-foreground, #fff);
            font-weight: 600;
            font-size: 0.875rem;
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 0.25rem;
        }

        .brand-details {
            flex: 1;
        }

        .brand-name {
            font-weight: 600;
            color: var(--text-primary, oklch(0.985 0 0));
            margin-bottom: 0.25rem;
        }

        .brand-description {
            font-size: 0.875rem;
            color: var(--text-secondary, oklch(0.708 0 0));
            line-height: 1.4;
        }

        /* Status Toggle */
        .status-toggle-container {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-width: 150px;
        }

        .status-toggle {
            display: inline-flex;
            align-items: center;
            cursor: pointer;
        }

        .status-toggle-input {
            display: none;
        }

        .status-toggle-label {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            transition: all var(--transition-normal, 250ms) ease;
            user-select: none;
        }

        .status-toggle-track {
            position: relative;
            width: 3rem;
            height: 1.5rem;
            background: var(--border-color, oklch(0.9 0 0));
            border-radius: 0.75rem;
            transition: all var(--transition-normal, 250ms) ease;
        }

        .status-toggle-thumb {
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

        .status-toggle-input:checked+.status-toggle-label .status-toggle-track {
            background: var(--success, oklch(0.696 0.17 162.48));
        }

        .status-toggle-input:checked+.status-toggle-label .status-toggle-thumb {
            transform: translateX(1.5rem);
        }

        .status-toggle-text {
            font-weight: 500;
            color: var(--text-primary, oklch(0.985 0 0));
            font-size: 0.875rem;
            min-width: 60px;
        }

        /* Loading Spinner */
        .status-loading {
            display: none;
            width: 24px;
            height: 24px;
        }

        .spinner {
            animation: spin 1s linear infinite;
            width: 24px;
            height: 24px;
        }

        .spinner .path {
            stroke: var(--accent-color, oklch(0.488 0.243 264.376));
            stroke-linecap: round;
            animation: dash 1.5s ease-in-out infinite;
        }

        @keyframes dash {
            0% {
                stroke-dasharray: 1, 150;
                stroke-dashoffset: 0;
            }

            50% {
                stroke-dasharray: 90, 150;
                stroke-dashoffset: -35;
            }

            100% {
                stroke-dasharray: 90, 150;
                stroke-dashoffset: -124;
            }
        }

        /* Date Text */
        .date-text {
            font-size: 0.875rem;
            color: var(--text-secondary, oklch(0.708 0 0));
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }

        .btn-action {
            width: 36px;
            height: 36px;
            border-radius: var(--radius, 0.625rem);
            border: 1px solid var(--border-color, oklch(0.9 0 0));
            background: var(--glass-base, rgba(255, 255, 255, 0.85));
            color: var(--text-secondary, oklch(0.708 0 0));
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all var(--transition-normal, 250ms) ease;
        }

        .btn-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn-action.edit:hover {
            background: var(--info, oklch(0.488 0.243 264.376));
            color: var(--sidebar-primary-foreground, #fff);
            border-color: var(--info, oklch(0.488 0.243 264.376));
        }

        .btn-action.delete:hover {
            background: var(--danger, oklch(0.704 0.191 22.216));
            color: var(--sidebar-primary-foreground, #fff);
            border-color: var(--danger, oklch(0.704 0.191 22.216));
        }

        .btn-action svg {
            width: 18px;
            height: 18px;
            fill: currentColor;
        }

        .inline-form {
            display: inline;
        }

        /* Pagination */
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 0 0;
            margin-top: 1.5rem;
            border-top: 1px solid var(--border-color, oklch(0.9 0 0));
        }

        .pagination-info {
            font-size: 0.875rem;
            color: var(--text-secondary, oklch(0.708 0 0));
        }

        /* Buttons */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            padding: 0.875rem 1.75rem;
            background: linear-gradient(135deg,
                    var(--accent-color, oklch(0.488 0.243 264.376)),
                    var(--info, oklch(0.488 0.243 264.376)));
            color: var(--sidebar-primary-foreground, #fff);
            border: none;
            border-radius: var(--radius, 0.625rem);
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all var(--transition-normal, 250ms) ease;
            text-decoration: none;
            box-shadow: 0 4px 12px var(--accent-glow, oklch(0.488 0.243 264.376 / 0.2));
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            background: var(--accent-hover, oklch(0.488 0.243 264.376 / 0.8));
            box-shadow: 0 6px 20px var(--accent-glow, oklch(0.488 0.243 264.376 / 0.3));
        }

        .btn-primary svg {
            width: 20px;
            height: 20px;
            fill: currentColor;
        }

        /* Notification Toast */
        .notification-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            max-width: 350px;
            padding: 1rem 1.5rem;
            border-radius: var(--radius, 0.625rem);
            background: var(--card, oklch(0.205 0 0));
            border: 1px solid var(--border-color, oklch(0.9 0 0));
            box-shadow: var(--dropdown-shadow, 0 10px 25px -5px rgb(0 0 0 / 0.4), 0 8px 10px -6px rgb(0 0 0 / 0.3));
            display: flex;
            align-items: center;
            gap: 1rem;
            transform: translateX(400px);
            opacity: 0;
            transition: all var(--transition-normal, 250ms) ease;
            backdrop-filter: blur(10px);
        }

        .notification-toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        .notification-toast.success {
            border-left: 4px solid var(--success, oklch(0.696 0.17 162.48));
        }

        .notification-toast.error {
            border-left: 4px solid var(--danger, oklch(0.704 0.191 22.216));
        }

        .notification-icon {
            width: 24px;
            height: 24px;
        }

        .notification-icon.success {
            color: var(--success, oklch(0.696 0.17 162.48));
        }

        .notification-icon.error {
            color: var(--danger, oklch(0.704 0.191 22.216));
        }

        .notification-content {
            flex: 1;
        }

        .notification-title {
            font-weight: 600;
            color: var(--text-primary, oklch(0.985 0 0));
            margin-bottom: 0.25rem;
        }

        .notification-message {
            font-size: 0.875rem;
            color: var(--text-secondary, oklch(0.708 0 0));
        }

        /* Focus styles for accessibility */
        .btn-icon:focus,
        .btn-action:focus,
        .status-toggle-label:focus,
        .btn-primary:focus {
            outline: 2px solid var(--ring, oklch(0.556 0 0));
            outline-offset: 2px;
        }

        /* Enhanced table row animation */
        .data-table tbody tr {
            animation: fadeInRow 0.3s ease-out;
            animation-fill-mode: both;
        }

        .data-table tbody tr:nth-child(1) {
            animation-delay: 0.05s;
        }

        .data-table tbody tr:nth-child(2) {
            animation-delay: 0.1s;
        }

        .data-table tbody tr:nth-child(3) {
            animation-delay: 0.15s;
        }

        .data-table tbody tr:nth-child(4) {
            animation-delay: 0.2s;
        }

        .data-table tbody tr:nth-child(5) {
            animation-delay: 0.25s;
        }

        .data-table tbody tr:nth-child(6) {
            animation-delay: 0.3s;
        }

        .data-table tbody tr:nth-child(7) {
            animation-delay: 0.35s;
        }

        .data-table tbody tr:nth-child(8) {
            animation-delay: 0.4s;
        }

        .data-table tbody tr:nth-child(9) {
            animation-delay: 0.45s;
        }

        .data-table tbody tr:nth-child(10) {
            animation-delay: 0.5s;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .page-header {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }

            .header-content {
                margin-bottom: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .card-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .brand-info {
                flex-direction: column;
                text-align: center;
                gap: 0.75rem;
            }

            .status-toggle-container {
                justify-content: center;
            }

            .pagination-container {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .action-buttons {
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .data-table {
                display: block;
            }

            .data-table thead {
                display: none;
            }

            .data-table tbody tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid var(--border-color, oklch(0.9 0 0));
                border-radius: var(--radius, 0.625rem);
                padding: 1rem;
                background: var(--card, oklch(0.205 0 0));
            }

            .data-table td {
                display: block;
                padding: 0.5rem 0;
                text-align: center;
            }

            .data-table td:before {
                content: attr(data-label);
                font-weight: 600;
                color: var(--text-secondary, oklch(0.708 0 0));
                display: block;
                font-size: 0.75rem;
                margin-bottom: 0.25rem;
            }

            .brand-info {
                flex-direction: row;
                text-align: left;
            }

            .status-toggle-container {
                min-width: auto;
            }
        }

        /* Dark/Light Mode Specific */
        html[data-theme='dark'] .card,
        html[data-theme='dark'] .stat-card {
            background: var(--glass-base, oklch(0.205 0 0 / 0.7));
        }

        html[data-theme='light'] .card,
        html[data-theme='light'] .stat-card {
            background: var(--card, oklch(1 0 0));
        }

        /* Loading overlay */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius, 0.625rem);
            backdrop-filter: blur(2px);
            z-index: 10;
        }

        /* Status colors for text */
        .text-status-active {
            color: var(--success, oklch(0.696 0.17 162.48));
        }

        .text-status-inactive {
            color: var(--danger, oklch(0.704 0.191 22.216));
        }

        .text-status-pending {
            color: var(--warning, oklch(0.769 0.188 70.08));
        }

        /* Badge styles */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid;
        }

        .status-badge.active {
            background: rgba(34, 197, 94, 0.1);
            color: var(--success, #16a34a);
            border-color: rgba(34, 197, 94, 0.2);
        }

        .status-badge.inactive {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger, #b91c1c);
            border-color: rgba(239, 68, 68, 0.2);
        }

        .status-badge.pending {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning, #b45309);
            border-color: rgba(245, 158, 11, 0.2);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // CSRF Token for AJAX requests
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // Refresh button
            const refreshBtn = document.getElementById('refreshBtn');
            if (refreshBtn) {
                refreshBtn.addEventListener('click', function() {
                    this.classList.add('rotate');
                    setTimeout(() => {
                        window.location.reload();
                    }, 300);
                });
            }

            // Status toggle functionality

            document.addEventListener('DOMContentLoaded', () => {

                const csrf = document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content');

                document.querySelectorAll('.brand-toggle').forEach(toggle => {

                    toggle.addEventListener('change', function() {

                        const brandId = this.dataset.id;
                        const isActive = this.checked ? 1 : 0;

                        fetch(`/brands/${brandId}/toggle-status`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrf,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    is_active: isActive
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                console.log('Updated:', data);
                            })
                            .catch(err => {
                                console.error(err);
                                this.checked = !this.checked; // rollback
                            });
                    });
                });
            });


            // Update stats counters
            function updateStatsCounters(isActive) {
                const activeCountElement = document.getElementById('activeCount');
                const inactiveCountElement = document.getElementById('inactiveCount');

                let activeCount = parseInt(activeCountElement.textContent);
                let inactiveCount = parseInt(inactiveCountElement.textContent);

                if (isActive) {
                    // Switching from inactive to active
                    activeCount++;
                    inactiveCount--;
                } else {
                    // Switching from active to inactive
                    activeCount--;
                    inactiveCount++;
                }

                // Update with animation
                animateCounter(activeCountElement, activeCount);
                animateCounter(inactiveCountElement, inactiveCount);
            }

            // Animate counter value
            function animateCounter(element, newValue) {
                const oldValue = parseInt(element.textContent);
                const duration = 300;
                const startTime = Date.now();

                function update() {
                    const currentTime = Date.now();
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);

                    // Easing function
                    const easeOut = progress => 1 - Math.pow(1 - progress, 3);

                    const currentValue = Math.round(oldValue + (newValue - oldValue) * easeOut(progress));
                    element.textContent = currentValue;

                    if (progress < 1) {
                        requestAnimationFrame(update);
                    }
                }

                requestAnimationFrame(update);
            }

            // Show notification
            function showNotification(title, message, type = 'success') {
                // Remove existing notifications
                const existingNotification = document.querySelector('.notification-toast');
                if (existingNotification) {
                    existingNotification.remove();
                }

                // Create notification element
                const notification = document.createElement('div');
                notification.className = `notification-toast ${type}`;
                notification.innerHTML = `
            <div class="notification-icon ${type}">
                <svg viewBox="0 0 24 24">
                    ${type === 'success' ?
                        '<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>' :
                        '<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>'
                    }
                </svg>
            </div>
            <div class="notification-content">
                <h4 class="notification-title">${title}</h4>
                <p class="notification-message">${message}</p>
            </div>
        `;

                // Add to DOM
                document.body.appendChild(notification);

                // Show notification
                setTimeout(() => {
                    notification.classList.add('show');
                }, 10);

                // Auto hide after 3 seconds
                setTimeout(() => {
                    notification.classList.remove('show');
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.remove();
                        }
                    }, 300);
                }, 3000);
            }

            // Add data-labels for mobile responsive table
            if (window.innerWidth <= 576) {
                const tableCells = document.querySelectorAll('.data-table td');
                const headers = ['Brand', 'Status', 'Created', 'Actions'];

                tableCells.forEach((cell, index) => {
                    const headerIndex = index % 4;
                    cell.setAttribute('data-label', headers[headerIndex]);
                });
            }
        });

        // Delete confirmation
        function confirmDelete(button) {
            if (confirm('Are you sure you want to delete this brand? This action cannot be undone.')) {
                button.closest('form').submit();
            }
        }
    </script>
@endsection
